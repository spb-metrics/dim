<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
//////////////////////////////////////////////////
//TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
//////////////////////////////////////////////////

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (file_exists("../../config/config.inc.php"))
{
	require "../../config/config.inc.php";
	////////////////////////////
	//VERIFICA��O DE SEGURAN�A//
	////////////////////////////
	if($_SESSION[id_usuario_sistema]=='')
	{
		header("Location: ". URL."/start.php");
	}

	//************* permiss�o para finalizar receita *************//
	$sql = "select
                  id_aplicacao
            from
                  aplicacao
            where
                  executavel = '/modulos/dispensar/finalizar_receita.php'
                  and status_2 = 'A'";
	$res_finalizar = mysqli_fetch_object(mysqli_query($db, $sql));
	$id_aplicacao_finalizar = $res_finalizar->id_aplicacao;
	/*
	 $sql = "select
	 inclusao, alteracao, exclusao, consulta
	 from
	 perfil_has_aplicacao
	 where
	 perfil_id_perfil = '$_SESSION[id_perfil_sistema]'
	 and aplicacao_id_aplicacao = '$id_aplicacao_finalizar'";

	 $acesso_finalizar = mysqli_fetch_object(mysqli_query($db, $sql));
	 $inclusao_perfil_finalizar  = $acesso_finalizar->inclusao;
	 $alteracao_perfil_finalizar = $acesso_finalizar->alteracao;
	 $exclusao_perfil_finalizar  = $acesso_finalizar->exclusao;
	 $consulta_perfil_finalizar  = $acesso_finalizar->consulta;
	 */

	//******************************//

	$sql="select 'S' as mostrar_responsavel_dispensacao
		   from unidade_has_aplicacao
		   where unidade_id_unidade=$_SESSION[id_unidade_sistema] and
				 aplicacao_id_aplicacao='$id_aplicacao_finalizar'";
	$res=mysqli_query($db, $sql);
	erro_sql("Mostrar Respons�vel Dispensa��o", $db, "");
	$unidade_mostrar_responsavel_dispensacao=mysqli_fetch_object($res);
	$mostrar_responsavel_dispensacao=$unidade_mostrar_responsavel_dispensacao->mostrar_responsavel_dispensacao;


	//echo $mostrar_responsavel_dispensacao;
	//exit;



	////////////////////////////////////
	//BLOCO HTML DE MONTAGEM DA P�GINA//
	////////////////////////////////////
	// require DIR."/header.php";


	require DIR."/buscar_aplic.php";

	?>

<script
	language="javascript" type="text/javascript"
	src="../../scripts/prescritor_material.js"></script>


<script language="javascript">

    function Trim(str){
      return str.replace(/^\s+|\s+$/g,"");
    }

      function habilitaBotaoSalvar(){
      var x=document.form_inclusao;
      if(Trim(x.login.value)=="" || Trim(x.senha.value)=="" ){
	  
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

    function verificaLoginSenhaResponsavelDispensacao(){
      var x=document.form_inclusao;
      var url = "../../xml_dispensacao/verificar_login_senha_responsavel_dispensacao.php?login="+x.login.value+"&senha="+x.senha.value;	  

	  requisicaoHTTP("GET", url, true, '');
	  
    }
	
	function checar_motivo(){

	var x= document.form_inclusao;
	if(x.motivo_fim_receita.value == ''){
	
	alert ('Motivo da finaliza��o do item deve ser informado');
	
	}else {
     //if(x.flag_mostrar_responsavel_dispensacao.value=="S"){	 	
	 //alert('if denise');
       // verificaLoginSenhaResponsavelDispensacao();
	//	window.location='observacao_receita.php';
      }
	
	}
	
	
//	}
	
	
function trataDados()
{
  var info = ajax.responseText;  // obt�m a resposta como string

// var id_login = 
    alert ('<*>' + info);
	//exit;

    var login_senha=info.split("@");
	
	//alert ('>>>[0]' + login_senha[0] + '\n' + '>>>[1]' + login_senha[1]);
    
    //retorno de verificar_login_senha_responsavel_dispensacao.php
     if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
	   //alert(login_senha[0] + "==sim_login_senha_responsavel_dispensacao = " + (login_senha[0]=="sim_login_senha_responsavel_dispensacao"));

       document.form_inclusao.id_login.value=login_senha[1];
       return;
    //habilitar os campos e os botoes colocar aqui
	
     }
     
     if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
       document.form_inclusao.id_login.value=login_senha[1];
       window.alert("Login e/ou Senha para Finaliza��o de Receita Inv�lidos!");
       document.form_inclusao.login.focus();
       return;
     }

}
	/* a Fun��o recarrega_pg_ant, fecha a p�gina de observa��o em seguida faz refresh na pagina finalizar receita para mostrar os itens finalizados*/
	   function recarrega_pg_ant(){
	   
	   //alert ('entrou na funcao recarga');
          window.opener.resultado_final_medicamento();
          window.close();										
       }



</script>


	<?PHP


	$id_itens_receita =  $_GET['id_item_receita'];
	$id_receita = $_GET['id_receita'];


	$data = date("Y/m/d");

	$sql_rec = "select * from  itens_receita
			   where 
			   receita_id_receita = $id_receita  and 
			   id_itens_receita = $id_itens_receita ";

	//echo $sql_rec;
	//exit;

	$result=mysqli_query($db, $sql_rec);

	while ($dados_est = mysqli_fetch_object($result)){

		$obs = $dados_est ->ds_observacao;
		$id_motivo = $dados_est -> motivo_fim_receita_id_motivo_fim_receita;
		$id_usua_fim_receita = $dados_est -> id_usua_fim_receita;

	}

	$sql_motivo = "select * from  motivo_fim_receita  where idmotivo_fim_receita = $id_motivo  ";
	$origem = mysqli_query($db, $sql_motivo);
	if($origem){
		while ($dadosorigem = mysqli_fetch_object($origem))
		{
			$motivo = $dadosorigem->motivo;
				
		}
	}
		
	$sql_usu = "select nome from usuario where id_usuario = $id_usua_fim_receita";
	$usu =mysqli_query($db, $sql_usu);
	if($usu){
		while($dadosusuario = mysqli_fetch_object($usu)) {
			$usuario = $dadosusuario->nome;
		}
	}
		
		
		

	 

	?>


<link
	href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left"></td>
	</tr>
	<tr class="titulo_tabela">
		<td colspan="2" valign="middle" align="center" width="100%"
			height="21">Finalizar Receita</td>

	</tr>


	<form name="form_inclusao" action="observacao_receita.php"
		method="POST" enctype="application/x-www-form-urlencoded">
	<tr>
		<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
			SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Motivo</td>

		<td colspan="4" class="campo_tabela" valign="middle" width="25%"><select
			size="1" id="motivo_fim_receita" name="motivo_fim_receita"
			style="width: 280px;" disabled>
			<option value=""><?PHP echo $motivo ?></option>

		</select></td>
	</tr>


	<tr>
		<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
			SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Observa��o
		</td>
		<td colspan="4" class="campo_tabela" valign="middle" width="25%"><textarea
			name="observacao" rows="5" cols="70" disabled><?php echo$obs; ?></textarea>
		</td>

	</tr>
	<tr>
		<td colspan="5" class="campo_tabela" valign="middle" width="25%"><?php
		//$mostrar_responsavel_dispensacao = "S";
		if($mostrar_responsavel_dispensacao!="S"){

			$mostrar_login_senha="none";
		}
		else{
			$mostrar_login_senha="''";
		}
		?>
		<div id="mostrar_responsavel_dispensacao" style="display:<?php echo $mostrar_login_senha;?>">
		<table border="0" width="100%">
			<tr>
				<td class="descricao_campo_tabela" width="9%">Finalizado por:</td>
				<td class="descricao_campo_tabela" valign="middle" width="7%">
				Login:</td>
				<td width="8%" class="descricao_campo_tabela"><input type="text"
					name="login" value="<?php echo $usuario; ?>" disabled> <input
					type="hidden" name="id_login"></td>
				<td class="descricao_campo_tabela" valign="middle" width="7%">
				Senha:</td>
				<td width="25%" class="descricao_campo_tabela"><input
					type="password" name="senha"
					onblur="habilitaBotaoSalvar(); document.form_inclusao.salvar.focus();"
					onfocus="desabilitaBotaoSalvar();" disabled></td>
			</tr>
		</table>
		</div>
		</td>

	</tr>

</table>


</table>
<table name='3' cellpadding='0' cellspacing='1' border='0' width='100%'
	height="10%">

	<tr>

		<td align="right" bgcolor="#D8DDE3"><input style="font-size: 10px;"
			type="button" name="receita" value="<< Voltar"
			onClick="javascript:window.close();"> <input style="font-size: 10px;"
			type="submit" name="salvar" value="Salvar >>"
			onClick="checar_motivo()" disabled></td>
	</tr>
	<input type="hidden" name="id_receita" id="id_receita"
		value="<?php echo $id_receita ?>">
	<input type="hidden" name="id_itens_receita" id="id_itens_receita"
		value="<?php echo $id_itens_receita  ?>">
	<input type="hidden" name="status" id="status" value="FINALIZADO">
	<input type="hidden" id="flag_mostrar_responsavel_dispensacao"
		name="flag_mostrar_responsavel_dispensacao"
		value="<?php echo $mostrar_responsavel_dispensacao;?>" disabled>
	</td>
	</tr>


	<tr>
		<td colspan="8" class="descricao_campo_tabela">
		<table align="center" border="0" cellpadding="0" cellspacing="0">
			<tr valign="top" class="descricao_campo_tabela" height="25">
				<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0">
				Campos Obrigat�rios</td>
				<td>&nbsp&nbsp&nbsp</td>
				<td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0">
				Campos n�o Obrigat�rios</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</form>

		<?php
		////////////////////
		//RODAP� DA P�GINA//
		////////////////////
		// require DIR."/footer.php";
		////////////////////////////////////////////
		//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
		////////////////////////////////////////////

}
else
{
	include_once "../../config/erro_config.php";
}
?>
