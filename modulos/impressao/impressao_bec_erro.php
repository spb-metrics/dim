<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: impressao_bec_erro.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de impressao de bec - erro
  //////////////////////////////////////////////////////////////////

  $configuracao="../../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  session_start();

  $header=array('Código','Material');
  $w=array(138,138);

  function cabecalho(){
    global $pdf, $header, $w;

    $pdf->AddPage();
    $pdf->Ln();

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(80,5,"Relatório de Erros de Transferência de Saldo para o Almoxafixado: ",0,1,"L");

    $pdf->Ln(5);
    //$pdf->SetX(-10);
    //$pdf->Line(10,50,$pdf->GetX(),50);

    //Colors, line width and bold font
//    $pdf->SetFillColor(14,90,152);  // cor do fundo do cabeçalho da tabela
//    $pdf->SetTextColor(255);  // cor do texto
    $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
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
  //VERIFICAÇÃO DE SEGURANÇA//
  ////////////////////////////

  if($_SESSION[id_usuario_sistema]==''){
    header("Location: ". URL."/start.php");
    exit();
  }

  if($_GET[nro]=="" || $_GET[aplicacao]==""){
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
    erro_sql("Select Aplicação", $db, "");
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

    $sql="select * from pedido_bec where num_pedido_bec='$_GET[nro]' and ";
    $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and status_2!='Transmitido'";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Pedido BEC", $db, "");
    while($resultado=mysqli_fetch_array($res)){
      $pdf->Cell($w[0],6,$resultado[cod_material],'LR',0,'L',$fill);
      $pdf->Cell($w[1],6,substr($resultado[descricao_material], 0, 46),'LR',0,'L',$fill);
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
