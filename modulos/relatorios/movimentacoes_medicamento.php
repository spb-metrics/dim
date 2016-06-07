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
// | Arquivo ............: medicamentos_vencidos.php                                 |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de argumentos do Relatório Medicamentos Vencidos     |
// | Data de Criação ....: 09/01/2007 - 13:00                                        |
// | Última Atualização .: 16/03/2007 - 10:30                                        |
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

    function obterData(){
      $date=new DateTime(date("Y-m-d"));
      $date->modify("day");
      return $date->format("d/m/Y");
    }
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
 document.form_argumentos.action = "relatorio_mov_med_csv.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

function exportar02()
{
 document.form_argumentos.action = "relatorio_mov_med_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}
    function validarCampos(){
      var x=document.form_argumentos;
      if(x.data_in.value==""){
        window.alert("Favor preencher os campos obrigatórios!");
        x.data_in.focus();
        return false;
      }
      if(x.data_fn.value==""){
        window.alert("Favor preencher os campos obrigatórios!");
        x.data_fn.focus();
        return false;
      }
      var date=new Date();
      var ano=date.getFullYear();
      var mes=date.getMonth()+1;
      var dia=date.getDate();
      var ano_fim=x.data_fn.value.substring(6, 10);
      var mes_fim=x.data_fn.value.substring(3, 5);
      var dia_fim=x.data_fn.value.substring(0, 2);
      var ano_inicio_aux=x.data_in_aux.value.substring(6, 10);
      var mes_inicio_aux=x.data_in_aux.value.substring(3, 5);
      var dia_inicio_aux=x.data_in_aux.value.substring(0, 2);
      var ano_inicio=x.data_in.value.substring(6, 10);
      var mes_inicio=x.data_in.value.substring(3, 5);
      var dia_inicio=x.data_in.value.substring(0, 2);
      if(ano_inicio<ano_inicio_aux){
        window.alert("Data Início tem que ser Maior ou Igual a " + x.data_in_aux.value + "!");
        x.data_in.focus();
        return false;
      }
      else{
        if(ano_inicio==ano_inicio_aux){
          if(mes_inicio<mes_inicio_aux){
            window.alert("Data Início tem que ser Maior ou Igual a " + x.data_in_aux.value + "!");
            x.data_in.focus();
            return false;
          }
          else{
            if(mes_inicio==mes_inicio_aux && dia_inicio<dia_inicio_aux){
              window.alert("Data Início tem que ser Maior ou Igual a " + x.data_in_aux.value + "!");
              x.data_in.focus();
              return false;
            }
          }
        }
      }
      if(ano_fim>ano){
        window.alert("Data Final tem que ser Menor que a Data Atual!");
        x.data_fn.focus();
        return false;
      }
      else{
        if(ano_fim==ano){
          if(mes_fim>mes){
            window.alert("Data Final tem que ser Menor que a Data Atual!");
            x.data_fn.focus();
            return false;
          }
          else{
            if(mes_fim==mes && dia_fim>dia){
              window.alert("Data Final tem que ser Menor que a Data Atual!");
              x.data_fn.focus();
              return false;
            }
          }
        }
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
      if(x.unidade01.disabled==false && x.unidade.value==""){
        window.alert("Favor preencher os campos obrigatórios!");
        x.unidade01.focus();
        return false;
      }
      if(x.medicamento.value==""){
        window.alert("Favor preencher os campos obrigatórios!");
        x.medicamento01.focus();
        return false;
      }
      return true;
    }
 -->
 </script>
</head>
<body>
    <? if ($db == true)
       {
        $sql = "select dt_inicio_extrato from parametro";
        $sql = mysqli_query($db, $sql);
        $result = mysqli_fetch_array($sql);
        $data_inicio = $result['dt_inicio_extrato'];
        //return $date->format("d/m/Y");
        $parte = explode("-", $data_inicio);
        $data= $parte[2]."/".$parte[1]."/".$parte[0];
        if (($parte[0]=='0')&&($parte[1]=='0')&&($parte[2]=='0')){
          echo "<script>window.alert('Data para iniciar o extrato não foi informada no parâmetro do sistema!');</script>";
          echo "<script>window.location='" . URL . "/start.php';</script>";
          exit();
        }
       }
    ?>
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
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Inicio
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="data_in" size="15" value="<?php echo $data;?>" style="width: 80px" onFocus=" nextfield ='data_fn'" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);">
                        <input type="hidden" name="data_in_aux" size="15" value="<?php echo $data;?>"
                        <script>
                        <!--
                          document.form_argumentos.data_in.focus();
                        //-->
                        </script>

                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Fim
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="data_fn" size="15" value="<?php echo obterData();?>" style="width: 80px" onblur="verificaData(this,this.value);" onFocus=" nextfield ='unidade01'" onKeyPress="return mascara_data(event,this);">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Unidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">
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
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Medicamento
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">
                        <input type="hidden" name="medicamento" id="medicamento" value="<?=$medicamento?>">
                        <input type="text" name="medicamento01" id="medicamento01" style="width: 500px" onFocus="nextfield ='csv';" onchange="if (this.value == ''){ document.form_argumentos.medicamento.value = '';}" value="<?=$medicamento01?>">
                        <div id="acDiv"></div>
                        <A HREF=JavaScript:window.popup_medicamento();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_medicamento" border="0" title="Pesquisar"></a>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" id="csv" value="  Exportar CSV  " onFocus=" nextfield ='pdf'" onClick="if(validarCampos()){exportar01();}">
                        <input type="button" style="font-size: 12px;" name="pdf" id="pdf" value=" Visualizar PDF " onFocus=" nextfield ='data_in'" onClick="if(validarCampos()){exportar02();}">
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
      //Instanciar objeto AutoComplete Medicamento
      var ACM = new dmsAutoComplete('medicamento01','acDiv');

      ACM.ajaxTarget = '../../xml/dmsMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       ACM.chooseFunc = function(id,label){
         document.form_argumentos.medicamento.value = id;
       }
      teclaTab('data_fn','medicamento01');
      
      //Instanciar objeto AutoComplete Unidade
      var AC = new dmsAutoComplete('unidade01','acDivU', 'id_unidade_sistema', 'unidade', 'unidadesuperior');

      AC.ajaxTarget = '../../xml/dmsUnidade_UnidadesSuperior.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       AC.chooseFunc = function(id,label){
         document.form_argumentos.medicamento01.focus();
         document.form_argumentos.unidade.value = id;
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
