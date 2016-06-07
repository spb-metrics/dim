<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
    exit("N�o existe arquivo de configura��o: $arq_conf!");
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

