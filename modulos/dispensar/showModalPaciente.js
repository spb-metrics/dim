/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
