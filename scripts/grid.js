/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function getHTTPObject() {
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

    function requestInfo(url,id,redirectPage, caminho) {
		var temp=new Array();
			http.open("GET", url, true);
			http.onreadystatechange = function() {
			if (http.readyState == 1){
                document.getElementById('pesquisar').disabled = true;
                document.getElementById('limpar').disabled = true;
                //document.getElementById('novo_paciente').disabled = true;
                document.getElementById('showTable_paciente').innerHTML = '<img src="./imagens/ajax_loader.gif" title="Carregando..."/> Carregando...';

		     	document.getElementById('showTable_paciente').className = 'loading';
	          }
				if (http.readyState == 4) {
				  if(http.status==200) {
			  		var results=http.responseText;
                    if (results=="")
                    {
                      alert('Paciente n�o cadastrado. Favor providenciar cadastro.');
                    }
					if(redirectPage=="" || results!="1") {
						
						var temp=id.split("~"); // To display on multiple div 
						var r=results.split("~"); // To display multiple data into the div 
						if(temp.length>1) {
							for(i=0;i<temp.length;i++) {	
								document.getElementById(temp[i]).innerHTML=r[i];
							}
						} else {
							document.getElementById(id).innerHTML = results;
							document.getElementById('pesquisar').disabled = false;
                            document.getElementById('limpar').disabled = false;
                            //document.getElementById('novo_paciente').disabled = false;
						}	
					}
                    else {
                           window.location.href=redirectPage;
					}

				  }
  				}
			};
			http.send(null);
       }

    function search_data() {
            var id_paciente = document.getElementById("id_paciente").value;
			var cartao      = document.getElementById("cartao_sus").value;
			var nome        = document.getElementById("nome").value;
			var nome_mae    = document.getElementById("nome_mae").value;
			var data_nasc   = document.getElementById("data_nasc").value;
			requestInfo('showTable_paciente.php?mode=display&id_paciente='+id_paciente+'&cartao_tela='+cartao+'&nome_tela='+nome+'&mae_tela='+nome_mae+'&data_nasc='+data_nasc,'showTable_paciente','');
	}
