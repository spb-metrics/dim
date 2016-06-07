<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//////////
//HEADER//
//////////

//error_reporting(E_ALL);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  /******************************************************************
  // ARQUIVO ...: Monta o XML das Unidades
  // BY ........: Fabio Hitoshi Ide
  // DATA ......: 15/06/2007
  /******************************************************************/

  $arq_conf="../config/config.inc.php";
  if(!file_exists($arq_conf)){
    exit("Não existe arquivo de configuração: $arq_conf!");
  }
  require($arq_conf);
  
  $valores=split("[|]", $_POST["id_combo"]);
  $id_combo=$valores[0];
  $combo=$valores[1];
  if($combo=="subgrupo"){
    $sql="select *
          from subgrupo
          where status_2='A' and grupo_id_grupo='$id_combo'
          order by descricao";
  }
  if($combo=="familia"){
    $sql="select *
          from familia
          where status_2='A' and subgrupo_id_subgrupo='$id_combo'
          order by descricao";
  }
  $result=mysqli_query($db, $sql);
  erro_sql("Subgrupo", $db, "");
  $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
  $xml= str_replace("\>", ">", $xml);
  $xml.="<combos>\n";
  while($combo_info=mysqli_fetch_object($result)){
    $xml.="<combo>\n";
    if($combo=="subgrupo"){
      $xml.="<codigo>" . $combo_info->id_subgrupo . "</codigo>\n";
      $xml.="<descricao>" . $combo_info->descricao . "</descricao>\n";
    }
    if($combo=="familia"){
      $xml.="<codigo>" . $combo_info->id_familia . "</codigo>\n";
      $xml.="<descricao>" . $combo_info->descricao . "</descricao>\n";
    }
    $xml.="</combo>\n";
  }
  $xml.="</combos>";

  Header("Content-type: application/xml; charset=iso-8859-1");

  echo $xml;
?>

