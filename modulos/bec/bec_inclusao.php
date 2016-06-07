<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    //seleciono dns da unidade logada
    $sql="select * from unidade where id_unidade = $_SESSION[id_unidade_sistema]";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Unidade", $db, "");
    if(mysqli_num_rows($res)>0){
      $consulta_unidade = mysqli_fetch_object($res);
      $flg_banco        = $consulta_unidade->flg_banco;
      $dns              = $consulta_unidade->dns_local;
      /*Inicio Glaison */
      if ($flg_banco == 1){
          $dns ="";
          $usuario_local    = $consulta_unidade->usuario_integra_local;
          $senha_local      = $consulta_unidade->senha_integra_local;
          $base_local       = $consulta_unidade->base_integra_ima;
          $sql = "select * from parametro";
          $res=mysqli_query($db, $sql);
          erro_sql("Parametro", $db, "");
             if(mysqli_num_rows($res)>0) {
                 $consulta_parametro = mysqli_fetch_object($res);
                 //$base_integra_ima = $consulta_parametro->base_integra_ima;
                 $base_almox    = $consulta_parametro->base_integra_almo;
                 $dns_almox     = $consulta_parametro->servidor_integra_almo;
                 $usuario_almox = $consulta_parametro->usuario_integra_almo;
                 $senha_almox   = $consulta_parametro->senha_integra_almo;
             }
      }
      else {
           $sql="select * from parametro";
           $res=mysqli_query($db, $sql);
           erro_sql("Parâmetro", $db, "");
               if(mysqli_num_rows($res)>0){
                  $consulta      = mysqli_fetch_object($res);
                  $usuario_local = $consulta->usuario_integra_local;
                  $senha_local   = $consulta->senha_integra_local;
                  $base_local    = $consulta->base_integra_local;

                  $base_almox    = $consulta->base_integra_almo;
                  $usuario_almox = $consulta->usuario_integra_almo;
                  $senha_almox   = $consulta->senha_integra_almo;
                  $dns_almox     = $consulta->servidor_integra_almo;
              }
      }
  }
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
      exit();
    }

    if(isset($_GET[aplicacao]))
    {
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }

    if(isset($_POST[flag]))
    {
      //quantidade de registros - total, alterado, erro
      $qtde_total=0;
      $qtde_alterado=0;
      $qtde_erro=0;

      if($_POST[flag_numero]=="")
      {
        //obtem data do sistema
        $data=date("Y-m-d H:i:s");

        //obtendo o prazo limte da tabela parametro
        $sql="select qtde_pedido_bec from parametro";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Parâmetro", $db, "");
        if(mysqli_num_rows($res)>0)
        {
          $prazo=mysqli_fetch_object($res);
        }

        //verificando a quantidade de becs existentes na tabela pedido_bec
        $sql="select distinct num_pedido_bec ";
        $sql.="from pedido_bec where unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Qtde BECs", $db, "");
        $atualizacao="";
        if(($quantidade=mysqli_num_rows($res))>=($limite=(int)$prazo->qtde_pedido_bec))
        {
          //quantidade de becs eh igual ao limite estipulado
          if($quantidade==$limite)
          {
            //obtem o numero de bec mais velho
            $sql="select min(num_pedido_bec) as num_pedido_bec from pedido_bec ";
            $sql.="where unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select BEC Mais Velho", $db, "");
            if(mysqli_num_rows($res)>0)
            {
              $num_bec=mysqli_fetch_object($res);
            }
            //apago o numero de bec mais velho
            $sql="delete from pedido_bec ";
            $sql.="where unidade_id_unidade='$_SESSION[id_unidade_sistema]' and ";
            $sql.="num_pedido_bec='$num_bec->num_pedido_bec'";
            mysqli_query($db, $sql);
            erro_sql("Delete BEC Mais Velho", $db, "");
            if(mysqli_errno($db)!="0")
            {
              $atualizacao="erro";
            }
          }
          //numero de becs eh maior que o limite estipulado
          else
          {
            $nova_qtde=$quantidade-$limite+1;
            $sql="select distinct num_pedido_bec ";
            $sql.="from pedido_bec where unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
            $sql.="order by num_pedido_bec";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Qtde BECs Maior que Estipulado", $db, "");
            $index=1;
            while($exclusao=mysqli_fetch_object($res))
            {
              if($index<=$nova_qtde)
              {
                $sql="delete from pedido_bec ";
                $sql.="where num_pedido_bec='$exclusao->num_pedido_bec' ";
                $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
                mysqli_query($db, $sql);
                erro_sql("Delete BECs", $db, "");
                if(mysqli_errno($db)!="0")
                {
                  $atualizacao="erro";
                }
              }
              else
              {
                break;
              }
              $index++;
            }
          }
        }

        //obtem o proximo numero de bec da unidade
        $sql="select num_pedido_bec from pedido_bec where unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Próximo Nro BEC", $db, "");
        if(mysqli_num_rows($res)>0)
        {
          $sql="select max(num_pedido_bec) as num_pedido_bec from pedido_bec where unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
          $res=mysqli_query($db, $sql);
          erro_sql("Select Nro BEC", $db, "");
          if(mysqli_num_rows($res)>0)
          {
            $info=mysqli_fetch_object($res);
            $numero=(int)$info->num_pedido_bec+1;
          }
        }
        else
        {
          $numero=1;
        }
        //obtem sigla da unidade
        $sql="select sigla from unidade where id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Sigla Unidade", $db, "");
        if(mysqli_num_rows($res)>0)
        {
          $sigla_unidade=mysqli_fetch_object($res);
          $sigla=$sigla_unidade->sigla;
        }

        if($dns_almox!=""){
            $dbALMOX = @mysql_connect($dns_almox, $usuario_almox, $senha_almox);
               if ($dbALMOX){
                  $base_CENTRAL=@mysql_select_db($base_almox, $dbALMOX);
               }
                else{
                    echo "<script>";
                    echo "alert ('Conexão com base almoxarifado falhou!');";
                    echo "window.location.href='".URL."/modulos/bec/bec_inclusao.php?aplicacao=".$_SESSION[aplicacao_acessada]."'";
                    echo "</script>";
                 }
        }

        if($dns !=""){
           $dbSIG2M = @mysql_connect($dns, $usuario_local, $senha_local);
                   if($dbSIG2M) {
                     $base_SIG2M=@mysql_select_db($base_local,$dbSIG2M);
                   }
                   else{
                      echo "<script>";
                      echo "alert ('Conexão com base local falhou!**');";
                      echo "window.location.href='".URL."/modulos/bec/bec_inclusao.php?aplicacao=".$_SESSION[aplicacao_acessada]."'";
                      echo "</script>";
                   }
        }
        else
        {
           $dbSIG2M = @mysql_connect("bearden.ima.sp.gov.br", $usuario_local, $senha_local);
               if ($dbSIG2M){
                  $base_Unidade=@mysql_select_db($base_local, $dbSIG2M);
                  echo mysql_error();
               }
                else{
                    echo "<script>";
                    echo "alert ('Conexão com base no servidor bearden falhou!');";
                    //echo" alert ('$base_local');";
                   // echo" alert ('$usuario_local');";
                   // echo" alert ('$senha_local');";


                    
                    echo "window.location.href='".URL."/modulos/bec/bec_inclusao.php?aplicacao=".$_SESSION[aplicacao_acessada]."'";
                    echo "</script>";
                 }

        }

        if ($dbALMOX){

           //verificar se sigla da unidade conectada é válida
           $sql="select sigla from $base_almox.setor where sigla like '$sigla'";
           $res=mysql_query($sql, $dbALMOX);
           erro_sql("Valida Sigla Unidade no SIG2M", "", $db);
           $res_info = mysql_fetch_object($res);
           if(($res_info->sigla=="") or ($res_info->sigla=="0"))
           {
            echo "<script>";
            echo "alert ('Sigla da Unidade Logada (" .$sigla .") não confere com Sigla do SIG2M');";
            echo "window.location.href='".URL."/modulos/bec/bec_inclusao.php?aplicacao=".$_SESSION[aplicacao_acessada]."'";
            echo "</script>";
            $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
           // $ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/arquivos_novos/ARQUIVO_TRAVA_UNIDADE_";
            $ARQ_EXTENSAO=".TXT";
            $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
            if(file_exists($str)){
              unlink($str);
            }
           }
           else {
              if($dbSIG2M) {
              //acessando tabela lote da unidade
              $sql="select l.cod_material, l.qtde, m.nome ";
              $sql.="from $base_local.lote as l, $base_local.material as m ";
              $sql.="where l.cod_material=m.cod_material and l.status='L'";
              $res=mysql_query($sql, $dbSIG2M);
              erro_sql("Select Lote Unidade", "", $db);
              while($info_unidade=mysql_fetch_object($res)){
                 //verificando se eh uma insercao ou atualizacao
                 //verificando se o bec ja existe na tabela pedido_bec
                 $sql="select num_pedido_bec, unidade_id_unidade, cod_material, qtde_sig2m from pedido_bec where num_pedido_bec='$numero' and ";
                 $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and ";
                 $sql.="cod_material='$info_unidade->cod_material'";
                 $result=mysqli_query($db, $sql);
                 
                // echo $sql;
                 //exit;
                 erro_sql("Select Pedido BEC", $db, "");
                 //bec ja existe na tabela pedido_bec
                 if(mysqli_num_rows($result)>0){
                    $info_bec=mysqli_fetch_object($result);
                    $qtde_atual=(int)$info_unidade->qtde+(int)$info_bec->qtde_sig2m;
                    $sql="update pedido_bec set qtde_sig2m='$qtde_atual' ";
                    $sql.="where num_pedido_bec='$info_bec->num_pedido_bec' and ";
                    $sql.="unidade_id_unidade='$info_bec->unidade_id_unidade' and ";
                    $sql.="cod_material='$info_bec->cod_material'";
                  }
              //bec nao existe na tabela pedido_bec
              else{
               //obtem id material
               $sql="select id_material from material where codigo_material='$info_unidade->cod_material' and status_2='A'";
               $result=mysqli_query($db, $sql);
               erro_sql("Select Material", $db, "");
               if(mysqli_num_rows($result)>0)
               {
                $mat_info=mysqli_fetch_object($result);
                $id_material=$mat_info->id_material;
               }
               else
               {
                $id_material="null";
               }
               $sql="insert into pedido_bec ";
               $sql.="(num_pedido_bec, sigla, cod_material, unidade_id_unidade, material_id_material, qtde_sig2m, qtde_dim, data_pedido, usua_incl, descricao_material) ";
               $sql.="values ('$numero', '" . strtoupper($sigla) . "', '$info_unidade->cod_material', '$_SESSION[id_unidade_sistema]', $id_material, '$info_unidade->qtde', '0', '$data', '$_SESSION[id_usuario_sistema]', '" . strtoupper($info_unidade->nome) . "')";
              }
              mysqli_query($db, $sql);
              erro_sql("Update/Insert Pedido BEC", $db, "");
              if(mysqli_errno($db)!="0")
              {
               $atualizacao="erro";
              }
             }

             //acessando tabela estoque do dim
             $sql="select e.quantidade, m.id_material, m.codigo_material, m.descricao ";
             $sql.="from estoque as e, material as m ";
             $sql.="where e.material_id_material=m.id_material and unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
             $sql.="and e.flg_bloqueado=''";
             $res=mysqli_query($db, $sql);
             erro_sql("Select Estoque", $db, "");
             while($info_dim=mysqli_fetch_object($res))
             {
              //verificando se eh uma insercao ou atualizacao
              //verificando se o bec ja existe na tabela pedido_bec
              $sql="select num_pedido_bec, unidade_id_unidade, cod_material, qtde_dim from pedido_bec where num_pedido_bec='$numero' and ";
              $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and ";
              $sql.="cod_material='$info_dim->codigo_material'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Pedido BEC - DIM", $db, "");
              //bec ja existe na tabela pedido_bec
              if(mysqli_num_rows($result)>0)
              {
               $info_bec=mysqli_fetch_object($result);
               $qtde_atual=(int)$info_bec->qtde_dim+(int)$info_dim->quantidade;
               $sql="update pedido_bec set qtde_dim='$qtde_atual' ";
               $sql.="where num_pedido_bec='$info_bec->num_pedido_bec' and ";
               $sql.="unidade_id_unidade='$info_bec->unidade_id_unidade' and ";
               $sql.="cod_material='$info_bec->cod_material'";
              }
              //bec nao existe na tabela pedido_bec
              else
              {
                $sql="insert into pedido_bec ";
                $sql.="(num_pedido_bec, sigla, cod_material, unidade_id_unidade, material_id_material, qtde_sig2m, qtde_dim, data_pedido, usua_incl, descricao_material) ";
                $sql.="values ('$numero', '" . strtoupper($sigla) . "', '$info_dim->codigo_material', '$_SESSION[id_unidade_sistema]', '$info_dim->id_material', '0', '$info_dim->quantidade', '$data', '$_SESSION[id_usuario_sistema]', '" . strtoupper($info_dim->descricao) . "')";
              }
              mysqli_query($db, $sql);
              erro_sql("Update/Insert Pedido BEC - DIM", $db, "");
              if(mysqli_errno($db)!="0")
              {
               $atualizacao="erro";
              }
             }
            }
            else
            {
              $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
             // $ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/arquivos_novos/ARQUIVO_TRAVA_UNIDADE_";
              $ARQ_EXTENSAO=".TXT";
              $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
              if(file_exists($str))
              {
                unlink($str);
              }
              $sql="select * from aplicacao where id_aplicacao='$_SESSION[APLICACAO]'";
              $res=mysqli_query($db, $sql);
              if(mysqli_num_rows($res)>0)
              {
               $sistema="";
               $aplic_info=mysqli_fetch_object($res);
              }
              else
              {
               $sistema="erro";
              }
              ?>
              <script>
                      alert ('Falha de conexão com a Unidade');
                      window.location="<?php echo URL . $aplic_info->executavel . "?aplicacao=$_SESSION[APLICACAO]";?>";
              </script>
              <?
            }
           }
         }
         else
         {
              $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
             // $ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/arquivos_novos/ARQUIVO_TRAVA_UNIDADE_";
              $ARQ_EXTENSAO=".TXT";
              $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
              if(file_exists($str))
              {
                unlink($str);
              }
              $sql="select * from aplicacao where id_aplicacao='$_SESSION[APLICACAO]'";
              $res=mysqli_query($db, $sql);
              if(mysqli_num_rows($res)>0)
              {
               $sistema="";
               $aplic_info=mysqli_fetch_object($res);
              }
              else
              {
               $sistema="erro";
              }
              ?>
              <script>
                      alert ('Falha de conexão com o Almoxarifado Central');
                      window.location="<?php echo URL . $aplic_info->executavel . "?aplicacao=$_SESSION[APLICACAO]";?>";
              </script>
              <?
         }
      }
      //indica que eh um reenvio
      if($_POST[flag_numero]!="")
      {
         require "../../config/config_almox.inc.php";
         $numero=$_POST[flag_numero];
      }

      //obtem sigla da unidade
      $sql="select sigla from unidade where id_unidade='$_SESSION[id_unidade_sistema]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Sigla Unidade", $db, "");
      if(mysqli_num_rows($res)>0)
      {
         $sigla_unidade=mysqli_fetch_object($res);
         $sigla=$sigla_unidade->sigla;
      }
      
      if($dbALMOX)
      {
      //verificar se sigla da unidade conectada é válida
      $sql="select sigla from $base_almox.setor where sigla like '$sigla'";
      $res=mysql_query($sql, $dbALMOX);
      erro_sql("Valida Sigla Unidade no SIG2M", "", $db);

      $res_info = mysql_fetch_object($res);

      if(($res_info->sigla=="") and ($res_info->sigla=="0"))
      {
         echo "<script>";
         echo "alert ('Sigla da Unidade Logada (" .$sigla .") não confere com Sigla do SIG2M');";
         echo "window.location.href='".URL."/modulos/bec/bec_inclusao.php?aplicacao=".$_SESSION[aplicacao_acessada]."'";
         echo "</script>";
         $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
         //$ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/ARQUIVO_TRAVA_UNIDADE_";
         $ARQ_EXTENSAO=".TXT";
         $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
         if(file_exists($str))
         {
           unlink($str);
         }
      }
      else
      {
         //obtendo as informacoes da tabela saldo setor referente a unidade no almoxarifado
         $sql="select sigla from pedido_bec where num_pedido_bec='$numero' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
         $res=mysqli_query($db, $sql);
         erro_sql("Sigla Unidade", $db, "");
         if(mysqli_num_rows($res)>0)
         {
           //Apagando as informacoes na tabela saldo setor referente a unidade no almoxarifado
           $sigla_unidade=mysqli_fetch_object($res);
           $sql="delete from $base_almox.saldo_setor where sigla='$sigla_unidade->sigla'";
           mysql_query($sql, $dbALMOX);
           erro_sql("Delete Saldo Setor", "", $db);
         }
         //obtendo as informacoes da tabela pedido_bec no dim
         $sql="select cod_material, qtde_sig2m, qtde_dim, data_pedido, sigla from pedido_bec where num_pedido_bec='$numero' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
         $res=mysqli_query($db, $sql);
         erro_sql("Select Informações Pedido BEC", $db, "");
         while($pedido_bec=mysqli_fetch_object($res))
         {
           //verificando se material existe na tabela material do almoxarifado
           $sql="select cod_material, nome from $base_almox.material where cod_material='$pedido_bec->cod_material'";
           $result=mysql_query($sql, $dbALMOX);
           erro_sql("Select Material", "", $db);
           if(mysql_num_rows($result)>0)
           {
             $qtde_atual=(int)$pedido_bec->qtde_sig2m+(int)$pedido_bec->qtde_dim;
             $sql="insert into $base_almox.saldo_setor (sigla, cod_material, qtde, data) values ";
             $sql.="('$pedido_bec->sigla', '$pedido_bec->cod_material', '$qtde_atual', '$pedido_bec->data_pedido')";
             mysql_query($sql, $dbALMOX);

             erro_sql("Insert Saldo Setor", "", $db);
             $qtde_alterado++;
             //atualiza o status do material para TRANSMITIDO na tabela pedido_bec no dim
             $sql="update pedido_bec set status_2='TRANSMITIDO' ";
             $sql.="where num_pedido_bec='$numero' and unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
             $sql.="and cod_material='$pedido_bec->cod_material'";
           }
           else
           {
             //atualiza o status do material para trans. parcial na tabela pedido_bec no dim
             $sql="update pedido_bec set status_2='NÃO TRANSMITIDO' ";
             $sql.="where num_pedido_bec='$numero' and unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
             $sql.="and cod_material='$pedido_bec->cod_material'";
             $qtde_erro++;
           }
           mysqli_query($db, $sql);
           erro_sql("Update Pedido Bec", $db, "");
           if(mysqli_errno($db)!="0")
           {
             $atualizacao="erro";
           }
           $qtde_total++;
         }
      }
      }
      //apagando o arquivo de trava
      $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
     // $ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/arquivos_novos/ARQUIVO_TRAVA_UNIDADE_";
      $ARQ_EXTENSAO=".TXT";
      $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
      if(!unlink($str))
      {
        exit("Erro ao apagar o arquivo $ARQ_TRAVA!");
      }

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if($atualizacao=="")
      {
        mysqli_commit($db);
        if($qtde_erro>0)
        {
          echo "<script>
                  resposta=window.confirm('Total de Registros: $qtde_total | Registros Atualizados: $qtde_alterado | Registros não Atualizados: $qtde_erro! Deseja visualizar o arquivo?');
                  if(resposta)
                  {
                    window.open('" . URL . "/modulos/impressao/impressao_bec_erro.php?nro=$numero&aplicacao=$_SESSION[APLICACAO]');
                  }
                </script>";
        }
        else
        {
          echo "<script>
                  window.alert('Operação efetuada com sucesso!');
                </script>";
        }
      }
      else
      {
        mysqli_rollback($db);
        echo "<script>
                window.alert('Não foi possível gerar o bec!');
              </script>";
      }
      echo "<script>window.location='" . URL . "/modulos/bec/bec_inclusao.php';</script>";
      $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
      //$ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/arquivos_novos/ARQUIVO_TRAVA_UNIDADE_";
      $ARQ_EXTENSAO=".TXT";
      $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
      if(file_exists($str))
      {
        unlink($str);
      }
    }
    require "../../verifica_acesso.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
<?php

    if($_GET[aplicacao]<>'')
    {
      $_SESSION[cod_aplicacao]=$_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript">
    <!--
      function trataDados(){
        var x=document.form_inclusao;
        var info = ajax.responseText;  // obtém a resposta como string
        //alert (info);
        aux=info.substr(0, 3);
        if(aux=="CRI"){
          mostra();
          javascript:showFrame('aplicacao');
          x.submit();
        }
        if(aux=="Err"){
          var msg="Erro ao criar arquivo!";
          window.alert(msg);
          x.cadastrar.disabled="";
        }
        if(aux=="EXI"){
          var msg="Aplicação Não pode ser Executada no Momento!";
          window.alert(msg);
          x.cadastrar.disabled="";
        }
      }

      function verificarArquivo(bec){
        var x=document.form_inclusao;
        x.cadastrar.disabled="true";
        if(bec){
          x.flag_numero.value=bec;
        }
        var url = "../../xml/becArquivo.php?id=" + <?php echo $_SESSION[id_unidade_sistema];?>;
        requisicaoHTTP("GET", url, true);
      }
      
    //-->
    </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td> <?php echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%'height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_inclusao" action="./bec_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr>
                      <td colspan="5">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                          <tr class="titulo_tabela">
                            <td valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?> </td>
                          </tr>
                          <tr>
                            <td class="campo_tabela" valign="middle" align="center" width="100%">
                              <?php
                                if($inclusao_perfil!=""){
                              ?>
                                  <input type="button" style="font-size: 12px;" name="cadastrar" value="Gerar" onclick="verificarArquivo('');">
                              <?php
                                }
                                else{
                              ?>
                                  <input type="button" style="font-size: 12px;" name="cadastrar" value="Gerar" disabled>
                              <?php
                                }
                              ?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5">
                        <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width='18%' align='center'> Nº Transferência </td>
                            <td width='18%' align='center'> Data da Solicitação </td>
                            <td width='34%' align='center'> Status </td>
                            <td width='20%' align='center'> Usuário </td>
                            <td width='10%' align='center'></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5">
                        <div id="aplicacao" style="display:'';">
                          <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr>
                              <td width='18%'></td>
                              <td width='18%'></td>
                              <td width='34%'></td>
                              <td width='20%'></td>
                              <td width='10%'></td>
                            </tr>
<?php
                            $cor_linha = "#CCCCCC";
                            ///////////////////////////////////////
                            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
                            ///////////////////////////////////////
                            $sql="select distinct p.num_pedido_bec, p.data_pedido, u.nome, p.sigla  ";
                            $sql.="from pedido_bec as p, usuario as u ";
                            $sql.="where u.id_usuario=p.usua_incl and p.unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
                            $sql.="order by p.num_pedido_bec desc";
                            $result=mysqli_query($db, $sql);
                            erro_sql("Select Lista", $db, "");
                            while($documento_info=mysqli_fetch_object($result)){
                              $pos1=strpos($documento_info->data_pedido, "-");
                              $pos2=strrpos($documento_info->data_pedido, "-");
                              $data_pedido=substr($documento_info->data_pedido, $pos2+1, 2) . "/" . substr($documento_info->data_pedido, $pos1+1, 2) . "/" . substr($documento_info->data_pedido, 0, 4);
                              $bec=$documento_info->num_pedido_bec;
                              $sigla=$documento_info->sigla;
?>
                              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                                <td align='left'>
                                  <?php echo $documento_info->num_pedido_bec;?>
                                </td>
                                <td align='center'>
                                  <?php echo $data_pedido;?>
                                </td>
<?php
                                //verifica se tem algum material que nao foi TRANSMITIDO
                                $sql="select count(num_pedido_bec) as nao_transmitido from pedido_bec where unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
                                $sql.="and num_pedido_bec='$documento_info->num_pedido_bec' and status_2!='TRANSMITIDO'";
                                $res=mysqli_query($db, $sql);
                                erro_sql("Select qtde não transmitida", $db, "");
                                $res_sql=mysqli_fetch_object($res);
                                $qtde_nao_transmitido=$res_sql->nao_transmitido;
                                if($qtde_nao_transmitido==0){
                                   $situacao="TRANSMITIDO";
                                }
                                else{
                                  $sql="select status_2 from pedido_bec where unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
                                  $sql.="and num_pedido_bec='$documento_info->num_pedido_bec'";
                                  $res=mysqli_query($db, $sql);
                                  erro_sql("Select Status", $db, "");
                                  if(mysqli_num_rows($res)==$qtde_nao_transmitido){
                                    $situacao="NÃO TRANSMITIDO";
                                  }
                                  else{
                                    $situacao="PARCIALMENTE TRANSMITIDO";
                                  }
                                }
?>
                                <td align='left'>
                                  <?php echo $situacao;?>
                                </td>
                                <td align='left'>
                                  <?php echo $documento_info->nome;?>
                                </td>
                                <td align='center'>
                                  <?php
                                    $pos1=strpos($documento_info->data_pedido, "-");
                                    $pos2=strrpos($documento_info->data_pedido, "-");
                                    $data_inclusao=substr($documento_info->data_pedido, $pos2+1, 2) . "/" . substr($documento_info->data_pedido, $pos1+1, 2) . "/" . substr($documento_info->data_pedido, 0, 4);
                                  ?>
                                    <img src="<?php echo URL;?>/imagens/i.p.printv.gif" border="0" title="Imprimir" onclick="javascript:window.open('<?php echo URL . "/modulos/impressao/impressao_bec.php?numero=" . $bec . "&aplicacao=" . $_SESSION[APLICACAO] . "&data=" . $data_inclusao . "&sigla=" . $sigla;?>');">&nbsp&nbsp&nbsp
                                  <?php
                                    if($inclusao_perfil!=""){
                                      if($data_pedido==date("d/m/Y")){
                                  ?>
                                        <img src="<?php echo URL;?>/imagens/quote.gif" border="0" title="Enviar" onclick="verificarArquivo('<?php echo $bec;?>');">
                                  <?php
                                      }
                                      else{
                                  ?>
                                        <img src="<?php echo URL;?>/imagens/gray/quote.gif" border="0" title="Desabilitado">
                                  <?
                                      }
                                    }
                                  ?>
                                </td>
                              </tr>
<?php
                              ////////////////////////
                              //MUDANDO COR DA LINHA//
                              ////////////////////////
                              if($cor_linha=="#EEEEEE"){
                                $cor_linha="#CCCCCC";
                              }
                              else{
                                $cor_linha="#EEEEEE";
                              }
                            }
?>
                            <tr>
                              <td colspan="5" height="100%"></td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
                    </tr>
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="flag_numero" value="">
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
