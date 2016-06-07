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
  //  Arquivo..: tmovimento_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 13/12/2007
  //  Analista.: Ricieri Rocha Conz
  //  Função...: Tela de inclusão Tipo Movimento
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


    if(isset($_POST[descricao])){

      $data=date("Y-m-d H:i:s");
      $sql="insert into tipo_movto (descricao, operacao, flg_movto, flg_movto_bloqueado, flg_movto_vencido, status_2, data_incl, usua_incl) ";
      $sql.="values ('" . strtolower($_POST[descricao]) . "','$_POST[operacao]',";
      
      if($_POST[flg_movto]==""){
        $sql.="NULL,";
      }
      else{
        $sql.= "'$_POST[reverter_movto]',";
      }
      
      if($_POST[flg_movto_bloqueado]==""){
        $sql.="NULL,";
      }
      else{
        $sql.= "'$_POST[flg_movto_bloqueado]',";
      }
     
     
     if ($_POST[flg_lote_vencido]==""){
       $sql.="NULL,";
     }
     else{
       $sql.="'$_POST[flg_lote_vencido]',";
     }
     $sql.="'A', '$data', '$_SESSION[id_usuario_sistema]')";
      
     //echo $sql;
     // exit;
      
      mysqli_query($db, $sql);
      erro_sql("Insert Incluir Tipo Movto", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0")
      {
        mysqli_commit($db);
        header("Location: ". URL."/modulos/tmovimento/tmovimento_inicial.php?i=t");
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/tmovimento/tmovimento_inicial.php?i=f");
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
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
        var x=document.form_inclusao;
        if(x.descricao.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          x.descricao.focus();
          x.descricao.select();
          return false;
        }
        
        if(x.operacao.value=="0"){
          window.alert("Favor Preencher os Campos Obrigatórios!");
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
                  <form name="form_inclusao" action="./tmovimento_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="5" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="4" valign="middle" width="83%">
                        <input type="text" name="codigo" size="30" style="width: 180px" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Movimento
                      </td>
                      <td class="campo_tabela" colspan="4" valign="middle" width="35%">
                        <input type="text" name="descricao" size="60" style="width: 400px">
                      </td>
                     
                    </tr>
                    <tr>
                     <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Operação
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="operacao" style="width: 180px">
                          <option value="0"> Selecione uma operação </option>
                          <option value="entrada"> Entrada </option>
                          <option value="saida"> Saída </option>
                          <option value="perda"> Perda </option>
                        </select>
                      </td>
                       <td class="descricao_campo_tabela" valign="middle" width="17%" >
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Movto Administrativo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="20%" height="22">
                        <input type="radio" value="s" name="reverter_movto" >Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="" name="reverter_movto" checked>Não
                      </td>
                    </tr>
                    <tr>
                       <td class="descricao_campo_tabela" valign="middle" width="17%" height="22">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Lote Bloqueado
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="flg_movto_bloqueado" style="width: 180px">
                          <option value="">  </option>
                          <option value="s"> Sim </option>
                          <option value="n"> Não </option>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="17%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Lote Vencido
                      </td>
                      <td class="campo_tabela" valign="middle" width="40%">
                        <select name="flg_lote_vencido" style="width: 180px">
                          <option value="">  </option>
                          <option value="s"> Sim </option>
                          <option value="n"> Não </option>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="6"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/tmovimento/tmovimento_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos()){document.form_inclusao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="6" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <tr>
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
      document.form_inclusao.descricao.focus();
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
