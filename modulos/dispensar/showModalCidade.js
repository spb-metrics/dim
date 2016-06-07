/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function modalWinCidade()
{
 if (window.showModalDialog)
 {
  var retorno = window.showModalDialog("pesquisa_cidade_dispensacao.php","name","dialogWidth:500px;dialogHeight:600px");
  if (retorno)
  {
   var valores = retorno.split('|');
   // 0 - id_cidade
   // 1 - nome

   window.document.form_inclusao.id_cidade_receita.value = valores[0];
   window.document.form_inclusao.cidade_receita.value = valores[1];

  }
 }
}

