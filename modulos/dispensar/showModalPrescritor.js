/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function modalWinPrescritor()
{
 if (window.showModalDialog)
 {
  var retorno = window.showModalDialog("pesquisa_prescritor.php","name","dialogWidth:600px;dialogHeight:600px");
  if (retorno)
  {
   var valores = retorno.split('|');
   // 0 - id_prescritor
   // 1 - tipo-prescritor
   // 2 - inscricao
   // 3 - nome+uf

   window.document.form_inclusao.id_prescritor.value = valores[0];
   window.document.form_inclusao.id_tipo_prescritor.value = valores[1];
   window.document.form_inclusao.inscricao.value = valores[2];
   
   var codigo = valores[0]+'|'+valores[1]+'|'+valores[2];
   var descricao = valores[3];
   
   var sel = document.getElementById("prescritor");
   sel.options[1] = new Option(descricao, codigo);
   sel.selectedIndex = 1;
   
   window.document.form_inclusao.medicamento01.focus();
   
   //window.document.form_inclusao.prescritor.value = valores[3];
   //carregarCombo(window.document.form_inclusao.id_prescritor.value, '../../xml/prescritor_id_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
  }
 }
}

