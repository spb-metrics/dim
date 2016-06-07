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
  // ARQUIVO ...: Monta o XML das Unidades
  // BY ........: Fabio Hitoshi Ide
  // DATA ......: 15/06/2007
  /******************************************************************/

  $arq_conf="../config/config.inc.php";
  if(!file_exists($arq_conf)){
    exit("N�o existe arquivo de configura��o: $arq_conf!");
  }
  require($arq_conf);

  $id_prescritor=$_POST["id_prescritor"];
  $sql="select m.id_material, m.descricao, m.codigo_material
        from material_prescritor as mp, material as m
        where mp.material_id_material = m.id_material and
        m.status_2='A' and mp.tipo_prescritor_id_tipo_prescritor = '$id_prescritor'
        order by m.descricao";
  $result=mysqli_query($db, $sql);
  erro_sql("Material Prescritor", $db, "");

  $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
  $xml= str_replace("\>", ">", $xml);
  $xml.="<medicamentos>\n";
  while($mat_info=mysqli_fetch_object($result)){
    $xml.="<material>\n";
    $xml.="<id_material>$mat_info->id_material</id_material>\n";
    $xml.="<codigo_material>$mat_info->codigo_material</codigo_material>\n";
    $xml.="<descricao>$mat_info->descricao</descricao>\n";
    $xml.="</material>\n";
  }
  $xml.="</medicamentos>";

  Header("Content-type: application/xml; charset=iso-8859-1");

  echo $xml;
?>

