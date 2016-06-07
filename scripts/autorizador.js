/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

var ajaxaut;
var dadosUsuarioaut;

// ------- cria o objeto e faz a requisição -------
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
		alert("Seu navegador não possui suporte a essa aplicação!");
}

// ------- Inicializa o objeto criado e envia os dados (se existirem) -------
function iniciaRequisicaoaut(tipo,url,bool){
	ajax.onreadystatechange=trataRespostaaut;
	ajax.open(tipo,url,bool);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	//ajax.overrideMimeType("text/XML");   /* usado somente no Mozilla */
	ajax.send(dadosUsuario);
}


// ------- Inicia requisição com envio de dados -------
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
			trataDadosaut();  // criar essa função no seu programa
		} else {
			alert("Problema na comunicação com o objeto XMLHttpRequest.");
		}
	}
}>
