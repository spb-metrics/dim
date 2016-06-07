<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// +-----------------------------------------------------------------------------------------------+
// | Arquivo: login.php                                                                            |
// +-----------------------------------------------------------------------------------------------+
// | Data: 16/06/2008 | Codificadora: Luciana Guskuma                                              |
// | Descrição: Inclusão do Código PHP, Redimensionamento da tela, Busca de sistema no banco de    |
// | dados                                                                                         |
// +-----------------------------------------------------------------------------------------------+
// | Data: 11/06/2008 | Codificador: Guilherme Bernhardt                                           |
// | Descrição: Inclusão do Código PHP, Redimensionamento da tela, Busca de sistema no banco de    |
// | dados                                                                                         |
// +-----------------------------------------------------------------------------------------------+
// | Data: 05/06/2008 | Codificador: André Castro                                                  |
// | Descrição: Esboço Inicial, Disposição do Lay-Out de acordo com o padrão definido              |
// +-----------------------------------------------------------------------------------------------+
// | Aplicação:                                                                                    |
// | Tela de Login para acesso ao Sistema                                                          |
// +-----------------------------------------------------------------------------------------------+

if(file_exists("./config/config.inc.php")){
    require "./config/config.inc.php";
?>

<html>
<head>
<title>DIM - Dispensação Individualizada de Medicamentos</title>

  <script language="JavaScript" type="text/javascript" src="./scripts/indexUnidade.js"></script>
  <script language="JavaScript" type="text/javascript" src="./scripts/pacienteCartao.js"></script>
  <script>
    function carregarUnidade(){
      carregarUnidades("opcao_unidade", "unidade", "./xml/indexUnidade.php");
    }

    function autenticarUnidade(){
      var x=document.form_login;
      var unidade=x.unidade.value;
      var url = "./xml/autenticarUnidade.php?unidade=" + unidade;
      requisicaoHTTP("GET", url, true);
    }

    function trataDados(){
      var x=document.form_login;
      var info = ajax.responseText;  // obtém a resposta como string
      if(info=="login"){
        window.alert("Login não confere!");
        x.login.focus();
        x.login.select();
      }
      if(info=="senha"){
        window.alert("Senha não confere!");
        x.senha.focus();
        x.senha.select();
      }
      if(info=="inativo"){
        window.alert("Usuário inativo!");
        x.login.focus();
        x.login.select();
      }
      if(info=="perfil"){
        window.alert("Usuário sem perfil!");
        x.login.focus();
        x.login.select();
      }
      if(info=="start" || info.substr(0, 5)=="start"){
        x.action="./start.php";
        x.submit();
      }
      if(info=="unidade"){
        x.login.disabled="true";
        x.senha.disabled="true";
        x.flg_unidade.value="t";
        carregarUnidade();
        document.getElementById("tela_descricao").style.display="";
        document.getElementById("tela_unidade").style.display="";
      }
    }

    function verificarAutenticacao(){
      var x=document.form_login;
      var login=x.login.value;
      var senha=x.senha.value;
      var url = "./xml/indexAutenticacao.php?login=" + login + "&senha=" + senha;
      requisicaoHTTP("GET", url, true);
    }

    function validarCampos(){
      var x=document.form_login;
      if(x.flg_unidade.value=="f"){
        if(x.login.value==""){
          window.alert('Preencha corretamente o campo Login!');
          x.login.focus();
          return false;
        }
        if(x.senha.value==""){
          window.alert('Preencha corretamente o campo Senha!');
          x.senha.focus();
          return false;
        }
      }
      else{
        if (document.getElementById("tela_unidade").style.display =="none")
        {
            document.getElementById("tela_unidade").style.display = "inline";
            document.getElementById("tela_descricao").style.display = "inline";
        }
        else
        {
            if(x.unidade.value==""){
              window.alert("Favor Selecionar Unidade!");
              x.unidade.focus();
              return false;
            }
        }
      }
      return true;
    }

    function btAcessar(){
      var x=document.form_login;
      if(validarCampos()){
        if(x.flg_unidade.value=="f"){
          verificarAutenticacao();
        }
        else{
          autenticarUnidade();
        }
      }
    }

    function btCancelar(){
      var x=document.form_login;
      x.login.value="";
      x.senha.value="";
      x.unidade.selectedIndex = 0;
      if(document.getElementById("tela_unidade").style.display == '')
      {
         document.getElementById("tela_unidade").style.display = 'none';
         document.getElementById("tela_descricao").style.display = 'none';
         x.login.disabled = false;
         x.senha.disabled = false;
         x.login.focus();
      }
    }

</script>

<?
$res = $_GET["res"];
 $sql = "select caminho_imagem_esquerda, caminho_imagem_direita from parametro";
 $result=mysqli_query($db, $sql);
 if(mysqli_num_rows($result)>0)
   $imagens=mysqli_fetch_object($result);
           
////// Define um fator de conversão para dependendo da resolução. A Resolução padrão adotada é 1024x768
$fator = $res / 1024;
unset($tam_img);
if ($res <= 800)
	$tam_img = "_800";
elseif ($res == 1024)
	$tam_img = "_1024";

$h_ln1 = floor(90*$fator);
$h_ln2 = floor(2*$fator);
$h_ln3 = floor(12*$fator);
$h_ln4 = floor(256*$fator);
$h_ln5 = floor(42*$fator);
$h_ln6 = floor(5*$fator);
$ima_texto = floor(317*$fator);
$ima_logo = floor(183*$fator);
$logo_prefeitura = floor(429*$fator);
$fonte1 = floor(24*$fator);
?>

<!-- Definição de Estilos -->
<style type='text/css'>



body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #41191A;
	font-size: 8px;
	color:white;
}

.style1 {
	font-family: Tahoma;
	color: #FFFFFF;
	font-size: <?=$fonte1?>px;
}
.style3 {
	font-family: Tahoma;
	color: #FFFFFF;
	font-size: 12px;
	font-weight: bold;
}
.inputs {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:11px;
font-weight:bold;
color:#FFFFFF;
border-style:none;
background-color:#7B6760;
}
a:link {
	color: #FFFFFF;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FFFFFF;
}
a:hover {
	text-decoration: none;
	color: #DFD4A7;
}
a:active {
	text-decoration: none;
}

.box
{
	background-color: #7b6760;
	font-size:13px;
	color:white;
	font-weight:bold;
	border:0px;
}
</style>

</head>

<body>

<form method='post' name='form_login'>
<table width='100%' cellspacing='0' cellpadding='0' border='0'>
<!-- Logo IMA-->
<tr height='<?=$h_ln1?>'>
	<td width='20'>&nbsp;</td>
	<td><img src='./imagens/ima1.jpg' width='<?=$ima_texto?>'/></td>
	<td align='right'><img src='./imagens/ima_topo.jpg' width='<?=$ima_logo?>' /></td>
	<td width='20' align='right'>&nbsp;</td>
</tr>
<!-- Linha em branco -->
<tr height='<?=$h_ln2?>'>
	<td colspan='4' bgcolor='#FFFFFF'></td>
</tr>
<!-- Espaçamento -->
<tr height='<?=$h_ln3?>'>
	<td colspan='4'></td>
</tr>
<!-- Logo PMC - Imagem Ilustrativa -->
<tr>
	<td colspan='4'>
	<table width='100%' height='<?=$h_ln4?>' border='0' cellpadding='0' cellspacing='0'>
	<tr>

        <td width='50%' align='center' bgcolor='#FFFFFF'><img src='.<?echo $imagens->caminho_imagem_esquerda;?>' width='<?=$logo_prefeitura?>'></td>
		<td width='50%' align='center' bgcolor='#FFFFFF'><img src='.<?echo $imagens->caminho_imagem_direita;?>' width='<?=$logo_prefeitura?>'>&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
    <td class='style1' height='<?=$h_ln5?>' colspan='4' align='center' valign='middle'>
    Dispensação Individualizada de Medicamentos
	</td>
</tr>
<!-- Linha em Branco -->
<tr height='<?=$h_ln6?>' bgcolor='#FFFFFF'>
	<td colspan='4'></td>
</tr>
<!-- Espaçamento -->
<tr height='<?=$h_ln6*2?>'>
	<td colspan='4'></td>
</tr>
<!-- Sistema -->
<tr>
	<td colspan='4' align='center' valign='top'>
      <table width='350' cellspacing='0' cellpadding='4' border='0'>
          <TR class="style3">
             <TD align="right">
              Login  &nbsp;&nbsp;
             </TD>
             <TD align="left" >
                <input class="inputs" type="text" name="login" id="login" value='<?=$login?>' size="31"  maxlength="20">
             </TD>
          </TR>
          <TR class="style3">
            <TD align="right">
              Senha &nbsp;&nbsp;
            </TD>
            <TD align="left" >
                <input class="inputs" type="password" name="senha" id="senha" value='<?=$senha?>' size="31" maxlength="20">
            </TD>
          </TR>
          <tr class="style3">
            <td align="right">
              <div id="tela_descricao" style="display:none">
                Unidade
              </div>
            </td>
            <td align="left">
              <div id="tela_unidade" style="display:none">
                <select class="inputs" name="unidade" id="unidade" size="1">
                  <option id="opcao_unidade" value=""></option>
                </select>
              </div>
            </td>
            <tr>
              <td colspan='2'>
        		<table width='100%' cellpadding='0' cellspacing='0' class='style3' border='0'>
        		<tr>
        			<td align='left'><a href='#' onclick='javascript:btAcessar();'>&lt;&lt; acessar </a></td>
                    <td align='center'><a href='javascript:btCancelar();'> cancelar &gt;&gt;</a></td>

        			<input type="hidden" name="flg_unidade" id="flg_unidade" value="f">
        		</tr>
        		</table>
              </td>
	               </tr>
      </table>

	</td>
</tr>
</table>
<?php if ($aviso)
{
?>
	<table width='100%' cellpadding='0' cellspacing='0' border='0'>
	<tr class='fundo'>
		<td class='style3' align='center' style='color:#f0231e'><?=$aviso?></td>
	</tr>
	</table>
<?php
 }
}
?>
<input type='hidden' name='pula_conect' value='1'>
<input type='hidden' name='res' value='<?=$res?>'>
<input type='hidden' name='xLogin'>
<input type='hidden' name='xSenha'>
<input type='hidden' name='atualizou' value='1'>
</form>
</body>
<script language="JavaScript" type="text/JavaScript">
    <!--
    //////////////////////////
    //DEFININDO FOCO INICIAL//
    //////////////////////////
    var x=document.form_login;
    x.login.focus();
    //-->
    </script>
</html>
