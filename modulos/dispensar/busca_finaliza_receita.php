<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  
  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

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
 <script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">

  function procura_receita()
  {
   
    var url = "../../xml_dispensacao/procura_receita.php?ano="+document.form_argumentos.ano.value
            +"&numero="+document.form_argumentos.numero.value
            +"&unidade="+document.form_argumentos.unidade.value;
	
		
    requisicaoHTTP("GET", url, true);

	
	

	
	

//alert (receita);
	
	
  }
  
  
  function trataDados()
  {
	var info = ajax.responseText;  // obt�m a resposta como string
    //alert (info);
	
	if (info == 'nao_receita') //retorno de procura_receita.php
	{
	 alert('Receita n�o existe');
	 document.form_argumentos.ano.value = '';
	 document.form_argumentos.numero.value = '';
	 document.form_argumentos.unidade.value = '';
	 document.form_argumentos.ano.focus();

    }
    else
    {
     document.form_argumentos.id_receita.value = info;
	 var receita = document.form_argumentos.id_receita.value;
	 popup_final_receita(receita);
    // document.form_argumentos.submit();
	 
    }
  }
  
  function valida_campos()
  {
  
  	
     if (document.form_argumentos.ano.value == "")
     {
        alert ("Favor preencher os campos obrigat�rios!");
        document.form_argumentos.ano.focus();
        return false;
     }
     
     if (document.form_argumentos.numero.value == "")
     {
        alert ("Favor preencher os campos obrigat�rios!");
        document.form_argumentos.numero.focus();
        return false;
     }

     if (document.form_argumentos.unidade.value == "")
     {
        alert ("Favor preencher os campos obrigat�rios!");
        document.form_argumentos.unidade.focus();
        return false;
     }

     procura_receita();
  }

 function popup_final_receita(receita)
 
{
//alert ('entrou na funcao');
//alert(document.form_argumentos.id_receita.value);

	var height = 500;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	var receita = document.form_argumentos.id_receita.value;
	if (window.showModalDialog)
	{
	
		var dialogArguments = new Object();
		
		var _R = window.showModalDialog("finalizar_receita.php?id_receita="+receita,dialogArguments,"dialogWidth=1000px;dialogHeight=500px;scroll=yes;status=no;");
	//alert ('entrou no if');
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("consulta_receita.php?id_receita="+receita, ID,"modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}
 
 
 
 
 </script>
</head>
<body>
    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
            <tr><td><? echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='1' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                 <!-- <form name="form_argumentos" action="finalizar_receita.php?&id_receita=<?php echo $_POST[id_receita];?>" method="POST" enctype="application/x-www-form-urlencoded">-->
                  <form name="form_argumentos">
                  
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <? echo $menu_sec?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> N�mero da Receita
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%">
                        <!--<input type="text" name="id_receita" value="<?=$id_receita?>">-->
                        <input type="hidden" name="id_receita" value="<?=$id_receita?>">
						
                        <input type="text" name="ano" size="4" maxlength="4" style="width: 45px" value="<?=$ano?>" onKeyPress="return isNumberKey(event);" onKeyUp="verifica_saida(this.value, 'unidade', 4, this.form);">
                        <b>&nbsp;-&nbsp;</b>
                        <script>
                          document.form_argumentos.ano.focus();
                        </script>
                        <input type="text" name="unidade" id="unidade" size="4" maxlength="3" style="width: 45px" value="<?=$unidade?>" onKeyPress="return isNumberKey(event);" onKeyUp="verifica_saida(this.value, 'numero', 3, this.form);">
                        <b>&nbsp;-&nbsp;</b>
                        <input type="text" name="numero" id="numero" size="4" maxlength="5" style="width: 45px" value="<?=$numero?>" onKeyPress="return isNumberKey(event);" onKeyUp="verifica_saida(this.value, 'buscar', 5, this.form);">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                      <?
                      $url = "./finaliza_receitar.php?id_receita=1" + $id_receita;
                      ?>
                          <!--<input type="button" style="font-size: 12px;" name="buscar" value="Pesquisar" onclick="return valida_campos();">-->
                          <input type="button" style="font-size: 12px;" name="buscar" value="Pesquisar" onclick= "return valida_campos();">                          
						  
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
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
