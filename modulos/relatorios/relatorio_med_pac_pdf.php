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
// | Arquivo ............: relatorio_med_pac_pdf.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Medicamentos por Paciente (.pdf)             |
// | Data de Criação ....: 17/01/2007 - 11:15                                        |
// | Última Atualização .: 16/03/2007 - 10:50                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+


$header = array('Nr Receita','Medicamento','Lote','Fabricante','Validade','Qtde Retirada','Data da Retirada');
$w = array(22,95,28,59,20,25,28);

function cabecalho($nome_und_at)
{
  global $pdf, $data_in, $data_fn, $nome_und, $nome_pac, $nome_med, $status, $header, $w;

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

  $pdf->Cell(28,5,"     Paciente:",0,0,"L");
  $pdf->Cell(150,5,$nome_pac,0,0,"L");

  $pdf->Cell(15,5,"Status:",0,0,"L");
  $pdf->Cell(0,5,$status,0,1,"L");
    
  $pdf->Cell(28,5,"     Medicamento:",0,0,"L");
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

  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  $nome_und = $_POST['unidade01'];
  $paciente = $_POST['paciente'];
  $nome_pac = $_POST['paciente01'];
  $status = $_POST['status'];
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
    erro_sql("Select Aplicação", $db, "");
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

    $sql = "select distinct und.nome as unidade, mat.codigo_material as codigo, mat.descricao as medicamento,
                   img.lote as lote, fab.descricao as fabricante, img.validade as validade,
                   img.qtde as quantidade, mvg.data_movto as data_retirada,
                   CONCAT(rec.ano,'-',rec.unidade_id_unidade,'-',rec.numero) as nr_receita
            from material mat
                 inner join itens_movto_geral img on mat.id_material = img.material_id_material
                 inner join fabricante fab on img.fabricante_id_fabricante = fab.id_fabricante
                 inner join movto_geral mvg on img.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join paciente pac on mvg.paciente_id_paciente = pac.id_paciente
                 inner join itens_receita irc on img.itens_receita_id_itens_receita = irc.id_itens_receita
                 inner join receita rec on irc.receita_id_receita = rec.id_receita
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
    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      $sql = $sql." and und.id_unidade in ($unidades)";
    }

    if (($paciente <> '') and ($nome_pac <> ''))
      $sql = $sql." and pac.id_paciente = '$paciente'";

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    $sql = $sql." order by und.nome, ";

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
        $sql = $sql." mat.descricao";
        break;
      case 4:
        $sql = $sql." CONCAT(rec.ano,'-',rec.unidade_id_unidade,'-',rec.numero)";
        break;
      //$sql = $sql." order by und.nome";
    }
    //echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $fill = 0;
      $cont_linhas = 0;
      $cont_und=1;
      $qtd_med=0;
      $qtd=0;
      $qtd_r=0;
      
      while($linha = mysqli_fetch_array($sql_query))
      {
        $qtd++;
        $und_atual = $linha['unidade'];
        
    //    if ($qtd_r == 0)
    //       { $und_anterior2 = $und_atual;}

           if ($und_atual <> $und_anterior)
           {
            $pdf->Cell(array_sum($w),0,'','T');
            $pdf->Ln();
            $pdf->Cell(35,5,"Receitas por Unidade:",0,0,"L");
            $pdf->Cell(175,5,$qtd_r,0,0,"L");
            $pdf->Cell(33,5,"Total Qtde Retirada:",0,0,"L");
            $pdf->Cell(20,5,$qtd_med,0,0,"L");
            $und_anterior2 = und_atual;
            $qtd_r = 0;
            $cont_und++;
            $qtd_gr+=$qtd_med;
            $qtd_med=0;
                       }
       
        if (($und_anterior == '') or ($und_atual <> $und_anterior))
        {
          $und_anterior = $und_atual;
          cabecalho($und_atual);
          $fill = 0;
          $cont_linhas = 0;
        }
        $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
        $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
        $pdf->Cell($w[0],5,$linha['nr_receita'],'LR',0,'C',$fill);
        $pdf->Cell($w[1],5,substr(" ".$linha['codigo']." - ".$linha['medicamento'],0,54),'LR',0,'L',$fill);
        $pdf->Cell($w[2],5,$linha['lote'],'LR',0,'L',$fill);
        $pdf->Cell($w[3],5,substr(" ".$linha['fabricante'],0,30),'LR',0,'L',$fill);
        $pdf->Cell($w[4],5,$validade,'LR',0,'C',$fill);
        $pdf->Cell($w[5],5,intval($linha['quantidade'])." ",'LR',0,'R',$fill);
        $pdf->Cell($w[6],5,$dt_ret,'LR',0,'C',$fill);
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 1;
        $qtd_r++;
        $qtd_med += $linha['quantidade'];
        
        if ($cont_linhas >= 24)
        {
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho($und_atual);
          $cont_linhas = 0;
        }
      }

      $pdf->Cell(array_sum($w),0,'','T');
      $pdf->Ln();
      $pdf->Cell(35,5,"Receitas por Unidade:",0,0,"L");
      $pdf->Cell(175,5,$qtd_r,0,0,"L");
      $pdf->Cell(33,5,"Total Qtde Retirada:",0,0,"L");
      $pdf->Cell(20,5,$qtd_med,0,0,"L");
      $pdf->Ln(7);
      $qtd_gr+=$qtd_med;
      $qtd_med=0;

      if ($cont_und > 1)
      {
       $pdf->Cell(25,5,"Total Receitas:",0,0,"L");
       $pdf->Cell(185,5,$qtd,0,0,"L");
       $pdf->Cell(33,5,"Total Geral Retiradas:",0,0,"L");
       $pdf->Cell(20,5,$qtd_gr,0,0,"L");
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

