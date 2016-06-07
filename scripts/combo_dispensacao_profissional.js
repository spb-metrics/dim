/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
                                  //após ser processado - chama função processarXML que vai
                                  //varrer os dados
                                  if(ajax.readyState==4){
                                    if(ajax.responseXML){
                                      comboXML(ajax.responseXML, opcao, campo);
                                    }
                                  }
                                }
	    //passa o código do estado escolhido

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
          //contéudo dos campos no arquivo XML
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

          //contéudo dos campos no arquivo XML
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
		opt.innerHTML = "Não existe opções associadas";

       }
	  }
    }
