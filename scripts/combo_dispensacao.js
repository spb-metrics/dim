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
            else if (ajax.responseXML== null)
              opcao.innerHTML = "Profissional não cadastrado. Favor providenciar o cadastrado.";
          }
        }
	   //passa o código do estado escolhido
	   var params="descricao=" + tipo;
	   //alert (params);
        ajax.send(params);
      }
    }

    function comboXML(obj, opt, campo){

      var dataArray=obj.getElementsByTagName("registro");
	  //total de elementos contidos na tag
	  if(dataArray.length>1){
	  
	    // verifica qtos ativos existem
        var j=0;
        for(var i=0; i<dataArray.length; i++){
           var aux=dataArray[i];
           var status=aux.getElementsByTagName("status")[0].firstChild.nodeValue;
           
           if(status=='A')
           {
             j++;
           }
        }
        
        //percorre o arquivo XML para extrair os dados
        for(var i=0; i<dataArray.length; i++){
          var item=dataArray[i];
          //contéudo dos campos no arquivo XML
          var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
          var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;
          var status=item.getElementsByTagName("status")[0].firstChild.nodeValue;

          opt.innerHTML="Selecione um prescritor";

          
          //cria um novo option dinamicamente
          if(status=='A')
          {
              var novo=document.createElement("option");
              //atribui um ID a esse elemento
              novo.setAttribute("id", "opcoes");
              //atribui um valor
              novo.value=codigo;
              //atribui um texto
              novo.text=descricao.toUpperCase();
              if(j==1)
                novo.selected =true;
              document.getElementById(campo).options.add(novo);
          }

        }
      }
	  else{

       opt.innerHTML="Selecione um prescritor";
	   if(dataArray.length==1){
        //percorre o arquivo XML para extrair os dados
        for(var i=0; i<dataArray.length; i++){
          var item=dataArray[i];
          //contéudo dos campos no arquivo XML
          var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
          var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;
          var status=item.getElementsByTagName("status")[0].firstChild.nodeValue;

          if(status=='I')
          {
            opt.innerHTML = "Este profissional está inativado no sistema.";
          }
          //cria um novo option dinamicamente
          if(status=='A')
          {
              var novo=document.createElement("option");
              //atribui um ID a esse elemento
              novo.setAttribute("id", "opcoes");
              //atribui um valor
              novo.value=codigo;
              //atribui um texto
              novo.text=descricao.toUpperCase();
              novo.selected =true;
              document.getElementById(campo).options.add(novo);
          }
        }
       }
       else{
	    //caso o XML volte vazio, printa a mensagem abaixo
		opt.innerHTML = "Profissional não cadastrado. Favor providenciar o cadastrado.";
       }
	  }
    }
