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
// | Arquivo ............: relatorio_med_venc_pdf.php                                |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Medicamentos Vencidos ou à Vencer (.pdf)     |
// | Data de Criação ....: 10/01/2007 - 14:15                                        |
// | Última Atualização .: 19/03/2007 - 15:530                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

function rec ($uni_sup,&$db){	

	$linha = "";
	$unidade = "";
	$continuar = true;
	while($continuar){
		$sql_uni = "select id_unidade,unidade_id_unidade,nome from unidade where unidade_id_unidade in ($uni_sup) and status_2 = 'A'";
		//echo "<br>".$sql_uni."<br>";
		$sql_query = mysqli_query($db, $sql_uni);
		erro_sql("Selecionar Filhos da unidade Pai", $db, "");
		//echo mysqli_error($db);
		$ids = "";
		if (mysqli_num_rows($sql_query) <= 0){
			$continuar = false;
		}
		while($linha = mysqli_fetch_object($sql_query)){
			$ids.=",\"".$linha ->id_unidade."\"";
			$uni_sup = substr($ids,1);
			$continuar = true;
		}

		$unidade .= $ids;
	}
	return $unidade;
}
/*
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
} */

$header = array('Código','Medicamento','Validade','Lote','Fabricante','Estoque','Unidade');
$w = array(15,90,22,25,60,20,45);
  
function cabecalho()
{
  global $pdf, $data_in, $data_fn, $nome_und, $nome_fab, $nome_med, $header, $w;

  $pdf->AddPage();
  $pdf->Ln();

  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(0,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(28,5,"     Período:",0,0,"L");
  $pdf->Cell(0,5,$data_in."  à  ".$data_fn,0,1,"L");

  $pdf->Cell(28,5,"     Unidade:",0,0,"L");
  if ($nome_und == '')
    $pdf->Cell(0,5,"Todas as Unidades",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_und,0,1,"L");

  $pdf->Cell(28,5,"     Fabricante:",0,0,"L");
  if ($nome_fab == '')
    $pdf->Cell(0,5,"Todos os Fabricantes",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_fab,0,1,"L");

  $pdf->Cell(28,5,"     Medicamento:",0,0,"L");
  if ($nome_med == '')
    $pdf->Cell(0,5,"Todos os Medicamentos",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_med,0,1,"L");

  $pdf->Ln(2);
  //$pdf->SetX(-10);
  //$pdf->Line(10,50,$pdf->GetX(),50);

  //Colors, line width and bold font
  /*$pdf->SetFillColor(14,90,152);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(255);  // cor do texto*/
  $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(0);  // cor do texto

  //$pdf->SetDrawColor(0,0,0);  // cor da linha
  $pdf->SetLineWidth(.3);
  $pdf->SetFont('','B');

  //Header
  for($i = 0; $i < count($header); $i++)
    $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
  $pdf->Ln(5.4);

  //Color and font restoration
  /*$pdf->SetFillColor(224,235,255);
  $pdf->SetTextColor(0);*/
  $pdf->SetFont('');
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

    require "../../fpdf152/Class.Pdf.inc.php";
    DEFINE("FPDF_FONTPATH","font/");

    $pdf = new PDF('L','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)

    $sql = "select apl.executavel, ime.descricao
            from aplicacao apl, item_menu ime
            where apl.id_aplicacao = $aplicacao
                  and ime.aplicacao_id_aplicacao = $aplicacao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplicação", $db, "");
    //echo mysqli_error();
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $executavel = $linha['executavel'];
      $nome_rel = $linha['descricao'];
    }
    $pos = strrpos($executavel, "/");
    if($pos === false)
    {
      $aplic = $executavel;
    }
    else
    {
      $aplic = substr($executavel, $pos+1);
    }
    $pdf->SetName($nome_rel);
    $pdf->SetUnd($und_user);
    $pdf->SetNomeAplic($aplic);
    $pdf->Open();
    cabecalho();

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

	if (($unidade <> '') and ($nome_und <> '')){
      $unidades = $unidade;
      $sql = $sql." and und.id_unidade in ($unidades)";	  
    }else {		
		$uni_sup = $_SESSION[id_unidade_sistema];		
		$ids_unidades =	"\"-1\"".rec($uni_sup,$db);
		$sql = $sql."and und.id_unidade in ($ids_unidades)";
	}
	
    /*if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and und.id_unidade in ($unidades)";
    }
    else */ if ($codigos <> '')
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
      $fill = 0;
      $cont_linhas = 0;
      while($linha = mysqli_fetch_array($sql_query))
      {
        $pdf->Cell($w[0],5,$linha['codigo'],'LR',0,'R',$fill);
        $pdf->Cell($w[1],5,substr(" ".$linha['medicamento'],0,46),'LR',0,'L',$fill);
        $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
        $pdf->Cell($w[2],5,$validade,'LR',0,'C',$fill);
        $pdf->Cell($w[3],5,$linha['lote'],'LR',0,'L',$fill);
        $pdf->Cell($w[4],5,substr(" ".$linha['fabricante'],0,28),'LR',0,'L',$fill);
        $pdf->Cell($w[5],5,intval($linha['estoque'])." ",'LR',0,'R',$fill);
        $pdf->Cell($w[6],5," ".$linha['unidade'],'LR',0,'L',$fill);
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 1;
        if ($cont_linhas == 25)
        {
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho();
          $cont_linhas = 0;
        }
      }
      $pdf->Cell(array_sum($w),0,'','T');
    }
    else{
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
    }


    $pdf->Output();
    $pdf->Close();
}
?>
