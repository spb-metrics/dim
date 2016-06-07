/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function carregarMedicamentos(tab, tab_aux, arq, bot, cod, url_img, cont, flg_excl, flg_incl){
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
      var btSalvar=document.getElementById(bot);
      var contador=document.getElementById(cont);
      var exclusao=document.getElementById(flg_excl);
      var inclusao=document.getElementById(flg_incl);

      ajax.open("POST", arq, true);
      ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      ajax.onreadystatechange=function(){
                                //após ser processado - chama função processarXML que vai
                                //varrer os dados
                                if(ajax.readyState==4){
                                  if(ajax.responseXML){
                                    processarXML_Medicamentos(ajax.responseXML, tabela, tabela_aux, btSalvar, url_img, contador, exclusao, inclusao);
                                  }
                                }
                              }
      //passa o código do estado escolhido
      var codigo=document.getElementById(cod).value;
      var params="id_prescritor=" + codigo;
      ajax.send(params);
    }
}

function processarXML_Medicamentos(obj, tab, tab_aux, bot, url, cont, flg_excl, flg_incl){
  //pega a tag material
  var dataArray=obj.getElementsByTagName("material");

  //total de elementos contidos na tag cs
  var tam=dataArray.length;
  if(tam>0){
    //percorre o arquivo XML para extrair os dados
    for(var i=0; i<tam; i++){
      var item=dataArray[i];
      //contéudo dos campos no arquivo XML
      var id_material=item.getElementsByTagName("id_material")[0].firstChild.nodeValue;
      var codigo_material=item.getElementsByTagName("codigo_material")[0].firstChild.nodeValue;
      var descricao=item.getElementsByTagName("descricao")[0].firstChild.nodeValue;

      var total_linhas=tab.rows.length;
      var linha=tab.insertRow(total_linhas);
      linha.id="linha" + (i+1);
      linha.className="campo_tabela";
      //codigo material
      var cel0=linha.insertCell(0);
      cel0.align="left";
      cel0.innerHTML=codigo_material;
      //descricao
      var cel1=linha.insertCell(1);
      cel1.align="left";
      cel1.innerHTML=descricao;
      //figura remover
      var cel2=linha.insertCell(2);
      cel2.align="center";
      if(flg_excl.value==""){
        cel2.innerHTML="";
      }
      else{
        var linkRemover="<img src='" + url + "/imagens/trash.gif' border='0' title='Excluir'>";
        var urlRemover="javascript:removerLinha('linha" + (i+1) + "', 'linha_aux" + (i+1) + "', '" + total_linhas + "')";
        cel2.innerHTML=linkRemover.link(urlRemover);
      }

      var total_linhas_aux=tab_aux.rows.length;
      var linha_aux=tab_aux.insertRow(total_linhas_aux);
      linha_aux.id="linha_aux" + (i+1);
      linha_aux.className="campo_tabela";
      //id material
      var cel0=linha_aux.insertCell(0);
      cel0.align="left";
      cel0.innerHTML=id_material;
      //descricao
      var cel1=linha_aux.insertCell(1);
      cel1.align="left";
      cel1.innerHTML=descricao;
      //linha
      var cel2=linha_aux.insertCell(2);
      cel2.align="left";
      cel2.innerHTML=(i+1);

      if(flg_incl.value=="" && flg_excl.value==""){
        bot.disabled='true';
      }
      else{
        bot.disabled='';
      }
      cont.value=(tam+1);
    }
  }
  else{
    //caso o XML volte vazio, printa a mensagem abaixo
    bot.disabled='true';
    cont.value="1";
  }
}

