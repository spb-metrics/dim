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
  //  Arquivo..: restoque_inclusao.php
  //  Bancos...: dbmdim
  //  Data.....: 27/11/2006 / 16/08/2010
  //  Analista.: Fabio Hitoshi Ide / Denise Ike
  //  Função...: Tela de reversao de movimento / Alterado para funcionar em FF
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
    require DIR."/header.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    if($_GET[aplicacao]<>''){
      $_SESSION[cod_aplicacao]=$_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

    require "../../verifica_acesso.php";

?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/restoqueDocumento.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript">
      <!--
      function checkAll(campo, formul){
        var tabela = document.getElementById('tabela_aux');
        if(campo.checked==true){
          for (var i = 0; i < tabela.rows.length; i++){
            var x=document.getElementById(i);
            if(x){
            
              x.checked = true;
            }
          }
        }
        else{
          for (var i = 0; i < tabela.rows.length; i++){
            var x=document.getElementById(i);
            if(x){
              x.checked = false;
            }
          }
        }
      }

      function obterItens(){
        var tabela = document.getElementById('tabela_aux');

        var formul=document.form_inclusao;
        var itens="";
        for(var i=0; i<tabela.rows.length; i++){
          var x=document.getElementById(i);
          if(x){
            if(x.checked==true){
              itens+=x.value + "@";
            }
          }
        }
        itens=itens.substr(0, itens.length-1);

        formul.lista_itens.value=itens;
      }
      
      function verificarEstoque(){
        obterItens();
        var x=document.form_inclusao;
        var numero=x.numero.value;
        var itens=x.lista_itens.value;
        var chave=x.chave.value;
        var id_login=x.id_login.value;
        var url = "../../xml/restoqueEstoque.php?numero=" + numero + "&itens=" + itens + "&chave=" + chave + "&id_login=" + id_login;
        var palavra=/#/gi;
        url=url.replace(palavra, "CERQUILHA");
        requisicaoHTTP("GET", url, true);
      }

      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string
        info_res=info.substr(0, 3);
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
	    if(info_res=="NRE"){
          var url="../../xml/restoqueDocumento.php?numero=" + x.numero.value + "&unidade=<?php echo $_SESSION[id_unidade_sistema];?>";
          requestInfo(url, "tabela", "", "Não foi encontrado documento para o numero informado!", "numero" , "salvar", "<?php echo $mostrar_responsavel_dispensacao;?>");
        }
        if(info_res=="REV"){
          var msg="Número do documento já foi revertido!\n";
          window.alert(msg);
          x.numero.focus();
          x.numero.select();
          x.salvar.disabled="true";
          document.getElementById("tabela").style.display="none";
        }
        var valor=info.split("|");
        if(valor[0]=="SAV"){
          if(valor[1]=="NO"){
            window.alert(valor[2]);
            x.salvar.disabled="";
          }
          else{
            var resposta=window.confirm("Operação efetuada com sucesso! Deseja imprimir?");
            if(resposta){
              var link="<?php echo URL;?>/modulos/impressao/impressao_restoque.php?chave=" + valor[2];
              window.open(link);
            }
            window.location="<?php echo URL;?>/modulos/restoque/restoque_inclusao.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>";
          }
        }
        if(info_res!="SAV" && info_res!="NRE" && info_res!="REV"){
          var msg="Materiais com saldo insuficientes no estoque\n";
          msg+="Material - Lote - Fabricante\n" + info;
          window.alert(msg);
          x.salvar.disabled="";
        }
      }

      function verificarDocumento(){
        var x=document.form_inclusao;
        var numero=x.numero.value;
        var url = "../../xml/restoqueDocumento.php?numero=" + numero + "&unidade=";
        requisicaoHTTP("GET", url, true);
      }

      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var doc=x.numero;
        if(doc.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          doc.focus();
          doc.select();
          return false;
        }
        return true;
      }
      
      function obterDados(){
        if(validarCampos()==true){
          verificarDocumento();
        }
      }
      
      function salvarDados(){
        var x=document.form_inclusao;
        if(validarCheckbox()==true){
          x.salvar.disabled="true";
          verificarEstoque();
        }
      }
      
      function validarCheckbox(){
        var formul=document.form_inclusao;
        var checado=false;
        for(var i=0; i<formul.elements.length; i++){
          var x=document.getElementById(i);
          if(x){
            if(x.checked==true){
              checado=true;
            }
          }
        }
        if(checado==false){
          window.alert("Selecionar pelo menos um material!");
          return false;
        }
        else{
          return true;
        }
      }
      
      function desabilitarCampos(){
        var x=document.form_inclusao;
        x.todos.checked=false;
        document.getElementById("tabela").style.display="none";
        x.salvar.disabled="true";
      }
      
      function desabilitarTodos(){
        var x=document.form_inclusao;
        if(x.todos.checked==true){
          x.todos.checked=false;
        }
      }

      function habilitaBotaoSalvar(){
        var x=document.form_inclusao;
        if(Trim(x.login.value)=="" || Trim(x.senha.value)=="" || document.getElementById('tabela_aux')==null){
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
                  <form name="form_inclusao" action="./restoque_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr>
                      <td colspan="5">
                      <table border="0" cellpadding="0" cellspacing="1" width="100%">
                        <tr class="titulo_tabela">
                          <td colspan="3" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?> </td>
                        </tr>
                        <tr>
                          <td class="descricao_campo_tabela" valign="middle" width="40%" align="right">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Num documento
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          </td>
                          <?php
                            if($inclusao_perfil!=""){
                          ?>
                            <td class="campo_tabela" valign="middle" width="30%">
                              <input type="text" id="numero" name="numero" maxlength="20" style="width: 200px" onKeyPress="return isNumberKey(event);" onchange="desabilitarCampos();">
                            </td>
                          <?php
                            }
                            else{
                          ?>
                            <td class="campo_tabela" valign="middle" width="30%">
                              <input type="text" name="numero" size="30" style="width: 200px" disabled>
                            </td>
                          <?php
                            }
                          ?>
                          <?php
                            if($inclusao_perfil!=""){
                          ?>
                            <td class="campo_tabela" valign="middle" align="left" width="30%">
                              <input type="button" style="font-size: 12px;" name="cadastrar" value=" OK " onclick="obterDados();">
                            </td>
                          <?php
                            }
                            else{
                          ?>
                            <td class="campo_tabela" valign="middle" align="left" width="30%">
                              <input type="button" style="font-size: 12px;" name="cadastrar" value=" OK " disabled>
                            </td>
                          <?php
                            }
                          ?>
                        </tr>
                      </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5">
                        <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width='10%' align='center'> Código </td>
                            <td width='40%' align='center'> Material </td>
                            <td width='15%' align='center'> Lote </td>
                            <td width='15%' align='center'> Fabricante </td>
                            <td width='15%' align='center'> Quantidade </td>
                            <td width='5%' align='center'> <input type="checkbox" name="todos" onclick="checkAll(this, document.form_inclusao);"> </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5">
                        <div id="tabela"></div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5" height="100%"></td>
                    </tr>
                    <tr>
                      <td colspan="5" width="100%" height="100%"></td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4">
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
                      <td valign="middle" align="right" width="30%">
                        <input type="button" name="salvar" id="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarMovimento();" disabled>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="5" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="chave" value="<?php echo $chave_unica;?>">
                    <input type="hidden" name="lista_itens">
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

    <script language="javascript">
    <!--
      var x=document.form_inclusao;
      x.numero.focus();
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
