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
  $cpf=$_GET[cpf];
  $logradouro=$_GET[logradouro];
  $numero=$_GET[numero];
  $complemento=$_GET[complemento];
  $bairro=$_GET[bairro];
  $id_cidade_rec=$_GET[id_cidade_receita];
  $unidade_ref=$_GET[unidade_referida];
  $id_status_pac=$_GET[id_status_paciente];
  $id_usuario_sistema=$_GET[id_usuario_sistema];
  $id_unidade_sistema=$_GET[id_unidade_sistema];


  $nome_sem_esp   = ereg_replace(' ', '', $nome);
  $mae_sem_esp    = ereg_replace(' ', '', $mae);
  $nome_mae_nasc  = $nome_sem_esp.$mae_sem_esp;

  $msg="";
  $dt_nasc=substr($dt_nasc, -4) . "/" . substr($dt_nasc, 3, 2) . "/" . substr($dt_nasc, 0, 2);
  
  
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

      $lista_pront=explode(",", $lista_prontuario);
    }

   //verificando se o paciente ja existe cadastrado
   //alterado 24/10/2008
      $sql="select id_paciente
            from paciente
            where data_nasc='$dt_nasc'
            and status_2='A'
            and nome_mae_nasc = '" . strtoupper($nome_mae_nasc) . "'";
            
      $result=mysqli_query($db, $sql);
      $numrow = mysqli_num_rows($result);
      erro_sql("Select Paciente", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      
      
  //Paciente nao existe cadastrado
  if($numrow==0){
    if (trim($cpf)!='')
    {

    $sql="insert into paciente
         (id_status_paciente, unidade_cadastro, unidade_referida,
         cidade_id_cidade, nome, tipo_logradouro, nome_logradouro, numero,
         complemento, bairro, nome_mae, sexo, data_nasc, status_2, cpf, data_incl,
         usua_incl, telefone, nome_mae_nasc, nome_mae_sem_espaco)
         values ('$id_status_pac',
                      '$id_unidade_sistema', '$unidade_ref',
                      '$id_cidade_rec',
                      '".strtoupper(trim($nome))."',
                      '$tipo_logradouro',
                      '".strtoupper(trim($logradouro))."',
                      '" . strtoupper(trim($numero)) . "',
                      '".strtoupper(trim($complemento)) ."',
                      '".strtoupper(trim($bairro)) ."',
                      '".strtoupper(trim($mae)) ."',
                      '$sexo', '$dt_nasc', 'A',
                      " . trim($cpf) . ",
                      '$data', '$id_usuario_sistema',
                      '".strtoupper(trim($telefone))."',
                      '".strtoupper(trim($nome_mae_nasc)) ."',
                      '".strtoupper(trim($mae_sem_esp))."')";
       }
       else
       {
       $sql="insert into paciente
         (id_status_paciente, unidade_cadastro, unidade_referida,
         cidade_id_cidade, nome, tipo_logradouro, nome_logradouro, numero,
         complemento, bairro, nome_mae, sexo, data_nasc, status_2, cpf, data_incl,
         usua_incl, telefone, nome_mae_nasc, nome_mae_sem_espaco)
         values ('$id_status_pac',
                      '$id_unidade_sistema', '$unidade_ref',
                      '$id_cidade_rec',
                      '".strtoupper(trim($nome))."',
                      '$tipo_logradouro',
                      '".strtoupper(trim($logradouro))."',
                      '" . strtoupper(trim($numero)) . "',
                      '".strtoupper(trim($complemento)) ."',
                      '".strtoupper(trim($bairro)) ."',
                      '".strtoupper(trim($mae)) ."',
                      '$sexo', '$dt_nasc', 'A',
                      Null,
                      '$data', '$id_usuario_sistema',
                      '".strtoupper(trim($telefone))."',
                      '".strtoupper(trim($nome_mae_nasc)) ."',
                      '".strtoupper(trim($mae_sem_esp))."')";

       }
        mysqli_query($db, $sql);
        erro_sql("Insert Paciente - 1", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
        $sql="select max(id_paciente) as id_paciente
              from paciente
              where status_2='A'";
        $result=mysqli_query($db, $sql);
        erro_sql("Select Id Paciente", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
        if(mysqli_num_rows($result)>0){
          $paciente_info=mysqli_fetch_object($result);
          $id_paciente_pac=$paciente_info->id_paciente;
          for($i=0; $i<count($lista_pront); $i++){
            $sql="insert into prontuario
                  (paciente_id_paciente, num_prontuario, unidade_id_unidade)
                  values('$id_paciente_pac', '$lista_pront[$i]', '$_SESSION[id_unidade_sistema]')";
           //       echo $sql;
            mysqli_query($db, $sql);
            erro_sql("Insert Prontuario", $db, "");
            if(mysqli_errno($db)!=0){
              $atualizacao="erro";
            }
          }
          for($i=0; $i<count($lista_cart); $i++){
            $sql="insert into cartao_sus
                  (paciente_id_paciente, cartao_sus, tipo_cartao, data_incl, usua_incl)
                  values('$id_paciente_pac', '$lista_cart[$i]', '', '$data',
                         '$id_usuario_sistema')";
           //              echo $sql."<br>";
            mysqli_query($db, $sql);
            erro_sql("Insert Cartao_SUS", $db, "");
            if(mysqli_errno($db)!=0){
              $atualizacao="erro";
            }
          }
          for($i=0; $i<count($lista_aten); $i++){
            $sql="insert into atencao_continuada_paciente
                  (id_paciente, id_atencao_continuada)
                  values ('$id_paciente_pac', '$lista_aten[$i]')";
            mysqli_query($db, $sql);
            erro_sql("Insert Atenção Continuada", $db, "");
            if(mysqli_errno($db)!=0){
              $atualizacao="erro";
            }
          }
        }

      }
      
      else{
        $nome_sem_esp   = ereg_replace(' ', '', $nome);
        $mae_sem_esp    = ereg_replace(' ', '', $mae);
        $nome_mae_nasc = $nome_sem_esp.$mae_sem_esp;

        $paciente_info=mysqli_fetch_object($result);
        $id_paciente_pac=$paciente_info->id_paciente;

        if ($cpf!='')
        {
        $sql="update paciente
              set nome='" . strtoupper(trim($nome)) . "',
                data_nasc='$dt_nasc',
                nome_mae='" . strtoupper(trim($mae)) . "',
                sexo='$sexo',
                telefone='" . strtoupper(trim($telefone)) . "',
                cpf='$cpf',
                tipo_logradouro='$tipo_logradouro',
                nome_logradouro='" . strtoupper(trim($logradouro)) . "',
                numero='" . strtoupper(trim($numero)) . "',
                complemento='" . strtoupper(trim($complemento)) . "',
                bairro='" . strtoupper(trim($bairro)) . "',
                cidade_id_cidade='$id_cidade_rec',
                unidade_referida='$unidade_ref',
                id_status_paciente='$id_status_pac',
                data_alt='$data', usua_alt='$id_usuario_sistema',
                nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."',
                nome_mae_nasc='".strtoupper(trim($nome_mae_nasc))."'
            where id_paciente='$id_paciente_pac'";
        }
        else
        {
        $sql="update paciente
              set nome='" . strtoupper(trim($nome)) . "',
                data_nasc='$dt_nasc',
                nome_mae='" . strtoupper(trim($mae)) . "',
                sexo='$sexo',
                telefone='" . strtoupper(trim($telefone)) . "',
                cpf=NULL,
                tipo_logradouro='$tipo_logradouro',
                nome_logradouro='" . strtoupper(trim($logradouro)) . "',
                numero='" . strtoupper(trim($numero)) . "',
                complemento='" . strtoupper(trim($complemento)) . "',
                bairro='" . strtoupper(trim($bairro)) . "',
                cidade_id_cidade='$id_cidade_rec',
                unidade_referida='$unidade_ref',
                id_status_paciente='$id_status_pac',
                data_alt='$data', usua_alt='$id_usuario_sistema',
                nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."',
                nome_mae_nasc='".strtoupper(trim($nome_mae_nasc))."'
            where id_paciente='$id_paciente_pac'";

        }
       $result=mysqli_query($db, $sql);
       erro_sql("Update/Paciente", $db, "");
       if(mysqli_errno($db)!=0){
         $atualizacao="erro";
       }
      
       for($i=0; $i<count($lista_pront); $i++){
        $sql="select pt.num_prontuario
              from prontuario as pt
              where pt.paciente_id_paciente=$id_paciente_pac and pt.num_prontuario='$lista_pront[$i]'
                    and pt.unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $result=mysqli_query($db, $sql);
        erro_sql("Select Prontuario", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
        //nao existe paciente e prontuario cadastrado
        if(mysqli_num_rows($result)==0){
          $sql="insert into prontuario
                (paciente_id_paciente, num_prontuario, unidade_id_unidade)
                values('$id_paciente_pac', '$lista_pront[$i]', '$_SESSION[id_unidade_sistema]')";
          mysqli_query($db, $sql);
          erro_sql("Insert Prontuario", $db, "");
          if(mysqli_errno($db)!=0){
            $atualizacao="erro";
          }
        }
      }
      for($i=0; $i<count($lista_cart); $i++){
        $sql="select c.cartao_sus, p.nome, p.nome_mae, p.data_nasc
              from cartao_sus as c, paciente as p
              where c.paciente_id_paciente=p.id_paciente and c.cartao_sus='$lista_cart[$i]'
                    and p.status_2='A'";
        $result=mysqli_query($db, $sql);
        erro_sql("Select Cartao_SUS/Paciente", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
        //nao existe paciente e cartao sus cadastrado
        if(mysqli_num_rows($result)==0){
          $cartao_novo.=$lista_cart[$i] . ",";
        }
      }
      if($cartao_novo!=""){
          $cartoes=split("[,]", $cartao_novo);
          for($i=0; $i<count($cartoes)-1; $i++){
            $sql="insert into cartao_sus
                  (paciente_id_paciente, cartao_sus, tipo_cartao, data_incl, usua_incl)
                  values('$id_paciente_pac', '$cartoes[$i]', '', '$data',
                         '$id_usuario_sistema')";

            mysqli_query($db, $sql);
            erro_sql("Insert Apenas Cartao_SUS", $db, "");
            if(mysqli_errno($db)!=0){
              $atualizacao="erro";
            }
          }
      }
      for($i=0; $i<count($lista_aten); $i++){
          $sql="select *
                from atencao_continuada_paciente
                where id_paciente='$id_paciente_pac' and
                      id_atencao_continuada='$lista_aten[$i]'";
          $result=mysqli_query($db, $sql);
          erro_sql("Select Existe Atenção", $db, "");
          if(mysqli_errno($db)!=0){
            $atualizacao="erro";
          }
          if(mysqli_num_rows($result)==0){
            $sql="insert into atencao_continuada_paciente
                  (id_paciente, id_atencao_continuada)
                  values ('$id_paciente_pac', '$lista_aten[$i]')";
            mysqli_query($db, $sql);
            erro_sql("Insert Apenas Atenção Continuada", $db, "");
            if(mysqli_errno($db)!=0){
              $atualizacao="erro";
            }
          }
      }
    }

     if ($atualizacao=="")
     {
       $atualizacao="ok!".$id_paciente_pac;
       mysqli_commit($db);
     }
     echo $atualizacao;

?>
