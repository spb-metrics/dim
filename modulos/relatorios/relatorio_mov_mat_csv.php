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
// | Arquivo ............: relatorio_mov_mat_csv.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Movimentação de Materiais (.csv)             |
// | Data de Criação ....: 22/01/2007 - 09:30                                        |
// | Última Atualização .: 19/03/2007 - 10:55                                        |
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
  $medicamento = $_POST['medicamento'];
  $nome_med = $_POST['medicamento01'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  $codigos = $_POST['codigos'];

  if ($movimento == 0)
  {
    $desc_mov = "TODOS OS MOVIMENTOS";
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

    $file .= "Unidade: ".$und_user."\n";

    $sql = "select descricao
            from item_menu
            where aplicacao_id_aplicacao = $aplicacao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplicação", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $nome_rel = $linha['descricao'];
    }
    $file .= $nome_rel."\n";

    $file .= "\nCRITÉRIOS DE PESQUISA\n";
    $file .= "Período: ".$data_in."  à  ".$data_fn;
    $file .= "\nTipo de Movimento: ".$tipo_mov." - ".$desc_mov;
    
    $file .= "\nUnidade: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;

    $file .= "\nMedicamento: ";
    if ($nome_med == '')
      $file .= "Todos os Medicamentos";
    else
      $file .= $nome_med;


    $sql = "select distinct tmv.descricao as tipo_movto, und.nome as unidade, mvg.id_movto_geral as documento, mat.codigo_material as codigo, mat.descricao as medicamento, img.lote as lote,
                   fab.descricao as fabricante, img.validade as validade, img.qtde as quantidade, mvg.data_movto as data_retirada
            from material mat
                 inner join itens_movto_geral img on mat.id_material = img.material_id_material
                 inner join fabricante fab on img.fabricante_id_fabricante = fab.id_fabricante
                 inner join movto_geral mvg on img.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join tipo_movto tmv on mvg.tipo_movto_id_tipo_movto = tmv.id_tipo_movto
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and fab.status_2 = 'A'
                  and und.status_2 = 'A'";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(mvg.data_movto,1,10) between '$data_inicio' and '$data_fim'";

    /*echo $unidade;
    echo $nome_und;*/
	
	if (($unidade <> '') and ($nome_und <> '')){
      $unidades = $unidade;
      $sql = $sql." and und.id_unidade in ($unidades)";	  
    }else {		
		$uni_sup = $_SESSION[id_unidade_sistema];		
		$ids_unidades =	"\"-1\"".rec($uni_sup,$db);
		$sql = $sql."and und.id_unidade in ($ids_unidades)";
	}
	
    /*if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and und.id_unidade in ($unidades)";
    }
    else */ if ($codigos <> '')
    {
      $sql = $sql." and und.id_unidade in ($codigos)";
    }

    if ($tipo_mov <> '')
      $sql = $sql." and tmv.operacao = '$tipo_mov'";

    if ((($movimento <> '') and ($movimento <> '0')) and ($desc_mov <> ''))
      $sql = $sql." and tmv.id_tipo_movto = $movimento";

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    $sql = $sql." order by und.nome, ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." mvg.data_movto";
        break;
      case 1:
        $sql = $sql." mvg.id_movto_geral";
        break;
      case 2:
        $sql = $sql." fab.descricao";
        break;
      case 3:
        $sql = $sql." img.lote";
        break;
      case 4:
        $sql = $sql." mat.descricao";
        break;
      case 5:
        $sql = $sql." tmv.descricao";
        break;
    }
    //echo $sql;
    //exit;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if(mysqli_num_rows($sql_query) > 0){
      $info="";
      $qtde=0;
      $qtd_t=0; //total das quantidades
      $qtd_doc=0; //quantidade total de documentos
      $qtd_t_doc=0; //qtde de todos os documentos
      $qtd_g=0; //qtde geral de todas as qtdes
      $indice=0; //indice do vetor
      $docs=array(mysqli_num_rows($sql_query)); //vetor para guardar os numeros dos documentos
      $cont_und=0;
      
      while($linha = mysqli_fetch_array($sql_query)){
         if($info!=""){
           $flag="sim";
           $valores=split("[|]", $info);
           for($i=0; $i<count($valores); $i++){
              if($valores[$i]==$linha[documento]){
                $flag="nao";
                break;
              }
           }
           if($flag=="sim"){
             $qtde++;
            }
          }
          else{
              $qtde++;
          }
          $info.=$linha[documento] . "|";
          $und_atual = $linha['unidade'];

          if (($und_anterior != '') && ($und_atual <> $und_anterior)){
             $result=array_unique($docs);
             $qtd_doc=count($result);
             $file.="\n\n Total de Documentos: $qtd_doc;;;;;;Qtde Total: $qtd_t\n\n";
             $qtd_g+=$qtd_t;
             $qtd_t_doc+=$qtd_doc;
             $qtd_t=0;
             $qtd_doc=0;
             $indice=0;
             unset($docs);
             $cont_und++;
          }

          if(($und_anterior == '') or ($und_atual <> $und_anterior)){
            $file .= "\n\nUnidade: ".$und_atual."\n";
            $file .= "\nTipo Movto;Documento;Medicamento;Lote;Fabricante;Validade;Quantidade;Data\n";
            $und_anterior = $und_atual;
            $qtd_g+=$qtd_t;
            $qtd_t_doc+=$qtd_doc;
            $qtd_t=0;
            $qtd_doc=0;

          }
          $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
          $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
          $file .= "\n".$linha['tipo_movto'].";".$linha['documento'].";".$linha['codigo']." - ".$linha['medicamento'].";".$linha['lote'].";";
          $file .= $linha['fabricante'].";".$validade.";".intval($linha['quantidade']).";".$dt_ret;
          $qtd_t+=intval($linha['quantidade']);
          $docs[$indice]=$linha['documento'];
          $indice++;
         }
      
         $result=array_unique($docs);
         $qtd_doc=count($result);
         $qtd_t_doc+=$qtd_doc;
         $qtd_g+=$qtd_t;
         $file.="\n\n Total de Documentos: $qtd_doc;;;;;;Qtde Total: $qtd_t";
         if ($cont_und>1){
         $file.="\n Total Geral Documentos: $qtd_t_doc;;;;;;Total Geral: $qtd_g";
         }
    }
    else{
        $file .= "\n\nUnidade: ".$nome_und."\n";
        $file .= "\nDocumento;Medicamento;Lote;Fabricante;Validade;Quantidade;Data\n";
        $file.="Não Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relatório_Movimentacao_Materiais.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
   
  }
?>
