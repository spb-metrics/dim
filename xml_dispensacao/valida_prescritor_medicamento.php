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

  $material   = $_GET[material];
  $tipo_prescritor = $_GET[tipo_prescritor];
  
  
 // echo "material: ".$material;
  //echo ""$tipo_prescritor;

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU
  if ($tipo_prescritor!='')
  {
   $sql_prescritor = "select tipo_prescritor_id_tipo_prescritor
                      from
                             material_prescritor
                      where
                             tipo_prescritor_id_tipo_prescritor = '$tipo_prescritor'";
   $res=mysqli_query($db, $sql_prescritor);
   //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
   $linhas_res=mysqli_num_rows($res);
  
   if ($linhas_res==0)
   {
     echo "sim_prescritor";
   }
   else
   {
    $sql_prescritor_material = "select tipo_prescritor_id_tipo_prescritor
                                from
                                       material_prescritor
                                where
                                       tipo_prescritor_id_tipo_prescritor = '$tipo_prescritor'
                                       and material_id_material = '$material'";
    $resultado=mysqli_query($db, $sql_prescritor_material);

    //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
    $linhas_resultado=mysqli_num_rows($resultado);

    if($linhas_resultado==0)
    {
     echo "nao_prescritor";
    }
    else
    {
     echo "sim_prescritor";
    }
   }
  }
  else
  {
   echo "nao_prescritor";
  }
?>
