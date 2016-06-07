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
// | Arquivo ............: relatorio_cons_inf_pdf.php                                |
// | Autor ..............: Fábio Hitoshi Ide <hitoshi.ide@ima.sp.gov.br>             |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de argumentos do Relatório Consolidação Informação   |
// | Data de Criação ....: 27/05/2009                                                |
// | Última Atualização .: 27/05/2009                                                |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

function rec ($uni_sup,&$db){	

	$linha = "";
	$unidade = "";
	$continuar = true;
	while($continuar){
		$sql_uni = "select id_unidade,unidade_id_unidade,nome from unidade where unidade_id_unidade in ($uni_sup) and status_2 = 'A'";
		//echo "<br>".$sql_uni."<br>";
		$sql_query = mysqli_query($db, $sql_uni);
		erro_sql("Selecionar Filhos da unidade Pai", $db, "");
		//echo mysqli_error($db);
		$ids = "";
		if (mysqli_num_rows($sql_query) <= 0){
			$continuar = false;
		}
		while($linha = mysqli_fetch_object($sql_query)){
			$ids.=",\"".$linha ->id_unidade."\"";
			$uni_sup = substr($ids,1);
			$continuar = true;
		}

		$unidade .= $ids;
	}
	return $unidade;
}




/*
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
}*/

function cabecalho_tabela($unidade_pai){
  global $pdf, $nome_mes, $ano_mes;

  $pdf->Cell(0,5,$unidade_pai,0,0,"L");
  $pdf->Ln(6);
  $pdf->Cell('80',5,'Unidade','LTR',0,'C',1);
  for($i = 0; $i < count($nome_mes); $i++)
    $pdf->Cell('18',5,$nome_mes[$i],'LTR',0,'C',1);
  $pdf->Ln();
  $pdf->Cell('80',5,'','LRB',0,'C',1);
  for($i = 0; $i < count($nome_mes); $i++)
    $pdf->Cell('18',5,$ano_mes[$i],'LRB',0,'C',1);
  $pdf->Ln(5.4);
}

function cabecalho()
{
  global $pdf, $data_in, $data_fn, $desc_mov;

  $pdf->AddPage();
  $pdf->Ln();

  $pdf->SetFont('Arial','',9);
  
  $pdf->SetX(95);
  $pdf->Cell(50,5,$desc_mov,0,0,"R");
  $pdf->Cell(0,5,"- " . $data_in."  à  ".$data_fn,0,0,"L");

  $pdf->Ln(6);

  //Colors, line width and bold font
  /*$pdf->SetFillColor(14,90,152);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(255);  // cor do texto*/
  $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(0);  // cor do texto

  //$pdf->SetDrawColor(0,0,0);  // cor da linha
  $pdf->SetLineWidth(.3);
  $pdf->SetFont('','B');

  //Color and font restoration
  /*$pdf->SetFillColor(224,235,255);
  $pdf->SetTextColor(0);*/
  $pdf->SetFont('');
}

if (file_exists("../../config/config.inc.php"))
{
  require "../../config/config.inc.php";
  set_time_limit(0);
  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  if ($_POST['unidade01'] <> '')
    $nome_und = $_POST['unidade01'];
  else
    $nome_und = $_POST['unidade02'];
  $tipo_mov = $_POST['operacao'];
  $movimento = $_POST['descricao'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];

  if ($movimento == 0)
  {
    $desc_mov = strtoupper($tipo_mov);
  }
  else
  {
    $sql = "select descricao
            from tipo_movto
            where id_tipo_movto = $movimento";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Tipo Movto", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $desc_mov = strtoupper($linha['descricao']);
    }
  }

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

    cabecalho();
    $data_inicial=split("[/]", $data_in);
    $data_final=split("[/]", $data_fn);
    //obtem todos os meses a partir da data inicial e final
    $sql_mes_ano="select (case tab.mes
                  when '01' then 'Janeiro'
                  when '02' then 'Fevereiro'
                  when '03' then 'Março'
                  when '04' then 'Abril'
                  when '05' then 'Maio'
                  when '06' then 'Junho'
                  when '07' then 'Julho'
                  when '08' then 'Agosto'
                  when '09' then 'Setembro'
                  when '10' then 'Outubro'
                  when '11' then 'Novembro'
                  else 'Dezembro'
                  end) as mes_nome,
                 tab.mes,
                 tab.ano
          from (select date_format(data_log, '%m') as mes,
                       date_format(data_log, '%Y') as ano
                from log_movto_consolidado
                where substring(data_log, 1, 7) between '$data_inicial[1]-$data_inicial[0]' and '$data_final[1]-$data_final[0]'
                group by substring(data_log, 1, 7)) as tab";
    $sql_query = mysqli_query($db, $sql_mes_ano);
    erro_sql("MES/ANO", $db, "");
    echo mysqli_error($db);
    while($mes_ano=mysqli_fetch_object($sql_query)){
      $nome_mes[]=$mes_ano->mes_nome;
      $ano_mes[]=$mes_ano->ano;
    }
    //obtem todos as unidades superiores a partir da data inicial e final
    $sql="select distinct uni.id_unidade,
                          uni.nome
          from movto_consolidado as mc,
               unidade as uni
          where uni.id_unidade=mc.unidade_id_unidade and
                concat(ano, '-', if(length(mes)=1,concat('0', mes), mes)) between '$data_inicial[1]-$data_inicial[0]' and '$data_final[1]-$data_final[0]'";
   
if (($unidade !="")){
      $unidades = $unidade;
      $sql = $sql." and mc.id_unidade in ($unidades)";	  
    }else {		
		$uni_sup = $_SESSION[id_unidade_sistema];		
		$ids_unidades =	"\"-1\"".rec($uni_sup,$db);
		$sql = $sql."and mc.id_unidade in ($ids_unidades)";
	}
	

/*
   if($unidade!=""){
      $unidades=$unidade;
      busca_nivel($unidade, $db);
      $sql.=" and mc.id_unidade in ($unidades)";
    }*/
    $sql.=" order by uni.nome";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("UNIDADE PAI", $db, "");
    echo mysqli_error($db);
    if(mysqli_num_rows($sql_query)<=0){
      cabecalho_tabela($nome_und);
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5,"Não Foram Encontrados Dados para a Pesquisa!",0,1,"L");
    }
    else{
      $cont_linha=1;
      while($unidade_pai=mysqli_fetch_object($sql_query)){
        if($cont_linha>26){
          cabecalho();
          $cont_linha=1;
        }
        cabecalho_tabela($unidade_pai->nome);
        $cont_linha+=3;
        //obtem todas as unidades basicas a partir da data inicial e final
        $sql="select distinct uni.nome,
                              uni.id_unidade
              from movto_consolidado as mc,
                   unidade as uni
              where uni.id_unidade=mc.id_unidade and
                    mc.unidade_id_unidade=$unidade_pai->id_unidade and";
								
									
        if($unidades!=""){
         $sql.=" mc.id_unidade in ($unidades) and";
        }
        $sql.=" concat(ano, '-', if(length(mes)=1,concat('0', mes), mes)) between '$data_inicial[1]-$data_inicial[0]' and '$data_final[1]-$data_final[0]'
                order by uni.nome";
				
				
	
        $result=mysqli_query($db, $sql);
        erro_sql("UNIDADE BASICA", $db, "");
        while($unidade_basica=mysqli_fetch_object($result)){
          if($cont_linha>26){
            cabecalho();
            $cont_linha=1;
            cabecalho_tabela($unidade_pai->nome);
            $cont_linha+=3;
          }
          $pdf->Cell(80,5,$unidade_basica->nome,'LRB',0);
          $result_mes=mysqli_query($db, $sql_mes_ano);
          while($mes=mysqli_fetch_object($result_mes)){
            //obtem total de cada mes para cada unidade basica
            $sql="select sum(total_mov) as total_mov
                  from movto_consolidado
                  where id_unidade=$unidade_basica->id_unidade and
                        mes=$mes->mes and
                        ano=$mes->ano";
            if($movimento!=0){
              $sql.=" and id_tipo_movimento=$movimento";
            }
            else{
              $sql.=" and id_tipo_movimento in (select id_tipo_movto
                                                from tipo_movto
                                                where operacao='$tipo_mov' and
                                                      status_2='A')";
            }
            $sql.=" group by id_unidade,
                          mes,
                          ano";
            $result_total_mes_unidade=mysqli_query($db, $sql);
            erro_sql("TOTAL MES UNIDADE", $db, "");
            $total_mes_unidade=mysqli_fetch_object($result_total_mes_unidade);
            if($total_mes_unidade==""){
              $qtde_mes_unidade="0";
            }
            else{
              $qtde_mes_unidade=$total_mes_unidade->total_mov;
            }
            $pdf->Cell(18,5,$qtde_mes_unidade,'LRB',0,'R');
          }
          $pdf->Ln();
          $cont_linha++;
        }
        $pdf->Ln();
        $cont_linha++;
      }
      if($cont_linha>26){
        cabecalho();
        $cont_linha=1;
      }
      $pdf->Cell(80,5,'Total','LRBT',0,'L');
      //obtem total por mes de todas as unidades
      $result_mes=mysqli_query($db, $sql_mes_ano);
      while($mes=mysqli_fetch_object($result_mes)){
        $sql="select sum(total_mov) as total_mov
              from movto_consolidado
              where mes=$mes->mes and
                    ano=$mes->ano";
        if($movimento!=0){
          $sql.=" and id_tipo_movimento=$movimento";
        }
        else{
          $sql.=" and id_tipo_movimento in (select id_tipo_movto
                                            from tipo_movto
                                                 where operacao='$tipo_mov' and
                                                       status_2='A')";
        }
				
        if($unidades!=""){
         $sql.=" and id_unidade in ($unidades)";
        }
        $sql.=" group by ano,
                mes";
					
			
			//exit($sql);
        $result_total_mes=mysqli_query($db, $sql);
        erro_sql("TOTAL MES", $db, "");
        $total_mes=mysqli_fetch_object($result_total_mes);
        if($total_mes==""){
          $qtde_mes="0";
        }
        else{
          $qtde_mes=$total_mes->total_mov;
        }
        $pdf->Cell(18,5,$qtde_mes,'LRBT',0,'R');
      }
	  
	  		
    }
    $pdf->Output();
    $pdf->Close();
}
?>
