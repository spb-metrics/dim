<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();

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

if($_POST[sigla]!=""){
$sql_cadastro = "insert into unidade
                    (unidade_id_unidade,
                     sigla,
                     cnes,
                     nome,
                     flg_nivel_superior,
                     coordenador,
                     rua,
                     numero,
                     complemento,
                     bairro,
                     municipio,
                     uf,
                     cep,
                     telefone,
                     e_mail,
                     status_2,
                     data_incl,
                     usua_incl,
                     cod_estabelecimento,
                     flg_banco,
                     dns_local,
                     usuario_integra_local,
                     senha_integra_local,
                     flg_transf_almo,
                     base_integra_ima
                     )
                     values (
                     '$_POST[nome_und_sup]',
                     '" . strtoupper($_POST[sigla]) . "',
                     '" . $_POST[cnes]."',
                     '" . strtoupper($_POST[nome]) . "',
                     '" .$_POST[flg_nivelsuperior]."',
                     '" . strtoupper($_POST[coordenador]) . "',
                     '" . strtoupper($_POST[rua]) . "',
                     '" . strtoupper($_POST[numero]) . "',
                     '" . strtoupper($_POST[complemento]) . "',
                     '" . strtoupper($_POST[bairro]) . "',
                     '" . strtoupper($_POST[municipio]) . "',
                     '" .$_POST[uf]."',
                     '" . strtoupper($_POST[cep]) . "',
                     '" . strtoupper($_POST[telefone]) . "',
                     '" . strtoupper($_POST[e_mail]) . "',
                     'A',
                     '".date("Y-m-d H:m:s")."',
                     '" .$_SESSION[id_usuario_sistema]."',
                     '" . strtoupper($_POST[cod_estabelecimento])."',
                     '" .$_POST[flg_banco]."',
                     '" .$_POST[dns_local]."',
                     '" .$_POST[usuario_integra_local]."',
                     '" .$_POST[senha_integra_local]."',
                     '" .$_POST[flg_transf_almo]."',
                     '" .$_POST[base_integra_ima]."'
                     )";

//echo $sql_cadastro;
//echo exit;
mysqli_query($db, $sql_cadastro);
erro_sql("Insert Unidade", $db, "");

if(mysqli_errno($db) == "0"){
mysqli_commit($db);
header("Location: ". URL."/modulos/unidade/unidade_inicial.php?i=t");
}
else{
mysqli_rollback($db);
header("Location: ". URL."/modulos/unidade/unidade_inicial.php?i=f");
}
exit();
}

////////////////////////////////////
//BLOCO HTML DE MONTAGEM DA PÁGINA//
////////////////////////////////////
require DIR."/header.php";
require DIR."/buscar_aplic.php";

?>
<script
	language="JavaScript" type="text/javascript"
	src="../../scripts/pacienteCartao.js"></script>
<script
	language="JavaScript" type="text/javascript"
	src="../../scripts/frame.js"></script>
<script
	language="JavaScript" type="text/javascript"
	src="../../scripts/scripts.js"></script>
<script language="JavaScript" type="text/JavaScript">

  <!--
  function trataDados(){
      var auxinfo="";
      var auxok="";
      var x=document.form_inclusao;
      var info1 = ajax.responseText;  // obtém a resposta como string
      var info=info1.substr(0, 3);

      if(info=="NAO"){
        var msg="Unidade já cadastrada!\n";
        window.alert(msg);
        x.sigla.focus();
        x.sigla.select();
      }

      if(info=="SAV"){
         auxinfo='S';
         if(x.checarCodEst.value=='S')
         {
           verificarEstabelecimento();
         }
      }


       var pos = info1.indexOf("|");
       var verifica = info1.substr(0, pos);
       var cnes = info1.substr(pos+1);
       //alert(info);
       if(info=="NOK"){
          alert("Já existe um estabelecimento cadastrado com esse número de CNES e CMES");
          x.cod_estabelecimento.focus();
          x.cod_estabelecimento.select();
       }



    if(x.checarCodEst.value=='S')
    {
        if(info=="OK!") {
           x.submit();
        }
    }
    else if(x.checarCodEst.value=='N')
    {
        if(info=="SAV"){
          x.submit();
        }
    }
  }

  function retirarEspaco(){
    var x=document.form_inclusao;
    var sigla=x.sigla.value;
    while(sigla.match("  ")){
      sigla=sigla.replace("  ", " ");
    }
    if(sigla.charAt(0)==" "){
      sigla=sigla.substr(1, sigla.length);
    }
    if(sigla.charAt(sigla.length-1)==" "){
      sigla=sigla.substr(0, sigla.length-1);
    }
    x.sigla.value=sigla;
  }

  function verificarSigla(){
    retirarEspaco();
    var x=document.form_inclusao;
    var sigla=x.sigla.value;
    var url = "../../xml/unidadeSigla.php?sigla=" + sigla;
    requisicaoHTTP("GET", url, true);
  }

  function verificarEstabelecimento(){
    var x=document.form_inclusao;
    if(x.checarCodEst.value=='S')
      {
         retirarEspaco();
         var cod_estabelecimento=x.cod_estabelecimento.value;
         var cnes = x.cnes.value;
         var url = "../../xml/unidadeEstabelecimento.php?cod_estabelecimento=" + cod_estabelecimento+"&cnes="+cnes+"&operacao=I";
         requisicaoHTTP("GET", url, true);
      }
  }
  
  
  function mudou_nivel(){
    var x=document.form_inclusao;
    x.nome_und_sup[0].selected=true;
  }

  function salvarDados(){
    var x=document.form_inclusao;

    if(x.checarCodEst.value=='S'){
      if(validarCampos()==true){
        if(validarCampos_codEstab()==true){
           verificarSigla();
//           verificarEstabelecimento();
        }
      }
    }
    else
    {
      if(validarCampos()==true){
       verificarSigla();
      }
    }
    
  }

  function validarCampos(){
    var x=document.form_inclusao;
    if(x.sigla.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.sigla.focus();
      return false;
    }
    if(x.nome.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.nome.focus();
      return false;
    }
    if(x.flg_nivelsuperior[1].checked){
      if(x.nome_und_sup.value==""){
        window.alert("É necessário informar uma Unidade Superior!");
        x.nome_und_sup.focus();
        return false;
      }
    }
    return true;
  }
  
  function validarCampos_codEstab(){
    var x=document.form_inclusao;
    if(x.cnes.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.cnes.focus();
      return false;
    }
    if(x.cod_estabelecimento.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.cod_estabelecimento.focus();
      return false;
    }
   return true;
  }
  //-->
  </script>

<table width="100%" class="caminho_tela" border="1" cellpadding="0"
	cellspacing="0">
	<tr>
		<td><?php echo $caminho; ?></td>
	</tr>
</table>

<table width="100%" height="95%" border="1" cellpadding="0"
	cellspacing="0">
	<tr height="5%">
		<td>
		<table border="0" width="117%" class="titulo_tabela">
			<tr>
				<td align="center" heigth="21"><? echo $nome_aplicacao; ?>: Incluir</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align="center" valign="top">
		<table width="100%" border="0" cellpadding="0" cellspacing="1">
			<form name="form_inclusao" action="./unidade_cadastro.php"
				method="POST" enctype="application/x-www-form-urlencoded">
			<tr>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat.gif";?>">Sigla</td>
				<td align="left" colspan="6" width="75%" "  class="campo_tabela"><input
					type="text" name="sigla" id="sigla" size="30" maxlength="10"></td>
			</tr>

			<tr>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat.gif";?>">Unidade</td>
				<td align="left" colspan="6" width="75%" class="campo_tabela"><input
					type="text" name="nome" id="nome" size="102" maxlength="40"></td>
			</tr>

			<tr>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat.gif";?>">Nível Superior</td>
				<td align="left" width="25%" class="campo_tabela"><input
					type="radio" name="flg_nivelsuperior" value="1"
					onclick="mudou_nivel()">Sim &nbsp&nbsp&nbsp <input type="radio"
					name="flg_nivelsuperior" value="0" checked onclick="mudou_nivel()">Não
				</td>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Unidade Superior </td>
				<td align="left" width="25%" class="campo_tabela"> <select
					name="nome_und_sup" style="width: 205px;">
					<option value=""></option>
					<?php
					$sql="select id_unidade, nome
                           from unidade
                           where flg_nivel_superior = '1' and status_2 = 'A' order by nome";
					$nivel = mysqli_query($db, $sql);
					erro_sql("Select Unidade Superior", $db, "");
					while($lista_nivel=mysqli_fetch_object($nivel)){
					?>
					<option value="<?php echo $lista_nivel->id_unidade; ?>"><?php echo $lista_nivel->nome; ?></option>
					<?php
					}
					?>
				</select></td>
			</tr>

			<tr>
			<?php
			$sql="select mostrar_cod_estab, nome_cod_estab from parametro";
			$param = mysqli_query($db, $sql);
			erro_sql("Tabela Parametro", $db, "");
			if($tb_parametro = mysqli_fetch_object($param)){
			$tb_parametro->mostrar_cod_estab;
			$tb_parametro->nome_cod_estab;
			if (strtoupper($tb_parametro->mostrar_cod_estab)=='S')
			{  ?>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat.gif";?>">Cnes</td>
				<td align="left" width="25%" class="campo_tabela"><input type="text"
					name="cnes" id="cnes" size="30" maxlength="10"
					onKeyPress="return isNumberKey(event);"></td>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat.gif";?>"><? echo $tb_parametro->nome_cod_estab;?>
				</td>
				<td align="left" width="25%" class="campo_tabela"><input type="text"
					name="cod_estabelecimento" id="cod_estabelecimento" size="30"
					maxlength="10" onKeyPress="return isNumberKey(event);"> <input
					type="hidden" name="checarCodEst" id="checarCodEst" value="S"></td>

					<?
			}
			else
			{?>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Cnes</td>
				<td align="left" width="25%" class="campo_tabela"><input type="text"
					name="cnes" id="cnes" size="30" maxlength="10"
					onKeyPress="return isNumberKey(event);"></td>
				<td align="left" width="25%" class="campo_tabela" colspan="2"><input
					type="hidden" name="checarCodEst" id="checarCodEst" value="N"></td>
					<?
			}
			}
			?>
			</tr>

			<tr>
				<td align="left" width="25%" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Coordenador</td>
				<td align="left" colspan="6" width="75%" class="campo_tabela"><input
					type="text" name="coordenador" id="coordenador" size="102"
					maxlength="100"></td>
			</tr>

			<tr>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Logradouro</td>
				<td align="left" colspan="6" class="campo_tabela"><input type="text"
					name="rua" id="rua" size="102" maxlength="40"></td>
			</tr>

			<tr>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Número</td>
				<td align="left" class="campo_tabela"><input type="text"
					name="numero" id="numero" size="10" maxlength="6"></td>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Complemento</td>
				<td align="left" class="campo_tabela"><input type="text"
					name="complemento" id="complemento" size="30" maxlength="20"></td>
			</tr>

			<tr>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Bairro</td>
				<td align="left" class="campo_tabela" colspan="3"><input type="text"
					name="bairro" id="bairro" size="102" maxlength="20"></td>
			</tr>

			<tr>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Cidade</td>
				<td align="left" class="campo_tabela"><input type="text"
					name="municipio" size="30" maxlength="20"></td>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">UF</td>
				<td align="left" class="campo_tabela"><select name="uf"
					style="width: 50px;">
					<option value=""></option>
					<?php
					$sql="select uf from estado order by uf";
					$estado=mysqli_query($db, $sql);
					erro_sql("Select UF", $db, "");
					while($listaestado=mysqli_fetch_object($estado)){
					if($listaestado->uf=="SP"){
					?>
					<option value="<?php echo $listaestado->uf; ?>" selected><?php echo $listaestado->uf; ?></option>
					<?php
					}
					else{
					?>
					<option value="<?php echo $listaestado->uf; ?>"
					<?php if($listaestado->uf==$_POST[uf]){echo "selected";}?>><?php echo $listaestado->uf; ?></option>
					<?
					}
					}
					?></td>
			</tr>

			<tr>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Cep</td>
				<td align="left" class="campo_tabela"><input type="text" name="cep"
					id="cep" size="20" maxlength="10"></td>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Telefone</td>
				<td align="left" class="campo_tabela"><input type="text"
					name="telefone" id="telefone" size="30" maxlength="15"></td>
			</tr>

			<tr>
				<td align="left" class="descricao_campo_tabela"><img
					src="<? echo URL."/imagens/obrigat_1.gif";?>">Email</td>
				<td align="left" class="campo_tabela" colspan="3"><input type="text"
					name="e_mail" id="e_mail" size="102" maxlength="40"></td>
			</tr>

			<!-- Glaison  Inicio -->

			<table border="0" width="100%" class="titulo_tabela" cellpadding="0"
				cellspacing="1">

				<TR align="center">
					<TD colspan="">Configurações</TD>
					<TD width="10"><A href="javascript:showFrame('unidades');"><IMG
						SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0"
						TITLE="Exibir Informações de Configurações"></A></TD>
				</TR>
			</table>

			<TR>
				<TD>
				<div id="unidades" style="display: '';">
				<table border="0" width="100%" cellpadding="1" cellspacing="1">
					<tr>
						<td colspan="4" align="left"</td>

						<tr>
							<td colspan="2" align="left" width="25%"
								class="descricao_campo_tabela"><img
								src="<? echo URL."/imagens/obrigat_1.gif";?>" alt="" />SIG2M</td>
							<td colspan="4" align="left" class="campo_tabela"><input
								type="radio" name="flg_banco" value="1" /> IMA <input
								type="radio" name="flg_banco" value="0" checked="checked" />
							Unidades</td>
						</tr>
						<tr>
							<td colspan="2" align="left" class="descricao_campo_tabela"><img
								src="<? echo URL."/imagens/obrigat_1.gif";?>" alt="" />DNS</td>
							<td colspan="4" align="left" class="campo_tabela"><input
								type="text" name="dns_local" id="dns_local" size="102"
								maxlength="70" /></td>
						</tr>
						
                             <!-- nova alteração para nome do banco de dados   -->
                         <tr>
							<td colspan="2" align="left" class="descricao_campo_tabela"><img
								src="<? echo URL."/imagens/obrigat_1.gif";?>" alt="" />Banco de Dados</td>
							<td colspan="4" align="left" class="campo_tabela"><input
								type="text" name="base_integra_ima" id="base_integra_ima" size="102"
								maxlength="20" /></td>
						</tr>
					          <!-- Fim nova alteração-->

						<tr>
							<td colspan="2" align="left"  width="25%" class="descricao_campo_tabela"><img
								src="<? echo URL."/imagens/obrigat_1.gif";?>" />Usuário</td>
							
							
							<td colspan="" align="left" width="25%" class="campo_tabela"><input
								type="text" name="usuario_integra_local"
								id="usuario_integra_local" size="30" maxlength="15" /></td>
							
							
							<td colspan="" align="left" width="25%" class="descricao_campo_tabela"><img
								src="<? echo URL."/imagens/obrigat_1.gif";?>" />Senha</td>

							<td   colspan="" align="left" width="25%" class="campo_tabela" ><input type="password" name="senha_integra_local"
								id="senha_integra_local" size="30" maxlength="15" /></td>
						</tr>


						<tr>
							<td colspan="2" align="left" "
								class="descricao_campo_tabela"><img
								src="<? echo URL."/imagens/obrigat_1.gif";?>" alt="" />Integração<br>
							&nbsp&nbsp&nbsp Almox. Central</td>
							<td colspan="4" align="left"  class="campo_tabela"><input
								type="radio" name="flg_transf_almo" value="s""> Sim
							&nbsp&nbsp&nbsp <input type="radio" name="flg_transf_almo"
								value="n"checked  "> Não</td>
						</tr>



						</div>

				</table>


		<!-- Glaison  Fim -->


		<tr>
			<td colspan="2" align="right" class="descricao_campo_tabela"
				height="35"><input style="font-size: 10px;" type="button"
				name="voltar" value="<<Voltar"
				onClick="window.location='<?php echo URL;?>/modulos/unidade/unidade_inicial.php'">
			<input style="font-size: 10px;" type="button" name="salvar>>"
				value="Salvar>>" onClick="salvarDados();"></td>
		</tr>
		<tr>
			<td colspan="2" class="descricao_campo_tabela">
			<table align="center" border="0" cellpadding="0" cellspacing="1">
				<tr valign="center" class="descricao_campo_tabela" height="21">
					<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0">
					Campos Obrigatórios</td>
					<td>&nbsp&nbsp&nbsp</td>
					<td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0">
					Campos não Obrigatórios</td>
				</tr>
			</table>
			</td>
		</tr>
		
		  </table>
      </td>
    </tr>

		
		
		
		<div align="center"><?php
		////////////////////
		//RODAPÉ DA PÁGINA//
		////////////////////
		?> <script language="JavaScript" type="text/JavaScript">
    <!--
    //////////////////////////
    //DEFININDO FOCO INICIAL//
    //////////////////////////
    var x=document.form_inclusao;
    x.sigla.focus();
    //-->
    </script> 
   
		<?php
    require DIR."/footer.php";
}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else{
include_once "../../config/erro_config.php";
}
?></div>
