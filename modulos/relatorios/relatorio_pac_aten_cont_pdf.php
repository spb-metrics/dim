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
  // | Arquivo ............: relatorio_pac_aten_cont_pdf.php                           |
  // | Autor ..............: Fabio Hitoshi Ide                                         |
  // +---------------------------------------------------------------------------------+
  // | Função .............: Relatório Pacientes Atencao Continuada (.pdf)             |
  // | Data de Criação ....: 23/01/2007 - 13:35                                        |
  // | Última Atualização .: 15/02/2007 - 18:30                                        |
  // | Versão .............: 1.0.0                                                     |
  // +---------------------------------------------------------------------------------+

  $header = array('Cartão SUS','Paciente','Prontuário','Mãe','Data Nasc.','Data Última Dispensação');
  $w = array(30,83,25,83,20,40);

  function cabecalho($link)
  {
    global $pdf, $data_in, $data_fn, $nome_und, $atencao, $paciente;

    $pdf->AddPage();
    $pdf->Ln();

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(22,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(38,5,"                   Unidade:",0,0,"L");
    if ($nome_und == '')
      $pdf->Cell(0,5,"Todas as Unidades",0,1,"L");
    else
      $pdf->Cell(0,5,$nome_und,0,1,"L");

    $pdf->Cell(38,5,"Atenção Continuada:",0,0,"L");
    if($atencao=="todos"){
      $pdf->Cell(0,5,"Todas as Atenções Continuadas",0,1,"L");
    }
    else{
      $sql="select * from atencao_continuada where id_atencao_continuada='$atencao'";
      $res=mysqli_query($link, $sql);
      erro_sql("Cabeçalho", $link, "");
      if(mysqli_num_rows($res)>0){
        $atencao_info=mysqli_fetch_object($res);
      }
      $pdf->Cell(0,5,$atencao_info->descricao,0,1,"L");
    }
    $pdf->Cell(38,5,"       Exibir Pacientes:",0,0,"L");
    if($paciente=="1"){
      $pdf->Cell(0,5,"Com Dispensação",0,1,"L");
    }
    if($paciente=="2"){
      $pdf->Cell(0,5,"Sem Dispensação",0,1,"L");
    }

    $pdf->SetX(-10);
    $pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
  }

  function cabecalho_tabela($nome_und_cad, $aten_cont)
  {
    global $pdf, $header, $w;

    $pdf->Ln(4);
    $pdf->SetFont('Arial','B');
    $pdf->Cell(20,5,"Unidade:",0,0,"L");
    $pdf->SetFont('Arial','');
    $pdf->Cell(140,5,$nome_und_cad,0,0,"L");
    $pdf->SetFont('Arial','B');
    $pdf->Cell(36,5,"Atenção Continuada:",0,0,"L");
    $pdf->SetFont('Arial','');
    $pdf->Cell(50,5,$aten_cont,0,1,"L");
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
    $pdf->Ln();

    //Color and font restoration
    /*$pdf->SetFillColor(224,235,255);
    $pdf->SetTextColor(0);*/
    $pdf->SetFont('');
  }

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    $unidade = $_POST['unidade'];
    $nome_und = $_POST['unidade01'];
    $atencao=$_POST[atencao];
    $paciente=$_POST[paciente];
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

    $sql="select uni.nome as uninome,
                 atencont.descricao,
                 pac.nome as pacnome,
                 prontuario.num_prontuario as prontuario,
                 rec.id_receita,
                 max(rec.data_ult_disp) as data_ult_disp,
                 cart.cartao_sus,
                 pac.nome_mae,
                 pac.data_nasc
         from unidade as uni left join paciente as pac on uni.id_unidade=pac.unidade_referida
              left join cartao_sus as cart on cart.paciente_id_paciente=pac.id_paciente
              left join atencao_continuada_paciente as atencontpac on atencontpac.id_paciente=pac.id_paciente
              left join atencao_continuada as atencont on atencont.id_atencao_continuada=atencontpac.id_atencao_continuada
              left join receita as rec on pac.id_paciente=rec.paciente_id_paciente
              left join prontuario on prontuario.paciente_id_paciente =  pac.id_paciente
              where uni.status_2='A' and
              ((pac.status_2='A' and atencont.descricao!='') or pac.status_2 is NULL)";
      switch($atencao){
        case "todos":
          break;
        default:
          $sql.=" and atencont.id_atencao_continuada='$atencao'";
         break;
      }
      if (($unidade <> '') and ($nome_und <> ''))
      {
        $unidades = $unidade;
        $sql = $sql." and pac.unidade_referida in ($unidades)";
      }
      else{
        $sql_unidades="select * from unidade where flg_nivel_superior!='1' and status_2='A'";
        $res_unidades=mysqli_query($db, $sql_unidades);
        erro_sql("Unidades", $db, "");
        $info_unidades="";
        while($unidades_info=mysqli_fetch_array($res_unidades)){
          $info_unidades.=$unidades_info[id_unidade] . ",";
        }
        $info_unidades=substr($info_unidades, 0, (strlen($info_unidades)-1));
        $sql.=" and pac.unidade_referida in ($info_unidades)";
      }

      $sql = $sql." group by uni.nome, atencont.descricao, pac.nome order by uni.nome, atencont.descricao,";

      switch ($ordem)
      {
        case 0:
          $sql = $sql." cart.cartao_sus";
          break;
        case 1:
          $sql = $sql." pac.nome";
          break;
        case 2:
          $sql = $sql." data_ult_disp desc";
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
        $info="";
        $pesquisa="";
        while($linha = mysqli_fetch_array($sql_query))
        {
          if($paciente=="1" && $linha[data_ult_disp]!=""){
            $info="achou";
            $pesquisa="sim";
          }
          if($paciente=="2" && $linha[data_ult_disp]==""){
            $info="achou";
            $pesquisa="sim";
          }
          if($info!=""){
            $und_cad_atual = $linha['uninome'];
            $aten_cont_atual = $linha['descricao'];

            if ($cont_linhas >= 23)
            {
              cabecalho($db);
              cabecalho_tabela($und_cad_atual, $aten_cont_atual);
              $cont_linhas = 3;
              $und_cad_anterior = $und_cad_atual;
              $aten_cont_anterior = $aten_cont_atual;
            }

            if (($und_cad_anterior == '') and ($aten_cont_anterior == ''))
            {
              $und_cad_anterior = $und_cad_atual;
              $aten_cont_anterior = $aten_cont_atual;
              $pdf->Cell(array_sum($w)-$w[5],0,'','T');
              cabecalho($db);
              cabecalho_tabela($und_cad_atual, $aten_cont_atual);
              $fill = 0;   $cont_linhas = $cont_linhas + 4;
            }

            if (($und_cad_atual <> $und_cad_anterior) or ($aten_cont_atual <> $aten_cont_anterior))
            {
              $und_cad_anterior = $und_cad_atual;
              $aten_cont_anterior = $aten_cont_atual;
              cabecalho_tabela($und_cad_atual, $aten_cont_atual);
              $fill = 0;   $cont_linhas = $cont_linhas + 2;
            }
            if($linha[descricao]!=""){
              if($linha[id_receita]!=""){
                if($paciente=="1"){
                  $dt_ult_disp = ((substr($linha[data_ult_disp],8,2))."/".(substr($linha[data_ult_disp],5,2))."/".(substr($linha[data_ult_disp],0,4)));
                }
                else{
                  $dt_ult_disp = "0";
                }
              }
              else{
                $dt_ult_disp="Somente Cadast. Categ.";
              }
              if($linha[cartao_sus]!="0"){
                $pdf->Cell($w[0],5,$linha['cartao_sus'],'LTRB',0,'L',$fill);
              }
              else{
                if($linha[cartao_sus_prov]!="0"){
                  $pdf->Cell($w[0],5,$linha['cartao_sus_prov'],'LTRB',0,'L',$fill);
                }
                else{
                  $pdf->Cell($w[0],5,"0",'LTRB',0,'L',$fill);
                }
              }
              $dt_nasc = ((substr($linha[data_nasc],8,2))."/".(substr($linha[data_nasc],5,2))."/".(substr($linha[data_nasc],0,4)));
              $pdf->Cell($w[1],5,$linha['pacnome'],'LTRB',0,'L',$fill);
              //nova coluna do relatorio NÚMERO DO PRONTUÁRIO
			 
				$pdf->Cell($w[2],5,$linha['prontuario'],'LTRB',0,'L',$fill);
			 			
			  //FIM NOVA COLUNA
              $pdf->Cell($w[3],5,$linha['nome_mae'],'LTRB',0,'L',$fill);
              $pdf->Cell($w[4],5,$dt_nasc,'LTRB',0,'C',$fill);
              $pdf->Cell($w[5],5,$dt_ult_disp,'LTRB',0,'C',$fill);
              $pdf->Ln();
              $fill=!$fill;
              $cont_linhas = $cont_linhas + 2;
            }
            else{
              $pdf->Cell(0,5,"Não Existe Paciente Associado a Categoria de Atenção Continuada!",0,1,"L");
              $fill=!$fill;
              $cont_linhas = $cont_linhas + 2;
            }
          }
          $info="";
        }
        if($pesquisa==""){
          cabecalho($db);
          cabecalho_tabela("", "");
          $pdf->SetFont('Arial','B',12);
          $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
        }
      }
      else{
        cabecalho($db);
        cabecalho_tabela("", "");
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
      }
    $pdf->Output();
    $pdf->Close();
  }
?>
