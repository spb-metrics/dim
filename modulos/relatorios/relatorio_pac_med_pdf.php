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
// | Arquivo ............: relatorio_pac_med_pdf.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Pacientes por Medicamento (.pdf)             |
// | Data de Criação ....: 19/01/2007 - 14:30                                        |
// | Última Atualização .: 19/03/2007 - 10:55                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+
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
}*/

$header = array('Paciente/Endereço','Telef','Lote','Fabric','Valid','Qtde Ret','Data Ret');
$w = array(157,20,20,20,20,20,20);

function cabecalho($nome_und_at)
{
  global $pdf, $data_in, $data_fn, $nome_und, $nome_med, $nome_lote, $nome_fab;
   $nome_lote_aux = ereg_replace("\\\'", "", $nome_lote);
   
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
    $pdf->Cell(85,5,"Todos os Medicamentos",0,0,"L");
  else
    $pdf->Cell(85,5,$nome_med,0,0,"L");

  $pdf->Cell(10,5,"Lote:",0,0,"L");
  if ($nome_lote == '')
    $pdf->Cell(70,5,"Todos os Lotes",0,0,"L");
  else
    $pdf->Cell(70,5,$nome_lote_aux,0,0,"L");
    
  $pdf->Cell(28,5,"Fabricante:",0,0,"L");
  if ($nome_fab == '')
    $pdf->Cell(0,5,"Todos os Fabricantes",0,1,"L");
  else
    $pdf->Cell(0,5,$nome_fab,0,1,"L");

  $pdf->SetX(-10);
  $pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
  $pdf->Ln(4);
  $pdf->SetFont('','B');
  $pdf->Cell(25,5,"Unidade:",0,0,0);
  $pdf->SetFont('','');
  $pdf->Cell(0,5,$nome_und_at,0,0,"L");
  $pdf->Ln(6);
}

function cabecalho_tabela($nome_med_at)
{
  global $pdf, $header, $w;

  $pdf->Ln(4);
  $pdf->SetFont('','B');
  $pdf->Cell(25,5,"Medicamento:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(0,5,$nome_med_at,0,1,"L");
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
    //$unidade = $_POST['unidade'];
  if ($_POST['unidade01'] <> ''){
    $nome_und = $_POST['unidade01'];
  } else{
    $nome_und = $_POST['unidade02'];
	}
  $medicamento = $_POST['medicamento'];
  $nome_med = $_POST['medicamento01'];
  $lote = $_POST['lote'];
  $nome_lote = $_POST['lote01'];
  $fabricante = $_POST['fabricante'];
  $nome_fab = $_POST['fabricante01'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  /*$unidade_tds = $_POST['unidade_tds'];
  $fabricante_tds = $_POST['fabricante_tds'];
  $medicamento_tds = $_POST['medicamento_tds'];*/
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

    $sql = "select distinct und.nome as unidade, pac.nome as paciente, mat.codigo_material as codigo, mat.descricao as medicamento, img.lote as lote,
                   fab.descricao as fabricante, img.validade as validade, img.qtde as quantidade, mvg.data_movto as data_retirada,
                   pac.tipo_logradouro, pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, pac.telefone
            from material mat
                 inner join itens_movto_geral img on mat.id_material = img.material_id_material
                 inner join fabricante fab on img.fabricante_id_fabricante = fab.id_fabricante
                 inner join movto_geral mvg on img.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join paciente pac on mvg.paciente_id_paciente = pac.id_paciente
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and fab.status_2 = 'A'
                  and und.status_2 = 'A'
                  and pac.status_2 = 'A'";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(mvg.data_movto,1,10) between '$data_inicio' and '$data_fim'";

    /*echo $unidade;
    echo $nome_und;*/
	
	
	//echo "unidad". $unidade."<br>";
	    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      $sql = $sql." and und.id_unidade in ($unidades)";
    }

	
	
	/*
    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      //busca_nivel($unidade, $db);
      $sql = $sql." and und.id_unidade in ($unidades)";
    }
    else if ($codigos <> '')
    {
      $sql = $sql." and und.id_unidade in ($codigos)";
    }
 */
    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = $medicamento";

    if (($lote <> '') and ($nome_lote <> ''))
      $sql = $sql." and img.lote = \"$nome_lote\"";
      
    if (($fabricante <> '') and ($nome_fab <> ''))
      $sql = $sql." and fab.id_fabricante = '$fabricante'";

    $sql = $sql." order by und.nome, mat.descricao,  ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." mvg.data_movto";
        break;
      case 1:
        $sql = $sql." fab.descricao";
        break;
      case 2:
        $sql = $sql." img.lote";
        break;
      case 3:
        $sql = $sql." pac.nome";
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
      $cont_linhas = 2;
      $cont_med = 0;
      $qtd_med=0;
      $qtd_m=0;
      $qtd=0;
      while($linha = mysqli_fetch_array($sql_query)){
            $qtd++;
            $und_atual = $linha['unidade'];
            $med_atual = $linha['codigo']." - ".$linha['medicamento'];

            if($qtd_m == 0){
              $med_ant = $med_atual;
            }
            if(($med_atual <> $med_ant)or ($und_atual <> $und_anterior)){
              $pdf->Cell(array_sum($w),0,'','T');
              $pdf->Ln();
              $pdf->Cell(43,5,"Pacientes por Medicamentos:",0,0);
              $pdf->Cell(166,5,$qtd_m,0,0);
              $pdf->Cell(32,5,"Total Retirada:",0,0);
              $pdf->Cell(20,5,$qtd_med,0,0);
              $pdf->Ln(3);
              $med_ant = $med_atual;
              $qtd_m = 0;
              $cont_linhas = $cont_linhas + 1;
              $cont_med++;
              $qtd_gr+=$qtd_med;
              $qtd_med=0;
            }
            if($cont_linhas >= 27){
//              $pdf->Cell(array_sum($w),0,'','T');
              cabecalho($und_atual);
              $cont_linhas = 2;
              $med_anterior = '';
            }
            if(($med_atual <> $med_anterior) && ($cont_linhas >= 24))
            {
               cabecalho($und_atual);
               $cont_linhas = 2;
               $med_anterior = '';
            }
            if($und_anterior == ''){
              //$pdf->Cell(array_sum($w),0,'','T');
              $pdf->Ln();
              $und_anterior = $und_atual;
              cabecalho($und_atual);
              $fill = 0;
              //$pdf->Cell(array_sum($w),0,'','T');
              $med_anterior = '';
              $cont_linhas = 0;
              $qtd_m = 0;
            }
            if($und_atual <> $und_anterior){
      /*      $pdf->Cell(array_sum($w),0,'','T');
              $pdf->Ln();
              $pdf->Cell(43,5,"Pacientes por Medicamento:",0,0);
              $pdf->Cell(166,5,$qtd_m,0,0);
              $pdf->Cell(32,5,"Total Retirada:",0,0);
              $pdf->Cell(20,5,$qtd_med,0,0);*/
              $med_anterior = '';
              $cont_linhas = 3;
              $qtd_m = 0;
              $und_anterior = $und_atual;
              cabecalho($und_atual);
              $fill = 0;
            }
            if(($med_anterior == '') or ($med_atual <> $med_anterior)){
              $med_anterior = $med_atual;
              //$pdf->Cell(array_sum($w),0,'','T');
              cabecalho_tabela($med_atual);
              $fill = 0;
              $cont_linhas = $cont_linhas + 3;
            }
            $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
            $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
            $pdf->Cell($w[0],5," ".$linha['paciente'],'LR',0,'L',$fill);
            $pdf->Cell($w[1],5," ",'LR',0,'L',$fill);
            $pdf->Cell($w[2],5,$linha['lote'],'LR',0,'L',$fill);
            $pdf->Cell($w[3],5,substr(" ".$linha['fabricante'],0,30),'LR',0,'L',$fill);
            $pdf->Cell($w[4],5,$validade,'LR',0,'C',$fill);
            $pdf->Cell($w[5],5,intval($linha['quantidade'])." ",'LR',0,'R',$fill);
            $pdf->Cell($w[6],5,$dt_ret,'LR',0,'C',$fill);
            $pdf->Ln();
            $qtd_med+= $linha['quantidade'];
            $fill=!$fill;
            $cont_linhas = $cont_linhas + 1;
            $qtd_m++;
            if($cont_linhas >= 27){
              cabecalho($und_atual);
              $cont_linhas = 2;
              $med_anterior = '';
            }
            $endereco=$linha["tipo_logradouro"] . " " . $linha["nome_logradouro"] . " " . $linha["numero"] . " " . $linha["complemento"] . " " . $linha["bairro"];
            $pdf->Cell($w[0],5," ".$endereco,'LRB',0,'L',$fill);
            $pdf->Cell($w[1],5," " . $linha["telefone"],'LRB',0,'L',$fill);
            $pdf->Cell($w[2],5," ",'LRB',0,'L',$fill);
            $pdf->Cell($w[3],5," ",'LRB',0,'L',$fill);
            $pdf->Cell($w[4],5," ",'LRB',0,'C',$fill);
            $pdf->Cell($w[5],5," ",'LRB',0,'R',$fill);
            $pdf->Cell($w[6],5," ",'LRB',0,'C',$fill);
            $pdf->Ln();
            $fill=!$fill;
            $cont_linhas = $cont_linhas + 1;
      }
      $pdf->Cell(array_sum($w),0,'','T');
      $pdf->Ln();
      $pdf->Cell(43,5,"Pacientes por Medicamento:",0,0);
      $pdf->Cell(166,5,$qtd_m,0,0);
      $pdf->Cell(32,5,"Total Retirada:",0,0);
      $pdf->Cell(20,5,$qtd_med,0,0);
      $pdf->Ln(6);
      $qtd_gr+=$qtd_med;
      $qtd_med=0;
      $fill=!$fill;
      $cont_linhas = $cont_linhas + 1;
      if($cont_linhas >= 27){
        cabecalho($und_atual);
        $cont_linhas = 2;
        $med_anterior = '';
      }

      if($cont_med > 1){
        $pdf->Cell(35,5,"Total Geral Pacientes:",0,0);
        $pdf->Cell(165,5,$qtd,0,0);
        $pdf->Cell(40,5,"Total Geral Retiradas:",0,0);
        $pdf->Cell(10,5,$qtd_gr,0,0);
      }
    }
    else{
         cabecalho($nome_und);
         $pdf->Cell(array_sum($w),0,'','T');
         cabecalho_tabela("");
         $pdf->SetFont('Arial','B',12);
         $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
         }
    $pdf->Output();
    $pdf->Close();
  }
?>
