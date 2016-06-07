/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function modalWinPaciente(paciente)
{
 var path = 'paciente_alteracao_popup.php?id_paciente='+paciente+'&dispensacao=ok';
 if (window.showModalDialog)
 {
   var retorno = window.showModalDialog(path,"name","dialogWidth:800px;dialogHeight:600px","scrollbars:yes","resizable:yes");
 if (retorno)
  {
  window.document.form_inclusao.id_paciente.value = retorno;
  }
 }
 else
 {
  window.open(path, 'modal=yes');
 }

}
