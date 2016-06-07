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
// | Arquivo ............: gerar_livro_pdf.php                                       |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Livro de Registro (.pdf)                                  |
// | Data de Criação ....: 24/01/2007 - 18:45                                        |
// | Última Atualização .: 23/03/2007 - 16:15                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

require("Classe_Livro.php");

function cabecalho_pagina($data)
{
  global $pdf;

  $pdf->AddPage();
  $pdf->Image("../../imagens/brasao_peqno.jpg",5,3,25,25); // importa uma imagem
  $pdf->SetFont('Arial','',9);
  $pdf->Cell(0,5,"pág. ".$pdf->PageNo()."                 ",'',1,"R"); //  Imprime página X/Total de Páginas - Paisagem
  $pdf->Cell(0,5,"data: ".$data,'',0,"R");
}

$w = array(18,98,19,19,19,19,22,63);

function cabecalho_tabela($link)
{
  global $pdf, $livro, $unidade, $mat_anterior, $w;
  
  $header01 = array('DATA','HISTÓRICO','','MOVIMENTO  ','','ESTOQUE','ASS. RESP.','OBS.');
  $header02 = array('','','ENTRADA','SAÍDA','PERDA','','TÉCNICO','');
  $linha01 = array('LTR','LTR','LTB','TB','TRB','LTR','LTR','LTR');
  $linha02 = array('LRB','LRB','LTRB','LTRB','LTRB','LRB','LRB','LRB');

  $sql = "select descricao
          from livro
          where id_livro = $livro";
  //echo $sql;
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Cabeçalho Tabela", $link, "");
  echo mysqli_error($link);
  if (mysqli_num_rows($sql_query) > 0)
  {
    $linha = mysqli_fetch_array($sql_query);
    $desc_livro = $linha['descricao'];
  }
  
  $sql = "select und.nome, und.rua, und.numero, und.complemento, und.bairro, und.municipio,
          und.uf, prm.cnpj_empresa
          from unidade und, parametro prm
          where id_unidade = $unidade
                and status_2 = 'A'";
  //echo $sql;
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Select Unidade", $link, "");
  echo mysqli_error($link);
  if (mysqli_num_rows($sql_query) > 0)
  {
    $linha = mysqli_fetch_array($sql_query);
    $nome_und = $linha['nome'];
  }

  $pdf->Ln();
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,5,"LIVRO DE REGISTRO ESPECÍFICO",'',1,"C");
  $pdf->Cell(0,5,"LIVRO ".$desc_livro,'',1,"C");
  $pdf->Cell(0,5,$nome_und,'',1,"C");
  $pdf->Ln(4);

  //Colors, line width and bold font
  $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(0);  // cor do texto
  $pdf->SetLineWidth(.3);
  $pdf->SetFont('Arial','B',10);
  //Header
  for($i = 0; $i < count($header01); $i++)
    $pdf->Cell($w[$i],5,$header01[$i],$linha01[$i],0,'C',1);
  $pdf->Ln();
  for($i = 0; $i < count($header02); $i++)
    $pdf->Cell($w[$i],5,$header02[$i],$linha02[$i],0,'C',1);
  $pdf->Ln();
  $pdf->SetFont('Arial','',9);
}

function cabecalho_troca($mat_at, $desc_mat_at)
{
  global $pdf;

  $pdf->Ln(5.4);
  $pdf->SetFont('Arial','B',10);
  $pdf->Cell(22,5,"Produto: ",'B',0,'L',1);
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,5,$mat_at." - ".$desc_mat_at,'B',0,'L',1);
  $pdf->Ln();
}

function termo($nome_termo, $data, $link)
{
  global $pdf, $livro, $unidade, $nr_livro;
  
  $arr = array("mes" => array('01' => "Janeiro",
                              '02' => "Fevereiro",
                              '03' => "Março",
                              '04' => "Abril",
                              '05' => "Maio",
                              '06' => "Junho",
                              '07' => "Julho",
                              '08' => "Agosto",
                              '09' => "Setembro",
                              '10' => "Outubro",
                              '11' => "Novembro",
                              '12' => "Dezembro" ));

  $sql = "select descricao
          from livro
          where id_livro = $livro";
  //echo $sql;
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Select Livro", $link, "");
  echo mysqli_error($link);
  if (mysqli_num_rows($sql_query) > 0)
  {
    $linha = mysqli_fetch_array($sql_query);
    $desc_livro = $linha['descricao'];
  }
  
  $sql = "select und.nome, und.rua, und.numero, und.complemento, und.bairro, und.municipio,
          und.uf, prm.cnpj_empresa
          from unidade und, parametro prm
          where id_unidade = $unidade
                and status_2 = 'A'";
  //echo $sql;
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Select Unidade - Termo", $link, "");
  echo mysqli_error($link);
  if (mysqli_num_rows($sql_query) > 0)
  {
    $linha = mysqli_fetch_array($sql_query);
    $nome_und = $linha['nome'];

    if($linha['rua'] <> '')
      $endereco = $linha['rua'];

    if($linha['numero'] <> '')
    {
       if($endereco <> '')
         $endereco = $endereco.", ".$linha['numero'];
       else
         $endereco = $linha['numero'];
    }
    
    if($linha['complemento'] <> '')
    {
       if($endereco <> '')
         $endereco = $endereco.", ".$linha['complemento'];
       else
         $endereco = $linha['complemento'];
    }
    
    if($linha['bairro'] <> '')
    {
       if($endereco <> '')
         $endereco = $endereco.", ".$linha['bairro'];
       else
         $endereco = $linha['bairro'];
    }
    $cidade = $linha['municipio'];
    $uf = $linha['uf'];
    $cnpj = $linha['cnpj_empresa'];
  }
  cabecalho_pagina($data);
  $pdf->Ln();
  $pdf->SetFont('Arial','',12);
  $pdf->Cell(0,6,"LIVRO ".$desc_livro,'',1,"C");
  $pdf->Cell(0,6,"LIVRO NÚMERO: ".$nr_livro,'',1,"C");
  $pdf->Ln(30);
  $pdf->SetFont('Arial','',14);
  $pdf->Cell(0,6,$nome_termo,'',1,"C");
  $pdf->SetFont('Arial','',12);
  $pdf->Ln(30);
  $txt = "       Este livro contém {nb} folhas numeradas eletronicamente, do número 1 ao número {nb} e ";
  $txt .= "servirá para registro de medicamentos da Portaria nº 344/98 - ".$desc_livro;
  $txt .= ", do Centro de Saúde ".$nome_und.".";
  $pdf->MultiCell(277,5,$txt);
  $pdf->Ln(10);
  $pdf->Cell(22,6,"Endereço: ",'',0,"L");    $pdf->Cell(0,6,$endereco,'',1,"L");
  $pdf->Cell(22,6,"Cidade: ",'',0,"L");      $pdf->Cell(180,6,$cidade,'',0,"L");
  $pdf->Cell(18,6,"Estado: ",'',0,"L");      $pdf->Cell(0,6,$uf,'',1,"L");
  $pdf->Cell(22,6,"CNPJ: ",'',0,"L");        $pdf->Cell(0,6,$cnpj,'',1,"L");
  $pdf->Ln();
  $pdf->Cell(40,6,"Farmacêutico(a): ",'',0,"L");
  $pdf->Cell(0,6,"__________________________________________",'',1,"L");
  $pdf->Cell(40,6,"CRF: ",'',0,"L");
  $pdf->Cell(0,6,"__________________________________________",'',1,"L");
  $pdf->Cell(40,6,"Assinatura: ",'',0,"L");
  $pdf->Cell(0,6,"__________________________________________",'',1,"L");
  $pdf->Ln(20);
  $dia = $data[0].$data[1];
  $mes = $data[3].$data[4];
  $ano = $data[6].$data[7].$data[8].$data[9];
  $pdf->Cell(0,6,$cidade.", ".$dia." de ".$arr["mes"][$mes]." de ".$ano.".",'',0,"L");
}

if (file_exists("../../config/config.inc.php"))
{
  require "../../config/config.inc.php";

  if ($_GET['flag'] == 1)
  {
    $data_in = $_GET['data_in'];
    $data_in01 = $_GET['data_in01'];
    $data_fn = $_GET['data_fn'];
    $nome_und = $_GET['unidade'];
    $unidade = $_GET['und_sup'];
    $livro = $_GET['livro'];
    $nr_livro = $_GET['nr_livro'];
  }
  else
  {
    $data_in = $_POST['data_in'];
    $data_in01 = $_POST['data_in01'];
    $data_fn = $_POST['data_fn'];
    $nome_und = $_GET['unidade'];
    $unidade = $_POST['und_sup'];
    $livro = $_POST['livro'];
    $nr_livro = $_POST['nr_livro']+1;
  }
  
  $campos_obr = "";

  if ($data_in == '')
  {
    if ($data_in01 == '')
      $campos_obr = "\\n - Data Início";
    else $data_in = $data_in01;
  }
  else if ($data_in01 == '')
  {
    if ($data_in == '')
      $campos_obr = "\\n - Data Início";
  }

  if ($data_fn == '')
  {
    $campos_obr = $campos_obr."\\n - Data Fim";
  }

  if ($livro == '')
  {
    $campos_obr = $campos_obr."\\n - Livro";
  }

  if ($campos_obr <> '')
  {
    $msg_erro = "Favor preencher os campos obrigatórios.";//.$campos_obr;
  ?>
    <script>
      alert('<?=$msg_erro?>');
      window.close();
    </script>
  <?
  }
  else // Geração do PDF
  {
    $max_linhas = 35;
    DEFINE("FPDF_FONTPATH","font/");
    $pdf = new PDF('L','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)
    $pdf->AliasNbPages();  // Define o número total de paginas para a macro {nb}
    //$pdf->SetData($data_fn);
    $pdf->Open();
    termo("TERMO DE ABERTURA", $data_in, $db);
    cabecalho_pagina($data_fn);
    cabecalho_tabela($db);

    $sql01 = "select distinct mat.id_material, mat.codigo_material, mat.descricao as medicamento, mat.lista_especial_id_lista_especial
            from material mat
                 inner join lista_especial esp on mat.lista_especial_id_lista_especial = esp.id_lista_especial
                 inner join livro liv on esp.livro_id_livro = liv.id_livro
                 inner join estoque est on mat.id_material = est.material_id_material
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and esp.status_2 = 'A'";

    if ($unidade <> '')
    {
      $sql01 = $sql01." and est.unidade_id_unidade = $unidade";
    }

    if ($livro <> '')
      $sql01 = $sql01." and liv.id_livro = $livro";

    $sql01 = $sql01." order by mat.descricao";

    //echo $sql01;
    $sql_query01 = mysqli_query($db, $sql01);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query01) > 0)
    {
      $fill = 0;
      $primeira_linha = true;
      $cont_linhas = 8;
      while($linha01 = mysqli_fetch_array($sql_query01))
      {
        $id_material = $linha01['id_material'];
        $id_lista_especial = $linha01['lista_especial_id_lista_especial'];
    
        $sql = "select mov.data_movto, mov.historico, mov.saldo_anterior, mov.qtde_entrada,
                       mov.qtde_saida, mov.qtde_perda, mov.saldo_atual, mvg.motivo
                from movto_livro mov, lista_especial esp, livro liv, movto_geral mvg
                where esp.status_2 = 'A'
                      and esp.livro_id_livro = liv.id_livro
                      and mov.movto_geral_id_movto_geral = mvg.id_movto_geral";
        if ($unidade <> '')
        {
          $sql = $sql." and mov.unidade_id_unidade = $unidade";
        }

        if ($livro <> '')
          $sql = $sql." and liv.id_livro = $livro";

        $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
        $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
        $sql = $sql." and SUBSTRING(mov.data_movto,1,10) between '$data_inicio' and '$data_fim'";
    
        if ($id_material <> '')
          $sql = $sql." and mov.material_id_material = $id_material";

        if ($id_lista_especial <> '')
          $sql = $sql." and esp.id_lista_especial = $id_lista_especial";

        $sql = $sql." order by mov.data_movto";

        //echo $sql;
        $sql_query = mysqli_query($db, $sql);
        erro_sql("Select Inforamções Ítens Relatório", $db, "");
        echo mysqli_error($db);
        
        $linha2 = mysqli_num_rows($sql_query);
        
        $mat_atual = $linha01['codigo_material'];
        $desc_mat = $linha01['medicamento'];
        
        if(($id_material!=$id_material_aux) and ($id_material_aux!='') and ($linha2>0) and ($cont_linhas=8))
        { //$pdf->Cell(0,5," cont teste ",'',1,"C");
          $cont_linhas = 10;
          $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
          cabecalho_pagina($data_fn);
          cabecalho_tabela($db);
         // cabecalho_troca($mat_atual, $desc_mat);
          $pdf->SetFont('Arial','',9);
        }
        
        if ($cont_linhas > $max_linhas)
        {
          $cont_linhas = 10;
          $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
          cabecalho_pagina($data_fn);
          cabecalho_tabela($db);
          cabecalho_troca($mat_atual, $desc_mat);
          $pdf->SetFont('Arial','',9);
        }
        
        if (mysqli_num_rows($sql_query) > 0)
        {
          while($linha = mysqli_fetch_array($sql_query))
          {
            if (($mat_anterior == '') or ($mat_atual <> $mat_anterior))
            {
              if (($cont_linhas + 3) > $max_linhas)
              {
                $cont_linhas = 8;
                $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
                cabecalho_pagina($data_fn);
                cabecalho_tabela($db);
              }
              $cont_linhas = $cont_linhas + 2;
              $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
              cabecalho_troca($mat_atual, $desc_mat);
              $pdf->SetFont('Arial','',9);
              $fill = 0;
              $primeira_linha = true;
              $mat_anterior = $mat_atual;
            }

            $data_movto = ((substr($linha['data_movto'],8,2))."/".(substr($linha['data_movto'],5,2))."/".(substr($linha['data_movto'],0,4)));
            if ($primeira_linha == true)
            {
              if (($cont_linhas + 1) > $max_linhas)
              {
                $cont_linhas = 10;
                $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
                cabecalho_pagina($data_fn);
                cabecalho_tabela($db);
                cabecalho_troca($mat_atual, $desc_mat);
                $pdf->SetFont('Arial','',9);
              }
              $cont_linhas = $cont_linhas + 1;
              $primeira_linha = false;
              $pdf->Cell($w[0],5,$data_movto,'LR',0,'C',$fill);
              $pdf->Cell($w[1],5,"estoque inicial",'LR',0,'L',$fill);
              $pdf->Cell($w[2],5," ",'LR',0,'R',$fill);
              $pdf->Cell($w[3],5," ",'LR',0,'R',$fill);
              $pdf->Cell($w[4],5," ",'LR',0,'R',$fill);
              $pdf->Cell($w[5],5,intval($linha['saldo_anterior'])." ",'LR',0,'R',$fill);
              $pdf->Cell($w[6],5," ",'LR',0,'C',$fill);
              $pdf->Cell($w[7],5," ",'LR',0,'L',$fill);
              $pdf->Ln();
              $fill=!$fill;
            }

            $tamanho = 1;
            $tamanho = max($tamanho,$pdf->NbLines($w[1],$linha['historico']));
            $tamanho = max($tamanho,$pdf->NbLines($w[7],$linha['motivo']));

            if (($cont_linhas + $tamanho) > $max_linhas)
            {
              $cont_linhas = 10;
              $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
              cabecalho_pagina($data_fn);
              cabecalho_tabela($db);
              cabecalho_troca($mat_atual, $desc_mat);
              $pdf->SetFont('Arial','',9);
            }
            
            $cont_linhas = $cont_linhas + $tamanho;
            $pdf->SetWidths($w);
            $pdf->SetAligns(array('C','L','R','R','R','R','C','L'));
            srand(microtime()*1000000);
            $pdf->Row(array($data_movto, $linha['historico'],
            (intval($linha['qtde_entrada']==0)?"":intval($linha['qtde_entrada']))." ",
            (intval($linha['qtde_saida']==0)?"":intval($linha['qtde_saida']))." ",
            (intval($linha['qtde_perda']==0)?"":intval($linha['qtde_perda']))." ",
            intval($linha['saldo_atual'])." ",
            " ", $linha['motivo']));
            $fill=!$fill;
          }
        }
        else
        {
          $sql03 = "select mov.data_movto, mov.saldo_atual
                    from movto_livro mov, lista_especial esp, livro liv, movto_geral mvg
                    where esp.status_2 = 'A'
                          and esp.livro_id_livro = liv.id_livro
                          and mov.movto_geral_id_movto_geral = mvg.id_movto_geral";
          if ($unidade <> '')
          {
            $sql03 = $sql03." and mov.unidade_id_unidade = $unidade";
          }

          if ($livro <> '')
            $sql03 = $sql03." and liv.id_livro = $livro";

          $sql03 = $sql03." and SUBSTRING(mov.data_movto,1,10) < '$data_inicio'";

          if ($id_material <> '')
            $sql03 = $sql03." and mov.material_id_material = $id_material";

          if ($id_lista_especial <> '')
            $sql03 = $sql03." and esp.id_lista_especial = $id_lista_especial";

          $sql03 = $sql03." order by mov.data_movto desc";

          $sql_query03 = mysqli_query($db, $sql03);
          erro_sql("Informações Ítens Relatório - Else", $db, "");
          echo mysqli_error($db);
          if (mysqli_num_rows($sql_query03) > 0)
          {
            if(($id_material!=$id_material_aux) and ($id_material_aux!='') and ($cont_linhas>8))// and ($linhaR>0))
            {
              $cont_linhas = 10;
              $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
              cabecalho_pagina($data_fn);
              cabecalho_tabela($db);
             // cabecalho_troca($mat_atual, $desc_mat);
              $pdf->SetFont('Arial','',9);
            }
            
            if (($cont_linhas + 3) > $max_linhas)
            {
              $cont_linhas = 8;
              $pdf->Line(10,$pdf->GetY(), 287, $pdf->GetY());
              cabecalho_pagina($data_fn);
              cabecalho_tabela($db);
            }
            $cont_linhas = $cont_linhas + 3;
            cabecalho_troca($mat_atual, $desc_mat);
            $pdf->SetFont('Arial','',9);
            $mat_anterior = $mat_atual;
            
            $linha03 = mysqli_fetch_array($sql_query03);
            $data_movto = ((substr($linha03['data_movto'],8,2))."/".(substr($linha03['data_movto'],5,2))."/".(substr($linha03['data_movto'],0,4)));
            $pdf->Cell($w[0],5,$data_movto,'LR',0,'C',$fill);
            $pdf->Cell($w[1],5,"sem movimento",'LR',0,'L',$fill);
            $pdf->Cell($w[2],5," ",'LR',0,'C',$fill);
            $pdf->Cell($w[3],5," ",'LR',0,'C',$fill);
            $pdf->Cell($w[4],5," ",'LR',0,'C',$fill);
            $pdf->Cell($w[5],5,intval($linha03['saldo_atual'])." ",'LR',0,'R',$fill);
            $pdf->Cell($w[6],5," ",'LR',0,'C',$fill);
            $pdf->Cell($w[7],5," ",'LR',1,'L',$fill);
            $fill=!$fill;
            $pdf->Line(10,$pdf->GetY()-5, 287, $pdf->GetY()-5);
          }
        }
        $pdf->Line(10,$pdf->GetY(), array_sum($w)+10, $pdf->GetY());
        $id_material_aux =$linha01['id_material'];
      }
    }
    termo("TERMO DE ENCERRAMENTO", $data_fn, $db);
    $pdf->Output();
    $pdf->Close();
  }
}
?>
