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
    xmlhttp = new XMLHttpRequest();  //firefox

  }
  else if (window.ActiveXObject){   //IE
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    if (!xmlhttp){
        xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
    }

}
  return xmlhttp;


}
var httpBallon  = getHTTPObject(); // We create the HTTP Object
function showToolTip(text,x,y){
	//if(document.all)e = event;
    var obj = document.getElementById("bubble_tooltip");
    var obj2 = document.getElementById("bubble_tooltip_content");
    var retornoajax=text;
    var pos_ris = retornoajax.indexOf("Cartao=");


      //    alert (pos_ris);
            
    var pos_ris = pos_ris + 7;
    
    var result= retornoajax.substring(pos_ris, text.length);
            //alert(result.length);


	result = result.replace(/~/g, "\n");



	obj2.innerHTML = result;


            //alert(obj2);

	obj.style.display = 'block';
	var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
	if(navigator.userAgent.toLowerCase().indexOf('safari')>=0)st=0;
	//var leftPos = e.clientX - 100;
	var leftPos = (x+75) - 100;
	if(leftPos<0)leftPos = 0;

	obj.style.left = leftPos + 'px';
    obj.style.top = y - obj.offsetHeight -1 + st + 'px';
}


  function hideToolTip()
  {
	document.getElementById('bubble_tooltip').style.display = 'none';
  }


    function requestInfoBallon(url,x,y) {
   // alert ("balao");
		var temp=new Array();
			httpBallon.open("GET", url, true);
			httpBallon.onreadystatechange = function() {
			//alert("entrou na funcao");
			//alert(httpBallon .$_REQUEST);
				if (httpBallon.readyState == 4) {
                 // alert (http.readyState);
                  if(httpBallon.status==200) {
                        //alert (http.status);
                  
			  		var results=httpBallon.responseText;
			  		        //  alert (results);
			  		
					if(results!="") {
						var r=results.split("~"); // To display multiple data into the div
						//alert (r);
                        showToolTip(results,x,y)
                        
                        //alert (showToolTip);

					}
				  }
  				}
			};
			httpBallon.send(null);
       }
