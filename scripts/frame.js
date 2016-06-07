/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/////////////////////////////////////////////////////////////////
//  Sistema..: DIM
//  Arquivo..: frame.js
//  Bancos...: dbtdim
//  Data.....: 06/12/2006
//  Analista.: Denise Ike
//////////////////////////////////////////////////////////////////

///////////////////////////////////////////
//abre e/ou fecha frame com informa��es  //
//                                       //
///////////////////////////////////////////

function showFrame(frame_l){
	if ((document.getElementById(frame_l) != null) )
		// Toggle visibility between none and inline
		if ((document.getElementById(frame_l).style.display == 'none') ){
			document.getElementById(frame_l).style.display = 'inline';
//			document.getElementById("Inc_Police_Called").checked = true;
		} else {
			document.getElementById(frame_l).style.display = 'none';
//			document.getElementById("Inc_Police_Called").checked = false;
		}
}

function OpenWindow(strLink,strTarget){
	var url;
	url =  strLink;
	window.open(url, target=strTarget);
}

function Search(strUrl,Opcao){
	var url;
	var strHash = false;
	for( i = 0; i <= Opcao; i++ ){
		if( i == Opcao ){
			url = strUrl;
			strHash = true;
		}
	}
	if( strHash ){
		var horizontal = window.screen.width;
		var vertical   = window.screen.height;
		var res_ver = window.screen.height;
		var res_hor = window.screen.width;
// 		alert(res_ver+"-"+res_hor);
		var pos_ver_fin = (res_ver / 2 )/2;
		var pos_hor_fin = (res_hor / 2 )/2;
		window.open(url,target="_blank","toolbar=no, location=no,directories=no,status=yes,menubar=no,resizable=yes,width="+screen.width+",height="+screen.height+",scrollbars=yes,top="+pos_ver_fin+",left="+pos_hor_fin);
//  		alert(pos_ver_fin+"-"+pos_hor_fin);
	}
}

