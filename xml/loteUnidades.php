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
  
  $valores=split("[|]", $_POST["id_material"]);
  $id_material=$valores[0];
  $lote=$valores[1];
  $id_fabricante=$valores[2];

  $sql="select sum(e.quantidade) as total
        from estoque as e, material as m, unidade as u
        where e.material_id_material=m.id_material and e.unidade_id_unidade=u.id_unidade and
              u.status_2='A' and e.material_id_material='$id_material' and e.lote='$lote' and
              e.fabricante_id_fabricante='$id_fabricante' and e.flg_bloqueado='' and
              e.quantidade>0";
  $result=mysqli_query($db, $sql);
  erro_sql("Quantidade Total", $db, "");
  if(mysqli_num_rows($result)>0){
    $qtde_total=mysqli_fetch_object($result);
  }

  $sql="select e.quantidade, u.nome
        from estoque as e, material as m, unidade as u
        where e.material_id_material=m.id_material and e.unidade_id_unidade=u.id_unidade and
        u.status_2='A' and e.material_id_material='$id_material' and e.lote='$lote' and
        e.fabricante_id_fabricante='$id_fabricante' and e.flg_bloqueado='' and e.quantidade>0";
  $result=mysqli_query($db, $sql);
  erro_sql("Tabela estoque", $db, "");

  $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
  $xml= str_replace("\>", ">", $xml);
  $xml.="<unidades>\n";
  while($unidades_info=mysqli_fetch_object($result)){
    $xml.="<cs>\n";
    $xml.="<qtde_total>$qtde_total->total</qtde_total>\n";
    $xml.="<unidade>" . $unidades_info->nome . "</unidade>\n";
    $xml.="<quantidade>" . $unidades_info->quantidade . "</quantidade>\n";
    $xml.="</cs>\n";
  }
  $xml.="</unidades>";

  Header("Content-type: application/xml; charset=iso-8859-1");

  echo $xml;
?>

