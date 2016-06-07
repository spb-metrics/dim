<!--
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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

         <td width='3%' align='center'><a href="javascript:showFrame (alert('Paciente com situação irregular, favor acertar o cadastro'))";><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Visualizar Receitas"></a></td>
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
