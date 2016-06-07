/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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

