/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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

