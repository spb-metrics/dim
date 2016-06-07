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
// | Arquivo ............: relatorio_mov_mat_pdf.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Movimentação de Materiais (.pdf)             |
// | Data de Criação ....: 20/01/2007 - 12:15                                        |
// | Última Atualização .: 19/03/2007 - 10:55                                        |
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

$header = array('Tipo Movto','Nº Doc','Medicamento','Lote','Fabricante','Validade','Quantidade','Data Movto');
$w = array(50,19,93,25,27,18,24,22);

function cabecalho($nome_und_at)
{
  global $pdf, $data_in, $data_fn, $nome_und, $tipo_mov, $desc_mov, $nome_med, $header, $w;

  $pdf->AddPage();
  $pdf->Ln();

  $pdf->SetFont('Arial','B',9);
  $pdf->Cell(22,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
  $pdf->SetFont('Arial','',9);
  
  $pdf->Cell(35,5,"     Tipo de Movimento:",0,0,"L");
  $pdf->Cell(0,5,$tipo_mov." - ".$desc_mov,0,1,"L");
  
  $pdf->Cell(35,5,"     Período:",0,0,"L");
  $pdf->Cell(0,5,$data_in."  à  ".$data_fn,0,1,"L");

  $pdf->Cell(35,5,"     Unidade:",0,0,"L");
  if ($nome_und == '')
    $pdf->Cell(0,5,"Todas as Unidades",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_und,0,1,"L");

  $pdf->Cell(35,5,"     Medicamento:",0,0,"L");
  if ($nome_med == '')
    $pdf->Cell(0,5,"Todos os Medicamentos",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_med,0,1,"L");

  $pdf->SetX(-10);
  $pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
  $pdf->Ln(4);
  $pdf->SetFont('','B');
  $pdf->Cell(22,5,"Unidade:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(0,5,$nome_und_at,0,0,"L");
  $pdf->Ln(6);

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
  set_time_limit(0);
  //echo "**".$_POST['data_in'];
  //echo exit;
  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  if ($_POST['unidade01'] <> '')
    $nome_und = $_POST['unidade01'];
  else
    $nome_und = $_POST['unidade02'];
  $tipo_mov = $_POST['operacao'];
  $movimento = $_POST['descricao'];
  $medicamento = $_POST['medicamento'];
  $nome_med = $_POST['medicamento01'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  $codigos = $_POST['codigos'];
  
  if ($movimento == 0)
  {
    $desc_mov = "TODOS OS MOVIMENTOS";
  }
  else
  {
    $sql = "select descricao
            from tipo_movto
            where id_tipo_movto = $movimento";
    $sql_query = mysqli_query($db, $sql);

    erro_sql("Tipo Movto", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $desc_mov = strtoupper($linha['descricao']);
    }
  }

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

    $sql = "select distinct tmv.descricao as tipo_movto, und.nome as unidade, mvg.id_movto_geral as documento, mat.codigo_material as codigo, mat.descricao as medicamento, img.lote as lote,
                   fab.descricao as fabricante, img.validade as validade, img.qtde as quantidade, mvg.data_movto as data_retirada, mvg.motivo
            from material mat
                 inner join itens_movto_geral img on mat.id_material = img.material_id_material
                 inner join fabricante fab on img.fabricante_id_fabricante = fab.id_fabricante
                 inner join movto_geral mvg on img.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join tipo_movto tmv on mvg.tipo_movto_id_tipo_movto = tmv.id_tipo_movto
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and fab.status_2 = 'A'
                  and und.status_2 = 'A'";

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
	
	/*
    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and und.id_unidade in ($unidades)";
    }
    else */ if ($codigos <> '')
    {
      $sql = $sql." and und.id_unidade in ($codigos)";
    }

    if ($tipo_mov <> '')
      $sql = $sql." and tmv.operacao = '$tipo_mov'";

    if ((($movimento <> '') and ($movimento <> '0')) and ($desc_mov <> ''))
      $sql = $sql." and tmv.id_tipo_movto = $movimento";

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    $sql = $sql." order by und.nome, ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." mvg.data_movto";
        break;
      case 1:
        $sql = $sql." mvg.id_movto_geral";
        break;
      case 2:
        $sql = $sql." fab.descricao";
        break;
      case 3:
        $sql = $sql." img.lote";
        break;
      case 4:
        $sql = $sql." mat.descricao";
        break;
      case 5:
        $sql = $sql." tmv.descricao";
        break;
    }
    
//echo $sql;
//echo exit;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    $qtde_doc = 0;
    $qtd_t = 0;
    $qtde_t_doc=0;
    if (mysqli_num_rows($sql_query) > 0){

      $fill = 0;
      $cont_linhas = 0;
      $info="";
      $docs=array(mysqli_num_rows($sql_query));
      $indice=0;
      $qtde_t_doc=0;
      $qtd_t=0;
      $qtde_doc=0;
      $cont_und=0;
      while($linha = mysqli_fetch_array($sql_query)){
        if($info!=""){
          $flag="sim";
          $valores=split("[|]", $info);
          for($i=0; $i<count($valores); $i++){
            if($valores[$i]==$linha[documento]){
              $flag="nao";
              break;
              }
          }
          if($flag=="sim"){
            $qtde++;
            }
          }
        else{
            $qtde++;
            }
        $info.=$linha[documento] . "|";
        $und_atual = $linha['unidade'];
        
        if(($und_anterior == '') or ($und_atual <> $und_anterior)){
          $und_anterior = $und_atual;
          $result=array_unique($docs);
          $qtde_doc=count($result);
          $pdf->Cell(array_sum($w),0,'','T');
          $pdf->Ln();
          $pdf->Cell(50,5,"Total de Documentos: ",0,0);
          $pdf->Cell(176,5,$qtde_doc,0,0);
          $pdf->Cell(20,5,"Qtde total:",0,0);
          $pdf->Cell(20,5,$qtd_t,0,0);
          $pdf->Ln(6);
          $qtde_t_doc+=$qtde_doc;
          $qtd_g+=$qtd_t;
          $qtd_t=0;
          $qtde_doc=0;
          unset($docs);
          cabecalho($und_atual);
          $fill = 0;
          $cont_linhas = 0;
          $indice=0;
          $cont_und++;
          }
        $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
        $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
        $pdf->Cell($w[0],5," ".$linha['tipo_movto'],'LR',0,'L',$fill);
        $pdf->Cell($w[1],5," ".$linha['documento'],'LR',0,'R',$fill);
        $pdf->Cell($w[2],5,substr(" ".$linha['codigo']." - ".$linha['medicamento'],0,45),'LR',0,'L',$fill);
        $pdf->Cell($w[3],5,$linha['lote'],'LR',0,'L',$fill);
        $pdf->Cell($w[4],5,substr(" ".$linha['fabricante'],0,27),'LR',0,'L',$fill);
        $pdf->Cell($w[5],5,$validade,'LR',0,'C',$fill);
        $pdf->Cell($w[6],5,intval($linha['quantidade'])." ",'LR',0,'R',$fill);
        $pdf->Cell($w[7],5,$dt_ret,'LR',0,'C',$fill);
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 1;
        $qtd_t+=$linha['quantidade'];
        $docs[$indice]=$linha['documento'];
        $indice++;

        if($cont_linhas >= 24){
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho($und_atual);
          $cont_linhas = 0;
          }
      }
      $result=array_unique($docs);
      $qtde_doc=count($result);
      $qtde_t_doc+=$qtde_doc;
      $qtd_g+=$qtd_t;
      $pdf->Cell(array_sum($w),0,'','T');
      $pdf->Ln();
      $pdf->Cell(50,5,"Total de Documentos: ",0,0);
      $pdf->Cell(176,5,$qtde_doc,0,0);
      $pdf->Cell(20,5,"Qtde total:",0,0);
      $pdf->Cell(20,5,$qtd_t,0,0);
      if ($cont_und >1){
         $pdf->Ln();
         $pdf->Cell(50,5,"Total Geral Documentos: ",0,0);
         $pdf->Cell(176,5,$qtde_t_doc-1,0,0);
         $pdf->Cell(20,5,"Total Geral:",0,0);
         $pdf->Cell(20,5,$qtd_g,0,0);
         $pdf->Ln(6);
         }
    }
    else{
      cabecalho($nome_und);
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
    }
    $pdf->Output();
    $pdf->Close();
}
?>
