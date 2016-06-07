<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: familia_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de familia
  //////////////////////////////////////////////////////////////////

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


    if(isset($_POST[flag]) && $_POST[flag]=="t"){
      $data=date("Y-m-d H:i:s");
      $sql="insert into familia (subgrupo_id_subgrupo, descricao, status_2, data_incl, usua_incl) ";
      $sql.="values ('$_POST[subgrupo]', '" . strtoupper($_POST[familia]) . "', 'A', '$data', '$_SESSION[id_usuario_sistema]')";
      mysqli_query($db, $sql);
      erro_sql("Insert Família", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0")
      {
        mysqli_commit($db);
        header("Location: ". URL."/modulos/familia/familia_inicial.php?i=t");
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/familia/familia_inicial.php?i=f");
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/buscar_aplic.php";
?>
    <script language="javascript" type="text/javascript" src="../../scripts/combo.js"></script>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(group, subgroup, family, flg){
        if(group.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          group.focus();
          return false;
        }
        if(subgroup.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          subgroup.focus();
          return false;
        }
        if(family.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          family.focus();
          family.select();
          return false;
        }
        flg.value="t";
        return true;
      }
      //-->
    </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./familia_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" disabled style="width: 200px">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Grupo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <select name="grupo" size="1" style="width: 200px" onChange="carregarCombo(this.value, '../../xml/subgrupo_combo.php', 'lista_subgrupo', 'opcao_subgrupo', 'subgrupo')">
                          <option value="0"> Selecione um Grupo </option>
                          <?php
                            $sql="select id_grupo, descricao from grupo where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                          ?>
                            <option value='<?php echo $grupo_info->id_grupo;?>'> <? echo strtoupper($grupo_info->descricao); ?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Subgrupo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <select name="subgrupo" id="subgrupo" size="1" style="width: 200px">
                           <option id="opcao_subgrupo"  value="0"> Selecione um Sub-Grupo </option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Família
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="familia" size="30" style="width: 500px" value="<?php if(isset($_POST[familia])){echo $_POST[familia];}?>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/familia/familia_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_inclusao.grupo, document.form_inclusao.subgrupo, document.form_inclusao.familia, document.form_inclusao.flag)){document.form_inclusao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="flag" value="f">
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
    <script language='javascript'>
    <!--
      var x=document.form_inclusao;
      if(x.grupo.selectedIndex==0){
        x.grupo.focus();
      }
      else{
        x.subgrupo.focus();
      }
    //-->
    </script>
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
