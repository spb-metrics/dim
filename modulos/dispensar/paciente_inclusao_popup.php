<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/buscar_aplic.php";

    $sql = "select limite_menor_dt_nasc
            from
                   parametro";
    $res=mysqli_query($db, $sql);
    erro_sql("Select Parametro", $db, "");
    $dados_parametro = mysqli_fetch_object($res);
    $data_parametro = substr($dados_parametro->limite_menor_dt_nasc,-2)."/".substr($dados_parametro->limite_menor_dt_nasc,5,2)."/".substr($dados_parametro->limite_menor_dt_nasc,0,4);

?>
    <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
    <script language="javascript">
    <!--
    function chkCpf() {
      var campo=document.form_inclusao.cpf;
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
          globalvar = campo;
          setTimeout("globalvar.focus()",250);
          globalvar.select();

           //campo.select();
           return false;
       }

     if ((str_aux == "00000000000") || (str_aux == "11111111111") ||
         (str_aux == "22222222222") || (str_aux == "33333333333") ||
         (str_aux == "44444444444") || (str_aux == "55555555555") ||
         (str_aux == "66666666666") || (str_aux == "77777777777") ||
         (str_aux == "88888888888") || (str_aux == "99999999999") ) {
           alert ("O CPF digitado é inválido !!!");
          globalvar = campo;
          setTimeout("globalvar.focus()",250);
          globalvar.select();

           //campo.select();
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
          globalvar = campo;
          globalvar = campo;
          setTimeout("globalvar.focus()",250);
          globalvar.select();

           //campo.select();
           return false;
     }
     return true;
    }
    
    function salvarPaciente()
    {
      var y=document.form_inclusao;
      var nome=y.nome.value;
      var mae=y.mae.value;
      var sexo=y.sexo.value;
      var tipo_logradouro=y.tipo_logradouro.value;
      var data_nasc=y.data_nasc.value;
      var telefone= y.telefone.value;
      var cpf= y.cpf.value;
      var logradouro=y.logradouro.value;
      var numero=y.numero.value;
      var complemento=y.complemento.value;
      var bairro=y.bairro.value;
      var id_cidade_rec=y.id_cidade_receita.value;
      var unidade_ref=y.unidade_referida.value;
      var id_status_pac=y.id_status_paciente.value;
      var id_usuario_sistema = y.id_usuario_sistema.value;
      var id_unidade_sistema = y.id_unidade_sistema.value;
      var lista_atencao=y.lista_atencao.value;
      var lista_cartao=y.lista_cartao.value;
      var lista_prontuario=y.lista_prontuario.value;
      var url = "../../xml/salvarPacienteInclusao.php?nome=" + nome + "&mae=" + mae + "&data_nasc=" + data_nasc
                      + "&tipo_logradouro=" + tipo_logradouro + "&sexo=" + sexo
                      + "&id_status_paciente="+ id_status_pac
                      + "&lista_atencao="+lista_atencao+ "&lista_cartao="+lista_cartao+ "&lista_prontuario="+lista_prontuario
                      + "&telefone=" + telefone + "&cpf=" + cpf + "&logradouro=" + logradouro + "&numero=" + numero
                      + "&complemento=" + complemento + "&bairro=" + bairro + "&id_cidade_receita=" + id_cidade_rec
                      + "&unidade_referida=" + unidade_ref
                      + "&id_usuario_sistema=" + id_usuario_sistema
                      + "&id_unidade_sistema=" + id_unidade_sistema;
     requisicaoHTTP("GET", url, true);
    }

   function preencheCampos(id, nome, nome_mae, cartao_sus, nasc)
    {
        var args = id+'|'+nome+'|'+nome_mae+'|'+cartao_sus+'|'+nasc;
    	if (window.showModalDialog)
    	{
    		var _R = new Object()
    		_R.args = args;
    		window.returnValue=_R;
    	}
    	else
    	{
    		if (window.opener.SetNameInclusao)
    		{
                window.opener.SetNameInclusao(args);
    		}
    	}
    	window.close();
    	window.close();
    }

    function verificarCPF(){
      var y=document.form_inclusao;
      var cpf=y.cpf.value;
      var nome=y.nome.value;
      var mae=y.mae.value;
      var data_nasc=y.data_nasc.value;
      var url = "../../xml/pacienteCpf.php?nome=" + nome + "&mae=" + mae + "&data_nasc=" + data_nasc + "&cpf=" + cpf;
      requisicaoHTTP("GET", url, true);
    }

    function trataDados(){
      var x=document.form_inclusao;
      var info  = ajax.responseText;  // obtém a resposta como string
      var texto = info.substr(0, 3);

      if(texto!="SAV" && texto!="CPF" && texto!="NPF" && texto!="ok!"){
         var msg="Os cartões SUS informados estão cadastrados para outros pacientes\n";
         msg+="Nº do cartão SUS - Nome do paciente - Nome da mãe - Data de nascimento\n";
         msg+=info;
         window.alert(msg);
      }
      if(texto=="SAV" ){
        verificarCPF();
      }
      if(texto=="CPF"){
        salvarPaciente();
      }
      if(texto=="NPF"){
        window.alert("CPF já existe cadastrado!");
        x.cpf.focus();
        x.cpf.select();
      }
      if(texto=="ok!")
      {
         var id_paciente =info.substr(3);
         preencheCampos(id_paciente);
      }
    }
	
    function retirarEspaco(){
      var x=document.form_inclusao;
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

    function verificarCartao(){
      retirarEspaco();
      var x=document.getElementById("lista_cartao");
      var itens=x.value;
      var y=document.form_inclusao;
      var nome = y.nome.value;
      var mae  = y.mae.value;
      var data_nasc = y.data_nasc.value;
      var url = "../../xml/pacienteCartao.php?nome=" + nome + "&mae=" + mae + "&data_nasc=" + data_nasc + "&itens=" + itens;
      requisicaoHTTP("GET", url, true);
    }

    function validar_campos(){
      var x=document.form_inclusao;
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
      return true;
    }
    
     var cont=0;
    var vetCod=new Array();

    function insereLinhas(){
      var x=document.form_inclusao;
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
          tab.id="linha_atencao"+cont;
          tab.className = "campo_tabela";
          var cell1=tab.insertCell(0);
          cell1.align="center";
          cell1.innerHTML=xValue;
          var cell2=tab.insertCell(1);
          cell2.align="left";
          cell2.innerHTML=xText;
          var cell3=tab.insertCell(2);
          cell3.align="center";
          var Site = "<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' onclick='removeLinhas("+tab.id+");' alt='Remover Registro'>";
          cell3.innerHTML=Site;

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
      document.getElementById("tabela").deleteRow(document.getElementById(lnh.id).rowIndex);
      var x=document.form_inclusao;
      x.atencao.focus();
    }


    function salvar_dados(){
      var x=document.form_inclusao;
      if (validar_campos()==true){
        var itens=document.getElementById('tb_prontuario');
        var total=document.getElementById("tb_prontuario").rows.length;
        var lista="";
        for(var i=1; i<total; i++){
          if(i==1){
            lista=itens.rows[i].cells[0].innerHTML + ",";
          }
          if(i>1){
            lista+=itens.rows[i].cells[0].innerHTML + ",";
          }
        }
        document.getElementById('lista_prontuario').value=lista;
        var itens=document.getElementById('tabela');
        var total=document.getElementById("tabela").rows.length;
        var lista="";
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
        verificarCartao();
      }
    }

    function validarProntuario(){
      var x=document.form_inclusao;
      if(x.prontuario.value.length==0){
        window.alert("Nº de prontuário não informado!");
        x.prontuario.select();
        return false;
      }
      return true;
    }
   ////////////////
    var contpront=0;
    var vetor_prontuario=new Array();

    function inserirLinhaPront(){
      var x=document.form_inclusao;
      if(validarProntuario()){
        var achou=false;
        var itens=document.getElementById("tb_prontuario");
        var total=document.getElementById("tb_prontuario").rows.length;
        for(var i=1; i<total; i++){
          if((itens.rows[i].cells[0].innerHTML==x.prontuario.value)&&(itens.rows[i].cells[1].innerHTML==x.unidade_cadastro.value)){
            achou=true;
          }
        }
        ant = itens.rows[total-1].cells[0].innerHTML;
        if (ant=="<B>Prontuário</B>")
         ant = 0;
        else
         ant = parseInt(itens.rows[total-1].cells[0].innerHTML);

        if(achou==false){
          var pos=total;
          var tab=document.getElementById("tb_prontuario").insertRow(pos);

          contpront=parseInt(x.contador_prontuario.value);
          contpront++;
          tab.id="linha_prontuario" + contpront;
          tab.className="campo_tabela";
          var cell1=tab.insertCell(0);
          cell1.align="center";
          cell1.innerHTML=x.prontuario.value;
          var cell2=tab.insertCell(1);
          cell2.align="left";
          cell2.innerHTML=x.unidade_cadastro.value;
          var cell3=tab.insertCell(2);
          cell3.align="center";

          var Site="<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' onclick='removerLinhaPront("+tab.id+");' alt='Remover Registro'>";
          cell3.innerHTML=Site;
          vetor_prontuario[contpront]=x.prontuario.value;
          x.contador_prontuario.value=contpront;
        }
        else{
          window.alert("Prontuário já inserido!");
        }
        x.prontuario.value="";
        x.prontuario.focus();
      }
    }

    /////////////////
    function removerLinhaPront(lnh){
      document.getElementById("tb_prontuario").deleteRow(document.getElementById(lnh.id).rowIndex);
      var x=document.form_inclusao;
      x.prontuario.focus();
    }
    
    
    function validarCartao(){
      var x=document.form_inclusao;
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
      var x=document.form_inclusao;
      if(validarCartao()){
        var achou=false;
        var itens=document.getElementById("tb_cartao");
        var total=document.getElementById("tb_cartao").rows.length;
        for(var i=1; i<total; i++){
          if(itens.rows[i].cells[1].innerHTML==x.cartao.value){
            achou=true;
          }
        }
       ant = itens.rows[total-1].cells[0].innerHTML;
       if (ant=="<B>Nro</B>" || ant=="<b>Nro</b>")
        ant = 0;
       else
        ant = parseInt(itens.rows[total-1].cells[0].innerHTML);
        if(achou==false){
          var pos=total;
          var tab=document.getElementById("tb_cartao").insertRow(pos);
          contador=parseInt(x.contador_cartao.value);
          contador++;
          tab.id="linha_cartao" + contador;
          tab.className="campo_tabela";
          var cell1=tab.insertCell(0);
          cell1.align="center";
          cell1.innerHTML=ant+1;
          var cell2=tab.insertCell(1);
          cell2.align="left";
          cell2.innerHTML=x.cartao.value;
          var cell3=tab.insertCell(2);
          cell3.align="center";
          var Site = "<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' onclick='removerLinha("+tab.id+");' alt='Remover Registro'>";
          cell3.innerHTML=Site;
          vetor_cartao[contador]=x.cartao.value;
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
      document.getElementById("tb_cartao").deleteRow(document.getElementById(lnh.id).rowIndex);
      var x=document.form_inclusao;
      x.cartao.focus();
    }

    function removerLinhaExiste(num){
      document.getElementById("tb_cartao").deleteRow(document.getElementById(num).rowIndex);
      var x=document.form_inclusao;
      x.cartao.focus();
    }


    function removerLinhaExistePront(num){
      document.getElementById("tb_prontuario").deleteRow(document.getElementById(num).rowIndex);
      var x=document.form_inclusao;
      x.prontuario.focus();
    }
    
    
    function removerLinhaExisteAtencao(num){
      document.getElementById("tabela").deleteRow(document.getElementById(num).rowIndex);
      var x=document.form_inclusao;
      x.atencao.focus();
    }
    
    
    
     //=========== Popup Cidade ============

    var d = new Date();
    var ID = d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

    function popup_cidade()
    {
    	var height = 350;
    	var width = 450;
    	var left = (screen.availWidth - width)/2;
    	var top = (screen.availHeight - height)/2;
    	if (window.showModalDialog)
    	{
    		var dialogArguments = new Object();
    		var _R = window.showModalDialog("pesquisa_cidade_paciente.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
    		if ("undefined" != typeof(_R))
    		{
    			SetName(_R.id, _R.strName);
    		}
    	}
    	else	//NS
    	{
    		var left = (screen.width-width)/2;
    		var top = (screen.height-height)/2;
     		var winHandle = window.open("pesquisa_cidade_paciente.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
    		winHandle.focus();
    	}
    	//return false;
    }

    function SetName(id, strName)
    {
    	document.form_inclusao.id_cidade_receita.value = id;
    	document.form_inclusao.cidade_receita.value = strName;
        if(id!=948)
    	{
    	  document.form_inclusao.unidade_referida.value="";
    	  document.form_inclusao.unidade_referida.disabled=true;
    	}
    	else
    	{
         	document.form_inclusao.unidade_referida.disabled=false;
    	}
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
           return false;
        }
        return true;
        
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
           return false;
          }
          return true;
   }
  }
    //-->
    </script>

    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='4'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./paciente_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="6" valign="middle" align="center" width="100%"> Paciente: Incluir </td>
                    </tr>
                    <tr>
                      <input type="hidden" id="id_unidade_sistema" name="id_unidade_sistema" value="<?=$_SESSION['id_unidade_sistema']?>">
                      <input type="hidden" id="id_usuario_sistema" name="id_usuario_sistema" value="<?=$_SESSION['id_usuario_sistema']?>">
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" id="nome"  name="nome" size="60"  maxlength="70">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="18%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Dt. Nascimento
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <input type="hidden" id="data_parametro" name="data_parametro" size="10"  value="<?php echo $data_parametro;?>">
                        <input type="text" id="data_nasc" name="data_nasc" size="10"  maxlength="10" onKeyPress="return mascara_data(event,this)"
                        onblur="if(verificaData(this))
                                {
                                 if(verificaDataMaior(this))
                                 {
                                  if(verificaDataMenor(this,document.form_inclusao.data_parametro))
                                  {
                                     document.form_inclusao.mae.focus();
                                  }
                                  else
                                  {
                                   document.form_inclusao.data_nasc.focus();
                                   document.form_inclusao.data_nasc.select();
                                  }
                                 }
                                 else
                                 {
                                  document.form_inclusao.data_nasc.focus();
                                  document.form_inclusao.data_nasc.select();
                                 }
                                }
                                else
                                {
                                document.form_inclusao.data_nasc.focus();
                                document.form_inclusao.data_nasc.select();
                                }
                                ; ">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome Mãe
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" id="mae" name="mae" size="60" maxlength="70">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="18%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Sexo
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <select name="sexo" size="1" style="width:85px;">
                          <option value="">Selecione</option>
                          <option value="F">Feminino</option>
                          <option value="M">Masculino</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        CPF
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                        <input type="text" name="cpf" id="cpf" size="30" maxlength="14" onkeypress="return isNumberKey(event);" onblur="return chkCpf();">
                      </td>

                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Telefone
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <input type="text" name="telefone" size="10" maxlength="12">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Tipo Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <select id="tipo_logradouro" name="tipo_logradouro" size="1" style="width:100px;">
                          <option value="">Selecione</option>
                          <option value="Avenida">Avenida</option>
                          <option value="Beco">Beco</option>
                          <option value="Caminho">Caminho</option>
                          <option value="Estrada">Estrada</option>
                          <option value="Ladeira">Ladeira</option>
                          <option value="Largo">Largo</option>
                          <option value="Lote">Lote</option>
                          <option value="Outro">Outro</option>
                          <option value="Praça">Praça</option>
                          <option value="Quadra">Quadra</option>
                          <option value="Rodovia">Rodovia</option>
                          <option value="Rua" selected>Rua</option>
                          <option value="Travessa">Travessa</option>
                          <option value="Via">Via</option>
                          <option value="Vila">Vila</option>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" id="logradouro" name="logradouro" size="63" maxlength="50">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Número
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <input type="text" name="numero" size="12" maxlength="7">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Complemento
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <input type="text" name="complemento" size="65" maxlength="15">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Bairro
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                        <input type="text" id="bairro" name="bairro"  size="45" maxlength="30">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Cidade
                      </td>
                     <?php
                       $sql = "select id_cidade, concat(cid.nome,'/',est.uf) as nome
                               from
                                      cidade cid, estado est, parametro par
                               where
                                      cid.estado_id_estado = est.id_estado
                                      and cid.id_cidade = par.cidade_id_cidade";
                       $res=mysqli_query($db, $sql);
                       erro_sql("Select Cidade", $db, "");
                       $dados_cidade_receita = mysqli_fetch_object($res);
                       $cidade_receita = $dados_cidade_receita->nome;
                       $id_cidade_receita = $dados_cidade_receita->id_cidade;
                     ?>
                     <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                       <input type="text" size="45" name="cidade_receita" value="<?php echo $cidade_receita;?>" disabled>
                       <img src="<?php echo URL;?>/imagens/i_002.gif" onclick="JavaScript:window.popup_cidade();" border="0" title="Pesquisar"></a>
                       <input type="hidden" name="id_cidade_receita" id="id_cidade_receita" value="<?php echo $id_cidade_receita;?>">
                     </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CS Cadastro
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="2">
                        <input type="text" id="unidade_cadastro" name="unidade_cadastro" value="<?php echo $_SESSION[nome_unidade_sistema];?>" size="30" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CS Unidade
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <select name="unidade_referida" id="unidade_referida" size="1" style="width:200px;">
                          <option value=""> Selecione</option>
                          <?php
                            $sql="select id_unidade, nome
                                  from
                                         unidade
                                  where
                                         status_2='A'
                                         and flg_nivel_superior=0
                                  order by
                                         nome";
                            $unidade=mysqli_query($db, $sql);
                            erro_sql("Select CS Unidade", $db, "");
                            while($listaunidade=mysqli_fetch_object($unidade)){
                              //$selecionado="";
                              //if($listaunidade->id_unidade==$_SESSION[id_unidade_sistema]){
                                //$selecionado="selected";
                              //}
                          ?>
                              <option value="<?php echo $listaunidade->id_unidade;?>"> <?php echo $listaunidade->nome;?></option>
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
                                     from
                                           status_paciente
                                     where
                                           status_2 = 'A'
                                           and flg_mostrar='S'
                                     order by
                                           descricao";
                            $status = mysqli_query($db, $sql);
                            erro_sql("Select Situação", $db, "");
                            while($listastatus=mysqli_fetch_object($status)){
                              $selecionado="";
                              if(strtoupper($listastatus->descricao)=="ATIVO"){
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
                            <td width="90%">Prontuário</td>
                            <td width="10%">
                              <img src="<?php echo URL. '/imagens/b_edit.gif'; ?>" border="0" title="Exibir Informações de Prontuário"
                              onclick="
                               if(document.getElementById('show_prontuario').style.display == 'none')
                               {
                                  document.getElementById('show_prontuario').style.display = 'inline';
                               }
                               else
                               {
                                  document.getElementById('show_prontuario').style.display = 'none';
                               }">
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
                                Nº Prontuário
                              </td>
                              <td class="campo_tabela" width="15%">
                                <input type="text" name="prontuario" size="15" maxlength="15" onkeypress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                              </td>
                              <td class="descricao_campo_tabela" width="100%">
                                <input type="button" name="bt_prontuario" value=" OK " onclick="inserirLinhaPront();">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3">
                                <table width="100%" border='0' cellpadding="0" cellspacing="1" name="tb_prontuario" id="tb_prontuario">
                                  <tr class="coluna_tabela">
                                    <td width="15%" align="center"><b>Prontuário</b></td>
                                    <td width="75%" align="left"><b>Unidade</b></td>
                                    <td width="5%" align="center">&nbsp;</td>
                                  </tr>
                                  <?php
                                    $sql="select p.num_prontuario, u.nome
                                          from
                                                 prontuario as p,
                                                 unidade as u
                                          where
                                                 p.paciente_id_paciente='$id_paciente'
                                                 and p.unidade_id_unidade=u.id_unidade
                                          order by
                                                 p.num_prontuario";
                                    $prontuario=mysqli_query($db, $sql);
                                    erro_sql("Select Prontuário", $db, "");

                                    $contpront=0;
                                    while($listaprontuario=mysqli_fetch_object($prontuario)){
                                    ?>
                                    <input type="text" id="prontuario<?echo $listaprontuario->num_prontuario;?>" name="prontuario" value="<?php echo $listaprontuario->num_prontuario;?>">
                                    <tr class="campo_tabela" id="<?echo $contpront+1;?>">
                                      <td width="15%" align="center"><?echo $listaprontuario->num_prontuario;?></td>
                                      <td width="75%" align="left"><?echo $listaprontuario->nome;?></td>
                                      <td width="5%" align="center">
                                          <img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' onclick='removerLinhaExistePront(<?echo $contpront+1;?>);' alt='Remover Registro'>
                                      </td>
                                    </tr>

                                  <?
                                    $contpront+=1;
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
                            <td width="90%">Cartões SUS</td>
                            <td width="10%">
                              <img src="<?php echo URL. '/imagens/b_edit.gif'; ?>" border="0" title="Exibir Informações de Cartão SUS"
                              onclick="
                               if(document.getElementById('show_cartao').style.display == 'none')
                               {
                                  document.getElementById('show_cartao').style.display = 'inline';
                               }
                               else
                               {
                                  document.getElementById('show_cartao').style.display = 'none';
                               }">
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
                                <input type="text" name="cartao" size="15" maxlength="15" onkeypress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                              </td>
                              <td class="descricao_campo_tabela" width="100%">
                                <input type="button" name="bt_cartao" value=" OK " onclick="inserirLinha();">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="3">
                                <table width="100%" border='0' cellpadding="0" cellspacing="1" name="tb_cartao" id="tb_cartao">
                                  <tr class="coluna_tabela">
                                    <td width="10%" align="center"><b>Nro</b></td>
                                    <td width="85%" align="center"><b>Cartão SUS</b></td>
                                    <td width="5%" align="center">&nbsp;</td>
                                  </tr>
                                  <?php
                                    $sql="select cartao_sus
                                          from
                                                 cartao_sus
                                          where
                                                 paciente_id_paciente='$id_paciente'
                                          order by
                                                 cartao_sus";
                                    $cartao=mysqli_query($db, $sql);
                                    erro_sql("Select Cartão SUS", $db, "");
                                    $contador=0;
                                    while($listacartao=mysqli_fetch_object($cartao)){
                                    ?>
                                    <input type="text" id="cartaosus<?echo $listacartao->cartao_sus;?>" name="cartaosus" value="<?php echo $listacartao-> cartao_sus;?>">
                                    <tr class="campo_tabela" id="<?echo $contador+1;?>">
                                      <td width="10%" align="center"><?echo $contador+1;?></td>
                                      <td width="85%" align="left"><?echo $listacartao->cartao_sus;?></td>
                                      <td width="5%" align="center">
                                          <img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' onclick='removerLinhaExiste(<?echo $contador+1;?>);' alt='Remover Registro'>
                                      </td>
                                    </tr>

                                  <?
                                    $contador+=1;
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
					        <td width="90%">Atenção Continuada</td>
                            <td width="10%">
                              <img src="<?php echo URL. '/imagens/b_edit.gif'; ?>" border="0" title="Exibir Informações de Atenção Continuada"
                              onclick="
                               if(document.getElementById('show_atencao').style.display == 'none')
                               {
                                  document.getElementById('show_atencao').style.display = 'inline';
                               }
                               else
                               {
                                  document.getElementById('show_atencao').style.display = 'none';
                               }">
                            </td>
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
                                                from
                                                       atencao_continuada
                                                where
                                                       status_2 = 'A'
                                                order by
                                                       descricao";
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
                                      <input style="font-size: 12px;" type="button" name="ok" value=" OK " onclick="insereLinhas();">
                                    </td>
                                  </TR>
			    	            </TABLE>
				              </TD>
			                </TR>
						    <tr>
							  <td colspan="4">
  							    <table width="100%" cellpadding="0" cellspacing="1" name="tabela" id="tabela">
                                  <tr bgcolor=#0E5A98>
							        <td width="10%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Código</b></font></td>
								    <td width="85%" align="left"><font color="#FFFFFF" face="arial" size="2"><b>Atenção Continuada</b></font></td>
								    <td width="5%" align="center"><font color="#FFFFFF" face="arial" size="2">&nbsp;</font></td>
							      </tr>
                                  <?php
                                    $sql="select a.descricao, a.id_atencao_continuada
                                          from
                                                 atencao_continuada a,
                                                 atencao_continuada_paciente p
                                          where
                                                 p.id_paciente = '$id_paciente'
                                                 and a.id_atencao_continuada=p.id_atencao_continuada
                                          order by
                                                 a.descricao";
                                    $atencao=mysqli_query($db, $sql);
                                    erro_sql("Select Código/Atenção Continuada", $db, "");
                                    $cont=0;
                                    while($listaatencao=mysqli_fetch_object($atencao)){
                                    ?>
                                    <input type="hidden" id="codigoAtencao<?echo $listaatencao->id_atencao_continuada;?>" name="codigoAtencao" value="<?php echo $listaatencao-> id_atencao_continuada;?>">
                                    <input type="hidden" id="descricaoAtencao<?echo $listaatencao->descricao;?>" name="descricaoAtencao" value="<?php echo $listaatencao-> descricao;?>">
                                    <tr class="campo_tabela">
							          <td width="10%" align="center"><?echo $listaatencao->id_atencao_continuada;?></td>
								      <td width="85%" align="left"><?echo $listaatencao->descricao;?></td>
								      <td width="5%" align="center"><img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' onclick='removerLinhaExisteAtencao(<?echo $cont+1;?>);' alt='Remover Registro'>
                                      </td>
							        </tr>
                                    <?
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
                        <input style="font-size: 10px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.close();">
                        <input style="font-size: 10px;" type="button" name="salvar" value="Salvar >>" onClick="salvar_dados();">
                        <input type="hidden" name="lista_atencao" id="lista_atencao">
                        <input type="hidden" name="lista_cartao" id="lista_cartao">
                        <input type="hidden" name="contador_cartao" value="<?php echo $contador;?>">
                        <input type="hidden" name="cont_atencao" value="<?php echo $cont;?>">
                        <input type="hidden" name="lista_prontuario" id="lista_prontuario">
                        <input type="hidden" name="contador_prontuario" value="<?php echo $contpront;?>">
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
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
   // require DIR."/footer.php";

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
