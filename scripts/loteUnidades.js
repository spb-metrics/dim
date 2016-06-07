/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function carregarUnidades(cod, lot, tab, arq, bot, qtde){
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
      var tabela=document.getElementById(tab);
      var quantidade=document.getElementById(qtde);
      var btSalvar=document.getElementById(bot);

      ajax.open("POST", arq, true);
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      ajax.onreadystatechange=function(){
                                //enquanto estiver processando...emite a msg de carregando
                                if(ajax.readyState==1){
                                  quantidade.value="Carregando...!";
                                }
                                //ap�s ser processado - chama fun��o processarXML que vai
                                //varrer os dados
                                if(ajax.readyState==4){
                                  if(ajax.responseXML){
                                    processarXML_Unidades(ajax.responseXML, tabela, quantidade, btSalvar);
                                  }
                                  else{
                                    //caso n�o seja um arquivo XML emite a mensagem abaixo
                                    quantidade.value="--Primeiro selecione um material e um lote--";
                                  }
                                }
                              }
      //passa o c�digo do estado escolhido
      var codigo=document.getElementById(cod).value;
      var lote=document.getElementById(lot).value;
      var params="id_material=" + codigo + "|" + lote;
      ajax.send(params);
    }
}

function processarXML_Unidades(obj, tab, qtde, bot){
  //pega a tag cs
  var dataArray=obj.getElementsByTagName("cs");

  //total de elementos contidos na tag cs
  var tam=dataArray.length;
  if(tam>0){
    //percorre o arquivo XML para extrair os dados
    for(var i=0; i<tam; i++){
      var item=dataArray[i];
      //cont�udo dos campos no arquivo XML
      var total=item.getElementsByTagName("qtde_total")[0].firstChild.nodeValue;
      var unidade=item.getElementsByTagName("unidade")[0].firstChild.nodeValue;
      var quantidade=item.getElementsByTagName("quantidade")[0].firstChild.nodeValue;

      qtde.value=parseInt(total, 10);
      var total_linhas=tab.rows.length;
      var linha=tab.insertRow(total_linhas);
      linha.id="linha" + i;
      linha.className="campo_tabela";
      //unidade
      var cel0=linha.insertCell(0);
      cel0.align="left";
      cel0.innerHTML=unidade;
      //quantidade
      var cel1=linha.insertCell(1);
      cel1.align="right";
      cel1.innerHTML=parseInt(quantidade, 10);
      bot.disabled='';
    }
  }
  else{
    //caso o XML volte vazio, printa a mensagem abaixo
    qtde.value = "--N�o existem unidades--";
    bot.disabled='true';
  }
}

