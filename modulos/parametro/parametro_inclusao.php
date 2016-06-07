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
  //  Arquivo..: parametro_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 05/12/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de inclusao de parametro
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

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }


    if(isset($_POST[cidade]) && isset($_POST[cnpj])){
      $sql="update parametro set email_msg_erro='$_POST[email]', ";
      $sql.="validade_arq_crm='$_POST[validade]', nome_arquivo='" . strtoupper($_POST[arquivo]) . "', ";
      $sql.="setor_farmacia='$_POST[setor]', cod_operacao='" . strtoupper($_POST[codigo]) . "', ";
      $sql.="dias_bloqueio_paciente='$_POST[bloquear]', caminho_banco_integra='$_POST[caminho]', ";
      $sql.="caminho_logo_empresa='$_POST[empresa]', caminho_logo_dim='$_POST[dim]', cnpj_empresa='$_POST[cnpj]', ";
      $sql.="qtde_pedido_bec='$_POST[exibir]', cidade_id_cidade='$_POST[cidade]', ";
      $sql.="usuario_integra_local='$_POST[usuario]', senha_integra_local='$_POST[senha]'";
      mysqli_query($db, $sql);
      erro_sql("Update Par�metro", $db, "");
      /////////////////////////////////////
      //SE INCLUS�O OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0")
      {
        mysqli_commit($db);
        header("Location: ". URL."/modulos/parametro/parametro_inclusao.php?i=t");
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/parametro/parametro_inclusao.php?i=f");
      }
    }
    else{
      $sql="select * from parametro";
      $res=mysqli_query($db, $sql);
      erro_sql("Select par�metro", $db, "");
      if(mysqli_num_rows($res)>0){
        $parametro_info=mysqli_fetch_object($res);
      }
    }
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require "../../verifica_acesso.php";

    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(cnpj, cid){
        if(cnpj.value==""){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          cnpj.focus();
          cnpj.select();
          return false;
        }
        if(cid.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigat�rios!");
          cid.focus();
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
            <tr><td> <?php echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./parametro_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Arquivo de Profissional
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="arquivo" size="30" style="width: 200px" value="<?php echo $parametro_info->nome_arquivo;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Validade Arquivo
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="validade" size="30" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php echo $parametro_info->validade_arq_crm;?>">
                        dias
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Email p/ receber resultado do processamento
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="email" size="30" style="width: 500px" value="<?php echo $parametro_info->email_msg_erro;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Setor de Integra��o com o DIM
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="setor" size="30" style="width: 200px" value="<?php echo $parametro_info->setor_farmacia;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Cod. Opera��o
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" style="width: 200px" value="<?php echo $parametro_info->cod_operacao;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Logo da Empresa
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="empresa" size="30" style="width: 500px" value="<?php echo $parametro_info->caminho_logo_empresa;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Logo do DIM
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="dim" size="30" style="width: 500px" value="<?php echo $parametro_info->caminho_logo_dim;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Caminho do banco para transfer�ncia de saldo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="caminho" size="30" style="width: 500px" value="<?php echo $parametro_info->caminho_banco_integra;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Bloquear paciente � partir de
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="bloquear" size="30" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php echo $parametro_info->dias_bloqueio_paciente;?>">
                        dias
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CNPJ
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="cnpj" size="30" style="width: 200px" value="<?php echo $parametro_info->cnpj_empresa;?>" onkeydown="return FormataCNPJ(this, event);">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Cidade
                      </td>
                      <?php
                        $sql="select * from cidade order by nome";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Cidade", $db, "");
                      ?>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="cidade" size="1" style="width: 200px">
                          <option value="0"> Selecione uma Cidade </option>
                          <?php
                            while($cidade_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $cidade_info->id_cidade;?>" <?php if($cidade_info->id_cidade==$parametro_info->cidade_id_cidade){echo "selected";}?>> <?php echo $cidade_info->nome;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Exibir no m�ximo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="exibir" size="30" style="width: 200px" value="<?php echo $parametro_info->qtde_pedido_bec;?>" onKeyPress="return isNumberKey(event);">
                        pedidos
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Usu�rio Integra��o Local
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="usuario" size="30" style="width: 200px" value="<?php echo $parametro_info->usuario_integra_local;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Senha Integra��o Local
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="password" name="senha" size="30" style="width: 200px" value="<?php echo $parametro_info->senha_integra_local;?>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/start.php'">
                        <?php
                          if($alteracao_perfil!=""){
                        ?>
                          <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_inclusao.cnpj, document.form_inclusao.cidade)){document.form_inclusao.submit();}">
                        <?php
                          }
                          else{
                        ?>
                          <input type="submit" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="return validarCampos(document.form_inclusao.cnpj, document.form_inclusao.cidade);" disabled>
                        <?php
                          }
                        ?>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
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
    <script language="javascript">
    <!--
      document.form_inclusao.arquivo.focus();
    //-->
    </script>
<?php
    ////////////////////
    //RODAP� DA P�GINA//
    ////////////////////
    require DIR."/footer.php";

    if($_GET[i]=='t'){echo "<script>window.alert('Opera��o efetuada com sucesso!')</script>";}
    if($_GET[i]=='f'){echo "<script>window.alert('N�o foi poss�vel configurar os par�metros!')</script>";}

  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
