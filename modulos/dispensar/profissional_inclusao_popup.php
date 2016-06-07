<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/buscar_aplic.php";
?>
    <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
    <script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
    <script language="javascript" type="text/javascript" src = "../../scripts/combo_dispensacao_profissional.js"></script>
    <script language="JavaScript" type="text/javascript" src = "../../scripts/scripts.js"></script>

    <script language="javascript">
function testar_prescritor()
{
 if (document.form_inclusao.flg_presc.value == 1)
 {
  document.form_inclusao.nome.focus();
  document.form_inclusao.flg_presc.value = 0;
 }
}

function preencheCampos(id, tipo, insc, nome, uf)
{
    var args = id+'|'+tipo+'|'+insc+'|'+nome+'/'+uf;

	if (window.showModalDialog)
	{
		var _R = new Object()
        _R.strArgs=args;
		window.returnValue=_R;
	}
	else
	{
		if (window.opener.SetName_NovoPrescritor)
		{
			window.opener.SetName_NovoPrescritor(args);
		}
	}
	window.close();
}

    function salvarProfissional()
    {
      var y=document.form_inclusao;

      var inscricao     = y.inscricao.value;
      var conselho      = y.conselho.value;
      var nome          = y.nome.value;
      var profissional  = y.prescritor.value;
      var especialidade = y.especialidade.value;
      var uf            = y.uf.value;

      var url = "../../xml_dispensacao/salvarProfissional.php?inscricao="+ inscricao
                      + "&conselho=" + conselho
                      + "&nome=" + nome
                      + "&profissional=" + profissional
                      + "&especialidade=" + especialidade
                      + "&uf="+ uf;
       requisicaoHTTP("GET", url, true);
    }

    function verificarInscricaoConselho()
    {

     var url = "../../xml_dispensacao/verificar_inscricao_conselho.php?"
                +"inscricao="+document.form_inclusao.inscricao.value
                +"&conselho="+document.form_inclusao.conselho.value
                +"&uf="+document.form_inclusao.uf.value;
     requisicaoHTTP("GET", url, true);
    }


    function trataDados()
    {
      var x=document.form_inclusao;
	  var info = ajax.responseText;  // obtém a resposta como string

      if (info.indexOf("ID")==0)
      {
       id_prof = info.substring(2);
       if (id_prof!=0)
       {
        //alert (info);
        preencheCampos(id_prof, x.prescritor.value, x.inscricao.value, x.nome.value, x.uf.options[x.uf.selectedIndex].text);
       }
      }
      else
      {
	   if(info=="existe_profissional")
       {
        alert ('Profissional já cadastrado!');
        x.inscricao.focus();
        x.conselho.selectedIndex=0;
        x.prescritor.selectedIndex=0;
        x.nome.value='';
        x.inscricao.value='';
        x.especialidade.value='';
        x.uf.selectedIndex=0;
       }
       else if(info=="nao_existe_profissional")
       {
         salvarProfissional();
       }
      }

    }

      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
       function validarCampos(inscr, consel, name, prescr, uf){
        if(inscr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          inscr.focus();
          inscr.select();
          return false;
        }
        if(consel.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          consel.focus();
          return false;
        }
        if(name.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          name.focus();
          name.select();
          return false;
        }
        if(prescr.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          prescr.focus();
          return false;
        }
        if(uf.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          uf.focus();
          return false;
        }
        return true;
      }

      //-->
</script>

    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
                  <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./profissional_inclusao_popup.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="flg_presc">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> Novo Prescritor </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Inscrição
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="inscricao" size="30" maxlength="10" style="width: 200px" value="<?php if(isset($_POST)){echo $_POST[inscricao];}?>" onKeyPress="return isNumberKey(event);">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Conselho Profissional
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                      <select name="conselho" size="1" style="width: 200px" onChange="carregarCombo(this.value, '../../xml/conselho_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor'); if (document.form_inclusao.nome.value==''){document.form_inclusao.flg_presc.value=1;}">
                          <option value="0"> Selecione um Conselho </option>
                          <?php
                            $sql="select distinct c.id_tipo_conselho, c.descricao from tipo_prescritor as p, tipo_conselho as c where p.tipo_conselho_id_tipo_conselho=c.id_tipo_conselho and p.status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Conselho", $db, "");
                            while($conselho_info=mysqli_fetch_object($res)){
                          ?>
                              <option value='<?php echo $conselho_info->id_tipo_conselho;?>'> <? echo strtoupper($conselho_info->descricao); ?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                   </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="nome" id='nome' size="30" style="width: 450px" value="<?php if(isset($_POST)){echo $_POST[nome];}?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Profissional
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                      <select id="prescritor" name="prescritor" size="1" style="width: 200px" onFocus="testar_prescritor();">
                          <option id="opcao_prescritor" value="0"> Primeiro Selecione um Conselho </option>
                      </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Especialidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="especialidade" size="30" style="width: 200px" value="<?php if(isset($_POST)){echo $_POST[especialidade];}?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        UF
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <select name="uf" size="1" style="width: 200px">
                          <option value="0"> Selecione uma UF  </option>
                          <?php
                            $sql="select id_estado, uf from estado";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select UF", $db, "");
                            while($uf_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $uf_info->id_estado;?>"> <?php echo $uf_info->uf;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>

                     <tr class="campo_botao_tabela">
                      <td colspan="4"valign="middle" align="right" width="100%" height="35">
                        <input style="font-size: 10px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.close();">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>"
                                                                                    onclick="if(validarCampos(document.form_inclusao.inscricao,
                                                                                                              document.form_inclusao.conselho,
                                                                                                              document.form_inclusao.nome,
                                                                                                              document.form_inclusao.prescritor,
                                                                                                              document.form_inclusao.uf))
                                                                                             {verificarInscricaoConselho();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
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

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
