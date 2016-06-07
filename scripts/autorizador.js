/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

var ajaxaut;
var dadosUsuarioaut;

// ------- cria o objeto e faz a requisi��o -------
function requisicaoHTTPaut(tipo,url,assinc){
	if(window.XMLHttpRequest){	  // Mozilla, Safari,...
		ajaxaut = new XMLHttpRequest();
	}
	else if (window.ActiveXObject){	// IE
		ajaxaut = new ActiveXObject("Msxml2.XMLHTTP");
		if (!ajaxaut) {
			ajaxaut = new ActiveXObject("Microsoft.XMLHTTP");
		}
    }

	if(ajax)	// iniciou com sucesso
		iniciaRequisicaoaut(tipo,url,assinc);
	else
		alert("Seu navegador n�o possui suporte a essa aplica��o!");
}

// ------- Inicializa o objeto criado e envia os dados (se existirem) -------
function iniciaRequisicaoaut(tipo,url,bool){
	ajax.onreadystatechange=trataRespostaaut;
	ajax.open(tipo,url,bool);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	//ajax.overrideMimeType("text/XML");   /* usado somente no Mozilla */
	ajax.send(dadosUsuario);
}


// ------- Inicia requisi��o com envio de dados -------
function enviaDadosaut(url){
	criaQueryString();
	requisicaoHTTP("POST",url,true);
}


// ------- Cria a string a ser enviada, formato campo1=valor1&campo2=valor2... -------
function criaQueryString(){
	dadosUsuario="";
	var frm = document.forms[0];
	var numElementos =  frm.elements.length;
	for(var i = 0; i < numElementos; i++)  {
		if(i < numElementos-1)  {
			dadosUsuario += frm.elements[i].name+"="+encodeURIComponent(frm.elements[i].value)+"&";
		} else {
			dadosUsuario += frm.elements[i].name+"="+encodeURIComponent(frm.elements[i].value);
		}
	}
}

// ------- Trata a resposta do servidor -------
function trataRespostaaut(){
	if(ajaxaut.readyState == 4){
		if(ajaxaut.status == 200){
			trataDadosaut();  // criar essa fun��o no seu programa
		} else {
			alert("Problema na comunica��o com o objeto XMLHttpRequest.");
		}
	}
}>
