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
// | Arquivo ............: relatorio_pac_med_csv.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Pacientes por Medicamento (.csv)             |
// | Data de Criação ....: 19/01/2007 - 16:05                                        |
// | Última Atualização .: 19/03/2007 - 10:55                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+
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

  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  if ($_POST['unidade01'] <> '')
    $nome_und = $_POST['unidade01'];
  else
    $nome_und = $_POST['unidade02'];
  $medicamento = $_POST['medicamento'];
  $nome_med = $_POST['medicamento01'];
  $lote = $_POST['lote'];
  $nome_lote = $_POST['lote01'];
  $fabricante = $_POST['fabricante'];
  $nome_fab = $_POST['fabricante01'];
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
    $file .= "\nUnidade: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;
      
    $file .= "\nMedicamento: ";
    if ($nome_med == '')
      $file .= "Todos os Medicamentos;";
    else
      $file .= $nome_med.";";
      
    $file .= "Lote: ";
    if ($nome_lote == '')
      $file .= "Todos os Lotes;";
    else
      $file .= $nome_lote.";";

    $file .= "Fabricante: ";
    if ($nome_fab == '')
      $file .= "Todos os Fabricantes";
    else
      $file .= $nome_fab.";";


    $sql = "select distinct und.nome as unidade, pac.nome as paciente, mat.codigo_material as codigo, mat.descricao as medicamento, img.lote as lote,
                   fab.descricao as fabricante, img.validade as validade, img.qtde as quantidade, mvg.data_movto as data_retirada,
                   pac.tipo_logradouro, pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, pac.telefone
            from material mat
                 inner join itens_movto_geral img on mat.id_material = img.material_id_material
                 inner join fabricante fab on img.fabricante_id_fabricante = fab.id_fabricante
                 inner join movto_geral mvg on img.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join paciente pac on mvg.paciente_id_paciente = pac.id_paciente
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and fab.status_2 = 'A'
                  and und.status_2 = 'A'
                  and pac.status_2 = 'A'";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(mvg.data_movto,1,10) between '$data_inicio' and '$data_fim'";

    /*echo $unidade;
    echo $nome_und;*/
     if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      $sql = $sql." and und.id_unidade in ($unidades)";
    }

	
	
	/*if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and und.id_unidade in ($unidades)";
    }
    else if ($codigos <> '')
    {
      $sql = $sql." and und.id_unidade in ($codigos)";
    }*/

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = $medicamento";

    if (($lote <> '') and ($nome_lote <> ''))
      $sql = $sql." and img.lote = '$nome_lote'";

    if (($fabricante <> '') and ($nome_fab <> ''))
      $sql = $sql." and fab.id_fabricante = '$fabricante'";

    $sql = $sql." order by und.nome, mat.descricao,  ";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." mvg.data_movto";
        break;
      case 1:
        $sql = $sql." fab.descricao";
        break;
      case 2:
        $sql = $sql." img.lote";
        break;
      case 3:
        $sql = $sql." pac.nome";
        break;
      }

    //echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $qtd_pac=0;  //quantidade de pacientes
      $qtd_med=0;  //quantidade de medicamentos retirados
      $qtd_t_pac=0;//quantidade total de pacientes
      $qtd_t_med=0;//quantidade total de medicamentos retirados
      $cont_und=0; //contador de unidade
      
      while($linha = mysqli_fetch_array($sql_query))
      {
        $und_atual = $linha['unidade'];
        $med_atual = $linha['codigo']." - ".$linha['medicamento'];
        
         if (($med_anterior != '') && ($med_atual <> $med_anterior)){
             $file .= "\n\nPacientes por Medicamento:$qtd_pac;;;;;Total Retirada:$qtd_r\n";
             $qtd_t_pac+=$qtd_pac;
             $qtd_t_r+=$qtd_r;
             $qtd_pac=0;
             $qtd_r=0;
          }
        
        if (($und_anterior == '') or ($und_atual <> $und_anterior))
        {
          $und_anterior = $und_atual;
          //$file .= "\n\nUnidade: ".$und_atual;
        }

        if (($med_anterior == '') or ($med_atual <> $med_anterior))
        {
          $med_anterior = $med_atual;
          //$file .= "\n\nMedicamento: ".$med_atual."\n";
          $file .= "\n\nPaciente;Endereço;Telefone;Medicamento;Lote;Fabricante;Validade;Qtde Retirada;Data da Retirada;Unidade\n";
          $cont_und++;
        }
        
        $endereco=$linha["tipo_logradouro"] . " " . $linha["nome_logradouro"] . " " . $linha["numero"] . " " . $linha["complemento"] . " " . $linha["bairro"];
        $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
        $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
        $file .= "\n".$linha['paciente'].";".$endereco.";".$linha["telefone"].";".$med_atual.";".$linha['lote'].";".$linha['fabricante'].";";
        $file .= $validade.";".intval($linha['quantidade']).";".$dt_ret.";".$und_atual;
        $qtd_re=intval($linha['quantidade']);
        $qtd_r+=$qtd_re;
        $qtd_pac++;
      }
      $qtd_t_pac+=$qtd_pac;
      $qtd_t_r+=$qtd_r;
      $file .= "\n\nPacientes por Medicamento:$qtd_pac;;;;;Total Retirada:$qtd_r\n";
      if ($cont_und>1){
         $file .= "\nTotal Geral Pacientes:$qtd_t_pac;;;;;Total Geral Retiradas:$qtd_t_r";
      }
    }
    else
    {
      $file .= "\n\nUnidade: ".$nome_und;
      $file .= "\n\nMedicamento:\n";
      $file .= "\nPaciente;Endereço;Telefone;Lote;Fabricante;Validade;Qtde Retirada;Data da Retirada\n";
      $file .= "Não Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relatório_Pacientes_Medicamento.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
}
?>
