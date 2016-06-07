<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: pacientes_medicamento.php                                 |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Tela de argumentos do Relat�rio Pacientes por Medicamento |
// | Data de Cria��o ....: 18/01/2007 - 14:00                                        |
// | �ltima Atualiza��o .: 16/03/2007 - 13:30                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
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
 <script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">
 <!--
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

function SetNameMedicamento(argumentos)
{
    var valores = argumentos.split('|');
    document.form_argumentos.medicamento.value = valores[0];
    document.form_argumentos.medicamento01.value = valores[1];
}

function exportar01()
{
 var teste_uni = document.form_argumentos.unidade01.value;
 document.form_argumentos.action = "relatorio_pac_med_csv.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
if (teste_uni !=""){
				  document.form_argumentos.submit();
			 }
			 else {		 
				 document.form_argumentos.unidade.value =""; 
				 document.form_argumentos.unidade01.value = ""; 
				 document.form_argumentos.submit();
			 }
 return false;
}

function exportar02()
{
 var teste_uni = document.form_argumentos.unidade.value;
 document.form_argumentos.action = "relatorio_pac_med_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
if (teste_uni !=""){
	//alert('valor');
	// document.form_argumentos.unidade.value =""; 
				  document.form_argumentos.submit();
			 }
			 else {		 
				document.form_argumentos.unidade.value =""; 
				 document.form_argumentos.unidade01.value = ""; 
				 document.form_argumentos.submit();
			 }
 return false;
}

function atualiza()
{
 key = getkey(event);

 if (key==8 || key==13)
 {
   document.form_argumentos.action = '<? echo $PHP_SELF; ?>';
   document.form_argumentos.method = "POST";
   document.form_argumentos.target = "_self";
   document.form_argumentos.submit();
 }
}

function getkey(e)
{
if (window.event)
   return window.event.keyCode;
else if (e)
   return e.which;
else
   return null;
}
    function validarCampos(){
      var x=document.form_argumentos;
      if(x.data_in.value==""){
        window.alert("Preencher Campos Obrigat�rios!");
        x.data_in.focus();
        return false;
      }
      if(x.data_fn.value==""){
        window.alert("Preencher Campos Obrigat�rios!");
        x.data_fn.focus();
        return false;
      }
      var data_inicial=x.data_in.value.split("/");
      var data_final=x.data_fn.value.split("/");
      if(data_final[2]<data_inicial[2]){
        window.alert("Data Fim deve ser maior ou igual a Data In�cio!");
        x.data_fn.focus();
        return false;
      }
      else{
        if(data_final[2]==data_inicial[2]){
          if(data_final[1]<data_inicial[1]){
            window.alert("Data Fim deve ser maior ou igual a Data In�cio!");
            x.data_fn.focus();
            return false;
          }
          else{
            if(data_final[1]==data_inicial[1] && data_final[0]<data_inicial[0]){
              window.alert("Data Fim deve ser maior ou igual a Data In�cio!");
              x.data_fn.focus();
              return false;
            }
          }
        }
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
erro_sql("Aplica��o", $db, "");
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
                      <td colspan="6" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao; ?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Inicio
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="2">
                        <input type="text" name="data_in" size="15" style="width: 80px" value="<?=date("d/m/Y")?>" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);" value="<?=$data_in?>">
                        <script>
                          document.form_argumentos.data_in.focus();
                        </script>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Fim
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="2">
                        <input type="text" name="data_fn" size="15" style="width: 80px" value="<?=date("d/m/Y")?>" onFocus=" nextfield ='unidade01'" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);" value="<?=$data_fn?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Unidade
                      </td>
                      <td class="campo_tabela" colspan="5" valign="middle" width="80%">
                            <input type="hidden" name="id_unidade_sistema" id="id_unidade_sistema" value="<?=$_SESSION[id_unidade_sistema]?>">
                            <input type="hidden" name="unidade" id="unidade" value="<?if($id_inicial!='')echo $id_inicial;?>">
                            <? if($id_inicial!=''){ ?>
                                 <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus=" nextfield ='medicamento01'" value="<?=$nome_inicial?>" disabled>
                            <? }
                              else
                              {
                            ?>
                               <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus=" nextfield ='medicamento01'">
                            <?} ?>
                            <input type="hidden" name="unidade02" id="unidade02" value="<?=$_SESSION[nome_unidade_sistema]?>">
                            <div id="acDivU"></div>

                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Medicamento
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                        <input type="hidden" name="medicamento" id="medicamento" value="<?=$medicamento?>">
                        <input type="text" name="medicamento01" id="medicamento01" style="width: 500px" onFocus="nextfield ='lote01'" onchange="if (this.value == ''){ document.form_argumentos.medicamento.value = '';}" value="<?=$medicamento01?>">
                        <div id="acDivM"></div>
                        <A HREF=JavaScript:window.popup_medicamento();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_medicamento" border="0" title="Pesquisar"></a>

                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Lote
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="2">
                        <input type="hidden" name="lote" id="lote" value="<?=$lote?>">
                        <input type="textBox" name="lote01" id="lote01" style="width: 100px" onFocus="nextfield ='fabricante01'" onchange="document.form_argumentos.lote.value='';">
                        <div id="acDivL"></div>
                      </td>
                      
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Fabricante
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="2">
                        <input type="hidden" name="fabricante" id="fabricante" value="<?=$fabricante?>">
                        <input type="textBox" name="fabricante01" id="fabricante01" style="width: 200px" onFocus="nextfield ='ordem'" onchange="document.form_argumentos.lote.value='';">
                        <div id="acDivF"></div>
                      </td>
                    </tr>
                    <tr height="21">
                      <td class="descricao_campo_tabela" valign="center" width="20%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Ordenado por
                      </td>
                      <td class="campo_tabela" valign="center" width="80%" colspan="5" height="21">
                        <select size="1" name="ordem" id="ordem" onFocus="nextfield ='csv'">
                          <option value='0' <?=($ordem == '0')?"selected":""?>>Data de Retirada</option>
                          <option value='1' <?=($ordem == '1')?"selected":""?>>Fabricante</option>
                          <option value='2' <?=($ordem == '2')?"selected":""?>>Lote</option>
                          <option value='3' <?=($ordem == '3')?"selected":""?>>Paciente</option>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="6" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" value="  Exportar CSV  " onFocus="nextfield ='pdf'" onClick="if(validarCampos()){exportar01();}">
                        <input type="button" style="font-size: 12px;" name="pdf" value=" Visualizar PDF " onFocus="nextfield ='data_in';" onClick="if(validarCampos()){exportar02();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="6" valign="middle" align="center" width="100%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
                      </td>
                    </tr>
                    <input type="hidden" name="aplicacao" value="<?=$_GET['aplicacao']?>">
                    <input type="hidden" name="nome_und" value="<?=$_SESSION[nome_unidade_sistema]?>">
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
      /* Defini��o dos estilos do DIV */
      /* CSS for the DIV */
      #acDivL{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivL UL{ list-style:none; margin: 0; padding: 0; }
      #acDivL UL LI{ display:block;}
      #acDivL A{ color:#000000; text-decoration:none; }
      #acDivL A:hover{ color:#000000; }
      #acDivL LI.selected{ background-color:#7d95ae; color:#000000; }
      
      #acDivU{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivU UL{ list-style:none; margin: 0; padding: 0; }
      #acDivU UL LI{ display:block;}
      #acDivU A{ color:#000000; text-decoration:none; }
      #acDivU A:hover{ color:#000000; }
      #acDivU LI.selected{ background-color:#7d95ae; color:#000000; }
      
      #acDivM{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivM UL{ list-style:none; margin: 0; padding: 0; }
      #acDivM UL LI{ display:block;}
      #acDivM A{ color:#000000; text-decoration:none; }
      #acDivM A:hover{ color:#000000; }
      #acDivM LI.selected{ background-color:#7d95ae; color:#000000; }
      
      #acDivF{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDivF UL{ list-style:none; margin: 0; padding: 0; }
      #acDivF UL LI{ display:block;}
      #acDivF A{ color:#000000; text-decoration:none; }
      #acDivF A:hover{ color:#000000; }
      #acDivF LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>
    <script language="javascript" type="text/javascript" src="../../scripts/dmsAutoComplete.js"></script>
    <script>
    <!--
      //Instanciar objeto AutoComplete
      var AC = new dmsAutoComplete('unidade01','acDivU', 'id_unidade_sistema', 'unidade', 'unidadesuperior');

      AC.ajaxTarget = '../../xml/dmsUnidade_UnidadesSuperior.php';
      //Definir fun��o de retorno
      //Esta fun��o ser� executada ao se escolher a palavra
       AC.chooseFunc = function(id,label){
         document.form_argumentos.medicamento01.focus();
         document.form_argumentos.unidade.value = id;
       }
       teclaTab('data_fn','ordem');
       
       //lote
       var ACL = new dmsAutoComplete('lote01','acDivL');
      ACL.ajaxTarget = '../../xml/dmsLote.php';
      //Definir fun��o de retorno
      //Esta fun��o ser� executada ao se escolher a palavra
       ACL.chooseFunc = function(id,label){
        document.form_argumentos.fabricante01.focus();
         document.form_argumentos.lote.value = id;
       }

       //fabricante
       var ACF = new dmsAutoComplete('fabricante01','acDivF');
      ACF.ajaxTarget = '../../xml/dmsFabricante.php';
      //Definir fun��o de retorno
      //Esta fun��o ser� executada ao se escolher a palavra
       ACF.chooseFunc = function(id,label){
         document.form_argumentos.ordem.focus();
         document.form_argumentos.fabricante.value = id;
       }
       
       //medicamento
       var ACM = new dmsAutoComplete('medicamento01','acDivM');
      ACM.ajaxTarget = '../../xml/dmsMedicamento.php';
      //Definir fun��o de retorno
      //Esta fun��o ser� executada ao se escolher a palavra
       ACM.chooseFunc = function(id,label){
       document.form_argumentos.lote01.focus();
         document.form_argumentos.medicamento.value = id;
       }
  //-->

  </script>
<?php
    ////////////////////
    //RODAP� DA P�GINA//
    ////////////////////
    require DIR."/footer.php";

  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
</body>
</html>
