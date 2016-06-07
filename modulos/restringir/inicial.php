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
  //  Arquivo..: inicial.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de incial de restringir material
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
    require DIR."/header.php";

    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if($_POST[flag_salvar]=="t"){
      //deleta os medicamentos associados ao prescritor na tabela material_prescritor
      $sql="delete from material_prescritor
            where tipo_prescritor_id_tipo_prescritor='$_POST[prescritor]'";
      mysqli_query($db, $sql);
      erro_sql("Delete Material Prescritor", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      if($_POST[lista_materiais]!=""){
        $valor=split("[,]", $_POST[lista_materiais]);
        for($i=0; $i<count($valor); $i++){
          $sql="insert into material_prescritor (tipo_prescritor_id_tipo_prescritor, material_id_material) ";
          $sql.="values ('$_POST[prescritor]', '$valor[$i]')";
          mysqli_query($db, $sql);
          erro_sql("Insert Material Prescritor", $db, "");
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }
        }

        /////////////////////////////////////
        //SE INCLUSÃO OCORREU SEM PROBLEMAS//
        /////////////////////////////////////
        if($atualizacao==""){
          mysqli_commit($db);
          echo "<script>alert('Operação efetuada com sucesso!')</script>";
        }
        else{
          mysqli_rollback($db);
          echo "<script>alert('Não foi possível incluir o medicamento!')</script>";
        }
      }
      else{
        if($atualizacao==""){
          mysqli_commit($db);
          echo "<script>alert('Operação efetuada com sucesso!')</script>";
        }
        else{
          mysqli_rollback($db);
          echo "<script>alert('Não foi possível incluir o medicamento!')</script>";
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////

    if($_GET[aplicacao] <> ''){
      $_SESSION[cod_aplicacao]=$_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

    require "../../verifica_acesso.php";

?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript" type="text/javascript" src="../../scripts/restringirMedicamento.js"></script>
    <script language="javascript">
    <!--
      var d=new Date();
      var ID=d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

      function popup_medicamento(){
        obterDados();
        var height=350;
	    var width=450;
	    var left=(screen.availWidth-width)/2;
	    var top=(screen.availHeight-height)/2;

        var x=document.form_inclusao;
        var id_prescritor=x.prescritor.value;
        var itens=x.lista_materiais.value;
	    if(window.showModalDialog){
 		  var dialogArguments=new Object();
		  var _R=window.showModalDialog("../mestoque/pesquisa_material.php?id_operacao=restringir&id_prescritor=" + id_prescritor + "&itens=" + itens, dialogArguments, "dialogWidth=450px;dialogHeight=350px;scroll=yes;status=no;");
		  if("undefined"!=typeof(_R)){
			SetNameMedicamento(_R.strArgs);
  		  }
	    }
	    //NS
	    else{
		  var left=(screen.width-width)/2;
		  var top=(screen.height-height)/2;
 		  var winHandle=window.open("../mestoque/pesquisa_material.php?id_operacao=restringir&id_prescritor=" + id_prescritor + "&itens=" + itens, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		  winHandle.focus();
	    }
      }

      function SetNameMedicamento(argumentos){
        var valores=argumentos.split('|');

        var x=document.form_inclusao;
        if(valores[0]=="restringir"){
          x.codigo.value=valores[1] + "|" + valores[2];
          x.descricao.value=valores[3];
          x.flag_pesquisa.value="t";
          adicionarItem();
          x.descricao.focus();
        }
      }

      function salvarDados(){
        var x=document.form_inclusao;
        x.salvar.disabled="true";
        obterDados();
        x.flag_salvar.value="t";
        x.submit();
      }

      function adicionarItem(){
        var x=document.form_inclusao;
        if(x.flag_pesquisa.value=="t"){
          inserirLinha();
          x.flag_pesquisa.value="f";
        }
      }

      function obterDados(){
        var itens=document.getElementById("tabela_aux");
        var total_linhas=itens.rows.length;
        var info="";
        for(var i=1; i<total_linhas; i++){
            info=info + itens.rows[i].cells[0].innerHTML + ",";
        }

        document.getElementById("lista_materiais").value=info.substr(0, info.length-1);
      }

      function abrirPesquisa(){
        obterDados();
        var x=document.form_inclusao;
        var id_prescritor=x.prescritor.value;
        var itens=x.lista_materiais.value;
        var url="<?php echo URL;?>/modulos/mestoque/pesquisa_material.php?id_operacao=restringir&id_prescritor=" + id_prescritor + "&itens=" + itens;
        abrir_janela(url);
      }

      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string
        x.codigo.value=info;
	    if(info==""){
          window.alert("Material não encontrado!");
          x.descricao.focus();
          x.descricao.select();
        }
        else{
          inserirLinha();
        }
      }

      function verificarMedicamento(){
        var y=document.form_inclusao;
        var descricao=y.descricao.value;
        var url = "../../xml/mestoqueVerificarMedicamento.php?descricao=" + descricao + "&aplicacao=restringir";
        requisicaoHTTP("GET", url, true);
      }

      function inserirLinha(){
        var x=document.form_inclusao;
        var achou=false;
        var itens=document.getElementById("tabela");
        var itens_aux=document.getElementById("tabela_aux");
        var total_linhas=itens.rows.length;
        var cont=x.contador.value;
        for(var i=1; i<total_linhas; i++){
          var valor=itens_aux.rows[i].cells[0].innerHTML + "|" + itens.rows[i].cells[0].innerHTML;
          if(valor==x.codigo.value){
            achou=true;
          }
        }

        if(achou==true){
          window.alert("Material já adicionado!");
          document.form_inclusao.descricao.focus();
          return false;
        }
        else{
          var tab=itens.insertRow(total_linhas);
          tab.id="linha" + cont;
          tab.className="campo_tabela";

          var valores=x.codigo.value.split("|");
          var id_material=valores[0];
          var codigo_material=valores[1];

          //codigo material
          var cel0=tab.insertCell(0);
          cel0.align="left";
          cel0.innerHTML=codigo_material;

          //descricao material
          var cel1=tab.insertCell(1);
          cel1.align="left";
          cel1.innerHTML=x.descricao.value;

          var cel2=tab.insertCell(2);
          cel2.align="center";
          var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
          var urlRemover="javascript:removerLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "')";
          cel2.innerHTML=linkRemover.link(urlRemover);

          //tabela auxiliar
          var tab_aux=itens_aux.insertRow(total_linhas);
          tab_aux.id="linha_aux" + cont;
          tab_aux.className="campo_tabela";

          //id material
          var cel0=tab_aux.insertCell(0);
          cel0.align="left";
          cel0.innerHTML=id_material;

          //descricao material
          var cel1=tab_aux.insertCell(1);
          cel1.align="left";
          cel1.innerHTML=x.descricao.value;

          var cel2=tab_aux.insertCell(2);
          cel2.align="left";
          cel2.innerHTML=cont;

          x.contador.value=(parseInt(cont, 10)+1);

          limpaCampos();
          x.descricao.focus();
          x.salvar.disabled="";
          return true;
        }
      }

      function limpaCampos(){
        var x=document.form_inclusao;
        x.descricao.value="";
        x.codigo.value="";
      }

      function removerLinha(lnh, lnh_aux, pos){
        var tab=document.getElementById("tabela");
        tab.deleteRow(document.getElementById(lnh).rowIndex);
        var tab_aux=document.getElementById("tabela_aux")
        tab_aux.deleteRow(document.getElementById(lnh_aux).rowIndex);

        var total_linhas=tab.rows.length;
        for(var i=pos; i<total_linhas; i++){
          var j=tab_aux.rows[i].cells[2].innerHTML;
          tab.rows[i].deleteCell(2);
          var cel2=tab.rows[i].insertCell(2);
          cel2.align="center";
          var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
          var urlRemover="javascript:removerLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "')";
          cel2.innerHTML=linkRemover.link(urlRemover);
        }
      }

      function removerLinhas(){
        var tab=document.getElementById("tabela");
        var tab_aux=document.getElementById("tabela_aux");
        var total_linhas_aux=tab_aux.rows.length;

        for(var i=total_linhas_aux; i>1; i--){
          var j=tab_aux.rows[i-1].cells[2].innerHTML;
          var lnh_aux="linha_aux" + j;
          var linha_aux=document.getElementById(lnh_aux);
          if(linha_aux){
            tab_aux.deleteRow(linha_aux.rowIndex);
          }
          var lnh="linha" + j;
          var linha=document.getElementById(lnh);
          if(linha){
            tab.deleteRow(linha.rowIndex);
          }
        }
      }

      function buscarMedicamento(){
        carregarMedicamentos("tabela", "tabela_aux", "../../xml/restringirMedicamento.php", "salvar", "prescritor", "<?php echo URL;?>", "contador", "flag_exclusao", "flag_inclusao");
      }

      function desabilitarCampos(){
        var x=document.form_inclusao;
        removerLinhas();
        x.descricao.value="";
        x.codigo.value="";
        x.contador.value="";
        if(x.prescritor.selectedIndex==0){
          x.descricao.disabled="true";
          x.salvar.disabled="true";
          document.getElementById("tela_material").style.display="none";
        }
        else{
          if(x.flag_inclusao.value==""){
            x.descricao.disabled="true";
            document.getElementById("tela_material").style.display="none";
          }
          else{
            x.descricao.disabled="";
            document.getElementById("tela_material").style.display="";
          }
          buscarMedicamento();
        }
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
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='4'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./inicial.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao; ?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Profissional
                      </td>
                      <td colspan="2" class="campo_tabela" valign="middle" width="100%">
                        <select name="prescritor" id="prescritor" size="1" style="width:200px;" onchange="desabilitarCampos();">
                          <option value="0"> Selecione Profissional </option>
                          <?php
                            $sql = "select * from tipo_prescritor where status_2 = 'A' order by descricao";
                            $res = mysqli_query($db, $sql);
                            erro_sql("Select Prescritor", $db, "");
                            while($prescritor_info = mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $prescritor_info->id_tipo_prescritor;?>"> <?php echo $prescritor_info->descricao;?> </option>
                          <?
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Material
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%">
                        <input type="hidden" name="codigo" id="codigo">
                        <input type="text" name="descricao" id="descricao" style="width:500px;" disabled onchange="verificarMedicamento();" onkeypress="return VerificarEnter(event);">
                        <div id="acDiv"></div>
                      </td>
                      <td class="campo_tabela"  width="100%">
                        <div id="tela_material" style="display:none">
                          <img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Pesquisar" onclick="popup_medicamento();">
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tabela" width="100%" cellpadding="0" cellspacing="1" border="0">
                          <tr class="coluna_tabela">
                            <td width='20%' align='center'>
                              Código
                            </td>
                            <td width='70%' align='center'>
                              Material
                            </td>
                            <td width='10%' align='center'></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" align="right" bgcolor="#D8DDE3" height="35">
                        <input style="font-size: 10px;" type="button" name="salvar" id="salvar" value="Salvar >>" disabled onclick="salvarDados();";>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <div style="display:none">
                          <table id="tabela_aux" width="100%" cellpadding="0" cellspacing="1" border="0">
                            <tr class="coluna_tabela">
                              <td width='20%' align='center'>
                                ID Material
                              </td>
                              <td width='70%' align='center'>
                                Material
                              </td>
                              <td width='10%' align='center'>
                                Linha
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <input type= "hidden" name="flag_salvar" id="flag_salvar" value="f">
                    <input type= "hidden" name="contador" id="contador" value="">
                    <input type= "hidden" name="lista_materiais" id="lista_materiais" value="">
                    <input type= "hidden" name="flag_pesquisa" id="flag_pesquisa" value="f">
                    <input type= "hidden" name="flag_inclusao" id="flag_inclusao" value="<?php echo $inclusao_perfil;?>">
                    <input type= "hidden" name="flag_exclusao" id="flag_exclusao" value="<?php echo $exclusao_perfil;?>">
                  </form>
                </table>
              </td>
            </tr>
            <tr><td colspan="4" height="100%"></td></tr>
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
      var AC = new dmsAutoComplete('descricao','acDiv', "", "", "restringir");

      AC.ajaxTarget = '../../xml/mestoqueMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
      AC.chooseFunc = function(id,label){
        document.form_inclusao.codigo.value=id;
        if(id!=""){
          inserirLinha();
        }
      }

      var x=document.form_inclusao;
      x.prescritor.focus();
    //-->
    </script>
<?php

    if($_GET[i]=='t'){echo "<script>alert('Operação efetuada com sucesso!')</script>";}
    if($_GET[i]=='f'){echo "<script>alert('Não foi possível incluir o medicamento!')</script>";}
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
