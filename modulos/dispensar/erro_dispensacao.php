<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

 session_start();

 if (isset($_GET[msg]))
 {
  $msg = $_GET[msg];
 }
 
?>

  <TABLE name="centralizadora" align="center" width="100%" height="100%" border="0">
    <TR>
      <TD align="center" valign="middle">
        <FONT color="Gray">ERRO NA <p><?php echo $msg;?>
        </FONT>
      </TD>
    </TR>
    <TR>
      <TD align="center" valign="middle">
      <input type="button" value="Ok" onClick="window.location='<?php echo URL;?>/inicial.php?aplicacao=<?php echo $_SESSION[DISP_INICIAL];?>'">
      </TD>
    </TR>

  </TABLE>
