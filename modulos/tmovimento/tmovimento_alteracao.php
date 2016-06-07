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
  //  Arquivo..: tmovimento_alteracao.php
  //  Bancos...: dbtdim
  //  Data.....: 13/12/2007
  //  Analista.: Ricieri Rocha Conz
  //  Fun��o...: Tela de Exclus�o Tipo de Movimento
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

    if(isset($_POST[descricao])){
      $data=date("Y-m-d H:i:s");
      $sql="update tipo_movto ";
      $sql.="set descricao='" . strtolower($_POST[descricao]) . "',operacao='$_POST[operacao]',";
      
       if($_POST[reverter_movto]==""){
        $sql.=" flg_movto=NULL,";
      }
      else{
        $sql.= "flg_movto='$_POST[reverter_movto]',";
      }

      if($_POST[flg_movto_bloqueado]==""){
        $sql.="flg_movto_bloqueado=NULL,";
      }
      else{
        $sql.= "flg_movto_bloqueado='$_POST[flg_movto_bloqueado]',";
      }


     if ($_POST[flg_movto_vencido]==""){
       $sql.="flg_movto_vencido=NULL,";
     }
     else{
       $sql.="flg_movto_vencido='$_POST[flg_movto_vencido]',";
     }
     
     $sql.="status_2='A', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]' ";
     $sql.="where id_tipo_movto='$_POST[codigo_antigo]'";
  //echo $sql;
  //exit;
      mysqli_query($db, $sql);
      erro_sql("Update Altera��o Tipo de Movimento", $db, "");

      /////////////////////////////////////
      //SE INCLUS�O OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0")
      {
        mysqli_commit($db);
        $aux=$_POST[aux];
        header("Location: ". URL."/modulos/tmovimento/tmovimento_inicial.php?a=t&".$aux);
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/tmovimento/tmovimento_inicial.php?i=f");
      }
    }
    else{
      if($_GET[codigo]==""){
        header("Location: ". URL."/modulos/tmovimento/tmovimento_inicial.php");
      }
      else{
        $sql="select id_tipo_movto, descricao, operacao, flg_movto, flg_movto_bloqueado, flg_movto_vencido from tipo_movto where id_tipo_movto='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Altera��o Tipo Movimento", $db, "");
        if(mysqli_num_rows($res)>0){
          $grupo_info=mysqli_fetch_object($res);
        }
      }
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
     function validarCampos(){
        var x=document.form_alteracao;
        if(x.descricao.value==""){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          x.descricao.focus();
          x.descricao.select();
          return false;
        }
        
         if(x.operacao.value=="0"){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          x.operacao.focus();
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
              <td colspan='10'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_alteracao" action="./tmovimento_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="5" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        C�digo
                      </td>
                      <td class="campo_tabela" colspan="4" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" style="width: 180px" disabled value="<?php echo $grupo_info->id_tipo_movto?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Movimento
                      </td>
                      <td class="campo_tabela" colspan="4" valign="middle" width="35%">
                        <input type="text" name="descricao" size="60" style="width: 400px" value="<?php echo $grupo_info->descricao?>">
                      </td>
                    </tr>
                    <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Opera��o
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="operacao" style="width: 180px" >
                          <option value="0"> Selecione uma opera��o </option>
                          <option value="entrada"<?php if($grupo_info->operacao=="entrada"){echo "selected";}?>> Entrada</option>
                          <option value="saida"<?php if($grupo_info->operacao=="saida"){echo "selected";}?>> Sa�da</option>
                          <option value="perda"<?php if($grupo_info->operacao=="perda"){echo "selected";}?>> Perda</option>
                        </select>
                      </td>

                      <td class="descricao_campo_tabela" valign="middle" width="17%" height="22" >
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Movto Administrativo
                      </td>
                      <td class="campo_tabela"  colspan="2" valign="middle" width="20%">
                        <input type="radio" value="s" name="reverter_movto" <?php if($grupo_info->flg_movto=="s"){echo "checked";}?>>Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="" name="reverter_movto" <?php if($grupo_info->flg_movto==""){echo "checked";}?>>N�o
                      </td>
                    </tr>
                    <tr>
                       <td class="descricao_campo_tabela" valign="middle" width="17%" height="22">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Lote Bloqueado
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="flg_movto_bloqueado" style="width: 180px" >
                          <option value=""<?php if($grupo_info->flg_movto_bloqueado==""){echo "selected";}?>> </option>
                          <option value="s"<?php if($grupo_info->flg_movto_bloqueado=="s"){echo "selected";}?>> Sim</option>
                          <option value="n"<?php if($grupo_info->flg_movto_bloqueado=="n"){echo "selected";}?>> N�o</option>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Lote Vencido
                      </td>
                      <td class="campo_tabela" valign="middle" width="40%">
                        <select name="flg_movto_vencido" style="width: 180px" >
                          <option value=""<?php if($grupo_info->flg_movto_vencido==""){echo "selected";}?>> </option>
                          <option value="s"<?php if($grupo_info->flg_movto_vencido=="s"){echo "selected";}?>> Sim</option>
                          <option value="n"<?php if($grupo_info->flg_movto_vencido=="n"){echo "selected";}?>> N�o</option>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="6"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/tmovimento/tmovimento_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos()){document.form_alteracao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="6" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
                      </td>
                    </tr>
                    <tr>
                    <input type="hidden" name="codigo_antigo" value="<?php echo $_GET[codigo];?>">
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
      document.form_alteracao.descricao.focus();
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
