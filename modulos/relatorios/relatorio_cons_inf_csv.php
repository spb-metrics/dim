<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: relatorio_cons_inf_csv.php                                |
// | Autor ..............: F�bio Hitoshi Ide <hitoshi.ide@ima.sp.gov.br>             |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Tela de argumentos do Relat�rio Consolida��o Informa��o   |
// | Data de Cria��o ....: 27/05/2009                                                |
// | �ltima Atualiza��o .: 27/05/2009                                                |
// | Vers�o .............: 1.0.0                                                     |
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
  erro_sql("Busca N�vel", $link, "");
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

    $file .= "Unidade: ".$und_user."\n";

    $sql = "select descricao
            from item_menu
            where aplicacao_id_aplicacao = $aplicacao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplica��o", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $nome_rel = $linha['descricao'];
    }
    $file .= $nome_rel."\n";

    $file .= "\n$desc_mov - ".$data_in."  �  ".$data_fn;

    $file .= "\nUnidade;";

    $data_inicial=split("[/]", $data_in);
    $data_final=split("[/]", $data_fn);
    //obtem todos os meses a partir da data inicial e final
    $sql_mes_ano="select (case tab.mes
                  when '01' then 'Janeiro'
                  when '02' then 'Fevereiro'
                  when '03' then 'Mar�o'
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
      $file.=$mes_ano->mes_nome . "/" . $mes_ano->ano . ";";
    }
    //obtem todos as unidades superiores a partir da data inicial e final
    $sql="select distinct uni.id_unidade,
                          uni.nome
          from movto_consolidado as mc,
               unidade as uni
          where uni.id_unidade=mc.unidade_id_unidade and
                concat(ano, '-', if(length(mes)=1,concat('0', mes), mes)) between '$data_inicial[1]-$data_inicial[0]' and '$data_final[1]-$data_final[0]'";
    /*if($unidade!=""){
      $unidades=$unidade;
      busca_nivel($unidade, $db);
      $sql.=" and mc.id_unidade in ($unidades)";
    } */
	
	if (($unidade !="")){
      $unidades = $unidade;
      $sql = $sql." and mc.id_unidade in ($unidades)";	  
    }else {		
		$uni_sup = $_SESSION[id_unidade_sistema];		
		$ids_unidades =	"\"-1\"".rec($uni_sup,$db);
		$sql = $sql."and mc.id_unidade in ($ids_unidades)";
	}
	
    $sql.=" order by uni.nome";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("UNIDADE PAI", $db, "");
    echo mysqli_error($db);
    if(mysqli_num_rows($sql_query)<=0){
      $file.="\nN�o Foram Encontrados Dados para a Pesquisa!";
    }
    else{
      while($unidade_pai=mysqli_fetch_object($sql_query)){
        $file.="\n$unidade_pai->nome";
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
          $file.="\n$unidade_basica->nome;";
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
              $qtde_mes_unidade=0;
            }
            else{
              $qtde_mes_unidade=$total_mes_unidade->total_mov;
            }
            $file.="$qtde_mes_unidade;";
          }
        }
        $file.="\n";
      }
      $file.="\nTotal;";
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
        $result_total_mes=mysqli_query($db, $sql);
        erro_sql("TOTAL MES", $db, "");
        $total_mes=mysqli_fetch_object($result_total_mes);
        if($total_mes==""){
          $qtde_mes=0;
        }
        else{
          $qtde_mes=$total_mes->total_mov;
        }
        $file.="$qtde_mes;";
      }
    }
    $filename = "Relat�rio_Movimentacao_Materiais.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
   
  }
?>
