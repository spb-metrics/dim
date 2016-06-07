<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: buscar_aplic.php                                          |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Funcao que buscar o caminho da aplicação - cabeçalho tela |
// | Data de Criação ....: 16/02/2007 - 11:15                                        |
// | Última Atualização .: 22/02/2007 - 11:45                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  ////////////////////////////
  //VERIFICAÇÃO DE SEGURANÇA//
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
    erro_sql("Busca Aplicação", $base, "");
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
    erro_sql("Aplicação", $db, "");
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
