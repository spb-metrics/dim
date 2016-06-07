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
  // ARQUIVO ...: Monta o XML dos Lotes
  // BY ........: Fabio Hitoshi Ide
  // DATA ......: 15/06/2007
  /******************************************************************/

  $arq_conf="../config/config.inc.php";
  if(!file_exists($arq_conf)){
    exit("Não existe arquivo de configuração: $arq_conf!");
  }
  require($arq_conf);

  $valores=split("[|]", $_POST["id_material"]);
  $id_material=$valores[0];
  $id_unidade=$valores[1];
  $sql="select m.id_material, m.codigo_material, f.id_fabricante, f.descricao, e.lote,
               e.validade, e.quantidade
        from estoque as e, material as m, fabricante as f
        where e.material_id_material=m.id_material and
              e.fabricante_id_fabricante=f.id_fabricante and
              m.id_material='$id_material' and e.unidade_id_unidade='$id_unidade'
              and e.quantidade>'0' and e.flg_bloqueado=''
        order by e.validade";
  $result=mysqli_query($db, $sql);
  erro_sql("Tabela estoque", $db, "");

  $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
  $xml= str_replace("\>", ">", $xml);
  $xml.="<lotes>\n";
  while($lote_info=mysqli_fetch_object($result)){
    $lote_value=$lote_info->id_material . "|" . $lote_info->id_fabricante . "|" . $lote_info->lote . "|" . $lote_info->validade . "|" . (int)$lote_info->quantidade;
    $pos1=strpos($lote_info->validade, "-");
    $pos2=strrpos($lote_info->validade, "-");
    $validade_info=substr($lote_info->validade, $pos2+1, strlen($lote_info->validade)) . "/" . substr($lote_info->validade, $pos1+1, 2) . "/" . substr($lote_info->validade, 0, 4);
    $lote_descricao=$lote_info->codigo_material . "|" . $lote_info->descricao . "|" . $lote_info->lote . "|" . $validade_info . "|" . (int)$lote_info->quantidade;
    $lote_descricao=str_replace("&", "&amp;", $lote_descricao);
    $xml.="<lote>\n";
    $xml.="<codigo>" . $lote_value . "</codigo>\n";
    $xml.="<descricao>" . $lote_descricao . "</descricao>\n";
    $xml.="</lote>\n";
  }
  $xml.="</lotes>";

  Header("Content-type: application/xml; charset=iso-8859-1");

  echo $xml;
?>

