<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

   header("Cache-Control: no-cache, must-revalidate");
   header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);

  $materiais   = $_GET[materiais];
  $tipo_prescritor = $_GET[tipo_prescritor];

  $mat_nao_aut ='';
  $desc_nao='';
  
  $vet_linha = split('[|]',$materiais);

   for($cont=0;$cont<count($vet_linha);$cont++)
   {
        $vet_dados[$cont] = split('[,]',$vet_linha[$cont]);
   }

  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU
 if ($tipo_prescritor!='')
 {
  for ($cont=count($vet_dados)-1;$cont>=0;$cont--)
  {
  $material=$vet_dados[$cont][0];
  
   $sql_prescritor = "select tipo_prescritor_id_tipo_prescritor, material_id_material
                      from
                             material_prescritor
                      where
                             tipo_prescritor_id_tipo_prescritor = '$tipo_prescritor'";
   $res=mysqli_query($db, $sql_prescritor);
   //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
   $linhas_res=mysqli_num_rows($res);
  
   if ($linhas_res==0)
   {
     //echo "s_sim_prescritor";
     $flag="s_sim_prescritor";
   }
   else
   {
    $sql_prescritor_material = "select mp.tipo_prescritor_id_tipo_prescritor,
                                       mp.material_id_material,
                                       m.descricao
                                from
                                       material_prescritor mp,
                                       material m
                                where
                                       mp.tipo_prescritor_id_tipo_prescritor = '$tipo_prescritor'
                                       and mp.material_id_material = '$material'
                                       and m.id_material = mp.material_id_material";
    $resultado=mysqli_query($db, $sql_prescritor_material);

    //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS

    $linhas_resultado=mysqli_num_rows($resultado);

    if($linhas_resultado==0)
    {
      $mat_nao_aut=$material.",".$mat_nao_aut;
     // echo "nao_prescritor";
    }
    else
    {
      $flag="s_sim_prescritor";
    }
    }
   }//for
  }
  else
  {
   $flag="nao_prescritor";
  }
  
  if($mat_nao_aut!='')
  {
      $mat_nao = substr($mat_nao_aut,0,-1);
      $sql ="select descricao
             from
                    material
             where
                  id_material in($mat_nao)";
      $reslt=mysqli_query($db, $sql);

      while ($desc_mat = mysqli_fetch_object($reslt))
      {
         $desc_nao = $desc_mat->descricao." ".$desc_nao;
      }
      $flag="mat_nao_prescritor=".$desc_nao;
  }
  else
    {
      $flag="s_sim_prescritor";
    }
  echo $flag;
?>
