<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();


  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: profissional_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de profissional
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }
    // caso o usuario digite varios espaços em branco
    if (isset($_POST[nome]))
     {
       $nome=$_POST[nome];
       while (strstr($nome,"  "))
        {
          $nome = str_replace("  ", " ", $nome);
        }
        $_POST[nome]=$nome;
     }



    if(isset($_POST[flag])){
      $sql="select inscricao, tipo_conselho_id_tipo_conselho, tipo_prescritor_id_tipo_prescritor, nome, especialidade, estado_id_estado
            from profissional where inscricao='$_POST[inscricao]' and tipo_conselho_id_tipo_conselho='$_POST[conselho]' and status_2='A'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Profissional Existente", $db, "");
      if(mysqli_num_rows($res)>0)
      {
        header("Location: ". URL."/modulos/profissional/profissional_inclusao.php?i=f");
      }
      else{
        if($_POST[flag]=="t"){
          $atualizacao="";
          $data_sistema=date("Y-m-d H:i:s");

          $sql="insert into profissional (tipo_conselho_id_tipo_conselho, tipo_prescritor_id_tipo_prescritor, nome, status_2, inscricao, data_inscricao, estado_id_estado, especialidade, usua_incl, data_incl) ";
          $sql.="values ('$_POST[conselho]', '$_POST[prescritor]', '" . strtoupper($_POST[nome]) . "', 'A', '$_POST[inscricao]', '', '$_POST[uf]', '" . strtoupper($_POST[especialidade]) . "' , '$_SESSION[id_usuario_sistema]', '$data_sistema')";
          mysqli_query($db, $sql);
          erro_sql("Insert Profissional", $db, "");
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }


    if ($_POST[lista_unidade] != "")
    {
      $lista_unidade = $_POST[lista_unidade];
      $lista_unidade = substr($lista_unidade,0, strlen($lista_unidade)-2);

      $lista_unid = explode(",", $lista_unidade);
     }



       if (mysqli_errno($db) == 0)
       {

          $sql = "select max(id_profissional) as codigo from profissional";
          $res=mysqli_query($db, $sql);
          erro_sql("Select max", $db, "");
          $info_profissional=mysqli_fetch_object($res);
          $id_profissional = $info_profissional->codigo;

          if ($_POST[lista_unidade]!="")
          {

           for ($x=0; $x<=count($lista_unid)-1; $x++)
            {
              $sql_insert_unidade = "insert into unidade_has_profissional (unidade_id_unidade, profissional_id_profissional, date_incl, usua_incl)
                                     values ('$lista_unid[$x]', '$id_profissional', '$data_sistema', '$_SESSION[id_usuario_sistema]')";
              mysqli_query($db, $sql_insert_unidade);

              erro_sql("Insert Profissional Unidade", $db, "");
              if(mysqli_errno($db)!="0"){
               $atualizacao="erro";
              }
            }
          }
       }

          if($atualizacao=="")
          {
            mysqli_commit($db);
            header("Location: ". URL."/modulos/profissional/profissional_inicial.php?i=t");
          }
          else
          {
            mysqli_rollback($db);
            header("Location: ". URL."/modulos/profissional/profissional_inicial.php?i=f");

          }
        }
      }  //else
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/buscar_aplic.php";
?>
    <script language="javascript" type="text/javascript" src="../../scripts/combo.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(inscr, consel, name, prescr, unid, flg){
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
        if(unid.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          unid.focus();
          return false;
        }
        document.form_inclusao.flag.value="t";
        return true;
      }
      
   function voltar_pagina()
  {
   if (document.form_inclusao.dispensacao.value == "nao")
   {
      window.location="profissional_inicial.php";
   }
   else if (document.form_inclusao.dispensacao.value == "ok")
   {
      window.location="../dispensar/nova_receita.php?dispensacao=ok&id_paciente=<?=$id_paciente?>";
   }
  }

  var cont = 0;
  var vetCod = new Array();

  function insereLinhas()
  {
    if (document.form_inclusao.unidade.selectedIndex > 0)
    {
      var origem = document.createElement('option');
      origem.value = document.form_inclusao.unidade.options[document.form_inclusao.unidade.selectedIndex].value;
      origem.text = document.form_inclusao.unidade.options[document.form_inclusao.unidade.selectedIndex].text;

      achou = false;

      var itens = document.getElementById("tabela");
      total_linhas = document.getElementById("tabela").rows.length;

      for (i=1; i<total_linhas; i++)
      {
        if (itens.rows[i].cells[0].innerHTML == origem.value)
        {
          achou = true;
        }
      }

      if (achou == false)
      {
        pos = document.getElementById("tabela").rows.length;

        var tab = document.getElementById("tabela").insertRow(pos);
        tab.id = "linha"+cont;
        tab.className = "campo_tabela";
        var x = tab.insertCell(0);
        var y = tab.insertCell(1);
        var z = tab.insertCell(2);

        x.align = "center";
        y.align = "left";
        z.align = "center";

        vetCod[cont] = origem.value;
        x.innerHTML = origem.value;
        y.innerHTML = origem.text;

        var Site = "<img src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
        var url = "JavaScript:removeLinhas('linha"+cont+"')";
        z.innerHTML = Site.link(url);
        cont++;
      }
      else
      {
        alert("Unidade já inserida!")
      }
    }
    else
    {
      alert("Selecione uma Unidade!")
    }
  }

  function removeLinhas(lnh)
  {
    document.getElementById("tabela").deleteRow(document.getElementById(lnh).rowIndex);
  }

  function salvar_dados()
  {
      var itens = document.getElementById('tabela');

      total_linhas = document.getElementById("tabela").rows.length;
      lista = document.getElementById('lista_unidade');
      for (i=1; i<total_linhas; i++)
      {
        if (i == 1)
        {
          lista.value = itens.rows[i].cells[0].innerHTML + ", ";
        }
        else if (i > 1)
        {
          lista.value = lista.value + itens.rows[i].cells[0].innerHTML + ", ";
        }

      document.form_inclusao.flag.value='t';
    }
  }
      //-->
    </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
                  <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./profissional_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao; ?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Inscrição
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="inscricao" size="30" maxlength="10" style="width: 200px" value="<?php if(isset($_POST)){echo $_POST[inscricao];}?>" onKeyPress="return isNumberKey(event);">
                        <script>
                          //document.form_inclusao.inscricao.focus();
                        </script>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Conselho Profissional
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                      <select name="conselho" size="1" style="width: 200px" onChange="carregarCombo(this.value, '../../xml/conselho_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor')">

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
                        <input type="text" name="nome" size="30" style="width: 450px" value="<?php if(isset($_POST)){echo $_POST[nome];}?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Profissional
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                      <select id="prescritor" name="prescritor" size="1" style="width: 200px">
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
                              if(!isset($_POST[uf]) && $uf_info->uf=="SP"){
                                $codigo_uf=$uf_info->id_estado;
                              }
                          ?>
                              <option value="<?php echo $uf_info->id_estado;?>" <?php if(isset($_POST[uf])){if($_POST[uf]==$uf_info->id_estado){echo "selected";}}else{if("SP"==$uf_info->uf){echo "selected";}}?>> <?php echo $uf_info->uf;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
<!--
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Data de Inscrição
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="data" size="30" style="width: 200px" value="<?php if(isset($_POST[data])){echo $_POST[data];}?>" onKeyPress="return mascara_data(event,this);" onblur="verificaData(this,this.value);">
                      </td>
                    </tr>
//-->
       <!-- associar Unidade -->
       
    <form name="FormularioUnidade" action="" method="POST" enctype="multipart/form-data">
               <TR>
    			<TD colspan="6"></TD>
    		   </TR>
 			   <TR valign="top">
		        <TD colspan="6">
 			     	<table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="1" >
						<TR align="center">
							<TD>Unidade</TD>
							<TD width="10"><A href="javascript:showFrame('show_unidade');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Unidade"></A></TD>
						</TR>
                  	</TABLE>
				</TD>
			</TR>
<?php
      $cor_linha = "#CCCCCC";
      $num_linha = 0;
?>
			<TR>
				<TD colspan="6">
					<div id="show_unidade" style="display:none;">
					<table border="0" width="100%" cellpadding="0" cellspacing="0" >
         			<TR>
                     <TD colspan="4">

                     <TABLE width="100%" cellpadding="0" cellspacing="1" border="0">
					    <TR>
					        <TD align="left" width="20%" bgcolor="#D8DDE3" class="descricao_campo_tabela">
                               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Unidade:
                            </TD>
    	                    <td align="left" width="80%" bgcolor="#D4DFED">
                               <select name="unidade" size="1"  style="width:200px;">
                                  <option value=""> Selecione</option>
                                  <?php
                                       $sql = "select id_unidade, nome from unidade where status_2 = 'A' order by nome";
                                       $unidade = mysqli_query($db, $sql);
                                       erro_sql("Select Unidade",$db, "");
                                       $total_itens = mysqli_num_rows($unidade);
                                       while ($listaunidade = mysqli_fetch_object($unidade))
                                       {?>
                                         <option value="<?php echo $listaunidade->id_unidade;?>"> <?php echo $listaunidade->nome;?></option>
                                       <?}
                                  ?>
                               </select>
                               <input style="font-size: 12px;" type="button" name="ok" value=" OK " onclick="insereLinhas();">
                            </td>
                        </TR>
			    	    </TABLE>
				     </TD>
			      </TR>
						<tr>
							<td colspan="4">
								<table width="100%" cellpadding="0" cellspacing="1" id="tabela">
                                    <tr bgcolor=#0E5A98>
										<td widht="20%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Código</b></font></td>
										<td widht="50%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Unidade</b></font></td>
										<td widht="10%" align="center"><font color="#FFFFFF" face="arial" size="2">&nbsp;</font></td>
									</tr>

					</table>
					</td>
					</tr>
					</table>
					</div>
				</TD>
			</TR>

                    <tr class="campo_botao_tabela">
                      <td colspan="4"valign="middle" align="right" width="100%" height="35">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/profissional/profissional_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_inclusao.inscricao, document.form_inclusao.conselho, document.form_inclusao.nome, document.form_inclusao.prescritor, document.form_inclusao.uf, document.form_inclusao.flag)){salvar_dados(); document.form_inclusao.submit();}">
                        <input type="hidden" name="dispensacao" <?php if (isset($dispensacao)){echo "value='".$dispensacao."'";}?> >
                        <input type="hidden" name="lista_unidade" id="lista_unidade" value="<?=$lista_unidade?>">

                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="flag" value="f">

                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";

    if($_GET[i]=='f'){echo "<script>window.alert('Profissional já cadastrado!')</script>";}

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
