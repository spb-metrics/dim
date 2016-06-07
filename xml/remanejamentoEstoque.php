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
   session_start();
//error_reporting(E_ALL);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $unidade=$_GET["unidade"];
  $valor=split("[|]", substr($_GET[itens], 0, (strlen($_GET[itens])-1)));
  for($i=0; $i<count($valor); $i++){
    $valores[]=split("[,]", substr($valor[$i], 0, (strlen($valor[$i])-1)));
  }


  //print_r($_SESSION['ITENS']);

  //echo"ajax remanejamentoEstoque";  exit;
//$ids_materiais=array();


 $_SESSION['ITENS'] = $valores;
 //echo"ajax remanejamentoEstoque"; print_r($valores);exit;
  $msg="";
  for($i=0; $i<count($valores); $i++){
      //$ids_materiais[$i][]=      $valores[$i][0];
    $sql="select * from estoque
          where fabricante_id_fabricante='" . $valores[$i][1] . "'
          and material_id_material='" . $valores[$i][0] . "' and lote='" . $valores[$i][2] . "'
          and quantidade>='" . $valores[$i][4] . "' and unidade_id_unidade='$unidade'";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Estoque", $db, "");
    if(mysqli_num_rows($res)<=0){
      $sql="select *
            from material
            where id_material='" . $valores[$i][0] . "' and status_2='A'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Material", $db, "");
      if(mysqli_num_rows($res)>0){
        $mat_descr=mysqli_fetch_object($res);
      }
      $sql="select *
            from fabricante
            where id_fabricante='" . $valores[$i][1] . "' and status_2='A'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Fabricante", $db, "");
      if(mysqli_num_rows($res)>0){
        $fabr_descr=mysqli_fetch_object($res);
      }//, fabricante e lote
      $msg.= $mat_descr->descricao." - ".$valores[$i][2]." - ".$fabr_descr->descricao."\n";
    }
  }
  if($msg==""){
    $msg="estoque";
  }
  echo $msg;
?>

