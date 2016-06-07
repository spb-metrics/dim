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
if (file_exists("../../config/config.inc.php"))
{
 require "../../config/config.inc.php";

  ////////////////////////////
  //VERIFICA��O DE SEGURAN�A//
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
     erro_sql("Select Paciente Cart�o SUS", $db, "");

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
      erro_sql("Select Cart�o SUS Provis�rio", $db, "");

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

