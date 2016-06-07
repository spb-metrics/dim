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
//  Sistema..: DIM - Dispensa��o Individualizada de Medicamentos
//  Arquivo..: start.php
//  Bancos...: dbtdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Fun��o...: Tela de in�cio do sistema
//////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////
//TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
//////////////////////////////////////////////////
if (file_exists("./config/config.inc.php"))
{
  require "./config/config.inc.php";

  $_SESSION[id_unidade_sistema] =  $_POST[unidade];
  $sql = "select * from unidade where id_unidade = '$_SESSION[id_unidade_sistema]'";

  $unidade = mysqli_query($db, $sql);
  erro_sql("Unidade", $db, "");
  
  $unidade_info = mysqli_fetch_object($unidade);
  $_SESSION[nome_unidade_sistema] =   $unidade_info->nome;
  
  $sql = "select * from unidade_has_usuario
         where unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
         and usuario_id_usuario = '$_SESSION[id_usuario_sistema]'";

  $perfil = mysqli_query($db, $sql);
  erro_sql("Unidade Usu�rio", $db, "");

  if(mysqli_num_rows($perfil)>0)
  {
    $perfil_info         = mysqli_fetch_object($perfil);
    $_SESSION[id_perfil_sistema] = $perfil_info->perfil_id_perfil;
  }
  else
  {
    $_SESSION["MSG_LOGIN"] = "Usu�rio sem perfil!";
    header("Location: ". URL);
  }

  require DIR."/header.php";

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA P�GINA//
  ////////////////////////////////////
  ?>
  
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" valign="middle">

      </td>
    </tr>
  </table>
  <?
  require DIR."/footer.php";
}
////////////////////////////////////////////
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else
{
  include_once("./config/erro_config.php");
}
?>
