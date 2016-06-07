<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $data=date("Y-m-d H:m:s");
  $nome=$_GET[nome];
  $mae=$_GET[mae];
  $sexo=$_GET[sexo];
  $tipo_logradouro=$_GET[tipo_logradouro];
  $dt_nasc=$_GET[data_nasc];
  $telefone= $_GET[telefone];
  $logradouro=$_GET[logradouro];
  $numero=$_GET[numero];
  $complemento=$_GET[complemento];
  $bairro=$_GET[bairro];
  $id_cidade_rec=$_GET[id_cidade_receita];
  $unidade_ref=$_GET[unidade_referida];
  $id_status_pac=$_GET[id_status_paciente];
  $id_paciente=$_GET[id_paciente];
  $cpf=$_GET[cpf];
  
  $msg="";
  $data_aux= $dt_nasc;
  $dt_nasc=substr($dt_nasc, -4) . "/" . substr($dt_nasc, 3, 2) . "/" . substr($dt_nasc, 0, 2);
  $nome_sem_esp   = ereg_replace(' ', '', $nome);
  $mae_sem_esp    = ereg_replace(' ', '', $mae);
  
  $nome_mae_nasc = $nome_sem_esp.$mae_sem_esp;
  if ($cpf!='')
  {
  $sql="update paciente
            set nome='" . strtoupper(trim($nome)) . "',
                data_nasc='$dt_nasc',
                nome_mae='" . strtoupper(trim($mae)) . "',
                sexo='$sexo',
                telefone='" . strtoupper(trim($telefone)) . "',
                tipo_logradouro='$tipo_logradouro',
                nome_logradouro='" . strtoupper(trim($logradouro)) . "',
                numero='" . strtoupper(trim($numero)) . "',
                complemento='" . strtoupper(trim($complemento)) . "',
                bairro='" . strtoupper(trim($bairro)) . "',
                cpf='" . trim($cpf) . "',
                cidade_id_cidade='$id_cidade_rec',
                unidade_referida='$unidade_ref',
                data_alt='$data',
                id_status_paciente='$id_status_pac',
                usua_alt='$_SESSION[id_usuario_sistema]',
                nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."',
                nome_mae_nasc= '".strtoupper(trim($nome_mae_nasc)) ."'
            where id_paciente='$id_paciente'";
  }
  else
  {
  $sql="update paciente
            set nome='" . strtoupper(trim($nome)) . "',
                data_nasc='$dt_nasc',
                nome_mae='" . strtoupper(trim($mae)) . "',
                sexo='$sexo',
                telefone='" . strtoupper(trim($telefone)) . "',
                tipo_logradouro='$tipo_logradouro',
                nome_logradouro='" . strtoupper(trim($logradouro)) . "',
                numero='" . strtoupper(trim($numero)) . "',
                complemento='" . strtoupper(trim($complemento)) . "',
                bairro='" . strtoupper(trim($bairro)) . "',
                cpf=Null,
                cidade_id_cidade='$id_cidade_rec',
                unidade_referida='$unidade_ref',
                data_alt='$data',
                id_status_paciente='$id_status_pac',
                usua_alt='$_SESSION[id_usuario_sistema]',
                nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."',
                nome_mae_nasc= '".strtoupper(trim($nome_mae_nasc)) ."'
            where id_paciente='$id_paciente'";
  }
      $result=mysqli_query($db, $sql);
      erro_sql("Update/Paciente", $db, "");

      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      

     if($_GET[lista_atencao]!=""){
      $lista_atencao=$_GET[lista_atencao];
      $lista_atencao=substr($lista_atencao, 0, strlen($lista_atencao)-2);

      $lista_aten=explode(",", $lista_atencao);
     }

    //obtem os cartoes sus
    if($_GET[lista_cartao]!=""){
      $lista_cartao=$_GET[lista_cartao];
      $lista_cartao=substr($lista_cartao, 0, strlen($lista_cartao)-2);

      $lista_cart=explode(",", $lista_cartao);
    }

    //obtem os prontuarios
    if($_GET[lista_prontuario]!=""){
     $lista_prontuario=$_GET[lista_prontuario];
     $lista_prontuario=substr($lista_prontuario, 0, strlen($lista_prontuario)-1);
     $lista_aux=explode(",", $lista_prontuario);
      for($i=0; $i<count($lista_aux); $i++){
          $lista_pront[$i]=explode("|", $lista_aux[$i]);
      }
    }
    
     $sql="delete
            from cartao_sus
            where paciente_id_paciente='$id_paciente'";
      $result=mysqli_query($db, $sql);
      erro_sql("Delete Cartao_SUS", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      //inserindo os cartoes sus informados
      for($i=0; $i<count($lista_cart); $i++){
        $sql="insert into cartao_sus
              (paciente_id_paciente, cartao_sus, tipo_cartao, data_incl, usua_incl)
              values('$id_paciente', '$lista_cart[$i]', '', '$data',
                     '$_SESSION[id_usuario_sistema]')";
        mysqli_query($db, $sql);
        erro_sql("Insert Cartao_SUS", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
      }

      //apagando os prontuarios referentes ao paciente
      $sql="delete
            from prontuario
            where paciente_id_paciente='$id_paciente'";
      $result=mysqli_query($db, $sql);
      erro_sql("Delete Prontuario", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }

      //inserindo os prontuarios informados
      for($i=0; $i<count($lista_pront); $i++){
        $prontuario= $lista_pront[$i][0];
        $unidade= $lista_pront[$i][1];

        $sql="insert into prontuario
              (paciente_id_paciente, unidade_id_unidade, num_prontuario)
              values($id_paciente, $unidade, '$prontuario')";
        mysqli_query($db, $sql);
        erro_sql("Insert Prontuario", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
      }

      //deletando as atencoes continuadas referentes ao paciente
      $sql="delete from atencao_continuada_paciente
            where id_paciente='$id_paciente'";
      mysqli_query($db, $sql);
      erro_sql("Insert Atenção Continuada", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      //inserindo as atencoes continuadas informadas
      for($i=0; $i<count($lista_aten); $i++){
        $sql="insert into atencao_continuada_paciente
              (id_paciente, id_atencao_continuada)
              values ('$id_paciente', '$lista_aten[$i]')";
        mysqli_query($db, $sql);
        erro_sql("Insert Atenção Continuada", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
      }
     if ($atualizacao=="")
     {
       $atualizacao="ok";
       mysqli_commit($db);
     }
     echo $atualizacao;
?>
