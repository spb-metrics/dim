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
  // | Arquivo ............: relatorio_pre_med_csv.php                                 |
  // | Autor ..............: F�bio Hitoshi Ide                                         |
  // +---------------------------------------------------------------------------------+
  // | Fun��o .............: Relat�rio de Prescritores por Medicamento (.csv)          |
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
    $file .= "Per�odo de Prescri��o: ".$data_in."  �  ".$data_fn;
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
    erro_sql("�tens Relat�rio", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $total_prescrito=0;
      $total_dispensado=0;
      while($linha = mysqli_fetch_array($sql_query))
      {
        $cod_atual = $linha['codigo_material'];
        $med_atual = $linha['matdescricao'];
        if (($cod_anterior == '' && $med_anterior == '') or ($cod_atual <> $cod_anterior && $med_atual <> $med_anterior))
        {
          if($total_prescrito!=0 && $total_dispensado!=0){
            $file.="\nTotal Prescrito: " . $total_prescrito . ";Total Dispensado: " . $total_dispensado;
          }
          $total_prescrito=0;
          $total_dispensado=0;
          $cod_anterior = $cod_atual;
          $med_anterior = $med_atual;
          $file .= "\n\nMedicamento: ".$cod_atual . ";";
          $file .= $med_atual;
          $file .= "\n\nInscri��o;Nome;Conselho Profissional;Qtde Prescrita;Qtde Dispensada;Unidade\n";
        }
        $file.="\n" . $linha[inscricao] . ";" . $linha[profnome] . ";" . $linha[consdescricao] . ";";
        $file .= (int)$linha['qtde_prescrita'].";".(int)$linha['qtde_dispensada'].";";
        $file .= $linha[unidnome];
        if($cod_anterior=="" || $cod_anterior==$cod_atual){
          $total_prescrito+=(int)$linha[qtde_prescrita];
          $total_dispensado+=(int)$linha[qtde_dispensada];
        }
      }
      $file.="\nTotal Prescrito: " . $total_prescrito . ";Total Dispensado: " . $total_dispensado;
    }
    else
    {
      $file .= "\n\nMedicamento: ".$cod_atual . ";";
      $file .= $med_atual;
      $file .= "\n\nInscri��o;Nome;Conselho Profissional;Qtde Prescrita;Qtde Dispensada;Unidade\n";
      $file .= "N�o Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relat�rio_Prescritores_Medicamento.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
}
?>
