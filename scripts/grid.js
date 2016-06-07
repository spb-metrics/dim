/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
                      alert('Paciente não cadastrado. Favor providenciar cadastro.');
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
