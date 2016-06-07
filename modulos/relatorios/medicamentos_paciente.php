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
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: medicamentos_paciente.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de argumentos do Relatório Medicamentos por Paciente |
// | Data de Criação ....: 15/01/2007 - 15:50                                        |
// | Última Atualização .: 15/03/2007 - 09:15                                        |
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

?>

<html>
<head>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">
 <!--
function popup_paciente()
{
	var height = 500;
	var width = 900;

	var left = ((screen.availWidth - width))/2;
	var top = (screen.availHeight - height)/2;
    var caminho = "pesquisa_paciente.php";
     if (window.showModalDialog)
	 {
		var dialogArguments = new Object();
		var _R = window.showModalDialog(caminho, dialogArguments, "dialogWidth=900px;dialogHeight=500px;dialogTop=100px;dialogLeft=60px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNamePaciente(_R.strArgs);
		}
	 }
	 else	//NS
	 {
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open(caminho, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
  	 }
}


function popup_medicamento()
{
	var height = 350;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;

	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_material.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogTop=250px;dialogLeft=290px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameMedicamento(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_material.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
}

function SetNamePaciente(argumentos)
{
   if(argumentos != 'limpar')
   {
     var valores = argumentos.split(';');
     document.form_argumentos.paciente.value = valores[0];
     document.form_argumentos.paciente02.value = valores[1];
     document.form_argumentos.paciente01.value = valores[1];
     document.form_argumentos.medicamento01.focus();
   }

}

function SetNameMedicamento(argumentos)
{
    var valores = argumentos.split('|');
    document.form_argumentos.medicamento.value = valores[0];
    document.form_argumentos.medicamento01.value = valores[1];
}

function exportar01()
{
 document.form_argumentos.action = "relatorio_med_pac_csv.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

function exportar02()
{
 document.form_argumentos.action = "relatorio_med_pac_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}
    function validarCampos(){
      var x=document.form_argumentos;
      if(x.data_in.value==""){
        window.alert("Preencher Campos Obrigatórios!");
        x.data_in.focus();
        return false;
      }
      if(x.data_fn.value==""){
        window.alert("Preencher Campos Obrigatórios!");
        x.data_fn.focus();
        return false;
      }
      var data_inicial=x.data_in.value.split("/");
      var data_final=x.data_fn.value.split("/");
      if(data_final[2]<data_inicial[2]){
        window.alert("Data Fim deve ser maior ou igual a Data Início!");
        x.data_fn.focus();
        return false;
      }
      else{
        if(data_final[2]==data_inicial[2]){
          if(data_final[1]<data_inicial[1]){
            window.alert("Data Fim deve ser maior ou igual a Data Início!");
            x.data_fn.focus();
            return false;
          }
          else{
            if(data_final[1]==data_inicial[1] && data_final[0]<data_inicial[0]){
              window.alert("Data Fim deve ser maior ou igual a Data Início!");
              x.data_fn.focus();
              return false;
            }
          }
        }
      }

      if(x.paciente.value=="" || x.paciente01.value==""){
        window.alert("Preencher Campo Paciente!");
        return false;
      }
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
erro_sql("Select Aplicação", $db, "");
echo mysqli_error($db);
if (mysqli_num_rows($sql_query) > 0)
{
  $linha = mysqli_fetch_array($sql_query);
  $menu_pri = $linha['menu_pri'];
  $menu_sec = $linha['menu_sec'];
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
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Inicio
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_in" size="15" style="width: 80px" value="<?=date("d/m/Y")?>" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);">
                        <script>
                          document.form_argumentos.data_in.focus();
                        </script>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Fim
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_fn" size="15" style="width: 80px" value="<?=date("d/m/Y")?>" onblur="verificaData(this,this.value);" onFocus=" nextfield ='unidade01'" onKeyPress="return mascara_data(event,this);">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Unidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="80%">
                        <input type="hidden" name="unidade" id="unidade" value="<?=$unidade?>">
                        <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus="nextfield ='medicamento01';" onchange="if (this.value == ''){ document.form_argumentos.unidade.value = '';}" value="<?if ($_POST[unidade01]){echo ($_POST[unidade01]);} else{echo "";}?>">
                        <div id="acDivU"></div>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Paciente
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                        <input type="hidden" name="paciente" id="paciente" value="<?=$paciente?>">
                        <input type="hidden" name="paciente01" id="paciente01" style="width: 500px">
                        <input type="text" name="paciente02" id="paciente02" style="width: 500px" disabled>
                        <input type="hidden" name="status" id="status" value="<?=$status?>">

                        <A HREF=JavaScript:window.popup_paciente();><IMG src="<?php echo URL;?>/imagens/i_002.gif" name="imagem_paciente" border="0" title="Pesquisar"></a>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Medicamento
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                        <input type="hidden" name="medicamento" id="medicamento" value="<?=$medicamento?>">
                        <input type="text" name="medicamento01" id="medicamento01" style="width: 500px" onFocus="nextfield ='ordem';" onchange="if (this.value == ''){ document.form_argumentos.medicamento.value = '';}" value="<?=$medicamento01?>">
                        <div id="acDivM"></div>
                        <A HREF=JavaScript:window.popup_medicamento();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_medicamento" border="0" title="Pesquisar"></a>

                      </td>
                    </tr>
                    <tr height="21">
                      <td class="descricao_campo_tabela" valign="center" width="20%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Ordenado por
                      </td>
                      <td class="campo_tabela" valign="center" width="80%" colspan="3" height="21">
                        <select size="1" name="ordem">
                          <option value='0' <?=($ordem == '0')?"selected":""?>>Data Retirada</option>
                          <option value='1' <?=($ordem == '1')?"selected":""?>>Fabricante</option>
                          <option value='2' <?=($ordem == '2')?"selected":""?>>Lote</option>
                          <option value='3' <?=($ordem == '3')?"selected":""?>>Medicamento</option>
                          <option value='4' <?=($ordem == '4')?"selected":""?>>Número da Receita</option>
                          <!--<option value='4'>Unidade</option>-->
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" value="  Exportar CSV  " onFocus=" nextfield ='pdf'" onClick="if(validarCampos()){exportar01();}">
                        <input type="button" style="font-size: 12px;" name="pdf" value=" Visualizar PDF " onFocus=" nextfield ='data_in'" onClick="if(validarCampos()){exportar02();}">
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
      #acDivU{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivU UL{ list-style:none; margin: 0; padding: 0; }
      #acDivU UL LI{ display:block;}
      #acDivU A{ color:#000000; text-decoration:none; }
      #acDivU A:hover{ color:#000000; }
      #acDivU LI.selected{ background-color:#7d95ae; color:#000000; }
      
      /* Definição dos estilos do DIV */
      /* CSS for the DIV */
      #acDivM{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivM UL{ list-style:none; margin: 0; padding: 0; }
      #acDivM UL LI{ display:block;}
      #acDivM A{ color:#000000; text-decoration:none; }
      #acDivM A:hover{ color:#000000; }
      #acDivM LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>
    <script language="javascript" type="text/javascript" src="../../scripts/dmsAutoComplete.js"></script>
    <script language="JavaScript" type="text/javascript">
      <!--


      //Instanciar objeto AutoComplete Unidade
      var AC = new dmsAutoComplete('unidade01','acDivU');

      AC.ajaxTarget = '../../xml/dmsUnidade.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       AC.chooseFunc = function(id,label){
          document.form_argumentos.unidade.value = id;
       }
       teclaTab('data_fn','ordem');

      //Instanciar objeto AutoComplete Medicamento
      var ACM = new dmsAutoComplete('medicamento01','acDivM');

      ACM.ajaxTarget = '../../xml/dmsMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       ACM.chooseFunc = function(id,label){
         document.form_argumentos.medicamento.value = id;
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
