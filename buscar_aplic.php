<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: buscar_aplic.php                                          |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Funcao que buscar o caminho da aplica��o - cabe�alho tela |
// | Data de Cria��o ....: 16/02/2007 - 11:15                                        |
// | �ltima Atualiza��o .: 22/02/2007 - 11:45                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  ////////////////////////////
  //VERIFICA��O DE SEGURAN�A//
  ////////////////////////////
  if($_SESSION[id_usuario_sistema]=='')
  {
    header("Location: ". URL."/start.php");
  }
  
  $caminho = "";
  
  function busca_aplicacao($nivel_sup, $base)
  {
    global $caminho;

    $sql = "select item_menu_id_item_menu, descricao
            from item_menu
            where id_item_menu = $nivel_sup
            and status_2 = 'A'";
    $sql_query = mysqli_query($base, $sql);
    erro_sql("Busca Aplica��o", $base, "");
    echo mysqli_error($base);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      if ($linha['item_menu_id_item_menu'] <> "0")
      {
        busca_aplicacao($linha['item_menu_id_item_menu'], $base);
      }
      $caminho = $caminho.$linha['descricao']." ".SETA." ";
    }
  }
  
  $aplicacao = $_SESSION[cod_aplicacao];
  //$aplicacao = 63;

  if ($aplicacao <> '')
  {
    $sql = "select item_menu_id_item_menu, descricao
            from item_menu
            where aplicacao_id_aplicacao = $aplicacao
            and status_2 = 'A'";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplica��o", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $nome_aplicacao = $linha['descricao'];
      if ($linha['item_menu_id_item_menu'] <> "0")
      {
        busca_aplicacao($linha['item_menu_id_item_menu'], $db);
      }
      $caminho = $caminho.$linha['descricao'];
    }
  }
  //echo $caminho;
?>
