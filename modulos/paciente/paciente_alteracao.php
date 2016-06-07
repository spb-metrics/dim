<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  session_start();

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
    require DIR."/header.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }
    
    $sql = "select limite_menor_dt_nasc from parametro";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Parametro", $db, "");
    $dados_parametro = mysqli_fetch_object($res);
    $data_parametro = substr($dados_parametro->limite_menor_dt_nasc,-2)."/".substr($dados_parametro->limite_menor_dt_nasc,5,2)."/".substr($dados_parametro->limite_menor_dt_nasc,0,4);

    //obtem as atencoes continuadas
    if($_POST[lista_atencao]!=""){
      $lista_atencao=$_POST[lista_atencao];
      $lista_atencao=substr($lista_atencao, 0, strlen($lista_atencao)-2);

      $lista_aten=explode(",", $lista_atencao);
     }

    //obtem os prontuarios

    if($_POST[lista_prontuario]!=""){

     $lista_prontuario=$_POST[lista_prontuario];

     $lista_prontuario=substr($lista_prontuario, 0, strlen($lista_prontuario)-1);
     $lista_aux=explode(",", $lista_prontuario);

      for($i=0; $i<count($lista_aux); $i++){
          $lista_pront[$i]=explode("|", $lista_aux[$i]);
      }
    }
    
    //obtem os cartoes sus
    if($_POST[lista_cartao]!=""){
      $lista_cartao=$_POST[lista_cartao];
      $lista_cartao=substr($lista_cartao, 0, strlen($lista_cartao)-2);

      $lista_cart=explode(",", $lista_cartao);
    }

    //alterando informacoes do paciente
    if($_POST[flag]=="t"){
      $nome_sem_esp   = ereg_replace(' ', '', $_POST[nome]);
      $mae_sem_esp    = ereg_replace(' ', '', $_POST[mae]);
      $nome_mae_nasc = $nome_sem_esp.$mae_sem_esp;
      
      $data=date("Y-m-d H:m:s");
      $dt_nasc=$_POST[data_nasc];
      $dt_nasc=substr($dt_nasc, -4) . "-" . substr($dt_nasc, 3, 2) . "-" . substr($dt_nasc, 0, 2);
      if ($_POST[cpf]!= '')
      {
      $sql="update paciente
            set nome='" . strtoupper(trim($_POST[nome])) . "',
                data_nasc='$dt_nasc',
                nome_mae='" . strtoupper(trim($_POST[mae])) . "',
                sexo='$_POST[sexo]',
                telefone='" . strtoupper(trim($_POST[telefone])) . "',
                cpf='" . trim($_POST[cpf]) . "',
                tipo_logradouro='$_POST[tipo_logradouro]',
                nome_logradouro='" . strtoupper(trim($_POST[logradouro])) . "',
                numero='" . strtoupper(trim($_POST[numero])) . "',
                complemento='" . strtoupper(trim($_POST[complemento])) . "',
                bairro='" . strtoupper(trim($_POST[bairro])) . "',
                cidade_id_cidade='$_POST[id_cidade_receita]',
                unidade_referida='$_POST[unidade_referida]',
                id_status_paciente='$_POST[id_status_paciente]',
                data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]',
                nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."',
                nome_mae_nasc='".strtoupper(trim($nome_mae_nasc))."'
            where id_paciente='$_POST[id_paciente_aux]'";
     }
     else
     {
      $sql="update paciente
            set nome='" . strtoupper(trim($_POST[nome])) . "',
                data_nasc='$dt_nasc',
                nome_mae='" . strtoupper(trim($_POST[mae])) . "',
                sexo='$_POST[sexo]',
                telefone='" . strtoupper(trim($_POST[telefone])) . "',
                cpf=Null,
                tipo_logradouro='$_POST[tipo_logradouro]',
                nome_logradouro='" . strtoupper(trim($_POST[logradouro])) . "',
                numero='" . strtoupper(trim($_POST[numero])) . "',
                complemento='" . strtoupper(trim($_POST[complemento])) . "',
                bairro='" . strtoupper(trim($_POST[bairro])) . "',
                cidade_id_cidade='$_POST[id_cidade_receita]',
                unidade_referida='$_POST[unidade_referida]',
                id_status_paciente='$_POST[id_status_paciente]',
                data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]',
                nome_mae_sem_espaco='".strtoupper(trim($mae_sem_esp)) ."',
                nome_mae_nasc='".strtoupper(trim($nome_mae_nasc))."'
            where id_paciente='$_POST[id_paciente_aux]'";

     }
      $result=mysqli_query($db, $sql);
      erro_sql("Update/Paciente", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }

      //apagando os prontuarios referentes ao paciente
      $sql="delete
            from prontuario
            where paciente_id_paciente='$_POST[id_paciente_aux]'";
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
              values($_POST[id_paciente_aux], $unidade, '$prontuario')";

        mysqli_query($db, $sql);
        erro_sql("Insert Prontuario", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
      }
      
      
      
      //apagando os cartoes sus referentes ao paciente
      $sql="delete
            from cartao_sus
            where paciente_id_paciente='$_POST[id_paciente_aux]'";
      $result=mysqli_query($db, $sql);
      erro_sql("Delete Cartao_SUS", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      //inserindo os cartoes sus informados
      for($i=0; $i<count($lista_cart); $i++){
        $sql="insert into cartao_sus
              (paciente_id_paciente, cartao_sus, tipo_cartao, data_incl, usua_incl)
              values($_POST[id_paciente_aux], $lista_cart[$i], '', '$data',
                     $_SESSION[id_usuario_sistema])";

        mysqli_query($db, $sql);
        erro_sql("Insert Cartao_SUS", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
      }
      //deletando as atencoes continuadas referentes ao paciente
      $sql="delete from atencao_continuada_paciente
            where id_paciente='$_POST[id_paciente_aux]'";
      mysqli_query($db, $sql);
      erro_sql("Insert Atenção Continuada", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      //inserindo as atencoes continuadas informadas
      for($i=0; $i<count($lista_aten); $i++){
        $sql="insert into atencao_continuada_paciente
              (id_paciente, id_atencao_continuada)
              values ($_POST[id_paciente_aux], $lista_aten[$i])";
        mysqli_query($db, $sql);
        erro_sql("Insert Atenção Continuada", $db, "");
        if(mysqli_errno($db)!=0){
          $atualizacao="erro";
        }
      }

      if($atualizacao==""){
        mysqli_commit($db);
          echo "<script>
                  window.location='" . URL . "/modulos/paciente/paciente_inicial.php?a=t&id_paciente=$id_paciente';
                </script>";
        exit();
      }
      else{
        mysqli_rollback($db);
          echo "<script>
                  window.location='" . URL . "/modulos/paciente/paciente_inicial.php?a=f&id_paciente=$id_paciente';
                </script>";
        exit();
      }
    }

    //mostrando informacoes do paciente
    if($_GET[id_paciente]!=""){
       $sql_select = "select id_paciente,
                      id_status_paciente, unidade_cadastro,
                      unidade_referida, cidade_id_cidade,
                      nome, tipo_logradouro, nome_logradouro,
                      numero, complemento, bairro, nome_mae,
                      sexo, data_nasc, telefone, cpf
                      from paciente
                      where id_paciente='$_GET[id_paciente]' and status_2='A'";
       $res=mysqli_query($db, $sql_select);
       erro_sql("Select Paciente Escolhido", $db, "");
       $paciente=mysqli_fetch_object($res);

       $id_paciente=$paciente->id_paciente;
       $id_status_paciente=$paciente->id_status_paciente;
       $unidade_cadastro=$paciente->unidade_cadastro;
       $unidade_referida=$paciente->unidade_referida;
       $cidade_id_cidade=$paciente->cidade_id_cidade;
       $nome=$paciente->nome;
       $tipo_logradouro=$paciente->tipo_logradouro;
       $logradouro=$paciente->nome_logradouro;
       $numero=$paciente->numero;
       $complemento=$paciente->complemento;
       $bairro=$paciente->bairro;
       $mae=$paciente->nome_mae;
       $sexo= $paciente->sexo;
       $data_nasc=substr($paciente->data_nasc,-2)."/".substr($paciente->data_nasc,5,2)."/".substr($paciente->data_nasc,0,4);
       $telefone=$paciente->telefone;
       $cpf=$paciente->cpf;
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
    <script language="javascript">
    <!--
    function chkCPF() {
      var campo=document.form_alteracao.cpf;
      var valor=campo.value;

      var tam = valor.length;
      for (var i=0; i<tam; i++)
      {
        valor = valor.replace(".", "");
        valor = valor.replace("/", "");
        valor = valor.replace("-", "");
      }
  
     campo.value= valor;
     strcpf = valor;
     str_aux = "";

     for (i = 0; i <= strcpf.length - 1; i++)
       if ((strcpf.charAt(i)).match(/\d/))
          str_aux += strcpf.charAt(i);
       else if (!(strcpf.charAt(i)).match(/[\.\-]/)) {
          alert ("O campo CPF apresenta caracteres inválidos !!!");
          //campo.select();
          campofoco = campo;
           setTimeout("campofoco.focus()",250);
           campofoco.select();
          return false;
       }

     if ((str_aux == "00000000000") || (str_aux == "11111111111") ||
         (str_aux == "22222222222") || (str_aux == "33333333333") ||
         (str_aux == "44444444444") || (str_aux == "55555555555") ||
         (str_aux == "66666666666") || (str_aux == "77777777777") ||
         (str_aux == "88888888888") || (str_aux == "99999999999") ) {
           alert ("O CPF digitado é inválido !!!");
           //campo.select();
          campofoco = campo;
           setTimeout("campofoco.focus()",250);
           campofoco.select();
          return false;
     }

     soma1 = soma2 = 0;
     for (i = 0; i <= 8; i++) {
       soma1 += str_aux.charAt(i) * (10-i);
       soma2 += str_aux.charAt(i) * (11-i);
     }

     d1 = ((soma1 * 10) % 11) % 10;
     d2 = (((soma2 + (d1 * 2)) * 10) % 11) % 10;

     if ((d1 != str_aux.charAt(9)) || (d2 != str_aux.charAt(10))) {
       alert ("O CPF digitado é inválido !!!");
       //campo.select();
          campofoco = campo;
           setTimeout("campofoco.focus()",250);
           campofoco.select();
          return false;
     }
     return true;
    }
    
    function verificarCPF(){
      var y=document.form_alteracao;
      var cpf=y.cpf.value;
      var id_paciente=y.id_paciente.value;
      var url = "../../xml/pacienteCpf.php?id_paciente=" + id_paciente + "&cpf=" + cpf;
      requisicaoHTTP("GET", url, true);
    }

    function trataDados(){
      var x = document.form_alteracao;
	  var info = ajax.responseText;  // obtém a resposta como string
      var texto=info.substr(0, 3);
      if(texto!="Nao" && texto!="" && texto!="SAV" && texto!="CPF" && texto!="NPF"){
         var msg="Os cartões SUS informados estão cadastrados para outros pacientes\n";
         msg+="Nº do cartão SUS - Nome do paciente - Nome da mãe - Data de nascimento\n";
         msg+=info;
         window.alert(msg);
      }
      if(texto=="Nao"){
        window.alert(info);
      }
      if(texto==""){
        verificarCartao();
      }
      if(texto=="SAV"){
        verificarCPF();
      }
      if(texto=="CPF"){
        x.flag.value='t';
        x.submit();
      }
      if(texto=="NPF"){
        window.alert("CPF já existe cadastrado!");
        x.cpf.focus();
        x.cpf.select();
      }
    }
    
    function retirarEspaco(){
      var x=document.form_alteracao;
      var nome=x.nome.value;
      var mae=x.mae.value;
      while(nome.match("  ")){
        nome=nome.replace("  ", " ");
      }
      if(nome.charAt(0)==" "){
        nome=nome.substr(1, nome.length);
      }
      if(nome.charAt(nome.length-1)==" "){
        nome=nome.substr(0, nome.length-1);
      }
      x.nome.value=nome;
      while(mae.match("  ")){
        mae=mae.replace("  ", " ");
      }
      if(mae.charAt(0)==" "){
        mae=mae.substr(1, mae.length);
      }
      if(mae.charAt(mae.length-1)==" "){
        mae=mae.substr(0, mae.length-1);
      }
      x.mae.value=mae;
    }

    function verificarPaciente(){
      retirarEspaco();
      var y=document.form_alteracao;
      var nome=y.nome.value;
      var mae=y.mae.value;
      var data_nasc=y.data_nasc.value;
      var id_paciente=y.id_paciente_aux.value;
      var url = "../../xml/pacientePaciente.php?nome=" + nome + "&mae=" + mae + "&data_nasc=" + data_nasc + "&id_paciente=" + id_paciente;
      requisicaoHTTP("GET", url, true);
    }
    
    function verificarCartao(){
      var x=document.getElementById("lista_cartao");
      var itens=x.value;
      var y=document.form_alteracao;
      var id_paciente=y.id_paciente.value;
      var url = "../../xml/pacienteCartao.php?id_paciente=" + id_paciente + "&itens=" + itens;
      requisicaoHTTP("GET", url, true);
    }

    function voltar_pagina(){
      var x=document.form_alteracao;
      var id_paciente=x.id_paciente.value;
      window.location="paciente_inicial.php?aplicacao=<?php echo $_SESSION[cod_aplicacao];?>&id_paciente=" + id_paciente;
    }

    function validar_campos(){
      var x=document.form_alteracao;
      if(x.nome.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.nome.focus();
        return false;
      }
      if(x.data_nasc.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.data_nasc.focus();
        return false;
      }
      else
      {
        if (verificaDataMenorteste(x.data_nasc,x.data_parametro)==false)
        {
           alert( "Data de Nascimento Inválida" );
           x.data_nasc.focus();
           x.data_nasc.select();
           return false;
        }
        if (verificaDataMaiorteste (x.data_nasc)==false)
        {
         alert("Data deve ser menor ou igual a data de hoje");
         x.data_nasc.focus();
         x.data_nasc.select();
         return false;
        }
      }

      if (x.cpf.value!="")
      {
       if (chkCPF() == false)
       {
         alert ('x');
         x.cpf.focus();
         x.cpf.select();
         return false;
       }
      }

      if(x.mae.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.mae.focus();
        return false;
      }
      if(x.sexo.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.sexo.focus();
        return false;
      }
      if(x.tipo_logradouro.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.tipo_logradouro.focus();
        return false;
      }
      if(x.logradouro.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.logradouro.focus();
        return false;
      }
      if(x.numero.value==""){
        alert ("Favor preencher os campos obrigatórios!");
        x.numero.focus();
        return false;
      }
      if(x.bairro.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.bairro.focus();
        return false;
      }
      if(x.id_cidade_receita.value==""){
        alert("Favor preencher o campo cidade!");
        return false;
      }
      if(x.cidade_receita.value==""){
        alert("Favor preencher os campos obrigatórios!");
        x.cidade_receita.focus();
        return false;
      }
      if(x.unidade_referida.disabled==false && x.unidade_referida.value==""){
        alert ("Favor preencher os campos obrigatórios!");
        x.unidade_referida.focus();
        return false;
      }
      if(x.id_status_paciente.value==""){
        alert ("Favor preencher os campos obrigatórios!");
        x.id_status_paciente.focus();
        return false;
      }
      if(x.id_status_paciente.value=="2"){
        alert ("Não é possível bloquear o paciente!");
        x.id_status_paciente.focus();
        return false;
      }
      return true;
    }

    var cont=0;
    var vetCod=new Array();

    function insereLinhas(){
      var x=document.form_alteracao;
      var xText=x.atencao.options[x.atencao.selectedIndex].text;
      var xValue=x.atencao.options[x.atencao.selectedIndex].value;
      if(x.atencao.selectedIndex>0){
        var achou = false;
        var itens=document.getElementById("tabela");
        var total_linhas=document.getElementById("tabela").rows.length;
        for(i=1; i<total_linhas; i++){
          if(itens.rows[i].cells[0].innerHTML==xValue){
            achou=true;
          }
        }
        if(achou==false){
          var pos=document.getElementById("tabela").rows.length;
          var tab=document.getElementById("tabela").insertRow(pos);
          cont=parseInt(x.cont_atencao.value);
          tab.id="linha"+cont;
          tab.className = "campo_tabela";
          var cell1=tab.insertCell(0);
          cell1.align="left";
          cell1.innerHTML=xValue;
          var cell2=tab.insertCell(1);
          cell2.align="left";
          cell2.innerHTML=xText;
          var cell3=tab.insertCell(2);
          cell3.align="center";
          var Site="<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
          var url="JavaScript:removeLinhas('linha"+cont+"')";
          cell3.innerHTML=Site.link(url);
          vetCod[cont]=xValue;
          cont++;
          x.cont_atencao.value=cont;
        }
        else{
          window.alert("Atenção já inserida!");
        }
      }
      else{
        window.alert("Selecione uma Atenção!");
      }
      x.atencao.selectedIndex=0;
      x.atencao.focus();
    }

    function removeLinhas(lnh){
      document.getElementById("tabela").deleteRow(document.getElementById(lnh).rowIndex);
      var x=document.form_alteracao;
      x.atencao.focus();
    }

    function salvar_dados(){
      var x=document.form_alteracao;
      if (validar_campos()==true){
        var itens=document.getElementById('tabela');
        var total=document.getElementById("tabela").rows.length;
        var lista="";
        var lista_pront="";
        var lista_unidade="";
        var campo="";
        
        for(var i=1; i<total; i++){
          if(i==1){
            lista=itens.rows[i].cells[0].innerHTML + ", ";
          }
          if(i>1){
            lista+=itens.rows[i].cells[0].innerHTML + ", ";
          }
        }
        document.getElementById('lista_atencao').value=lista;

        itens=document.getElementById('tb_cartao');
        total=document.getElementById("tb_cartao").rows.length;
        lista="";
        for(var i=1; i<total; i++){
          if(i==1){
            lista=itens.rows[i].cells[1].innerHTML + ", ";
          }
          if(i>1){
            lista+=itens.rows[i].cells[1].innerHTML + ", ";
          }
        }
        document.getElementById('lista_cartao').value=lista;

        itens=document.getElementById('tb_prontuario');
        total=document.getElementById("tb_prontuario").rows.length;

        for(var i=1; i<total; i++){
          var string=itens.rows[i].cells[1].innerHTML;
          var vetor=string.split(" ");
          for(var j=0; j<vetor.length; j++){
            var propriedade=vetor[j].split("=");
            if(propriedade[0]=="value"){
              var valor=propriedade[1].split('"');
              lista_unidade = lista_unidade+"|"+ valor;
            }

          }
        }

        var unidade = lista_unidade.split("|");
        var j =1;
        
        for(var i=1; i<total; i++){
           var teste = unidade[j];
           teste = teste.replace(/,/g, "");
           //alert (teste);
          if(i==1){
            lista_pront=itens.rows[i].cells[0].innerHTML + "|" + teste+ ",";
          }
          if(i>1){
            lista_pront+=itens.rows[i].cells[0].innerHTML + "|" + teste+ ",";
          }
          j++;
        }

        document.getElementById('lista_prontuario').value=lista_pront;
        
        //alert (lista_pront);
        
        verificarPaciente();
      }
    }

    function validarCartao(){
      var x=document.form_alteracao;
      if(x.cartao.value.length!=15){
        window.alert("Cartão SUS deve ser 15 dígitos!");
        x.cartao.focus();
        return false;
      }
      return true;
    }

    var contador=0;
    var vetor_cartao=new Array();

    function inserirLinha(){
      var x=document.form_alteracao;
      if(validarCartao()){
        var achou=false;
        var itens=document.getElementById("tb_cartao");
        var total=document.getElementById("tb_cartao").rows.length;
        for(var i=1; i<total; i++){
          if(itens.rows[i].cells[1].innerHTML==x.cartao.value){
            achou=true;
          }
        }
        if(achou==false){
          var pos=total;
          var tab=document.getElementById("tb_cartao").insertRow(pos);
          contador=parseInt(x.contador_cartao.value);
          tab.id="linha" + contador;
          tab.className="campo_tabela";
          var cell1=tab.insertCell(0);
          cell1.align="left";
          cell1.innerHTML=contador+1;
          var cell2=tab.insertCell(1);
          cell2.align="left";
          cell2.innerHTML=x.cartao.value;
          var cell3=tab.insertCell(2);
          cell3.align="center";
          var Site="<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
          var url="JavaScript:removerLinha('linha" + contador + "')";
          cell3.innerHTML=Site.link(url);
          vetor_cartao[contador]=x.cartao.value;
          contador++;
          x.contador_cartao.value=contador;
        }
        else{
          window.alert("Cartão SUS já inserido!");
        }
        x.cartao.value="";
        x.cartao.focus();
      }
    }

    function removerLinha(lnh){
      document.getElementById("tb_cartao").deleteRow(document.getElementById(lnh).rowIndex);
      var x=document.form_alteracao;
      x.cartao.focus();
    }


    function validarProntuario(){
      var x=document.form_alteracao;
      if(x.prontuario.value.length==0){
        window.alert("Nº de prontuário não informado!");
        x.prontuario.select();
        return false;
      }
      return true;
    }
    
    
    var cont_pront=0;
    var vetor_prontuario=new Array();
    
    function inserirLinhaPront(){
      var x=document.form_alteracao;
      var campo="";
      if(validarProntuario()){
        var achou=false;
        var itens=document.getElementById("tb_prontuario");
        var total=document.getElementById("tb_prontuario").rows.length;

        for(var i=1; i<total; i++){
          var prontuario=itens.rows[i].cells[0].innerHTML;
          var string=itens.rows[i].cells[1].innerHTML;
          var vetor=string.split(" ");
          for(var j=0; j<vetor.length; j++){
            var propriedade=vetor[j].split("=");
            if(propriedade[0]=="value"){
              if (propriedade[1].indexOf('"')==0)
              {
               var unidade = propriedade[1].replace('"', '');
               unidade = unidade.replace('"', '');
               //alert (unidade);
              }
              else
              {
               var unidade = propriedade[1];
               //alert (unidade);
              }

              if(prontuario==x.prontuario.value && unidade==x.id_logada.value){
                achou=true;
              }
            }
          }
        }

        if(achou==false){
          var pos=total;
          var tab=document.getElementById("tb_prontuario").insertRow(pos);
          cont_pront=parseInt(x.contador_prontuario.value)+1;
          tab.id="linha_prontuario" + cont_pront;
          tab.className="campo_tabela";
          var cell1=tab.insertCell(0);
          cell1.align="left";
          cell1.innerHTML=x.prontuario.value;

          var cell2=tab.insertCell(1);
          cell2.align="left";
          var aux= document.form_alteracao.id_logada.value;
          var nome= 'lista_logada'+cont_pront;
          cell2.innerHTML="<input type='hidden' name='lista_logada' id='" +nome+ "' value='"+aux+"'>";

          var cell3=tab.insertCell(2);
          cell3.align="left";
          cell3.innerHTML=x.unidade_logada.value;

          var cell4=tab.insertCell(3);
          cell4.align="center";
          var Site="<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
          var url="JavaScript:removerLinhaPront('linha_prontuario" + cont_pront + "')";
          cell4.innerHTML=Site.link(url);
          vetor_prontuario[cont_pront]=x.prontuario.value;
          cont_pront++;
          x.contador_prontuario.value=cont_pront;
        }
        else{
          window.alert("Prontuário já inserido!");
        }
        x.prontuario.value="";
        x.prontuario.focus();
      }
    }

    function removerLinhaPront(lnh){
      document.getElementById("tb_prontuario").deleteRow(document.getElementById(lnh).rowIndex);
      var x=document.form_alteracao;
      x.prontuario.focus();
    }

function verificaDataMaior(campo)
{
   if (campo.value!='')
   {
        var data_hoje = new Date();
        var data_param = campo.value;
        var dia = data_hoje.getDate();
        var mes = data_hoje.getMonth()+1;
        var ano = data_hoje.getFullYear();

        if (mes<10)
        {
           mes="0"+mes;
        }
        if (dia<10)
        {
           dia="0"+dia;
        }
        data_hoje = dia+"/"+mes+"/"+ano;
        if (parseInt( data_param.split( "/" )[2].toString() + data_param.split( "/" )[1].toString() + data_param.split( "/" )[0].toString() ) > parseInt( data_hoje.split( "/" )[2].toString() + data_hoje.split( "/" )[1].toString() + data_hoje.split( "/" )[0].toString() ) )
        {
           alert("Data deve ser menor ou igual a data de hoje");
           globalvar = campo;
           setTimeout("globalvar.focus()",250);
           globalvar.select();
        }
   }
}

function verificaDataMaiorteste(campo)
{
   if (campo.value!='')
   {
        var data_hoje = new Date();
        var data_param = campo.value;
        var dia = data_hoje.getDate();
        var mes = data_hoje.getMonth()+1;
        var ano = data_hoje.getFullYear();

        if (mes<10)
        {
           mes="0"+mes;
        }
        if (dia<10)
        {
           dia="0"+dia;
        }
        data_hoje = dia+"/"+mes+"/"+ano;
        if (parseInt( data_param.split( "/" )[2].toString() + data_param.split( "/" )[1].toString() + data_param.split( "/" )[0].toString() ) > parseInt( data_hoje.split( "/" )[2].toString() + data_hoje.split( "/" )[1].toString() + data_hoje.split( "/" )[0].toString() ) )
        {
           return false;
        }
   }
}

// Verifica se data2 é maior que data1
 function verificaDataMenor(campo1, campo2)
 {
  if (campo1.value!='')
   {
          var data1 = campo1.value;
          var data2 = campo2.value;
          //alert(data2);
          if ( parseInt( data2.split( "/" )[2].toString() + data2.split( "/" )[1].toString() + data2.split( "/" )[0].toString() ) > parseInt( data1.split( "/" )[2].toString() + data1.split( "/" )[1].toString() + data1.split( "/" )[0].toString() ) )
          {
           alert( "Data de Nascimento Inválida" );
           globalvar = campo1;
           setTimeout("globalvar.focus()",250);
           globalvar.select();
          }
   }
  }

// Verifica se data2 é maior que data1
 function verificaDataMenorteste(campo1, campo2)
 {
  if (campo1.value!='')
   {
          var data1 = campo1.value;
          var data2 = campo2.value;
          //alert(data2);
          if ( parseInt( data2.split( "/" )[2].toString() + data2.split( "/" )[1].toString() + data2.split( "/" )[0].toString() ) > parseInt( data1.split( "/" )[2].toString() + data1.split( "/" )[1].toString() + data1.split( "/" )[0].toString() ) )
          {
           return false;
          }
   }
  }

    //-->
    </script>

    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='4'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_alteracao" action="./paciente_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <input type="hidden" name="id_logada" id="id_logada" value="<?php echo $_SESSION[id_unidade_sistema];?>">
                      <input type="hidden" name="unidade_logada" id="unidade_logada" value="<?php echo $_SESSION[nome_unidade_sistema];?>" size="30">
                      <td colspan="6" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" name="nome" id="nome" size="60"  maxlength="70" value="<?php echo $nome;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="18%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Dt. Nascimento
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <input type="hidden" id="data_parametro" name="data_parametro" size="10"  value="<?php echo $data_parametro;?>">
                        <input type="text" id="data_nasc" name="data_nasc" size="10"  maxlength="10" onKeyPress="return mascara_data(event,this)" onblur="if(verificaData(this)==true){verificaDataMaior(this); verificaDataMenor(this,document.form_alteracao.data_parametro)};" value="<?php echo $data_nasc;?>">

                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome Mãe
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" name="mae" id="mae" size="60" maxlength="70" value="<?php echo $mae;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="18%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Sexo
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <select name="sexo" size="1" style="width:85px;">
                          <option value="">Selecione</option>
                          <option value="F" <?php if($sexo=="F"){echo "selected";}?>>Feminino</option>
                          <option value="M" <?php if($sexo=="M"){echo "selected";}?>>Masculino</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        CPF
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                        <input type="text" name="cpf" id="cpf" size="30" maxlength="11" onkeypress="return isNumberKey(event);" onblur="return chkCPF();" value="<?php echo $cpf;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Telefone
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <input type="text" name="telefone" id="telefone" size="10" maxlength="12" value="<?php echo $telefone;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Tipo Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <select name="tipo_logradouro" size="1" style="width:100px;">
                          <option value="">Selecione</option>
                          <option value="Avenida" <?php if($tipo_logradouro=="Avenida"){echo "selected";}?>>Avenida</option>
                          <option value="Beco" <?php if($tipo_logradouro=="Beco"){echo "selected";}?>>Beco</option>
                          <option value="Caminho" <?php if($tipo_logradouro=="Caminho"){echo "selected";}?>>Caminho</option>
                          <option value="Estrada" <?php if($tipo_logradouro=="Estrada"){echo "selected";}?>>Estrada</option>
                          <option value="Ladeira" <?php if($tipo_logradouro=="Ladeira"){echo "selected";}?>>Ladeira</option>
                          <option value="Largo" <?php if($tipo_logradouro=="Largo"){echo "selected";}?>>Largo</option>
                          <option value="Lote" <?php if($tipo_logradouro=="Lote"){echo "selected";}?>>Lote</option>
                          <option value="Outro" <?php if($tipo_logradouro=="Outro"){echo "selected";}?>>Outro</option>
                          <option value="Praça" <?php if($tipo_logradouro=="Praça"){echo "selected";}?>>Praça</option>
                          <option value="Quadra" <?php if($tipo_logradouro=="Quadra"){echo "selected";}?>>Quadra</option>
                          <option value="Rodovia" <?php if($tipo_logradouro=="Rodovia"){echo "selected";}?>>Rodovia</option>
                          <option value="Rua" <?php if($tipo_logradouro=="Rua"){echo "selected";}?>>Rua</option>
                          <option value="Travessa" <?php if($tipo_logradouro=="Travessa"){echo "selected";}?>>Travessa</option>
                          <option value="Via" <?php if($tipo_logradouro=="Via"){echo "selected";}?>>Via</option>
                          <option value="Vila" <?php if($tipo_logradouro=="Vila"){echo "selected";}?>>Vila</option>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" name="logradouro" id="logradouro" size="65" maxlength="50" value="<?php echo $logradouro;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Número
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <input type="text" name="numero" id="numero" size="12" maxlength="7" value="<?php echo $numero;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Complemento
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <input type="text" name="complemento" id="complemento" size="65" maxlength="15" value="<?php echo $complemento;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Bairro
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                        <input type="text" name="bairro" id="bairro" size="45" maxlength="30" value="<?php echo $bairro;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Cidade
                      </td>
                     <?
                      $sql="select id_cidade, concat(cid.nome,'/',est.uf) as nome
                            from cidade cid, estado est
                            where cid.estado_id_estado = est.id_estado
                                  and cid.id_cidade = '$cidade_id_cidade'";
                     $res=mysqli_query($db, $sql);
                     erro_sql("Select Cidade", $db, "");
                     if(mysqli_num_rows($res)>0){
                       $dados_cidade_receita=mysqli_fetch_object($res);
                       $cidade_receita=$dados_cidade_receita->nome;
                       $id_cidade_receita=$dados_cidade_receita->id_cidade;
                     }
                     ?>
                     <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                       <input type="text" size="45" name="cidade_receita" id="cidade_receita" value="<?php echo $cidade_receita;?>" disabled>
                       <a href="JavaScript:abrir_janela('<?php echo URL."/modulos/dispensar/pesquisa_cidade.php?opcao=a"?>');"><img src="<?php echo URL;?>/imagens/i_002.gif" border="0" title="Pesquisar"></a>
                       <input type="hidden" name="id_cidade_receita" id="id_cidade_receita" value="<?php echo $id_cidade_receita;?>">
                     </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CS Cadastro
                      </td>
                      <?php
                        $sql="select nome
                              from unidade
                              where id_unidade='$unidade_cadastro' and status_2='A'";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select CS Cadastro", $db, "");
                        if(mysqli_num_rows($res)>0){
                          $cs_cadastro=mysqli_fetch_object($res);
                        }
                      ?>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="2">
                        <input type="text" name="unidade_cadastro" id="unidade_cadastro" value="<?php echo $cs_cadastro->nome;?>" size="30" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CS Unidade
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <?php
                          $habilitado="";
                          if($unidade_referida=="0" || $unidade_referida==""){
                            $habilitado="disabled";
                          }
                        ?>
                        <select name="unidade_referida" id="unidade_referida" size="1" style="width:200px;" <?php echo $habilitado;?>>
                          <option value="">Selecione</option>
                          <?php
                            $sql="select id_unidade, nome
                                  from unidade
                                  where status_2='A' and flg_nivel_superior=0
                                  order by nome";
                            $unidade=mysqli_query($db, $sql);
                            erro_sql("Select CS Unidade", $db, "");
                            while($listaunidade=mysqli_fetch_object($unidade)){
                              $selecionado="";
                              if($listaunidade->id_unidade==$unidade_referida){
                                $selecionado="selected";
                              }
                          ?>
                              <option value="<?php echo $listaunidade->id_unidade;?>" <?php echo $selecionado;?>> <?php echo $listaunidade->nome;?></option>
                          <?
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Situação
                      </td>
                      <td class="campo_tabela" colspan="6" valign="middle" width="80%">
                        <select name="id_status_paciente" size="1" style="width:205px;">
                          <option value="">Selecione</option>
                          <?php
                            $sql = "select id_status_paciente, descricao
                                     from status_paciente
                                     where status_2='A'
                                     order by descricao";
                            $status = mysqli_query($db, $sql);
                            erro_sql("Select Situação", $db, "");
                            while($listastatus=mysqli_fetch_object($status)){
                              $selecionado="";
                              if(strtoupper($listastatus->id_status_paciente)==$id_status_paciente){
                                $selecionado="selected";
                              }
                          ?>
                                  <option value="<?php echo $listastatus->id_status_paciente;?>" <?php echo $selecionado;?>> <?php echo $listastatus->descricao;?></option>
                          <?
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    
                    <tr>
                      <td colspan="6">
                        <table class="titulo_tabela" border="0" width="100%" cellpadding="0" cellspacing="1">
                          <tr align="center">
                            <td width="100%">Prontuário</td>
                            <td width="10%">
                              <a href="javascript:showFrame('show_prontuario');"><img src="<?php echo URL. '/imagens/b_edit.gif'; ?>" border="0" title="Exibir Informações de Cartão SUS"></a>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6">
                        <div id="show_prontuario" style="display:none">
                          <table border="0" width="100%" cellpadding="0" cellspacing="1">
                            <tr>
                              <td class="descricao_campo_tabela" width="20%">
                               <img src="<?php echo URL; ?>/imagens/obrigat_1.gif" border="0">
                                Nº prontuário
                              </td>
                              <td class="campo_tabela" width="15%">
                                <input type="text" name="prontuario" id="prontuario" size="15" maxlength="15">
                              </td>
                              <td class="descricao_campo_tabela" width="100%">
                                <input type="button" name="bt_prontuario" id="bt_prontuario" value=" OK " onclick="inserirLinhaPront();">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3">
                                <table width="100%" border='0' cellpadding="0" cellspacing="1" id="tb_prontuario">
                                  <tr class="coluna_tabela">
                                    <td width="15%" align="center"><b>Nro</b></td>
                                    <td width="0%" align="center"></td>
                                    <td width="75%" align="left"><b>Unidade</b></td>
                                    <td width="5%" align="center">&nbsp;</td>
                                  </tr>
                                  <?php
                                    $sql="select p.num_prontuario, u.nome, u.id_unidade
                                          from prontuario as p, unidade as u
                                          where p.paciente_id_paciente='$id_paciente'
                                          and p.unidade_id_unidade = u.id_unidade
                                          order by p.num_prontuario";
                                    $prontuario=mysqli_query($db, $sql);
                                    erro_sql("Select Prontuário", $db, "");
                                    $cont_pront=0;
                                    while($listaprontuario=mysqli_fetch_object($prontuario)){
                                      $cont_pront++;
                                      $nome_linha="linha_prontuario".$cont_pront;
                                  ?>
                                      <tr class="campo_tabela" id="<?php echo $nome_linha;?>">
                                        <td align="left"><?php echo $listaprontuario->num_prontuario;?></td>


                                          <? if($_SESSION[id_unidade_sistema] == $listaprontuario->id_unidade)

                                          {?>
                                             <td><input type='hidden' name='lista_logada' id='lista_logada<?php echo $cont_pront;?>' value='<?php echo $listaprontuario->id_unidade;?>'></td>
                                             <td align="left"><?php echo $listaprontuario->nome;?></td>
                                             <td align="center">
                                             <a href="JavaScript:removerLinhaPront('<?php echo $nome_linha;?>');"><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Remover Registro"></a>
                                          <?} else
                                              {?>
                                                <td><input type='hidden' name='lista_logada' id='lista_logada<?php echo $cont_pront;?>' value='<?php echo $listaprontuario->id_unidade;?>'></td>
                                                <td align="left" colspan="2"><?php echo $listaprontuario->nome;?></td>
                                            <?}?>
                                        </td>
							          </tr>
                                  <?php
                                   }
                                  ?>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    
			        <tr>
                      <td colspan="6">
                        <table class="titulo_tabela" border="0" width="100%" cellpadding="0" cellspacing="1">
                          <tr align="center">
                            <td width="100%">Cartões SUS</td>
                            <td width="10%">
                              <a href="javascript:showFrame('show_cartao');"><img src="<?php echo URL. '/imagens/b_edit.gif'; ?>" border="0" title="Exibir Informações de Cartão SUS"></a>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="6">
                        <div id="show_cartao" style="display:none">
                          <table border="0" width="100%" cellpadding="0" cellspacing="1">
                            <tr>
                              <td class="descricao_campo_tabela" width="20%">
                               <img src="<?php echo URL; ?>/imagens/obrigat_1.gif" border="0">
                                Cartão SUS
                              </td>
                              <td class="campo_tabela" width="15%">
                                <input type="text" name="cartao" id="cartao" size="15" maxlength="15" onkeypress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                              </td>
                              <td class="descricao_campo_tabela" width="100%">
                                <input type="button" name="bt_cartao" id="bt_cartao" value=" OK " onclick="inserirLinha();">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3">
                                <table width="100%" border='0' cellpadding="0" cellspacing="1" id="tb_cartao">
                                  <tr class="coluna_tabela">
                                    <td width="15%" align="center"><b>Nro</b></td>
                                    <td width="75%" align="left"><b>Cartão SUS</b></td>
                                    <td width="5%" align="center">&nbsp;</td>
                                  </tr>
                                  <?php
                                    $sql="select cartao_sus
                                          from cartao_sus
                                          where paciente_id_paciente='$id_paciente'
                                          order by cartao_sus";
                                    $cartao=mysqli_query($db, $sql);
                                    erro_sql("Select Cartão SUS", $db, "");
                                    $contador=0;
                                    while($listacartao=mysqli_fetch_object($cartao)){
                                      $contador++;
                                      $nome_linha="linha_cartao" . $contador;
                                  ?>
                                      <tr class="campo_tabela" id="<?php echo $nome_linha;?>">
                                        <td align="left"><?php echo $contador;?></td>
					  				    <td align="left"><?php echo $listacartao->cartao_sus;?></td>
                                        <td align="center">
                                          <a href="JavaScript:removerLinha('<?php echo $nome_linha;?>');"><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Remover Registro"></a>
                                        </td>
							          </tr>
                                  <?php

                                    }
                                  ?>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
         			<TR>
	                  <TD colspan="6">
     			     	<table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="1" >
                          <TR align="center">
					        <TD>Atenção Continuada</TD>
                            <TD width="10"><A href="javascript:showFrame('show_atencao');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Atenção Continuada"></A></TD>
                          </TR>
                        </TABLE>
                      </TD>
			        </TR>
			        <TR>
                      <TD colspan="6">
					    <div id="show_atencao" style="display:none;">
					      <table border="0" width="100%" cellpadding="0" cellspacing="0" >
         			        <TR>
                              <TD colspan="4">
                     	        <TABLE width="100%" cellpadding="0" cellspacing="1" border="0">
					              <TR>
					                <TD align="left" width="20%" bgcolor="#D8DDE3" class="descricao_campo_tabela">
                                      <img src="<?php echo URL."/imagens/obrigat_1.gif";?>">Atenção Continuada:
                                    </TD>
    	                            <td align="left" width="80%" bgcolor="#D4DFED">
                                      <select name="atencao" size="1"  style="width:200px;">
                                        <option value=""> Selecione</option>
                                        <?php
                                          $sql="select id_atencao_continuada, descricao
                                                from atencao_continuada
                                                where status_2 = 'A'
                                                order by descricao";
                                          $atencao=mysqli_query($db, $sql);
                                          erro_sql("Select Atenção Continuada", $db, "");
                                          $total_itens=mysqli_num_rows($atencao);
                                          while($listaatencao=mysqli_fetch_object($atencao)){
                                        ?>
                                            <option value="<?php echo $listaatencao->id_atencao_continuada;?>"> <?php echo $listaatencao->descricao;?></option>
                                        <?php
                                          }
                                        ?>
                                      </select>
                                      <input style="font-size: 12px;" type="button" name="ok" id="ok" value=" OK " onclick="insereLinhas();">
                                    </td>
                                  </TR>
			    	            </TABLE>
				              </TD>
			                </TR>
						    <tr>
							  <td colspan="4">
  							    <table width="100%" cellpadding="0" cellspacing="1" id="tabela">
                                  <tr bgcolor=#0E5A98>
							        <td width="15%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Código</b></font></td>
								    <td width="75%" align="left"><font color="#FFFFFF" face="arial" size="2"><b>Atenção Continuada</b></font></td>
								    <td width="5%" align="center"><font color="#FFFFFF" face="arial" size="2">&nbsp;</font></td>
							      </tr>
                                  <?php
                                    $sql="select a.descricao, a.id_atencao_continuada
                                          from atencao_continuada a,
                                               atencao_continuada_paciente p
                                          where a.id_atencao_continuada=p.id_atencao_continuada
                                                and p.id_paciente = '$id_paciente'
                                          order by a.descricao";
                                    $atencao=mysqli_query($db, $sql);
                                    erro_sql("Select Código/Atenção Continuada", $db, "");
                                    $cont=0;
                                    while($listaatencao=mysqli_fetch_object($atencao)){
                                      $nome_linha="linha_atencao".$cont;
                                  ?>
                                      <tr class="campo_tabela" id="<?php echo $nome_linha;?>">
 			 				            <td align="left"><?php echo $listaatencao->id_atencao_continuada;?></td>
									    <td align="left"><?php echo $listaatencao->descricao;?></td>
                                        <td align="center">
                                          <a href="JavaScript:removeLinhas('<?php echo $nome_linha;?>');"><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Remover Registro"></a>
                                        </td>
									  </tr>
                                  <?php
                                      $cont++;
                                    }
                                  ?>
                                </table>
						      </td>
						    </tr>
					      </table>
					    </div>
				      </TD>
			        </TR>
                    <tr height="35">
                      <td colspan="6" align="right" class="descricao_campo_tabela">
                        <input style="font-size: 10px;" type="button" name="voltar"  id="voltar" value="<< Voltar"  onClick="voltar_pagina();">
                        <input style="font-size: 10px;" type="button" name="salvar" id="salvar" value="Salvar >>" onClick="salvar_dados();">
                        <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $id_paciente;?>">
                        <input type="hidden" name="lista_atencao" id="lista_atencao">
                        <input type="hidden" name="flag" id="flag" value="f">
                        <input type="hidden" name="lista_cartao" id="lista_cartao">
                        <input type="hidden" name="contador_cartao" id="contador_cartao" value="<?php echo $contador;?>">
                        <input type="hidden" name="lista_prontuario" id="lista_prontuario">
                        <input type="hidden" name="contador_prontuario" id="contador_prontuario" value="<?php echo $cont_pront;?>">
                        <input type="hidden" name="cont_atencao" id="cont_atencao" value="<?php echo $cont;?>">
                        <input type="hidden" name="id_paciente_aux" id="id_paciente_aux" value="<?php echo $_GET[id_paciente];?>">
                        <input type="hidden" name="prontok" id="prontok" value="false">
                        <input type="hidden" name="cartaook" id="cartaook" value="false">
                      </td>
                    </tr>
                    <tr>
        			  <td colspan="6" class="descricao_campo_tabela" height="21">
				        <table align="center" border="0" cellpadding="0" cellspacing="0">
				          <tr valign="top" class="descricao_campo_tabela">
						    <td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
						    <td>&nbsp&nbsp&nbsp</td>
                            <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
					      </tr>
				        </table>
                      </td>
			        </tr>
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
    <script language="javascript">
    <!--
      var x=document.form_alteracao;
      x.nome.focus();
    //-->
    </script>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
