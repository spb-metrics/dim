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
// | Arquivo ............: relatorio_med_pac_und_pdf.php                             |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Med Ret Pac de Outras Unds (.pdf)            |
// | Data de Criação ....: 22/01/2007 - 13:35                                        |
// | Última Atualização .: 19/03/2007 - 10:50                                        |
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

/*function busca_nivel($und_sup, $link)
{
  global $unidades;

  $sql = "select id_unidade, unidade_id_unidade, sigla, nome, flg_nivel_superior
          from unidade
          where unidade_id_unidade = '$und_sup'
                and status_2 = 'A'";
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Busca Nível", $link, "");
  echo mysqli_error($link);
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

$header = array('Paciente','Medicamento','Qtde Retirada','Data da Retirada');
$w = array(120,97,30,30);

function cabecalho($nome_und_at)
{
  global $pdf, $data_in, $data_fn, $nome_und, $nome_med;

  $pdf->AddPage();
  $pdf->Ln();

  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(22,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(28,5,"     Período:",0,0,"L");
  $pdf->Cell(0,5,$data_in."  à  ".$data_fn,0,1,"L");

  $pdf->Cell(28,5,"     Unidade:",0,0,"L");
  if ($nome_und == '')
    $pdf->Cell(0,5,"Todas as Unidades",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_und,0,1,"L");

  $pdf->Cell(28,5,"     Medicamento:",0,0,"L");
  if ($nome_med == '')
    $pdf->Cell(0,5,"Todos os Medicamentos",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_med,0,1,"L");

  $pdf->SetX(-10);
  $pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
  $pdf->Ln(4);
  $pdf->SetFont('','B');
  $pdf->Cell(38,5,"Unidade do Movimento:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(0,5,$nome_und_at,0,0,"L");
  $pdf->Ln(6);
}

function cabecalho_tabela($nome_cid, $nome_und_ref)
{
  global $pdf, $header, $w;
  
  $pdf->Ln(2);
  $pdf->SetFont('','B');
  $pdf->Cell(15,5,"Cidade:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(105,5,$nome_cid,0,0,"L");
  $pdf->SetFont('','B');
  $pdf->Cell(30,5,"Unidade Referida:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(0,5,$nome_und_ref,0,0,"L");
  $pdf->Ln(5);
  
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
  $pdf->SetTextColor(0); */
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
    echo mysqli_error($db);
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

    $sql = "select und.nome as unidade, concat(cid.nome,'/',est.uf) as cidade, und01.nome as und_ref, pac.nome as paciente,
                   mat.descricao as medicamento, sum(mvl.qtde_saida) as quantidade, mvg.data_movto as data_retirada
            from material mat
                 inner join movto_livro mvl on mat.id_material = mvl.material_id_material
                 inner join movto_geral mvg on mvl.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join paciente pac on mvg.paciente_id_paciente = pac.id_paciente
                 inner join cidade cid on pac.cidade_id_cidade = cid.id_cidade
                 inner join estado est on cid.estado_id_estado = est.id_estado
                 left join unidade und01 on pac.unidade_referida = und01.id_unidade
                 inner join tipo_movto tmv on mvg.tipo_movto_id_tipo_movto = tmv.id_tipo_movto,
                 parametro par
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and und.status_2 = 'A'
                  and pac.status_2 = 'A'
                  and tmv.id_tipo_movto = 3
                  and ((mvg.unidade_id_unidade <> pac.unidade_referida) or (pac.cidade_id_cidade <> par.cidade_id_cidade))";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(mvg.data_movto,1,10) between '$data_inicio' and '$data_fim'";

    /*echo $unidade;
    echo $nome_und;*/
	
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
    else */
	if ($codigos <> '')
    {
      $sql = $sql." and und.id_unidade in ($codigos)";
    }

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";
      
    $sql = $sql." group by und.nome, cid.nome, und01.nome, pac.nome, mat.descricao, mvg.data_movto, mvg.id_movto_geral";
    $sql = $sql." order by und.nome, cid.nome, und01.nome, ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." mvg.data_movto";
        break;
      case 1:
        $sql = $sql." mat.descricao";
        break;
      case 2:
        $sql = $sql." pac.nome";
        break;
    }
    //echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $fill = 0;
      $cont_linhas = 0;
      while($linha = mysqli_fetch_array($sql_query))
      {
        $und_atual = $linha['unidade'];
        $cid_atual = $linha['cidade'];
        if ($linha['und_ref'] == "")
        {
          $und_ref_atual = "---";
        }
        else
        {
          $und_ref_atual = $linha['und_ref'];
        }

        if ($cont_linhas >= 24)
        {
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho($und_atual);
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho_tabela($cid_atual, $und_ref_atual);
          $cont_linhas = 2;
          $cid_anterior = $cid_atual;
          $und_ref_anterior = $und_ref_atual;
        }
        
        if (($und_anterior == '') or ($und_atual <> $und_anterior))
        {
          $und_anterior = $und_atual;
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho($und_atual);
          $fill = 0; $cont_linhas = 1;
          $cid_anterior = '';
        }

        if (($cont_linhas+3) >= 24)
        {
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho($und_atual);
          $cont_linhas = 2;
          $cid_anterior = $cid_atual;
          $und_ref_anterior = $und_ref_atual;
        }

        if ((($cid_anterior == '') and ($und_ref_anterior == ''))
            or (($cid_atual <> $cid_anterior) or ($und_ref_atual <> $und_ref_anterior)))
        {
          $cid_anterior = $cid_atual;
          $und_ref_anterior = $und_ref_atual;
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho_tabela($cid_atual, $und_ref_atual);
          $fill = 0;  $cont_linhas = $cont_linhas + 2;
        }
        
        $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
        //$pdf->Cell($w[0],5," ".$linha['paciente']." - $cont_linhas",'LR',0,'L',$fill);
        $pdf->Cell($w[0],5," ".$linha['paciente']."",'LR',0,'L',$fill);
        $pdf->Cell($w[1],5,substr($linha['medicamento'],0,48),'LR',0,'L',$fill);
        $pdf->Cell($w[2],5,intval($linha['quantidade'])." ",'LR',0,'R',$fill);
        $pdf->Cell($w[3],5,$dt_ret,'LR',0,'C',$fill);
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 1;
      }
      $pdf->Cell(array_sum($w),0,'','T');
    }
    else{
      cabecalho($nome_und);
      $pdf->Cell(array_sum($w),0,'','T');
      cabecalho_tabela("", "");
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
    }


    $pdf->Output();
    $pdf->Close();
}
?>
