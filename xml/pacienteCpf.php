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

  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $cpf=$_GET["cpf"];
  $id_paciente=$_GET["id_paciente"];
  
  if ($id_paciente==""){

    $nome=$_GET["nome"];
    $mae=$_GET["mae"];
    $dt_nasc=$_GET["data_nasc"];


    $dt_nasc=substr($dt_nasc, -4) . "/" . substr($dt_nasc, 3, 2) . "/" . substr($dt_nasc, 0, 2);

    $nome_sem_esp   = ereg_replace(' ', '', $nome);
    $mae_sem_esp    = ereg_replace(' ', '', $mae);

    $sql="select id_paciente
        from paciente
        where nome_mae_nasc like '".strtoupper(trim($nome_sem_esp)) ."%' and
        nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."' and
        data_nasc='".trim($dt_nasc) ."' and
        status_2='A'";
    $result=mysqli_query($db, $sql);
    erro_sql("Select Paciente", $db, "");
    //existe paciente com nome, nome_mae, data_nasc
    if(mysqli_num_rows($result)>0){
       $paciente_info=mysqli_fetch_object($result);
       $id_paciente=$paciente_info->id_paciente;
    }
  }

  $msg="CPF";
  if($cpf!=""){
    $sql="select id_paciente
          from paciente
          where
          id_paciente!='".trim($id_paciente) ."'
          and cpf='$cpf'";

    $result=mysqli_query($db, $sql);
    erro_sql("Select Paciente", $db, "");
    //existe paciente com cpf
    if(mysqli_num_rows($result)>0){
      $msg="NPF";
    }
  }
  echo $msg;
?>
