<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  session_regenerate_id();
  $session_id=session_id();
  $chave_unica=date("Y-m-d H:i:s") . $_SESSION[id_unidade_sistema] . $session_id;

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: entrada_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de entrada manual
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

    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require "../../verifica_acesso.php";

    if($_GET[aplicacao] <> ''){
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
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
		  var _R=window.showModalDialog("../mestoque/pesquisa_material.php?id_operacao=entrada", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogLeft=290px;dialogTop=250px;scroll=yes;status=no;");
		  if("undefined"!=typeof(_R)){
			SetNameMedicamento(_R.strArgs);
  		  }
	    }
	    //NS
	    else{
		  var left=(screen.width-width)/2;
		  var top=(screen.height-height)/2;
 		  var winHandle=window.open("../mestoque/pesquisa_material.php?id_operacao=entrada", ID, "modal=1,dialog=1,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		  winHandle.focus();
	    }
      }

      function SetNameMedicamento(argumentos){
        var valores=argumentos.split('|');

        var x=document.form_inclusao;
        if(valores[0]=="entrada"){
          x.codigo.value=valores[1];
          x.descricao.value=valores[2];
        }
      }

      function inserirDados(){
        var y=document.form_inclusao;
        var itens=y.lista_materiais.value;
        var numero=y.numero.value;
        var chave=y.chave.value;
        var id_login=y.id_login.value;
        var url = "../../xml/entradaInsercao.php?itens=" + itens + "&numero=" + numero + "&chave=" + chave + "&id_login=" + id_login;
        var palavra=/<?php echo SIMBOLO; ?>/gi;
        url=url.replace(palavra, "CERQUILHA");
        requisicaoHTTP("GET", url, true);
      }

      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string

        if(info.substr(0, 8)=="validade"){
          if(info.substr(9, 2)=="NO"){
             var validade=info.substr(11, 10);
             valid=validade.substr(8, 2) + "-" + validade.substr(5, 2) + "-" + validade.substr(0, 4);
             window.alert ("Esse lote já está cadastrado com à validade: "+valid+ " !!!");
             x.validade.focus();
             x.validade.select();
           }
           else if(info.substr(9, 2)=="OK"){
                  inserirLinha();
           }
        }
        else{
            x.codigo.value=info;
            var numero=x.numero.value;
            var valor=info.split("|");
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
            if(valor[0]=="estoque"){
              if(valor[1]=="NO"){
                window.alert(valor[2]);
                x.salvar.disabled="";
              }
              else{
                var resposta=window.confirm("Operação efetuada com sucesso! Deseja imprimir?");
                if(resposta){
                  var link="<?php echo URL;?>/modulos/impressao/impressao_entrada.php?chave=" + valor[2] + "&numero=" + numero + "&data=" + valor[3];
                  window.open(link);
                }
                window.location="<?php echo URL;?>/modulos/entrada/entrada_inclusao.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>";
              }
            }
            if(info==""){
              window.alert("Material não encontrado!");
              x.descricao.focus();
              x.descricao.select();
              }
         }
      }
      
      function verificarMedicamento(){
        var y=document.form_inclusao;
        var descricao=y.descricao.value;

        //descricao = encodeURIComponent(descricao);
        var url = "../../xml/mestoqueVerificarMedicamento.php?descricao=" + descricao + "&aplicacao=entrada";
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
            if(itens_aux.rows[i].cells[0].innerHTML==x.codigo.value &&
               itens_aux.rows[i].cells[1].innerHTML==x.fabricante.options[x.fabricante.selectedIndex].value &&
               itens_aux.rows[i].cells[2].innerHTML==x.lote.value){
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

            //material
            var cel0=tab.insertCell(0);
            cel0.align="left";
            cel0.innerHTML=x.descricao.value;

            //fabricante
            var cel1=tab.insertCell(1);
            cel1.align="left";
            var simbolo=/&/gi;
            var palavra=x.fabricante.options[x.fabricante.selectedIndex].text;
            palavra=palavra.replace(simbolo, "&amp;");
            cel1.innerHTML=palavra;

            //lote
            var cel2=tab.insertCell(2);
            cel2.align="left";
            cel2.innerHTML=x.lote.value;

            //validade
            var cel3=tab.insertCell(3);
            cel3.align="center";
            cel3.innerHTML=x.validade.value;

            //quantidade
            var cel4=tab.insertCell(4);
            cel4.align="right";
            cel4.innerHTML=parseInt(x.quantidade.value, 10);

            var cel5=tab.insertCell(5);
            cel5.align="center";
            var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
            var urlAlterar="javascript:alterarLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "')";
            cel5.innerHTML=linkAlterar.link(urlAlterar);

            var cel6=tab.insertCell(6);
            cel6.align="center";
            var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
            var urlRemover="javascript:removerLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "')";
            cel6.innerHTML=linkRemover.link(urlRemover);

            //tabela auxiliar
            var tab_aux=itens_aux.insertRow(total_linhas_aux);
            tab_aux.id="linha_aux" + cont;
            tab_aux.className="campo_tabela";

            //id material
            var cel0=tab_aux.insertCell(0);
            cel0.align="left";
            cel0.innerHTML=x.codigo.value;

            //id fabricante
            var cel1=tab_aux.insertCell(1);
            cel1.align="left";
            cel1.innerHTML=x.fabricante.options[x.fabricante.selectedIndex].value;

            //lote
            var cel2=tab_aux.insertCell(2);
            cel2.align="left";
            cel2.innerHTML=x.lote.value;

            //validade
            var cel3=tab_aux.insertCell(3);
            cel3.align="center";
            var str=x.validade.value.substr(6, 4) + "-" + x.validade.value.substr(3, 2) + "-" + x.validade.value.substr(0, 2);
            cel3.innerHTML=str;

            //quantidade
            var cel4=tab_aux.insertCell(4);
            cel4.align="right";
            cel4.innerHTML=parseInt(x.quantidade.value, 10);

            //indice fabricante
            var cel5=tab_aux.insertCell(5);
            cel5.align="right";
            cel5.innerHTML=x.fabricante.selectedIndex;

            var cel6=tab_aux.insertCell(6);
            cel6.align="right";
            cel6.innerHTML=cont;

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
        var x=document.form_inclusao;
        x.descricao.value="";
        x.codigo.value="";
        x.fabricante.options[0].selected=true;
        x.lote.value="";
        x.validade.value="";
        x.quantidade.value="";
      }

      function removerLinha(lnh, lnh_aux, pos){
        var tab=document.getElementById("tabela");
        tab.deleteRow(document.getElementById(lnh).rowIndex);
        var tab_aux=document.getElementById("tabela_aux")
        tab_aux.deleteRow(document.getElementById(lnh_aux).rowIndex);

        var total_linhas=tab.rows.length;
        for(var i=pos; i<total_linhas; i++){
          var j=tab_aux.rows[i].cells[6].innerHTML;
          tab.rows[i].deleteCell(5);
          var cel5=tab.rows[i].insertCell(5);
          cel5.align="center";
          var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
          var urlAlterar="javascript:alterarLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "')";
          cel5.innerHTML=linkAlterar.link(urlAlterar);

          tab.rows[i].deleteCell(6);
          var cel6=tab.rows[i].insertCell(6);
          cel6.align="center";
          var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
          var urlRemover="javascript:removerLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "')";
          cel6.innerHTML=linkRemover.link(urlRemover);
        }
        if(total_linhas<=1){
          document.getElementById("salvar").disabled="true";
        }
        document.form_inclusao.descricao.focus();
      }
      
      function alterarLinha(lnh, lnh_aux, pos){
        var itens=document.getElementById("tabela");
        var itens_aux=document.getElementById("tabela_aux");
        var x=document.form_inclusao;
        
        x.descricao.value=itens.rows[pos].cells[0].innerHTML;
        x.codigo.value=itens_aux.rows[pos].cells[0].innerHTML;
        x.fabricante.options[itens_aux.rows[pos].cells[5].innerHTML].selected=true;
        x.lote.value=itens.rows[pos].cells[2].innerHTML;
        x.validade.value=itens.rows[pos].cells[3].innerHTML;
        x.quantidade.value=itens.rows[pos].cells[4].innerHTML;
        removerLinha(lnh, lnh_aux, pos);
      }
      
      function salvarDados(){
        var x=document.form_inclusao;
        x.salvar.disabled="true";
        var itens=document.getElementById("tabela_aux");
        var total_linhas=itens.rows.length;
        var lista=new Array(total_linhas);
        for(var i=1; i<lista.length; i++){
          lista[i]=new Array(5);
        }
        var info="";
        for(var i=1; i<lista.length; i++){
          for(var j=0; j<lista[i].length; j++){
            info=info + itens.rows[i].cells[j].innerHTML + ",";
          }
          info=info + "|";
        }

        document.getElementById("lista_materiais").value=info;
        inserirDados();
      }

      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      
      //verifica se não existe lote cadastrado - pois a validade deve ser a mesma
      function verificaValidade(){
        var x=document.form_inclusao;
        var material=x.codigo.value;
        var fabr=x.fabricante.value;
        var lot=x.lote.value;
        var valid=x.validade.value.substr(6, 4) + "-" + x.validade.value.substr(3, 2) + "-" + x.validade.value.substr(0, 2);
        var und=<?php echo $_SESSION[id_unidade_sistema];?>;
        var url = "../../xml/verificarValidade.php?mat="+material+"&fabr="+fabr+"&lot="+lot+"&valid="+valid+"&und="+und;
        var palavra=/<?php echo SIMBOLO;?>/gi;
        url=url.replace(palavra, "CERQUILHA");
        requisicaoHTTP("GET", url, true);
      }
      
      function validarCampos(){
        var x=document.form_inclusao;
        var doc=x.numero;
        var descr=x.descricao;
        var mat=x.codigo;
        var fabr=x.fabricante;
        var lot=x.lote;
        var valid=x.validade;
        var qtde=x.quantidade;

        if(doc.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          doc.focus();
          doc.select();
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
        if(fabr.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          fabr.focus();
          return false;
        }
        if(lot.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          lot.focus();
          lot.select();
          return false;
        }
        if(valid.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          valid.focus();
          valid.select();
          return false;
        }
        if(qtde.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          qtde.focus();
          qtde.select();
          return false;
        }
        if(parseInt(qtde.value, 10)==0){
          window.alert("Quantidade Igual a Zero!");
          qtde.focus();
          qtde.select();
          return false;
        }
        if(!validarData(x.validade)){
          alert ("A data fornecida foi preenchida incorretamente.");
          x.validade.focus();
          x.validade.select();
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
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%'height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_inclusao" action="./entrada_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> Entrada </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Num documento
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" maxlength="15" style="width: 200px" onKeyPress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                      </td>
                    </tr>
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela"colspan="3" valign="middle" width="100%">
                        <input type="text" name="descricao" id="descricao" size="30" style="width: 560px" onchange="verificarMedicamento();">
                        <div id="acDiv"></div>
                        <a href="JavaScript:popup_medicamento();"><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Pesquisar"></a>
                      </td>
                    </tr>
                    <input type="hidden" name="codigo" id="codigo" size="30" style="width: 200px">
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Fabricante
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="fabricante" id="fabricante" size="1" style="width: 200px">
                          <option value="0"> Selecione um Fabricante </option>
                          <?php
                            $sql="select id_fabricante, descricao from fabricante where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Fabricante", $db, "");
                            while($fabricante_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $fabricante_info->id_fabricante;?>"> <?php echo $fabricante_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Lote
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="lote" maxlength="30" style="width: 200px" onKeyPress="return validarLote(event);">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Validade
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="validade" size="8"  maxlength="10" onKeyPress="return mascara_data(event,this);">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="quantidade" maxlength="12" style="width: 200px" onKeyPress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                      <?php
                        if($inclusao_perfil!=""){
                      ?>
                        <input type="button" name="adicionar" style="font-size: 12px;" value="Adicionar >>" onclick="verificaValidade();">
                      <?php
                        }
                        else{
                      ?>
                        <input type="button" name="adicionar" style="font-size: 12px;" value="Adicionar >>" onclick="verificaValidade();" disabled>
                      <?php
                        }
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tabela" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width='41%' align='center'> Material </td>
                            <td width='13%' align='center'> Fabricante </td>
                            <td width='13%' align='center'> Lote </td>
                            <td width='10%' align='center'> Validade </td>
                            <td width='13%' align='center'> Quantidade </td>
                            <td width='5%' align='center'></td>
                            <td width='5%' align='center'></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <div style="display:none">
                          <table id="tabela_aux" cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr class="coluna_tabela">
                              <td width='41%' align='center'> ID Material </td>
                              <td width='13%' align='center'> ID Fabricante </td>
                              <td width='13%' align='center'> Lote </td>
                              <td width='10%' align='center'> Validade </td>
                              <td width='13%' align='center'> Quantidade </td>
                              <td width='5%' align='center'> Index Fabricante </td>
                              <td width='5%' align='center'> Linha </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="3">
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
                    <input type="hidden" name="chave" value="<?php echo $chave_unica;?>">
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
      var AC = new dmsAutoComplete('descricao','acDiv', "", "", "entrada");

      AC.ajaxTarget = '../../xml/mestoqueMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
      AC.chooseFunc = function(id,label){
        document.form_inclusao.codigo.value = id;
      }

      document.form_inclusao.numero.focus();
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
