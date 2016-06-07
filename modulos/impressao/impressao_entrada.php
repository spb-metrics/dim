<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: impressao_entrada.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de impressao de entrada
  //////////////////////////////////////////////////////////////////

  $configuracao="../../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  session_start();

  $header=array('C�digo','Material','Fabricante','Lote','Validade','Quantidade');
  $w=array(30,90,60,35,30,30);

  function cabecalho(){
    global $pdf, $header, $w;

    $pdf->AddPage();
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    $pdf->Cell(35,5,"N�mero do Documento: ",0,0,"L");
    $pdf->Cell(40,5, $_GET[numero],0,0,"L");
    $pdf->Cell(10,5,"Data: ",0,0,"L");
    $pos1=strpos($_GET[data], "-");
    $pos2=strrpos($_GET[data], "-");
    $data_info=substr($_GET[data], $pos2+1, 2) . "/" . substr($_GET[data], $pos1+1, 2) . "/" . substr($_GET[data], 0, 4) . substr($_GET[data], $pos2+3, strlen($_GET[data]));
    $pdf->Cell(10,5, $data_info,0,1,"L");

    $pdf->Ln(5);
    //$pdf->SetX(-10);
    //$pdf->Line(10,50,$pdf->GetX(),50);

    //Colors, line width and bold font
//    $pdf->SetFillColor(14,90,152);  // cor do fundo do cabe�alho da tabela
//    $pdf->SetTextColor(255);  // cor do texto
    $pdf->SetFillColor(255,255,255);  // cor do fundo do cabe�alho da tabela
    $pdf->SetTextColor(0);  // cor do texto

    //$pdf->SetDrawColor(0,0,0);  // cor da linha
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('','B');

    //Header
    for($i = 0; $i < count($header); $i++)
      $pdf->Cell($w[$i],5,$header[$i],1,0,'C',1);
    $pdf->Ln();

    //Color and font restoration
//    $pdf->SetFillColor(224,235,255);
//    $pdf->SetTextColor(0);
    $pdf->SetFont('');
  }

  ////////////////////////////
  //VERIFICA��O DE SEGURAN�A//
  ////////////////////////////

  if($_SESSION[id_usuario_sistema]==''){
    header("Location: ". URL."/start.php");
    exit();
  }

  if($_GET[numero]=="" || $_GET[data]=="" || $_GET[chave]==""){
    header("Location: ". URL."/modulos/entrada/entrada_inclusao.php");
    exit();
  }
  else{
    require "../../fpdf152/Class.Pdf.inc.php";

    DEFINE("FPDF_FONTPATH","font/");
    $pdf=new PDF('L','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)

    //obtem o nome da unidade
    $sql="select * from unidade where id_unidade='$_SESSION[id_unidade_sistema]'";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Unidade", $db, "");
    if(mysqli_num_rows($res)>0){
      $unidade_info=mysqli_fetch_object($res);
    }
    $pdf->SetUnd($unidade_info->nome);
    
    //seleciona o nome e a pagina da aplicacao
    $sql="select * from aplicacao where id_aplicacao = '$_SESSION[APLICACAO]'";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Aplica��o", $db, "");
    if(mysqli_num_rows($res)>0){
      $aplicacao_info=mysqli_fetch_object($res);
    }
    $pdf->SetName($aplicacao_info->descricao);
    //obtem a pagina da aplicacao
    $executavel=$aplicacao_info->executavel;
    $pos=strrpos($executavel, "/");
    if($pos===false){
      $aplic=$executavel;
    }
    else{
      $aplic=substr($executavel, $pos+1);
    }
    $pdf->SetNomeAplic($aplic);
    $pdf->Open();
    cabecalho();

    $fill=0;
    $cont_linhas=0;
    $sql="select mat.codigo_material, mat.descricao as mdescricao,
                 fab.descricao as fdescricao, imov.lote, imov.validade, imov.qtde
          from movto_geral as mov, itens_movto_geral as imov, material as mat,
               fabricante as fab
          where mov.id_movto_geral=imov.movto_geral_id_movto_geral and
                imov.material_id_material=mat.id_material and mat.status_2='A' and
                imov.fabricante_id_fabricante=fab.id_fabricante and fab.status_2='A' and
                mov.id_movto_geral='$_GET[chave]'";
    $result=mysqli_query($db, $sql);
    erro_sql("�tens Relat�rio", $db, "");
    while($itens=mysqli_fetch_object($result)){
      $pdf->Cell($w[0],6,$itens->codigo_material,'LR',0,'L',$fill);
      $pdf->Cell($w[1],6,substr($itens->mdescricao, 0, 46),'LR',0,'L',$fill);
      $pdf->Cell($w[2],6,substr($itens->fdescricao, 0, 28),'LR',0,'L',$fill);
      $pdf->Cell($w[3],6,$itens->lote,'LR',0,'L',$fill);
      $valid=substr($itens->validade, 8, 2) . "/" . substr($itens->validade, 5, 2) . "/" . substr($itens->validade, 0, 4);
      $pdf->Cell($w[4],6,$valid,'LR',0,'C',$fill);
      $pdf->Cell($w[5],6,(int)$itens->qtde,'LR',0,'R',$fill);
      $pdf->ln();
      $index=0;
      $fill=!$fill;
      $cont_linhas=$cont_linhas + 1;
      if($cont_linhas==21){
        $pdf->Cell(array_sum($w),0,'','T');
        cabecalho($db);
        $cont_linhas=0;
      }
    }
    $pdf->Cell(array_sum($w),0,'','T');
    $pdf->Output();
    $pdf->Close();
  }
?>
