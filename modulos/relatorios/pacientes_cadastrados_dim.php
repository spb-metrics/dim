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
// | Arquivo ............: pacientes_cadastrados_dim.php                             |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Tela de argumentos do Relat�rio Pacientes Cadastrados DIM |
// | Data de Cria��o ....: 23/01/2007 - 09:15                                        |
// | �ltima Atualiza��o .: 16/02/2007 - 13:50                                        |
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
function exportar01()
{
 document.form_argumentos.action = "relatorio_pac_cad_dim_csv.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

function exportar02()
{
 document.form_argumentos.action = "relatorio_pac_cad_dim_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
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
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao; ?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Inicio
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_in" size="15" style="width: 80px" value="<?=date("d/m/Y")?>" onFocus=" nextfield ='data_fn'" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);" value="<?=$data_in?>">
                        <script>
                          document.form_argumentos.data_in.focus();
                        </script>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Fim
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_fn" size="15" style="width: 80px" value="<?=date("d/m/Y")?>" onFocus=" nextfield ='unidade01'" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);" value="<?=$data_fn?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Unidade de Cadastro
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">
                            <input type="hidden" name="id_unidade_sistema" id="id_unidade_sistema" value="<?=$_SESSION[id_unidade_sistema]?>">
                            <input type="hidden" name="unidade" id="unidade" value="<?if($id_inicial!='')echo $id_inicial;?>">
                            <? if($id_inicial!=''){ ?>
                                 <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus=" nextfield ='ordem'" value="<?=$nome_inicial?>" disabled>
                            <? }
                              else
                              {
                            ?>
                               <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" onFocus=" nextfield ='ordem'">
                            <?} ?>
                            <input type="hidden" name="unidade02" id="unidade02" value="<?=$_SESSION[nome_unidade_sistema]?>">
                            <div id="acDivU"></div>
                      </td>
                    </tr>
                    <tr height="21">
                      <td class="descricao_campo_tabela" valign="center" width="20%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Ordenado por
                      </td>
                      <td class="campo_tabela" valign="center" width="80%" colspan="3" height="21" value="<?=$ordem?>">
                        <select size="1" name="ordem">
                          <option value='0' <?=($ordem == '2')?"selected":""?>>Data Nascimento</option>
                          <option value='1' <?=($ordem == '1')?"selected":""?>>Nome da M�e</option>
                          <option value='2' <?=($ordem == '0')?"selected":""?>>Paciente</option>
                          <option value='3' <?=($ordem == '3')?"selected":""?>>Sexo</option>
                          <option value='4' <?=($ordem == '4')?"selected":""?>>Status</option>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" id="csv" onFocus=" nextfield ='pdf'" value="  Exportar CSV  " onClick="if(validarCampos()){exportar01();}">
                        <input type="button" style="font-size: 12px;" name="csv" id="pdf" onFocus=" nextfield ='data_in'" value=" Visualizar PDF " onClick="if(validarCampos()){exportar02();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21">
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
      //Definir fun��o de retorno
      //Esta fun��o ser� executada ao se escolher a palavra
       AC.chooseFunc = function(id,label){
         document.form_argumentos.medicamento01.focus();
         document.form_argumentos.unidade.value = id;
       }

       teclaTab('data_fn','ordem');
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
