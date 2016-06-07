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

  if ($_GET[aplicacao] <> '')
  {
    $_SESSION[cod_aplicacao] = $_GET[aplicacao];
  }

  require "../../verifica_acesso.php";

  require DIR."/buscar_aplic.php";
?>



   <script language="JavaScript" type="text/javascript" src="../../scripts/grid_unidade_aplicacao.js"></script>
   <script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>

 <script language="JavaScript" type="text/JavaScript">
 <!--
   function excluir_linha(linha){
     var tab=document.getElementById("tabela");
     tab.deleteRow(document.getElementById(linha).rowIndex);
   }

   function search_data(){
     var x=document.cadastro;
     var unidade  = x.unidade.value;
     requestInfo('showTableLista.php?mode=display&unidade='+unidade+'&excluir=<?php echo $exclusao_perfil;?>','showTableLista','', 'salvar');
   }

   function salvar_unidade_has_aplicacao(){
     var f = document.cadastro;
     var unidade = f.unidade.value.split("|");
     var id_unidade=unidade[0];
     var itens=document.getElementById("tabela");
     var total_linhas=itens.rows.length;
     var id_aplicacao="";
     for(var i=0; i<total_linhas; i++){
       id_aplicacao+=itens.rows[i].id + "|";
     }
     id_aplicacao=id_aplicacao.substr(0, (id_aplicacao.length-1));
     var aplicacao = id_aplicacao;
     var url = "../../xml/salvar_unidade_has_aplicacao.php?unidade="+id_unidade+"&aplicacao="+aplicacao;
     requisicaoHTTP("GET", url, true, '');
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
     x.aplicacao.selectedIndex = 0;
     x.aplicacao.disabled=true;
     x.salvar.disabled=true;
     removerItens();
   }

   function removerItens(){
     var itens=document.getElementById("tabela");
     var total_linhas=itens.rows.length;
     for(var i=total_linhas-1; i>=0; i--){
       excluir_linha(itens.rows[i].id);
     }
   }

   function carregarUnidadeAplicacao(){
     var x=document.cadastro;
     if(x.unidade.value!=""){
       <?php
         if("$inclusao_perfil"!=""){
       ?>
           x.aplicacao.disabled=false;
       <?php
         }
       ?>
       search_data();
     }
     else{
       x.salvar.disabled=true;
       x.aplicacao.disabled=true;
       removerItens();
     }
   }

   function inserir_linha(){
     var x=document.cadastro;
     var achou=false;
     var valores_unidade=x.unidade.value.split("|");
     var valores_aplicacao=x.aplicacao.value.split("|");

     var itens=document.getElementById("tabela");
     var total_linhas=itens.rows.length;
     for(var i=0; i<total_linhas; i++){
       var valor=itens.rows[i].id;
       if(valor==valores_aplicacao[0]){
         achou=true;
       }
     }
     if(achou==true){
       window.alert("Aplicação já adicionada!");
       x.aplicacao.selectedIndex=0;
       x.aplicacao.focus();
       return false;
     }
     else{
       var tab=itens.insertRow(total_linhas);
       tab.id=valores_aplicacao[0];
       tab.className="campo_tabela";
       //descricao unidade
       var cel0=tab.insertCell(0);
       cel0.align="left";
       cel0.width="30%";
       cel0.innerHTML=valores_unidade[1];
       //descricao aplicacao
       var cel1=tab.insertCell(1);
       cel1.align="left";
       cel1.width="30%";
       cel1.innerHTML=valores_aplicacao[1];
       var cel2=tab.insertCell(2);
       cel2.align="center";
       cel2.width="3%";
       <?php
         if("$exclusao_perfil"!=""){
       ?>
       var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
       var urlRemover="javascript:excluir_linha(" + valores_aplicacao[0] + ")";
       cel2.innerHTML=linkRemover.link(urlRemover);
       <?php
         }
       ?>
       x.aplicacao.selectedIndex=0;
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

  </script>

  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
     <tr><td> <?php echo $caminho;?> </td></tr>
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
                <select name="unidade" style="width:400px;" onChange="carregarUnidadeAplicacao();">
                  <option value="">Selecione uma unidade</option>
                   <?php
                     $sql="select id_unidade, nome
                           from unidade
                           where flg_nivel_superior <> '1' and status_2 = 'A' order by nome";
                     $nivel = mysqli_query($db, $sql);
                     erro_sql("Select Unidade Superior", $db, "");
                     while($lista_nivel=mysqli_fetch_object($nivel)){
                   ?>
                       <option value="<?php echo $lista_nivel->id_unidade . '|'. $lista_nivel->nome;?>"><?php echo $lista_nivel->nome; ?></option>
                   <?php
                     }
                   ?>
                </select>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Aplicação
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <select name="aplicacao" style="width:400px;" onchange="inserir_linha();" disabled>
                  <option value="">Selecione uma aplicação</option>
                   <?php
                     $sql="select descricao, id_aplicacao
                           from aplicacao
                           where status_2 = 'A' and mostrar_resp_ope='S' order by descricao";
                     $nivel = mysqli_query($db, $sql);
                     erro_sql("", $db, "");
                     while($lista_nivel=mysqli_fetch_object($nivel)){
                   ?>
                       <option value="<?php echo $lista_nivel->id_aplicacao . '|' . $lista_nivel->descricao; ?>"><?php echo $lista_nivel->descricao; ?> </option>
                   <?php
                     }
                   ?>
                </select>
              </td>
            </tr>

         	<TR>
	          <TD colspan="4">
     			<table width="100%" cellpadding="0" cellspacing="1" >
                  <TR align="center" class="coluna_tabela">
					<TD width='30%' align='center'>Unidade</TD>
					<TD width='30%' align='center'>Aplicação</TD>
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
                <input style="font-size: 12px;" type="button" id="salvar" name="salvar" name="salvar" value="Salvar >>" onClick="if(enviar()){salvar_unidade_has_aplicacao();}" disabled>
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
