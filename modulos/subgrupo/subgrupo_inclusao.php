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
  //  Arquivo..: item_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de inclusao de subgrupo
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if(isset($_POST[grupo]) && isset($_POST[subgrupo])){
      $data=date("Y-m-d H:i:s");
      $sql="insert into subgrupo (grupo_id_grupo, descricao, status_2, data_incl, usua_incl) ";
      $sql.="values ('$_POST[grupo]', '" . trim(strtoupper($_POST[subgrupo])) . "', 'A', '$data', '$_SESSION[id_usuario_sistema]')";
      mysqli_query($db, $sql);
      erro_sql("Insert Subgrupo", $db, "");

      /////////////////////////////////////
      //SE INCLUS�O OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0"){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/subgrupo/subgrupo_inicial.php?i=t");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/subgrupo/subgrupo_inicial.php?i=f");
      }
      exit();
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/buscar_aplic.php";
?>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(group, subgroup){
        if(group.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          group.focus();
          return false;
        }
        if(subgroup.value==""){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          subgroup.focus();
          subgroup.select();
          return false;
        }
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
                  <form name="form_inclusao" action="./subgrupo_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Grupo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <select name="grupo" size="1" style="width: 200px">
                           <option value="0"> Selecione um Grupo </option>
                         <?php
                            $sql="select id_grupo, descricao from grupo where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $grupo_info->id_grupo;?>"> <?php echo $grupo_info->descricao;?> </option>
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
                        <input type="text" name="subgrupo" size="30" style="width: 500px" onkeypress="VerificarEnter();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/subgrupo/subgrupo_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_inclusao.grupo, document.form_inclusao.subgrupo)){document.form_inclusao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
                      </td>
                    </tr>
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
      document.form_inclusao.grupo.focus();
    //-->
    </script>
<?php
    ////////////////////
    //RODAP� DA P�GINA//
    ////////////////////
    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
