<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

   header("Cache-Control: no-cache, must-revalidate");
   header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
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

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU
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
