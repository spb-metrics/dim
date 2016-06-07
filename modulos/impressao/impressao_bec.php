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
  //  Arquivo..: impressao_bec.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de impressao de bec
  //////////////////////////////////////////////////////////////////

  $configuracao="../../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  session_start();

  $header=array('C�digo','Material','Qtde Almoxarifado Local','Qtde Farm�cia','Qtde Total', 'Situa��o');
  $w=array(25,100,40,40,40,32);

  function cabecalho(){
    global $pdf, $header, $w;

    $pdf->AddPage();
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(80,5,"Relat�rio de Transfer�ncia de Saldo para o Almoxafixado",0,1,"L");
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(40,5,"N�mero da Transfer�ncia: ",0,0,"L");
    $pdf->Cell(40,5, $_GET[numero],0,0,"L");
    $pdf->Cell(10,5,"Data: ",0,0,"L");
    $pdf->Cell(10,5, $_GET[data],0,1,"L");
    $pdf->Cell(40,5,"Sigla: ",0,0,"L");
    $pdf->Cell(40,5, $_GET[sigla],0,1,"L");

    $pdf->Ln(2);
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
      $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
    $pdf->Ln(5.4);

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

  if($_GET[numero]=="" || $_GET[aplicacao]=="" || $_GET[data]=="" || $_GET[sigla]==""){
    header("Location: ". URL."/modulos/bec/bec_inclusao.php");
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
    $sql="select * from aplicacao where id_aplicacao = '$_GET[aplicacao]'";
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
    $sql="select cod_material, descricao_material, qtde_sig2m, qtde_dim, status_2 ";
    $sql.="from pedido_bec where num_pedido_bec='$_GET[numero]' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Material", $db, "");
    while($resultado=mysqli_fetch_array($res)){
      $pdf->Cell($w[0],6,$resultado[cod_material],'LR',0,'L',$fill);
      $pdf->Cell($w[1],6,substr($resultado[descricao_material], 0, 46),'LR',0,'L',$fill);
      if(number_format($resultado[qtde_sig2m])==0){
        $pdf->Cell($w[2],6,number_format($resultado[qtde_sig2m], 0),'LR',0,'R',$fill);
      }
      else{
        $pdf->Cell($w[2],6,(int)$resultado[qtde_sig2m],'LR',0,'R',$fill);
      }
      if(number_format($resultado[qtde_dim])==0){
        $pdf->Cell($w[2],6,number_format($resultado[qtde_dim], 0),'LR',0,'R',$fill);
      }
      else{
        $pdf->Cell($w[2],6,(int)$resultado[qtde_dim],'LR',0,'R',$fill);
      }
      $qtde_total=0;
      $qtde_total+=(int)$resultado[qtde_sig2m]+(int)$resultado[qtde_dim];
      if($qtde_total==0){
        $pdf->Cell($w[4],6,"0",'LR',0,'R',$fill);
      }
      else{
        $pdf->Cell($w[4],6,$qtde_total,'LR',0,'R',$fill);
      }
      $pdf->Cell($w[5],6,$resultado[status_2],'LR',0,'L',$fill);
      $pdf->ln();
      $fill=!$fill;
      $cont_linhas=$cont_linhas + 1;
      if($cont_linhas==21){
        $pdf->Cell(array_sum($w),0,'','T');
        cabecalho();
        $cont_linhas=0;
      }
    }

    $pdf->Cell(array_sum($w),0,'','T');
    $pdf->Output();
    $pdf->Close();
  }
?>
