<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  //header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");

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

  $header=array('Código','Medicamento','Validade','Lote','Fabricante','Estoque');
  $w=array(15,135,22,25,60,20);
  
  function cabecalho(){
    global $pdf, $data_in, $data_fn, $nome_und, $nome_fab, $nome_med, $header, $w;

    $pdf->AddPage();
    $pdf->Ln();

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
    for($i = 0; $i < count($header); $i++){
      $pdf->Cell($w[$i],5,$header[$i],'LTR',0,'C',1);
    }
    $pdf->Ln(5.4);

    //Color and font restoration
    /*$pdf->SetFillColor(224,235,255);
    $pdf->SetTextColor(0);*/
    $pdf->SetFont('');
  }

  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    $id_unidade=$_GET['id_unidade'];
    $und_user=$_GET['nome_unidade'];
    $aplicacao=$_GET['aplicacao'];

    require "../../fpdf152/Class.Pdf.inc.php";
    DEFINE("FPDF_FONTPATH","font/");

    $pdf=new PDF('L','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)

    $sql="select apl.executavel, ime.descricao
          from aplicacao apl, item_menu ime
          where apl.id_aplicacao=$aplicacao
                and ime.aplicacao_id_aplicacao=$aplicacao";
    $sql_query=mysqli_query($db, $sql);
    erro_sql("Aplicação", $db, "");
    //echo mysqli_error();
    if(mysqli_num_rows($sql_query) > 0){
      $linha=mysqli_fetch_array($sql_query);
      $executavel=$linha['executavel'];
      $nome_rel=$linha['descricao'];
    }
    $pos=strrpos($executavel, "/");
    if($pos===false){
      $aplic=$executavel;
    }
    else{
      $aplic=substr($executavel, $pos+1);
    }
    $pdf->SetName($nome_rel);
    $pdf->SetUnd($und_user);
    $pdf->SetNomeAplic($aplic);
    $pdf->Open();
    cabecalho();

    $sql="select mat.codigo_material,
                 mat.descricao as medicamento,
                 DATE_FORMAT(est.validade,'%d/%m/%Y') as validade,
                 est.lote,
                 fab.descricao as fabricante,
                 est.quantidade
          from estoque as est,
               material as mat,
               fabricante as fab
          where est.material_id_material=mat.id_material and
                est.fabricante_id_fabricante=fab.id_fabricante and
                est.unidade_id_unidade=$id_unidade and
                est.quantidade>0 and
                fab.status_2='A' and
                mat.status_2='A' and
                mat.flg_dispensavel='S' and
                mat.status_2='A'
          order by mat.descricao,
                   est.validade,
                   est.lote,
                   fab.descricao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Itens Relatório", $db, "");
    if(mysqli_num_rows($sql_query)>0){
      $fill=0;
      $cont_linhas=0;
      $mat_descricao="";
      $total=0;
      while($linha=mysqli_fetch_array($sql_query)){
        if($mat_descricao!="" && $mat_descricao!=$linha["medicamento"]){
          $pdf->Cell($w[0],5,"",'LTB',0,'R',$fill);
          $pdf->Cell($w[1],5,"",'TB',0,'L',$fill);
          $pdf->Cell($w[2],5,"",'TB',0,'C',$fill);
          $pdf->Cell($w[3],5,"",'TB',0,'L',$fill);
          $pdf->Cell($w[4],5,"",'TB',0,'L',$fill);
          $pdf->Cell($w[5],5, "Total: " . intval($total),'RTB',0,'R',$fill);
          $pdf->Ln();
          $cont_linhas=$cont_linhas + 1;
          $total=0;
        }
        if ($cont_linhas==27){
          $pdf->Cell(array_sum($w),0,'','T');
          cabecalho();
          $cont_linhas = 0;
        }
        $mat_descricao=$linha["medicamento"];
        $total+=$linha["quantidade"];
        $pdf->Cell($w[0],5,$linha['codigo_material'],'LRT',0,'R',$fill);
        $pdf->Cell($w[1],5,substr(" ".$linha['medicamento'],0,60),'LRT',0,'L',$fill);
        $pdf->Cell($w[2],5,$linha['validade'],'LRT',0,'C',$fill);
        $pdf->Cell($w[3],5,$linha['lote'],'LRT',0,'L',$fill);
        $pdf->Cell($w[4],5,substr(" ".$linha['fabricante'],0,28),'LRT',0,'L',$fill);
        $pdf->Cell($w[5],5,intval($linha['quantidade'])." ",'LRT',0,'R',$fill);
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas=$cont_linhas + 1;
        if ($cont_linhas==27){
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
