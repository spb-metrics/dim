<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();

//////////////////////////////////////////////////
//TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
//////////////////////////////////////////////////
if (file_exists(DIR . "/config/config.inc.php") &&
   file_exists(DIR . "/config/config_sig2m.inc.php") &&
   file_exists(DIR . "/config/config_almox.inc.php"))
{
  ////////////////////////////
  //VERIFICA��O DE SEGURAN�A//
  ////////////////////////////
 if($_SESSION[id_usuario_sistema]=='')
 {
	  header("Location: ". URL."/start.php");
 }

 if(isset($_GET[aplicacao]))
 {
  $sql = "select * from perfil_has_aplicacao where perfil_id_perfil = '$_SESSION[id_perfil_sistema]' and aplicacao_id_aplicacao = '$_GET[aplicacao]'";
  $res=mysqli_query($db, $sql);
  erro_sql("Perfil Aplica��o", $db, "");
  $acesso = mysqli_fetch_object($res);
  
  $inclusao_perfil  = $acesso->inclusao;
  $alteracao_perfil = $acesso->alteracao;
  $exclusao_perfil  = $acesso->exclusao;
  $consulta_perfil  = $acesso->consulta;
  
  $_SESSION[aplicacao_acessada] = $acesso->aplicacao_id_aplicacao;

//  echo $inclusao.$alteracao.$exclusao.$consulta;
//  echo exit;
 }
 else
 {
  if ($_SESSION[aplicacao_acessada]!="")
  {
   $sql = "select * from perfil_has_aplicacao where perfil_id_perfil = '$_SESSION[id_perfil_sistema]' and aplicacao_id_aplicacao = '$_SESSION[aplicacao_acessada]'";
   $res=mysqli_query($db, $sql);
   erro_sql("Perfil Aplica��o Acessada", $db, "");
   $acesso = mysqli_fetch_object($res);

   $inclusao_perfil  = $acesso->inclusao;
   $alteracao_perfil = $acesso->alteracao;
   $exclusao_perfil  = $acesso->exclusao;
   $consulta_perfil  = $acesso->consulta;

  }
 }
 
 $sql="select 'S' as mostrar_responsavel_dispensacao
       from unidade_has_aplicacao
       where unidade_id_unidade=$_SESSION[id_unidade_sistema] and
             aplicacao_id_aplicacao='$_GET[aplicacao]'";
 $res=mysqli_query($db, $sql);
 erro_sql("Mostrar Respons�vel Dispensa��o", $db, "");
 $unidade_mostrar_responsavel_dispensacao=mysqli_fetch_object($res);
 $mostrar_responsavel_dispensacao=$unidade_mostrar_responsavel_dispensacao->mostrar_responsavel_dispensacao;
}
////////////////////////////////////////////
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else
{
  include_once DIR . "/config/erro_config.php";
}
?>
