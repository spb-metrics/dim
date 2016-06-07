<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);

  $descricao  = $_GET[descricao];
  $substituir = "\+";
  $descricao = ereg_replace("~", $substituir, $descricao);

  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU

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
