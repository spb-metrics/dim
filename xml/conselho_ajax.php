<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
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
