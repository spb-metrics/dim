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

  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    $data_in=$_POST['data_in'];
    $data_fn=$_POST['data_fn'];
    $unidade=$_POST['unidade'];
    $nome_und=$_POST['unidade01'];
    $medicamento=$_POST['medicamento'];
    $sql="select codigo_material
          from material
          where id_material=$medicamento";
    $result=mysqli_query($db, $sql);
    erro_sql("select codigo material", $db, "");
    if(mysqli_num_rows($result)>0){
      $cod_mat=mysqli_fetch_object($result);
      $codigo_material=$cod_mat->codigo_material;
    }
    $nome_med=$_POST['medicamento01'];
    $aplicacao=$_POST['aplicacao'];
    $und_user=$_POST['nome_und'];

    /////// buscar unidades q pertencem ao distrito

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
    }

    //====

    $movto_geral_aux=0;
    $file.="Unidade: ".$und_user."\n";
    
    $sql="select descricao
          from item_menu
          where aplicacao_id_aplicacao=$aplicacao";
    $sql_query=mysqli_query($db, $sql);
    erro_sql("Select Aplica��o", $db, "");
    echo mysqli_error($db);
    if(mysqli_num_rows($sql_query)>0){
      $linha=mysqli_fetch_array($sql_query);
      $nome_rel=$linha['descricao'];
    }
    $file.= $nome_rel;
    
    $sql="select movl.movto_geral_id_movto_geral,
                 imov.lote,
                 imov.qtde,
                 movl.saldo_anterior,
                 movl.qtde_entrada,
                 movl.qtde_saida,
                 movl.qtde_perda,
                 movl.saldo_atual,
                 DATE_FORMAT(movl.data_movto,'%d/%m/%Y') as data_movto,
                 movl.historico,
                 usr.login
          from movto_livro as movl, movto_geral as movt,
               itens_movto_geral as imov, usuario as usr
          where movt.id_movto_geral= movl.movto_geral_id_movto_geral and
                movl.movto_geral_id_movto_geral=imov.movto_geral_id_movto_geral and
                movt.usuario_id_usuario=usr.id_usuario and
                movl.material_id_material=imov.material_id_material and
                movl.material_id_material=$medicamento";

    if($data_in!="" && $data_fn!=""){
      $data_inicio=((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
      $data_fim=((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
      $sql=$sql." and SUBSTRING(movl.data_movto,1,10) between '$data_inicio' and '$data_fim'";
    }
    if ($unidade <> '')
    {
      $unidades = $unidade;
      busca_nivel($unidade, $db);
      $sql = $sql." and movl.unidade_id_unidade in ($unidades)";
    }
    $sql = $sql." order by movl.movto_geral_id_movto_geral asc, lote desc";
    //echo $sql;
    $sql_query=mysqli_query($db, $sql);
    erro_sql("�tens Relat�rio", $db, "");
    echo mysqli_error($db);
    if(mysqli_num_rows($sql_query)>0){
      $file.="\n\nMedicamento: ".$codigo_material . ";";
      $file.=$nome_med;
      $file.="\n\nData;Hist�rico;Lote;Qtde;Saldo Anterior;Qtde Entrada;Qtde Sa�da;Qtde Perda;Estoque;Login\n";
      while($linha=mysqli_fetch_array($sql_query)){
        $movto_geral=$linha['movto_geral_id_movto_geral'];
        if($movto_geral_aux!=$movto_geral){
            $file.="\n" . $linha['data_movto'] . ";" . $linha['historico'] . ";" . $linha['lote'] . ";";
            $file.=(int)$linha['qtde'].";".(int)$linha['saldo_anterior'] .";" . (int)$linha['qtde_entrada'] . ";";
            $file.=(int)$linha['qtde_saida'] . ";" . (int)$linha['qtde_perda'] . ";" . (int)$linha['saldo_atual']. ";" . $linha['login'];
        }
        
        else{
           $file.="\n"." ". ";" ." ". ";" . $linha['lote']. ";" .(int)$linha['qtde']. ";" . " ". ";" ." ". ";" . " ". ";" . " ". ";" . " ". ";" . " ";
        }
        $movto_geral_aux=$linha["movto_geral_id_movto_geral"];
        
      }
    }
    else{
      $file .= "\n\nMedicamento: ".$codigo_material . ";";
      $file .= $nome_med;
      $file .= "\n\nData;Hist�rico;Lote;Qtde;Saldo Anterior;Qtde Entrada;Qtde Sa�da;Qtde Perda;Estoque\n";
      $file .= "N�o Foram Encontrados Dados para a Pesquisa!";
    }
    $filename = "Relat�rio_Movimentacoes_Medicamento.csv";
    header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");
    print $file;
  }
?>
