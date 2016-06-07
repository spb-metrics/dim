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
  //  Arquivo..: lcontrole_exclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de exclusao de lista de controle
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

    if(isset($_POST[codigo_atual])){
      $data=date("Y-m-d H:i:s");
      $sql="update lista_especial ";
      $sql.="set status_2='I', date_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]' ";
      $sql.="where id_lista_especial='$_POST[codigo_atual]'";
      mysqli_query($db, $sql);
      erro_sql("Update Lista Especial", $db, "");

      /////////////////////////////////////
      //SE INCLUS�O OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0"){
        mysqli_commit($db);
        $aux=$_POST[aux];
        header("Location: ". URL."/modulos/lcontrole/lcontrole_inicial.php?e=t&".$aux);
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/lcontrole/lcontrole_inicial.php?e=f");
      }
      exit();
    }
    else{
      if($_GET[codigo]==""){
        header("Location: ". URL."/modulos/lcontrole/lcontrole_inicial.php");
        exit();
      }
      else{
        $sql="select lista, descricao, livro_id_livro, flg_receita_controlada, flg_medicamento_controlado from lista_especial where id_lista_especial='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Lista Especial Escolhida", $db, "");
        if(mysqli_num_rows($res)>0){
          $lista_info=mysqli_fetch_object($res);
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript">
    <!--
      function trataDados(){
        var x=document.form_exclusao;
        var info = ajax.responseText;  // obt�m a resposta como string
        info=info.substr(0, 3);
        if(info=="SAV"){
          x.submit();
        }
        else{
          var msg="N�o � poss�vel excluir a lista, pois existe material associado!\n";
          window.alert(msg);
        }
      }

      function verificarExclusao(){
        var x=document.form_exclusao;
        var id=x.codigo_atual.value;
        var url = "../../xml/lcontroleExclusao.php?id=" + id;
        requisicaoHTTP("GET", url, true);
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
                  <form name="form_exclusao" action="./lcontrole_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Excluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        C�digo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" disabled value="<?php echo $lista_info->lista?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Lista de Controle Especial
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="descricao" maxlength="60" style="width: 500px" disabled value="<?php echo $lista_info->descricao?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Livro
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="livro" size="1" disabled style="width: 200px">
                           <option value="0"> Selecione um Livro </option>
                         <?php
                            $sql="select id_livro, descricao from livro";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Livro", $db, "");
                            while($livro_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $livro_info->id_livro;?>" <?php if($livro_info->id_livro==$lista_info->livro_id_livro){echo "selected";}?>> <?php echo $livro_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                       <img src="<? echo URL."/imagens/obrigat.gif";?>">Receita Controlada
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                       <input type="radio" name="controlada" value="S" <?php if($lista_info->flg_receita_controlada=="S"){echo "checked";}?> disabled> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                       <input type="radio" name="controlada" value="" <?php if($lista_info->flg_receita_controlada!="S"){echo "checked";}?> disabled> N�o
                      </td>
                        <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>">Medicamento Controlado
                        </td>
                        <td align="left" width="30%" class="campo_tabela">
                          <input type="radio" name="medicontrolado" value="S" <?php if($lista_info->flg_medicamento_controlado=="S"){echo "checked";}?> disabled> Sim
                          &nbsp; &nbsp; &nbsp; &nbsp;
                          <input type="radio" name="medicontrolado" value=""<?php if($lista_info->flg_medicamento_controlado!="S"){echo "checked";}?> disabled> N�o
                        </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/lcontrole/lcontrole_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
                        <input type="button" name="excluir" style="font-size: 12px;" value="Excluir >>" onclick="verificarExclusao();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
                      </td>
                    </tr>
                    <input type="hidden" name="codigo_atual" value="<?php echo $_GET[codigo];?>">
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET['pesquisa']?>">
                  </form>
                </table>
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
    require DIR."/footer.php";
  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else{
    include_once "../../config/erro_config.php";
  }
?>
