<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/////////////////////////////////////////////////////////////////
//  Sistema..: DIM
//  Arquivo..: erro_select.php
//  Bancos...:
//  Data.....: 11/05/2007
//  Analista.: Denise Ike / Fabio Hitoshi Ide
//  Função...: Mensagem de erro nos SQL's
//////////////////////////////////////////////////////////////////

 if (isset($_GET[msg]))
 {
  $msg = $_GET[msg];
 }
 
?>

  <TABLE name="centralizadora" align="center" width="100%" height="100%" border="0">
    <TR>
      <TD align="center" valign="middle">
        <FONT color="Gray">
          ERRO NA <?php echo $msg;?><BR><BR>
        </FONT>
      </TD>
    </TR>
    <TR>
      <TD align="center" valign="middle">
      <input type="button" value="Ok" onClick="javascript:history.go(-1);">
      </TD>
    </TR>

  </TABLE>
