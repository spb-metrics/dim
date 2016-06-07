/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function carregarUnidades(opcao_combo, combo, arq){
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
    //deixa apenas o elemento 1 no option, os outros s�o exclu�dos
    document.getElementById(combo).options.length = 1;

    var opcao=document.getElementById(opcao_combo);

    ajax.open("POST", arq, true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    ajax.onreadystatechange=function(){
                              //enquanto estiver processando...emite a msg de carregando
                              if(ajax.readyState==1){
                                opcao.innerHTML="Carregando...!";
                              }
                              //ap�s ser processado - chama fun��o processarXML que vai
                              //varrer os dados
                              if(ajax.readyState==4){
                                if(ajax.responseXML){
                                  processarXML(ajax.responseXML, opcao, combo);
                                }
                                else{
                                  //caso n�o seja um arquivo XML emite a mensagem abaixo
                                  opcao.innerHTML="--Primeiro selecione um material--";
                                }
                              }
                            }
    //passa o c�digo do estado escolhido
    ajax.send("");
  }
}

function processarXML(obj, opt, comb){
  //pega a tag lote
  var dataArray=obj.getElementsByTagName("combo");

  //total de elementos contidos na tag lote
  var tam=dataArray.length;
  if(tam>0){
    //percorre o arquivo XML para extrair os dados
    for(var i=0; i<tam; i++){
      var item=dataArray[i];
      //cont�udo dos campos no arquivo XML
      var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
      var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

      opt.innerHTML="--Selecione uma Unidade--";

      //cria um novo option dinamicamente
      var novo=document.createElement("option");
      //atribui um ID a esse elemento
      novo.setAttribute("id", "opcoes");
      //atribui um valor
      novo.value=codigo;
      //atribui um texto
      novo.text=descricao;
      //finalmente adiciona o novo elemento
      document.getElementById(comb).options.add(novo);
    }
    document.getElementById(comb).focus();
  }
  else{
    //caso o XML volte vazio, printa a mensagem abaixo
    opt.innerHTML = "--N�o existe unidade--";
  }
}

