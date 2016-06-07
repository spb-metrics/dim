<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    $sql = "select tentativas_senha from parametro";
    $parametro = mysqli_fetch_object(mysqli_query($db, $sql));
    $max_tentativas = $dados_cidade_receita->tentativas_senha;
    if ($max_tentativas=='')
    {
     $max_tentativas = 3;
    }
    
?>

<html>
  <head><title> Autorização </title></head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<head>
 <BASE target="_self">
</head>
<script language="javascript">
function valida()
{
     var url = "../../xml_dispensacao/valida_autorizador.php?autorizador="+document.form_autoriza.login.value+
              "&senha="+document.form_autoriza.senha.value;
     requisicaoHTTP("GET", url, true);

}

function trataDados()
{
   var info = ajax.responseText;  // obtém a resposta como string

   if (info == 'invalido')
   {
    alert ('Autorizador Inválido');
    document.form_autoriza.cont.value = parseInt(document.form_autoriza.cont.value,10)+1;
    document.form_autoriza.login.value = '';
    document.form_autoriza.login.focus();
    document.form_autoriza.senha.value = '';
 	
    if (parseInt(document.form_autoriza.cont.value,10) > parseInt(document.form_autoriza.max_tentativas.value,10))
    {
           var id = '';
           preencheCampos(id);
    }
   }
   else
   {
    if (info == 'nada')
    {
    }
    else
    {
     preencheCampos(info);
    }
   }
}

function preencheCampos(id)
{
	if (window.showModalDialog)
	{
		var _R = new Object()
		_R.id=id;
		window.returnValue=_R;
	}
	else
	{
		if (window.opener.SetNameAutorizador)
		{
			window.opener.SetNameAutorizador(id);
		}
	}
	window.close();
}
</script>
<script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>

<body onLoad="document.form_autoriza.login.focus()">


    <form name="form_autoriza" action="autorizador.php" method="POST" enctype="application/x-www-form-urlencoded">
          <table bgcolor="#FFFFFF" align='center' border="0" cellspacing="1" cellpadding="0" width="100%">
          <input type="hidden" size="20" name="cont" value=1>
          <input type="hidden" size="20" name="max_tentativas" value="<?php echo $max_tentativas; ?>">
            <tr>
              <td class="descricao_campo_tabela" align="center" width="30%">Login:</td>
              <td class="campo_tabela" valign="middle" width="70%">
               <input type="text" name="login" size="15" maxlenght="50">
              </td>
            </tr>
            <tr>
              <td class="descricao_campo_tabela" align="center" width="30%">Senha:</td>
              <td class="campo_tabela" valign="middle" width="70%">
              <input type="password" name="senha" size="15" maxlenght="50">
              </td>
            </tr>
            <tr>
              <td class="descricao_campo_tabela" colspan="2" align="right"><input style="font-size: 10px;" type="button" name="salvar" value="Ok" onClick="valida();"></td>
            </tr>
          </table>
    </form>
</body>
<?
}
?>
