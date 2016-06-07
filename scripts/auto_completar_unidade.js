/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function dmsAutoComplete(elem,divname,elemaux1,elemaux2){

	var me = this;
	this.clearField = true;
	this.minLength = 3;
	this.elem = document.getElementById(elem);
	this.elem1 = document.getElementById(elemaux1);
    this.elem2 = document.getElementById(elemaux2);
    this.highlighted = -1;
	this.arrItens = new Array();
	this.chooseFunc = null; //Função para executar com obj selecionado
	this.div = document.getElementById(divname);
//    this.hideSelects = true;
	//Keycodes que devem ser monitorados
	var TAB = 9;
	var ESC = 27;
	var KEYUP = 38;
	var KEYDN = 40;
	var ENTER = 13;
    var ESP = 32;
//	var BACK_SPACE = 8;
//	var DEL = 46;
//	var BACK = 37;

	//Tamanho do DIV = Tamanho do campo
	this.div.style.width = this.elem.style.width;

	//Desabilitar autocomplete IE
	me.elem.setAttribute("autocomplete","off");

	//Crate AJAX Request
	this.ajaxReq = createRequest();

	//Ação a ser executada no KEYDOWN (funções de navegação)
	me.elem.onkeydown = function(ev)
	{
		var key = me.getKeyCode(ev);
        //alert (key);
		switch(key)
		{
			case TAB:
                if (me.elem.value!='')
                {
				 if (me.highlighted.id != undefined)
                 {
			 		 me.acChoose(me.highlighted.id);
				 }
				 else
				 {
                    me.acChoose('');
                    me.elem1.value = '';
                    me.elem2.value = '';
                 }
				 me.hideDiv();
				 return false;
				}
			break;
			case ENTER:
				if (me.highlighted.id != undefined)
                {
					me.acChoose(me.highlighted.id);
				}
				else
				{
                    me.acChoose('');
                    me.elem1.value = '';
                    me.elem2.value = '';
                }
				me.hideDiv();
				return false;
			break;

			case ESC:
				me.hideDiv();
				return false;
			break;

			case KEYUP:
				me.changeHighlight('up');
				return false;
			break;

			case KEYDN:
				me.changeHighlight('down');
				return false;
			break;
		}

	};

	this.setElemValue = function(){
		var a = me.highlighted.firstChild;
		me.elem.value = a.innerTEXT;
	}

	this.highlightThis = function(obj,yn){
		if (yn = 'y'){
			me.highlighted.className = '';
			me.highlighted = obj;
			me.highlighted.className = 'selected';

			me.setElemValue(obj);

		}else{
			obj.className = '';
			me.highlighted = '';
		}
	}

	this.changeHighlight = function(way){

		if (me.highlighted != '' && me.highlighted != null ){
			me.highlighted.className = '';
			switch(way){
				case 'up':
					if(me.highlighted.parentNode.firstChild == me.highlighted){
						me.highlighted = me.highlighted.parentNode.lastChild;
					}else{
						me.highlighted = me.highlighted.previousSibling;
					}
				break;
				case 'down':
					if(me.highlighted.parentNode.lastChild == me.highlighted){
						me.highlighted = me.highlighted.parentNode.firstChild;
					}else{
						me.highlighted = me.highlighted.nextSibling;
					}
				break;

			}
			me.highlighted.className = 'selected';
			me.setElemValue();
		}else{
			switch(way){
				case 'up':
					me.highlighted = me.div.firstChild.lastChild;
				break;
				case 'down':
					me.highlighted = me.div.firstChild.firstChild;
				break;

			}
			me.highlighted.className = 'selected';
			me.setElemValue();
		}

	}

	//Rotina no KEYUP (pegar input)
	me.elem.onkeyup = function(ev)
	{
		var key = me.getKeyCode(ev);
		switch(key)
		{
		//The control keys were already handled by onkeydown, so do nothing.
		//case TAB:
		case ESC:
		case KEYUP:
		case KEYDN:
			return;
		case ENTER:
			return false;
			break;
		default:
			//Cancelar requisicao antiga
			me.ajaxReq.abort();
			//Enviar query por AJAX
			//Verificar tamanho mínimo
			if (me.elem.value.length >= me.minLength){
				if (me.ajaxReq != undefined){

					me.ajaxReq.open("POST", me.ajaxTarget, true);
					me.ajaxReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
					me.ajaxReq.onreadystatechange = me.acResult;

					//var param = 'unidade01=' + me.elem.value.toUpperCase();
					var param=elem+"=" + me.elem.value.toUpperCase();
					me.ajaxReq.send(param);

				}
			}else{
				return;
			}

			//Remover elementos highlighted
			me.highlighted = '';
		}
	};

	//Sumir com autosuggest
	me.elem.onblur = function() {
		me.hideDiv();
	}

	//Ajax return function
	this.acResult = function(){

		if (me.ajaxReq.readyState == 4){

			//alert(linkReq.responseText); //DEBUG

			me.showDiv()

			//Pegar resposta do servidor
			var xmlRes = me.ajaxReq.responseXML;

			//verificar conteudo
			if (xmlRes == undefined) return false;

			var itens = xmlRes.getElementsByTagName('item');
			var itCnt = itens.length;

			//Pegar primeiro filho
			me.div.innerHTML = '';
			var ul = document.createElement('ul');
			me.div.appendChild(ul);

			if (itCnt > 0){
				for (i=0; i<itCnt; i++){

					//Popular array global
					me.arrItens[itens[i].getAttribute("id")] = new Array();
					me.arrItens[itens[i].getAttribute("id")]['label'] = itens[i].getAttribute("label");
					me.arrItens[itens[i].getAttribute("id")]['flabel'] = itens[i].getAttribute("flabel");

					//Adicionar LI
					var li = document.createElement('li');
					li.id = itens[i].getAttribute("id");
					li.onmouseover = function(){ this.className = 'selected'; me.highlightThis(this,'y')}
					li.onmouseout  = function(){ this.className = '';  me.highlightThis(this,'n')}
					li.onmousedown = function() {
						me.acChoose(this.id);
						me.hideDiv();
						return false;
					}

					var a = document.createElement('a');
					a.href = '#';
					a.onclick = function() { return false; }
					a.innerHTML = unescape(itens[i].getAttribute("label"));
					if(itens[i].getAttribute("flabel") != null){
						a.innerTEXT = unescape(itens[i].getAttribute("flabel"));
					}else{
						a.innerTEXT = unescape(itens[i].getAttribute("label"));
					}

					li.appendChild(a);
					ul.appendChild(li);
				}
			}else{
				me.hideDiv();
			}
		}
	}

	this.acChoose = function (id){

		if (id != ''){
			//Função de retorno (Opcional)
			if (me.chooseFunc != null){
				me.chooseFunc(id,unescape(me.arrItens[id]['label']));
          	}
		}
		else
		{
         me.chooseFunc('0|0','');
        }

		//Esconder lista de clientes
		me.hideDiv();
		if (this.clearField){
		//	me.elem.value = '';
		}else{
			me.elem.value = unescape(me.arrItens[id]['label']);
		}

	}

	this.positionDiv = function()
	{
		var el = this.elem;
		var x = 0;
		var y = el.offsetHeight;

		//Walk up the DOM and add up all of the offset positions.
		while (el.offsetParent && el.tagName.toUpperCase() != 'BODY')
		{
			x += el.offsetLeft;
			y += el.offsetTop;
			el = el.offsetParent;
		}

		x += el.offsetLeft;
		y += el.offsetTop;

		this.div.style.left = x + 'px';
		this.div.style.top = y + 'px';
	};

	this.hideDiv = function(){

		me.highlighted = '';
		me.div.style.display = 'none';
		me.handleSelects('');

	}

	this.showDiv = function(){

		me.highlighted = '';
		me.positionDiv();
		me.handleSelects('none');
		me.div.style.display = 'block';

	}

	this.handleSelects = function(state){

		if (!me.hideSelects) return false;

		var selects	= document.getElementsByTagName('SELECT');
		for (var i = 0; i < selects.length; i++)
        {
            selects[i].style.display = state;
        }
	}

	//HELPER FUNCTIONS

	/********************************************************
	Helper function to determine the keycode pressed in a
	browser-independent manner.
	********************************************************/
	this.getKeyCode = function(ev)
	{
		if(ev)			//Moz
		{
			return ev.keyCode;
		}
		if(window.event)	//IE
		{
			return window.event.keyCode;
		}
	};

	/********************************************************
	Helper function to determine the event source element in a
	browser-independent manner.
	********************************************************/
	this.getEventSource = function(ev)
	{
		if(ev)			//Moz
		{
			return ev.target;
		}

		if(window.event)	//IE
		{
			return window.event.srcElement;
		}
	};

	/********************************************************
	Helper function to cancel an event in a
	browser-independent manner.
	(Returning false helps too).
	********************************************************/
	this.cancelEvent = function(ev)
	{
		if(ev)			//Moz
		{
			ev.preventDefault();
			ev.stopPropagation();
		}
		if(window.event)	//IE
		{
			window.event.returnValue = false;
		}
	}
}


//Função que cria AJAX Request
function createRequest() {
  try {
    request = new XMLHttpRequest();
  } catch (trymicrosoft) {
    try {
      request = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (othermicrosoft) {
      try {
        request = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (failed) {
        request = false;
      }
    }
  }

  if (!request)
    alert("Error initializing XMLHttpRequest!");
  else
  	return request;
}
