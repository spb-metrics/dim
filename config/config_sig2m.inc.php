<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM2
  //  Arquivo..: config.inc.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi ide
  //  Função...: Programa de configuração de ambiente:
  //             - define conexão com o banco de dados
  //             - define variáveis de ambiente
  //////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////////////////////
  //PARÂMETROS DE CONFIGURAÇÃO DO SISTEMA - ALTERE-OS DE  ACORDO COM O AMBIENTE //
  ////////////////////////////////////////////////////////////////////////////////

  DEFINE("LOTE", "001");

  if(!isset($_SESSION["MSG_RECUPERACAO"])) {
      $_SESSION["MSG_RECUPERACAO"] = "Aguardando inserção de login do usuario";
  }

  ///////////////////////////////
  //CONEXÃO COM A BASE DE DADOS//
  ///////////////////////////////

  $sql="select * from unidade where id_unidade = $_SESSION[id_unidade_sistema]";
  $res = mysqli_query($db, $sql);
  erro_sql("Select Unidade", $db, "");

  if(mysqli_num_rows($res)>0){
    $consulta = mysqli_fetch_object($res);
    $dns = $consulta->dns_local;
    $flg_banco = $consulta->flg_banco;
     /*Inicio Glaison*/
    if ($flg_banco == 1)  {
     $dns = "";
     $usuario_local = $consulta->usuario_integra_local;
     $senha_local   = $consulta->senha_integra_local;
     $base_local    = $consulta->base_integra_ima;
    }
        else {
            //fim glaison
           $sql="select * from parametro";
           $res=mysqli_query($db, $sql);
           erro_sql("Parâmetro", $db, "");
               if(mysqli_num_rows($res)>0){
                  $consulta      = mysqli_fetch_object($res);
                  $usuario_local = $consulta->usuario_integra_local;
                  $senha_local   = $consulta->senha_integra_local;
                  $base_local    = $consulta->base_integra_local;
               }
        }
 }
  if($dns!="") {
  

    $dbSIG2M = @mysql_connect($dns, $usuario_local, $senha_local);
    erro_sql("Conexão SIG2M", "", $db);

    if($dbSIG2M) {
      $base_SIG2M=@mysql_select_db($base_local, $dbSIG2M);
      erro_sql("Seleção BD SIG2M", "", $db);

      if(!$base_SIG2M){
        echo "<script>window.location='" . URL . "/start.php?base=f&nome=SIG2M';</script>";
      }
    }
    else {
      echo "<script>window.location='" . URL . "/start.php?servidor=f&nome=SIG2M';</script>";
    }
  }


  
  
  else {
  
  $dbSIG2M = @mysql_connect("bearden.ima.sp.gov.br", $usuario_local, $senha_local);
               if ($dbSIG2M){
                  $base_Unidade=@mysql_select_db($base_local, $dbSIG2M);
               }
                else{
                    echo "<script>";
                    echo "alert ('Conexão com base  falhou!');";
                    echo "window.location.href='".URL."/modulos/bec/bec_inclusao.php?aplicacao=".$_SESSION[aplicacao_acessada]."'";
                    echo "</script>";
                 }
  
  
  
  
    //echo "<script>window.location='" . URL . "/start.php?erro=f&dns=$dns';</script>";
  }

?>
