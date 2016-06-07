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

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (file_exists("../../config/config.inc.php"))
{
	require "../../config/config.inc.php";




	////////////////////////////
	//VERIFICAÇÃO DE SEGURANÇA//
	////////////////////////////
	if($_SESSION[id_usuario_sistema]=='')
	{
		header("Location: ". URL."/start.php");
	}

	//************* permissão para finalizar receita *************//
	$sql = "select
                  id_aplicacao
            from
                  aplicacao
            where
                  executavel = '/modulos/dispensar/finalizar_receita.php'
                  and status_2 = 'A'";
	$res_finalizar = mysqli_fetch_object(mysqli_query($db, $sql));
	$id_aplicacao_finalizar = $res_finalizar->id_aplicacao;
	//echo $id_aplicacao_finalizar;

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

	//******************************//


	if($_GET[id_receita]!="")
	{

		//busca dados do paciente
		$id_receita = $_GET[id_receita];
		$sql = "select ano, unidade_id_unidade, numero,
                    profissional_id_profissional, data_emissao,
                    subgrupo_origem_id_subgrupo_origem, cidade_id_cidade,
                    paciente_id_paciente
             from
                    receita
             where
                    id_receita = '$id_receita'";
		$dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));
		 
		$ano = $dados_receita->ano;
		$unidade = $dados_receita->unidade_id_unidade;
		$numero = $dados_receita->numero;
		$prescritor = $dados_receita->profissional_id_profissional;
		$data_emissao = $dados_receita->data_emissao;
		$data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);
		$origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
		$cidadereceita = $dados_receita->cidade_id_cidade;
		$id_paciente = $dados_receita->paciente_id_paciente;

		$sql = "select inscricao, nome
             from
                    profissional
             where
                    id_profissional = '$prescritor'";
		$dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
		$inscricao = $dados_prescritor->inscricao;
		$nomeprescritor = $dados_prescritor->nome;

		$sql = "select nome
             from
                    cidade
             where
                    id_cidade = '$dados_prescritor->cidade_id_cidade'";
		$dados_cidade = mysqli_fetch_object(mysqli_query($db, $sql));
		$nomecidade = $dados_cidade->nome;

		$sql = "select uf
             from
                    estado
             where
                    id_estado = '$dados_cidade->estado_id_estado'";
		$dados_estado = mysqli_fetch_object(mysqli_query($db, $sql));
		$nomeuf = $dados_estado->uf;

		$sql = "select descricao
             from
                    subgrupo_origem
             where
                    id_subgrupo_origem = '$origem'";
		$dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
		$nomeorigem = $dados_origem->descricao;

		$sql = "select id_cidade, concat(c.nome,'/',e.uf) as nome
             from
                    cidade c,
                    estado e
             where
                    c.id_cidade = '$cidadereceita'
                    and c.estado_id_estado = e.id_estado";
		$dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
		$nomecidadereceita = $dados_cidadereceita->nome;

		$sql = "select
                    nome, data_nasc, sexo
             from
                    paciente
             where
                    id_paciente = '$id_paciente'";
		$dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));
		 
		//$cartao_sus      = $dados_paciente->cartao_sus;
		//$cartao_sus_prov = $dados_paciente->cartao_sus_prov;
		$nome            = $dados_paciente->nome;
		$data_nasc       = $dados_paciente->data_nasc;
		$data_nasc       = substr($data_nasc,-2)."/".substr($data_nasc,5,2)."/".substr($data_nasc,0,4);
		$sexo            = $dados_paciente->sexo;
		if ($sexo=='F')
		{
			$sexo = "FEMININO";
		}
		else
		{
			if ($sexo=='M')
			{
				$sexo = "MASCULINO";
			}
			else
			{
				$sexo='';
			}
		}
	}



	////////////////////////////////////
	//BLOCO HTML DE MONTAGEM DA PÁGINA//
	////////////////////////////////////
	// require DIR."/header.php";


	?>

<script language="javascript">

	window.onbeforeunload = fecharJanela;  

		function fecharJanela(){			
				//alert ('entrou na função recarregar');
                    window.opener.resultado_final_receita();
	    }
		
	


   function popup_observacao_receita_all(receita, id_iten_all) {

	var height = 500;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	var cont = 0;
	var repet = true;
	var primeiro_reg = true;
	var id_iten_all='';
	

         while(repet){
             var check = document.getElementById('opcao[' + cont + ']');
			 var id_item_receita = document.getElementById('id_item_receita[' + cont + ']');
	
             if(check != null ){
		
                if(check.checked == true){
					if(!primeiro_reg == true){
						id_iten_all += ',';
					}else{
						primeiro_reg=false;
					}
					id_iten_all += id_item_receita.value;
					//alert (id_iten_all);
				}
                cont++;
             }
             else{
               repet = false;
             }
      }

		

	if (window.showModalDialog)
	{		
		var dialogArguments = new Object();
		var _R = window.showModalDialog("observacao_receita.php?id_receita="+receita+"&id_item_receita="+id_iten_all, dialogArguments, "dialogWidth=1000px;dialogHeight=270px;scroll=yes;status=no;");
	}

}

    function popup_observacao_receita(receita,id_item_receita) {

	var height = 500;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("observacao_receita_detalhe.php?id_receita="+receita+"&id_item_receita="+id_item_receita, dialogArguments, "dialogWidth=1000px;dialogHeight=200px;scroll=yes;status=no;");
	}

}

function checkAll(chk){
  var cont = 0;
  var repet = true;
  var checkar = chk.checked;
	         while(repet) {
             var check = document.getElementById('opcao[' + cont + ']');
			  if(check != null){ 
			     check.checked=checkar;
                 cont++;
                }
                else{
				
                  repet = false;
                }
        }
			if (checkar){
			document.form_inclusao.finalizar.disabled =false;
			
			}else{
			//alert ('if');
			document.form_inclusao.finalizar.disabled =true;
			
		}		
 }

	function resultado_final_medicamento(){
    var id_receita = document.form_inclusao.id_receita.value;
    document.form_inclusao.action = "finalizar_receita.php?id_receita="+id_receita;
    document.form_inclusao.submit(); 
	
}  

function teste(obj){
  var cont = 0;
  var check = obj.value;
  var c = obj.checked;  
  var repet = true;
  var checkar = obj.checked;
  var linhas = document.form_inclusao.qtd_linhas.value;
  var lista = false;
	if (check != c){
	document.form_inclusao.finalizar.disabled =false;
	}
	else {
	document.form_inclusao.finalizar.disabled =true;
         while(cont < linhas) {
             var check = document.getElementById('opcao[' + cont + ']');             
			  if(check.checked){
			  
                 lista = true;
			     break;
                 
                }
                else{
				
                 cont ++;
                }
        }
    if (lista){
		document.form_inclusao.finalizar.disabled =false;
		}
    else
    {
    document.form_inclusao.finalizar.disabled =true;
    }
   }
}
	
function desabilita_check_all() {
var linhas = document.form_inclusao.qtd_linhas.value;
var cont =0;
	while(cont < linhas) {
             var check = document.getElementById('opcao[' + cont + ']');             
			  if(check.checked){

					var x =  document.form_inclusao.todos.disabled = true;
					
			}		
}
}

function desabilita(chk){
  var cont = 0;
  var repet = true;
  var checkar = chk.checked;
	         while(repet) {
             var check = document.getElementById('opcao[' + cont + ']');
			  if(check != null){ 
			     //check.checked=checkar;
				 check.disabled =true;

                 cont++;
                }
                else{
				
                  repet = false;
                }
        }
			if (checkar){
			document.form_inclusao.finalizar.disabled =true;
			
			}
 }
 
</script>
<form name="form_inclusao" method="POST">
<link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left">
		<table borde="0" width="100%" cellpadding="0" cellspacing="1">

			<tr class="titulo_tabela">
				<td colspan="8" valign="middle" align="center" width="100%"
					height="21">Receita</td>
			</tr>

			<tr>
				<td class="descricao_campo_tabela" valign="middle" width="25%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Ano</td>
				<td class="campo_tabela" valign="middle" width="15%"><input
					type="text" name="ano" size="10" maxlength="4"
					value="<?php echo $ano;?>" disabled></td>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Unidade</td>
				<td class="campo_tabela" valign="middle" width="20%"><input
					type="text" name="codigo_unidade" size="10" maxlength="10"
					value="<?php echo $unidade;?>" disabled></td>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Número</td>
				<td colspan="8" class="campo_tabela" valign="middle" width="15%"><input
					type="text" name="numero" size="5" maxlength="10"
					value="<?php echo $numero;?>" disabled></td>
			</tr>

			<tr>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Dt. Emissão
				</td>
				<td class="campo_tabela" valign="middle" width="15%"><input
					type="text" name="data_emissao" size="10" maxlength="10"
					value="<?php echo $data_emissao;?>" disabled></td>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Origem</td>
				<td colspan="8" class="campo_tabela" valign="middle" width="15%"><select
					size="1" name="origem_receita" style="width: 150px;" disabled>
					<option value="<?php echo $origem;?>"><?php echo $nomeorigem;?></option>
				</select></td>
			</tr>

			<tr>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Cidade</td>
				<td colspan="8" class="campo_tabela" valign="middle" width="15%"><input
					type="text" size="30" value="<?php echo $nomecidadereceita;?>"
					disabled></td>
			</tr>

			<tr>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Paciente</td>
				<td colspan="8" class="campo_tabela" valign="middle" width="25%"><input
					type="text" name="nome" size="70" maxlength="70"
					value="<?php echo $nome;?>" disabled></td>
			</tr>

			<tr>
				<td class="descricao_campo_tabela" valign="middle" width="15%"><IMG
					SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Prescritor

				</td>
				<td colspan="8" class="campo_tabela" valign="middle" width="25%"><select
					size="1" name="prescritor" style="width: 450px;" disabled>
					<option value="<?php echo $prescritor;?>" selected><?php echo $nomeprescritor;?></option>
				</select></td>
			</tr>

			<tr class="titulo_tabela">
				<td colspan="8" valign="middle" align="center" width="100%">
				Materiais / Medicamentos Dispensados</td>
			</tr>
			<tr bgcolor='#6B6C8F' class="coluna_tabela">
				<td width='30%' align='center'>Materiais / Medicamentos</td>
				<td width='8%' align='center'>Ult. Disp</td>
				<td width='5%' align='center'>N.Notificação</td>
				<td width='5%' align='center'>Qte. Prescrita</td>
				<td width='8%' align='center'>Tempo Tratamento</td>
				<td width='8%' align='center'>Qtde. Dispensada Anterior</td>
				<td width='8%' align='center'>Saldo</td>
				<td width='8%' align='center'><input type="checkbox" name="todos"
					onclick="checkAll(this);" disabled></td>

			</tr>


			<input type="hidden" name=id_receita
				value=<?php echo $id_receita ; ?>>
				<?php
					
				$count = 0;
				$sql = "select ir.*, ma.descricao from itens_receita ir, material ma
                  where ir.receita_id_receita = '$id_receita'
                  and ir.material_id_material = ma.id_material
                  order by id_itens_receita ";
				$item = mysqli_query($db, $sql);
					
				$qtd_linhas = mysqli_num_rows($item);
				while ($dados_item = mysqli_fetch_object($item))
				{
					?>
			<tr class="linha_tabela">
				<td bgcolor="#D8DDE3" align="left"><!--Descrição do medicamento -->
				<?php echo $dados_item->descricao;?> <?php
				$id_item_receita = $dados_item->id_itens_receita;
				?></td>


				<td bgcolor="#D8DDE3" align="left"><!--Ultima dispensação --> <?php
								if (substr($dados_item->data_ult_disp,0,10) == '0000-00-00'){
					echo "-----";
				}else{
					echo substr($dados_item->data_ult_disp,8,2)."/".substr($dados_item->data_ult_disp,5,2)."/".substr($dados_item->data_ult_disp,0,4);
				}
			
				?></td>

				<td bgcolor="#D8DDE3" align="center"><?php 
					
				if($dados_item->num_receita_controlada == ""){
					echo "-----";

				}else{
					echo $dados_item->num_receita_controlada;
				}



				?></td>

				<td bgcolor="#D8DDE3" align="left"><!--Quantidade prescrita para a receita -->
				<?php echo intval($dados_item->qtde_prescrita);?></td>


				<td bgcolor="#D8DDE3" align="left"><!--Tempo de tratamento --> <?php echo $dados_item->tempo_tratamento;?>
				</td>
				<td bgcolor="#D8DDE3" align="left"><!--Quantidade dispensada anterior -->
				<?php echo intval($dados_item->qtde_disp_anterior + $dados_item->qtde_disp_mes);?>
				</td>

				<td bgcolor="#D8DDE3" align="left"><!--saldo --> <?php
				$disp = intval($dados_item->qtde_disp_anterior)+intval($dados_item->qtde_disp_mes);
				
				$prescrita=$dados_item->qtde_prescrita;
				$saldo = ($prescrita  - $disp);

				echo intval($saldo);?></td>

				<td bgcolor="#D8DDE3" align="left"><!-- <input type="checkbox"  name="lista_itens">-->
				<?php
					
				$status =$dados_item->status;
				if($status !="FINALIZADO" ) {
					echo "<script>   document.form_inclusao.todos.disabled=false;  </script>";
					echo '<input type=\'hidden\' id=\'id_item_receita['.$count.']\' value=\''. $id_item_receita. '\' > ';
					echo '<input type=\'checkbox\' name=\'opcao['.$count.']\' id=\'opcao['.$count.']\' value=\''.$info.'\' onclick=\'  teste(this); id_item_receita['.$count.']='.$id_item_receita.' \'> ';
					$count = $count+1;
				}else {
					?> <img src='<?php echo URL;?>/imagens/b_search.png'
					onclick='JavaScript:window.popup_observacao_receita(<?php echo $id_receita ;?>,<?php echo $id_item_receita ;?>);'
					border='0' title='Detalhar Finalização do Medicamento'></a> <?php
	
				}
				?> <input type="hidden" name="lista_itens"></td>
			</tr>
			<!-- fim da tabela-->

			<?php
				}
				?>
			<input type="hidden" name="qtd_linhas"
				value="<?php echo $qtd_linhas;?>">

		</table>
		</td>
	</tr>

	<td height="100%" align="center" valign="top">
	<table name='3' cellpadding='0' cellspacing='1' border='0' width='100%'
		height="10%">
		<tr>
			<td colspan="8" align="right" bgcolor="#D8DDE3"><input
				style="font-size: 10px;" type="button" name="receita"
				value="<< Voltar" onClick="javascript:window.close();"> <!-- verificar a permissão  if $incluir_perfil_finalizar .... -->

				<?PHP				
				if ($inclusao_perfil_finalizar == ""){
					echo"<script>
									document.form_inclusao.todos.disabled =true;
									desabilita(this);
								</script>";
					?> <input style="font-size: 10px;" type="button" name="finalizar"
				value="Finalizar >>"
				onClick="javascript:window.popup_observacao_receita_all(<?php echo $id_receita ;?>) , (<?php echo $id_item_receita;?>) ;"
				disabled> <?PHP
				} else {
					?> <input style="font-size: 10px;" type="button" name="finalizar"
				value="Finalizar >>"
				onClick="javascript:window.popup_observacao_receita_all(<?php echo $id_receita ;?>) , (<?php echo $id_item_receita; ?>);"
				disabled> <?PHP

				} ?></td>

		</tr>
	</table name='3'>
	</td>
	</tr>
</table>
</form>

				<?php




				////////////////////
				//RODAPÉ DA PÁGINA//
				////////////////////
				// require DIR."/footer.php";
				////////////////////////////////////////////
				//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
				////////////////////////////////////////////

}
else
{
	include_once "../../config/erro_config.php";
}
?>
