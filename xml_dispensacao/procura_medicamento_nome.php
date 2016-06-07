<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $descricao  = $_GET[descricao];
  $substituir = "\+";
  $descricao = ereg_replace("~", $substituir, $descricao);

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU

  $sql = "select m.id_material, m.unidade_material_id_unidade_material,
                 m.grupo_id_grupo, m.subgrupo_id_subgrupo,
                 m.tipo_material_id_tipo_material, m.familia_id_familia,
                 m.lista_especial_id_lista_especial, m.codigo_material,
                 m.descricao, m.flg_dispensavel, m.dias_limite_disp,
                 u.unidade
          from
                 material m,
                 unidade_material u
          where
                 m.descricao = '$descricao'
                 and m.flg_dispensavel = 'S'
                 and m.status_2 = 'A'
                 and m.unidade_material_id_unidade_material = u.id_unidade_material";
  $ver_medicamento = mysqli_query($db, $sql);
  $medicamento=mysqli_fetch_object($ver_medicamento);

  //echo $sql;

  if(mysqli_num_rows($ver_medicamento)>0)
  {
   echo 'med'.$medicamento->id_material.'|'.$medicamento->unidade;
  }
  else
  {
   echo 'mednao';
  }

 ?>
