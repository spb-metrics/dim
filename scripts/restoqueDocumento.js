/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function getHTTPObject(){
  var xmlhttp;
 
  if(window.XMLHttpRequest){
    xmlhttp = new XMLHttpRequest();
  }
  else if (window.ActiveXObject){
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    if (!xmlhttp){
        xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  }
  return xmlhttp;
}
var http = getHTTPObject(); // We create the HTTP Object

/*
	Funtion Name=requestInfo
	Param = url >> Url to call : id = Passing div id for multiple use ~ as a seprator for eg. div1~div2 :
	redirectPage >> if you like to redirect to other page once the event success then
	the response text = 1 and the redirectPage not left empty
*/

function requestInfo(url,id,redirectPage, msg, campo, bot, responsavel){
  var temp=new Array();
  http.open("GET", url, true);
  http.onreadystatechange=function(){
				            if(http.readyState==4){
				              if(http.status==200){
			  		            var results=http.responseText;
                                var info=results.substr(0, 3);
                                if(info=="NAO"){
                                  window.alert(msg);
                                  document.getElementById(campo).focus();
                                  document.getElementById(campo).select();
					              document.getElementById(bot).disabled="true";
                                  document.getElementById(id).style.display="none";
                                }
					            if(redirectPage=="" && info!="NAO"){
                                  var temp=id.split("~"); // To display on multiple div
						          var r=results.split("~"); // To display multiple data into the div
                                  if(temp.length>1){
							        for(i=0;i<temp.length;i++){
								      document.getElementById(temp[i]).innerHTML=r[i];
							        }
						          }
                                  else{
							        document.getElementById(id).innerHTML = results;
                                    document.getElementById("tabela").style.display="";
							        if(responsavel=="S"){
  							          document.getElementById(bot).disabled=true;
                                    }
                                    else{
  							          document.getElementById(bot).disabled=false;
                                    }
						          }
					            }
				              }
  				            }
			              };
  http.send(null);
}

