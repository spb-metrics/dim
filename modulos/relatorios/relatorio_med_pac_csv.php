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
// | Arquivo ............: relatorio_med_pac_csv.php                                 |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Relat�rio de Medicamentos por Paciente (.csv)             |
// | Data de Cria��o ....: 18/01/2007 - 13:20                                        |
// | �ltima Atualiza��o .: 16/03/2007 - 10:50                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+


if (file_exists("../../config/config.inc.php"))
{
  require "../../config/config.inc.php";

  $data_in = $_POST['data_in'];
  $data_fn = $_POST['data_fn'];
  $unidade = $_POST['unidade'];
  $nome_und = $_POST['unidade01'];
  $paciente = $_POST['paciente'];
  $nome_pac = $_POST['paciente01'];
  $status = $_POST['status'];
  $medicamento = $_POST['medicamento'];
  $nome_med = $_POST['medicamento01'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  $codigos = $_POST['codigos'];

    $file .= "Unidade: ".$und_user."\n";
    
    $sql = "select descricao
            from item_menu
            where aplicacao_id_aplicacao = $aplicacao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Select Aplica��o", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $nome_rel = $linha['descricao'];
    }
    $file .= $nome_rel."\n";
    
    $file .= "\nCRIT�RIOS DE PESQUISA\n";
    $file .= "Per�odo: ".$data_in."  �  ".$data_fn;
    $file .= "\nUnidade: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;

    $file .= "\nPaciente: ";
    if ($nome_pac <> '')
      $file .= $nome_pac;
      
    $file .= "\nStatus: ";
    if ($status <> '')
      $file .= $status;

    $file .= "\nMedicamento: ";
    if ($nome_med == '')
      $file .= "Todos os Medicamentos";
    else
      $file .= $nome_med;


    $sql = "select distinct und.nome as unidade, mat.codigo_material as codigo, mat.descricao as medicamento,
                   img.lote as lote, fab.descricao as fabricante, img.validade as validade,
                   img.qtde as quantidade, mvg.data_movto as data_retirada,
                   CONCAT(rec.ano,'-',rec.unidade_id_unidade,'-',rec.numero) as nr_receita
            from material mat
                 inner join itens_movto_geral img on mat.id_material = img.material_id_material
                 inner join fabricante fab on img.fabricante_id_fabricante = fab.id_fabricante
                 inner join movto_geral mvg on img.movto_geral_id_movto_geral = mvg.id_movto_geral
                 inner join unidade und on mvg.unidade_id_unidade = und.id_unidade
                 inner join paciente pac on mvg.paciente_id_paciente = pac.id_paciente
                 inner join itens_receita irc on img.itens_receita_id_itens_receita = irc.id_itens_receita
                 inner join receita rec on irc.receita_id_receita = rec.id_receita
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

    if (($paciente <> '') and ($nome_pac <> ''))
      $sql = $sql." and pac.id_paciente = '$paciente'";

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    $sql = $sql." order by und.nome, ";

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
        $sql = $sql." mat.descricao";
        break;
      case 4:
        $sql = $sql." CONCAT(rec.ano,'-',rec.unidade_id_unidade,'-',rec.numero)";
        break;
    }
    //echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("�tens Relat�rio", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0){
       $qtd_rec=0;
       $cont_und=0;
       
       while($linha = mysqli_fetch_array($sql_query)){
            $und_atual = $linha['unidade'];

            if (($und_anterior != '') && ($und_atual <> $und_anterior)){
               $file .= "\n\nReceitas por Unidade:$qtd_rec;;;;;Total Qtde Retirada:$qtd_tr";
               $qtd_t_rec+=$qtd_rec;
               $qtd_g_tr+=$qtd_tr;
               $qtd_rec=0;
               $qtd_tr=0;
               }
            if (($und_anterior == '') or ($und_atual <> $und_anterior)){
               $und_anterior = $und_atual;
               $file .= "\n\nUnidade: ".$und_atual;
               $file .= "\n\nNr Receita;Medicamento;Lote;Fabricante;Validade;Qtde Retirada;Data da Retirada\n";
               $cont_und++;
               }
            $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
            $dt_ret = ((substr($linha['data_retirada'],8,2))."/".(substr($linha['data_retirada'],5,2))."/".(substr($linha['data_retirada'],0,4)));
            $file .= "\n".$linha['nr_receita'].";".$linha['codigo']." - ".$linha['medicamento'].";".$linha['lote'].";";
            $file .= $linha['fabricante'].";".$validade.";".intval($linha['quantidade']).";".$dt_ret;
            $qtd_r=intval($linha['quantidade']);
            $qtd_tr+=$qtd_r;
            $qtd_rec++;
            }
            $qtd_t_rec+=$qtd_rec;
            $qtd_g_tr+=$qtd_tr;
            $file .= "\n\nReceitas por Unidade:$qtd_rec;;;;;Total Qtde Retirada:$qtd_tr\n";
            $file .= "\nTotal de Receitas:$qtd_t_rec;;;;;Total Geral Retirada:$qtd_g_tr";
       }
   else{
       $file .= "\n\nUnidade: ".$nome_und;
       $file .= "\n\nNr Receita;Medicamento;Lote;Fabricante;Validade;Qtde Retirada;Data da Retirada\n";
       $file .= "N�o Foram Encontrados Dados para a Pesquisa!";
       }
    $filename = "Relat�rio_Medicamentos_Paciente.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
    
  }
?>
