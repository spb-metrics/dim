<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//////////
//HEADER//
//////////

//error_reporting(E_ALL);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  $ARQ_CONFIG="../config/config.inc.php";
  if(!file_exists($ARQ_CONFIG)){
    exit("Não existe arquivo de configuração: $ARQ_CONFIG");
  }
  require $ARQ_CONFIG;

    function soma_data($pData, $pDias)//formato BR
    {
      if(ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $pData, $vetData))
      {
        $fAno = $vetData[1];
        $fMes = $vetData[2];
        $fDia = $vetData[3];

        for($x = 1; $x <= $pDias; $x++){
          if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12){
            $fMaxDia = 31;
          }
          elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11){
            $fMaxDia = 30;
          }
          else{
            if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0){
              $fMaxDia = 29;
            }
            elseif($fMes == 2){
              $fMaxDia = 28;
            }
          }
          $fDia++;
          if($fDia > $fMaxDia){
            if($fMes == 12){
              $fAno++;
              $fMes = 1;
              $fDia = 1;
            }
            else{
              $fMes++;
              $fDia = 1;
            }
          }
        }
        if(strlen($fDia) == 1)
          $fDia = "0" . $fDia;
        if(strlen($fMes) == 1)
          $fMes = "0" . $fMes;
        return "$fAno-$fMes-$fDia";
      }
    }

   $descricao=$_GET["descricao"];
   $descricao =rawurldecode  ($descricao);

   $id_movto=$_GET["id_movto"];
   $id_unidade=$_GET["id_unidade"];
   $aplicacao=$_GET["aplicacao"];
   if($aplicacao=="entrada" || $aplicacao=="lote" || $aplicacao=="remanejamento" || $aplicacao=="restringir"){
     
	  $sql="select *
           from material
           where status_2 = 'A' and trim('$descricao') = replace(descricao, '+', ' ')
           order by descricao";
	 
	 
	 /*$sql="select *
           from material
           where status_2 = 'A' and descricao='". trim($descricao) . "'
           order by descricao";*/
		   
		//   echo $sql;
   }
   if($aplicacao=="mestoque"){
     $sql="select * from tipo_movto where id_tipo_movto='$id_movto'";
     $result=mysqli_query($db, $sql);
     $movimento=mysqli_fetch_object($result);
     $operacao=$movimento->operacao;
     $flg_bloqueado=$movimento->flg_movto_bloqueado;
     $flg_vencido=$movimento->flg_movto_vencido;
     if ($operacao == "entrada")
     {
      /* $sql = "select distinct mat.codigo_material, mat.descricao,
                      udm.unidade, mat.id_material
               from material mat
                    inner join unidade_material udm
                    on mat.unidade_material_id_unidade_material = udm.id_unidade_material
               where mat.status_2='A' and mat.descricao='" . trim($descricao) . "'
               order by mat.descricao";*/
			   
			    $sql = "select distinct mat.codigo_material, mat.descricao,
                      udm.unidade, mat.id_material
               from material mat
                    inner join unidade_material udm
                    on mat.unidade_material_id_unidade_material = udm.id_unidade_material
               where mat.status_2='A' and  trim('$descricao')= replace(descricao, '+', ' ')
               order by mat.descricao";
			   
			   
			   //echo $sql;
			   
     }
     else if (($operacao=="saida") or ($operacao=="perda"))
     {
      /* $sql = "select distinct mat.codigo_material, mat.descricao, udm.unidade, mat.id_material
               from material mat
                    inner join unidade_material udm
                    on mat.unidade_material_id_unidade_material = udm.id_unidade_material
                    inner join estoque est
                    on mat.id_material = est.material_id_material
               where mat.status_2='A' and mat.descricao='" . trim($descricao) . "'
                     and est.unidade_id_unidade = '$id_unidade'
                     and est.quantidade > 0";*/
					 
					 $sql = "select distinct mat.codigo_material, mat.descricao, udm.unidade, mat.id_material
               from material mat
                    inner join unidade_material udm
                    on mat.unidade_material_id_unidade_material = udm.id_unidade_material
                    inner join estoque est
                    on mat.id_material = est.material_id_material
               where mat.status_2='A' and trim('$descricao')= replace(descricao, '+', ' ')
                     and est.unidade_id_unidade = '$id_unidade'
                     and est.quantidade > 0";
					 
					 

       if (strtoupper($flg_bloqueado) == "S")
       {
         if (strtoupper($flg_vencido) == "S")
           $sql = $sql." and (est.flg_bloqueado = 'S'";
         else
           $sql = $sql." and est.flg_bloqueado = 'S'";
       }
       else if (strtoupper($flg_bloqueado) == "N")
       {
         $sql = $sql." and est.flg_bloqueado <> 'S'";
       }

       $sql_param = "select dias_vencto_material from parametro";
       $res_param = mysqli_query($db, $sql_param);
       if(mysqli_num_rows($res_param) > 0)
       {
         $info_param = mysqli_fetch_object($res_param);
         $vencimento = soma_data(date("Y-m-d"), $info_param->dias_vencto_material) ;
       }
       if (strtoupper($flg_vencido) == "S")
       {
         if (strtoupper($flg_bloqueado) == "S")
           $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento')";
         else
           $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento'";
       }
       else if (strtoupper($flg_vencido) == "N")
       {
         $vencimento = date("Y-m-d");
         $sql = $sql." and SUBSTRING(est.validade,1,10) > '$vencimento'";
       }
       $sql = $sql." order by mat.descricao";
     }
   }
    $results=mysqli_query($db, $sql);
    if(mysqli_num_rows($results)>0){
      $med_info=mysqli_fetch_object($results);
      $msg=$med_info->id_material;
      if($aplicacao=="remanejamento" || $aplicacao=="restringir"){
        $msg.="|$med_info->codigo_material";
      }
    }
    else{
      $msg="";
    }
    echo $msg;
?>
