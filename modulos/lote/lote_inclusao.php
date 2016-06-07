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
  //  Arquivo..: lote_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de lote
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if($_POST[flag]=="t"){
      $data=date("Y-m-d H:i:s");
      $valores=split("[|]", $_POST[lote]);
      $sql="update estoque ";
      $sql.="set flg_bloqueado='$_POST[bloqueado]', motivo_bloqueio='" . strtoupper($_POST[motivo]) . "', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]', data_bloqueio='$data', usua_bloqueio='$_SESSION[id_usuario_sistema]' ";
      $sql.="where lote='$valores[0]' and material_id_material='$_POST[codigo]' and fabricante_id_fabricante='$valores[1]'";
      mysqli_query($db, $sql);
      erro_sql("Update Estoque", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0"){
        mysqli_commit($db);
        if($_POST[bloqueado]==""){
          header("Location: ". URL."/modulos/lote/lote_inicial.php?a=t");
        }
        else{
          header("Location: ". URL."/modulos/lote/lote_inicial.php?i=t");
        }
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/lote/lote_inicial.php?i=f");
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
    <script language="javascript" type="text/javascript" src="../../scripts/loteUnidades.js"></script>
    <script language="javascript" type="text/javascript" src="../../scripts/mestoqueLote.js"></script>
    <script language="javascript">
      <!--
      var d=new Date();
      var ID=d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

      function popup_medicamento(){
        var height=350;
	    var width=450;
	    var left=(screen.availWidth-width)/2;
	    var top=(screen.availHeight-height)/2;

	    if(window.showModalDialog){
 		  var dialogArguments=new Object();
		  var _R=window.showModalDialog("../mestoque/pesquisa_material.php?id_operacao=lote", dialogArguments, "dialogWidth=450px;dialogHeight=350px;scroll=yes;status=no;");
		  if("undefined"!=typeof(_R)){
			SetNameMedicamento(_R.strArgs);
  		  }
	    }
	    //NS
	    else{
		  var left=(screen.width-width)/2;
		  var top=(screen.height-height)/2;
 		  var winHandle=window.open("../mestoque/pesquisa_material.php?id_operacao=lote", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		  winHandle.focus();
	    }
      }

      function SetNameMedicamento(argumentos){
        var valores=argumentos.split('|');

        var x=document.form_inclusao;
        if(valores[0]=="saida"  || valores[0]=="perda" || valores[0]=="lote"){
          x.codigo.value=valores[1];
          x.descricao.value=valores[2];
          x.flg_lote.value="t";
          x.descricao.focus();
        }
      }

      function removerLote(){
        var x=document.getElementById("lote");
        for(var i=x.length-1; i>0; i--){
          x.options[i].selected=true;
          x.remove(i);
        }
        document.getElementById("opcao_lote").innerHTML="Selecione um Lote";
      }

      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string
        x.codigo.value=info;
	    if(info!=""){
          x.flg_lote.value="t";
          buscarLote();
        }
        else{
          removerLote();
          x.quantidade.value="";
          removerLinhas();
          window.alert("Material não encontrado!");
          x.descricao.focus();
          x.descricao.select();
          x.salvar.disabled="true";
        }
      }

      function verificarMedicamento(){
        var y=document.form_inclusao;
        var descricao=y.descricao.value;
        var url = "../../xml/mestoqueVerificarMedicamento.php?descricao=" + descricao + "&aplicacao=lote";
        requisicaoHTTP("GET", url, true);
      }

      function buscarLote(){
        carregarLote('', 'flg_lote', 'lote', 'opcao_lote', '../../xml/mestoqueLote.php', 'codigo', '', '', 'lote');
        var x=document.form_inclusao;
        if(x.flg_lote.value=="t"){
          x.flg_lote.value="f";
          x.quantidade.value="";
          x.salvar.disabled="true";
          removerLinhas();
        }
      }
      
      function obterUnidades(){
        var x=document.form_inclusao;
        removerLinhas();
        if(x.lote.selectedIndex!=0){
          carregarUnidades("codigo", "lote", "tb_unidades", "../../xml/loteUnidades.php", "salvar", "quantidade");
        }
        else{
          x.quantidade.value="";
          x.salvar.disabled="true";
        }
      }
      
      function removerLinhas(){
        var tab=document.getElementById("tb_unidades");
        var total_linhas=tab.rows.length;
        for(var i=0; i<total_linhas; i++){
          var lnh="linha" + i;
          var linha=document.getElementById(lnh);
          if(linha){
            tab.deleteRow(linha.rowIndex);
          }
        }
      }
      
      function btSalvar(){
        if(validarCampos()){
          var x=document.form_inclusao;
          x.salvar.disabled="true";
          x.flag.value="t";
          x.submit();
        }
      }
      
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var cod=x.codigo;
        var descr=x.descricao;
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        if(cod.value==""){
          window.alert("Material Não Cadastrado!");
          descr.focus();
          descr.select();
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
                  <form name="form_inclusao" action="./lote_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="descricao" id="descricao" size="30" style="width: 500px"  onfocus="buscarLote();" onchange="verificarMedicamento();">
                        <div id="acDiv"></div>
                        <a href="JavaScript:popup_medicamento();"><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Pesquisar"></a>
                      </td>
                    </tr>
                    <input type="hidden" name="codigo" id="codigo" size="30" style="width: 200px">
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Lote
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="lote" id="lote" size="1" style="width: 500px" onchange="obterUnidades();">
                          <option value="0" id="opcao_lote"> Selecione um Lote </option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="quantidade" id="quantidade" size="30" style="width: 200px" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Bloqueado
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="radio" value="S" name="bloqueado" checked> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="" name="bloqueado"> Não
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Motivo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <textarea name="motivo" row="2" cols="31" style="width: 500px"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tb_unidades" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="50%" align="center"> Unidade </td>
                            <td width="50%" align="center"> Quantidade </td>
                          </tr>
                          <tr>
                            <td colspan="2" height="100%"></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/lote/lote_inicial.php'">
                        <input type="button" name="salvar" id="salvar" style="font-size: 12px;" value="Salvar >>" onclick="btSalvar();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="flag" value="f">

                    <input type="hidden" name="flg_lote" id="flg_lote" value="f">
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
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
?>

    <style type="text/css">
    <!--
      /* Definição dos estilos do DIV */
      /* CSS for the DIV */
      #acDiv{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDiv UL{ list-style:none; margin: 0; padding: 0; }
      #acDiv UL LI{ display:block;}
      #acDiv A{ color:#000000; text-decoration:none; }
      #acDiv A:hover{ color:#000000; }
      #acDiv LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>

    <script language="javascript" type="text/javascript" src="../../scripts/mestoque.js"></script>
    <script language="javascript">
    <!--
      //Instanciar objeto AutoComplete
      var AC = new dmsAutoComplete('descricao','acDiv', "", "", "lote");

      AC.ajaxTarget = '../../xml/mestoqueMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
      AC.chooseFunc = function(id,label){
        var x=document.form_inclusao;
        x.codigo.value = id;
        if(x.codigo.value!=""){
          x.flg_lote.value="t";
          buscarLote();
          x.lote.focus();
          x.quantidade.value="";
          removerLinhas();
          x.salvar.disabled="true";
        }
      }

      var x=document.form_inclusao;
      x.descricao.focus();
      x.salvar.disabled=true;
    //-->
    </script>

<?php

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
