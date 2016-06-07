/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function carregarLotes(arq, cod, unid, tab, tab_aux, bot){
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
      var tabela_aux=document.getElementById(tab_aux);
      var btAdicionar=document.getElementById(bot);

      ajax.open("POST", arq, true);
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      ajax.onreadystatechange=function(){
                                //enquanto estiver processando...emite a msg de carregando
                                if(ajax.readyState==1){
//                                  quantidade.value="Carregando...!";
                                }
                                //após ser processado - chama função processarXML que vai
                                //varrer os dados
                                if(ajax.readyState==4){
                                  if(ajax.responseXML){
                                    processarXML_Lotes(ajax.responseXML, tabela, tabela_aux, btAdicionar);
                                  }
                                  else{
                                    //caso não seja um arquivo XML emite a mensagem abaixo
//                                    quantidade.value="--Primeiro selecione um material e um lote--";
                                  }
                                }
                              }
      //passa o código do estado escolhido
      var params="id_material=" + cod + "|" + unid;
      ajax.send(params);
    }
}

function processarXML_Lotes(obj, tab, tab_aux, bot){
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
      var valores_codigo=codigo.split("|");
      var valores_descricao=descricao.split("|");

      var total_linhas=tab.rows.length;
      var total_linhas_aux=tab_aux.rows.length;
      var linha=tab.insertRow(total_linhas);
      var linha_aux=tab_aux.insertRow(total_linhas_aux);
      linha.id="linha" + i;
      linha.className="campo_tabela";
      linha_aux.id="linha_aux" + i;
      linha_aux.className="campo_tabela";
      //tabela tb_mat
      //codigo
      var cel0=linha.insertCell(0);
      cel0.align="left";
      cel0.innerHTML=valores_descricao[0];
      //fabricante
      var cel1=linha.insertCell(1);
      var simbolo=/&/gi;
      var palavra=valores_descricao[1];
      palavra=palavra.replace(simbolo, "&amp;");
      cel1.align="left";
      cel1.innerHTML=palavra;
      //lote
      var cel2=linha.insertCell(2);
      cel2.align="left";
      cel2.innerHTML=valores_descricao[2];
      //validade
      var cel3=linha.insertCell(3);
      cel3.align="center";
      cel3.innerHTML=valores_descricao[3];
      //estoque
      var cel4=linha.insertCell(4);
      cel4.align="right";
      cel4.innerHTML=valores_descricao[4];
      //quantidade atendida
      var cel5=linha.insertCell(5);
      cel5.align="center";
      var el=document.createElement('input');
      el.type='text';
      el.name='qtde_atendida[]';
      el.id=i;
      el.size=10;
      el.onkeyup=function (){
        if(isNaN(this.value)){
          this.value="";
        }
      };
//      el.onkeypress=function (){return isNumberKey(event)};
      el.onblur=function (){
                  validarQtde(this.id);
                };
      cel5.appendChild(el);
      //tabela tb_mat_aux
      //id codigo
      var cel0=linha_aux.insertCell(0);
      cel0.align="left";
      cel0.innerHTML=valores_codigo[0];
      //id fabricante
      var cel1=linha_aux.insertCell(1);
      cel1.align="left";
      cel1.innerHTML=valores_codigo[1];
      //lote
      var cel2=linha_aux.insertCell(2);
      cel2.align="left";
      cel2.innerHTML=valores_codigo[2];
      //validade
      var cel3=linha_aux.insertCell(3);
      cel3.align="center";
      cel3.innerHTML=valores_codigo[3];
      //estoque
      var cel4=linha_aux.insertCell(4);
      cel4.align="right";
      cel4.innerHTML=valores_codigo[4];
      //quantidade atendida
      var cel5=linha_aux.insertCell(5);
      cel5.align="center";
      var el_aux=document.createElement('input');
      el_aux.type='text';
      el_aux.name='qtde_atendida_aux[]';
      el_aux.id="aux" + i;
      el_aux.size=10;
      cel5.appendChild(el_aux);
      bot.disabled='';
    }
  }
  else{
    //caso o XML volte vazio, printa a mensagem abaixo
//    qtde.value = "--Não existe unidades--";
    bot.disabled='true';
    window.alert("Não Existem Lotes!");
  }
}

