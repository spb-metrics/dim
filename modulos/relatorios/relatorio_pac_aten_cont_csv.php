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
  // | Autor ..............: Fabio Hitoshi Ide                                         |
  // +---------------------------------------------------------------------------------+
  // | Função .............: Relatório Pacientes Atenção Continuada (.csv)             |
  // | Data de Criação ....: 23/01/2007 - 11:30                                        |
  // | Última Atualização .: 15/02/2007 - 18:15                                        |
  // | Versão .............: 1.0.0                                                     |
  // +---------------------------------------------------------------------------------+

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
    //nova variavel para guardar o numero do prontuario do paciente.
    $prontuario = $_POST['prontuario'];

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
    $file .= "\nUnidade: ";
    if ($nome_und == '')
      $file .= "Todas as Unidades";
    else
      $file .= $nome_und;

    $file.="\nAtenção Continuada: ";
    if($atencao=="todos"){
      $file.="Todas as Atenções Continuadas";
    }
    else{
      $sql="select * from atencao_continuada where id_atencao_continuada='$atencao'";
      $res=mysqli_query($db, $sql);
      erro_sql("Cabeçalho", $db, "");
      if(mysqli_num_rows($res)>0){
        $atencao_info=mysqli_fetch_object($res);
      }
      $file.=$atencao_info->descricao;
    }
    $file.="\nExibir Pacientes: ";
    if($paciente=="1"){
      $file.="Com Dispensação";
    }
    if($paciente=="2"){
      $file.="Sem Dispensação";
    }

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
   // echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Ítens Relatório", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
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
          if ((($und_cad_anterior == '') and ($aten_cont_anterior == ''))
               or (($und_cad_atual <> $und_cad_anterior) or ($aten_cont_atual <> $aten_cont_anterior)))
          {
            $und_cad_anterior = $und_cad_atual;
            $aten_cont_anterior = $aten_cont_atual;
            $file .= "\n\nUnidade: ".$und_cad_atual.";";
            $file .= "Atenção Continuada: ".$aten_cont_anterior;

            $file .= "\n\nCartão SUS;Paciente;Prontuário;Mãe;Data Nasc.;Data Última Dispensação\n";
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
              $cartao_info=$linha['cartao_sus'];
            }
            else{
              if($linha[cartao_sus_prov]!="0"){
                $cartao_info=$linha['cartao_sus_prov'];
              }
              else{
                $cartao_info="0";
              }
            }
            $dt_nasc = ((substr($linha['data_nasc'],8,2))."/".(substr($linha['data_nasc'],5,2))."/".(substr($linha['data_nasc'],0,4)));
            $file .= "\n".$cartao_info.";".$linha['pacnome'].";".$linha['prontuario'].";".$linha[nome_mae].";".$dt_nasc.";".$dt_ult_disp.";";
          }
          else{
            $file .= "\n\nUnidade:;";
            $file .= "Atenção Continuada:";
            $file .= "\n\nCartão SUS;Paciente;Mãe;Data Nasc.;Data Última Dispensação\n";
            $file.="Não Existe Paciente Associado a Categoria de Atenção Continuada!";
          }
        }
        $info="";
      }
      if($pesquisa==""){
        $file .= "\n\nUnidade:;";
        $file .= "Atenção Continuada:";
        $file .= "\n\nCartão SUS;Paciente;Mãe;Data Nasc.;Data Última Dispensação\n";
        $file.="Não Foram Encontrados Dados para a Pesquisa!";
      }
    }
    else
    {
      $file .= "\n\nUnidade:;";
      $file .= "Atenção Continuada:";
      $file .= "\n\nCartão SUS;Paciente;Mãe;Data Nasc.;Data Última Dispensação\n";
      $file.="Não Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relatório_Paciente_Atenção_Continuada.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
  }
?>
