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
if (file_exists("../../config/config.inc.php"))
{
 require "../../config/config.inc.php";

  ////////////////////////////
  //VERIFICAÇÃO DE SEGURANÇA//
  ////////////////////////////
 if($_SESSION[id_usuario_sistema]=='')
 {
  header("Location: ". URL."/start.php");
 }
 
 if ($_GET[dispensacao]=="ok")
 {
   $dispensacao = "ok";
 }
 else if ($_GET[dispensacao]=="nao")
 {
   $dispensacao = "nao";
 }

  ////////////////////////
  //CADASTRO DA PACIENTE //
  ///////////////////////
//echo $_GET[flag_cartao];
  if($_GET[cartao_sus]!="" && $_GET[flag_cartao]=="t")
  {
     $sql_select = "select * from paciente where cartao_sus = '$_GET[cartao_sus]' and status_2='A'";
     $verifica = mysqli_query($db, $sql_select);
     erro_sql("Select Paciente Cartão SUS", $db, "");

     if(mysqli_num_rows($verifica) == 0)
     {
          header("Location: ". URL."/modulos/paciente/paciente_inclusao.php?cartao_sus=".$_GET[cartao_sus]."&dispensacao=".$dispensacao."&cartao_sus_prov=".$_GET[cartao_sus_prov]);
     }
     else
     {
        header("Location: ". URL."/modulos/paciente/paciente_inclusao.php?i=sus&dispensacao=".$dispensacao."&cartao_sus_prov=".$_GET[cartao_sus_prov]);
     }
  }
  else{
    if($_GET[cartao_sus_prov]!="")
    {
      $sql_select = "select * from paciente where cartao_sus_prov = '$_GET[cartao_sus_prov]' and status_2='A'";
      $verifica = mysqli_query($db, $sql_select);
      erro_sql("Select Cartão SUS Provisório", $db, "");

      if(mysqli_num_rows($verifica) == 0)
      {
        header("Location: ". URL."/modulos/paciente/paciente_inclusao.php?cartao_sus=".$_GET[cartao_sus]."&dispensacao=".$dispensacao."&cartao_sus_prov=".$_GET[cartao_sus_prov]);
      }
      else
      {
        header("Location: ". URL."/modulos/paciente/paciente_inclusao.php?i=suspr&cartao_sus=".$_GET[cartao_sus]."&dispensacao=".$dispensacao);
      }
    }
  }
}

