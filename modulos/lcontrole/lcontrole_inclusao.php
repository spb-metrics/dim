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
  //  Arquivo..: lcontrole_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de lista de controle
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }


    if(isset($_POST[codigo]) and isset($_POST[descricao])){
      $data=date("Y-m-d H:i:s");
      if($_POST[livro]==""){
        $_POST[livro]="null";
      }
      $sql="insert into lista_especial (lista, descricao, status_2, date_incl, usua_incl, livro_id_livro, flg_receita_controlada, flg_medicamento_controlado) ";
      $sql.="values ('" . strtoupper($_POST[codigo]) . "', '" . strtoupper($_POST[descricao]) . "', 'A', '$data', '$_SESSION[id_usuario_sistema]', $_POST[livro], '$_POST[controlada]', '$_POST[medicontrolado]')";
      mysqli_query($db, $sql);
      erro_sql("Insert Lista Especial", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0"){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/lcontrole/lcontrole_inicial.php?i=t");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/lcontrole/lcontrole_inicial.php?i=f");
      }
      exit();
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript">
      <!--
      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string
        info=info.substr(0, 3);
	    if(info=="SAV"){
          x.submit();
        }
        else{
          var msg="Lista já cadastrada!\n";
          window.alert(msg);
          x.codigo.focus();
          x.codigo.select();
        }
      }

      function verificarCodigo(){
        retirarEspaco();
        var x=document.form_inclusao;
        var codigo=x.codigo.value;
        var url = "../../xml/lcontroleCodigo.php?codigo=" + codigo;
        requisicaoHTTP("GET", url, true);
      }

      function retirarEspaco(){
        var x=document.form_inclusao;
        var codigo=x.codigo.value;
        while(codigo.match("  ")){
          codigo=codigo.replace("  ", " ");
        }
        if(codigo.charAt(0)==" "){
          codigo=codigo.substr(1, codigo.length);
        }
        if(codigo.charAt(codigo.length-1)==" "){
          codigo=codigo.substr(0, codigo.length-1);
        }
        x.codigo.value=codigo;
      }

      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var cod=x.codigo;
        var descr=x.descricao;
        if(cod.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          cod.focus();
          cod.select();
          return false;
        }
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        return true;
      }
      
      function salvarDados(){
        if(validarCampos()==true){
          verificarCodigo();
        }
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
                  <form name="form_inclusao" action="./lcontrole_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Lista de Controle Especial
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="descricao" maxlength="60" style="width: 500px">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Livro
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="livro" size="1" style="width: 200px">
                           <option value=""> Selecione um Livro </option>
                         <?php
                            $sql="select id_livro, descricao from livro where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Livro", $db, "");
                            while($livro_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $livro_info->id_livro;?>"> <?php echo $livro_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Receita Controlada
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                          <input type="radio" name="controlada" value="S"> Sim
                          &nbsp; &nbsp; &nbsp; &nbsp;
                          <input type="radio" name="controlada" value="" checked> Não
                      </td>
                      
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>">Medicamento Controlado
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                          <input type="radio" name="medicontrolado" value="S"> Sim
                          &nbsp; &nbsp; &nbsp; &nbsp;
                          <input type="radio" name="medicontrolado" value="" checked> Não
                      </td>

                      
                    </tr>

                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/lcontrole/lcontrole_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarDados();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                        </font>
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
      document.form_inclusao.codigo.focus();
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
  else{
    include_once "../../config/erro_config.php";
  }
?>
