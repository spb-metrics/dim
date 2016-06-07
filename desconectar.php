<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
/////////////////////////////////////////////////////////////////
//  Sistema..: Dim -  Dispensação Individualizada de Medicamentos
//  Arquivo..: desconectar.php
//  Bancos...: dbtdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Função...: Efetua desconexão do sistema, eliminando sessão
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
//TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
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
  	$mensagem = "Usuário desconectado com sucesso";
  }
  $_SESSION=array();
  session_destroy();
  session_unset();

  header("Location: ".URL);
  
}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else
{
  include_once("./config/erro_config.php");
}
?>
