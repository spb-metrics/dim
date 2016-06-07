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
// | Arquivo ............: recibo_receita.php                                        |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de pesquisa do Recibo da Receita                     |
// | Data de Criação ....: 28/01/2007 - 16:00                                        |
// | Última Atualização .: 22/02/2007 - 18:10                                        |
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

    require "../../verifica_acesso.php";

?>

<html>
<head>
 <script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">
 <!--
function exportar01()
{
 document.form_argumentos.action = "recibo_receita_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

  function exportar02()
 {
   var url;

   url = "recibo_receita_pdf.php?ano=" + escape(document.form_argumentos.ano.value)
         + "&numero=" + escape(document.form_argumentos.numero.value)
         + "&unidade=" + escape(document.form_argumentos.unidade.value);
   window.open(url,target="_blank");
  }
  function validarCampos(){
    var x=document.form_argumentos;
    if(x.ano.value=="" || x.unidade.value=="" || x.numero.value==""){
      window.alert("Preencher os Campos Obrigatórios");
      if(x.ano.value==""){
        x.ano.focus();
        return false;
      }
      else{
        if(x.unidade.value==""){
          x.unidade.focus();
          return false;
        }
        else{
          if(x.numero.value==""){
            x.numero.focus();
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
erro_sql("Aplicação", $db, "");
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
            <tr>
              <td> <? echo $caminho?> </td>
            </tr>
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
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Número da Receita
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%">
                        <input type="text" name="ano" size="4" maxlength="4" style="width: 45px" value="<?=$ano?>" onKeyPress="return isNumberKey(event);" onKeyUp="verifica_saida(this.value, 'unidade', 4, this.form);">
                        <b>&nbsp;-&nbsp;</b>
                        <script>
                          document.form_argumentos.ano.focus();
                        </script>
                        <input type="text" name="unidade" id="unidade" size="4" maxlength="3" style="width: 45px" value="<?=$unidade?>" onKeyPress="return isNumberKey(event);" onKeyUp="verifica_saida(this.value, 'numero', 3, this.form);">
                        <b>&nbsp;-&nbsp;</b>
                        <input type="text" name="numero" id="numero" size="4" maxlength="5" style="width: 45px" value="<?=$numero?>" onKeyPress="return isNumberKey(event);" onKeyUp="verifica_saida(this.value, 'pdf', 5, this.form);">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                          <?php
                            if($consulta_perfil!=""){
                          ?>
                            <input type="button" style="font-size: 12px;" name="pdf" value=" Imprimir " onClick="if(validarCampos()){exportar02();}">
                          <?php
                            }
                            else{
                          ?>
                            <input type="button" style="font-size: 12px;" name="pdf" value=" Imprimir " onClick="exportar02();" disabled>
                          <?php
                            }
                          ?>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                  </form>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
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
