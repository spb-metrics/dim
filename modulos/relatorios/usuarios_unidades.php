<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2011             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: usuarios_unidades.php                                     |
// | Autor ..............: Leon Watanabe <leon.watanabe@ima.sp.gov.br>               |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de argumentos do Relatório Usuários por Unidade      |
// | Data de Criação ....: 25/03/2011                                                |
// | Última Atualização .: 28/03/2011                                                |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";


$desabilita = disabled;
$classe01 = "class='descricao_campo_tabela_hidden'";

 if ($_REQUEST["operacao"] == '0')
 {
   $desabilita = disabled;
   $classe01 = "class='descricao_campo_tabela_hidden'";
   //$classe02 = "class='campo_tabela_hidden'";
 }
 else if ($_REQUEST["operacao"] <> '')
 {
   $desabilita = '';
   $classe01 = "class='descricao_campo_tabela'";
   //$classe02 = "class='campo_tabela'";
 }

?>

<html>
<head>
 <script language="javascript" type="text/javascript" src="../../scripts/combo.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">

 <!--

 
 
function popup_usuario()
{
	var height = 350;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;

	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_usuario.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameUsuario(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_usuario.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

function SetNameUsuario(argumentos)
{
    var valores = argumentos.split('|');
    document.form_argumentos.usuario.value = valores[0];
    document.form_argumentos.usuario01.value = valores[1];
}

function exportar01()
{
 document.form_argumentos.action = "relatorio_usu_unid_csv.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

function exportar02()
{
 document.form_argumentos.action = "relatorio_usu_unid_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();

 return false;
}

function atualiza()
{
  document.form_argumentos.action = '<? echo $PHP_SELF; ?>?aplicacao=<?=$_GET['aplicacao']?>';
  document.form_argumentos.method = "POST";
  document.form_argumentos.target = "_self";
  document.form_argumentos.submit();
}
    function validarCampos(){
      //sem dados a validar
      return true;
    }
 -->
 </script>
</head>
<body>
<?
$sql = "select im01.descricao as menu_sec, im02.descricao as menu_pri
        from item_menu im01
             inner join item_menu im02 on im02.id_item_menu = im01.item_menu_id_item_menu
        where im01.aplicacao_id_aplicacao = $aplicacao";
$sql_query = mysqli_query($db, $sql);
erro_sql("Aplicação", $db, "");
echo mysqli_error($db);
if (mysqli_num_rows($sql_query) > 0)
{
  $linha = mysqli_fetch_array($sql_query);
  $menu_pri = $linha['menu_pri'];
  $menu_sec = $linha['menu_sec'];
}

$sql = "select id_unidade, nome from unidade where flg_nivel_superior=0 and id_unidade=$_SESSION[id_unidade_sistema]";
$sql_inicial = mysqli_query($db, $sql);
erro_sql("Unidade Inicial", $db, "");
echo mysqli_error($db);
$nome_inicial="";
$id_inicial="";
if (mysqli_num_rows($sql_inicial) > 0)
{
   $inicial = mysqli_fetch_object($sql_inicial);
   $nome_inicial = $inicial->nome;
   $id_inicial= $inicial->id_unidade;
}
?>
    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
            <tr><td> <? echo $caminho; ?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='1' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_argumentos" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao; ?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Unidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">                              
							<input type="hidden" name="id_unidade_sistema" id="id_unidade_sistema" value="<?=$_SESSION[id_unidade_sistema]?>">
                             <input type="hidden" name="unidade" id="unidade" value="<?if($id_inicial!='')echo $id_inicial;?>">
                            
                            <? if($id_inicial!=''){ ?>
                                 <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus=" nextfield ='operacao'" value="<?=$nome_inicial?>" disabled>
                            <? }
                              else
                              {
                            ?>
                               <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus=" nextfield ='operacao'">
                            <?} ?>
							
							
                            <input type="hidden" name="unidade02" id="unidade02" value="<?=$_SESSION[nome_unidade_sistema]?>">
                            <div id="acDivU"></div>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Perfil
                      </td>
                      <td class="campo_tabela"  colspan="3" valign="middle" width="30%">
                        <select name="operacao" style="width: 250px" onFocus="nextfield ='descricao'" onChange="carregarCombo(this.value, '../../xml/movimento_ajax.php', 'lista_descricao', 'opcao_tipo', 'descricao');">
                        <?
                          echo "<option selected value='0;TODOS'>Selecione um Perfil</option>";
                          $sql = "select id_perfil,descricao
                                  from perfil where status_2 = 'A'";

                          $sql_query = mysqli_query($db, $sql);
                          erro_sql("Perfil", $db, "");
                          echo mysqli_error($db);
	                      while ($linha = mysqli_fetch_array($sql_query))
                          {
                            $desc_op = strtoupper($linha['descricao']);
							$id_op = $linha['id_perfil'];
                            ?>
                            <option value="<?=$id_op.';'.$desc_op?>" <?=($operacao==$id_op)?"selected":""?>><?=$desc_op?></option>
                        <?
                          }
                        ?>
                        </select>
                      </td>

                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Usuário
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                        <input type="hidden" name="usuario" id="usuario" value="<?=$usuario?>">
                        <input type="text" name="usuario01" id="usuario01" style="width: 500px" onFocus="nextfield ='ordem'" onchange="if (this.value == ''){ document.form_argumentos.usuario.value = '';}" value="<?=$usuario01?>">
                        <div id="acDiv"></div>
						<A HREF=JavaScript:window.popup_usuario();><IMG src="<?php echo URL;?>/imagens/i_002.gif" name="imagem_paciente" border="0" title="Pesquisar"></a>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" id="csv" value="  Exportar CSV  " onFocus=" nextfield ='pdf'" onClick="if(validarCampos()){exportar01();} atualiza();">
                        <input type="button" style="font-size: 12px;" name="pdf" id="pdf" value=" Visualizar PDF " onFocus=" nextfield ='data_in'"  onClick="if(validarCampos()){exportar02();} atualiza();" >
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="aplicacao" value="<?=$_GET['aplicacao']?>">
                    <input type="hidden" name="nome_und" value="<?=$_SESSION['nome_unidade_sistema']?>">
                    <input type="hidden" name="codigos" value="<?=$codigos?>">
                  </form>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
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
      
      #acDivU{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivU UL{ list-style:none; margin: 0; padding: 0; }
      #acDivU UL LI{ display:block;}
      #acDivU A{ color:#000000; text-decoration:none; }
      #acDivU A:hover{ color:#000000; }
      #acDivU LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>
    <script language="javascript" type="text/javascript" src="../../scripts/dmsAutoComplete.js"></script>
    <script language="JavaScript" type="text/javascript">
    <!--
      //Instanciar objeto AutoComplete Unidade
      var AC = new dmsAutoComplete('unidade01','acDivU', 'id_unidade_sistema', 'unidade', 'unidadesuperior');

      AC.ajaxTarget = '../../xml/dmsUnidade_UnidadesSuperior.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       AC.chooseFunc = function(id,label){
         document.form_argumentos.usuario01.focus();
         document.form_argumentos.unidade.value = id;
       }
       teclaTab('data_fn','ordem');
       
      //Instanciar objeto AutoComplete usuario
      var ACM = new dmsAutoComplete('usuario01','acDiv');

      ACM.ajaxTarget = '../../xml/dmsUsuario.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       ACM.chooseFunc = function(id,label){
         document.form_argumentos.usuario.value = id;
       }
   //-->
   </script>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
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
</body>
</html>
