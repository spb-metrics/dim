<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);

  $material=$_POST[descricao];

  $sql = " select m.*, l.*, u.unidade
       from material m, unidade_material u,
       where id_material = $material
       and m.unidade_material_id_unidade_material = u.id_unidade_material
       and m.lista_especial_id_lista_especial = l.id_lista_especial

  $result=mysqli_query($db, $sql);
  erro_sql("Tabela Material", $db, "");

  $linhas=mysqli_num_rows($result);
  if($linhas>0){
    $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
    $xml= str_replace("\>", ">", $xml);
    $xml.="<material>\n";

    $material_info=mysqli_fetch_object($result))
    $codigo=$material_info->unidade;
    $controlada=$material_info->flg_receita_controlada;
    $xml.="<registro>\n";
    $xml.="<codigo>" . $codigo . "</codigo>\n";
    $xml.="<controlada>" . $controlada . "</controlada>\n";
    $xml.="</registro>\n";

    $xml.="</material>";

    Header("Content-type: application/xml; charset=iso-8859-1");
  }
  echo $xml;
?>
