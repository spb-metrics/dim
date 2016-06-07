<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  $nome=$_GET["nome"];
  $mae=$_GET["mae"];
  $dt_nasc=$_GET["data_nasc"];
  $id_paciente=$_GET["id_paciente"];
  
  $nome_sem_esp   = ereg_replace(' ', '', $nome);
  $mae_sem_esp    = ereg_replace(' ', '', $mae);
  $nome_mae_nasc = $nome_sem_esp.$mae_sem_esp;

  $msg="";
  $dt_nasc=substr($dt_nasc, -4) . "/" . substr($dt_nasc, 3, 2) . "/" . substr($dt_nasc, 0, 2);
  $sql="select id_paciente
        from paciente
        where
        data_nasc='".trim($dt_nasc) ."'
        and status_2='A'
        and nome_mae_nasc = '".strtoupper(trim($nome_mae_nasc)) ."'";

  $result=mysqli_query($db, $sql);
  erro_sql("Select Paciente", $db, "");
  //existe paciente com nome, nome_mae, data_nasc
  if(mysqli_num_rows($result)>0){
    $paciente_info=mysqli_fetch_object($result);
    $id_paciente_pac=$paciente_info->id_paciente;
    //id de cadastro nao eh igual id atual
    if($id_paciente_pac!=$id_paciente){
      $msg="Nao � poss�vel alterar paciente, pois j� existe paciente cadastrado\n";
      $msg.="Nome do paciente - Nome da m�e - Data de nascimento\n";
      $msg.="$nome - $mae - $_GET[data_nasc]";
    }
  }
  echo $msg;
?>
