/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function carregarLote(ind_lot, flg_lot, lot, op_lot, arq, cod, tp_mov, unid, aplic){
  if(aplic=="mestoque"){
    var indLote=document.getElementById(ind_lot).value;
  }
  if(document.getElementById(flg_lot).value=="t"){
    if(aplic=="mestoque"){
      document.getElementById(flg_lot).value="f";
    }
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
      document.getElementById(lot).options.length = 1;

      var opcao=document.getElementById(op_lot);

      ajax.open("POST", arq, true);
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      ajax.onreadystatechange=function(){
                                //enquanto estiver processando...emite a msg de carregando
                                if(ajax.readyState==1){
                                  opcao.innerHTML="Carregando...!";
                                }
                                //após ser processado - chama função processarXML que vai
                                //varrer os dados
                                if(ajax.readyState==4){
                                  if(ajax.responseXML){
                                    processarXML(ajax.responseXML, opcao, indLote, aplic, lot, ind_lot);
                                  }
                                  else{
                                    //caso não seja um arquivo XML emite a mensagem abaixo
                                    opcao.innerHTML="--Primeiro selecione um material--";
                                  }
                                }
                              }
      //passa o código do estado escolhido
      var codigo=document.getElementById(cod).value;
      var params="id_material=" + codigo;
      if(aplic=="mestoque"){
        var movimento=document.getElementById(tp_mov).value;
        var unidade=document.getElementById(unid).value;
        params+="|" + movimento + "|" + unidade;
      }
      else{
        params+="||";
      }
      params+="|" + aplic;
      ajax.send(params);
    }
  }
}

function processarXML(obj, opt, indiceLote, aplicacao, lot, ind_lote){
  //pega a tag lote
  var dataArray=obj.getElementsByTagName("lote");

  //total de elementos contidos na tag lote
  var tam=dataArray.length;
  if(tam>0){
    //percorre o arquivo XML para extrair os dados
    for(var i=0; i<tam; i++){
      var item=dataArray[i];
      //contéudo dos campos no arquivo XML
      var codigo=item.getElementsByTagName("codigo")[0].firstChild.nodeValue;
      var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

      opt.innerHTML="--Selecione uma das opções abaixo--";

      //cria um novo option dinamicamente
      var novo=document.createElement("option");
      //atribui um ID a esse elemento
      novo.setAttribute("id", "opcoes");
      //atribui um valor
      novo.value=codigo;
      //atribui um texto
      novo.text=descricao;
      if(aplicacao=="mestoque"){
        if(indiceLote==codigo){
          novo.selected=true;
          document.getElementById(ind_lote).value="";
        }
      }
      //finalmente adiciona o novo elemento
      document.getElementById(lot).options.add(novo);
    }
  }
  else{
    //caso o XML volte vazio, printa a mensagem abaixo
    opt.innerHTML = "--Não existe lote--";
  }
}

