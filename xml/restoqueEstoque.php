<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

//error_reporting(E_ALL);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $numero=$_GET["numero"];
  $chave_unica=$_GET["chave"];
  $usuario=$_GET["id_login"];
  if($usuario==""){
    $usuario=$_SESSION[id_usuario_sistema];
  }
  $unidade=$_SESSION["id_unidade_sistema"];
  $_GET[itens]=str_replace("CERQUILHA", "#", $_GET[itens]);
  $itens=split("[@]", $_GET["itens"]);
  for($i=0; $i<count($itens); $i++){
    $valores[]=split("[|]", $itens[$i]);
  }
  $msg="";
  $msg_duplicacao="SAV|NO|Houve tentativa de reincidência na movimentação.\nVerifique se esta operação foi realizada com sucesso.\nInforme a IMA sobre a ocorrência.";
  $msg_erro="SAV|NO|Não foi possível realizar estorno dos materiais!";
  $atualizacao="";
  $sql="select * from movto_geral where num_controle='$chave_unica'";
  $result=mysqli_query($db, $sql);
  if(mysqli_errno($db)!="0"){
    $atualizacao="erro";
    $msg=$msg_erro;
  }
  else{
    if(mysqli_num_rows($result)>0){
      $atualizacao="erro";
      $msg=$msg_duplicacao;
    }
    else{
      $sql="select t.operacao
            from movto_geral as mov, tipo_movto as t
            where mov.tipo_movto_id_tipo_movto=t.id_tipo_movto and
            mov.unidade_id_unidade='$unidade'
            and t.flg_movto='s' and mov.id_movto_geral='$numero'";
      $res=mysqli_query($db, $sql);
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
        $msg=$msg_erro;
      }
      if(mysqli_num_rows($res)>0){
        $operacao_info=mysqli_fetch_object($res);
        $operacao=$operacao_info->operacao;
      }
      $data=date("Y-m-d H:i:s");
      //insercao de um registro por documento na tabela movto_geral
      //realizando uma entrada por reversao
      if($operacao!="entrada"){
        $sql="insert into movto_geral
              (tipo_movto_id_tipo_movto, id_movto_estornado, usuario_id_usuario,
               unidade_id_unidade, data_movto, data_incl, num_controle)
              values ('11', '$numero', '$usuario',
                      '$unidade', '$data', '$data', '$chave_unica')";
        mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
        }
        $sql="select id_movto_geral
              from movto_geral
              where tipo_movto_id_tipo_movto='11' and id_movto_estornado='$numero' and
                    usuario_id_usuario='$usuario' and
                    unidade_id_unidade='$unidade' and
                    data_movto='$data' and data_incl='$data'";
      }
      else{
        //realizando uma saida por reversao
        $sql="insert into movto_geral
              (tipo_movto_id_tipo_movto, id_movto_estornado, usuario_id_usuario,
               unidade_id_unidade, data_movto, data_incl, num_controle)
              values ('12', '$numero', '$usuario',
                      '$unidade', '$data', '$data', '$chave_unica')";
        mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
        }
        $sql="select id_movto_geral
              from movto_geral
              where tipo_movto_id_tipo_movto='12' and id_movto_estornado='$numero' and
                    usuario_id_usuario='$usuario' and
                    unidade_id_unidade='$unidade' and data_movto='$data'
                    and data_incl='$data'";
      }
      $res=mysqli_query($db, $sql);
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
        $msg=$msg_erro;
      }
      if(mysqli_num_rows($res)>0){
        $chave=mysqli_fetch_object($res);
      }
      for($i=0; $i<count($valores); $i++){
        if($operacao=="entrada"){
          $sql="select * from estoque
                where fabricante_id_fabricante='" . $valores[$i][1] . "'
                and material_id_material='" . $valores[$i][0] . "' and lote='" . $valores[$i][2] . "'
                and quantidade>='" . $valores[$i][3] . "' and unidade_id_unidade='$unidade'";
          $res=mysqli_query($db, $sql);
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
            $msg=$msg_erro;
            break;
          }
          if(mysqli_num_rows($res)<=0){
            $sql="select *
                  from material
                  where id_material='" . $valores[$i][0] . "' and status_2='A'";
            $res=mysqli_query($db, $sql);
            if(mysqli_errno($db)!="0"){
              $atualizacao="erro";
              $msg=$msg_erro;
              break;
            }
            if(mysqli_num_rows($res)>0){
              $mat_descr=mysqli_fetch_object($res);
            }
            $sql="select *
                  from fabricante
                  where id_fabricante='" . $valores[$i][1] . "' and status_2='A'";
            $res=mysqli_query($db, $sql);
            if(mysqli_errno($db)!="0"){
              $atualizacao="erro";
              $msg=$msg_erro;
              break;
            }
            if(mysqli_num_rows($res)>0){
              $fabr_descr=mysqli_fetch_object($res);
            }//, fabricante e lote
            $atualizacao="erro";
            $msg=$mat_descr->descricao." - ".$valores[$i][2]." - ".$fabr_descr->descricao."\n";
            break;
          }
        }
        //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
        $sql="insert into itens_movto_geral
              (movto_geral_id_movto_geral, material_id_material, fabricante_id_fabricante,
               lote, validade, qtde)
              values ('$chave->id_movto_geral', '" . $valores[$i][0] . "',
                      '" . $valores[$i][1] . "', '" . strtoupper($valores[$i][2]) . "',
                      '" . $valores[$i][4] . "', '" . $valores[$i][3] . "')";
        mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
$msg.=$sql;
          break;
        }
        //obtem a quantidade de material de uma unidade no estoque
        $sql="select *
              from estoque
              where fabricante_id_fabricante='" . $valores[$i][1] . "' and
                    material_id_material='" . $valores[$i][0] . "' and
                    unidade_id_unidade='$unidade' and
                    lote='" . $valores[$i][2] . "'";
        $res=mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        if(mysqli_num_rows($res)>0){
          $estoque_info=mysqli_fetch_object($res);
          $qtde_estoque_unidade=(int)$estoque_info->quantidade;
        }
        else{
          $qtde_estoque_unidade=0;
        }
        //obtem o saldo anterior de um material no estoque
        $sql="select *
              from estoque
              where material_id_material='" . $valores[$i][0] . "' and
                    unidade_id_unidade='$unidade'";
        $res=mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        $saldo_anterior=0;
        if(mysqli_num_rows($res)>0){
          while($qtde_estoque_material=mysqli_fetch_object($res)){
            $saldo_anterior+=(int)$qtde_estoque_material->quantidade;
          }
        }
        //verifica se eh uma insercao ou uma atualizacao no estoque
        $sql="select *
              from estoque
              where fabricante_id_fabricante='" . $valores[$i][1] . "' and
                    material_id_material='" . $valores[$i][0] . "'
                    and unidade_id_unidade='$unidade' and
                    lote='" . $valores[$i][2] . "'";
        $res=mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        if(mysqli_num_rows($res)>0){
          if($operacao!="entrada"){
            $qtde=(int)$valores[$i][3]+$qtde_estoque_unidade;
          }
          else{
            $qtde=$qtde_estoque_unidade-(int)$valores[$i][3];
          }
          $sql="update estoque
                set quantidade='$qtde', data_alt='$data',
                    usua_alt='$usuario'
                where fabricante_id_fabricante='" . $valores[$i][1] . "' and
                      material_id_material='" . $valores[$i][0] . "'
                      and unidade_id_unidade='$unidade' and
                      lote='" . $valores[$i][2] . "'";
        }
        else{
          $sql="insert into estoque
                (fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote,
                 validade, quantidade, data_incl, usua_incl, flg_bloqueado)
                values ('" . $valores[$i][1] . "', '" . $valores[$i][0] . "',
                        '$unidade' ,'" . strtoupper($valores[$i][2]) . "',
                        '" . $valores[$i][4] . "', '" . $valores[$i][3] . "', '$data',
                        '$usuario', '')";
        }
        mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        //obtem o saldo atual de um material no estoque
        $sql="select *
              from estoque
              where material_id_material='" . $valores[$i][0] . "' and
                    unidade_id_unidade='$unidade'";
        $res=mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        if(mysqli_num_rows($res)>0){
          $saldo_atual=0;
          while($qtde_estoque_material=mysqli_fetch_object($res)){
            $saldo_atual+=(int)$qtde_estoque_material->quantidade;
          }
        }
        //verificando se eh uma atualizacao ou insercao
        $sql="select *
              from movto_livro
              where movto_geral_id_movto_geral='$chave->id_movto_geral'
                    and unidade_id_unidade='$unidade' and
                    material_id_material='". $valores[$i][0] . "'";
        $res=mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        if(mysqli_num_rows($res)>0){
          //atualizando o movimento do livro
          if($operacao!="entrada"){
            $livro_info=mysqli_fetch_object($res);
            $qtde=(int)$livro_info->qtde_entrada+(int)$valores[$i][3];
            $sql="update movto_livro
                  set qtde_entrada='$qtde', saldo_atual='$saldo_atual'
                  where movto_geral_id_movto_geral='$chave->id_movto_geral' and
                        unidade_id_unidade='$unidade' and
                        material_id_material='" . $valores[$i][0] . "'";
          }
          else{
            $livro_info=mysqli_fetch_object($res);
            $qtde=(int)$livro_info->qtde_saida+(int)$valores[$i][3];
            $sql="update movto_livro
                  set qtde_saida='$qtde', saldo_atual='$saldo_atual'
                  where movto_geral_id_movto_geral='$chave->id_movto_geral' and
                        unidade_id_unidade='$unidade' and
                        material_id_material='" . $valores[$i][0] . "'";
          }
        }
        else{
          //insercao movimento do livro
          if($operacao!="entrada"){
            $sql="select *
                  from tipo_movto
                  where id_tipo_movto='11'";
          }
          else{
            $sql="select *
                  from tipo_movto
                  where id_tipo_movto='12'";
          }
          $res=mysqli_query($db, $sql);
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
            $msg=$msg_erro;
            break;
          }
          if(mysqli_num_rows($res)>0){
            $mov_info=mysqli_fetch_object($res);
          }
          $history=$mov_info->descricao . " Nº do documento: " . $chave->id_movto_geral;
          if($operacao!="entrada"){
            $sql="insert into movto_livro
                  (movto_geral_id_movto_geral, unidade_id_unidade, material_id_material,
                   tipo_movto_id_tipo_movto, saldo_anterior, qtde_entrada, saldo_atual,
                   data_movto, historico)
                   values ('$chave->id_movto_geral', '$unidade',
                          '" . $valores[$i][0] . "', '11', '$saldo_anterior',
                          '" . $valores[$i][3] . "', '$saldo_atual', '$data',
                          '" . strtoupper($history) . "')";
          }
          else{
            $sql="insert into movto_livro
                  (movto_geral_id_movto_geral, unidade_id_unidade, material_id_material,
                   tipo_movto_id_tipo_movto, saldo_anterior, qtde_saida, saldo_atual,
                   data_movto, historico)
                  values ('$chave->id_movto_geral', '$unidade',
                          '" . $valores[$i][0] . "', '12', '$saldo_anterior',
                          '" . $valores[$i][3] . "', '$saldo_atual', '$data',
                          '" . strtoupper($history) . "')";
          }
        }
        mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
      }
    }
  }
  /////////////////////////////////////
  //SE INCLUSÃO OCORREU SEM PROBLEMAS//
  /////////////////////////////////////
  if($atualizacao==""){
    mysqli_commit($db);
  }
  else{
    mysqli_rollback($db);
  }

  if($msg==""){
    $msg="SAV|OK|" . $chave->id_movto_geral."|";
  }
  echo $msg;
?>

