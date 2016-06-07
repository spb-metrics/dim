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
  //  Arquivo..: impressao_restoque.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de impressao de reversao estoque
  //////////////////////////////////////////////////////////////////

  $configuracao="../../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  session_start();

  $header=array('C�digo','Material','Fabricante','Lote','Validade','Quantidade');
  $w=array(30,90,60,35,30,30);

  function cabecalho($link){
    global $pdf, $header, $w;

    $pdf->AddPage();
    $pdf->Ln();

    $sql="select t.operacao, t.descricao, mov.data_movto, mov.id_movto_estornado ";
    $sql.="from movto_geral as mov, tipo_movto as t ";
    $sql.="where mov.tipo_movto_id_tipo_movto=t.id_tipo_movto and mov.id_movto_geral='$_GET[chave]'";
    $res=mysqli_query($link, $sql);
    erro_sql("Select Movimento", $link, "");
    if(mysqli_num_rows($res)>0){
      $movimento=mysqli_fetch_object($res);
    }
    $sql="select t.operacao, t.descricao, mov.id_movto_geral ";
    $sql.="from movto_geral as mov, tipo_movto as t ";
    $sql.="where mov.tipo_movto_id_tipo_movto=t.id_tipo_movto and mov.id_movto_geral='$movimento->id_movto_estornado'";
    $res=mysqli_query($link, $sql);
    erro_sql("Select Movimento Estornado", $link, "");
    if(mysqli_num_rows($res)>0){
      $movimento_estornado=mysqli_fetch_object($res);
    }
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(35,5,"N�mero do Documento: ",0,0,"L");
    $pdf->Cell(40,5, $_GET[chave],0,0,"L");
    $pdf->Cell(10,5,"Data: ",0,0,"L");
    $data_movto=$movimento->data_movto;
    $pos1=strpos($data_movto, "-");
    $pos2=strrpos($data_movto, "-");
    $data_info=substr($data_movto, $pos2+1, 2) . "/" . substr($data_movto, $pos1+1, 2) . "/" . substr($data_movto, 0, 4) . substr($data_movto, $pos2+3, strlen($data_movto));
    $pdf->Cell(10,5, $data_info,0,1,"L");
    $pdf->Cell(35,5,"Tipo do Movimento: ",0,0,"L");
    $pdf->Cell(25,5, $movimento->operacao,0,0,"L");
    $pdf->Cell(30,5, $movimento->descricao,0,1,"L");
    $pdf->Ln();
    $pdf->Cell(35,5,"Documento Revertido: ",0,1,"L");
    $pdf->Cell(35,5,"N�mero: ",0,0,"L");
    $pdf->Cell(25,5, $movimento_estornado->id_movto_geral,0,1,"L");
    $pdf->Cell(35,5,"Tipo: ",0,0,"L");
    $pdf->Cell(25,5, $movimento_estornado->operacao,0,0,"L");
    $pdf->Cell(30,5, $movimento_estornado->descricao,0,1,"L");

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

  if($_GET[chave]==""){
    header("Location: ". URL."/modulos/mestoque/mestoque_inclusao.php");
    exit();
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
    cabecalho($db);

    $fill=0;
    $cont_linhas=0;
    $sql="select i.validade, m.codigo_material, i.lote, i.qtde, m.descricao as mdescricao, f.descricao as fdescricao ";
    $sql.="from movto_geral as mov, itens_movto_geral as i, material as m, fabricante as f, tipo_movto as t ";
    $sql.="where t.id_tipo_movto=mov.tipo_movto_id_tipo_movto and i.fabricante_id_fabricante=f.id_fabricante and i.material_id_material=m.id_material ";
    $sql.="and mov.id_movto_geral=i.movto_geral_id_movto_geral and mov.id_movto_geral='$_GET[chave]'";
    $res=mysqli_query($db, $sql);
    erro_sql("Select �tens Relat�rio", $db, "");
    while($resultado=mysqli_fetch_array($res)){
      $pdf->Cell($w[0],6,$resultado[codigo_material],'LR',0,'L',$fill);
      $pdf->Cell($w[1],6,substr($resultado[mdescricao], 0, 46),'LR',0,'L',$fill);
      $pdf->Cell($w[2],6,substr($resultado[fdescricao], 0, 28),'LR',0,'L',$fill);
      $pdf->Cell($w[3],6,$resultado[lote],'LR',0,'L',$fill);
      $pos1=strpos($resultado[validade], "-");
      $pos2=strrpos($resultado[validade], "-");
      $validade=substr($resultado[validade], $pos2+1, strlen($resultado[validade])) . "/" . substr($resultado[validade], $pos1+1, 2) . "/" . substr($resultado[validade], 0, 4);
      $pdf->Cell($w[4],6,$validade,'LR',0,'C',$fill);
      $pdf->Cell($w[5],6,(int)$resultado[qtde],'LR',0,'R',$fill);
      $pdf->ln();
      $fill=!$fill;
      $cont_linhas=$cont_linhas + 1;
      if ($cont_linhas==21){
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
