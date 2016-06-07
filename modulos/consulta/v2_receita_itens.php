<!--
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
-->

<HTML>
<HEAD>
</HEAD>
<BODY>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr class="linha_recibo">
              <td colspan="4" align="center" width="100%" >
                <b>Número da Receita: <?=$nr_receita;?></b>
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
              <td valign="middle" align="left" width="15%">Cartão SUS:</td>
              <td valign="middle" align="left" width="50%"><?=$cartao_sus;?></td>
              <td valign="middle" align="left" width="20%">Data da dispensação:</td>
              <td valign="middle" align="left" width="15%"><?=$data_dispensasao;?></td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Prescritor:</td>
              <td valign="middle" align="left" width="50%"><?=$nomeprescritor;?></td>
              <td valign="middle" align="left" width="20%">Data da prescrição:</td>
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
          <table border="0" align="center" width="100%" cellspacing="0" cellpadding="0" rules=groups frame=void>
<?
  $sql = "select mat.codigo_material, mat.descricao, irc.qtde_prescrita,
          irc.qtde_disp_anterior, irc.qtde_disp_mes, usr.nome,
          img.lote, img.validade, img.qtde, img.movto_geral_id_movto_geral
          from material mat
               inner join itens_receita irc on mat.id_material = irc.material_id_material
               left join itens_movto_geral img on irc.id_itens_receita = img.itens_receita_id_itens_receita
               left join usuario usr on img.usuario_autorizador = usr.id_usuario
          where mat.status_2 = 'A'
                and mat.flg_dispensavel = 'S'";

  if ($id_receita <> '')
    $sql = $sql." and irc.receita_id_receita = '$id_receita'";

  if ($id_movto_geral <> '')
    $sql = $sql." and img.movto_geral_id_movto_geral = '$id_movto_geral'";

  $sql = $sql." order by mat.descricao, img.qtde desc";

  //echo $sql;
  $sql_query = mysqli_query($db, $sql);
  erro_sql("Material", $db, "");
  echo mysqli_error($db);
  $qtde_linhas = mysqli_num_rows($sql_query);
  if ($qtde_linhas > 0)
  {
    $primeira_linha = true;
    while($linha = mysqli_fetch_array($sql_query))
    {
      $med_atual = $linha['codigo_material']." - ".$linha['descricao'];
      $qtd_pre_atual = intval($linha['qtde_prescrita']);
      $qtd_dsan_atual = intval($linha['qtde_disp_anterior']);
      $qtd_dis_atual = intval($linha['qtde_disp_mes']);
      if (($med_anterior == '') or ($med_atual <> $med_anterior))
      {
        $med_anterior = $med_atual;
        ?>
            <thead>
        <?
      
        if ($cont==''||$cont=='1')
        {
          $cont='0';
        ?>
             <tr>
                <td class="linha_cabecalho02"align="center" width="15%" height="18"><b>Lote</b></td>
                <td class="linha_cabecalho02"align="center" width="15%" height="18"><b>Validade</b></td>
                <td class="linha_cabecalho02"align="center" width="28%" height="18" colspan="2"><b>Quantidade Dispensada</b></td>
                <td class="linha_cabecalho02"align="center" width="11%" height="18"><b>Qtde Prescrita</b></td>
                <td class="linha_cabecalho02"align="center" width="15%" height="18"><b>Qtde Disp Anterior</b></td>
                <td class="linha_cabecalho03"align="center" width="14%" height="18"><b>Qtde Dispensada</b></td>
              </tr>
        <?
      }
      ?>
               <tr >
                <td class="celula_lbt" height="18" width="62%" colspan="4">&nbsp;<b>Material / Medicamento:</b>&nbsp;&nbsp;&nbsp;<?=$med_atual;?></td>
                <td  class="celula_bt" width="11%" align="center"><?=$qtd_pre_atual;?></td>
                <td  class="celula_bt" width="15%" align="center"><?=$qtd_dsan_atual;?></td>
                <td  class="celula_rbt" width="10%" align="center"><?=$qtd_dis_atual;?></td>
              </tr>
            </thead>
        <?
      }
      $lote = $linha['lote'];
      $qtde = intval($linha['qtde']);
      $validade = ((substr($linha['validade'],8,2))."/".(substr($linha['validade'],5,2))."/".(substr($linha['validade'],0,4)));
      if ($linha['nome'] <> '')
        $auto = " Autorizado por: ".$linha['nome'];
      else
        $auto = "&nbsp;";
      ?>
            <tbody>
              <tr>
                <td class="celula_l" align="center" width="10%"><?=$linha['lote'];?></td>
                <td class="celula01" align="center" width="10%"><?=$validade;?></td>
                <td class="celula01" align="center" width="20%"><?=intval($linha['qtde']);?></td>
                <td class="celula_r" align="left" colspan="5"><?=$auto;?></td>
              </tr>
              
            </tbody>
  <?
    }

  }
  ?>
         </table>
        </td>
      </tr>
      <tr>
        <td class="celula_t" colspan="6">&nbsp;</td>
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
              <td align="right" width="40%">
                <br><? echo date("d/m/Y H:i");$cont='1' ?>
              </td>
            </tr>

          </table>

</BODY>
</HTML>
