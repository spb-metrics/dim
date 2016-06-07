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
 
  //////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";
  
  $_SESSION[APLICACAO]=$_GET[aplicacao];

  //echo $_GET[aplicacao];
  if ($_GET[aplicacao] <> '')
  {
    $_SESSION[cod_aplicacao] = $_GET[aplicacao];
  }

  require "../../verifica_acesso.php";

  require DIR."/buscar_aplic.php";
?>

   <script language="JavaScript" type="text/javascript" src="../../scripts/grid_unidade_grupo.js"></script>
   <script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
   <script language="javascript" type="text/javascript" src="../../scripts/combo_dispensacao_profissional.js"></script>

 <script language="JavaScript" type="text/JavaScript">
 <!--
 
	function reload (){
	
	window.location.reload() 
	
	}
   function excluir_linha(linha){
     var tab=document.getElementById("tabela");
	 //alert(linha);
	 //alert (document.getElementById(linha).rowIndex);
	 tab.deleteRow(document.getElementById(linha).rowIndex);
     var ids='';
	// alert(linha);
	//alert(linha+"".indexOf("n"));
	
	//so pode entrar nessa condição caso o botao excluir for precionado

	 
	 if((linha+"").indexOf("n") == -1){
	  ids =document.getElementById("id_itens_deletados").value;
	   
	   ids = ids+','+ linha;
	   document.getElementById("id_itens_deletados").value =ids;
	 //  alert(ids);
	 
	 }

	 
   }

   function search_data(){   
     var x=document.cadastro;
     var unidade  = x.unidade.value;	
     requestInfo('showTableLista.php?mode=display&unidade='+unidade+'&excluir=<?php echo $exclusao_perfil;?>','showTableLista','', 'salvar');
   }
   
   function salvar_unidade_grupo(){
   
     var f = document.cadastro;
     var unidade = f.unidade.value.split("|");
     var id_unidade=unidade[0];     	 	 
     var itens=document.getElementById("tabela");
     var total_linhas=itens.rows.length;
     var id_grupo="";
	 var id_principal="";
	 var teste="";
	 var prin="";
     for(var i=0; i<total_linhas; i++){
	 var idLinha = itens.rows[i].id ;
	 
	 if(idLinha.indexOf("n") != -1){		
		idLinha= idLinha.substr(1);
		id_grupo+=idLinha + "|";
		}		
     }
	 id_grupo=id_grupo.substr(0, (id_grupo.length-1));   
     	var grupo = id_grupo;
		ids = document.getElementById("id_itens_deletados").value ;
		//alert ("valor de id"+ids);
     var url = "../../xml/salvar_unidade_grupo.php?unidade="+unidade+"&grupo="+grupo+"&ids="+ids;
      //var url = "../../xml/salvar_unidade_grupo.php?unidade="+unidade+"&grupo="+grupo;
		requisicaoHTTP("GET", url, true, '');
	
		 //	window.location.reload();
		
	 }
   
   
   
   function trataDados(){
     var x=document.cadastro;
     var info = ajax.responseText;  // obtém a resposta como string
	 if (info==""){
       window.alert("Operação efetuada com sucesso!");
     }
     if(info=="erro"){
       window.alert("Não foi possível efetuar operação!");
     }
     x.unidade.selectedIndex=0;
     x.grupo.selectedIndex = 0;
     x.grupo.disabled=false;
     x.salvar.disabled=true;
     removerItens();
   }

   function removerItens(){
	var itens=document.getElementById("tabela");
    // alert (itens);
	 
	 var total_linhas=itens.rows.length;
	 

     for(var i=total_linhas-1; i>=0; i--){
       excluir_linha(itens.rows[i].id);
     }
	  reload();
   }

   function carregarUnidadeAplicacao(){ 
     var x=document.cadastro;	
	//limpar o hidden ates de carefa a aplicacao
		x.id_itens_deletados = "";
		
		//var uni = x.unidade.value.split("|");
		 //  var uni=document.getElementById('unidade').value;
		  //uni.split("|");
		   
		//	alert(uni[0]);
		
 	 
     if(x.unidade.value!=""){
	    <?php           
		 if($inclusao_perfil!=""){
       ?>
           //x.grupo.disabled=false;
       <?php
         }
       ?>
	   
       search_data();
     }
     else{	     	   
       x.salvar.disabled=true;
       x.grupo.disabled=false;
       //removerItens();
     }

   }

   function inserir_linha(){
     var x=document.cadastro;
     var achou=false;

			if(x.flg_nivel_superior.value != 0){
			var valores_unidade=x.unidade.value;
			
			}else{

			var valores_unidade=x.unidade1.value;
			
			}
       var valores_aplicacao=x.grupo.value.split('|');

		 
		 if (x.principal[0].checked == false){
		 var valores_principal = "N";
		 
		 } else{
		 
		 var valores_principal = "S";
		 }
	 
	 var itens=document.getElementById("tabela");
     var total_linhas=itens.rows.length;
     for(var i=0; i<total_linhas; i++){
       var valor=itens.rows[i].id;
	      if(valor.indexOf("n") != -1){		
		   valor= valor.substr(1);
		   
		//val+=idLinha + "|";
		
		 valor = valor[0];
		}
		  
		  
		  //alert("valor "+valor);
		  //alert("valor_aplicao"+valores_aplicacao[0]);
	   
       if(valor==valores_aplicacao[0]){
	   
	
         achou=true;
       }
     }
     if(achou==true){
       window.alert("Grupo já adicionado!");
       x.grupo.selectedIndex=0;
       x.grupo.focus();
       return false;
     }
     else{
       var tab=itens.insertRow(total_linhas);
       tab.id="n"+valores_aplicacao[0]+","+valores_principal;
       tab.className="campo_tabela";
       //descricao unidade
       var cel0=tab.insertCell(0);
       cel0.align="left";
       cel0.width="30%";
	   if(x.flg_nivel_superior.value == 0){
			
			cel0.innerHTML=valores_unidade;

		

			
			}else{
			 var nome_uni=valores_unidade.split('|');
			 cel0.innerHTML=nome_uni[1];
					

			
			}
	   
       //descricao aplicacao
       var cel1=tab.insertCell(1);
       cel1.align="left";
       cel1.width="30%";
       cel1.innerHTML=valores_aplicacao[1];


	   
	 
	   
       var cel2=tab.insertCell(2);
       cel2.align="center";
       cel2.width="3%";
      
    //descrição principal
	   	var cel3=tab.insertCell(2);
		cel3.align="left";
		cel3.width="5%";
		cel3.innerHTML=valores_principal;
		
	   
	
		

	  <?php
         if("$exclusao_perfil"!=""){
       ?>
       var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
       var urlRemover="javascript:excluir_linha('"+tab.id+"')";
       cel2.innerHTML=linkRemover.link(urlRemover);
       <?php
			
         }
       ?>
       x.grupo.selectedIndex=0;
       x.salvar.disabled=false;
     }
   }
 //-->

  function enviar()  // type=submit
  {  
     if (document.cadastro.unidade.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.unidade.focus();
        return false;
     }

     return true;   // envia formulario
  }
  
<?php  
  
$sql = "select id_unidade, unidade_id_unidade,nome,flg_nivel_superior from unidade where id_unidade=$_SESSION[id_unidade_sistema]";
$sql_inicial = mysqli_query($db, $sql);
erro_sql("Unidade Inicial", $db, "");
echo mysqli_error($db);
$nome="";
$id_inicial="";
if (mysqli_num_rows($sql_inicial) > 0)
{
   $inicial = mysqli_fetch_object($sql_inicial);
   $nome = $inicial->nome;
   $id_inicial= $inicial->id_unidade;
   $unidade_id_unidade = $inicial->unidade_id_unidade;
   $flg_nivel_superior = $inicial->flg_nivel_superior;
   
  
}	

?>
  </script>

  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
     <tr><td> <?php echo $caminho; ?> </td></tr>
  </table>

  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr height="5%">
      <td>
        <table width="100%" class="titulo_tabela" height="21">
          <tr><td align="center"> <?php echo $nome_aplicacao;?></td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <form name="cadastro" id="cadastro">
	  <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Unidade
			
              </td>
			  <td align="left" width="70%" class="campo_tabela">
				<input type="hidden" name="flg_nivel_superior" id="flg_nivel_superior" value="<?echo $flg_nivel_superior;?>">
			    <input type="hidden" name="id_unidade_sistema" id="id_unidade_sistema" value="<?=$_SESSION[id_unidade_sistema]?>">
        <? if($flg_nivel_superior==0){ ?>
		
					
						<input type="hidden" name="unidade" id="unidade" style="width: 250px" value="<?echo $id_inicial;?>">
                         <input type="textBox" name="unidade1" id="unidade1" style="width: 250px" value="<? echo$nome;?>"disabled>
					
					<tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Principal
                      </td>
                      <td class="campo_tabela" colspan="6" valign="middle" width="100%">
							<input type="radio" name="principal" value="S" <?php if($principal=="S"){echo "checked"; echo"";}?> >Sim &nbsp&nbsp&nbsp
							<input type="radio" name="principal" value="N" <?php if($principal=="" or $principal=="N"){echo "checked";}?> >Não											
                        <div id="acDivU"></div>
                      </td>	
					  
                    </tr>
					
					
					
					
					
					<tr>		
						 <td align="left" width="30%" class="descricao_campo_tabela">
							<img src="<? echo URL."/imagens/obrigat.gif";?>">Grupo
						</td>

					 <td align="left" width="70%" class="campo_tabela">
						<select id="grupo" name="grupo" size="1" style="width: 400px" onchange="inserir_linha();">
                          <option id="grupo1" value="0">Selecione um Grupo </option>
                      
								  <? $sql = "select descricao, id_grupo 
										from grupo
										where status_2 = 'A' and id_grupo not in
										(select grupo_id_grupo from unidade_grupo where unidade_id_unidade =$id_inicial) order by descricao";
								  
												$nivel = mysqli_query($db, $sql);

													 erro_sql("Select Unidade Superior", $db, "");
													 while($lista_nivel=mysqli_fetch_object($nivel)){
								  ?>

								<option id="grupo" value="<?php echo $lista_nivel->id_grupo . '|' . $lista_nivel->descricao; ?>"><?php echo $lista_nivel->descricao; ?> </option>
					  
								<? }?>
					  </select>
					</td>
					
					
					
					</tr>	
						

                            <? }
                              else
                              { ?>
							  
							<select name="unidade" id="unidade" style="width:400px;" onChange="carregarCombo(this.value, '../../xml/grupo_ajax.php', 'lista_grupos', 'opt_grupo', 'grupo'); search_data();">
									
								 <option value="">Selecione uma unidade</option>
								   <?php
								   
									if($unidade_id_unidade != 0){
									   $sql="select id_unidade, nome
											   from unidade
											   where  status_2 = 'A' and unidade_id_unidade='$id_inicial' and flg_nivel_superior=0 order by nome";
										 }else{
											$sql="select id_unidade, nome
											   from unidade
											   where  status_2 = 'A'  order by nome";										 										 
										 }
										 $nivel = mysqli_query($db, $sql);
										
										 erro_sql("Select Unidade Superior", $db, "");
										 while($lista_nivel=mysqli_fetch_object($nivel)){
									?>
													<option value='<?php echo $lista_nivel->id_unidade.'|'.$lista_nivel->nome;?>'><?php echo $lista_nivel->nome;?> </option>
													
													<?	} ?>
					<tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Principal
                      </td>
                      <td class="campo_tabela" colspan="6" valign="middle" width="100%">
							<input type="radio" name="principal" value="S" <?php if($principal=="S"){echo "checked"; echo"";}?> >Sim &nbsp&nbsp&nbsp
							<input type="radio" name="principal" value="N" <?php if($principal=="" or $principal=="N"){echo "checked";}?> >Não											
                        <div id="acDivU"></div>
                      </td>	
					  
                    </tr>
					
									
									<tr>
									  <td align="left" width="30%" class="descricao_campo_tabela">
									   <img src="<? echo URL."/imagens/obrigat.gif";?>">Grupo
									  </td>

									  <td align="left" width="70%" class="campo_tabela">
							   
										 <select id="grupo" name="grupo" size="1" style="width: 400px" onchange="inserir_linha();">
												  <option id="opt_grupo" value="0">Selecione um Grupo </option>
									 </select>

									  </td>
									</tr>
											
					
					
					
					<?php } ?>
						
					 
					
					</select>
				
				</td>

            </tr>
			<input type="hidden" name="id_itens_deletados" id="id_itens_deletados" value = "-1" >
         

								
         	<TR>
	          <TD colspan="4">
     			<table width="100%" cellpadding="0" cellspacing="1" >
                  <TR align="center" class="coluna_tabela">			
					<TD width='30%' align='center'>Unidade</TD>
					<TD width='30%' align='center'>Grupo</TD>
					<TD width='5%' align='center'>Principal</TD>
                    <TD width="3%" align='right'></TD>
                  </TR>
                </TABLE>
              </TD>
			</TR>

            <tr>
              <td colspan='4'>
                <div id="showTableLista"></div>
              </td>
            </tr>
    		<tr>
			  <td colspan="2" class="descricao_campo_tabela" height="21">
				<table align="center" border="0">
				       <tr valign="top" class="descricao_campo_tabela">
						<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
						<td>&nbsp&nbsp&nbsp</td>
                        <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
					   </tr>
				</table>
              </td>
			</tr>
            <tr>
              <td colspan="2" align="right" class="descricao_campo_tabela" height="35">
                <input style="font-size: 12px;" type="button" id="salvar" name="salvar" name="salvar" value="Salvar >>" onClick="if(enviar()){salvar_unidade_grupo();}" >
              </td>
            </tr>
            </form>
          </table>
      </td>
    </tr>
  </table>

<?php
  ////////////////////
  //RODAPÉ DA PÁGINA//
  ////////////////////
  ?>
    <script language="JavaScript" type="text/JavaScript">
    //////////////////////////
    //DEFININDO FOCO INICIAL//
    //////////////////////////
    document.cadastro.unidade.focus();
	 carregarUnidadeAplicacao();
	 //search_data();
    if (document.cadastro.unidade.disabled)
    {
       document.cadastro.unidade.changer();
    };
	

		
    </script>
  <?php
  require DIR."/footer.php";
  
}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else
{
  include_once "../../config/erro_config.php";
}
?>
