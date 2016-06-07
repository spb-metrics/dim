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
  //  Arquivo..: familia_alteracao.php
  //  Bancos...: dbtDIM
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de alteracao de familia
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
      $sql="update familia ";
      $sql.="set subgrupo_id_subgrupo='$_POST[subgrupo]', descricao='" . strtoupper($_POST[familia]) . "', status_2='A', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]' ";
      $sql.="where id_familia='$_POST[codigo_antigo]'";
      mysqli_query($db, $sql);
      erro_sql("Update Família", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0")
      {
        mysqli_commit($db);
        $aux=$_POST[aux];
        header("Location: ". URL."/modulos/familia/familia_inicial.php?a=t&".$aux);
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/familia/familia_inicial.php?a=f");
      }
    }
    else{
      if($_GET[codigo]=="" && !isset($_POST[flag])){
        header("Location: ". URL."/modulos/familia/familia_inicial.php");
      }
      else{
        $sql="select f.id_familia, sbg.id_subgrupo, sbg.grupo_id_grupo, f.descricao ";
        $sql.="from familia as f, subgrupo as sbg ";
        $sql.="where f.subgrupo_id_subgrupo=sbg.id_subgrupo and id_familia='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Família Escolhida", $db, "");
        if(mysqli_num_rows($res)>0){
          $consulta=mysqli_fetch_object($res);
        }
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
          family.select()
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
                  <form name="form_alteracao" action="./familia_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" style="width: 200px" disabled value="<?php if(isset($_POST[codigo_antigo])){echo $_POST[codigo_antigo];}else{echo $consulta->id_familia;}?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Grupo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <select name="grupo" size="1" style="width: 200px" onChange="document.form_alteracao.codigo01.value=''; carregarCombo(this.value, '../../xml/subgrupo_combo.php', 'lista_subgrupo', 'opcao_subgrupo', 'subgrupo');">
                          <option value="0"> Selecione um Grupo </option>
                          <?php
                            $sql="select id_grupo, descricao from grupo where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $grupo_info->id_grupo;?>" <?php if(isset($_POST[grupo])){if($grupo_info->id_grupo==$_POST[grupo]){echo "selected";}}else{if($consulta->grupo_id_grupo==$grupo_info->id_grupo){echo "selected";}}?>> <?php echo $grupo_info->descricao;?> </option>
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
                      <?
                       $idsubgrupo = 0;
                       if (isset($consulta->id_subgrupo))
                          $idsubgrupo = $consulta->id_subgrupo;
                      ?>
                      <input type="hidden" name="codigo01" id="codigo01" value="<?echo $idsubgrupo?>">
                        <select name="subgrupo" id="subgrupo" size="1" style="width: 200px">
                          <option id="opcao_subgrupo" value="0"> Selecione um Sub-Grupo </option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Família
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="familia" size="30" style="width: 500px" value="<?php if(isset($_POST[familia])){echo $_POST[familia];}else{echo $consulta->descricao;}?>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/familia/familia_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_alteracao.grupo, document.form_alteracao.subgrupo, document.form_alteracao.familia, document.form_alteracao.flag)){document.form_alteracao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="codigo_antigo" value="<?php if(isset($_POST[codigo_antigo])){echo $_POST[codigo_antigo];}else{echo $_GET[codigo];}?>">
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET[pesquisa]?>">
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
      //Instanciar objeto Combo
       var AC = new carregarCombo(document.form_alteracao.grupo.value, '../../xml/subgrupo_combo.php', 'lista_subgrupo', 'opcao_subgrupo', 'subgrupo');
      document.form_alteracao.grupo.focus();
    //-->
    </script>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
