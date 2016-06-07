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
if (file_exists(DIR . "/config/config.inc.php") &&
   file_exists(DIR . "/config/config_sig2m.inc.php") &&
   file_exists(DIR . "/config/config_almox.inc.php"))
{
  ////////////////////////////
  //VERIFICAÇÃO DE SEGURANÇA//
  ////////////////////////////
 if($_SESSION[id_usuario_sistema]=='')
 {
	  header("Location: ". URL."/start.php");
 }

 if(isset($_GET[aplicacao]))
 {
  $sql = "select * from perfil_has_aplicacao where perfil_id_perfil = '$_SESSION[id_perfil_sistema]' and aplicacao_id_aplicacao = '$_GET[aplicacao]'";
  $res=mysqli_query($db, $sql);
  erro_sql("Perfil Aplicação", $db, "");
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
   erro_sql("Perfil Aplicação Acessada", $db, "");
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
 erro_sql("Mostrar Responsável Dispensação", $db, "");
 $unidade_mostrar_responsavel_dispensacao=mysqli_fetch_object($res);
 $mostrar_responsavel_dispensacao=$unidade_mostrar_responsavel_dispensacao->mostrar_responsavel_dispensacao;
}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else
{
  include_once DIR . "/config/erro_config.php";
}
?>
