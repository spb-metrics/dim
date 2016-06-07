<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////

    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    if($_GET[id_receita]!="")
    {
    
     //busca dados do paciente
     $id_receita = $_GET[id_receita];
     $sql = "select date_format(data_emissao, '%d/%m/%Y') as data_emissao,
                    paciente_id_paciente,
                    profissional_id_profissional
             from receita_siga
             where id_receita = '$id_receita'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));
     
     $paciente = $dados_receita->paciente_id_paciente;
     $data_emissao = $dados_receita->data_emissao;
     $prescritor = $dados_receita->profissional_id_profissional;
     
     $sql = "select nome from profissional where id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeprescritor = $dados_prescritor->nome;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select nome from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));
     
     $nome            = $dados_paciente->nome;
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
   // require DIR."/header.php";

    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>
    <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" cellpadding="0" cellspacing="1">
            <tr class="titulo_tabela">
             <td colspan="4" valign="middle" align="center" width="100%" height="21"> Receita </td>
            </tr>
            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Dt. Emiss�o
             </td>
             <td colspan="3" class="campo_tabela" valign="middle" width="15%">
              <input type="text" name="data_emissao" size="10"  maxlength="10" value="<?php echo $data_emissao;?>" disabled>
             </td>
            </tr>
            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Paciente
             </td>
             <td colspan="3" class="campo_tabela" valign="middle" width="25%">
              <input type="text" name="nome" size="70"  maxlength="70" value="<?php echo $nome;?>" disabled>
             </td>
            </tr>
            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Prescritor
             </td>
             <td colspan="3" class="campo_tabela" valign="middle" width="25%">
              <select size="1" name="prescritor" style="width:450px;" disabled>
                 <option value="<?php echo $prescritor;?>" selected><?php echo $nomeprescritor;?></option>
              </select>
             </td>
            </tr>
            <tr class="titulo_tabela">
             <td colspan="4" valign="middle" align="center" width="100%"> Medicamentos Prescritos </td>
            </tr>
            <tr bgcolor='#6B6C8F' class="coluna_tabela">
              <td width='10%' align='center'>
                Data Prescri��o
              </td>
              <td width='70%' align='center'>
                Medicamento
              </td>
              <td width='10%' align='center'>
                Quantidade
              </td>
              <td width='10%' align='center'>
                Tempo Tratamento
              </td>
            </tr>
            <?php
             $sql = "select ma.descricao,
                            ir.qtde_prescrita,
                            ir.tempo_tratamento
                     from item_receita_siga ir, material ma
                     where ir.receita_id_receita = '$id_receita'
                           and ir.material_id_material = ma.id_material
                     order by id_itens_receita ";
             $item = mysqli_query($db, $sql);
             while ($dados_item = mysqli_fetch_object($item))
             {
              ?>
               <tr class="linha_tabela" >
                   <td bgcolor="#D8DDE3" align="center"><?php echo $data_emissao;?></td>
                   <td bgcolor="#D8DDE3" align="left"><?php echo $dados_item->descricao;?></td>
                   <td bgcolor="#D8DDE3" align="right"><?php echo intval($dados_item->qtde_prescrita);?></td>
                   <td bgcolor="#D8DDE3" align="right"><?php echo $dados_item->tempo_tratamento;?></td>
               </tr>
            <?
             }
            ?>
          </table>
        </td>
      </tr>

       <td height="100%" align="center" valign="top">
                     <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="10%">
                            <tr>
                            <td colspan="6" align="right" bgcolor="#D8DDE3">
                            <input style="font-size: 10px;" type="button" name="receita" value="<< Voltar" onClick="javascript:window.close();">
                            </td>
                            </tr>
                     </table name='3'>
                 </td>
            </tr>
      </table>


<?php
    ////////////////////
    //RODAP� DA P�GINA//
    ////////////////////
   // require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////

  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
