<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    if($_GET[id_receita]!="")
    {

     //busca dados do paciente
     $id_receita = $_GET[id_receita];
     $sql = "select * from receita where id_receita = '$id_receita'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));

     $ano = $dados_receita->ano;
     $unidade = $dados_receita->unidade_id_unidade;
     $numero = $dados_receita->numero;
     $prescritor = $dados_receita->profissional_id_profissional;

     $sql = "select * from profissional where id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $inscricao = $dados_prescritor->inscricao;
     $nomeprescritor = $dados_prescritor->nome;

     $sql = "select * from cidade where id_cidade = '$dados_prescritor->cidade_id_cidade'";
     $dados_cidade = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidade = $dados_cidade->nome;

     $sql = "select * from estado where id_estado = '$dados_cidade->estado_id_estado'";
     $dados_estado = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeuf = $dados_estado->uf;

     $data_emissao = $dados_receita->data_emissao;
     $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);

     $origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
     $sql = "select * from subgrupo_origem where id_subgrupo_origem = '$origem'";
     $dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeorigem = $dados_origem->descricao;

     $cidadereceita = $dados_receita->cidade_id_cidade;
     $sql = "select id_cidade, concat(c.nome,'/',e.uf) as nome
      from cidade c, estado e
      where c.estado_id_estado = e.id_estado
      and c.id_cidade = '$cidadereceita'";
     //$sql = "select * from cidade where id_cidade = '$cidadereceita'";
     $dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidadereceita = $dados_cidadereceita->nome;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $cartao_sus      = $dados_paciente->cartao_sus;
     $cartao_sus_prov = $dados_paciente->cartao_sus_prov;
     $nome            = $dados_paciente->nome;
     $data_nasc       = $dados_paciente->data_nasc;
     $data_nasc       = substr($data_nasc,-2)."/".substr($data_nasc,5,2)."/".substr($data_nasc,0,4);
     $sexo            = $dados_paciente->sexo;
     if ($sexo=='F')
     {
      $sexo = "FEMININO";
      }
     else
     {
      if ($sexo=='M')
      {
       $sexo = "MASCULINO";
      }
      else
      {
       $sexo='';
      }
     }
     $data_dispensasao = $dados_receita->data_ult_disp;
     $data_dispensasao = substr($data_dispensasao,8,2)."/".substr($data_dispensasao,5,2)."/".substr($data_dispensasao,0,4);
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>

<script>
  function imprimir_recibo()
  {
   var url;

   url = "<?= URL?>/modulos/consulta/recibo_receita_imp.php?id_receita=<?=$id_receita?>"
   if (confirm("Deseja imprimir recibo?")){
       window.open(url,target="_blank");
       /*"toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=0,width=850,height=500,scrollbars=1,top=100,left=100"*/
    }
  }
</script>

<body onload="imprimir_recibo()">

    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="left">
          <table width="100%" cellpadding="0" cellspacing="1">
          
            <tr class="titulo_tabela">
              <td colspan="6" valign="middle" align="center" width="100%"> Receita </td>
            </tr>

            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Ano
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
              <input type="text" name="ano" size="10"  maxlength="4" value="<?php echo $ano;?>" disabled>
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Unidade
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="text" name="codigo_unidade" size="10" maxlength="10" value="<?php echo $unidade;?>" disabled>
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Número
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="text" name="numero" size="5" maxlength="10" value="<?php echo $numero;?>" disabled>
              </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Dt. Emissão
             </td>
             <td class="campo_tabela" valign="middle" width="15%">
              <input type="text" name="data_emissao" size="10"  maxlength="10" value="<?php echo $data_emissao;?>" disabled>
             </td>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Origem
             </td>
             <td colspan="3" class="campo_tabela" valign="middle" width="15%">
              <select size="1" name="origem_receita" style="width:150px;" disabled>
                  <option value="<?php echo $origem;?>"><?php echo $nomeorigem;?></option>
              </select>
             </td>
            </tr>
            
            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Cidade
             </td>
             <td colspan="5" class="campo_tabela" valign="middle" width="15%">
              <input type="text" size="30" value="<?php echo $nomecidadereceita;?>" disabled>
             </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Paciente
             </td>
             <td colspan="5" class="campo_tabela" valign="middle" width="25%">
              <input type="text" name="nome" size="70"  maxlength="70" value="<?php echo $nome;?>" disabled>
             </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Prescritor
             </td>
             <td colspan="5" class="campo_tabela" valign="middle" width="25%">
              <select size="1" name="prescritor" style="width:250px;" disabled>
                 <option value="<?php echo $prescritor;?>" selected><?php echo $nomeprescritor;?></option>
              </select>
             </td>
            </tr>

            <tr class="titulo_tabela">
             <td colspan="6" valign="middle" align="center" width="100%"> Medicamentos </td>
            </tr>

             <tr>
        <td colspan="6" height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='6'>
                  <table bgcolor='#D0D0D0' width="100%" cellpadding="0" cellspacing="1" border="0">
            <tr bgcolor='#6B6C8F' class="coluna_tabela">
                   <td colspan='2' width='35%' align='center'>
                       Medicamento
                   </td>
                   <td width='15%' align='center'>
                       Lote
                   </td>
                   <td width='30%' align='center'>
                       Fabricante
                   </td>
                   <td width='10%' align='center'>
                       Validade
                   </td>
                   <td width='10%' align='center'>
                       Qtde. Dispensada
                   </td>
            </tr>
            <?php
             $sql = "select ir.*, ma.descricao from itens_receita ir, material ma
                  where ir.receita_id_receita = '$id_receita'
                  and ir.material_id_material = ma.id_material
                  order by id_itens_receita ";
             $item = mysqli_query($db, $sql);
             while ($dados_item = mysqli_fetch_object($item))
             {
              $sql = "select img.*, fa.descricao from itens_movto_geral img, fabricante fa
                   where img.itens_receita_id_itens_receita = '$dados_item->id_itens_receita'
                   and img.fabricante_id_fabricante = fa.id_fabricante";
              $est = mysqli_query($db, $sql);
              while ($dados_est = mysqli_fetch_object($est))
              {
            ?>
             <tr class="linha_tabela" >
                   <td colspan='2' bgcolor="#FFFFFF" align="left">
                    <?php echo $dados_item->descricao;?>
                   </td>
                   <td bgcolor="#FFFFFF" align="left"><?php echo $dados_est->lote;?></td>
                   <td bgcolor="#FFFFFF" align="left"><?php echo $dados_est->descricao;?></td>
                   <td bgcolor="#FFFFFF" align="center"><?php echo substr($dados_est->validade,8,2)."/".substr($dados_est->validade,5,2)."/".substr($dados_est->validade,0,4);?></td>
                   <td bgcolor="#FFFFFF" align="right"><?php echo intval($dados_est->qtde);?></td>
             </tr>
            <?php
              }
              if (mysqli_num_rows($est)==0)
              {?>
               <tr class="linha_tabela" >
                   <td colspan='2' bgcolor="#FFFFFF" align="left">
                    <?php echo $dados_item->descricao;?>
                   </td>
                   <td bgcolor="#FFFFFF" align="center">--</td>
                   <td bgcolor="#FFFFFF" align="center">--</td>
                   <td bgcolor="#FFFFFF" align="center">--</td>
                   <td bgcolor="#FFFFFF" align="right">0</td>
               </tr>
            <?}
             }
            ?>
            </table>
            </td>
            </tr>
            </table>
            </td>
          </table>
        </td>
      </tr>
      <tr>
       <td height="100%" align="center" valign="top">
       <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%'>
             <tr>
               <td colspan="6" align="right" bgcolor="#D8DDE3">
                 <input style="font-size: 10px;" type="button" name="receita" value="Nova Receita" onClick="window.location='<?php echo URL;?>/modulos/dispensar/inicial.php?aplicacao=<?php echo $_SESSION[cod_aplicacao];?>'">
                 <input style="font-size: 10px;" type="button" name="receita" value="Completar Receita" onClick="window.location='<?php echo URL;?>/modulos/dispensar/busca_altera_receita.php?aplicacao=<?php echo $_SESSION[cod_aplicacao];?>'">
               </td>
             </tr>

                     </table name='3'>
                 </td>
            </tr>
      </table>

</body>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////

  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
