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
  // | Arquivo ............: relatorio_pre_med_pdf.php                                 |
  // | Autor ..............: Fábio Hitoshi Ide                                         |
  // +---------------------------------------------------------------------------------+
  // | Função .............: Relatório de Prescritores por Medicamento (.pdf)          |
  // | Data de Criação ....: 17/01/2007 - 11:15                                        |
  // | Última Atualização .: 16/03/2007 - 10:50                                        |
  // | Versão .............: 1.0.0                                                     |
  // +---------------------------------------------------------------------------------+
  require("Classe_Extrato.php");
  $header=array('Data','Histórico','Lote','Qtde','Saldo','','Movto','','Estoque','Login');
  $w=array(20,110,25,15,15,15,15,15,15,32);
  $header2=array('','','','', 'Anterior','Entrada','Saída','Perda','','');
  $w2=array(20,110,25,15,15,15,15,15,15,32);

  
  function cabecalho_tabela($codigo_med_at, $nome_med_at){
    global $pdf, $header, $w, $header2, $w2;

    $pdf->Ln(4);
    $pdf->SetFont('','B');
    $pdf->Cell(30,5,"Medicamento:",0,0,"L");
    $pdf->SetFont('','');
    $pdf->Cell(20,5,$codigo_med_at,0,0,"L");
    $pdf->Cell(100,5,$nome_med_at,0,0,"L");
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
     if(($i ==6)||($i ==7))
       $pdf->Cell($w[$i],5,$header[$i],'T',0,'C',1);
     else
      $pdf->Cell($w[$i],5,$header[$i],'LTR',0,'C',1);
    $pdf->Ln();
    for($i = 0; $i < count($header2); $i++)
     if(($i ==5)||($i ==6)||($i ==7))
      $pdf->Cell($w2[$i],5,$header2[$i],'LTB',0,'C',1);
     else
      $pdf->Cell($w2[$i],5,$header2[$i],'LRB',0,'C',1);
    $pdf->Ln(5.4);
    $pdf->SetFont('');
  }

  function cabecalho(){
    global $pdf, $data_in, $data_fn, $nome_und, $nome_pre, $nome_med;

    $pdf->AddPage();
  }

  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    $data_in=$_POST['data_in'];
    $data_fn=$_POST['data_fn'];
    $unidade=$_POST['unidade'];
    $nome_und=$_POST['unidade01'];
    $medicamento=$_POST['medicamento'];
    
    $sql="select codigo_material
          from material
          where id_material=$medicamento";
    $result=mysqli_query($db, $sql);
    erro_sql("select codigo material", $db, "");
    if(mysqli_num_rows($result)>0){
      $cod_mat=mysqli_fetch_object($result);
      $codigo_material=$cod_mat->codigo_material;
    }
    $nome_med=$_POST['medicamento01'];
    $aplicacao=$_POST['aplicacao'];
    $und_user=$_POST['nome_und'];

/////// buscar unidades q pertencem ao distrito

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
}

//====
    //require "../../fpdf152/Class.Pdf.inc.php";
    $max_linhas = 36;
    DEFINE("FPDF_FONTPATH","font/");

    $pdf=new PDF('L','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)

    $sql="select apl.executavel, ime.descricao
          from aplicacao apl, item_menu ime
          where apl.id_aplicacao = $aplicacao
                and ime.aplicacao_id_aplicacao = $aplicacao";
    $sql_query=mysqli_query($db, $sql);
    erro_sql("Select Aplicação", $db, "");
    echo mysqli_error($db);
    if(mysqli_num_rows($sql_query)>0){
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

    $sql="select movl.movto_geral_id_movto_geral,
                 imov.lote,
                 imov.qtde,
                 movl.saldo_anterior,
                 movl.qtde_entrada,
                 movl.qtde_saida,
                 movl.qtde_perda,
                 movl.saldo_atual,
                 DATE_FORMAT(movl.data_movto,'%d/%m/%Y') as data_movto,
                 movl.historico,
                 usr.login
          from movto_livro as movl, movto_geral as movt,
               itens_movto_geral as imov, usuario as usr
          where movt.id_movto_geral= movl.movto_geral_id_movto_geral and
                movl.movto_geral_id_movto_geral=imov.movto_geral_id_movto_geral and
                movt.usuario_id_usuario=usr.id_usuario and
                movl.material_id_material=imov.material_id_material and
                movl.material_id_material=$medicamento";

    if($data_in!="" && $data_fn!=""){
      $data_inicio=((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
      $data_fim=((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
      $sql=$sql." and SUBSTRING(movl.data_movto,1,10) between '$data_inicio' and '$data_fim'";
    }
    
    if ($unidade <> '')
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and movl.unidade_id_unidade in ($unidades)";
    }
    $sql = $sql." order by movl.movto_geral_id_movto_geral asc, lote desc";

//echo $sql;
    $sql_query=mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    
    if(mysqli_num_rows($sql_query)>0){
      $fill=0;
      $cont_linhas=0;
      cabecalho();
      cabecalho_tabela($codigo_material, $nome_med);
      $movto_geral_aux="";

      while($linha=mysqli_fetch_array($sql_query)){
            $cont_linhas++;
            $data_movto = $linha['data_movto'];
            $historico = $linha['historico'];
            $lote = $linha['lote'];
            $qtde = intval($linha['qtde']);
            $movto_geral=$linha["movto_geral_id_movto_geral"];
            $saldo_anterior=$linha["saldo_anterior"];
            $qtde_entrada=$linha["qtde_entrada"];
            $qtde_saida=$linha["qtde_saida"];
            $qtde_perda=$linha["qtde_perda"];
            $saldo_atual=$linha["saldo_atual"];
            $login=$linha["login"];

            if($movto_geral_aux!=$movto_geral){
              if($cont_linhas>=0){
                $pdf->SetWidths($w);
                $pdf->SetAligns(array('C','L','R','R','R','R','R','R','R'));
                srand(microtime()*1000000);
                $pdf->Row2(array($data_movto, $historico,
                //(intval($lote==0)?"":intval($lote))." ",
                (intval($lote==0)?"":$lote)." ",
                (intval($qtde==0)?"":intval($qtde))." ",
                (intval($saldo_anterior==0)?"0":intval($saldo_anterior))." ",
                (intval($qtde_entrada==0)?"0":intval($qtde_entrada))." ",
                (intval($qtde_saida==0)?"0":intval($qtde_saida))." ",
                (intval($qtde_perda==0)?"0":intval($qtde_perda))." ",
                (intval($saldo_atual==0)?"0":intval($saldo_atual))." ", $login));
              }
            
              $tamanho = 1;
              $tamanho = max($tamanho,$pdf->NbLines($w[1],$historico));

              if (($cont_linhas + $tamanho) > $max_linhas)
              {
                $cont_linhas = 1;
                $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
                cabecalho();
                cabecalho_tabela($codigo_material, $nome_med);
                $pdf->SetFont('Arial','',9);
              }

              $cont_linhas = $cont_linhas + $tamanho;
          }
          else{
            if($cont_linhas>=0){
                $pdf->SetWidths($w);
                $pdf->SetAligns(array('C','L','R','R','R','R','R','R','R'));
                srand(microtime()*1000000);
                $pdf->Row2(array(" ", " ",
                //(intval($lote==0)?"0":intval($lote))." ",
                (intval($lote==0)?"":$lote)." ",
                (intval($qtde==0)?"":intval($qtde))." ", " ", " ", " ", " ", " ", " "));
            }
          }
         $movto_geral_aux=$linha["movto_geral_id_movto_geral"];
      }
      $pdf->Cell(array_sum($w),0,'','T');
      $pdf->Ln();
    }
    else{
      cabecalho("", $nome_med);
      cabecalho_tabela("", $nome_med);
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
    }

    $pdf->Output();
    $pdf->Close();
  }
?>
