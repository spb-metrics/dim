<!--
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
-->

<HTML>
<HEAD></HEAD>
<BODY>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr class="linha_recibo">
              <td colspan="4" align="center" width="100%" >
                <b>N�mero da Receita: <?=$nr_receita;?></b>
              </td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td colspan="4" align="center" width="100%" height="1">&nbsp;</td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Paciente:</td>
              <td valign="middle" align="left" width="50%" colspan="3"><?=$nome;?></td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Cart�o SUS:</td>
              <td valign="middle" align="left" width="50%"><?=$cartao_sus;?></td>
              <td valign="middle" align="left" width="20%">Data da dispensa��o:</td>
              <td valign="middle" align="left" width="15%"><?=$data_dispensasao;?></td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Prescritor:</td>
              <td valign="middle" align="left" width="50%"><?=$nomeprescritor;?></td>
              <td valign="middle" align="left" width="20%">Data da prescri��o:</td>
              <td valign="middle" align="left" width="15%"><?=$data_emissao;?></td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Dispensado por:</td>
              <td valign="middle" align="left" width="50%" colspan="3"><?=$matricula . " - " . $dispensado;?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        
          <table border="0" align="center" width="100%" cellspacing="1" cellpadding="0" rules=groups frame=void>
           <thead>
     
     <tr>
                <td class="linha_cabecalho02"align="center" width="15%" height="18"><b>Lote</b></td>
                <td class="linha_cabecalho02"align="center" width="15%" height="18"><b>Validade</b></td>
                <td class="linha_cabecalho02"align="center" width="28%" height="18" colspan="2"><b>Quantidade Dispensada</b></td>
                <td class="linha_cabecalho02"align="center" width="11%" height="18"><b>Qtde Prescrita</b></td>
                <td class="linha_cabecalho02"align="center" width="15%" height="18"><b>Qtde Disp Anterior</b></td>
                <td class="linha_cabecalho03"align="center" width="14%" height="18"><b>Qtde Dispensada</b></td>
              </tr>
              
               <tr >
                <td class="celula_lbt" height="18" width="62%" colspan="4">&nbsp;<b>Material / Medicamento:</b>&nbsp;&nbsp;&nbsp;<?=$med_atual;?></td>
                <td  class="celula_bt" width="11%" align="center"><?=$qtd_pre_atual;?></td>
                <td  class="celula_bt" width="15%" align="center"><?=$qtd_dsan_atual;?></td>
                <td  class="celula_rbt" width="10%" align="center"><?=$qtd_dis_atual;?></td>
              </tr>
              

              
            </thead>
            <tbody>
              <tr>
                <td class="celula_lb" align="center" width="13%"><?=$lote;?></td>
                <td class="celula_b" align="center" width="13%"><?=$validade;?></td>
                <td class="celula_b" align="center" width="20%"><?=$qtde;?></td>
                <td class="celula_rb" align="left" colspan="4"><?=$auto;?></td>
              </tr>
            </tbody>
            
           </table>
        </td>
      </tr>
      <tr class="linha_tabela">
        <td colspan="6">&nbsp;</td>
      </tr>
      <tr height="35">
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr class="cabecalho_recibo01">
              <td align="center" width="55%">
                Recebi e conferi os medicamentos listados acima e suas quantidades, em <?=$data_dispensasao;?>.
              </td>
              <td align="center" width="45%">
              ___________________________________________________
              </td>
            </tr>

            <tr class="cabecalho_recibo01">
              <td align="left" width="50%">
                <br>recibo_receita_imp.php
              </td>
              <td align="right" width="50%">
                <br><? echo date("d/m/Y H:i"); ?>
              </td>
            </tr>
          </table>
         
</BODY>
</HTML>
