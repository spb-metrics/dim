/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
          alert("Esse browser não tem recursos para uso do Ajax");
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
                                //após ser processado - chama função processarXML que vai
                                //varrer os dados
                                if(ajax.readyState==4){
                                  if(ajax.responseXML){
                                    processarXML_Unidades(ajax.responseXML, tabela, quantidade, btSalvar);
                                  }
                                  else{
                                    //caso não seja um arquivo XML emite a mensagem abaixo
                                    quantidade.value="--Primeiro selecione um material e um lote--";
                                  }
                                }
                              }
      //passa o código do estado escolhido
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
      //contéudo dos campos no arquivo XML
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
    qtde.value = "--Não existem unidades--";
    bot.disabled='true';
  }
}

