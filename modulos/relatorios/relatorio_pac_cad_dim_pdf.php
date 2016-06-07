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
// | Arquivo ............: relatorio_pac_cad_dim_pdf.php                             |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório Pacientes Cadastrados DIM (.pdf)                |
// | Data de Criação ....: 23/01/2007 - 13:35                                        |
// | Última Atualização .: 19/02/2007 - 10:55                                        |
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
}*/

$header = array('Paciente','Nome da Mãe','Data Nascimento','Sexo', 'Status', 'Endereço');
$w = array(120,97,30,15,15,277);

function cabecalho()
{
  global $pdf, $data_in, $data_fn, $nome_und;

  $pdf->AddPage();
  $pdf->Ln();

  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(22,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(38,5,"     Período:",0,0,"L");
  $pdf->Cell(0,5,$data_in."  à  ".$data_fn,0,1,"L");

  $pdf->Cell(38,5,"     Unidade de Cadastro:",0,0,"L");
  if ($nome_und == '')
    $pdf->Cell(0,5,"Todas as Unidades",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_und,0,1,"L");

  $pdf->SetX(-10);
  $pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
}

function cabecalho_tabela($nome_und_cad, $nome_und_ref)
{
  global $pdf, $header, $w;

  $pdf->Ln(4);
  $pdf->SetFont('Arial','B');
  $pdf->Cell(36,5,"Unidade de Cadastro:",0,0,"L");
  $pdf->SetFont('Arial','');
  $pdf->Cell(50,5,$nome_und_cad,0,0,"L");
  $pdf->SetFont('Arial','B');
  $pdf->Cell(30,5,"Unidade Referida:",0,0,"L");
  $pdf->SetFont('Arial','');
  $pdf->Cell(0,5,$nome_und_ref,0,1,"L");
  //$pdf->Ln(5);

  //Colors, line width and bold font
  /*$pdf->SetFillColor(14,90,152);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(255);  // cor do texto*/
  $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(0);  // cor do texto

  //$pdf->SetDrawColor(0,0,0);  // cor da linha
  $pdf->SetLineWidth(.3);
  $pdf->SetFont('','B');

  //Header
  for($i = 0; $i < count($header)-1; $i++)
    $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
  $pdf->Ln();
  $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
  $pdf->Ln();

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

    $sql = "select pac.nome as paciente, pac.nome_mae, pac.data_nasc, pac.sexo, pac.status_2,
                   pac.tipo_logradouro, pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro,
                   und01.nome as und_cad, und02.nome as und_ref, cid.nome as cidade
            from paciente pac
                 left join cartao_sus cart on pac.id_paciente = cart.paciente_id_paciente
                 inner join unidade und01 on pac.unidade_cadastro = und01.id_unidade
                 inner join unidade und02 on pac.unidade_referida = und02.id_unidade
                 inner join cidade cid on pac.cidade_id_cidade = cid.id_cidade
            where (cart.cartao_sus is NULL or cart.cartao_sus = 0)";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(pac.data_incl,1,10) between '$data_inicio' and '$data_fim'";
		
		
	if (($unidade <> '') and ($nome_und <> '')){
      $unidades = $unidade;
      $sql = $sql." and pac.unidade_cadastro in ($unidades)";	  
    }else {		
		$uni_sup = $_SESSION[id_unidade_sistema];		
		$ids_unidades =	"\"-1\"".rec($uni_sup,$db);
		$sql = $sql."and pac.unidade_cadastro in ($ids_unidades)";
	}
    
	
	/*if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and pac.unidade_cadastro in ($unidades)";
    }
    else */if ($codigos <> '')
    {
      $sql = $sql." and pac.unidade_cadastro in ($codigos)";
    }

    $sql = $sql." order by und01.nome, und02.nome, ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." pac.data_nasc";
        break;
      case 1:
        $sql = $sql." pac.nome_mae";
        break;
      case 2:
        $sql = $sql." pac.nome";
        break;
      case 3:
        $sql = $sql." pac.sexo";
        break;
      case 4:
        $sql = $sql." pac.status_2";
        break;
    }
    //echo $sql;
	//exit;
	
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $fill = 0;
      $cont_linhas = 0;
      while($linha = mysqli_fetch_array($sql_query))
      {
        $und_cad_atual = $linha['und_cad'];
        $und_ref_atual = $linha['und_ref'];
        
        if ($cont_linhas >= 23)
        {
          $pdf->Cell(array_sum($w)-$w[5],0,'','T');
          cabecalho();
          cabecalho_tabela($und_cad_atual, $und_ref_atual);
          $cont_linhas = 3;
          $und_cad_anterior = $und_cad_atual;
          $und_ref_anterior = $und_ref_atual;
        }

        if (($und_cad_anterior == '') and ($und_ref_anterior == ''))
        {
          $und_cad_anterior = $und_cad_atual;
          $und_ref_anterior = $und_ref_atual;
          $pdf->Cell(array_sum($w)-$w[5],0,'','T');
          cabecalho();
          cabecalho_tabela($und_cad_atual, $und_ref_atual);
          $fill = 0;   $cont_linhas = $cont_linhas + 4;
        }
        
        if (($und_cad_atual <> $und_cad_anterior) or ($und_ref_atual <> $und_ref_anterior))
        {
          $und_cad_anterior = $und_cad_atual;
          $und_ref_anterior = $und_ref_atual;
          $pdf->Cell(array_sum($w)-$w[5],0,'','T');
          cabecalho_tabela($und_cad_atual, $und_ref_atual);
          $fill = 0;   $cont_linhas = $cont_linhas + 2;
        }
        $dt_nasc = ((substr($linha['data_nasc'],8,2))."/".(substr($linha['data_nasc'],5,2))."/".(substr($linha['data_nasc'],0,4)));
        $endereco = " ".$linha['tipo_logradouro']." ".$linha['nome_logradouro'].", ".$linha['numero'];
        $endereco = $endereco.", ".$linha['complemento']." - ".$linha['bairro']." - ".$linha['cidade'];
        $pdf->Cell($w[0],5," ".$linha['paciente'],'LT',0,'L',$fill);
        $pdf->Cell($w[1],5,$linha['nome_mae'],'T',0,'L',$fill);
        $pdf->Cell($w[2],5,$dt_nasc,'T',0,'C',$fill);
        $pdf->Cell($w[3],5,$linha['sexo'],'T',0,'C',$fill);
        $pdf->Cell($w[4],5,$linha['status_2'],'TR',0,'C',$fill);
        $pdf->Ln();
        $pdf->Cell($w[5],5,$endereco,'LR',1,'L',$fill);
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 2;
      }
      $pdf->Cell(array_sum($w)-$w[5],0,'','T');
    }
    else{
      cabecalho();
      cabecalho_tabela("", "");
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
    }

    $pdf->Output();
    $pdf->Close();
}
?>
