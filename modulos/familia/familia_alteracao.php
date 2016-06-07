<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: familia_alteracao.php
  //  Bancos...: dbtDIM
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de alteracao de familia
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
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


    if(isset($_POST[flag]) && $_POST[flag]=="t"){
      $data=date("Y-m-d H:i:s");
      $sql="update familia ";
      $sql.="set subgrupo_id_subgrupo='$_POST[subgrupo]', descricao='" . strtoupper($_POST[familia]) . "', status_2='A', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]' ";
      $sql.="where id_familia='$_POST[codigo_antigo]'";
      mysqli_query($db, $sql);
      erro_sql("Update Fam�lia", $db, "");

      /////////////////////////////////////
      //SE INCLUS�O OCORREU SEM PROBLEMAS//
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
        erro_sql("Select Fam�lia Escolhida", $db, "");
        if(mysqli_num_rows($res)>0){
          $consulta=mysqli_fetch_object($res);
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
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
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          group.focus();
          return false;
        }
        if(subgroup.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          subgroup.focus();
          return false;
        }
        if(family.value==""){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
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
                        C�digo
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
                        Fam�lia
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
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
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
    //RODAP� DA P�GINA//
    ////////////////////
    require DIR."/footer.php";
  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
