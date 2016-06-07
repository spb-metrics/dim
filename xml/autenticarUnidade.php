<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  $_SESSION["id_unidade_sistema"]=$_GET["unidade"];
  $sql = "select *
          from unidade
          where id_unidade='$_SESSION[id_unidade_sistema]'";

  $unidade=mysqli_query($db, $sql);
  erro_sql("Unidade", $db, "");

  $unidade_info=mysqli_fetch_object($unidade);
  $_SESSION["nome_unidade_sistema"]=$unidade_info->nome;

  $sql="select *
        from unidade_has_usuario
        where unidade_id_unidade='$_SESSION[id_unidade_sistema]'
              and usuario_id_usuario='$_SESSION[id_usuario_sistema]'";

  $perfil=mysqli_query($db, $sql);
  erro_sql("Unidade Usu�rio", $db, "");

  if(mysqli_num_rows($perfil)>0){
    $perfil_info=mysqli_fetch_object($perfil);
    $_SESSION["id_perfil_sistema"]=$perfil_info->perfil_id_perfil;
    $msg="start";
  }
  else{
    $msg="perfil";
  }
  echo $msg;
?>

