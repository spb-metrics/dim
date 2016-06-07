<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $_SESSION[APLICACAO]=$_GET[aplicacao];

  $_SESSION[DISP_INICIAL]=$_GET[aplicacao];

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    if ((isset($_GET[cartao_tela]) and $_GET[cartao_tela]!='') or (isset($_GET[nome_tela]) and $_GET[nome_tela]!='')
    or (isset ($_GET[cpf_tela]) and $_GET[cpf_tela]!='') or (isset($_GET[pront_tela]) and $_GET[pront_tela]!=''))
    {
       $cartao_sus = trim($_GET[cartao_tela]);
       $nome = trim($_GET[nome_tela]);
       $data_nasc = trim($_GET[data_tela]);
       $nome_mae = trim($_GET[mae_tela]);
       $cpf = trim($_GET[cpf_tela]);
       $prontuario = trim($_GET[pront_tela]);
    }

    //executando botão pesquisar
    if ((isset($_POST[cartao_sus])) and ($_POST[cartao_sus]==""))
    {
       $cartao_sus = trim($_POST[cartao_sus]);
       $nome = trim($_POST[nome]);
       $data_nasc = trim($_POST[data_nasc]);
       $nome_mae = trim($_POST[nome_mae]);
       $cpf = trim($_POST[cpf]);
       $prontuario =  trim($_POST[prontuario]);
    }

    if ((isset($_POST[prontuario])) and ($_POST[prontuario]==""))
    {
       $cartao_sus = trim($_POST[cartao_sus]);
       $nome = trim($_POST[nome]);
       $data_nasc = trim($_POST[data_nasc]);
       $nome_mae = trim($_POST[nome_mae]);
       $cpf = trim($_POST[cpf]);
       $prontuario =  trim($_POST[prontuario]);
    }

    if ((isset($_POST[cpf])) and ($_POST[cpf]==""))
    {
       $cartao_sus = trim($_POST[cartao_sus]);
       $nome = trim($_POST[nome]);
       $data_nasc = trim($_POST[data_nasc]);
       $nome_mae = trim($_POST[nome_mae]);
       $cpf = trim($_POST[cpf]);
       $prontuario =  trim($_POST[prontuario]);
    }


    if ((isset($_POST[nome])) or (isset($_POST[nome_mae])))
    {
       $nome = trim($_POST[nome]);
       $nome_mae = trim($_POST[nome_mae]);
       $cartao_sus = trim($_POST[cartao_tela]);
       $data_nasc = trim($_POST[data_tela]);
       $cpf = trim($_POST[cpf]);
       $prontuario =  trim($_POST[prontuario]);
    }

       $substituir = "\'";
       $nome = ereg_replace("6e54c9a95b", $substituir, $nome);
       $nome_mae = ereg_replace("6e54c9a95b", $substituir, $nome_mae);
       $troca ="'";
       $nome_text = ereg_replace("\\\'", $troca, $nome);
       $mae_text  = ereg_replace("\\\'", $troca, $nome_mae);


    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require "../../verifica_acesso.php";

    // verificar permissão para inclusão de paciente e inclusão de profissional
    $sql = "select id_aplicacao from aplicacao where executavel = '/modulos/paciente/paciente_inicial.php' and status_2 = 'A'";
    $res_paciente = mysqli_fetch_object(mysqli_query($db, $sql));
    $id_aplicacao_paciente = $res_paciente->id_aplicacao;

    $sql = "select inclusao, alteracao, exclusao, consulta from perfil_has_aplicacao where perfil_id_perfil = '$_SESSION[id_perfil_sistema]' and aplicacao_id_aplicacao = '$id_aplicacao_paciente'";
    $acesso = mysqli_fetch_object(mysqli_query($db, $sql));

    $inclusao_perfil_paciente  = $acesso->inclusao;
    $alteracao_perfil_paciente = $acesso->alteracao;
    $exclusao_perfil_paciente  = $acesso->exclusao;
    $consulta_perfil_paciente  = $acesso->consulta;

    //caminho
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>

<!-- JavaScript -->

    <link href="<?php echo CSS;?>" rel="stylesheet" style type="text/css">
    <link rel="stylesheet" style type="text/css" href="css/bubble-tooltip.css" media="screen">
    <script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
    <script language="javascript" type="text/javascript" src="showModalPaciente.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/grid.js"></script>
	<script language="JavaScript" type="text/javascript" src="../../scripts/ballon.js"></script>
<script type="text/javascript">

function limpar_campos()
{
 document.form_inclusao.cartao_sus.value ='';
 document.form_inclusao.nome.value ='';
 document.form_inclusao.nome_mae.value ='';
 document.form_inclusao.data_nasc.value ='';
 document.form_inclusao.cpf.value ='';
 document.form_inclusao.prontuario.value ='';
}

function mensagem()
{
    var cartao = document.getElementById('cartao_sus').value;
    var nome =document.getElementById('nome').value;
    var cpf = document.getElementById('cpf').value;
    var prontuario =document.getElementById('prontuario').value;

    if ((cartao=='') && (nome =='') && (prontuario=='') && (cpf==''))
    {
       alert('É necessário digitar o nome, cartão sus, prontuário ou cpf');
       document.form_inclusao.pesquisar.disabled=false;
    }

    else if ((cartao=='') && (prontuario=='') && (cpf=='') && (nome!=''))
    {
        var nome = document.getElementById('nome').value;
        var tam = nome.length;
        for (var i=0; i<tam; i++)
        {
          nome = nome.replace('  ', ' ');
        }
        document.form_inclusao.nome.value = nome;

        var nome_aux = nome.split(' ');
        var aux_pos = nome.length - 1;
    	var pos = nome_aux.length;
        var espaco = false;

        var aux = nome.indexOf(' ');

        if (aux_pos == aux)
           espaco = true;

        if ((nome_aux[1]==undefined)||((nome_aux[pos-1]=='') && (espaco== true))){
         if(confirm('Você informou apenas um nome. Esta consulta poderá demorar muito tempo.Tem certeza que deseja continuar?'))
               search_data();
         } else search_data();
    }
}

function preencheCampos(id)
{
    var args = id;
	if (window.showModalDialog)
	{
		var _R = new Object()
        _R.strArgs=args;
		window.returnValue=_R;
	}
	else
	{
		if (window.opener.SetNamePaciente)
		{
			window.opener.SetNamePaciente(args);
		}
	}
    if (id!='limpar')
    {
       window.close();
    }
}

///////// funções do SIGA///////////
    function verifica_cartao_siga(aux_id_paciente)
    {
        var url = "verificacao_siga.php?id_paciente="+aux_id_paciente;
        requisicaoHTTP("GET", url, true, '');
    }


    function trataDados()
    {
    	var info = ajax.responseText;  // obtém a resposta como string

    	var pos = info.indexOf("|");
    	var mensagem = info.substr(pos+1);
       if(mensagem!='')
    	    alert (mensagem);

        var aux_id_paciente = info.substr(0,pos);
    	//alert ("|preencheCampos("+aux_id_paciente+")");
		preencheCampos(aux_id_paciente);
    }

/////////////////////////////////

    function search_data() {
    var x=document.form_inclusao;
    var id_paciente = x.id_paciente.value;
	var cartao      = x.cartao_sus.value;
	var cpf         = x.cpf.value;
	var prontuario  = x.prontuario.value;
	var nome        = x.nome.value;
	var nome_mae    = x.nome_mae.value;
	var data_nasc   = x.data_nasc.value;
	var id_unidade  = x.id_unidade.value;

	requestInfo('showTable_paciente.php?mode=display&id_paciente='+'&id_unidade_tela='+id_unidade+'&cartao_tela='+cartao+'&cpf_tela='+cpf+'&pront_tela='+prontuario+'&nome_tela='+nome+'&mae_tela='+nome_mae+'&data_nasc='+data_nasc,'showTable_paciente','');

	//alert('oi');
	}


    function validar_campos()
    {
     if ((document.form_inclusao.cartao_sus.value == "") && (document.form_inclusao.nome.value != "") && (document.form_inclusao.prontuario.value == "") && (document.form_inclusao.cpf.value == ""))
     {
        mensagem();
     }
    else
    {
     if ((document.form_inclusao.cartao_sus.value == "") && (document.form_inclusao.nome.value == "") && (document.form_inclusao.prontuario.value == "") && (document.form_inclusao.cpf.value == ""))
     {
        alert ("Favor preencher um dos campos obrigatórios para pesquisa(Cartão, CPF, Prontuário e/ou Nome)!");
        document.form_inclusao.cartao_sus.focus();
        return false;
     }

     if (!(document.form_inclusao.cartao_sus.value == "")&&(document.form_inclusao.cartao_sus.value.length < 15))
     {
       alert ("Cartão SUS deve conter 15 digitos!");
       document.form_inclusao.cartao_sus.focus();
       return false;
     }



     if (document.form_inclusao.data_nasc.value != "")
     {
         //validar data de nascimento
         erro=0;
         hoje = new Date();
         anoAtual = hoje.getFullYear();
         barras = document.form_inclusao.data_nasc.value.split("/");
         if (barras.length == 3)
         {
                   dia = barras[0];
                   mes = barras[1];
                   ano = barras[2];
                   resultado = (!isNaN(dia) && (dia > 0) && (dia < 32)) && (!isNaN(mes) && (mes > 0) && (mes < 13)) && (!isNaN(ano) && (ano.length == 4) && (ano <= anoAtual && ano >= 1700));
                   if (!resultado) {
                             alert("Formato de data invalido!");
                             document.form_inclusao.data_nasc.focus();
                             return false;
                   }

         }
         else
         {
                   alert("Formato de data invalido!");
                   document.form_inclusao.data_nasc.focus();
                   return false;
         }
     }
      search_data();
      return true;
     }
    }


    var d = new Date();
    var ID = d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

    function popup_receitas(paciente)
    {
   	var height = 500;
    	var width = 1000;
    	var left = (screen.availWidth - width)/2;
    	var top = (screen.availHeight - height)/2;
    	if (window.showModalDialog)
    	{
    		var dialogArguments = new Object();
    		var _R = window.showModalDialog("pesquisa_receitas.php?id_paciente="+paciente, dialogArguments, "dialogWidth=1000px;dialogHeight=500px;dialogTop=100px;dialogLeft=20px;scroll=yes;status=no;");
    		if ("undefined" != typeof(_R))
    		{
    			SetNameReceita(_R.id, _R.strName);
    		}
    		preencheCampos('limpar');

    	}
    	else	//NS
    	{
            var left = (screen.width-width)/2;
    		var top = (screen.height-height)/2;
     		var winHandle = window.open("pesquisa_receitas.php?id_paciente="+paciente, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
    		winHandle.focus();
    		preencheCampos('limpar');
    	}
    }

    function SetNameReceita(id, strName)
    {
    	document.form_inclusao.id_cidade_receita.value = id;
    	document.form_inclusao.cidade_receita.value = strName;
    }

    function popup_paciente(paciente)
    {
    	var height = 550;
    	var width = 750;
    	var left = (screen.availWidth - width)/2;
    	var top = (screen.availHeight - height)/2;
    	if (window.showModalDialog)
    	{
    		var dialogArguments = new Object();
    		var _R = window.showModalDialog("paciente_alteracao_popup.php?id_paciente="+paciente, dialogArguments, "dialogWidth=770px;dialogHeight=550px;dialogTop=100px;dialogLeft=130px;scroll=yes;status=no;");
    		if ("undefined" != typeof(_R))
    		{
    			SetName(_R.id);
    		}
    	}
    	else	//NS
    	{
    		var left = (screen.width-width)/2;
    		var top = (screen.height-height)/2;
     		var winHandle = window.open("paciente_alteracao_popup.php?id_paciente="+paciente, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
    		winHandle.focus();
    	}
    }

    function SetName(id)
    {
        document.form_inclusao.nome.focus();
    }


    function popup_incluipaciente()
    {
    	var height = 550;
    	var width = 750;
    	var left = (screen.availWidth - width)/2;
    	var top = (screen.availHeight - height)/2;
    	if (window.showModalDialog)
    	{
			params  = 'dialogWidth='+screen.width;
			params += '; dialogHeight='+screen.height;
			params += '; dialogTop=0, dialogLeft=0'
			params += '; fullscreen=yes';
			params += ';scroll=yes;status=no;';

    		var dialogArguments = new Object();
    	//	var _R = window.showModalDialog("https://sigasaudehomologacao.ima.sp.gov.br/sms/login.do?method=logoff", "", params);
    		var _R = window.open("https://sigasaude.ima.sp.gov.br/sms/login.do?method=logoff", "_blank");

			window.close();
    	}
    	else	//NS
    	{
    		var left = (screen.width-width)/2;
    		var top = (screen.height-height)/2;
     		var winHandle = window.open("paciente_inclusao_popup.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
    		winHandle.focus();
    	}
    }

    function SetNameInclusao(argumentos)
    {
      var valores = argumentos.split('|');
      document.form_inclusao.id_paciente.value = valores[0];
      document.form_inclusao.nome.value = valores[1];
      document.form_inclusao.nome_mae.value = valores[2];
      document.form_inclusao.cartao_sus.value = valores[3];
      document.form_inclusao.data_nasc.value = valores[4];
      document.form_inclusao.prontuario.value = valores[5];
      document.form_inclusao.cpf.value = valores[6];
      preencheCampos(valores[0]);
    }


    function preencheCamposReceita(id, nome, nome_mae, cartao_sus, nasc, prontuario, cpf)
    {
     var args = id+'|'+nome+'|'+nome_mae+'|'+cartao_sus+'|'+nasc+'|'+prontuario+'|'+cpf;
     if (window.showModalDialog)
    	{
    		var _R = new Object()
    		_R.args = args;
    		window.returnValue=_R;
    	}
    	else
    	{
    		if (window.opener.SetNameReceita)
    		{
                window.opener.SetNameReceita(args);
    		}
    	}
    	window.close();
     }


    </script>
    <body onLoad="<?php if(is_null($inclusao_perfil_paciente)){echo"document.getElementById('novo_paciente').setAttribute('disabled',true);";}?>">
    <form name="form_inclusao" action="./inicial.php?aplicacao=<?php echo $_GET[aplicacao];?>" method="POST" enctype="application/x-www-form-urlencoded">
    <table width="100%" style="height: 100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="100%" align="center" valign="top">
          <table name="3" width="100%" style="height: 20%" cellpadding="0" cellspacing="1" border="0">
            <tr>
              <td colspan='4' valign="top">
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> Pesquisar Paciente </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <input type="hidden" id="id_unidade" name="id_unidade" value="<?php echo $_SESSION[id_unidade_sistema];?>">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Cartão SUS
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="cartao_sus" id="cartao_sus" size="15"  maxlength="15" <?php if (isset($cartao_sus)){echo "value='".$cartao_sus."'";}?> >
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Prontuário
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="prontuario" id="prontuario" size="15"  maxlength="15" <?php if (isset($prontuario)){echo "value='".$prontuario."'";}?> >
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Nome
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%">
                        <input type="hidden" id="id_paciente" name="id_paciente" value="<?php echo $_GET[id_paciente];?>">
                        <input type="text" name="nome" id="nome" size="60"  maxlength="70" value="<?php echo $nome;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>CPF
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="cpf" id="cpf" size="15"  onKeyPress="return numbers(event);" maxlength="15" <?php if (isset($cpf)){echo "value='".$cpf."'";}?> >
                      </td>
                   </tr>

                   <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Nome Mãe
                      </td>
                      <td class="campo_tabela"  valign="middle"  width="50%" >
                        <input type="text" name="nome_mae" id="nome_mae" size="60" maxlength="70" value="<?php echo $nome_mae;?>">
                      </td>

                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                          <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Data Nasc.
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <input type="text" name="data_nasc" id="data_nasc" size="15"  maxlength="10" <?php if (isset($data_nasc)){echo "value='".$data_nasc."'";}?> onKeyPress="return mascara_data_dispensacao(event,this);" onBlur="verificaData(this);" >
                      </td>
                    </tr>

                    <tr>
                        <td colspan="2" align="right" bgcolor="#D8DDE3">
                            <input style="font-size: 10px;" type="button" name="pesquisar" id="pesquisar"  value="Pesquisar"  onclick="if(validar_campos()){ document.getElementById('id_paciente').value='';};">
                            <input style="font-size: 10px;" type="button" name="limpar"  id="limpar"  value="Limpar" onClick="limpar_campos();" >
                        </td>

                        <td colspan="2" align="right" bgcolor="#D8DDE3">
                            <?php
							
							//quando nao tiver permissao nao constroi o botao, problema com componente html dentro do php
                           //echo "*".!is_null($inclusao_perfil_paciente);
                            if(is_null($inclusao_perfil_paciente))
                            {												
                             echo "<input style='font-size: 10px;' type='button' id='novo_paciente' name='novo_paciente'  id='novo_paciente' value='Novo Paciente' disabled />";
							 //echo "<script> document.form_inclusao.novo_paciente.disabled=true;   </script>";
							 
                            } 
							
							
                            else
                            {
                             echo "<input style='font-size: 10px;' type='button' name='novo_paciente' id='novo_paciente' value='Novo Paciente' onclick='JavaScript:window.popup_incluipaciente();'>";
                            }
                            ?>
                        </td>
                    </tr>

                  <tr>
                    <td colspan="4" >
                      <table width="100%" cellpadding="0" cellspacing="1" border="0">
                        <tr class="coluna_tabela">
                          <td width='27%' align='center'>
                              Nome
                          </td>

                          <td width='8%' align='center'>
                              Data Nasc.
                          </td>

                          <td width='30%' align='center'>
                              Nome Mãe
                          </td>

                          <td width='17%' align='center'>
                              Endereço
                          </td>
                          <td width='3%' align='center'>
                          </td>
                          <td width='3%' align='center'>
                          </td>
                          <td width='3%' align='center'>
                          </td>
                          <td width='3%' align='center'>
                          </td>
                          <td width='6%' align='center'>
                          </td>
                      </tr>
                   </table>


                   <tr>
                     <td colspan='4'>
                       <div id="showTable_paciente"></div>
                     </td>
                   </tr>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
  </form>
</body>
</html>
<script type="text/javascript">
 search_data();
 
							
</script>



<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    //require DIR."/footer.php";

  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
