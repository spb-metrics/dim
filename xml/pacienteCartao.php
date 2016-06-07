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

  $id_paciente=$_GET["id_paciente"];
  $lista_cartao=$_GET["itens"];

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
  
  $msg="";
  $msg_aux="SAV";
  $lista_cartao=substr($lista_cartao, 0, strlen($lista_cartao)-1);
  if($lista_cartao!=""){
    $lista_cart=explode(",", $lista_cartao);
    for($i=0; $i<count($lista_cart); $i++){
      $sql="select c.cartao_sus, p.nome, p.nome_mae, p.data_nasc, p.id_paciente
            from cartao_sus as c, paciente as p
            where c.paciente_id_paciente=p.id_paciente and c.cartao_sus='$lista_cart[$i]'
                  and p.status_2='A'";
//echo $sql;
      $result=mysqli_query($db, $sql);
      erro_sql("Select Cartao_SUS/Paciente", $db, "");
      //existe paciente e cartao sus cadastrado
      if(mysqli_num_rows($result)>0){
        $cartao_info=mysqli_fetch_object($result);
        $id_pac=$cartao_info->id_paciente;
        $cartao_pac=$cartao_info->cartao_sus;
        $nome_pac=$cartao_info->nome;
        $nome_mae_pac=$cartao_info->nome_mae;
        $data_nasc_pac=$cartao_info->data_nasc;
        $data_nasc_pac=substr($data_nasc_pac, -2) . "/" . substr($data_nasc_pac, 5, 2) . "/" . substr($data_nasc_pac, 0, 4);
        //id diferente do informado
        if($id_pac!=$id_paciente){
          $msg.=$cartao_pac . " - " . $nome_pac . " - " . $nome_mae_pac . " - ";
          $msg.=$data_nasc_pac . "\n";
        }
      }
    }
  }
  if($msg==""){
    echo $msg_aux;
  }
  else{
    echo $msg;
  }
?>
