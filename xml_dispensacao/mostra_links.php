<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

   echo "<td colspan='5' class='campo_tabela' valign='middle' width='35%'>";
   echo "<input type='text' size='30' name='cidade_receita' value='".$cidade_receita."' disabled>";
   echo "<a href='pesquisa_cidade_dispensacao.php' target='name' onclick='modalWinCidade(); return false;'>";
   echo "<img src='".URL."/imagens/b_search.png' border='0' title='Pesquisar'></a>";
   echo "<input type='hidden' name='id_cidade_receita' value='".$id_cidade_receita."'>";
   echo "</td>";
?>
