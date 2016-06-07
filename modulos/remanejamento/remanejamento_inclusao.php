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
  //  Arquivo..: remanejamento_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de remanejamento
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

    $mostrar_responsavel_dispensacao=$_GET[responsavel];

    if($_POST[flag]=="t"){
      if($_POST[id_login]==""){
        $_POST[id_login]=$_SESSION[id_usuario_sistema];
	  }
	  
	  $lista_materiais = $_POST['lista_materiais'];
	  
      $valor=split("[|]", substr($lista_materiais, 0, (strlen($lista_materiais)-1)));
      for($i=0; $i<count($valor); $i++){
        $valores[]=split("[,]", substr($valor[$i], 0, (strlen($valor[$i])-1)));
      }
      //obtem data do sistema
      $data=date("Y-m-d H:i:s");

      //insercao de uma solicitacao na tabela solicita_remanej
      $sql="insert into solicita_remanej (id_unid_solicitada, id_unid_solicitante, data_incl, usua_incl, status_2) ";
      $sql.="values ('$_POST[unidade_solicitada]', '$_SESSION[id_unidade_sistema]', '$data', '$_POST[id_login]', 'SOLICITADA')";
	  mysqli_query($db, $sql);
      erro_sql("Insert Solicita Remanej", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }

      //obtem id da solicitacao inserida na tabela solicita_remanej
      $sql="select id_solicita_remanej from solicita_remanej where id_unid_solicitada='$_POST[unidade_solicitada]' ";
      $sql.="and id_unid_solicitante='$_SESSION[id_unidade_sistema]' and data_incl='$data' and ";
      $sql.="usua_incl='$_POST[id_login]' and status_2='SOLICITADA'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Id Solicita Remanej", $db, "");
      if(mysqli_num_rows($res)>0){
        $chave=mysqli_fetch_object($res);
      }

      //para cada solicitacao, insercao de varias linhas na tabela itens_solicita_remanej
      for($i=0; $i<count($valores); $i++){
        //eh uma insercao
        $sql="insert into item_solicita_remanej (id_solicita_remanej, material_id_material, qtde_solicita, qtde_atendida) ";
        $sql.="values ('$chave->id_solicita_remanej', '" . $valores[$i][0] . "', '" . $valores[$i][1] . "', '0')";
        mysqli_query($db, $sql);
        erro_sql("Insert Item Solicita Remanej", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
      }

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if($atualizacao==""){
        mysqli_commit($db);
        echo "<script>window.alert('Operação efetuada com sucesso!')</script>";
      }
      else{
        mysqli_rollback($db);
        echo "<script>window.alert('Não foi possível realizar a solicitação de remanejamento!')</script>";
      }
      echo "<script>window.location='./remanejamento_inicial.php?aplicacao=$_SESSION[APLICACAO]';</script>";
      exit();
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";

    $sql="select num_max_material from parametro";
    $res=mysqli_query($db, $sql);
    erro_sql("Select parametro", $db, "");
    if(mysqli_num_rows($res)>0){
      $qtde_max=mysqli_fetch_object($res);
      $max_itens=$qtde_max->num_max_material;
      if($max_itens==""){
        $max_itens="vazio";
      }
    }
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
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
		  var _R=window.showModalDialog("../mestoque/pesquisa_material.php?id_operacao=remanejamento", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
		  if("undefined"!=typeof(_R)){
			SetNameMedicamento(_R.strArgs);
  		  }
	    }
	    //NS
	    else{
		  var left=(screen.width-width)/2;
		  var top=(screen.height-height)/2;
 		  var winHandle=window.open("../mestoque/pesquisa_material.php?id_operacao=remanejamento", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		  winHandle.focus();
	    }
      }

      function SetNameMedicamento(argumentos){
        var valores=argumentos.split('|');

        var x=document.form_inclusao;
        if(valores[0]=="remanejamento"){
          x.codigo.value=valores[1] + "|" + valores[2];
          x.descricao.value=valores[3];
          x.descricao.focus();
        }
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
        var login_senha=info.split("@");
        if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
          window.alert("Login e/ou Senha Inválidos!");
          x.login.focus();
          return;
        }
        if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
          x.id_login.value=login_senha[1];
          salvarDados();
          return;
        }
      }

     function verificarMedicamento(){
        var y=document.form_inclusao;
        var descricao=y.descricao.value;
        var tam = descricao.length;

		//alert(descricao);
		
 /*for (var i=0; i<tam; i++)
       {
          descricao = descricao.replace("+","~");
       }*/

        var url = "../../xml/mestoqueVerificarMedicamento.php?descricao=" + descricao + "&aplicacao=remanejamento";
        requisicaoHTTP("GET", url, true);

      }

      var cont=1;
      function inserirLinha(){
        if(validarCampos()==true){
          var achou=false;

          var itens=document.getElementById("tabela");
          var total_linhas=itens.rows.length;

          var itens_aux=document.getElementById("tabela_aux");
          var total_linhas_aux=itens_aux.rows.length;

          if("<?php echo $max_itens;?>"!="vazio"){
            if(total_linhas><?php echo $max_itens;?>){
              window.alert("Este material não será adicionado.\nO limite máximo para cada documento é de <?php echo $max_itens;?> materiais.\nClique no botão salvar para efetuar a operação com os <?php echo $max_itens;?> materiais adicionados anteriormente.");
              return false;
            }
          }

          var x=document.form_inclusao;

          for(var i=1; i<total_linhas_aux; i++){
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

            //quantidade
            var cel2=tab.insertCell(2);
            cel2.align="right";
            cel2.innerHTML=parseInt(x.quantidade.value, 10);

            var cel3=tab.insertCell(3);
            cel3.align="center";
            var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
            var urlAlterar="javascript:alterarLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "')";
            cel3.innerHTML=linkAlterar.link(urlAlterar);

            var cel4=tab.insertCell(4);
            cel4.align="center";
            var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
            var urlRemover="javascript:removerLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "')";
            cel4.innerHTML=linkRemover.link(urlRemover);

            //tabela auxiliar
            var tab_aux=itens_aux.insertRow(total_linhas_aux);
            tab_aux.id="linha_aux" + cont;
            tab_aux.className="campo_tabela";

            //id material
            var cel0=tab_aux.insertCell(0);
            cel0.align="left";
            cel0.innerHTML=id_material;

            //quantidade
            var cel1=tab_aux.insertCell(1);
            cel1.align="right";
            cel1.innerHTML=parseInt(x.quantidade.value, 10);

            var cel2=tab_aux.insertCell(2);
            cel2.align="right";
            cel2.innerHTML=cont;

            cont++;

            limpaCampos();
            document.form_inclusao.descricao.focus();
            if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
              document.getElementById("salvar").disabled=true;
            }
            else{
              document.getElementById("salvar").disabled="";
            }
            return true;
          }
        }
      }

      function limpaCampos(){
      //alert ('oi');
        var x=document.form_inclusao;
        x.descricao.value="";
        x.codigo.value="";
        x.quantidade.value="";
      }

      function removerLinha(lnh, lnh_aux, pos){
        var tab=document.getElementById("tabela");
        tab.deleteRow(document.getElementById(lnh).rowIndex);
        var tab_aux=document.getElementById("tabela_aux")
        tab_aux.deleteRow(document.getElementById(lnh_aux).rowIndex);

        var total_linhas=tab.rows.length;
        for(var i=pos; i<total_linhas; i++){
          var j=tab_aux.rows[i].cells[2].innerHTML;
          tab.rows[i].deleteCell(3);
          var cel3=tab.rows[i].insertCell(3);
          cel3.align="center";
          var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
          var urlAlterar="javascript:alterarLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "')";
          cel3.innerHTML=linkAlterar.link(urlAlterar);

          tab.rows[i].deleteCell(4);
          var cel4=tab.rows[i].insertCell(4);
          cel4.align="center";
          var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
          var urlRemover="javascript:removerLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "')";
          cel4.innerHTML=linkRemover.link(urlRemover);
        }
        if(total_linhas<=1){
          document.getElementById("salvar").disabled="true";
        }
        document.form_inclusao.quantidade.focus();
      }

      function alterarLinha(lnh, lnh_aux, pos){
        var itens=document.getElementById("tabela");
        var itens_aux=document.getElementById("tabela_aux");
        var x=document.form_inclusao;

        x.descricao.value=itens.rows[pos].cells[1].innerHTML;
        x.codigo.value=itens_aux.rows[pos].cells[0].innerHTML + "|" + itens.rows[pos].cells[0].innerHTML;
        x.quantidade.value=itens_aux.rows[pos].cells[1].innerHTML;
        removerLinha(lnh, lnh_aux, pos);
      }

      function salvarDados(){
        var x=document.form_inclusao;
        x.salvar.disabled="true";
        var itens=document.getElementById("tabela_aux");
        var total_linhas=itens.rows.length;
        var lista=new Array(total_linhas);
        for(var i=1; i<lista.length; i++){
          lista[i]=new Array(2);
        }
        var info="";
        for(var i=1; i<lista.length; i++){
          for(var j=0; j<lista[i].length; j++){
            info=info + itens.rows[i].cells[j].innerHTML + ",";
          }
          info=info + "|";
        }

        document.getElementById("lista_materiais").value=info;
        x.flag.value='t';
        x.submit();
      }

      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var unid=x.unidade_solicitada;
        var mat=x.codigo;
        var descr=x.descricao;
        var qtde=x.quantidade;

        if(unid.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          unid.focus();
          return false;
        }
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        if(mat.value==""){
          window.alert("Material Não Cadastrado!");
          descr.focus();
          descr.select();
          return false;
        }
        if(qtde.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          qtde.focus();
          return false;
        }
        if(parseInt(qtde.value, 10)==0){
          window.alert("Quantidade Igual a Zero!");
          qtde.focus();
          qtde.select();
          return false;
        }
        return true;
      }

      function habilitaBotaoSalvar(){
        var x=document.form_inclusao;
        if(Trim(x.login.value)=="" || Trim(x.senha.value)=="" || document.getElementById('tabela_aux').rows.length==1){
          x.salvar.disabled=true;
        }
        else{
          x.salvar.disabled=false;
        }
      }

      function desabilitaBotaoSalvar(){
        var x=document.form_inclusao;
        x.salvar.disabled=true;
      }

      function Trim(str){
        return str.replace(/^\s+|\s+$/g,"");
      }

      function salvarMovimento(){
        var x=document.form_inclusao;
        if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
          verificaLoginSenhaResponsavelDispensacao();
        }
        else{
          salvarDados();
        }
      }

      function verificaLoginSenhaResponsavelDispensacao(){
        var x=document.form_inclusao;
        var url = "../../xml_dispensacao/verificar_login_senha_responsavel_dispensacao.php?login="+x.login.value+"&senha="+x.senha.value;
        requisicaoHTTP("GET", url, true, '');
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
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_inclusao" action="./remanejamento_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nº da Solicitação
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" size="30" style="width: 200px" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitante
                      </td>
                      <?php
                        $sql="select id_unidade, nome from unidade where id_unidade='$_SESSION[id_unidade_sistema]'";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Unidade Solicitante", $db, "");
                        if(mysqli_num_rows($res)>0){
                          $unidade_info=mysqli_fetch_object($res);
                        }
                      ?>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="unidade_solicitante" size="30" disabled style="width: 200px" value="<?php echo $unidade_info->nome;?>">
                      </td>
                    </tr>
                    <tr>
                      <?php
                        $sql="select id_unidade, nome from unidade where status_2='A' and id_unidade!='$_SESSION[id_unidade_sistema]' order by nome";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Unidade Solicitada", $db, "");
                      ?>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitada
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="unidade_solicitada" size="1" style="width: 200px">
                          <option value="0"> Selecione uma Unidade </option>
                          <?php
                            while($unidade_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $unidade_info->id_unidade;?>"> <?php echo $unidade_info->nome;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr class="titulo_tabela">
                      <td colspan="4" align="center"> Materiais </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%">
                        <input type="text" name="descricao" id="descricao" size="30" style="width: 450px" onchange="verificarMedicamento();">
                        <div id="acDiv"></div>
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <a href="JavaScript:popup_medicamento();"><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Pesquisar"></a>
                      </td>
                    </tr>
                    <input type="hidden" name="codigo" id="codigo" size="30" style="width: 200px">
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="quantidade" id="quantidade" maxlength="12" style="width: 200px" onKeyPress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" name="adicionar" style="font-size: 12px;" value="Adicionar >>" onclick="inserirLinha();">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tabela" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="10%" align="center"> Código </td>
                            <td width="70%" align="center"> Material </td>
                            <td width="10%" align="center"> Quantidade </td>
                            <td width="5%" align="center"></td>
                            <td width="5%" align="center"></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <div style="display:none">
                          <table id="tabela_aux" cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr class="coluna_tabela">
                              <td width='34%' align='center'> ID Material </td>
                              <td width='33%' align='center'> Quantidade </td>
                              <td width='33%' align='center'> Linha </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="2">
                        <?php
                          if($mostrar_responsavel_dispensacao!="S"){
                            $mostrar_login_senha="none";
                          }
                          else{
                            $mostrar_login_senha="''";
                          }
                        ?>
                        <div id="mostrar_responsavel_dispensacao" style="display:<?php echo $mostrar_login_senha;?>">
                          <table>
                            <tr>
                              <td class="descricao_campo_tabela" width="30%">
                                Realizado por:
                              </td>
                              <td class="descricao_campo_tabela" width="10%">
                                Login:
                              </td>
                              <td>
                                <input type="text" name="login" onblur="habilitaBotaoSalvar();" onfocus="desabilitaBotaoSalvar();">
                                <input type="hidden" name="id_login" value="">
                              </td>
                              <td class="descricao_campo_tabela" width="10%">
                                Senha:
                              </td>
                              <td>
                                <input type="password" name="senha" onblur="habilitaBotaoSalvar(); document.form_inclusao.salvar.focus();" onfocus="desabilitaBotaoSalvar();">
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                      <td valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inicial.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>'">
                        <input type="button" name="salvar" id="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarMovimento();" disabled>
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
                    <input type="hidden" name="lista_materiais" id="lista_materiais">
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
      var AC = new dmsAutoComplete('descricao','acDiv', "", "", "remanejamento");

      AC.ajaxTarget = '../../xml/mestoqueMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
      AC.chooseFunc = function(id,label){
        document.form_inclusao.codigo.value = id;
      }

      document.form_inclusao.unidade_solicitada.focus();
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
