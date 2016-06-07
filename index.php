<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// +-----------------------------------------------------------------------------------------------+
// | Arquivo: index.php                                                                            |
// +-----------------------------------------------------------------------------------------------+
// | Data: 11/06/2008 | Codificador: Guilherme Bernhardt                                           |
// | Descrição: Esboço inicial                                                                     |
// +-----------------------------------------------------------------------------------------------+
// | Aplicação:                                                                                    |
// | Arquivo que passa os parâmetros para a tela de login                                          |
// +-----------------------------------------------------------------------------------------------+
//$aviso = utf8_decode($aviso);
?>
<html>
<head>
<script>

if(navigator.appName.indexOf('Internet Explorer')>0){

 alert ('Pagina Bloqueada! Utilize o Navegador FireFox para acesso ao Sistema!');
      window.open('atualize_navegador.php', '_self');
    }else{
function redireciona_login()
{
	document.frm_redir.action="login.php?res="+screen.width;
	document.frm_redir.submit();
}

}
</script>
</head>
<body onload='redireciona_login();'>

<? 
if ($xLogin)
	$login= $xLogin;
?>
<form method='post' name='frm_redir'>
<input type='hidden' name='login' value='<?=$login?>'>
<input type='hidden' name='opcao_sistema' value='<?=$opcao_sistema?>'>
<input type='hidden' name='foco' value='<?=$foco?>'>
<input type='hidden' name='aviso' value='<?=$aviso?>'>
<input type='hidden' name='login' value='<?=$login?>'>
<input type='hidden' name='login' value='<?=$login?>'>
</form>
</body>
</html>
