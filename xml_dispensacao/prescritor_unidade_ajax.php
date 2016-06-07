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

  $unidade=$_POST[descricao];  // valor vindo da variavel params de combo.js

  $sql = "select
                concat(concat(p.id_profissional,'|',p.tipo_prescritor_id_tipo_prescritor),'|', p.inscricao) as codigo,
                p.status_2, concat(p.nome,'/',e.uf) as nome, p.tipo_prescritor_id_tipo_prescritor, p.inscricao
          from
                profissional p,
                estado e,
                unidade_has_profissional u
          where
               u.unidade_id_unidade = $unidade
               and p.status_2 = 'A'
               and u.profissional_id_profissional = p.id_profissional
               and p.estado_id_estado = e.id_estado
          order by
               p.nome " ;
  $result=mysqli_query($db, $sql);
  erro_sql("Tabela Prescritor", $db, "");

  $linhas=mysqli_num_rows($result);
  if($linhas>0){
    $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
    $xml= str_replace("\>", ">", $xml);
    $xml.="<prescritores>\n";
    while($prescritores_info=mysqli_fetch_object($result)){
      $codigo          = $prescritores_info->codigo;
      $descricao       = $prescritores_info->nome;
      $tipo_prescritor = $prescritores_info->tipo_prescritor_id_tipo_prescritor;
      $inscricao       = $prescritores_info->inscricao;
      $status          = $prescritores_info->status_2;
      
      $xml.="<registro>\n";
      $xml.="<codigo>" . $codigo . "</codigo>\n";
      $xml.="<descricao>" . $descricao . "</descricao>\n";
      $xml.="<inscricao>" . $inscricao . "</inscricao>\n";
      $xml.="<tipo>" . $tipo_prescritor . "</tipo>\n";
      $xml.="<status>" . $status . "</status>\n";
      $xml.="</registro>\n";
    }
    $xml.="</prescritores>";

    Header("Content-type: application/xml; charset=iso-8859-1");
  }
  echo $xml;
?>
