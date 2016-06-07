<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: relatorio_med_venc_csv.php                                |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Medicamentos Vencidos ou à Vencer (.csv)     |
// | Data de Criação ....: 15/01/2007 - 11:00                                        |
// | Última Atualização .: 19/03/2007 - 15:30                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

function busca_nivel($und_sup, $link)
{
  global $unidades;

  $sql = "select id_unidade, unidade_id_unidade, sigla, nome, flg_nivel_superior
          from unidade
          where unidade_id_unidade = '$und_sup'
                and status_2 = 'A'";
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Busca Nível", $link, "");
  //echo mysqli_error();
  while ($linha = mysqli_fetch_array($sql_query))
  {
    $und_sup01 = $linha['id_unidade'];
    $unidades = $unidades.",".$und_sup01;
    if ($linha['flg_nivel_superior'] == '1')
    {
      busca_nivel($und_sup01, $link);
    }
  }
}

if (file_exists("../../config/config.inc.php"))
{
  require "../../config/config.inc.php";

  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  if ($_POST['unidade01'] <> '')
    $nome_und = $_POST['unidade01'];
  else
    $nome_und = $_POST['unidade02'];
  $fabricante = $_POST['fabricante'];
  $nome_fab = $_POST['fabricante01'];
  $medicamento = $_POST['medicamento'];
  $nome_med = $_POST['medicamento01'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  $codigos = $_POST['codigos'];

    //Header
    
    $file .= "Unidade: ".$und_user."\n";

    $sql = "select descricao
            from item_menu
            where aplicacao_id_aplicacao = $aplicacao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplicação", $db, "");
    //echo mysqli_error();
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $nome_rel = $linha['descricao'];
    }
    $file .= $nome_rel."\n";

    $file .= "\nCRITÉRIOS DE PESQUISA\n";
    $file .= "Período: ".$data_in."  à  ".$data_fn;
    $file .= "\nUnidade: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;

    $file .= "\nFabricante: ";
    if ($nome_fab == '')
      $file .= "Todos os Fabricantes";
    else
      $file .= $nome_fab;

    $file .= "\nMedicamento: ";
    if ($nome_med == '')
      $file .= "Todos os Medicamentos";
    else
      $file .= $nome_med;
    
    $file .= "\n\nCódigo;Medicamento;Validade;Lote;Fabricante;Estoque;Unidade\n";

    $sql = "select mat.codigo_material as codigo, mat.descricao as medicamento, est.validade as validade,
                   est.lote as lote, fab.descricao as fabricante, est.quantidade as estoque, und.nome as unidade
            from material mat
                 inner join estoque est on mat.id_material = est.material_id_material
                 inner join fabricante fab on est.fabricante_id_fabricante = fab.id_fabricante
                 inner join unidade und on est.unidade_id_unidade = und.id_unidade
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and fab.status_2 = 'A'
                  and und.status_2 = 'A'
                  and est.quantidade > 0";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(est.validade,1,10) between '$data_inicio' and '$data_fim'";

    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and und.id_unidade in ($unidades)";
    }
    else if ($codigos <> '')
    {
      $sql = $sql." and und.id_unidade in ($codigos)";
    }

    if (($fabricante <> '') and ($nome_fab <> ''))
      $sql = $sql." and fab.id_fabricante = '$fabricante'";

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." order by mat.codigo_material";
        break;
      case 1:
        $sql = $sql." order by est.validade";
        break;
      case 2:
        $sql = $sql." order by fab.descricao";
        break;
      case 3:
        $sql = $sql." order by est.lote";
        break;
      case 4:
        $sql = $sql." order by mat.descricao";
        break;
      case 5:
        $sql = $sql." order by und.nome";
        break;
    }
    //echo $sql;

    $sql_query = mysqli_query($db, $sql);
    erro_sql("Itens Relatório", $db, "");
    //echo mysqli_error();
    if (mysqli_num_rows($sql_query) > 0)
    {
      while($linha = mysqli_fetch_array($sql_query))
      {
        $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
        $file .= "\n".$linha['codigo'].";".$linha['medicamento'].";".$validade.";";
        $file .= $linha['lote'].";".$linha['fabricante'].";".intval($linha['estoque']).";".$linha['unidade'];
      }
    }
    else{
      $file.="Não Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relatório_Medicamentos_Vencidos.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
}
//echo "<meta http-equiv=\"refresh\" content=\"0; url='javascript:window.close();'\">";
//exit;
?>

