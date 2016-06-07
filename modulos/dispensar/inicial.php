<?php
/* 
	Copyright 2011 Inform·tica de MunicÌpios Associados
	Este arquivo È parte do programa DIM
	O DIM È um software livre; vocÍ pode redistribuÌ-lo e/ou modific·-lo dentro dos termos da LicenÁa P˙blica Geral GNU como publicada pela FundaÁ„o do Software Livre (FSF); na vers„o 2 da LicenÁa.
	Este programa È distribuÌdo na esperanÁa que possa ser  ˙til, mas SEM NENHUMA GARANTIA; sem uma garantia implÌcita de ADEQUA«√O a qualquer  MERCADO ou APLICA«√O EM PARTICULAR. Veja a LicenÁa P˙blica Geral GNU/GPL em portuguÍs para maiores detalhes.
	VocÍ deve ter recebido uma cÛpia da LicenÁa P˙blica Geral GNU, sob o tÌtulo "LICENCA.txt", junto com este programa, se n„o, acesse o Portal do Software P˙blico Brasileiro no endereÁo www.softwarepublico.gov.br ou escreva para a FundaÁ„o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  //CRIANDO NUMERO DE CONTROLE PARA EVITAR DUPLICIDADE NA GRAVA«√O
  session_regenerate_id();
  $teste = session_id();
  $num_controle = date("Y-m-d H:i:s").$id_unidade_sistema.$teste;
  
  $paciente_compl = $_SESSION ['id_paciente'];
  

  

  //echo "pac sessao".$paciente_compl;
 // echo "paciente da sesaao: ".$pac;
 //$pac=  $_SESSION[APLICACAO]=$_GET[aplicacao];

  //TESTANDO EXIST NCIA DE ARQUIVO DE CONFIGURA«√O//
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICA«√O DE SEGURAN«A//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }
    require DIR."/header.php";

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P¡GINA//
    ////////////////////////////////////

    //permiss„o de acesso para dispensar (nova receita)
    require "../../verifica_acesso.php";

    $sql = "select
                  id_aplicacao
            from
                  aplicacao
            where
                  executavel = '/modulos/dispensar/busca_altera_receita.php'
                  and status_2 = 'A'";
    $res_aplicacao = mysqli_fetch_object(mysqli_query($db, $sql));
    $aplicacao_altera_receita = $res_aplicacao->id_aplicacao;

    
    $sql = "select
                  id_aplicacao
            from
                  aplicacao
            where
                  executavel = '/modulos/profissional/profissional_inicial.php'
                  and status_2 = 'A'";
    $res_profissional = mysqli_fetch_object(mysqli_query($db, $sql));
    $id_aplicacao_profissional = $res_profissional->id_aplicacao;

    $sql = "select
                  inclusao, alteracao, exclusao, consulta
            from
                  perfil_has_aplicacao
            where
                  perfil_id_perfil = '$_SESSION[id_perfil_sistema]'
                  and aplicacao_id_aplicacao = '$id_aplicacao_profissional'";
    $acesso = mysqli_fetch_object(mysqli_query($db, $sql));
    $inclusao_perfil_profissional  = $acesso->inclusao;
    $alteracao_perfil_profissional = $acesso->alteracao;
    $exclusao_perfil_profissional  = $acesso->exclusao;
    $consulta_perfil_profissional  = $acesso->consulta;

    //caminho
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
    
?>
<script language="javascript">

   function habilitaBotaomanter(){
   

        var x=document.form_inclusao;
		
		
		var idpaciente = x.id_paciente_sessao.value;
            if( x.aux_id_paciente.value == '' && idpaciente == ''){
              x.manter.disabled=true;
              }
              else {
                x.manter.removeAttribute('disabled');
               }
          }
</script>



  <script language="javascript">


       
    function Trim(str){
      return str.replace(/^\s+|\s+$/g,"");
    }

      function habilitaBotaoSalvar(){
      var x=document.form_inclusao;
      if(Trim(x.login.value)=="" || Trim(x.senha.value)=="" || document.getElementById('hidden_lista').rows.length==1){
        x.salvar.disabled=true;
      }
      else{
        x.salvar.disabled=false;
      }
    }
    function desabilitaBotaoSalvar(){
      var x=document.form_inclusao;
      x.salvar.disabled=true;

    }
    function verificaLoginSenhaResponsavelDispensacao(){
      var x=document.form_inclusao;
      var url = "../../xml_dispensacao/verificar_login_senha_responsavel_dispensacao.php?login="+x.login.value+"&senha="+x.senha.value;
	  requisicaoHTTP("GET", url, true, '');

    }
    function salvarReceita(){
	//id_paciente_sessao = "";
	//paciente_compl ="";

      var x=document.form_inclusao;
      if(x.flag_mostrar_responsavel_dispensacao.value=="S"){
        verificaLoginSenhaResponsavelDispensacao();
      }
      else{
        salvar_receita();
      }
    }

    var isNN = (navigator.appName.indexOf("Netscape")!=-1);

    function autoTab(input,len, e) {
      var keyCode = (isNN) ? e.which : e.keyCode;
      var filter = (isNN) ? [0,8,9] : [0,8,9,16,17,18,37,38,39,40,46];
      if(input.value.length >= len && !containsElement(filter,keyCode)) {
         input.value = input.value.slice(0, len);
         input.form[(getIndex(input)+1) % input.form.length].focus();
    }

    function containsElement(arr, ele) {
      var found = false, index = 0;
      while(!found && index < arr.length)
        if(arr[index] == ele)
          found = true;
        else
          index++;
      return found;
    }

    function getIndex(input) {
       var index = -1, i = 0, found = false;
       while (i < input.form.length && index == -1)
        if (input.form[i] == input)index = i;
        else i++;
       return index;
       }
     return true;
    }
    
  // atualizar um campo texto, a partir de outro onde digitei a informacao
  // funcoes carregar e processarXML.

    function carregar_paciente(unit, campo, arquivo, campo1, campo2, campo3, campo4, campo5, campo6){
      //verifica se o browser tem suporte a ajax

      this.assincr = true;

      //alert("Entrou na funÁ„o para checar ajax");

	  try{
        var ajax=new ActiveXObject("Microsoft.XMLHTTP");
              //alert ("ie");

      }
      catch(e){

        try{
          var ajax=new ActiveXObject("Msxml2.XMLHTTP");
              // alert ("ff");

        }
	    catch(ex){
          try{
            var ajax=new XMLHttpRequest();
                  // alert ("tes2");
          }
	      catch(exc){
            alert("Esse browser n„o tem recursos para uso do Ajax");
            var ajax=null;
          }
        }
      }
	  //se tiver suporte ajax
	  if(ajax){
             //alert("ajax true");

	    //deixa apenas o elemento 1 no option, os outros s„o excluÌdos
        var opcao=document.getElementById(campo);
        var aux1=document.getElementById(campo1);
        var aux2=document.getElementById(campo2);
        var aux3=document.getElementById(campo3);
        var aux4=document.getElementById(campo4);
        var aux5=document.getElementById(campo5);
        var aux6=document.getElementById(campo6);
        ajax.open("POST", arquivo, true);

           //alert (var);
           
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");







	    ajax.onreadystatechange = function(){
                                   //alert("balao");


                         //chama funÁ„o processarXML que vai varrer os dados
                             if(ajax.readyState==4){
                                     //alert ("readsate 4");
                                if(ajax.responseXML){
                                //alert ("*");
                                    processarXML(ajax.responseXML, opcao, aux1, aux2, aux3, aux4, aux5, aux6);
                                      //alert("processa xml");
                                 }
                             }
                       }
	    //passa o cÛdigo do estado escolhido
	    var params=campo+"=" + unit;
	    //alert (params);
        ajax.send(params);

      }
    }

    function processarXML(obj, opt, camp1, camp2, camp3, camp4, camp5, camp6){
      //pega a tag cidade
      var dataArray=obj.getElementsByTagName("registro");

	  //total de elementos contidos na tag cidade
	  if(dataArray.length>0){
        //percorre o arquivo XML para extrair os dados
        for(var i=0; i<dataArray.length; i++){
          var item=dataArray[i];
          //contÈudo dos campos no arquivo XML
          var nome=item.getElementsByTagName("nome")[0].firstChild.nodeValue;
          var mae=item.getElementsByTagName("mae")[0].firstChild.nodeValue;
          var nasc=item.getElementsByTagName("nasc")[0].firstChild.nodeValue;
          var cartao=item.getElementsByTagName("cartao")[0].firstChild.nodeValue;
          var cpf=item.getElementsByTagName("cpf")[0].firstChild.nodeValue;
          var prontuario=item.getElementsByTagName("prontuario")[0].firstChild.nodeValue;
          camp1.value=nome;
          camp2.value=mae;
          camp3.value=nasc;
          if (cartao=='0')
          {
           camp4.value='';
          }
          else
          {
           camp4.value=cartao;
          }
          if (cpf=='0')
          {
           camp5.value='';
          }
          else
          {
           camp5.value=cpf;
          }
          if (prontuario=='0')
          {
           camp6.value='';
          }
          else
          {
           camp6.value=prontuario;
          }

        }
      }
	  else{
	    //caso o XML volte vazio, printa a mensagem abaixo
       alert("Paciente Inv·lido");
		opt.value="";
    	camp1.value="";
    	camp2.value="";
    	camp3.value="";
    	camp4.value="";
    	camp5.value="";
    	camp6.value="";
        opt.focus();
	  }
    }


function esconde_botao()
{
   to1 = document.getElementById('novareceita');
   to1.style.display = "none";
   to2 = document.getElementById('completarreceita');
   to2.style.display = "none";
}

function mostra_botao()
{
    to1 = document.getElementById('novareceita');
    to1.style.display = "block";
    to2 = document.getElementById('completarreceita');
    to2.style.display = "block";
}

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
		var _R = window.showModalDialog("pesquisa_cidade_dispensacao.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetName(_R.id, _R.strName);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_cidade_dispensacao.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

function SetName(id, strName)
{
	document.form_inclusao.id_cidade_receita.value = id;
	document.form_inclusao.cidade_receita.value = strName;
}

function popup_autorizador()
{
	var height = 115;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("autorizador.php", dialogArguments, "dialogWidth=450px;dialogHeight=115px;dialogTop=350px;dialogLeft=280px;scroll=no;status=no;");
       // alert("value "+ _R.value);
        if ("undefined" != typeof(_R))
		{
			SetNameAutorizador(_R.id);
		}
	}
	else	//NS
	{
        var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
        var winHandle = window.open("autorizador.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=no,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

function SetNameAutorizador(id)
{
	document.form_inclusao.flg_autorizador.value = id;
	
	 if(id==''){
       limpar_ultima_saida();
       limpar_receita_controlada();
       limpar_estoque();
       document.form_inclusao.medicamento01.value = '';
       document.form_inclusao.medicamento.value = '';
       document.form_inclusao.unidade.value = '';
       document.form_inclusao.grupo.selectedIndex = 0;
     }
}

function popup_prescritor()
{
	var height = 350;
	var width = 450;
	var left  = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_prescritor.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNamePrescritor(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_prescritor.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

function SetNamePrescritor(argumentos)
{
    var valores = argumentos.split('|');

    document.form_inclusao.id_prescritor.value = valores[0];
    document.form_inclusao.id_tipo_prescritor.value = valores[1];
    document.form_inclusao.inscricao.value = valores[2];

    var codigo = valores[0]+'|'+valores[1]+'|'+valores[2];
    var descricao = valores[3];

    var sel = document.getElementById("prescritor");
    sel.options.length = 1;

    sel.options[1] = new Option(descricao, codigo);
    sel.selectedIndex = 1;
}

function popup_medicamento()
{
	var height = 350;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;

    limpar_ultima_saida();
    limpar_receita_controlada();
    limpar_estoque();
    document.form_inclusao.medicamento01.value = '';
	document.form_inclusao.medicamento.value = '';
	document.form_inclusao.unidade.value = '';
	
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_material.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameMedicamento(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_material.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

function SetNameMedicamento(argumentos)
{
    if (document.form_inclusao.id_paciente.value!=''){
	
	var valores = argumentos.split('|');
	
    document.form_inclusao.flg_material.value = '1';
    document.form_inclusao.medicamento.value = valores[0];
    document.form_inclusao.medicamento01.value = valores[1];
    document.form_inclusao.unidade.value = valores[2];
	
	document.form_inclusao.qtde_prescrita.focus();
	document.form_inclusao.medicamento01.focus();
   // valida_prescritor_medicamento();
	buscar_estoque();
	ultima_saida();
	}else{
	//limpar grid de medicamento
		alert ('Necessario selecionar um paciente para dispensaÁ„o de medicamento!');	
		document.form_inclusao.nome.focus();
		
	}
	//document.form_inclusao.qtde_prescrita.focus();
}

function popup_novo_prescritor()
{
   // document.form_inclusao.novo.disabled = true;
	var height = 250;
	var width = 750;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("profissional_inclusao_popup.php", dialogArguments, "dialogWidth=750px;dialogHeight=250px;dialogTop=250px;dialogLeft=130px;scroll=no;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetName_NovoPrescritor(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("profissional_inclusao_popup.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=no,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
    //document.form_inclusao.novo.disabled = false;
}

function SetName_NovoPrescritor(argumentos)
{
    var valores = argumentos.split('|');

    document.form_inclusao.id_prescritor.value = valores[0];
    document.form_inclusao.id_tipo_prescritor.value = valores[1];
    document.form_inclusao.inscricao.value = valores[2];

    var codigo = valores[0]+'|'+valores[1]+'|'+valores[2];
    var descricao = valores[3];

    var sel = document.getElementById("prescritor");
    sel.options.length = 1;

    sel.options[1] = new Option(descricao, codigo);
    sel.selectedIndex = 1;
}

function mensagem()
{
    var cartao = document.getElementById('cartao_sus').value;
    var nome =document.getElementById('nome').value;
    var cpf = document.getElementById('cpf').value;
    var prontuario =document.getElementById('prontuario').value;
    
    if ((cartao=='') && (nome =='') && (prontuario=='') && (cpf==''))
    {
       alert('… necess·rio digitar o nome, cart„o sus, prontu·rio ou cpf');
       document.form_inclusao.pesquisar.disabled=false;
    }
    
    else if ((cartao=='') && (prontuario=='') && (cpf=='') && (nome!=''))
    {
        var nome = document.getElementById('nome').value;
        var tam = nome.length;
        for (var i=0; i<tam; i++)
        {
          nome = nome.replace("  ", " ");
        }
        document.form_inclusao.nome.value = nome;

        var nome_aux = nome.split(" ");
        var aux_pos = nome.length - 1;
    	var pos = nome_aux.length;
        var espaco = false;
       
        var aux = nome.indexOf(" ");

        if (aux_pos == aux)
           espaco = true;
        
        if ((nome_aux[1]==undefined)||((nome_aux[pos-1]=='') && (espaco== true))){
         if(confirm('VocÍ informou apenas um nome. Esta consulta poder· demorar muito tempo.Tem certeza que deseja continuar?'))
            popup_paciente();
         }
         else popup_paciente();
    }
    else popup_paciente();
}


function popup_paciente()
{


    //document.form_inclusao.pesquisar.disabled=true;
	var height = 500;
	var width = 900;
	
    var cartao_tela = document.form_inclusao.cartao_sus.value;
    var nome_tela   = document.form_inclusao.nome.value;
    var mae_tela    = document.form_inclusao.nome_mae.value;
    var data_tela   = document.form_inclusao.data_nasc.value;
    var cpf         = document.form_inclusao.cpf.value;
    var prontuario  = document.form_inclusao.prontuario.value;

	var left = ((screen.availWidth - width))/2;
	var top = (screen.availHeight - height)/2;
    var caminho = "pesquisa_paciente.php?cartao_tela="+cartao_tela+"&cpf_tela="+cpf+"&pront_tela="+prontuario+"&nome_tela="+nome_tela+"&mae_tela="+mae_tela+"&data_tela="+data_tela;
    if ((nome_tela!='')||(cartao_tela!='')||(prontuario!='')||(cpf!=''))
    {
     if (window.showModalDialog)
	 {
		var dialogArguments = new Object();
		var _R = window.showModalDialog(caminho, dialogArguments, "dialogWidth=900px;dialogHeight=500px;dialogTop=100px;dialogLeft=60px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNamePaciente(_R.strArgs);
		}
	 }
	 else	//NS
	 {
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open(caminho, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
  	 }
   }

   //document.form_inclusao.pesquisar.disabled=false;
}

function SetNamePaciente(argumentos)
{
   if(argumentos != 'limpar')
   {

     document.form_inclusao.flg_paciente.value = '1';
     document.form_inclusao.id_paciente.value = argumentos;

     document.form_inclusao.cartao_sus.disabled=true;
     document.form_inclusao.nome.disabled=true;
     document.form_inclusao.nome_mae.disabled=true;
     document.form_inclusao.data_nasc.disabled=true;
     document.form_inclusao.prontuario.disabled=true;
     document.form_inclusao.cpf.disabled=true;
	// alert('antes do focus....');

     document.form_inclusao.medicamento01.focus();
	 //alert('logo depois....');

    document.form_inclusao.data_emissao.focus();
   }
   else limpar_campos_receita();

}

function TrimJS(){

String = document.form_inclusao.medicamento01.value;
Resultado = String

//Retira os espaÁos do inicio
//Enquanto o primeiro caracter for igual ‡ "EspaÁo"
//1 caracter do inicio È removido

var i
i = 0

if (Resultado.charCodeAt(2-1) == '32'){
}

while (Resultado.charCodeAt(0) == '32'){
   Resultado = String.substring(i,String.length);
  i++;}

//Pega a string j· formatada e agora retira os espaÁos do final
//mesmo esquema, enquanto o ultimo caracter for um espaÁo,
//ele retira 1 caracter do final...

while(Resultado.charCodeAt(Resultado.length-1) == "32"){
   Resultado = Resultado.substring(0,Resultado.length-1);
  }

document.form_inclusao.medicamento01.value = Resultado;

String = ""

}

function imprimir_recibo()
{
   var url;

   url = "<?= URL?>/modulos/consulta/v2_recibo_receita_imp.php?id_receita="+document.form_inclusao.id_receita.value;
   if (confirm("Deseja imprimir recibo?"))
   {
	if (window.showModalDialog)
	{
		window.showModalDialog(url, null, "dialogWidth=800px;dialogHeight=600px;scroll=yes;status=no;");
	}
	else	//NS
	{
    	var height = 500;
	    var width = 900;
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open(url, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
   }
}


function visualizar_receita(receita, ano, id_unidade, numero, situacao, paciente, prescritor)
{

	var height = 600;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	var url;

    url = "<?= URL?>/modulos/consulta/saida_por_usuario.php?id_receita="+receita+"&ano="+ano+"&id_unidade="+id_unidade+"&numero="+numero+"&situacao="+situacao+"&paciente="+paciente+"&prescritor="+prescritor;


	if (window.showModalDialog)
	{

		window.showModalDialog(url, null, "dialogWidth=1000px;dialogHeight=600px;scroll=yes;status=no;");
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open(url, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

  function desabilitar_lixeira()
  {
    var linha = document.getElementById("lista_dispensados");
    total_linhas = document.getElementById("lista_dispensados").rows.length;
    for (i=1; i<total_linhas; i++)
    {
      linha.rows[i].cells[6].innerHTML = "";
    }

  }

  var cont = 0;
  function insere_linhas()
  {
      var todos_meds ='';
      var med = document.form_inclusao.medicamento01.value;

      var achou_medicamento = false;

      document.getElementById("cabec_lista_dispensados").style.display = 'inline';

      var itens = document.getElementById("lista_dispensados");
      total_linhas = document.getElementById("lista_dispensados").rows.length;

      var h_itens = document.getElementById("hidden_lista");
      h_total_linhas = document.getElementById("hidden_lista").rows.length;

      for (i=1; i<total_linhas; i++)
      {
        if (itens.rows[i].cells[0].innerHTML == med)
        {
          achou_medicamento = true;
          break;
        }
      }

      if (!achou_medicamento)
      {
        var existe_estoque = false;
        var vai_dispensar = false;

        //verificar se existe estoque para o medicamento selecionado
        for (var i=0;i<document.form_inclusao.elements.length;i++)
        {
         var x = document.form_inclusao.elements[i];
         if (x.name == 'dispensar')
         {
          existe_estoque = true;
          break;
         }
        }

        if (existe_estoque)
        {
         //verificar se vai ser dispensada alguma qtde do medicamento
         for (var i=0;i<document.form_inclusao.elements.length;i++)
         {
          var x = document.form_inclusao.elements[i];
          if (x.name == 'dispensar')
          {
           if ((x.value != '')&&(parseInt(x.value,10) != 0))
           {
            vai_dispensar = true;
            break
           }
          }
         }
        }

        if (vai_dispensar)
        {
         for (var i=0;i<document.form_inclusao.elements.length;i++)
         {
          var x = document.form_inclusao.elements[i];
          if (x.name == 'dispensar')
          {
           if ((x.value != '')&&(parseInt(x.value,10) != 0))
           {

            var texto = x.id;
            var id_estoque = texto.substr(4,texto.length);

            var id_medicamento = document.form_inclusao.medicamento.value;

            var lote = document.getElementById('lot'+ id_estoque);
            var fabricante = document.getElementById('fab'+ id_estoque);
            var id_fabricante = document.getElementById('idf'+ id_estoque);
            var validade = document.getElementById('val'+ id_estoque);
            var qtde_lote = document.getElementById('disp'+ id_estoque);

            pos = document.getElementById('lista_dispensados').rows.length;
            var tab = document.getElementById("lista_dispensados").insertRow(pos);
            tab.id = "linha"+cont;
            tab.className = "descricao_campo_tabela";

            h_pos = document.getElementById('hidden_lista').rows.length;
            var h_tab = document.getElementById("hidden_lista").insertRow(h_pos);
            h_tab.id = "linha"+cont;
            h_tab.className = "descricao_campo_tabela";

            var a = tab.insertCell(0);   //medicamento
            var b = tab.insertCell(1);   //lote
            var c = tab.insertCell(2);   //fabricante
            var d = tab.insertCell(3);   //validade
            var e = tab.insertCell(4);   //qtde_dispensar
            var f = tab.insertCell(5);   //bolinha
            var g = tab.insertCell(6);   //lixo

            a.align = "left";
            b.align = "center";
            c.align = "left";
            d.align = "center";
            e.align = "right";
            f.align = "center";
            g.align = "center";

            a.innerHTML = document.form_inclusao.medicamento01.value;
            b.innerHTML = lote.value;

            var simbolo=/&/gi;
            var palavra=fabricante.value;
            palavra=palavra.replace(simbolo, '&amp;');

            c.innerHTML = palavra;
            d.innerHTML = validade.value;
            e.innerHTML = qtde_lote.value;

            var Site = "<img name='imagem_lixo' src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
            var url = "JavaScript:removeLinhas('linha"+cont+"',"+document.form_inclusao.medicamento.value+"," + qtde_lote.value +")";

            g.innerHTML = Site.link(url);

            var h_a = h_tab.insertCell(0); //id_medicamento
            var h_b = h_tab.insertCell(1); //id_estoque
            var h_c = h_tab.insertCell(2); //qtde_lote
            var h_d = h_tab.insertCell(3); //qtde_prescrita
            var h_e = h_tab.insertCell(4); //tempo_tratamento
            var h_f = h_tab.insertCell(5); //qtde_anterior
            var h_g = h_tab.insertCell(6); //qtde_dispensada
            var h_h = h_tab.insertCell(7); //rec_controlada
            var h_i = h_tab.insertCell(8); //id_autorizador

            h_a.innerHTML = id_medicamento;

            h_b.innerHTML = id_estoque;
            h_c.innerHTML = qtde_lote.value;
            h_d.innerHTML = document.form_inclusao.qtde_prescrita.value;
            h_e.innerHTML = document.form_inclusao.tempo_tratamento.value;
            h_f.innerHTML = document.form_inclusao.anterior.value;
            h_g.innerHTML = document.form_inclusao.qtde_dispensar.value;
            h_h.innerHTML = document.form_inclusao.rec_controlada.value;
            h_i.innerHTML = document.form_inclusao.flg_autorizador.value;

            cont++;


           }//x.value != '' && x.value != '0'

          }//name == 'dispensar'
         }//for
        }//vai_dispensar

        else

        { //n„o vai dispensar / n„o existe estoque

          pos = document.getElementById('lista_dispensados').rows.length;
          var tab = document.getElementById("lista_dispensados").insertRow(pos);
          tab.id = "linha"+cont;
          tab.className = "descricao_campo_tabela";

          h_pos = document.getElementById('hidden_lista').rows.length;
          var h_tab = document.getElementById("hidden_lista").insertRow(h_pos);
          h_tab.id = "linha"+cont;
          h_tab.className = "descricao_campo_tabela";

          var a = tab.insertCell(0);   //medicamento
          var b = tab.insertCell(1);   //lote
          var c = tab.insertCell(2);   //fabricante
          var d = tab.insertCell(3);   //validade
          var e = tab.insertCell(4);   //qtde_dispensar
          var f = tab.insertCell(5);   //bolinha
          var g = tab.insertCell(6);   //lixo

          a.align = "left";
          b.align = "center";
          c.align = "left";
          d.align = "center";
          e.align = "right";
          f.align = "center";
          g.align = "center";

          var h_a = h_tab.insertCell(0) ; //id_medicamento
          var h_b = h_tab.insertCell(1) ; //id_estoque
          var h_c = h_tab.insertCell(2) ; //qtde_lote
          var h_d = h_tab.insertCell(3) ; //qtde_prescrita
          var h_e = h_tab.insertCell(4) ; //tempo_tratamento
          var h_f = h_tab.insertCell(5) ; //qtde_anterior
          var h_g = h_tab.insertCell(6) ; //qtde_dispensada
          var h_h = h_tab.insertCell(7) ; //rec_controlada
          var h_i = h_tab.insertCell(8) ; //id_autorizador

          var id_medicamento = document.form_inclusao.medicamento.value;

          a.innerHTML = document.form_inclusao.medicamento01.value;
          b.innerHTML = '--';
          c.innerHTML = '--';
          d.innerHTML = '--';
          e.innerHTML = '0';

          var bolinha = "<img src='<?php echo URL;?>/imagens/bolinhas/ball_amarela.gif' border='0' alt='Sem estoque'>";

          f.innerHTML = bolinha;

          var Site = "<img name='imagem_lixo' src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
          var url = "JavaScript:removeLinhas('linha"+cont+"',"+document.form_inclusao.medicamento.value+",0)";
          g.innerHTML = Site.link(url);

          h_a.innerHTML = id_medicamento ;
          h_b.innerHTML = '0';
          h_c.innerHTML = '0';
          h_d.innerHTML = document.form_inclusao.qtde_prescrita.value ;
          h_e.innerHTML = document.form_inclusao.tempo_tratamento.value ;
          h_f.innerHTML = document.form_inclusao.anterior.value ;
          h_g.innerHTML = '0' ;
          h_h.innerHTML = document.form_inclusao.rec_controlada.value ;
          h_i.innerHTML = document.form_inclusao.flg_autorizador.value ;

          cont++;

        }

      for (i=1; i<h_total_linhas; i++)
      {
         todos_meds=h_itens.rows[i].cells[0].innerHTML+"|"+todos_meds;
      }
      document.form_inclusao.todos_med.value=todos_meds + document.form_inclusao.medicamento.value;
      
      var x=document.form_inclusao;
      if(x.flag_mostrar_responsavel_dispensacao.value!="S"){
        document.form_inclusao.salvar.disabled = false;
      }
        limpar_campos();

      }
      else    //achou medicamento
      {
        alert('Medicamento j· adicionado!')
        limpar_campos();
      }
  }

function removeLinhas(lnh, cod, qtde)
{
  document.getElementById("lista_dispensados").deleteRow(document.getElementById(lnh).rowIndex);
  document.getElementById("hidden_lista").deleteRow(document.getElementById(lnh).rowIndex);
  if (document.getElementById('hidden_lista').rows.length==1)
  {
   document.getElementById("cabec_lista_dispensados").style.display = 'none';

   var x=document.form_inclusao;
   if(x.flag_mostrar_responsavel_dispensacao.value!="S"){
     document.form_inclusao.salvar.disabled = true;
   }
   document.form_inclusao.medicamento01.focus();
  }
  
  var i=0;
  var alt_tab = document.getElementById("hidden_lista");
  var alt_tab_total_linhas = document.getElementById("hidden_lista").rows.length;

  for (i=1; i<alt_tab_total_linhas; i++)
  {
    var teste = alt_tab.rows[i].cells[0].innerHTML;
    if (teste==cod)
    {
     var val_disp = alt_tab.rows[i].cells[6].innerHTML;
     alt_tab.rows[i].cells[6].innerHTML = val_disp-qtde;
    }

  }
  
//number(alt_tab.rows[i].cells[3].innerHTML
}

function salvar_receita()
{
 //o campo cidade eh obrigatorio quando habilitado
 var cidadeObrigatorio = !document.form_inclusao.cidade_receita.disabled;
 var cidadeReceita = document.form_inclusao.cidade_receita;
 var idCidadeReceita = document.form_inclusao.id_cidade_receita;
 
 if ((cidadeObrigatorio == true) && (cidadeReceita.value == "" || idCidadeReceita == "" || idCidadeReceita == 0))
 {
  alert ('Favor preencher os campos obrigatÛrios: Cidade');
  cidadeReceita.focus();
  return false;
 }

 if (document.form_inclusao.data_emissao.value == '')
 {
  alert ('Favor preencher os campos obrigatÛrios!');
  document.form_inclusao.data_emissao.focus();
  return false;
 }

 if (document.form_inclusao.origem_receita.value == '')
 {
  alert ('Favor preencher os campos obrigatÛrios!');
  document.form_inclusao.origem_receita.focus();
  return false;
 }

 if (document.form_inclusao.id_prescritor.value == '')
 {
  alert ('Favor preencher os campos obrigatÛrios!');
  document.form_inclusao.inscricao.focus();
  return false;
 }

 if (document.getElementById('hidden_lista').rows.length==1)
 {
  //alert ('Pelo menos um medicamento deve ser adicionado a receita!');
  alert ('Pelo menos um material/medicamento deve ser adicionado a receita!');
  document.form_inclusao.medicamento01.focus();
  return false;
 }

 if (document.form_inclusao.medicamento.value!='')
 {
  //alert ('Existe um medicamemto que n„o foi adicionado na lista!');
  alert ('Existe um material/medicamento que n„o foi adicionado na lista!');
  document.form_inclusao.adicionar.focus();
 }

 var data = document.form_inclusao.data_emissao.value;
 var erro= false;
 var dia, mes, ano;

 if (data.indexOf(" ")+data.indexOf(".")+data.indexOf("-")+data.indexOf("+")==-4)
 {
  dia = data.substring(0,2);
  if (dia.charAt(1)=="/")
  {
   data = "0" + data;
   dia  = dia.charAt(0);
  }
  if (!isNaN(dia) && dia>=1 && dia<=31)
   if (data.charAt(2)=="/")
   {
    mes = data.substring(3,5);
    if (mes.charAt(1)=="/")
    {
     data = data.substring(0,3) + "0" + data.substring(3,data.length);
     mes  = mes.charAt(0);
     erro = true;
    }
    if (!isNaN(mes) && mes>=1 && mes<=12)
     if (data.charAt(5)=="/")
     {
      ano = data.substring(6,data.length);
	  if (!isNaN(ano) && ano.length!=3)
	   erro = true;
	 }
   }
 }
 if (!erro)
 {
  alert ("A data fornecida foi preenchida incorretamente.");
  document.form_inclusao.data_emissao.focus();
  document.form_inclusao.data_emissao.select();
  return false;
 }
 else
 {
   ano = parseInt(ano,10);
   if (ano<4)
   {
    ano += 2000;
   }
   else
   {
    if (ano<100)
    {
     ano += 1900;
     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11))
     {
      alert ("Este mÍs n„o possui mais de 30 dias.");
      erro = true;
     }
	}
    else
    {
     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11))
     {
      alert ("Este mÍs n„o possui mais de 30 dias.");
      erro = false;
     }
     if (mes==2)
     {
      if (dia>29)
      {
       alert ("Fevereiro n„o pode conter mais de 29 dias.");
       erro = false;
      }
      else
       if (dia==29 && ano%4!=0)
       {
        alert ("Este n„o È um ano bissexto.");
        erro = false;
       }
     }
     if (!erro)
     {
      document.form_inclusao.data_emissao.focus();
      document.form_inclusao.data_emissao.select();
      return false;
     }
     else
     {
      limpar_estoque();
      limpar_ultima_saida();
      document.form_inclusao.medicamento01.value = '';
      document.form_inclusao.medicamento.value = '';
      document.form_inclusao.unidade.value = '';
      document.form_inclusao.salvar.disabled = true;
      document.form_inclusao.limpar.disabled = true;
      salvar_valida_prescritor_medicamento();
     }
    }
   }
  }

}

function acertar_dados_salvar()
{
   var itens=document.getElementById("hidden_lista");

   var total_linhas=itens.rows.length;
   var lista=new Array(total_linhas);

  for(var i=1; i<lista.length; i++)
  {
    lista[i]=new Array(9);
  }

  var info='';
  for(var i=1; i<lista.length; i++)
  {
   for(var j=0; j<lista[i].length; j++)
   {
      info+=itens.rows[i].cells[j].innerHTML + ',';
   }
   info = info.substr(0,(info.length)-1);
   info=info + '|';
  }
  info = info.substr(0,(info.length)-1);

  document.getElementById('itens_receita').value=info;
}

function limpar_campos_receita()
{
    var x=document.form_inclusao;
    
    x.login.value="";
    x.senha.value="";
    x.salvar.disabled=true;

    x.novo.disabled=false;
    x.numero_receita.value='';
    x.id_receita.value='';
    x.id_prescritor.value='';
    x.inscricao.value='';
    x.id_tipo_prescritor.value='';
    x.id_paciente.value='';
    x.flg_paciente.value='';
    x.flg_material.value='';
    x.flg_autorizador.value='';
    x.medicamento.value='';
    x.medicamento01.value='';
    x.unidade.value='';
    x.tempo_tratamento.value='';
    x.prescritor.value='';
    document.getElementById("prescritor").selectedIndex = 0;
    x.nome_mae.value='';
    x.nome.value='';
    x.cartao_sus.value='';
    x.data_nasc.value='';
    x.prontuario.value='';
    x.cpf.value='';
    x.itens_receita.value='';
    x.todos_med.value='';
    x.flag_salvar.value='';
    x.qtde_prescrita.value='';
    x.tempo_tratamento.value='';
    x.qtde_dispensar.value='';
    x.anterior.value='0';

    document.getElementById("grupo").selectedIndex = 0;

    x.nome.disabled=false;
    x.nome_mae.disabled=false;
    x.cartao_sus.disabled=false;
    x.data_nasc.disabled=false;
    x.cpf.disabled=false;
    x.prontuario.disabled=false;
    x.nome.focus();
    
/*    if(document.getElementById("origem_receita").options[2].text == 'LOCAL')
    {
        document.getElementById("origem_receita").selectedIndex = 2;
    }
    else */

    document.getElementById("origem_receita").selectedIndex = 0;
    
    if(x.aux_id_cidade_receita.value != x.id_cidade_receita.value)
    {
      x.id_cidade_receita.value=x.aux_id_cidade_receita.value;
      x.cidade_receita.value=x.aux_cidade_receita.value;
    }

    document.form_inclusao.data_emissao.value = "";
    limpar_ultima_saida();
    limpar_estoque();
    carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
    
    var cont = document.getElementById('hidden_lista').rows.length;
    while (cont >1)
    {
       document.getElementById('lista_dispensados').deleteRow(2);
       document.getElementById('hidden_lista').deleteRow(1);
       cont--;
    }
}

//glaison inicio funÁ„o limpar receita reaproveitando o nome do paciente


function bot_nova_receita_mpaciente(){
   var x=document.form_inclusao;
  var paciente_compl = x.id_paciente_sessao.value;
   //var paciente_compl = <?php echo $paciente_compl;?>;
  // alert ('paccc '+paciente_compl);
   
   if (paciente_compl != ""){
   
   SetNamePaciente(paciente_compl);
   } else {
		var x=document.form_inclusao;
		

		x.numero_receita.value='';
		x.id_receita.value='';
		x.id_prescritor.value='';
		x.inscricao.value='';
		x.id_tipo_prescritor.value='';
		
		x.id_paciente.value= x.aux_id_paciente.value ;

		x.flg_paciente.value='';
		x.flg_material.value='';
		x.flg_autorizador.value='';
		x.medicamento.value='';
		x.medicamento01.value='';
		x.tempo_tratamento.value='';
		x.prescritor.value='';
		document.getElementById("prescritor").selectedIndex = 0;


		x.nome_mae.value= x.aux_nome_mae.value ;

		x.nome.value = x.aux_nome.value;

		x.cartao_sus.value = x.aux_cartao_sus.value;
		x.data_nasc.value = x.aux_data_nasc.value;
		x.cpf.value = x.aux_cpf.value;
		x.prontuario.value = x.aux_prontuario.value ;


		x.itens_receita.value='';
		x.todos_med.value='';
		x.flag_salvar.value='';
		x.origem_receita.disabled = false;
		x.inscricao.disabled = false;
		x.prescritor.disabled = false;
		x.medicamento01.disabled = false;
		x.qtde_prescrita.disabled = false;
		x.tempo_tratamento.disabled = false;
		x.qtde_dispensar.disabled = false;
		x.novo.disabled = false;


		x.pesquisar.disabled = false;
		x.data_emissao.disabled = false;
		x.salvar.style.visibility = "visible";
		x.imagem_cidade.style.visibility = "visible";
		x.imagem_prescritor.style.visibility = "visible";
		x.imagem_medicamento.style.visibility = "visible";

		x.nome.disabled=true;
		x.nome_mae.disabled=true;
		x.cartao_sus.disabled=true;
		x.data_nasc.disabled=true;
		x.cpf.disabled=true;
		x.prontuario.disabled=true;
		x.data_emissao.focus();

		x.limpar.disabled=false;
		



		if(x.flag_mostrar_responsavel_dispensacao.value=="S"){
		  document.getElementById("mostrar_responsavel_dispensacao").style.display="";
		}

		if(x.aux_id_cidade_receita.value != x.id_cidade_receita.value)
		{
		  x.id_cidade_receita.value=x.aux_id_cidade_receita.value;
		  x.cidade_receita.value=x.aux_cidade_receita.value;
		}
		//alert ('prescritor '+ x.prescritor.value);
		esconde_botao();
		buscar_inscricao();
		document.getElementById("origem_receita").selectedIndex = 0;
		document.form_inclusao.data_emissao.value = "";
		var cont = document.getElementById('hidden_lista').rows.length;
		while (cont >1)
		{
		   document.getElementById('lista_dispensados').deleteRow(2);
		   document.getElementById('hidden_lista').deleteRow(1);
		   cont--;
		}
	   document.getElementById('cabec_lista_dispensados').style.display='none';
	   carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
	}
}


//glaison fim  funÁ„o limpar receita reaproveitando o nome do paciente


function limpar_campos()
{
 limpar_ultima_saida();
 limpar_receita_controlada();
 limpar_estoque();

 document.form_inclusao.medicamento01.value='';
 document.form_inclusao.medicamento.value='';
 document.form_inclusao.unidade.value='';
 document.form_inclusao.flg_autorizador.value = '';

 document.form_inclusao.qtde_prescrita.value='';
 document.form_inclusao.tempo_tratamento.value='';
 document.form_inclusao.qtde_dispensar.value='';
 document.form_inclusao.anterior.value='0';

}

function monta_lista()
{
  insere_linhas();
}

function header_medicamentos_dispensados()
{
    var url = "../../xml_dispensacao/header_medicamentos_dispensados.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraHeader_medicamentos_dispensados
    });
}

function medicamentos_dispensados()
{
    var url = "../../xml_dispensacao/medicamentos_dispensados.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraMedicamentos_dispensados
    });
}

function mostraHeader_medicamentos_dispensados(cabec)
{
    var div = document.getElementById("cabec");
    div.innerHTML = cabec.responseText;
}

function mostraMedicamentos_dispensados(lista)
{
    var div = document.getElementById("lista");
    div.innerHTML = lista.responseText;

}

function receita_controlada()
{
    var url = "../../xml_dispensacao/receita_controlada.php?material="+document.form_inclusao.medicamento.value;
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraNRControlada
    });
}

function mostraNRControlada(controlada)
{
    var div = document.getElementById("controlada");
    div.innerHTML = controlada.responseText;
    if (document.form_inclusao.rec_controlada.type=='hidden')
    {
     if (document.form_inclusao.medicamento01.value=='')
     {
      document.form_inclusao.medicamento01.focus();
     }
     else
     {
      if (document.form_inclusao.medicamento01.value=='')
      {
       document.form_inclusao.medicamento01.focus();
      }
      else
      {
       document.form_inclusao.qtde_prescrita.focus();
      }
     }
    }
    else
    {
     document.form_inclusao.rec_controlada.focus();
    }
}

function limpar_receita_controlada()
{
    var url = "../../xml_dispensacao/limpar_receita_controlada.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraNRControlada
    });
}

function limpar_estoque()
{
    var url = "../../xml_dispensacao/limpar_estoque.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraResposta
    });
}

function buscar_estoque()
{
    var url = "../../xml_dispensacao/buscar_estoque.php?material="+document.form_inclusao.medicamento.value;
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraResposta
    });
}

function mostraResposta(resposta)
{
    //alert(resposta.responseText);
    var div = document.getElementById("resposta");
    div.innerHTML = resposta.responseText;
    //ultima_saida();
}

function ultima_saida()
{

    var url = "../../xml_dispensacao/ultima_saida.php?material="+document.form_inclusao.medicamento.value
                                                   +"&paciente="+document.form_inclusao.id_paciente.value;
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraSaida
    });
}

function limpar_ultima_saida()
{
    var url = "../../xml_dispensacao/limpar_ultima_saida.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraSaida
    });
}

function mostraSaida(saida)
{
    var div = document.getElementById("saida");
    div.innerHTML = saida.responseText;
}

function valida_receita()
{
    var tab=document.getElementById("hidden_lista");
    var total_itens=tab.rows.length-1;
    var itens = '';
    for (i=1; i<=total_itens; i++)
    {
      itens = itens + tab.rows[i].cells[0].innerHTML + '|';
    }
    itens = itens.substr(0,itens.length-1);
    var url = "../../xml_dispensacao/valida_receita.php?paciente="+document.form_inclusao.id_paciente.value
              +"&prescritor="+document.form_inclusao.id_prescritor.value
              +"&data="+document.form_inclusao.data_emissao.value
              +"&itens="+itens
              +"&total_itens="+total_itens;
    requisicaoHTTP("GET", url, true, '');
}

function procura_medicamento_nome()
{
    var medicamento = document.form_inclusao.medicamento01.value;
    var tam = medicamento.length;
    
    for (var i=0; i<tam; i++)
    {
       medicamento = medicamento.replace("+","~");
    }

    var url = "../../xml_dispensacao/procura_medicamento_nome.php?descricao="+medicamento;
    requisicaoHTTP("GET", url, true, '');

}

function validaNomeCidade()
{
    var descCidade = document.form_inclusao.cidade_receita.value;
    var idCidade = document.form_inclusao.id_cidade_receita.value;
	descCidade = descCidade.replace('/', '_');

    var url = "../../xml_dispensacao/procura_cidade.php?id="+idCidade+"&descricao="+descCidade;
    requisicaoHTTP("GET", url, true, '');
}

function valida_prescritor_medicamento()
{
//alert('valida_prescritor_medicamento');
    var url = "../../xml_dispensacao/valida_prescritor_medicamento.php?material="+document.form_inclusao.medicamento.value
              +"&tipo_prescritor="+document.form_inclusao.id_tipo_prescritor.value;
    requisicaoHTTP("GET", url, true, '');
}

function salvar_valida_prescritor_medicamento()
{

    var url = "../../xml_dispensacao/salvar_valida_prescritor_medicamento.php?materiais="+document.form_inclusao.todos_med.value
              +"&tipo_prescritor="+document.form_inclusao.id_tipo_prescritor.value;
    requisicaoHTTP("GET", url, true, '');
}

function s_validade_receita()
{
    var url = "../../xml_dispensacao/validade_receita.php?material="+document.form_inclusao.todos_med.value
            +"&data="+document.form_inclusao.data_emissao.value;
    requisicaoHTTP("GET", url, true, '');
}

function validade_receita()
{
    var url = "../../xml_dispensacao/validade_receita.php?material="+document.form_inclusao.medicamento.value
            +"&data="+document.form_inclusao.data_emissao.value;
    requisicaoHTTP("GET", url, true, '');
}

function autorizacao_receita_vencida()
{
    var url = "../../xml_dispensacao/autorizacao_receita_vencida.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true, '');
}

function precisa_autorizador()
{
    var url = "../../xml_dispensacao/precisa_autorizador.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true, '');
}

function precisa_autorizador_receita_vencida()
{
    var url = "../../xml_dispensacao/precisa_autorizador_receita_vencida.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true, '');
}

function proc_salvar_receita()
{

 var f = document.form_inclusao;
 var num_controle = f.num_controle.value;
 var ano = f.ano_tela.value;
 var unidade = f.unidade_tela.value;
 var data_emissao = f.data_emissao.value;
 var origem = f.origem_receita.value;
 var cidade = f.id_cidade_receita.value;
 var paciente = f.id_paciente.value;
 var prescritor = f.id_prescritor.value;
 var itens_receita = f.itens_receita.value;

 var id_login=document.form_inclusao.id_login.value;
 var url = "../../xml_dispensacao/proc_salvar_receita.php?num_controle="+num_controle
           + "&ano="+ano
           + "&unidade="+unidade
           + "&data_emissao="+ data_emissao
           + "&origem="+ origem
           + "&cidade="+ cidade
           + "&prescritor="+ prescritor
           + "&paciente="+ paciente
           + "&itens_receita="+ itens_receita
           + "&id_login="+id_login;
		
 requisicaoHTTP("GET", url, true, '');
}

function trataDados()
{
	var info = ajax.responseText;  // obtÈm a resposta como string
    var variavel=info;
    v="RIS";
    e="Erro ";

//se no retorno ajax existir a string 'RIS' - receita foi incluÌda com sucesso
    x=variavel.indexOf(v);

//se no retorno ajax existir a string 'ERRO' - erro na inclus„o da receita
    er=variavel.indexOf(e);
	
	//retorno da validacao da cidade (cidade nao encontrada)
	if (info == 'cidade_not_found') {
		window.alert('Cidade n„o encontrada!');
		document.getElementById('cidade_receita').value = '';
		document.getElementById('id_cidade_receita').value = '';
		document.getElementById('cidade_receita').focus();
		return;
	}
	
	//retorno da validacao da cidade (ok)
	if (info == 'cidade_found') {
      document.getElementById('inscricao').focus();
		return; //ok
	}

//quando o browser enviar mais de uma vez a tela para inclus„o
   if (info=='duplicacao_browser')
   {
    alert("Houve tentativa de reincidÍncia ao completar a receita. \n Verifique se esta operaÁ„o foi realizada com sucesso!");
    document.form_inclusao.salvar.disabled = false;
    return
   }
   
   if ((x==-1)&&(er==-1))
   {
      var login_senha=info.split("@");
     //retorno de verificar_login_senha_responsavel_dispensacao.php
     if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
       document.form_inclusao.id_login.value=login_senha[1];
       salvar_receita();
     }
     if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
       document.form_inclusao.id_login.value=login_senha[1];
       window.alert("Login e/ou Senha para DispensaÁ„o Inv·lidos!");
       document.form_inclusao.login.focus();
       return;
     }
   
    var pos = info.indexOf("=");
    var retorno = info.substr(0,pos+1);
    if (retorno == 'mat_nao_prescritor=') //retorno de valida_prescritor_medicamento.php
	{
     var materiais=info.substr(pos+1);
     limpar_ultima_saida();
     limpar_receita_controlada();
     limpar_estoque();

	 alert('Material(is) n„o pode(m) ser dispensado(s) por esse prescritor:\n'+materiais);
	 document.form_inclusao.medicamento01.value = '';
	 document.form_inclusao.medicamento.value = '';
	 document.form_inclusao.unidade.value = '';
	 document.form_inclusao.inscricao.focus();
	 document.form_inclusao.salvar.disabled=false;
    }
    
    if (info == 'nao_prescritor') //retorno de valida_prescritor_medicamento.php
	{
     limpar_ultima_saida();
     limpar_receita_controlada();
     limpar_estoque();

	 alert('Esse medicamento n„o est· autorizado para esse prescritor');
	 limpar_estoque();
	 document.form_inclusao.medicamento01.value = '';
	 document.form_inclusao.medicamento.value = '';
	 document.form_inclusao.unidade.value = '';
	 document.form_inclusao.medicamento01.focus();
    }

    if (info == 'sim_prescritor')  //retorno de valida_prescritor_medicamento.php
    {
      //alert ('valida receita');
      validade_receita();
    }

    if (info == 's_sim_prescritor')  //retorno de valida_prescritor_medicamento.php
    {
      document.form_inclusao.flag_salvar.value='salvar_pressionado';
      acertar_dados_salvar();
      s_validade_receita();
      valida_receita();
    }
    
    if (info == 'validade_expirou')
    {
	 //if (!confirm('Receita com prazo de validade vencida. Deseja dispensar medicamento?'))
	 if (!confirm('Receita com prazo de validade vencida. Deseja dispensar iten?'))
	 {
      limpar_ultima_saida();
      limpar_receita_controlada();
      limpar_estoque();

	  document.form_inclusao.medicamento01.value = '';
	  document.form_inclusao.medicamento.value = '';
	  document.form_inclusao.unidade.value = '';
	  document.form_inclusao.medicamento01.focus();
     }
     else
     {
      autorizacao_receita_vencida();
     }
    }

    if (info == 'validade_no_prazo')
    {
        precisa_autorizador();
    }

    if (info == 'sem_estoque')
    {
        limpar_ultima_saida();
        limpar_receita_controlada();
        limpar_estoque();

        alert('Receita com prazo de validade vencida e sem quantidade em estoque.')
	    document.form_inclusao.medicamento01.value = '';
	    document.form_inclusao.medicamento.value = '';
	    document.form_inclusao.unidade.value = '';
	    document.form_inclusao.medicamento01.focus();
    }

    if (info == 'com_estoque')
    {
        precisa_autorizador_receita_vencida();
    }

    if (info == 'sim_autorizador_sem_msg')
    {
     popup_autorizador ();
     //modalWinAutorizador('nova_receita', 0);
     if (document.form_inclusao.flg_autorizador.value!='')
     {
      if (document.form_inclusao.id_paciente.value!='')
      {
       receita_controlada();
       buscar_estoque();
       ultima_saida();
      }
      else
      {
       alert ('Selecione um paciente');
       document.form_inclusao.medicamento01.value='';
       document.form_inclusao.medicamento.value='';
       document.form_inclusao.unidade.value='';
      }
     }
     else
     {
      limpar_ultima_saida();
      limpar_receita_controlada();
      limpar_estoque();
      document.form_inclusao.medicamento01.value = '';
 	  document.form_inclusao.medicamento.value = '';
 	  document.form_inclusao.unidade.value = '';
	  document.form_inclusao.flg_autorizador.value = '';
	  document.form_inclusao.medicamento01.focus();
     }
    }

    if (info == 'nao_autorizador')  // precisa_autorizador.php
    {
       if(document.form_inclusao.flag_salvar.value!='salvar_pressionado')
       {
         if (document.form_inclusao.id_paciente.value!='')
         {
          receita_controlada();
          buscar_estoque();
          ultima_saida();
         }
         else
         {
          alert ('Selecione um paciente');
          document.form_inclusao.medicamento01.value='';
          document.form_inclusao.medicamento.value='';
          document.form_inclusao.unidade.value='';
         }
       }
    }

    if (info == 'sim_autorizador')
    {
         //if (confirm('Medicamento precisa ser autorizado. Deseja dispensar o medicamento?'))
         if (confirm('Material/Medicamento precisa ser autorizado. Deseja dispensar o material/medicamento?'))
         {
          popup_autorizador();
          if (document.form_inclusao.flg_autorizador.value!='')
          {
           if (document.form_inclusao.id_paciente.value!='')
           {
            receita_controlada();
            buscar_estoque();
            ultima_saida();
           }
           else
           {
            alert ('Selecione um paciente');
            document.form_inclusao.medicamento01.value='';
            document.form_inclusao.medicamento.value='';
            document.form_inclusao.unidade.value='';
           }
          }
          else
          {
           limpar_ultima_saida();
           limpar_receita_controlada();
           limpar_estoque();
           document.form_inclusao.medicamento01.value = '';
 	       document.form_inclusao.medicamento.value = '';
 	       document.form_inclusao.unidade.value = '';
	       document.form_inclusao.flg_autorizador.value = '';
	       document.form_inclusao.medicamento01.focus();
          }
         }
         else
         {
          limpar_ultima_saida();
          limpar_receita_controlada();
          limpar_estoque();
          document.form_inclusao.medicamento01.value = '';
	      document.form_inclusao.medicamento.value = '';
	      document.form_inclusao.unidade.value = '';
	      document.form_inclusao.flg_autorizador.value = '';
 	      document.form_inclusao.medicamento01.focus();
         }
    }

    if (info == 'receita_nao_existe')
    {
     proc_salvar_receita();
    }

    if (info == 'receita_existe_verificar')
    {
     if (confirm('Receita j· foi incluÌda! Deseja incluir mesmo assim?'))
     {
      proc_salvar_receita();
     }
     else
     {
      document.form_inclusao.salvar.disabled = false;
     }
    }

    if (info == 'receita_existe')
    {
     alert('Receita j· foi incluÌda!');

     document.form_inclusao.salvar.disabled = false;
    }

    if (info.substring(0,3) == 'med')
    {
      if (info.substring(3) == 'nao')
      {
         //  alert('Medicamento Inv·lido!');
         alert('Material/Medicamento Inv·lido!');

         document.form_inclusao.medicamento01.value = ''
         document.form_inclusao.medicamento.value = '';
         document.form_inclusao.unidade.value = '';
         document.form_inclusao.qtde_prescrita.value = '';
         document.form_inclusao.tempo_tratamento.value = '';
         document.form_inclusao.anterior.value = '';
         document.form_inclusao.qtde_dispensar.value = '';
         document.form_inclusao.flg_autorizador.value = '';

         limpar_ultima_saida();
         limpar_receita_controlada();
         limpar_estoque();

         document.form_inclusao.medicamento01.focus();
         document.form_inclusao.medicamento01.select();
      }
      else
      {
        var codigos = info.substring(3);
        var vet = new Array();
        vet = codigos.split('|');
        document.form_inclusao.medicamento.value = vet[0];
        document.form_inclusao.unidade.value = vet[1];

        document.form_inclusao.flg_autorizador.value = '';
        // valida_prescritor_medicamento();
        buscar_estoque();
      }
    }

   }
   else
   {
    if ((x!=-1)&&(er==-1))
    {
     var retornoajax=info;
     var1="-";
     x=retornoajax.indexOf(var1);
     var2="|";
     y=retornoajax.indexOf(var2);
     var3="*";
     w=retornoajax.indexOf(var3);

     var pos_ris = retornoajax.indexOf("RIS-");
     var pos_ris = pos_ris + 4;

     alert('OperaÁ„o concluÌda com sucesso! \n Receita n˙mero:'+ retornoajax.substring(pos_ris,y));
	// alert ($_SESSION ['id_paciente']= $paciente); 

	//id_paciente = ;
	
	document.form_inclusao.id_paciente_sessao.value = document.form_inclusao.id_paciente.value;
	// alert (document.form_inclusao.id_paciente.value); 

     document.form_inclusao.numero_receita.value = retornoajax.substring(pos_ris,y);

     document.form_inclusao.num_controle.value = retornoajax.substring(w+1);

     document.form_inclusao.id_receita.value = retornoajax.substring(y+1,w);
     
     document.form_inclusao.data_emissao.disabled = true;
     document.form_inclusao.origem_receita.disabled = true;
     document.form_inclusao.inscricao.disabled = true;
     document.form_inclusao.prescritor.disabled = true;
     document.form_inclusao.medicamento01.disabled = true;
     
     document.form_inclusao.grupo.disabled = true;
     
     document.form_inclusao.qtde_prescrita.disabled = true;
     document.form_inclusao.tempo_tratamento.disabled = true;
     document.form_inclusao.qtde_dispensar.disabled = true;
     document.form_inclusao.cidade_receita.disabled = true;
     document.form_inclusao.imagem_cidade.style.visibility = 'hidden';
     document.form_inclusao.imagem_prescritor.style.visibility = 'hidden';
     document.form_inclusao.imagem_medicamento.style.visibility = 'hidden';
     desabilitar_lixeira();
     document.form_inclusao.novo.disabled = true;

     document.form_inclusao.cartao_sus.disabled = true;
     document.form_inclusao.nome.disabled = true;
     document.form_inclusao.nome_mae.disabled = true;
     document.form_inclusao.data_nasc.disabled = true;
     document.form_inclusao.pesquisar.disabled = true;
      document.form_inclusao.cpf.disabled = true;
     document.form_inclusao.prontuario.disabled = true;

     document.form_inclusao.salvar.style.visibility = 'hidden';
     
     document.form_inclusao.login.value="";
     document.form_inclusao.senha.value="";
     document.getElementById("mostrar_responsavel_dispensacao").style.display="none";

     limpar_ultima_saida();

     mostra_botao();

     imprimir_recibo();

    }
    else
    {
     if (er!=-1)
     {
      document.form_inclusao.flag_salvar.value='';
      
      var msg_alerta = info;
      //alert (msg_alerta);
      var texto_insuf = msg_alerta.substring(5);
      var vetor = texto_insuf.split("|");
      var msg_usr = "";
      msg_usr = "Quantidade insuficiente em estoque.\n";
      //msg_usr = msg_usr + "Medicamento   -Lote \n";
      msg_usr = msg_usr + "Material   -Lote \n";
      for (i=0;i<vetor.length;i++)
      {
        var lista = vetor[i].split(",");
        msg_usr = msg_usr + lista[0] + " - " + lista[1] + "\n";
      }
      alert (msg_usr);
      
      document.form_inclusao.salvar.disabled = false;
     }
    }
   }

}

function bot_nova_receita()
{
   
    var x=document.form_inclusao;
   
   
  // alert (x.paciente_compl.value);
  // alert(x.id_paciente_sessao.value);
	//x.paciente_compl = x.id_paciente_sessao.value;

    x.numero_receita.value='';
    x.id_receita.value='';
    x.id_prescritor.value='';
    x.inscricao.value='';
    x.id_tipo_prescritor.value='';

    x.aux_id_paciente.value = x.id_paciente.value ;

    x.id_paciente.value='';

    x.flg_paciente.value='';
    x.flg_material.value='';
    x.flg_autorizador.value='';
    x.medicamento.value='';
    x.medicamento01.value='';
    x.tempo_tratamento.value='';
    x.prescritor.value='';
    document.getElementById("prescritor").selectedIndex = 0;

    x.aux_nome_mae.value = x.nome_mae.value;
    x.nome_mae.value='';
    
    //alert (x.aux_nome_mae.value);

    x.aux_nome.value = x.nome.value;
    x.nome.value='';

    x.aux_cartao_sus.value = x.cartao_sus.value;
    x.cartao_sus.value='';

    x.aux_data_nasc.value = x.data_nasc.value;
    x.data_nasc.value='';

    x.aux_cpf.value = x.cpf.value;
    x.cpf.value='';

    x.aux_prontuario.value = x.prontuario.value;
    x.prontuario.value='';

    x.itens_receita.value='';
    x.todos_med.value='';
    x.flag_salvar.value='';
    x.origem_receita.disabled = false;
    x.inscricao.disabled = false;
    x.prescritor.disabled = false;
    
    document.getElementById("grupo").selectedIndex = 0;
    
    x.grupo.disabled =  false;
    x.medicamento01.disabled = false;
    x.qtde_prescrita.disabled = false;
    x.tempo_tratamento.disabled = false;
    x.qtde_dispensar.disabled = false;
    x.novo.disabled = false;

    x.pesquisar.disabled = false;
    x.data_emissao.disabled = false;
    x.salvar.style.visibility = "visible";
    x.imagem_cidade.style.visibility = "visible";
    x.imagem_prescritor.style.visibility = "visible";
    x.imagem_medicamento.style.visibility = "visible";
    
    x.nome.disabled=false;
    x.nome_mae.disabled=false;
    x.cartao_sus.disabled=false;
    x.data_nasc.disabled=false;
    x.cpf.disabled=false;
    x.prontuario.disabled=false;
    x.nome.focus();
    
    x.limpar.disabled=false;
    
    if(x.flag_mostrar_responsavel_dispensacao.value=="S"){
      document.getElementById("mostrar_responsavel_dispensacao").style.display="";
    }

    if(x.aux_id_cidade_receita.value != x.id_cidade_receita.value)
    {
      x.id_cidade_receita.value=x.aux_id_cidade_receita.value;
      x.cidade_receita.value=x.aux_cidade_receita.value;
    }
    //alert ('prescritor '+ x.prescritor.value);
    esconde_botao();
    buscar_inscricao();
/*    if(document.getElementById("origem_receita").options[2].text == 'LOCAL')
    {
        document.getElementById("origem_receita").selectedIndex = 2;
    }
    else*/
    document.getElementById("origem_receita").selectedIndex = 0;
    
    document.form_inclusao.data_emissao.value = "";
    var cont = document.getElementById('hidden_lista').rows.length;
    while (cont >1)
    {
       document.getElementById('lista_dispensados').deleteRow(2);
       document.getElementById('hidden_lista').deleteRow(1);
       cont--;
    }
   document.getElementById('cabec_lista_dispensados').style.display='none';
   //carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
}


function limpar_pesquisa_paciente(){
	x.id_paciente.value='';
	x.nome_mae.value='';
	x.nome.value='';
	x.cartao_sus.value='';
	x.data_nasc.value='';
	x.cpf.value='';
	x.prontuario.value='';
}




function preenche_campos()
{
 if (document.form_inclusao.prescritor.value!=0)
 {
  var str = document.form_inclusao.prescritor.value;
  var vet = new Array();
  vet = str.split('|');

  document.form_inclusao.id_prescritor.value = vet[0];
  document.form_inclusao.id_tipo_prescritor.value = vet[1];
 }
}

function buscar_inscricao()
{
 if (document.form_inclusao.prescritor.value!=0)
 {
  var str = document.form_inclusao.prescritor.value;
  var vet = new Array();
  vet = str.split('|');
  document.form_inclusao.id_prescritor.value = vet[0];
  document.form_inclusao.id_tipo_prescritor.value = vet[1];
  document.form_inclusao.inscricao.value = vet[2];

 }
 else
 {
  document.form_inclusao.prescritor.value = "";
  document.form_inclusao.inscricao.value = "";
  document.form_inclusao.id_prescritor.value = "";
  document.form_inclusao.id_tipo_prescritor.value = "";
  carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
 }
}

function retirar_brancos()
{
 if (document.form_inclusao.rec_controlada.type=="text")
 {
  var texto = document.form_inclusao.rec_controlada.value;
  var tam = texto.length;
  for (var i=0; i<tam; i++)
  {
   texto = texto.replace(" ", "");
  }
  document.form_inclusao.rec_controlada.value = texto;
  var campo = document.form_inclusao.rec_controlada;
 }
 validarNotificacao(campo);
 return
}

function validarNotificacao(campo){
    var numero = campo.value;
    var comprimento = numero.length;
    var aux = numero.charAt(0);
    var aux2 ='';
    var cont=0;

    var caracteres = ",.;/<>:?~^]}¥`[{=+-_)\\\\(*&®%$#@!'|‡ËÏÚ˘‚ÍÓÙ˚‰ÎÔˆ¸·ÈÌÛ˙„ı¿»Ã“Ÿ¬ Œ‘€ƒÀœ÷‹¡…Õ”⁄√’Á« ";
    caracteres = caracteres + '"';

    for (i = 0;i<caracteres.length;i++)
    {
        if(numero.indexOf(caracteres.charAt(i)) != -1)
        {
            var strerror = caracteres.substring(i,i+1);
            window.alert("VocÍ digitou um caracter inv·lido!");
            globalvar = campo;
            setTimeout("globalvar.focus()",250);
            globalvar.select();
            return false
        }
    }


    for (var i=1;i<comprimento;i++)
    {
        aux2 = numero.charAt(i);
        
        if (aux==aux2)
        {
           cont++;
        }
        aux = numero.charAt(i);
    }
    comprimento--;
    
    if (comprimento > 0)
    {
      if (cont == comprimento)
      {
       alert("Digite um n˙mero de noficaÁ„o v·lido!");
       globalvar = campo;
       setTimeout("globalvar.focus()",250);
       globalvar.select();
      }
    }
    else{
      if (campo.value =='0')
      {
       alert("Digite um n˙mero de noficaÁ„o v·lido!");
       globalvar = campo;
       setTimeout("globalvar.focus()",250);
       globalvar.select();
      }
    }
 }
 
function adicionar_medicamentos()
{
   var soma=0;
   var existe=false;

   //verificando se medicamento foi selecionado
   if ((document.form_inclusao.medicamento01.value == '') || (document.form_inclusao.medicamento.value == ''))
   {
      //alert ("Medicamento Inv·lido!");
      alert ("Material/Medicamento Inv·lido!");

      document.form_inclusao.medicamento01.value = ''
      document.form_inclusao.medicamento.value = '';
      document.form_inclusao.unidade.value = '';
      document.form_inclusao.qtde_prescrita.value = '';
      document.form_inclusao.tempo_tratamento.value = '';
      document.form_inclusao.anterior.value = '';
      document.form_inclusao.qtde_dispensar.value = '';
      document.form_inclusao.flg_autorizador.value = '';

      limpar_ultima_saida();
      limpar_receita_controlada();
      limpar_estoque();

      document.form_inclusao.medicamento01.focus();
      document.form_inclusao.medicamento01.select();
      return false;
   }

   //verificando se qtde_prescrita est· preenchida
   if ((parseInt(document.form_inclusao.qtde_prescrita.value,10) == 0) || (document.form_inclusao.qtde_prescrita.value == ''))
   {
      alert ('Quantidade Prescrita deve ser informada!');
      document.form_inclusao.qtde_prescrita.focus();
      document.form_inclusao.qtde_prescrita.select();
      return false;
   }

   //verificando se tempo_tratamento est· preenchido
   if ((parseInt(document.form_inclusao.tempo_tratamento.value,10) == 0) || (document.form_inclusao.tempo_tratamento.value == ''))
   {
      alert ('Tempo de Tratamento deve ser informado!');
      document.form_inclusao.tempo_tratamento.focus();
      document.form_inclusao.tempo_tratamento.select();
      return false;
   }

   //verificando se qtde a dispensar est· preenchido
   if ((parseInt(document.form_inclusao.qtde_dispensar.value,10) == 0) || (document.form_inclusao.qtde_dispensar.value == ''))
   {
     var tem_lote='nao';

     for (var i=0;i<document.form_inclusao.elements.length;i++)
     {
        var x = document.form_inclusao.elements[i];
        if (x.name == 'dispensar')
        {
          tem_lote='sim';
          //alert(x.value);
          if ((tem_lote=='sim') && ((x.value!='')&&(x.value!=0)))
          {
              alert ('Quantidade a dispensar deve ser informada!');
              document.form_inclusao.qtde_dispensar.focus();
              document.form_inclusao.qtde_dispensar.select();
              calcular_qtde_dispensar();
              return false;
          }
        }
     }
   }

   
   //verificando se qtde_dispensar È menor ou igual que a qtde_prescrita
   if (parseInt(document.form_inclusao.qtde_dispensar.value,10) > parseInt(document.form_inclusao.qtde_prescrita.value,10))
   {
      alert ('Quantidade a dispensar È maior que a quantidade prescrita!');
      document.form_inclusao.qtde_dispensar.focus();
      document.form_inclusao.qtde_dispensar.select();
      return false;
   }

   //caso exista rec_controlada, verifica se est· preenchida se o medicamento for dispensado
   if ((document.form_inclusao.rec_controlada.type=='text')
        && (parseInt(document.form_inclusao.qtde_dispensar.value,10)!=0)
        && ((document.form_inclusao.rec_controlada.value == '') || (document.form_inclusao.rec_controlada.value == '0')))
   {
      alert ('N˙mero da NotificaÁ„o deve ser informado!');
      document.form_inclusao.rec_controlada.focus();
      document.form_inclusao.rec_controlada.select();
      return false;
   }

   //verificando se existe estoque para o medicamento selecionado
   existe_estoque = false;
   for (var i=0;i<document.form_inclusao.elements.length;i++)
   {
     var x = document.form_inclusao.elements[i];
     if (x.name == 'estoque')
     {
      existe_estoque = true;
     }
   }

   //existindo estoque, verifica se soma das quantidade a dispensar dos estoques È igual a quantidade a dispensar
   if (existe_estoque)
   {
    var soma_estoque=0;
    var tem_lote='nao';
    
    for (var i=0;i<document.form_inclusao.elements.length;i++)
    {
     var x = document.form_inclusao.elements[i];
     if (x.name == 'dispensar')
     {
          tem_lote='sim';
          if (x.value=='')
          {
             soma_estoque = soma_estoque + 0;
          }
          else
          {
             soma_estoque = soma_estoque + parseInt(x.value, 10)
          }
     }
    }
    if ((tem_lote=='sim')&&(soma_estoque > parseInt(document.form_inclusao.qtde_dispensar.value,10)))
    {
     alert ('A soma dos lotes È maior que a quantidade escolhida para dispensar!');
     return false;
    }

    if ((tem_lote=='sim')&&(soma_estoque < parseInt(document.form_inclusao.qtde_dispensar.value,10)))
    {
     alert ('A soma dos lotes È menor que a quantidade escolhida para dispensar!');
     return false;
    }
   }

   //verificar se quantidade existente em estoque <= quantidade a dispensar
   if (existe_estoque)
   {
     var tam_vet=0;
     var estoque='';

     for (var i=0;i<document.form_inclusao.elements.length;i++)
     {
      var x = document.form_inclusao.elements[i];
      if (x.name == 'estoque')
      {
       estoque = estoque + x.value + '|';
       tam_vet = tam_vet + 1;
      }
     }
     estoque = estoque.substr(0,estoque.length-1);
     var vet_estoque = estoque.split("|");

     var dispensar='';
     for (var i=0;i<document.form_inclusao.elements.length;i++)
     {
      var x = document.form_inclusao.elements[i];
      if (x.name == 'dispensar')
      {
       if (x.value=='')
       {
        dispensar = dispensar + '0|';
       }
       else
       {
        dispensar = dispensar + x.value + '|';
       }
      }
     }
     dispensar = dispensar.substr(0,dispensar.length-1);
     var vet_dispensar = dispensar.split("|");

     for (var i=0;i<tam_vet;i++)
     {
       if (parseInt(vet_estoque[i],10) < parseInt(vet_dispensar[i],10))
       {
        alert ("Quantidade a dispensar por lote È maior que a quantidade existente no lote!");
        return false;
       }
     }
   }
   return true;
}

function calcular_qtde_dispensar()
{

  var qtde_prescrita = document.form_inclusao.qtde_prescrita.value;
  var tempo_tratamento = document.form_inclusao.tempo_tratamento.value;
  var qtde_dispensar = 0;
  var saldo = 0;

  if ((qtde_prescrita!=0) && (tempo_tratamento!=0))
  {
    qtde_dispensar = ((qtde_prescrita/tempo_tratamento)*30);

    if (qtde_prescrita<qtde_dispensar)
    {
     document.form_inclusao.qtde_dispensar.value = Math.round(qtde_prescrita);
    }
    else
    {
     if ((qtde_dispensar > 0) && (qtde_dispensar < 1))
     {
      qtde_dispensar = 1;
     }
     document.form_inclusao.qtde_dispensar.value = Math.round(qtde_dispensar);
    }
    document.form_inclusao.qtde_dispensar.focus();
    document.form_inclusao.qtde_dispensar.select();
    return;
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
           globalvar = campo;
           setTimeout("globalvar.focus()",250);
           campo.select();
        }
   }
}

function habilitaCampoCidade(elemento) {
	<?
		$sql = "select c.id_cidade, concat(c.nome,'/',e.uf) as nome
			 from cidade c, estado e, parametro p
			 where p.cidade_id_cidade = c.id_cidade
			 and c.estado_id_estado = e.id_estado";

		 $dados_cidade_receita = mysqli_fetch_object(mysqli_query($db, $sql));
		 $cidade_receita = $dados_cidade_receita->nome;
		 $id_cidade_receita = $dados_cidade_receita->id_cidade;
	?>
	var idCidadeParam = "<?echo $id_cidade_receita;?>";
	var descCidadeParam = "<?echo $cidade_receita;?>";
	var index = elemento.selectedIndex;
	var exibirCidade = elemento[index].getAttribute('cidade');
	var campoCidade = document.getElementById('cidade_receita');
	var idCampoCidade = document.getElementById('id_cidade_receita');
	var imagemCidade = document.getElementById('cidade_img');

	if (exibirCidade == 'N') {
		campoCidade.removeAttribute('disabled');
		campoCidade.value = '';
		idCampoCidade.value = '';
		imagemCidade.src='<?php echo URL; ?>/imagens/obrigat.gif';
		campoCidade.focus();
	} else {
		SetName(idCidadeParam, descCidadeParam);
		campoCidade.setAttribute('disabled', '');
		imagemCidade.src='<?php echo URL; ?>/imagens/obrigat_1.gif';
	}
}

</script>

<script language="JavaScript" type="text/javascript" src = "../../scripts/scripts.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/combo_dispensacao.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/prototype.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/auto_completar_dispensacao.js"></script>
<script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>

    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>

      <tr>
        <td align="left" valign="top">
           <body onload="esconde_botao();habilitaBotaomanter();" > 
          
          
          <form name="form_inclusao">
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr class="titulo_tabela" height="15">
             <td valign="middle" align="center" width="100%"> Paciente </td>
             <input type="hidden" name="num_controle" id="num_controle" value="<?php echo $num_controle;?>">
            </tr>
          </table>
          
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">

                <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Cart„o SUS
              </td>
              <td class="campo_tabela" valign="middle" width="40%">
               <input type="hidden" id="aux_id_paciente" name="aux_id_paciente" size="15">
			    <input type="hidden" id="id_paciente_sessao" name="id_paciente_sessao" value="<?php echo $paciente_compl;?>">
			   
                <input type="hidden" id="aux_cartao_sus" name="aux_cartao_sus" size="15">
                <input type="text" id="cartao_sus" name="cartao_sus" size="15"  maxlength="15" onKeyPress="return numbers(event);">
          	  
			  </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Prontu·rio
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
                 <input type="hidden" id="aux_prontuario" name="aux_prontuario" size="15">
                <input type="text" id="prontuario" name="prontuario" size="15"  maxlength="15">
              </td>
            </tr>
            
            </tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Nome
              </td>
              <td class="campo_tabela" valign="middle" width="40%">
                <input type="hidden" id="flg_paciente" name="flg_paciente" size="10">

                <input type="hidden" id="id_paciente" name="id_paciente" >
                <input type="hidden" id="aux_nome" name="aux_nome" size="60">
                <input type="text" id="nome" name="nome" size="60"  maxlength="70">
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>CPF
              </td>
              <td class="campo_tabela" valign="middle" width="15%" >
               <input type="hidden" id="aux_cpf" name="aux_cpf" size="15">
                <input type="text" id="cpf" name="cpf" size="15"  maxlength="15" onKeyPress="return numbers(event);">
              </td>
              </td>
            </tr>

           <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Nome M„e
              </td>
              <td class="campo_tabela"  valign="middle"  width="50%" >
                <input type="hidden" id="aux_nome_mae" name="aux_nome_mae" size="60">
                <input type="text" id="nome_mae" name="nome_mae" size="60" maxlength="70">
              </td>

              <td class="descricao_campo_tabela" valign="middle" width="15%">
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Data Nasc.
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
                <input type="hidden" id="aux_data_nasc" name="aux_data_nasc" size="60">
                <input type="text" id="data_nasc" name="data_nasc" size="15"  maxlength="10" onKeyPress="return mascara_data_dispensacao(event,this);" onBlur="if(verificaData(this)==true){verificaDataMaior(this)};" >
              </td>
            </tr>

            <tr>
              <td colspan="4" align="center" bgcolor="#D8DDE3">
               <table width="100%">
                <tr>
                 <td width="57%" align="right" bgcolor="#D8DDE3">
                   <?
                     $sql = "select flg_pesquisa_um_nome from parametro";
                     $pesquisa     = mysqli_query($db, $sql);
                     $d_pesquisa   = mysqli_fetch_object($pesquisa);
                     $flg_pesquisa = $d_pesquisa->flg_pesquisa_um_nome;
                     if (strtoupper($flg_pesquisa)=='S')
                     {
                   ?>
                     <input style="font-size: 10px;" type="button" id="pesquisar" name="pesquisar"  value="Buscar Paciente"  onclick="window.mensagem();">
                   <?}
                      else {?>
                          <input style="font-size: 10px;" type="button" id="pesquisar" name="pesquisar"  value="Buscar Paciente"  onclick="window.mensagem();">
                    <?}?>
                  </td>
                   <td width="20%" align="right" bgcolor="#D8DDE3">

                       <input style="font-size: 10px;" type="button" id="manter" name="manter"  value="Manter Paciente" onclick="window.bot_nova_receita_mpaciente();">


                      </td>


                  <td width="47%" align="right" bgcolor="#D8DDE3">
                      <input style="font-size: 10px;" size="10" type="button" id="limpar" name="limpar"     value="        Limpar        " onclick="limpar_campos_receita(); document.form_inclusao.pesquisar.disabled=false;">
                 </td>
                </tr>
               </table>
             </td>
            </tr>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr class="titulo_tabela" height="15">
             <td valign="middle" align="center" width="100%"> Receita </td>
            </tr>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Ano
              </td>

              <td class="campo_tabela" valign="middle" width="17%">
               <input type="text" id="ano" name="ano" size="10"  maxlength="4" value="<?php echo date('Y');?>" disabled>
               <input type="hidden" name="ano_tela" maxlength="4" value="<?php echo date('Y');?>">
              </td>

              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Unidade
              </td>

              <td class="campo_tabela" valign="middle" width="18%">
               <input type="text" id="codigo_unidade" name="codigo_unidade" size="8" maxlength="10" value="<?php echo $_SESSION[id_unidade_sistema];?>" disabled>
               <input type="hidden" id="unidade_tela" name="unidade_tela" maxlength="10" value="<?php echo $_SESSION[id_unidade_sistema];?>">
              </td>

              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>N˙mero
              </td>

              <td class="campo_tabela" valign="middle" width="15%">
               <input type="text" id="numero_receita" name="numero_receita" size="8"  maxlength="10" disabled>
               <input type="hidden" id="id_receita" name="id_receita">
              </td>
            </tr>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Data Emiss„o
              </td>
              <td class="campo_tabela" valign="middle" width="17%">
                  <input type="text" id="data_emissao" name="data_emissao" size="10"  maxlength="10" onKeyPress="return mascara_data_dispensacao(event,this);"  onBlur="if(verificaData(this)==true){verificaDataMaior(this)}" >
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Origem
              </td>
              <td class="campo_tabela" valign="middle" width="48%">
                  <select size="1" id="origem_receita" name="origem_receita" style="width:280px;" onChange="habilitaCampoCidade(this);">
                   <option value="" cidade="">Selecione uma origem</option>
                  <?php
                      $sql = "select id_subgrupo_origem, descricao, st_exibir_cidade from subgrupo_origem where status_2 = 'A' order by descricao";
                      $origem = mysqli_query($db, $sql);
                      while ($dadosorigem = mysqli_fetch_object($origem))
                      {
                      if ($origem_receita=="")
                      {
                       ?>
                          <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>" cidade="<?php echo $dadosorigem->st_exibir_cidade;?>"><?php echo $dadosorigem->descricao;?></option>
                        <?

                      }
                      else
                      {
                       if($dadosorigem->id_subgrupo_origem == $origem_receita)
                       {
                       ?>
                         <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>" selected><?php echo $dadosorigem->descricao;?></option>
                     <?}
                       else
                       {?>
                         <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>"><?php echo $dadosorigem->descricao;?></option>
                    <? }
                      }
                      }
                       ?>
                  </select>
              </td>
            </tr>
            <tr>
                <td class="descricao_campo_tabela" valign="middle" width="15%">
                  <IMG id="cidade_img" SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Cidade
                </td>
                <?
                $sql = "select c.id_cidade, concat(c.nome,'/',e.uf) as nome
                     from cidade c, estado e, parametro p
                     where p.cidade_id_cidade = c.id_cidade
                     and c.estado_id_estado = e.id_estado";

                 $dados_cidade_receita = mysqli_fetch_object(mysqli_query($db, $sql));
                 $cidade_receita = $dados_cidade_receita->nome;
                 $id_cidade_receita = $dados_cidade_receita->id_cidade;
                 ?>
                <td colspan="3" class="campo_tabela" valign="middle">
                 <input type="text" size="55" id="cidade_receita" name="cidade_receita" value="<?php echo $cidade_receita;?>" disabled />
				 <div id="acDiv"></div>
                 <A HREF=JavaScript:window.popup_cidade();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_cidade" border="0" title="Pesquisar"></a>
                 <input type="hidden" id="id_cidade_receita" name="id_cidade_receita" value="<?php echo $id_cidade_receita;?>">
                 <input type="hidden" id="aux_id_cidade_receita" name="aux_id_cidade_receita" value="<?php echo $id_cidade_receita;?>">
                 <input type="hidden" id="aux_cidade_receita" name="aux_cidade_receita" value="<?php echo $cidade_receita;?>">
                </td>
            </tr>

          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>

             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>No. InscriÁ„o
             </td>

             <td class="campo_tabela" valign="middle" width="17%">
              <input type="text" id="inscricao" name="inscricao" size="10"  maxlength="10""
                     onChange="if (this.value!='')
                               {
                                document.form_inclusao.id_prescritor.value='';
                                document.form_inclusao.id_tipo_prescritor.value='';
                                document.form_inclusao.prescritor.value='';
                                carregarCombo(this.value, '../../xml_dispensacao/prescritor_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
                               }
                               else
                               {
                                document.form_inclusao.id_prescritor.value='';
                                document.form_inclusao.id_tipo_prescritor.value='';
                                document.form_inclusao.prescritor.value='';
                                carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
                               }"
                     onBlur="if (this.value!=''){document.form_inclusao.prescritor.focus();}">

             </td>

             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Prescritor
             </td>

             <td class="campo_tabela" valign="middle" width="48%">
              <select size="1" id="prescritor" name="prescritor" style="width:280px;" onChange="buscar_inscricao();" onBlur="preenche_campos();">
                <option id="opcao_prescritor" value="0">Selecione um prescritor</option>
                <?php
                   $sql = " select concat(concat(p.id_profissional,'|',p.tipo_prescritor_id_tipo_prescritor),'|', p.inscricao) as codigo, concat(p.nome,'/',e.uf) as nome
                     from unidade_has_profissional u, profissional p, estado e
                     where u.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                     and p.status_2 = 'A'
                     and u.profissional_id_profissional = p.id_profissional
                     and p.estado_id_estado = e.id_estado
                     order by p.nome " ;

                   $result=mysqli_query($db, $sql);
                   erro_sql("Tabela tipo movimento", $db,"");
                   while($movimento_info=mysqli_fetch_object($result)){
                     echo "<option id='opcao_prescritor' value='$movimento_info->codigo'>$movimento_info->nome</option>";
                   }
                ?>
              </select>
              <a href=JavaScript:window.popup_prescritor();>
              <img name="imagem_prescritor" src="<?php echo URL;?>/imagens/i_002.gif" border="0" title="Pesquisar"></a>
              <input type="hidden" id="id_prescritor" name="id_prescritor">
              <input type="hidden" id="id_tipo_prescritor" name="id_tipo_prescritor">
			  
			  <?php
			  //$id_tipo_prescritor = id_tipo_prescritor;
			  //$_SESSION[id_tipo_prescritor] = id_tipo_prescritor;
			  //echo "tipopre ".$id_tipo_prescritor;
		
			  ?>
              <?php
                if($inclusao_perfil_profissional!="")
                {?>
                 <input style="font-size: 10px;" type="button" id="novo" name="novo" value="Novo Prescritor" onclick='popup_novo_prescritor();'>
                <?}
                else
                {?>
                 <input style="font-size: 10px;" type="button" id="novo" name="novo" value="Novo Prescritor" disabled>
                <?}?>
             </td>
            </tr>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>
             <td  colspan=7 class="descricao_campo_tabela">
              <div id="cabec_lista_dispensados" style="display:none;">
                   <table id='lista_dispensados' bgcolor='#D8DDE3' width='100%' cellpadding='0' cellspacing='1' border='0'>
                          <tr class='titulo_tabela' height='21'>
                              <td colspan='8' valign='middle' align='center' width='100%'> Medicamentos Dispensados </td>
                          </tr>
                          <tr bgcolor='#6B6C8F' class='coluna_tabela'>
                              <td width='39%' align='center'>Material / Medicamento</td>
                              <td width='8%' align='center'>Lote</td>
                              <td width='25%' align='center'>Fabricante</td>
                              <td width='10%' align='center'>Validade</td>
                              <td width='12%' align='center'>Qtde. Disp.</td>
                              <td width='3%' align='center'></td>
                              <td width='3%' align='center'></td>
                          </tr>
                   </table>
              </div>
             </td>
            </tr>
            <tr>
             <td  colspan=7 class="descricao_campo_tabela">
              <div id="hidden_lista_dispensados" style="display:none;">
                   <table id='hidden_lista' bgcolor='#D8DDE3' width='100%' cellpadding='0' cellspacing='1' border='0'>
                          <tr bgcolor='#6B6C8F' class='coluna_tabela'>
                              <td width='5%' align='center'>id_medicamento</td>
                              <td width='5%' align='center'>id_estoque</td>
                              <td width='5%' align='center'>qtde_lote</td>
                              <td width='5%' align='center'>qtde_prescrita</td>
                              <td width='5%' align='center'>tempo_tratamento</td>
                              <td width='5%' align='center'>qtde_anterior</td>
                              <td width='5%' align='center'>qtde_dispensada</td>
                              <td width='5%' align='center'>rec_controlada</td>
                              <td width='5%' align='center'>id_autorizador</td>
                          </tr>
                   </table>
              </div>
             </td>
            </tr>
            <div id="lista"></div>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
             <tr class="titulo_tabela" height="15">
               <td valign="middle" align="center" width="100%"> Incluir </td>
             </tr>
          </table>
          
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
             <tr>
               <td class="descricao_campo_tabela" valign="middle" width="16%">
                 <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Grupo
               </td>
               <td class="campo_tabela" valign="middle" colspan='3'>
                <select size="1" id="grupo" name="grupo" style="width:280px;" onChange="limpar_campos();">
                <?php
                   $sql =" select g.id_grupo, g.descricao
                           from
                                  grupo g,
                                  unidade_grupo ug
                           where
                                  g.status_2 = 'A'
                                  and g.id_grupo = ug.grupo_id_grupo
                                  and ug.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                           order by
                                  ug.principal desc ";

                   $result=mysqli_query($db, $sql);
                   erro_sql("Tabela grupo medicamento", $db,"");
                   if (mysqli_num_rows($result)>0)
                   {
                     while($grupo_info=mysqli_fetch_object($result)){
                       echo "<option id='opcao_grupo' value='$grupo_info->id_grupo'>$grupo_info->descricao</option>";
                     }
                   }else
                   {
                     echo "<option id='opcao_grupo' value='0'>Sem grupo cadastrado para unidade</option>";
                   }
                ?>
                </select>
               </td>
             </tr>

             <tr>
               <td class="descricao_campo_tabela" valign="middle" width="16%">
                 <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Material / Medicamento 
               </td>
               <td class="campo_tabela" valign="middle" width="60%">
                  <input type="hidden" id="flg_material" name="flg_material">
                  <input type="hidden" id="flg_autorizador" name="flg_autorizador">
                  <input type="hidden" id="medicamento" name="medicamento">
                  <input type="text" id="medicamento01" name="medicamento01" style="width: 400px" style="text-transform:uppercase"
                         onFocus="if(document.form_inclusao.data_emissao.value==''){document.form_inclusao.data_emissao.focus();}
                                  if(document.form_inclusao.flg_material.value=='1'){document.form_inclusao.flg_material.value='0';valida_prescritor_medicamento();}
                                  if(document.form_inclusao.flg_paciente.value=='1'){carregar_paciente(document.form_inclusao.id_paciente.value, 'id_paciente', 'paciente_ajax.php','nome', 'nome_mae', 'data_nasc', 'cartao_sus', 'cpf', 'prontuario', <?php echo $_SESSION[id_unidade_sistema];?>);}"
                         onBlur="document.form_inclusao.flg_autorizador.value = ''; document.form_inclusao.unidade.value = ''; document.form_inclusao.medicamento.value = ''; TrimJS(); procura_medicamento_nome();"
                         value="<?php echo $medicamento01;?>">
                  <div id="acDiv"></div>
                  <A HREF=JavaScript:window.popup_medicamento();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_medicamento" border="0" title="Pesquisar"></a>
                  <input type="text" id="unidade" name="unidade" size="3" disabled>
               </td>
               <td  class="campo_tabela" valign="middle" width="25%">
                <div id="controlada"></div>
               </td>
             </tr>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
             <tr>
                 <td class="descricao_campo_tabela" valign="middle" width='25%'>
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Qtde. Prescrita
                 </td>
                 <td class="campo_tabela" valign="middle" width='25%'>
                  <input type="text" id="qtde_prescrita" name="qtde_prescrita" size="10"  maxlength="10" onChange="if(document.form_inclusao.tempo_tratamento.value!=''){calcular_qtde_dispensar();document.form_inclusao.tempo_tratamento.focus();}" value="<?php echo $qtde_prescrita;?>"
                         onKeyPress="return numbers(event);">
                 </td>
                 <td class="descricao_campo_tabela" valign="middle" width='25%'>
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Tempo de Tratamento
                 </td>
                 <td class="campo_tabela" valign="middle" width='25%'>
                  <input type="text"  id="tempo_tratamento" name="tempo_tratamento" maxlength="10" onChange="calcular_qtde_dispensar();"
                         onKeyPress="return numbers(event);"> Dias
                 </td>
             </tr>
             <tr>
                 <td class="descricao_campo_tabela" valign="middle" width='25%'>
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Qtde. Dispensada Anterior
                 </td>
                 <td class="campo_tabela" valign="middle" width='25%'>
                  <input type="text" id="anterior" name="anterior" size="10"  maxlength="10" value="0" disabled>
                 </td>
                 <td class="descricao_campo_tabela" valign="middle" width='25%'>
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Qtde. a Dispensar
                 </td>
                 <td class="campo_tabela" valign="middle" width='25%'>
                  <input type='text' id='qtde_dispensar' name='qtde_dispensar' value='<?php echo intval($qtde_dispensar);?>' maxlength='10' onKeyPress="return numbers(event);">
                 </td>
                 <input type="hidden" size="20" id="itens_receita" name="itens_receita">
             </tr>
          </table>
             
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>
                <td height="100%" align="center" valign="top">
                <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
                <tr>
                    <td colspan='6'>
                    <div id = "resposta">
                        <table id="tabela1" bgcolor='#D8DDE3' width="100%" cellpadding="0" cellspacing="1" border="0">
                        <tr bgcolor='#6B6C8F' class="coluna_tabela">
                         <td width='10%' align='center'>
                          Lote
                         </td>
                         <td width='30%' align='center'>
                          Fabricante
                         </td>
                         <td width='10%' align='center'>
                          Validade
                         </td>
                         <td width='10%' align='center'>
                          Estoque
                         </td>
                         <td width='15%' align='center'>
                          Qtde. a Dispensar
                         </td>
                        </tr>
                        </table>
                    </div>
                    </td>
                    </tr>
                </table>
              </td>
            </tr>
          </table>

          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr>
              <td align="center" bgcolor="#D8DDE3">
                <div id="saida">
                </div>
              </td>
            </tr>
            <tr>
              <td align="center" bgcolor="#D8DDE3">
                <table width="100%">
                <tr>
                  <td width="80%">
                    <?php
                      if($mostrar_responsavel_dispensacao!="S"){
                        $mostrar_login_senha="none";
                      }
                      else{
                        $mostrar_login_senha="''";
                      }
                    ?>
                    <div id="mostrar_responsavel_dispensacao" style="display:<?php echo $mostrar_login_senha;?>">
                      <table>
                        <tr>
                          <td class="descricao_campo_tabela" width="30%">
                            Dispensado por:
                          </td>
                          <td class="descricao_campo_tabela" width="10%">
                            Login:
                          </td>
                          <td>
                            <input type="text" name="login" onblur="habilitaBotaoSalvar();" onfocus="desabilitaBotaoSalvar();">
                            <input type="hidden" name="id_login" value="">
                          </td>
                          <td class="descricao_campo_tabela" width="10%">
                            Senha:
                          </td>
                          <td>
                            <input type="password" name="senha" onblur="habilitaBotaoSalvar(); document.form_inclusao.salvar.focus();" onfocus="desabilitaBotaoSalvar();">
                          </td>
                        </tr>
                      </table>
                    </div>
                  </td>
                 <td width="15%" align="right" bgcolor="#D8DDE3">
                    <input style="font-size: 10px;" type="button" id="salvar" name="salvar" value="      Salvar >>     " onClick="salvarReceita()" disabled>
                  </td>
                  <td width="5%" align="right" bgcolor="#D8DDE3">
                 </td>
                </tr>
               </table>
                <input type="hidden" id="todos_med" name="todos_med">
                <input type="hidden" id="flag_salvar" name="flag_salvar">
                <input type="hidden" id="flag_mostrar_responsavel_dispensacao" name="flag_mostrar_responsavel_dispensacao" value="<?php echo $mostrar_responsavel_dispensacao;?>">
              </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela">
              <table align="center" border="0" cellpadding="0" cellspacing="0">
               <tr valign="top" class="descricao_campo_tabela"  height="15">
                <td align="center" bgcolor="#D8DDE3" width='50%'>
                 <input style="font-size: 10px;" type="button" name="novareceita" id="novareceita" value="   Nova Receita   " style="display: none;" onClick="bot_nova_receita();habilitaBotaomanter();">
                </td>
                <td align="center" bgcolor="#D8DDE3" width='50%'>
                 <input style="font-size: 10px;" type="button" name="completarreceita" id="completarreceita" value=" Completar Receita " style="display: none;" onclick="window.location='<?php echo URL;?>/modulos/dispensar/busca_altera_receita.php?aplicacao=<?php echo $aplicacao_altera_receita;?>'">
                </td>
               </tr>
              </table>
              </td>
            </tr>

    		<tr>
			  <td class="descricao_campo_tabela">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
				       <tr valign="top" class="descricao_campo_tabela"  height="15">
						<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos ObrigatÛrios</td>
						<td>&nbsp&nbsp&nbsp</td>
                        <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos n„o ObrigatÛrios</td>
					   </tr>
				</table>
              </td>
			</tr>

          </table>
         </tr>
         </form>
         </body>
        </table>

        </td>
      </tr>
    <style type="text/css">
    <!--
      /* DefiniÁ„o dos estilos do DIV */
      /* CSS for the DIV */
      #acDiv{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDiv UL{ list-style:none; margin: 0; padding: 0; }
      #acDiv UL LI{ display:block;}
      #acDiv A{ color:#000000; text-decoration:none; }
      #acDiv A:hover{ color:#000000; }
      #acDiv LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>
    
    <script>
      //alert('grupo '+document.form_inclusao.grupo.value);
      
      document.form_inclusao.nome.focus();
      //Instanciar objeto AutoComplete Medicamento
      var ACM = new dmsAutoComplete('medicamento01','acDiv','medicamento','unidade','grupo');

      ACM.ajaxTarget = '../../xml_dispensacao/medicamento_dispensacao.php';
      //Definir funÁ„o de retorno
      //Esta funÁ„o ser· executada ao se escolher a palavra
       ACM.chooseFunc = function(id,label)
       {
         var array_material = id.split("|");
         
         var nome_med = document.form_inclusao.medicamento01.value;
         if (array_material[0]=='0')
         {
           //retira espaÁos em branco do nome do medicamento digitado em tela
          TrimJS();
          procura_medicamento_nome();
         }
         else
         {
          document.form_inclusao.medicamento.value = array_material[0];
          document.form_inclusao.unidade.value = array_material[1];
          document.form_inclusao.flg_autorizador.value = '';
          document.form_inclusao.qtde_prescrita.value = '';
          document.form_inclusao.tempo_tratamento.value = '';
          document.form_inclusao.qtde_dispensar.value = '';
          valida_prescritor_medicamento();
         }
       }
	   </script>
	   
	   <script language="javascript" type="text/javascript" src="../../scripts/dmsAutoComplete.js"></script>
	   <script>
	   //autocomplete cidade (inicio)
	   //Instanciar objeto AutoComplete usuario
		var ACM_cidade = new dmsAutoComplete('cidade_receita','acDiv');

		ACM_cidade.ajaxTarget = '../../xml/dmsCidade.php';
		//Definir funÁ„o de retorno
		//Esta funÁ„o ser· executada ao se escolher a palavra
		ACM_cidade.chooseFunc = function(id,label){
			document.form_inclusao.id_cidade_receita.value = id;
		}
		ACM_cidade.elem.onblur = function() {
			cidDesc = document.form_inclusao.cidade_receita.value;
			cidDesc = cidDesc.replace(/^\s+|\s+$/g,"");
			if (cidDesc.length > 0) {
				validaNomeCidade();
			}
			ACM_cidade.hideDiv();
		}
	   //autocomplete (fim)
       
  </script>

<?php
    ////////////////////
    //RODAP… DA P¡GINA//
    ////////////////////

    require DIR."/footer.php";

  //************************
  ////////////////////////////////////////////
  //SE N√O ENCONTRAR ARQUIVO DE CONFIGURA«√O//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
