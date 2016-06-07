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
// | Arquivo ............: relatorio_pac_cad_dim_csv.php                             |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório Pacientes Cadastrados DIM (.csv)                |
// | Data de Criação ....: 23/01/2007 - 11:30                                        |
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


/*function busca_nivel($und_sup, $link)
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

  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  if ($_POST['unidade01'] <> '')
    $nome_und = $_POST['unidade01'];
  else
    $nome_und = $_POST['unidade02'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  $codigos = $_POST['codigos'];

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
    $file .= "\nUnidade de Cadastro: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;

    $sql = "select pac.nome as paciente, pac.nome_mae, pac.data_nasc, pac.sexo, pac.status_2,
                   pac.tipo_logradouro, pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro,
                   und01.nome as und_cad, und02.nome as und_ref, cid.nome as cidade
            from paciente pac
                 left join cartao_sus cart on pac.id_paciente = cart.paciente_id_paciente
                 inner join unidade und01 on pac.unidade_cadastro = und01.id_unidade
                 inner join unidade und02 on pac.unidade_referida = und02.id_unidade
                 inner join cidade cid on pac.cidade_id_cidade = cid.id_cidade
            where (cart.cartao_sus is NULL or cart.cartao_sus = 0)";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(pac.data_incl,1,10) between '$data_inicio' and '$data_fim'";

    if (($unidade <> '') and ($nome_und <> '')){
      $unidades = $unidade;
      $sql = $sql." and pac.unidade_cadastro in ($unidades)";	  
    }else {		
		$uni_sup = $_SESSION[id_unidade_sistema];		
		$ids_unidades =	"\"-1\"".rec($uni_sup,$db);
		$sql = $sql."and pac.unidade_cadastro in ($ids_unidades)";
	}
	
	
	/*if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and pac.unidade_cadastro in ($unidades)";
    }
    else */
	
	if ($codigos <> '')
    {
      $sql = $sql." and pac.unidade_cadastro in ($codigos)";
    }

    $sql = $sql." order by und01.nome, und02.nome, ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." pac.data_nasc";
        break;
      case 1:
        $sql = $sql." pac.nome_mae";
        break;
      case 2:
        $sql = $sql." pac.nome";
        break;
      case 3:
        $sql = $sql." pac.sexo";
        break;
      case 4:
        $sql = $sql." pac.status_2";
        break;
    }
   // echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      while($linha = mysqli_fetch_array($sql_query))
      {
        $und_cad_atual = $linha['und_cad'];
        $und_ref_atual = $linha['und_ref'];
        if ((($und_cad_anterior == '') and ($und_ref_anterior == ''))
             or (($und_cad_atual <> $und_cad_anterior) or ($und_ref_atual <> $und_ref_anterior)))
        {
          $und_cad_anterior = $und_cad_atual;
          $und_ref_anterior = $und_ref_atual;
          $file .= "\n\nUnidade de Cadastro: ".$und_cad_atual.";";
          $file .= "Unidade Referida: ".$und_ref_anterior;
          
          $file .= "\n\nPaciente;Nome da Mãe;Data Nascimento;Sexo;Status;Endereço\n";
        }
        $dt_nasc = ((substr($linha['data_nasc'],8,2))."/".(substr($linha['data_nasc'],5,2))."/".(substr($linha['data_nasc'],0,4)));
        $endereco = $linha['tipo_logradouro']." ".$linha['nome_logradouro'].", ".$linha['numero'];
        $endereco = $endereco.", ".$linha['complemento']." - ".$linha['bairro']." - ".$linha['cidade'];

        $file .= "\n".$linha['paciente'].";".$linha['nome_mae'].";".$dt_nasc.";";
        $file .= $linha['sexo'].";".$linha['status_2'].";".$endereco;
      }
    }
    else
    {
      $file .= "\n\nUnidade de Cadastro:;";
      $file .= "Unidade Referida:";
      $file .= "\n\nPaciente;Nome da Mãe;Data Nascimento;Sexo;Status;Endereço\n";
      $file.="Não Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relatório_Paciente_Cadastrados_DIM.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
}
?>
