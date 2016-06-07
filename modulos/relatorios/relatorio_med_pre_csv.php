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
  // | Arquivo ............: relatorio_med_pre_csv.php                                 |
  // | Autor ..............: Fábio Hitoshi Ide                                         |
  // +---------------------------------------------------------------------------------+
  // | Função .............: Relatório de Medicamentos por Prescritor (.csv)           |
  // | Data de Criação ....: 18/01/2007 - 13:20                                        |
  // | Última Atualização .: 16/03/2007 - 10:50                                        |
  // | Versão .............: 1.0.0                                                     |
  // +---------------------------------------------------------------------------------+

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    $data_in = $_POST['data_in'];
    $data_fn = $_POST['data_fn'];
    $unidade = $_POST['unidade'];
    $nome_und = $_POST['unidade01'];
    $prescritor = $_POST['prescritor'];
    $nome_pre = $_POST['prescritor01'];
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
    erro_sql("Select Aplicação", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $nome_rel = $linha['descricao'];
    }
    $file .= $nome_rel."\n";
    
    $file .= "\nCRITÉRIOS DE PESQUISA\n";
    $file .= "Período de Prescrição: ".$data_in."  à  ".$data_fn;
    $file .= "\nUnidade: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;

    $file .= "\nPrescritor: ";
    if ($nome_pre <> '')
      $file .= $nome_pre;
      
    $file .= "\nMedicamento: ";
    if ($nome_med == '')
      $file .= "Todos os Medicamentos";
    else
      $file .= $nome_med;

    $sql="select mat.codigo_material,
                 mat.descricao,
                 sum(item.qtde_prescrita) as qtde_prescrita,
                 sum(item.qtde_disp_anterior + item.qtde_disp_mes) as qtde_dispensada,
                 max(rec.data_ult_disp) as data_ult_disp,
                 unid.nome
         from receita as rec,
              itens_receita as item,
              profissional as prof,
              unidade as unid,
              material as mat
         where rec.id_receita=item.receita_id_receita and
               rec.unidade_id_unidade=unid.id_unidade and
               rec.profissional_id_profissional=prof.id_profissional and
               item.material_id_material=mat.id_material and
               prof.status_2='A' and
               unid.status_2='A' and
               mat.status_2='A'";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(rec.data_ult_disp,1,10) between '$data_inicio' and '$data_fim'";

    /*echo $unidade;
    echo $nome_und;*/
    if (($unidade <> '') and ($nome_und <> ''))
    {
      $unidades = $unidade;
      $sql = $sql." and unid.id_unidade in ($unidades)";
    }

    if (($prescritor <> '') and ($nome_pre <> ''))
      $sql = $sql." and prof.id_profissional = '$prescritor'";

    if (($medicamento <> '') and ($nome_med <> ''))
      $sql = $sql." and mat.id_material = '$medicamento'";

    $sql = $sql." group by mat.descricao, unid.nome order by mat.descricao,";

    switch ($ordem)
    {
      case 0:
        $sql = $sql." qtde_dispensada";
        break;
      case 1:
        $sql = $sql." qtde_prescrita";
        break;
      case 2:
        $sql = $sql." data_ult_disp desc";
        break;
      case 3:
        $sql = $sql." unid.nome";
        break;
    }
    //echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $total_prescrito=0;
      $total_dispensado=0;
      while($linha = mysqli_fetch_array($sql_query))
      {
        $cod_atual = $linha['codigo_material'];
        $med_atual = $linha['descricao'];
        if (($cod_anterior == '' && $med_anterior == '') or ($cod_atual <> $cod_anterior && $med_atual <> $med_anterior))
        {
          if($total_prescrito!=0 && $total_dispensado!=0){
            $file.="\nTotal Prescrito: " . $total_prescrito . ";Total Dispensado: " . $total_dispensado;
          }
          $total_prescrito=0;
          $total_dispensado=0;
          $cod_anterior = $cod_atual;
          $med_anterior = $med_atual;
          $file .= "\n\nCódigo: ".$cod_atual . ";";
          $file .= "Medicamento: ".$med_atual;
          $file .= "\n\nQtde Prescrita;Qtde Dispensada;Data Últ. Dispensação;Unidade\n";
        }
        $dt_ult_disp = ((substr($linha['data_ult_disp'],8,2))."/".(substr($linha['data_ult_disp'],5,2))."/".(substr($linha['data_ult_disp'],0,4)));

        $file .= "\n".(int)$linha['qtde_prescrita'].";".(int)$linha['qtde_dispensada'].";";
        $file .= $dt_ult_disp.";".$linha['nome'];
        if($cod_anterior=="" || $cod_anterior==$cod_atual){
          $total_prescrito+=(int)$linha[qtde_prescrita];
          $total_dispensado+=(int)$linha[qtde_dispensada];
        }
      }
      $file.="\nTotal Prescrito: " . $total_prescrito . ";Total Dispensado: " . $total_dispensado;
    }
    else
    {
      $file .= "\n\nCódigo: ".$und_atual . ";";
      $file .= "Medicamento: ".$und_atual;
      $file .= "\n\nQtde Prescrita;Qtde Dispensada;Data Últ. Dispensação;Unidade\n";
      $file .= "Não Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relatório_Medicamentos_Prescritor.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
}
?>
