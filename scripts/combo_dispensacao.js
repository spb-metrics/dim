/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
            alert("Esse browser n�o tem recursos para uso do Ajax");
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
          //ap�s ser processado - chama fun��o processarXML que vai
          //varrer os dados
          if(ajax.readyState==4){
            if(ajax.responseXML){
              comboXML(ajax.responseXML, opcao, campo);
            }
            else if (ajax.responseXML== null)
              opcao.innerHTML = "Profissional n�o cadastrado. Favor providenciar o cadastrado.";
          }
        }
	   //passa o c�digo do estado escolhido
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
          //cont�udo dos campos no arquivo XML
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
          //cont�udo dos campos no arquivo XML
          var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
          var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;
          var status=item.getElementsByTagName("status")[0].firstChild.nodeValue;

          if(status=='I')
          {
            opt.innerHTML = "Este profissional est� inativado no sistema.";
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
		opt.innerHTML = "Profissional n�o cadastrado. Favor providenciar o cadastrado.";
       }
	  }
    }
