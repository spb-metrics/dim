/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function carregarCombo(tipo, arquivo, selName, opSel, campo)
   {
      //verifica se o browser tem suporte a ajax
	  try{
        var ajax=new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e){
        try{
          var ajax=new ActiveXObject("Msxml2.XMLHTTP");
        }
	    catch(ex){
          try{
            var ajax=new XMLHttpRequest();
          }
	      catch(exc){
            alert("Esse browser não tem recursos para uso do Ajax");
            var ajax=null;
          }
        }
      }
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
        }
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
          document.getElementById(campo).options.add(novo);
          novo.selected =true;
        }
       }
       else{
	    //caso o XML volte vazio, printa a mensagem abaixo
		opt.innerHTML = "Não existe opções associadas";

       }
	  }
    }
    
  // atualizar um campo texto, a partir de outro onde digitei a informacao
  // funcoes carregar e processarXML.

    function carregar_paciente(unit, campo, arquivo, campo1, campo2, campo3){
      //verifica se o browser tem suporte a ajax
	  try{
        var ajax=new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e){
        try{
          var ajax=new ActiveXObject("Msxml2.XMLHTTP");
        }
	    catch(ex){
          try{
            var ajax=new XMLHttpRequest();
          }
	      catch(exc){
            alert("Esse browser não tem recursos para uso do Ajax");
            var ajax=null;
          }
        }
      }
	  //se tiver suporte ajax
	  if(ajax){
	    //deixa apenas o elemento 1 no option, os outros são excluídos
        var opcao=document.getElementById(campo);
        var aux1=document.getElementById(campo1);
        var aux2=document.getElementById(campo2);
        var aux3=document.getElementById(campo3);

	    ajax.open("POST", arquivo, true);
	    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	    ajax.onreadystatechange=function(){
                             //chama função processarXML que vai varrer os dados
                             if(ajax.readyState==4){
                                 if(ajax.responseXML){
                                    processarXML(ajax.responseXML, opcao, aux1, aux2, aux3);
                                 }
                             }
                       }
	    //passa o código do estado escolhido
	    var params=campo+"=" + unit;
        ajax.send(params);
      }
    }

    function processarXML(obj, opt, camp1, camp2, camp3){
      //pega a tag cidade
      var dataArray=obj.getElementsByTagName("registro");

	  //total de elementos contidos na tag cidade
	  if(dataArray.length>0){
        //percorre o arquivo XML para extrair os dados
        for(var i=0; i<dataArray.length; i++){
          var item=dataArray[i];
          //contéudo dos campos no arquivo XML
          var nome=item.getElementsByTagName("nome")[0].firstChild.nodeValue;
          var mae=item.getElementsByTagName("mae")[0].firstChild.nodeValue;
          var nasc=item.getElementsByTagName("nasc")[0].firstChild.nodeValue;
          camp1.value=nome;
          camp2.value=mae;
          camp3.value=nasc;
        }
      }
	  else{
	    //caso o XML volte vazio, printa a mensagem abaixo
       alert("Inválido");
		opt.value="";
    	camp1.value="";
    	camp2.value="";
    	camp3.value="";
        opt.focus();
	  }
    }

