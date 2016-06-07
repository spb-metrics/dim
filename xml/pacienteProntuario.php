<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

 /* $id_paciente="";
  $lista_cartao='555555555555555,';
 */
  $id_paciente=$_GET["id_paciente"];
  $lista_prontuario=$_GET["itens"];


  $nome=$_GET["nome"];
  $mae=$_GET["mae"];
  $dt_nasc=$_GET["data_nasc"];
  
  $nome_sem_esp   = ereg_replace(' ', '', $nome);
  $mae_sem_esp    = ereg_replace(' ', '', $mae);
  $dt_nasc=substr($dt_nasc, -4) . "/" . substr($dt_nasc, 3, 2) . "/" . substr($dt_nasc, 0, 2);
  
  $sql="select id_paciente
        from paciente
        where nome_mae_nasc like '".strtoupper(trim($nome_sem_esp)) ."%' and
        nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."' and
        data_nasc='".trim($dt_nasc) ."' and
        status_2='A'";
  //echo $sql."<p>";
  $result=mysqli_query($db, $sql);
  erro_sql("Select Paciente", $db, "");
  //existe paciente com nome, nome_mae, data_nasc
  if(mysqli_num_rows($result)>0){
       $paciente_info=mysqli_fetch_object($result);
       $id_paciente=$paciente_info->id_paciente;
  }
  $msg1="";
  $msg="NAO";
  $msg_aux="OKP";
  $grava="S";
  $lista_prontuario=substr($lista_prontuario, 0, strlen($lista_prontuario)-1);
  
  if(($id_paciente!="")&&($lista_prontuario!=""))
  {
  $lista_pront=explode(",", $lista_prontuario);
  for($i=0; $i<count($lista_pront); $i++){
    $sql="select num_prontuario, paciente_id_paciente, unidade_id_unidade
          from prontuario
          where paciente_id_paciente = $id_paciente
          and num_prontuario='$lista_pront[$i]'";
    $result=mysqli_query($db, $sql);
    erro_sql("Select Prontuario/Paciente", $db, "");
    //existe paciente e cartao sus cadastrado
    
    if(mysqli_num_rows($result) > 0){
      $prontuario_info=mysqli_fetch_object($result);
      $prontuario_pac=$prontuario_info->num_prontuario;
      $msg1.=$prontuario_pac. "\n";
      $grava="N";
    }
   }
  }
  
  if(($id_paciente=="") ||($grava=="S"))
  {
       echo $msg_aux;
  }
  else //if($grava=="N"){
    echo $msg." ".$msg1;
  //}
?>
