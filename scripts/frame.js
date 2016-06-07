/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/////////////////////////////////////////////////////////////////
//  Sistema..: DIM
//  Arquivo..: frame.js
//  Bancos...: dbtdim
//  Data.....: 06/12/2006
//  Analista.: Denise Ike
//////////////////////////////////////////////////////////////////

///////////////////////////////////////////
//abre e/ou fecha frame com informações  //
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

