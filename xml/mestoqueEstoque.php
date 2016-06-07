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
  $motivo=$_GET["motivo"];
  $chave_unica=$_GET["chave"];
  $usuario=$_GET["id_login"];
  if($usuario==""){
    $usuario=$_SESSION[id_usuario_sistema];
  }
  $unidade=$_SESSION["id_unidade_sistema"];
  $_GET[itens]=str_replace("CERQUILHA", SIMBOLO, $_GET[itens]);
  $valor=split("[|]", substr($_GET[itens], 0, (strlen($_GET[itens])-1)));
  for($i=0; $i<count($valor); $i++){
    $valores[]=split("[,]", substr($valor[$i], 0, (strlen($valor[$i])-1)));
  }
  $msg="";

  $msg_duplicacao="estoque|NO|Houve tentativa de reincidência na movimentação.\nVerifique se esta operação foi realizada com sucesso.\nInforme a IMA sobre a ocorrência.";
  $msg_erro="estoque|NO|Não foi possível cadastrar entrada e/ou saída de material!";
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
      $sql="select * from tipo_movto where id_tipo_movto=$numero";
      $result=mysqli_query($db, $sql);
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
        $msg=$msg_erro;
      }
      if(mysqli_num_rows($result)>0){
        $tipo_operacao=mysqli_fetch_object($result);
        $operacao=$tipo_operacao->operacao;
      }
      $data=date("Y-m-d H:i:s");
      //insercao de um registro por documento na tabela movto_geral
      $sql="insert into movto_geral ";
      $sql.="(tipo_movto_id_tipo_movto, usuario_id_usuario, unidade_id_unidade, data_movto,
              data_incl, motivo, num_controle) ";
      $sql.="values ('$numero', '$usuario', '$unidade', '$data', '$data',
                     '" . strtoupper($motivo) . "', '$chave_unica')";
      mysqli_query($db, $sql);
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
        $msg=$msg_erro;
      }
      $sql="select id_movto_geral from movto_geral ";
      $sql.="where tipo_movto_id_tipo_movto='$numero' and usuario_id_usuario='$usuario' and ";
      $sql.="unidade_id_unidade='$unidade' and data_movto='$data' and data_incl='$data'";
      $res=mysqli_query($db, $sql);
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
        $msg=$msg_erro;
      }
      if(mysqli_num_rows($res)>0){
        $chave=mysqli_fetch_object($res);
      }
      for($i=0; $i<count($valores); $i++){
        $sql="select * from estoque
              where fabricante_id_fabricante='" . $valores[$i][1] . "'
              and material_id_material='" . $valores[$i][0] . "' and lote='" . $valores[$i][2] . "'
              and quantidade>='" . $valores[$i][4] . "' and unidade_id_unidade='$unidade'";
        $res=mysqli_query($db, $sql);
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
          $msg=$msg_erro;
          break;
        }
        if(mysqli_num_rows($res)<=0 && $operacao!="entrada"){
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
          $msg=$mat_descr->descricao." - ".$valores[$i][2]." - ".$fabr_descr->descricao;
          break;
        }
        else{
          //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
          $sql="insert into itens_movto_geral ";
          $sql.="(movto_geral_id_movto_geral, material_id_material, fabricante_id_fabricante, lote,
                  validade, qtde) ";
          $sql.="values ('$chave->id_movto_geral', '" . $valores[$i][0] . "',
                         '" . $valores[$i][1] . "', '" . strtoupper($valores[$i][2]) . "',
                         '" . $valores[$i][3] . "', '" . $valores[$i][4] . "')";
          mysqli_query($db, $sql);
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
            $msg=$msg_erro;
            break;
          }
          //obtem a quantidade de material de uma unidade no estoque
          $sql="select * from estoque ";
          $sql.="where fabricante_id_fabricante='" . $valores[$i][1] . "' and
                       material_id_material='" . $valores[$i][0] . "' ";
          $sql.="and unidade_id_unidade='$unidade' and lote='" . $valores[$i][2] . "'";
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
          $sql="select * from estoque ";
          $sql.="where fabricante_id_fabricante='" . $valores[$i][1] . "' and
                       material_id_material='" . $valores[$i][0] . "' ";
          $sql.="and unidade_id_unidade='$unidade' and lote='" . $valores[$i][2] . "'";
          $res=mysqli_query($db, $sql);
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
            $msg=$msg_erro;
            break;
          }
          if(mysqli_num_rows($res)>0){
            if($operacao=="entrada"){
              $qtde=(int)$valores[$i][4]+$qtde_estoque_unidade;
            }
            else{
              $qtde=$qtde_estoque_unidade-(int)$valores[$i][4];
            }
            $sql="update estoque ";
            $sql.="set quantidade='$qtde', data_alt='$data', usua_alt='$usuario' ";
            $sql.="where fabricante_id_fabricante='" . $valores[$i][1] . "' and
                         material_id_material='" . $valores[$i][0] . "' ";
            $sql.="and unidade_id_unidade='$unidade' and lote='" . $valores[$i][2] . "'";
          }
          else{
            //verificando se existe material/lote/fabricante bloqueado para alguma unidade
            $sql="select * from estoque where fabricante_id_fabricante='" . $valores[$i][1] . "' ";
            $sql.="and material_id_material='" . $valores[$i][0] . "' and
                   lote='" . $valores[$i][2] . "' and ";
            $sql.="flg_bloqueado='S'";
            $res=mysqli_query($db, $sql);
            if(mysqli_errno($db)!="0"){
              $atualizacao="erro";
              $msg=$msg_erro;
              break;
            }
            //existe material/fabricante/lote bloqueado para alguma unidade
            if(mysqli_num_rows($res)>0){
              $sql="insert into estoque ";
              $sql.="(fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote,
                      validade, quantidade, data_incl, usua_incl, flg_bloqueado) ";
              $sql.="values ('" . $valores[$i][1] . "', '" . $valores[$i][0] . "',
                             '$unidade' ,'" . strtoupper($valores[$i][2]) . "',
                             '" . $valores[$i][3] . "', '" . $valores[$i][4] . "', '$data',
                             '$usuario', 'S')";
            }
            //nao existe material/fabricante/lote bloqueado para alguma unidade
            else{
              $sql="insert into estoque ";
              $sql.="(fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote,
                      validade, quantidade, data_incl, usua_incl, flg_bloqueado) ";
              $sql.="values ('" . $valores[$i][1] . "', '" . $valores[$i][0] . "',
                             '$unidade' ,'" . strtoupper($valores[$i][2]) . "',
                             '" . $valores[$i][3] . "', '" . $valores[$i][4] . "', '$data',
                             '$usuario', '')";
            }
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
          $sql="select * from movto_livro where movto_geral_id_movto_geral='$chave->id_movto_geral' ";
          $sql.="and unidade_id_unidade='$unidade' and material_id_material='" . $valores[$i][0] . "'";
          $res=mysqli_query($db, $sql);
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
            $msg=$msg_erro;
            break;
          }
          if(mysqli_num_rows($res)>0){
            //atualizando o movimento do livro
            if($operacao=="entrada"){
              $livro_info=mysqli_fetch_object($res);
              $qtde=(int)$livro_info->qtde_entrada+(int)$valores[$i][4];
              $sql="update movto_livro set qtde_entrada='$qtde', saldo_atual='$saldo_atual'";
              $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and ";
              $sql.="unidade_id_unidade='$unidade' and material_id_material='" . $valores[$i][0] . "'";
              mysqli_query($db, $sql);
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
                $msg=$msg_erro;
                break;
              }
            }
            if($operacao=="saida"){
              $livro_info=mysqli_fetch_object($res);
              $qtde=(int)$livro_info->qtde_saida+(int)$valores[$i][4];
              $sql="update movto_livro set qtde_saida='$qtde', saldo_atual='$saldo_atual'";
              $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and ";
              $sql.="unidade_id_unidade='$unidade' and material_id_material='" . $valores[$i][0] . "'";
              mysqli_query($db, $sql);
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
                $msg=$msg_erro;
                break;
              }
            }
            if($operacao=="perda"){
              $livro_info=mysqli_fetch_object($res);
              $qtde=(int)$livro_info->qtde_perda+(int)$valores[$i][4];
              $sql="update movto_livro set qtde_perda='$qtde', saldo_atual='$saldo_atual'";
              $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and ";
              $sql.="unidade_id_unidade='$unidade' and material_id_material='" . $valores[$i][0] . "'";
              mysqli_query($db, $sql);
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
                $msg=$msg_erro;
                break;
              }
            }
          }
          else{
            //insercao movimento do livro
            $sql="select * from tipo_movto where id_tipo_movto='$numero'";
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
            if($operacao=="entrada"){
              $sql="insert into movto_livro ";
              $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material,
                      tipo_movto_id_tipo_movto, saldo_anterior, qtde_entrada, saldo_atual,
                      data_movto, historico) ";
              $sql.="values ('$chave->id_movto_geral', '$unidade', '" . $valores[$i][0] . "',
                             '$numero', '$saldo_anterior', '" . $valores[$i][4] . "',
                             '$saldo_atual', '$data', '" . strtoupper($history) ." . ')";
              mysqli_query($db, $sql);
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
                $msg=$msg_erro;
                break;
              }
            }
            if($operacao=="saida"){
              $sql="insert into movto_livro ";
              $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material,
                      tipo_movto_id_tipo_movto, saldo_anterior, qtde_saida, saldo_atual,
                      data_movto, historico) ";
              $sql.="values ('$chave->id_movto_geral', '$unidade', '" . $valores[$i][0] . "',
                             '$numero', '$saldo_anterior', '" . $valores[$i][4] . "',
                             '$saldo_atual', '$data', '" . strtoupper($history) . "')";
              mysqli_query($db, $sql);
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
                $msg=$msg_erro;
                break;
              }
            }
            if($operacao=="perda"){
              $sql="insert into movto_livro ";
              $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material,
                      tipo_movto_id_tipo_movto, saldo_anterior, qtde_perda, saldo_atual,
                      data_movto, historico) ";
              $sql.="values ('$chave->id_movto_geral', '$unidade', '" . $valores[$i][0] . "',
                             '$numero', '$saldo_anterior', '" . $valores[$i][4] . "',
                             '$saldo_atual', '$data', '" . strtoupper($history) . "')";
              mysqli_query($db, $sql);
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
                $msg=$msg_erro;
                break;
              }
            }
          }
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
    $msg="estoque|OK|" . $chave->id_movto_geral."|";
  }
  echo $msg;
?>

