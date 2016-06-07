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

