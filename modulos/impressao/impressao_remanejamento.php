<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
  header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");                          // HTTP/1.0

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: impressao_remanejamento.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de impressao de remanejamento
  //////////////////////////////////////////////////////////////////


  $header = array('C�digo','Material','Fabricante','Lote','Validade','Qtde Solicitada', 'Qtde Atendida');
  $w = array(20,90,60,30,25,25,25);

  function cabecalho($link,$pdf, $header, $w)
  {
    //global $pdf, $header, $w;
     

    $pdf->AddPage();
    $pdf->Ln();

    $pdf->SetFont('Arial','',9);
    $pdf->Cell(35,5,"N�mero da Solicita��o: ",0,0,"L");
    $pdf->Cell(40,5, $_GET[codigo],0,0,"L");
    $pdf->Cell(35,5,"Unidade Solicitante: ",0,0,"L");
    $pdf->Cell(40,5, $_GET[unidade_solicitante],0,0,"L");
    $pdf->Cell(35,5,"Unidade Solicitada: ",0,0,"L");
    $sql="select * from unidade where id_unidade='$_GET[unidade_solicitada]' and status_2='A'";
    $res=mysqli_query($link, $sql);
    erro_sql("Select Unidade Solicitada", $link, "");
    if(mysqli_num_rows($res)>0){
      $unidade=mysqli_fetch_object($res);
    }
    $pdf->Cell(40,5, $unidade->nome,0,1,"L");

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

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }
	
	
	if($_GET[aplicacao] <> ''){
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

	  //if(isset($_GET[aplicacao])){
      //$aplicacao = $_GET[aplicacao];//$_SESSION[aplicacao]; //=$_GET[aplicacao];
	  //echo "app".$aplicacao."<br>";
    //}


    if($aplicacao=="" || $_GET[codigo]=="" || $_GET[unidade_solicitante]=="" || $_GET[unidade_solicitada]==""){
      
	  /*echo('aplica��o'.$aplicacao.'<br>');
	  echo('codigo'.$_GET[codigo].'<br>');
	  echo('uni solicitante'.$_GET[unidade_solicitante].'<br>');
	  echo('unidade solicitada'.$_GET[unidade_solicitada].'<br>');*/
	  
	  //header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial.php");
    }
    else{

      require "../../fpdf152/Class.Pdf.inc.php";

      DEFINE("FPDF_FONTPATH","font/");
      $pdf = new PDF('L','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)

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
      $pos = strrpos($executavel, "/");
      if($pos === false)
      {
        $aplic = $executavel;
      }
      else
      {
        $aplic = substr($executavel, $pos+1);
      }
      $pdf->SetNomeAplic($aplic);
      $pdf->Open();
      cabecalho($db,$pdf, $header, $w);


      $fill = 0;
      $cont_linhas = 0;
      $lista_materiais=$_SESSION["ITENS"];
    
      $index=0;
      $vazio="";
      if(count($lista_materiais)>0){
	  //echo "<script> alert ('maior que 0');</script>";
        foreach($lista_materiais as $linha){
          foreach($linha as $coluna){
            if($index==0){
		
              $sql="select * from material where id_material='$coluna' and status_2='A'";
              $res=mysqli_query($db, $sql);
              erro_sql("Select Material", $db, "");
              $mat_info=mysqli_fetch_object($res);
              $sql="select * from item_solicita_remanej ";
              $sql.="where id_solicita_remanej='$_GET[codigo]' and ";
              $sql.="material_id_material='$mat_info->id_material'";
              $res=mysqli_query($db, $sql);
              erro_sql("Select Qtde Solicitada", $db, "");
              $qtde_solicitada=mysqli_fetch_object($res);
			  
			  
            }
            if($index==1){
			
              $sql="select * from fabricante where id_fabricante='$coluna'";
              $res=mysqli_query($db, $sql);
              erro_sql("Select Fabricante", $db, "");
              $fabricante_info=mysqli_fetch_object($res);
			  
			 
            }
            if($index==2){
				
              $lote_info=$coluna;
			  
            }
            if($index==3){
		
              $validade_info=$coluna;
			
            }
            if($index==4){
			
              $qtde_info=$coluna;
              if($qtde_info=="" || $qtde_info=="0"){
                $vazio="zerado";
              }
            }
            if($index==(QTDE_COLUNA-2)){
			
              if($vazio==""){
		
                $pdf->Cell($w[0],6,$mat_info->codigo_material,'LR',0,'L',$fill);
                $pdf->Cell($w[1],6,substr($mat_info->descricao, 0, 46),'LR',0,'L',$fill);
                $pdf->Cell($w[2],6,substr($fabricante_info->descricao, 0, 28),'LR',0,'L',$fill);
                $pdf->Cell($w[3],6,$lote_info,'LR',0,'L',$fill);
                $pdf->Cell($w[4],6,$validade_info,'LR',0,'C',$fill);
                $pdf->Cell($w[5],6,(int)$qtde_solicitada->qtde_solicita,'LR',0,'R',$fill);
                $pdf->Cell($w[5],6,(int)$qtde_info,'LR',0,'R',$fill);
                $pdf->ln();
              }
              $vazio="";
              $index=0;
              $fill=!$fill;
              $cont_linhas = $cont_linhas + 1;
              if ($cont_linhas == 21)
              {
			  	
                $pdf->Cell(array_sum($w),0,'','T');
                cabecalho($db);
                $cont_linhas = 0;
              }
            }
            else{
              $index++;
            }
          }
        }
      }
      else{
	    	//echo "<script> alert (ITENS);</script>";
        $pdf->SetFont('Arial','B',12);
		
        $pdf->Cell(0,5,"N�o Foi Poss�vel Gerar o Relat�rio!",0,1,"L");
      }

      $pdf->Cell(array_sum($w),0,'','T');
      $pdf->Output();
      $pdf->Close();
    }
    $_SESSION["ITENS"]=null;
  }
?>
