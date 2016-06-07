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

  $operacao=$_POST[descricao];  // valor vindo da variavel params de combo.js
  $codigo01=$_POST[codigo01];
  if (isset($codigo01))
  {
      $sql = "select id_tipo_prescritor, descricao from tipo_prescritor where status_2 = 'A' and tipo_conselho_id_tipo_conselho=$operacao and id_tipo_prescritor=$codigo01";
  }
  else  $sql = "select id_tipo_prescritor, descricao from tipo_prescritor where status_2 = 'A' and tipo_conselho_id_tipo_conselho=".$operacao;
  
  
  $result=mysqli_query($db, $sql);
  erro_sql("Tabela Tipo Prescritor", $db, "");

  $linhas=mysqli_num_rows($result);
  if($linhas>0){
    $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
    $xml= str_replace("\>", ">", $xml);
    $xml.="<prescritores>\n";
    while($prescritores_info=mysqli_fetch_object($result)){
      $codigo=$prescritores_info->id_tipo_prescritor;
      $descricao=$prescritores_info->descricao;
      $xml.="<registro>\n";
      $xml.="<codigo>" . $codigo . "</codigo>\n";
      $xml.="<descricao>" . $descricao . "</descricao>\n";
      $xml.="</registro>\n";
    }
    $xml.="</prescritores>";

    Header("Content-type: application/xml; charset=iso-8859-1");
  }
  echo $xml;
?>
