/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/////////////////////////////////////////////////////////////////
//  Sistema..: DIM
//  Arquivo..: scripts.js
//  Bancos...: dbtdim
//  Data.....: 27/11/2006
//  Analista.: Fabio Hitoshi Ide
//  Função...: Scripts usadas no sistema
//////////////////////////////////////////////////////////////////

function validarData(campo) {
      if(campo.value != "" ){
         var data = campo.value;
         var erro = false;
         var dia, mes, ano;
         if (data.indexOf(" ")+data.indexOf(".")+data.indexOf("-")+data.indexOf("+")==-4) {
            dia = data.substring(0,2);
            if (dia.charAt(1)=="/") {
               data = "0" + data;
               dia  = dia.charAt(0);
            }
            if (!isNaN(dia) && dia>=1 && dia<=31)
               if (data.charAt(2)=="/") {
                  mes = data.substring(3,5);
                  if (mes.charAt(1)=="/") {
                     data = data.substring(0,3) + "0" + data.substring(3,data.length);
                     mes  = mes.charAt(0);
                     erro = true;
                  }
                  if (!isNaN(mes) && mes>=1 && mes<=12)
                     if (data.charAt(5)=="/") {
                        ano = data.substring(6,data.length);
                        if (!isNaN(ano) && ano.length==4)
                        erro = true;
                     }
               }
            }
            if (!erro){
                   campo.focus();
                   campo.select();
            } else {
               ano = parseInt(ano,10);
               if (ano<4){
                  ano += 2000;
               } else {
                  if (ano<100){
                     ano += 1900;
                     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11)) {
                        erro = true;
                     }
                  } else {
                     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11)) {
                        erro = false;
                     }
                     if (mes==2) {
                        if (dia>29) {
                           erro = false;
                        } else
                           if (dia==29 && ano%4!=0) {
                              erro = false;
                           }
                     }
                     if (!erro) {
                               campo.focus();
                               campo.select();
                     } else {
                        data = data.substr(0,6) + ano;
                        campo.value = data;
                     }
                  }
               }
            }
            return erro;
      }
}

//funcao para tecla Tab em formularios com auto completar
 function teclaTab(elem, ultimo){
    nextfield = elem; // nome do primeiro campo do site
    netscape = "";
    ver = navigator.appVersion; len = ver.length;
    for(iln = 0; iln < len; iln++) if (ver.charAt(iln) == "(") break;
    	netscape = (ver.charAt(iln+1).toUpperCase() != "C");

    	function keyDown(DnEvents) {
    		k = (netscape) ? DnEvents.which : window.event.keyCode;
    		if (k == 9) { // preciona tecla TAB
                if (nextfield == ultimo) {
        			return true;
        		} else {
        			// se existem mais campos vai para o proximo
                    eval(document.getElementById(nextfield).focus());
        			return false;
        		}
    	  }
       }
    document.onkeydown = keyDown; // work together to analyze keystrokes
    if (netscape) document.captureEvents(Event.KEYDOWN|Event.KEYUP);
}

//função para bloquear alguns caracteres no campo lote
function validarLote(evt){
      var charCode = evt.keyCode || evt.which;

     if ((charCode >44 && charCode < 58)||(charCode >64 && charCode < 91)||(charCode >96 && charCode < 123)||(charCode == 35 || charCode == 8  || charCode == 9  || charCode ==32 || charCode ==37 || charCode ==39))
        return true;

       return false;
     }
     
//funcao para verifica se o valor contido no campo eh numero
function verificarNumero(campo){
  if(isNaN(campo.value)){
    window.alert("Não é número!");
    campo.select();
    campo.focus();
    return false;
  }
  return true;
}

// verificar se tecla ENTER foi pressionada
    function VerificarEnter(evt) {
      var charCode=(evt.which)?evt.which:evt.keyCode
      if(charCode==13){
        return false;
      }
      return true;
   }

   
//////////////////////////////////////////////////////
// FUNÇÃO CRIADA POR DANIEL P. FONTES EM 07/06/2006 //
//   USADO PARA PERMITIR APENAS VALORES NUMÉRICOS   //
//       onkeydown="return isNumberKey(event);"     //
//////////////////////////////////////////////////////
function isNumberKey(evt)
{
   var charCode = (evt.which) ? evt.which : evt.keyCode

   /*var charKey = String.fromCharCode(charCode);
   window.status = "Código: " + charCode + " - ASCII: " + charKey;   */

   /*if ((charCode < 48 || charCode >57 && charCode < 96 || charCode > 105) && (charCode >= 32))*/

   if ((charCode >= 48 && charCode <= 57) || charCode==8 || charCode==9)
      return true;

   return false;
}


function so_numeros(campo,e) //no campo do form usar
//substituir "xxx" pelo número máximo de caracteres que o campo aceita.
{
    if(window.event)//IE
        keyCode = window.event.keyCode;
    else if (e) //FIREFOX e outros. É ' else if ' mesmo, senão não dá pra botar argumentos...
        keyCode = e.which;

    //alert(keyCode);
    
    if ((keyCode >= 48 && keyCode <= 57) || keyCode==8 || keyCode==9)
      return true;

    return false;
}


/*esta sendo usado na tela inicial.php da dispensação*/
function numbers(evt)
{
    var key_code = evt.keyCode  ? evt.keyCode  :
                   evt.charCode ? evt.charCode :
                   evt.which    ? evt.which    : void 0;

    //alert (key_code);
    // Habilita teclas <DEL>, <TAB>, <ENTER>, <ESC> e <BACKSPACE>
    if (key_code == 8  ||  key_code == 9  ||  key_code == 13  ||  key_code == 27)
    {
        return true;
    }

    // Habilita teclas <HOME>, <END>, mais as quatros setas de navegação (cima, baixo, direta, esquerda)
    //else if ((key_code >= 35)  &&  (key_code <= 40))
    //{
    //    return true
    //}

    // Habilita números de 0 a 9
    else if ((key_code >= 48)  &&  (key_code <= 57))
    {
        return true
    }

    return false;
}

/////// apenas letras e numeros
function isCharAndNumKey(evt)
{
   var charCode = (evt.which) ? evt.which : evt.keyCode

   /*var charKey = String.fromCharCode(charCode);
   window.status = "Código: " + charCode + " - ASCII: " + charKey;   */

   /*if ((charCode < 48 || charCode >57 && charCode < 96 || charCode > 105) && (charCode >= 32))*/

   if ((charCode >= 48 && charCode <= 57) || (charCode >= 97 && charCode <= 122) || (charCode >= 65 && charCode <= 90) || charCode==8 || charCode==9)
      return true;

   return false;
}


//////////////////////////////////////////////////////
// FUNÇÃO CRIADA POR DANIEL P. FONTES EM 18/10/2006 //
//   USADO PARA PERMITIR APENAS VALORES NUMÉRICOS   //
//        FORÇANDO MÁSCADA DE DATA DD/MM/AAAA       //
//   onkeydown="return mascara_data(event,this);"   //
//////////////////////////////////////////////////////
function mascara_data(e,ConteudoCampo)
{

   var charCode = (e.which) ? e.which : e.keyCode

   //if ((charCode < 48 || charCode >57 && charCode < 96 || charCode > 105) && (charCode >= 32))
   if ((charCode >= 47 && charCode <= 57) || charCode==8 || charCode==9 || charCode==46 || charCode==37 || charCode==39)
   {
     var valor = (window.Event) ? e.which : e.keyCode;
     if (valor != 8)
     {
	   NumDig = ConteudoCampo.value;
	   TamDig = NumDig.length;

       if (TamDig == 2)
         ConteudoCampo.value = NumDig.substr(0,2)+"/";
       else if (TamDig == 5)
     	 ConteudoCampo.value = NumDig.substr(0,5)+"/";
       /*else if (TamDig == 9)
         ConteudoCampo.value = NumDig.substr(0,10);*/
     }//end if valor != 8
     return true;
   }
   else
   {
     return false;
   }
}

function mascara_data_dispensacao(evt, valor)
{

    var key_code = evt.keyCode  ? evt.keyCode  :
                   evt.charCode ? evt.charCode :
                   evt.which    ? evt.which    : void 0;
                   
    var ok = false;

    // Habilita teclas <DEL>, <TAB>, <ENTER>, <ESC> e <BACKSPACE>
    if (key_code == 8  ||  key_code == 9  ||  key_code == 13  ||  key_code == 27  ||  key_code == 46)
    {
        ok = true;
    }

    // Habilita teclas <HOME>, <END>, mais as quatros setas de navegação (cima, baixo, direta, esquerda)
    else if ((key_code >= 35)  &&  (key_code <= 40))
    {
        ok = true
    }

    // Habilita números de 0 a 9
    else if ((key_code >= 48)  &&  (key_code <= 57))
    {
        ok = true
    }
    else
    {
     ok = false;
    }

    if (ok)
    {
      if (key_code!=8)
      {
        NumDig = valor.value;
        TamDig = NumDig.length;
        if (TamDig == 2)
          valor.value = NumDig.substr(0,2)+"/";
        else if (TamDig == 5)
       	  valor.value = NumDig.substr(0,5)+"/";
        return true;
      }
    }
    else
    {
     return false;
    }
    
}

function Search(strUrl,Opcao){
	var url;
	var strHash = false;
	for( i = 0; i <= Opcao; i++ ){
		if( i == Opcao ){
			url = strUrl;
			strHash = true;
		}
	}
	if( strHash ){
		var horizontal = window.screen.width;
		var vertical   = window.screen.height;
		var res_ver = window.screen.height;
		var res_hor = window.screen.width;
// 		alert(res_ver+"-"+res_hor);
		var pos_ver_fin = (res_ver / 2 )/2;
		var pos_hor_fin = (res_hor / 2 )/2;
		window.open(url,target="_blank","toolbar=no, location=no,directories=no,status=yes,menubar=no,resizable=yes,width="+screen.width+",height="+screen.height+",scrollbars=yes,top="+pos_ver_fin+",left="+pos_hor_fin);
//  		alert(pos_ver_fin+"-"+pos_hor_fin);
	}
}

function verificaData(campo) {
      if(campo.value != "" ){
         var data = campo.value;
         var erro = false;
         var dia, mes, ano;
         if (data.indexOf(" ")+data.indexOf(".")+data.indexOf("-")+data.indexOf("+")==-4) {
            dia = data.substring(0,2);
            if (dia.charAt(1)=="/") {
               data = "0" + data;
               dia  = dia.charAt(0);
            }
            if (!isNaN(dia) && dia>=1 && dia<=31)
               if (data.charAt(2)=="/") {
                  mes = data.substring(3,5);
                  if (mes.charAt(1)=="/") {
                     data = data.substring(0,3) + "0" + data.substring(3,data.length);
                     mes  = mes.charAt(0);
                     erro = true;
                  }
                  if (!isNaN(mes) && mes>=1 && mes<=12)
                     if (data.charAt(5)=="/") {
                        ano = data.substring(6,data.length);
                        if (!isNaN(ano) && ano.length==4)
                        erro = true;
                     }
               }
            }
            if (!erro){
               alert ("A data fornecida foi preenchida incorretamente.");
                   campo.focus();
                   campo.select();
            } else {
               ano = parseInt(ano,10);
               if (ano<4){
                  ano += 2000;
               } else {
                  if (ano<100){
                     ano += 1900;
                     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11)) {
                        alert ("Este mês não possui mais de 30 dias.");
                        erro = true;
                     }
                  } else {
                     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11)) {
                        alert ("Este mês não possui mais de 30 dias.");
                        erro = false;
                     }
                     if (mes==2) {
                        if (dia>29) {
                           alert ("Fevereiro não pode conter mais de 29 dias.");
                           erro = false;
                        } else
                           if (dia==29 && ano%4!=0) {
                              alert ("Este não é um ano bissexto.");
                              erro = false;
                           }
                     }
                     if (!erro) {
                               campo.focus();
                               campo.select();
                     } else {
                        data = data.substr(0,6) + ano;
                        campo.value = data;
                     }
                  }
               }
            }
            return erro;
      }
}

function formataData(src, val) {
  if (val.length > 4 && val.length<=10){
    val = removeChar(val, "/");
    val = val.slice(0,8);
	pre = insereChar(val.slice(0, val.length - 4), "/", 2);
	pos = val.slice(val.length - 4)
    val = pre.concat("/").concat(pos);
  }
  src.value = val;
}

function insereChar(str, char, dist) {
  if (str.length <= dist) {
    return str;
  } else {
    pre = insereChar(str.slice(0, str.length - dist), char, dist);
	pos = str.slice(str.length - dist);
	str = pre.concat(char).concat(pos);
	return str;
  }
}

function removeChar(str, char) {
  while (str.indexOf(char) != -1) {
    pre = str.slice(0, str.indexOf(char));
	pos = str.slice(str.indexOf(char) + 1);
	str = pre.concat(pos);
  }
  return str;
}

function checkAll(campo, formul){
  if(campo.checked==true){
    for (var i = 0; i < formul.elements.length; i++){
      var x=formul.elements[i];
      if(x.name=='opcao[]'){
        x.checked = true;
      }
    }
  }
  else{
    for (var i = 0; i < formul.elements.length; i++){
      var x=formul.elements[i];
      if(x.name=='opcao[]'){
        x.checked = false;
      }
    }
  }
}

 // José Renato
function abrir_janela(url)
{
  window.open(url,target="_blank","toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=0,width=850,height=500,scrollbars=1,top=100,left=100");
}

function FormataCNPJ(Campo, teclapres){
  var charCode = (teclapres.which) ? teclapres.which : event.keyCode
  if((charCode < 48 || charCode >57 && charCode < 96 || charCode > 105) && (charCode >= 32)){
    return false;
  }
  else
  {
    var tecla = teclapres.keyCode;
    var vr = new String(Campo.value);
    vr = vr.replace(".", "");
    vr = vr.replace(".", "");
    vr = vr.replace("/", "");
    vr = vr.replace("-", "");
    tam = vr.length + 1 ;
    if(tecla != 9 && tecla != 8){
      if(tam > 2 && tam < 6)
        Campo.value = vr.substr(0, 2) + '.' + vr.substr(2, tam);
      if(tam >= 6 && tam < 9)
        Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,tam-5);
      if(tam >= 9 && tam < 13)
        Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,3) + '/' + vr.substr(8,tam-8);
      if(tam >= 13 && tam < 15)
         Campo.value = vr.substr(0,2) + '.' + vr.substr(2,3) + '.' + vr.substr(5,3) + '/' + vr.substr(8,4)+ '-' + vr.substr(12,tam-12);
    }
  }
}

//pula cursor para proximo campo quando a quantidade máxima de caracteres do campo atual for alcançada
//chamada: ... onKeyUp="verifica_saida(this.value, 'prox.campo', 4, this.form);"

 function verifica_saida(valor, nome_objeto, tamanho, formulario) {
  if (valor.length == tamanho) {
	eval(formulario)[nome_objeto].focus();
  }
 }

/*---------------------------------------------------------------*/
//BARRA STATUS
//Variáveis globais
//var _loadTimer	= setInterval(__loadAnima,18);
var _loadTimer;
var _loadPos	= 0;
var _loadDir	= 2;
var _loadLen	= 0;

//Anima a barra de progresso
function __loadAnima(){
	var elem = document.getElementById("barra_progresso");
	if(elem != null){
		if (_loadPos==0) _loadLen += _loadDir;
		if (_loadLen>32 || _loadPos>79) _loadPos += _loadDir;
		if (_loadPos>79) _loadLen -= _loadDir;
		if (_loadPos>79 && _loadLen==0) _loadPos=0;
		elem.style.left		= _loadPos;
		elem.style.width	= _loadLen;
	}
}

//Esconde o carregador
function __loadEsconde(){
	this.clearInterval(_loadTimer);
	var objLoader				= document.getElementById("carregador_pai");
	objLoader.style.display		="none";
	objLoader.style.visibility	="hidden";
}

//Chama barra de status
function mostra(){
  document.getElementById("carregador_pai").style.display="";
  _loadTimer=setInterval(__loadAnima,18);
}

function esconde()
{
  document.getElementById("carregador_pai").style.display="none";
  __loadEsconde();
}
/*---------------------------------------------------------------*/

