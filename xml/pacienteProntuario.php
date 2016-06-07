<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
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
