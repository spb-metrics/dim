<!--
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
-->

     <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?echo $cor_linha; ?>';">
     <td width='30%' align='left'><?php echo $info_pessoas->nome;?></td>
     <td width='10%' align='left'><?php echo substr($info_pessoas->data_nasc,-2)."/". substr($info_pessoas->data_nasc,5,2)."/".substr($info_pessoas->data_nasc,0,4);?></td>
     <td width='30%' align='left'><?php echo $info_pessoas->nome_mae;?></td>
     <td width='21%' align='left'><?php echo $info_pessoas->nome_logradouro;?></td>
     <?

     $substituir = '6e54c9a95b';
     $nome = ereg_replace("\\\'", $substituir, $nome);
     $nome_mae = ereg_replace("\\\'", $substituir, $nome_mae);

     if(($info_pessoas->id_status_paciente == 3) ||($info_pessoas->id_status_paciente == 2))
       {?>

         <td width='3%' align='center'><a href="javascript:showFrame (alert('Paciente com situa��o irregular, favor acertar o cadastro'))";><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Visualizar Receitas"></a></td>
       <?}
     else
       {?>

         <td width='3%' align='center'><a href='<?php echo URL;?>/modulos/dispensar/dispensar.php?id_paciente=<?php echo $info_pessoas->id_paciente;?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Visualizar Receitas"></a></td>
       <?
       }?>

     <td width='3%' align='center'><a href='<?php echo URL;?>/modulos/paciente/paciente_alteracao.php?id_paciente=<?php echo $info_pessoas->id_paciente;?>&dispensacao=ok'><img src="<?php echo URL;?>/imagens/b_edit.png" border="0" title="Editar Paciente"></a></td>

       <?
    if ($cor_linha == "#CCCCCC")
    {
        $cor_linha = "#EEEEEE";
    }
    else
    {
      $cor_linha = "#CCCCCC";
    }
  ?>
