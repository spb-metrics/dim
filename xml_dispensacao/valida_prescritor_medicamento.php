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

  $material   = $_GET[material];
  $tipo_prescritor = $_GET[tipo_prescritor];
  
  
 // echo "material: ".$material;
  //echo ""$tipo_prescritor;

  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU
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
