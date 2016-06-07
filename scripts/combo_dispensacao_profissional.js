/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function getHTTPObject() {
  var xmlhttp;
  if(window.XMLHttpRequest){
    xmlhttp = new XMLHttpRequest();  //firefox
  }
  else if (window.ActiveXObject){   //IE
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    if (!xmlhttp){
        xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  }
  return xmlhttp;
}

 var ajax = getHTTPObject();

function carregarCombo(tipo, arquivo, selName, opSel, campo)
   {
	  //se tiver suporte ajax

	  if(ajax){

        document.getElementById(campo).options.length = 1;

        var opcao=document.getElementById(opSel);

	    ajax.open("POST", arquivo, true);
	    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	    ajax.onreadystatechange=function(){
                                  //enquanto estiver processando...emite a msg de carregando

                                  if(ajax.readyState==1){
	                                opcao.innerHTML="Carregando...";
                                  }
                                  //ap�s ser processado - chama fun��o processarXML que vai
                                  //varrer os dados
                                  if(ajax.readyState==4){
                                    if(ajax.responseXML){
                                      comboXML(ajax.responseXML, opcao, campo);
                                    }
                                  }
                                }
	    //passa o c�digo do estado escolhido

       if (document.getElementById('codigo01'))
       {
         if(document.getElementById('codigo01').value != 0)
         {
             var params="descricao=" + tipo+"&codigo01="+ document.getElementById('codigo01').value;
         }
         else var params="descricao=" + tipo;
       }
       else
         {
             var params="descricao=" + tipo;
         }
        ajax.send(params);
      }
    }

    function comboXML(obj, opt, campo){
      var dataArray=obj.getElementsByTagName("registro");
	  //total de elementos contidos na tag
	  if(dataArray.length>1){

        //percorre o arquivo XML para extrair os dados
        for(var i=0; i<dataArray.length; i++){
          var item=dataArray[i];
          //cont�udo dos campos no arquivo XML
          var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
          var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

          opt.innerHTML="Selecione um item";

          //cria um novo option dinamicamente
          var novo=document.createElement("option");
          //atribui um ID a esse elemento
          novo.setAttribute("id", "opcoes");
          //atribui um valor
          novo.value=codigo;
          //atribui um texto
          novo.text=descricao.toUpperCase();

          document.getElementById(campo).options.add(novo);
          //document.getElementById(campo).options[i+1] = new Option(novo.text, novo.value);
        }
        document.getElementById(campo).focus();
      }
	  else{

       opt.innerHTML="";
	   if(dataArray.length==1){
        //percorre o arquivo XML para extrair os dados
        for(var i=0; i<dataArray.length; i++){
          var item=dataArray[i];

          //cont�udo dos campos no arquivo XML
          var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
          var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

          //cria um novo option dinamicamente
          var novo=document.createElement("option");
          //atribui um ID a esse elemento
          novo.setAttribute("id", "opcoes");
          //atribui um valor
          novo.value=codigo;
          //atribui um texto
          novo.text=descricao.toUpperCase();
          novo.selected =true;
          // alert(campo);
          document.getElementById(campo).options.add(novo);
          document.getElementById(campo).focus();
        //document.getElementById(campo).options[0] = new Option(novo.text, novo.value);
        }
       }
       else{
	    //caso o XML volte vazio, printa a mensagem abaixo
		opt.innerHTML = "N�o existe op��es associadas";

       }
	  }
    }
