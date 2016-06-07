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
//  Sistema..: DIM - Dispensação Individualizada de Medicamentos
//  Arquivo..: start.php
//  Bancos...: dbtdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Função...: Tela de início do sistema
//////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////
//TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
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
  erro_sql("Unidade Usuário", $db, "");

  if(mysqli_num_rows($perfil)>0)
  {
    $perfil_info         = mysqli_fetch_object($perfil);
    $_SESSION[id_perfil_sistema] = $perfil_info->perfil_id_perfil;
  }
  else
  {
    $_SESSION["MSG_LOGIN"] = "Usuário sem perfil!";
    header("Location: ". URL);
  }

  require DIR."/header.php";

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
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
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else
{
  include_once("./config/erro_config.php");
}
?>
