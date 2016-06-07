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
// | Arquivo ............: consolidacao_informacao.php                               |
// | Autor ..............: Fábio Hitoshi Ide <hitoshi.ide@ima.sp.gov.br>             |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de argumentos do Relatório Consolidação Informação   |
// | Data de Criação ....: 27/05/2009                                                |
// | Última Atualização .: 27/05/2009                                                |
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

function subtrai_data($pData, $pDias)//formato BR
{
  if(ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $pData, $vetData))
  {
    $fDia = $vetData[1];
    $fMes = $vetData[2];
    $fAno = $vetData[3];

    for($x = 1; $x <= $pDias; $x++)
    {
      $fDia--;
      if($fDia < 1)
      {
        if($fMes == 1)
        {
          $fAno--;
          $fMes = 12;
          $fDia = 31;
        }
        else
        {
          $fMes--;
          if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12)
          {
            $fMaxDia = 31;
          }
          elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11)
          {
            $fMaxDia = 30;
          }
          else
          {
            if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0)
            {
              $fMaxDia = 29;
            }
            elseif($fMes == 2)
            {
              $fMaxDia = 28;
            }
          }
          $fDia = $fMaxDia;
        }
      }
    }
    if(strlen($fDia) == 1)
      $fDia = "0" . $fDia;
    if(strlen($fMes) == 1)
      $fMes = "0" . $fMes;
    return "$fDia/$fMes/$fAno";
  }
}
?>

<html>
<head>
 <script language="javascript" type="text/javascript" src="../../scripts/combo.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">

 <!--
function exportar01()
{
 document.form_argumentos.action = "relatorio_cons_inf_csv.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

function exportar02()
{
 document.form_argumentos.action = "relatorio_cons_inf_pdf.php";
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
  document.form_argumentos.operacao.selectedIndex=0;
  
  document.form_argumentos.submit();
  
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
      if(!validarMesAno(x.data_in)){
        alert ("A data fornecida foi preenchida incorretamente.");
        x.data_in.focus();
        x.data_in.select();
        return false;
      }
      if(!validarMesAno(x.data_fn)){
        alert ("A data fornecida foi preenchida incorretamente.");
        x.data_fn.focus();
        x.data_fn.select();
        return false;
      }
      var data_inicial=x.data_in.value.split("/");
      var data_final=x.data_fn.value.split("/");
      var hoje=new Date();
      if(data_final[1]>hoje.getFullYear()){
        window.alert("Data Fim deve ser menor que a Data Atual!");
        x.data_fn.focus();
        return false;
      }
      else{
        if(data_final[1]==hoje.getFullYear() && data_final[0]>(hoje.getMonth()+1)){
          window.alert("Data Fim deve ser menor que a Data Atual!");
          x.data_fn.focus();
          return false;
        }
      }
      if(data_final[1]<data_inicial[1]){
        window.alert("Data Fim deve ser maior ou igual a Data Início!");
        x.data_fn.focus();
        x.data_fn.select();
        return false;
      }
      else{
        if(data_final[1]==data_inicial[1]){
          if(data_final[0]<data_inicial[0]){
            window.alert("Data Fim deve ser maior ou igual a Data Início!");
            x.data_fn.focus();
            x.data_fn.select();
            return false;
          }
        }
      }
      if((data_final[1]-data_inicial[1])>1){
        window.alert("Intervalo Data Início/Fim deve ser de no máximo 12 meses!");
        x.data_fn.focus();
        x.data_fn.select();
        return false;
      }
      else{
        if((data_final[1]-data_inicial[1])==1 && data_final[0]>=data_inicial[0]){
          window.alert("Intervalo Data Início/Fim deve ser de no máximo 12 meses!");
          x.data_fn.focus();
          x.data_fn.select();
          return false;
        }
      }
      if(x.operacao.selectedIndex==0){
        window.alert("Preencher Campos Obrigatórios!");
        x.operacao.focus();
        return false;
      }
      return true;
    }

function mascara_mes_ano(e,ConteudoCampo)
{

   var charCode = (e.which) ? e.which : e.keyCode

   //if ((charCode < 48 || charCode >57 && charCode < 96 || charCode > 105) && (charCode >= 32))
   if ((charCode >= 47 && charCode <= 57) || charCode==8 || charCode==9 || charCode==46 || charCode==37 || charCode==39)
   {
     var valor = (window.Event) ? e.which : e.keyCode;
     if (valor != 8)
     {
	   NumDig = ConteudoCampo.value;
	   TamDig = NumDig.length;

       if (TamDig == 2)
         ConteudoCampo.value = NumDig.substr(0,2)+"/";
       else if (TamDig == 5)
     	 ConteudoCampo.value = NumDig.substr(0,7);
     }//end if valor != 8
     return true;
   }
   else
   {
     return false;
   }
}

function validarMesAno(campo){
  if(campo.value!=""){
    var data=campo.value;
    var erro=false;
    var mes, ano;
    mes=data.substring(0,2);
    ano=data.substring(3,data.length);
    if(data.charAt(2)=="/" &&
       (!isNaN(mes) && mes>=1 && mes<=12) &&
       (!isNaN(ano) && ano.length==4)){
      erro=true;
    }
    return erro;
  }
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
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Inicio
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_in" size="15" style="width: 80px" onFocus=" nextfield ='data_fn'" value="<?if ($_POST[data_in]){echo $data_in;} else{echo date("m/Y");}?>" onKeyPress="return mascara_mes_ano(event,this);">
                        <?
                        if ($unidade == "")
                        {
                        ?>
                          <script>
                            document.form_argumentos.data_in.focus();
                          </script>
                        <?
                        }
                        ?>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Fim
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_fn" size="15" style="width: 80px" value="<?if ($_POST[data_fn]){echo $data_fn;} else{echo date("m/Y");}?>" onFocus=" nextfield ='unidade01'" onKeyPress="return mascara_mes_ano(event,this);" value="<?=$data_fn?>">
                      </td>
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
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Tipo de Movimento
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="operacao" style="width: 220px" onFocus="nextfield ='descricao'" onChange="carregarCombo(this.value, '../../xml/movimento_ajax.php', 'lista_descricao', 'opcao_tipo', 'descricao');">
                        <?
                          //echo "<option selected value='$id_termo'>$desc_termo</option>";
                          echo "<option selected value='0'>Selecione um Tipo de Movimento</option>";
						 
                          $sql = "select distinct operacao
                                  from tipo_movto";
                          //echo $sql;
                          $sql_query = mysqli_query($db, $sql);
                          erro_sql("Tipo Movimento", $db, "");
                          echo mysqli_error($db);
	                      while ($linha = mysqli_fetch_array($sql_query))
                          {
                            $desc_op = strtoupper($linha['operacao']);
                            ?>
                            <option value="<?=$desc_op?>" <?=($operacao==$desc_op)?"selected":""?>><?=$desc_op?></option>
                        <?
                          }
                        ?>
                        </select>
                      </td>

                      <td <?=$classe01?> valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Descrição
                      </td>
                      <td class='campo_tabela' valign="middle" width="30%">
                        <select name="descricao" id="descricao" onFocus="nextfield ='medicamento01'">
                          <option id="opcao_tipo" value="0"> Primeiro Selecione um Movimento </option>
                        </select>
                      

                        <?
                        if ($_REQUEST['operacao'] <> '')
	                    {
                        ?>
                          <script>
                            var x=document.form_argumentos;
                            if(x.operacao.selectedIndex!=0){
                              x.descricao.focus();
                            }
                          </script>
                        <?
                        }
                        ?>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" id="csv" value="  Exportar CSV  " onFocus=" nextfield ='pdf'" onClick="if(validarCampos()){exportar01(); }atualiza();">
                        <input type="button" style="font-size: 12px;" name="pdf" id="pdf" value=" Visualizar PDF " onFocus=" nextfield ='data_in'"  onClick="if(validarCampos()){exportar02();}atualiza();">


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
         document.form_argumentos.operacao.focus();
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
