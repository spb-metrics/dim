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

  $header = array('Inscrição','Nome','Conselho Profissional','Qtde Prescrita','Qtde Dispensada','Unidade');
  $w = array(30,92,40,30,35,50);

  function cabecalho_tabela($codigo_med_at, $nome_med_at)
  {
    global $pdf, $header, $w;

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
      $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
    $pdf->Ln(5.4);

    //Color and font restoration
    /*$pdf->SetFillColor(224,235,255);
    $pdf->SetTextColor(0);*/
    $pdf->SetFont('');
  }

  function cabecalho()
  {
    global $pdf, $data_in, $data_fn, $nome_und, $nome_pre, $nome_med;

    $pdf->AddPage();
    $pdf->Ln();

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(22,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(35,5,"                       Período:",0,0,"L");
    $pdf->Cell(0,5,$data_in."  à  ".$data_fn,0,1,"L");

    $pdf->Cell(35,5,"                      Unidade:",0,0,"L");
    if ($nome_und == '')
      $pdf->Cell(0,5,"Todas as Unidades",0,1,"L");
    else
      $pdf->Cell(0,5,$nome_und,0,1,"L");

    $pdf->Cell(35,5,"              Medicamento:",0,0,"L");
    if ($nome_med == '')
      $pdf->Cell(0,5,"Todos os Medicamentos",0,1,"L");
    else
      $pdf->Cell(0,5,$nome_med,0,1,"L");

    $pdf->SetX(-10);
    $pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
  }

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    $data_in = $_POST['data_in'];
    $data_fn = $_POST['data_fn'];
    $unidade = $_POST['unidade'];
    $nome_und = $_POST['unidade01'];
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

    $sql="select mat.codigo_material,
                 mat.descricao as matdescricao,
                 prof.inscricao,
                 prof.nome as profnome,
                 cons.descricao as consdescricao,
                 sum(item.qtde_prescrita) as qtde_prescrita,
                 sum(item.qtde_disp_anterior+item.qtde_disp_mes) as qtde_dispensada,
                 unid.nome as unidnome,
                 rec.data_emissao
          from receita as rec,
               itens_receita as item,
               profissional as prof,
               unidade as unid,
               material as mat,
               tipo_conselho as cons
          where rec.id_receita=item.receita_id_receita and
                rec.unidade_id_unidade=unid.id_unidade and
                rec.profissional_id_profissional=prof.id_profissional and
                item.material_id_material=mat.id_material and
                prof.tipo_conselho_id_tipo_conselho=cons.id_tipo_conselho and
                prof.status_2='A' and
                unid.status_2='A' and
                mat.status_2='A'";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(rec.data_emissao,1,10) between '$data_inicio' and '$data_fim'";

    /*echo $unidade;
    echo $nome_und;*/
    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      $sql = $sql." and unid.id_unidade in ($unidades)";
    }

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    $sql = $sql." group by mat.descricao, prof.nome, unid.nome order by mat.descricao,";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." prof.inscricao";
        break;
      case 1:
        $sql = $sql." profnome";
        break;
      case 2:
        $sql = $sql." consdescricao";
        break;
      case 3:
        $sql = $sql." qtde_prescrita";
        break;
      case 4:
        $sql = $sql." qtde_dispensada";
        break;
      case 5:
        $sql = $sql." unidnome";
        break;
    }
//echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $fill = 0;
      $cont_linhas = 0;
      $total_prescrito=0;
      $total_dispensado=0;
      while($linha = mysqli_fetch_array($sql_query))
      {
        $cod_atual = $linha['codigo_material'];
        $med_atual = $linha['matdescricao'];
        if($cont_linhas>=24){
          $pdf->Cell(array_sum($w),0,'','T');
          if($cod_atual!=$cod_anterior && $med_atual!=$med_anterior){
            $pdf->Ln();
            $pdf->Cell(30,5,"Total Prescrito:",0,0,"L");
            if($total_prescrito==0){
              $pdf->Cell(20,5,"0",0,0,"L");
            }
            else{
              $pdf->Cell(20,5,$total_prescrito,0,0,"L");
            }
            $pdf->Cell(30,5,"Total Dispensado:",0,0,"L");
            if($total_dispensado==0){
              $pdf->Cell(20,5,"0",0,0,"L");
            }
            else{
              $pdf->Cell(20,5,$total_dispensado,0,0,"L");
            }
            $total_prescrito=0;
            $total_dispensado=0;
          }
          cabecalho();
          cabecalho_tabela($cod_atual, $med_atual);
          $cont_linhas = 3;
          $cod_anterior=$cod_atual;
          $med_anterior=$med_atual;
        }
        if($cod_anterior == '' && $med_anterior == ''){
          $cod_anterior=$cod_atual;
          $med_anterior=$med_atual;
          $pdf->Cell(array_sum($w)-$w[5],0,'','T');
          cabecalho();
          cabecalho_tabela($cod_atual, $med_atual);
          $fill = 0;   $cont_linhas = $cont_linhas + 4;
        }
        if ($cod_atual <> $cod_anterior && $med_atual <> $med_anterior)
        {
          $pdf->Cell(array_sum($w),0,'','T');
          $pdf->Ln();
          $pdf->Cell(30,5,"Total Prescrito:",0,0,"L");
          if($total_prescrito==0){
            $pdf->Cell(20,5,"0",0,0,"L");
          }
          else{
            $pdf->Cell(20,5,$total_prescrito,0,0,"L");
          }
          $pdf->Cell(30,5,"Total Dispensado:",0,0,"L");
          if($total_dispensado==0){
            $pdf->Cell(20,5,"0",0,0,"L");
          }
          else{
            $pdf->Cell(20,5,$total_dispensado,0,0,"L");
          }
          $total_prescrito=0;
          $total_dispensado=0;
          $cod_anterior = $cod_atual;
          $med_anterior = $med_atual;
          $pdf->Ln();
          cabecalho_tabela($cod_atual, $med_atual);
          $fill = 0;   $cont_linhas = $cont_linhas + 3;
        }
        $pdf->Cell($w[0],5,$linha[inscricao],'LR',0,'R',$fill);
        $pdf->Cell($w[1],5,$linha[profnome]." ",'LR',0,'L',$fill);
        $pdf->Cell($w[2],5,$linha[consdescricao],'LR',0,'L',$fill);
        if((int)$linha[qtde_prescrita]==0){
          $pdf->Cell($w[3],5,"0",'LR',0,'R',$fill);
        }
        else{
          $pdf->Cell($w[3],5,(int)$linha[qtde_prescrita],'LR',0,'R',$fill);
        }
        if((int)$linha[qtde_dispensada]==0){
          $pdf->Cell($w[4],5,"0",'LR',0,'R',$fill);
        }
        else{
          $pdf->Cell($w[4],5,(int)$linha[qtde_dispensada],'LR',0,'R',$fill);
        }
        $pdf->Cell($w[5],5,$linha[unidnome],'LR',0,'L',$fill);
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 2;
        if($cod_anterior==$cod_atual){
          $total_prescrito+=(int)$linha[qtde_prescrita];
          $total_dispensado+=(int)$linha[qtde_dispensada];
        }
      }
      $pdf->Cell(array_sum($w),0,'','T');
      $pdf->Ln();
      $pdf->Cell(30,5,"Total Prescrito:",0,0,"L");
      if($total_prescrito==0){
        $pdf->Cell(20,5,"0",0,0,"L");
      }
      else{
        $pdf->Cell(20,5,$total_prescrito,0,0,"L");
      }
      $pdf->Cell(30,5,"Total Dispensado:",0,0,"L");
      if($total_dispensado==0){
        $pdf->Cell(20,5,"0",0,0,"L");
      }
      else{
        $pdf->Cell(20,5,$total_dispensado,0,0,"L");
      }
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
