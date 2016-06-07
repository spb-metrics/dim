<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
/////////////////////////////////////////////////////////////////
//  Sistema..: Dim -  Dispensa��o Individualizada de Medicamentos
//  Arquivo..: desconectar.php
//  Bancos...: dbtdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Fun��o...: Efetua desconex�o do sistema, eliminando sess�o
//////////////////////////////////////////////////////////////////

/////////////////
//CACHE READERS//
/////////////////
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//////////////////////////////////////////////////
//TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
//////////////////////////////////////////////////
if (file_exists("./config/config.inc.php"))
{
  require "./config/config.inc.php";
  
  if ($_SESSION["EXPIRADO"]=="sim")
  {
    $mensagem = $_SESSION["MSG_LOGIN"];
  }
  else 
  {
  	$mensagem = "Usu�rio desconectado com sucesso";
  }
  $_SESSION=array();
  session_destroy();
  session_unset();

  header("Location: ".URL);
  
}
////////////////////////////////////////////
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else
{
  include_once("./config/erro_config.php");
}
?>
