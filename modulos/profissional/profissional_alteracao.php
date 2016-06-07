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
  //  Arquivo..: profissional_alteracao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de alteracao de profissional
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

    if ($_POST[lista_unidade] != "")
    {
      $lista_unidade = $_POST[lista_unidade];
      $lista_unidade = substr($lista_unidade,0, strlen($lista_unidade)-2);

      $lista_unid = explode(",", $lista_unidade);
    }
    
    
    if(isset($_POST[flag])){
      $sql="select inscricao, tipo_conselho_id_tipo_conselho, tipo_prescritor_id_tipo_prescritor, nome, especialidade, estado_id_estado
            from profissional where inscricao='$_POST[inscricao]' and tipo_conselho_id_tipo_conselho='$_POST[conselho]' and status_2='A'
            and id_profissional <> $_POST[codigo_antigo]";
     //echo $sql;
      $res=mysqli_query($db, $sql);
      erro_sql("Select Profissional Existente", $db, "");
//      if((mysqli_num_rows($res)>0) && ($_POST[inscricao]!=$_POST[inscricao_atual]) && ($_POST[conselho]!=$_POST[conselho_atual])){
      if (mysqli_num_rows($res)>0){
        header("Location: ". URL."/modulos/profissional/profissional_alteracao.php?a=f&codigo=$_POST[codigo_antigo]");
      }
      else{
        if($_POST[flag]=="t"){
          $data_sistema=date("Y-m-d H:i:s");
          $sql="update profissional ";
          $sql.="set inscricao='$_POST[inscricao]', tipo_conselho_id_tipo_conselho='$_POST[conselho]', ";
          $sql.="nome='" . strtoupper($_POST[nome]) . "', tipo_prescritor_id_tipo_prescritor='$_POST[prescritor]', ";
          $sql.="especialidade='" . strtoupper($_POST[especialidade]) . "', estado_id_estado='$_POST[uf]', ";
          $sql.="status_2='A', data_alt='$data_sistema', usua_alt='$_SESSION[id_usuario_sistema]' ";
          $sql.="where id_profissional='$_POST[codigo_antigo]'";
          mysqli_query($db, $sql);
          erro_sql("Update Profissional", $db, "");
          $atualizacao="";
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }


          if (mysqli_errno($db) == 0)
          {

            $sql_delete_unidade = "delete from unidade_has_profissional where profissional_id_profissional = '$_POST[codigo_antigo]'";
            mysqli_query($db, $sql_delete_unidade);
            erro_sql("Delete Unidade", $db, "");
            
          if ($_POST[lista_unidade]!="")
            {
              for ($x=0; $x<=count($lista_unid)-1; $x++)
              {
                $sql_insert_unidade = "insert into unidade_has_profissional (unidade_id_unidade, profissional_id_profissional, date_incl, usua_incl)
                                     values ('$lista_unid[$x]', '$_POST[codigo_antigo]', '$data_sistema', '$_SESSION[id_usuario_sistema]')";
                mysqli_query($db, $sql_insert_unidade);
                erro_sql("Insert Unidade Profissional", $db, "");
                if(mysqli_errno($db)!="0"){
                  $atualizacao="erro";
                }
              }
            }

          if($atualizacao=="")
          {
            mysqli_commit($db);
            $aux=$_POST[aux];
            header("Location: ". URL."/modulos/profissional/profissional_inicial.php?a=t&".$aux);
          }
          else
          {
            mysqli_rollback($db);
            header("Location: ". URL."/modulos/profissional/profissional_inicial.php?a=f");
          }
        }
      }
    }
    }
    else{
      if($_GET[codigo]=="" && !isset($_POST[flag])){
        header("Location: ". URL."/modulos/profissional/profissional_inicial.php");
      }
      else{
        $sql="select inscricao, tipo_conselho_id_tipo_conselho, tipo_prescritor_id_tipo_prescritor, nome, especialidade, estado_id_estado
              from profissional where id_profissional='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Profissional Escolhido", $db, "");
        if(mysqli_num_rows($res)>0){
          $consulta=mysqli_fetch_object($res);
        }
      }
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
        flg.value="t";
        return true;
      }

  var cont = 0;
  var vetCod = new Array();
  
      function insereLinhas()
  {
    if (document.form_alteracao.unidade.selectedIndex > 0)
    {
      var origem = document.createElement('option');
      origem.value = document.form_alteracao.unidade.options[document.form_alteracao.unidade.selectedIndex].value;
      origem.text = document.form_alteracao.unidade.options[document.form_alteracao.unidade.selectedIndex].text;

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

        x.align = "left";
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
      alert("Selecione uma unidade!")
    }
  }

  function removeLinhas(lnh)
  {
    document.getElementById("tabela").deleteRow(document.getElementById(lnh).rowIndex);
  }

  function salvar_dados()
  {
    //if (validar_campos() == true)
    //{
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
      //}
      document.form_alteracao.flag.value='t';
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
                  <form name="form_alteracao" action="./profissional_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Inscrição
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="inscricao" size="30" style="width: 200px" value="<?php if(isset($_POST[inscricao])){echo $_POST[inscricao];}else{echo $consulta->inscricao;}?>" onKeyPress="return isNumberKey(event);">
                        <script>
                          //document.form_alteracao.inscricao.focus();
                        </script>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Conselho Profissional
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
            <!-- -->
                        <select name="conselho" size="1" style="width: 200px" onchange="document.form_alteracao.codigo01.value=''; carregarCombo(this.value, '../../xml/conselho_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');">

                          <option value="0"> Selecione um Conselho </option>
                          <?php
                            $sql="select distinct c.id_tipo_conselho, c.descricao from tipo_prescritor as p, tipo_conselho as c where p.tipo_conselho_id_tipo_conselho=c.id_tipo_conselho and p.status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Conselho", $db, "");
                            while($conselho_info=mysqli_fetch_object($res)){
                          ?>
                              <option value='<?php echo $conselho_info->id_tipo_conselho;?>'<?php if(isset($_POST[conselho])){if($_POST[conselho]==$conselho_info->id_tipo_conselho){echo "selected";}}else{if($consulta->tipo_conselho_id_tipo_conselho==$conselho_info->id_tipo_conselho){echo "selected";}}?>> <?php echo $conselho_info->descricao;?>
                              </option>
                          <?php
                            }
                          ?>
                        </select>
                        
            <!-- -->
                        
                        
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="nome" size="30" style="width: 450px" value="<?php if(isset($_POST[nome])){echo $_POST[nome];}else{echo $consulta->nome;}?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Profissional
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                      <?
                       $tipo_presc = 0;
                       if (isset($consulta->tipo_prescritor_id_tipo_prescritor))
                          $tipo_presc = $consulta->tipo_prescritor_id_tipo_prescritor;
                      ?>
                      
                      <input type="hidden" name="codigo01" id="codigo01" value="<?echo $tipo_presc?>">
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
                        <input type="text" name="especialidade" size="30" style="width: 200px" value="<?php if(isset($_POST[especialidade])){echo $_POST[especialidade];}else{echo $consulta->especialidade;}?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        UF
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <select name="uf" size="1" style="width: 200px">
                          <option value="0"> Selecione uma UF </option>
                          <?php
                            $sql="select id_estado, uf from estado";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select UF", $db, "");
                            while($uf_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $uf_info->id_estado;?>" <?php if(isset($_POST[uf])){if($_POST[uf]==$uf_info->id_estado){echo "selected";}}else{if($consulta->estado_id_estado==$uf_info->id_estado){echo "selected";}}?>> <?php echo $uf_info->uf;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
  <!-- -->
  
                    <form name="FormularioUnidade" action="" method="POST" enctype="multipart/form-data">
			        <TR>
            			<TD colspan="4"></TD>
		            </TR>
         			<TR valign="top">
		        		<TD colspan="6">
     			     	<table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="0">
						<TR align="center">
							<TD>Unidade</TD>
							<TD width="10"><A href="javascript:showFrame('show_unidade');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Unidade"></A></TD>
						</TR>
					</TABLE>
				</TD>
			</TR>
<?php
      $cor_linha = "#CCCCCC";
      // cinza claro = #CCCCCC
      // cinza escuro = #EEEEEE
      $num_linha = 0;
?>
			<TR>
				<TD colspan="6">
					<div id="show_unidade" style="display:none;">
					<table border="0" width="100%"  cellpadding="0" cellspacing="0">
         			<TR>
                     <TD colspan="6">
                     	<TABLE width="100%" cellpadding="0" cellspacing="1">
					    <TR>
					        <TD align="left" width="20%" class="descricao_campo_tabela">
                               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Unidade:
                            </TD>
  	                    <td align="left" width="80%" bgcolor="#D4DFED">
                               <select name="unidade" size="1"  style="width:200px;">
                                  <option value=""> Selecione</option>
                                  <?php
                                       $sql = "select id_unidade, nome from unidade order by nome";
                                       $unidade = mysqli_query($db, $sql);
                                       erro_sql("Select Unidade", $db, "");
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
							<td colspan="4" width="100%">
								<table width="100%" cellpadding="0" cellspacing="1" id="tabela" border="0" >
                                    <tr bgcolor=#0E5A98>
										<td widht="20%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Código</b></font></td>
										<td widht="50%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Unidade</b></font></td>
										<td widht="10%" align="center"><font color="#FFFFFF" face="arial" size="2">&nbsp;</font></td>
									</tr>
                                    <?php


                                       
                                       $sql_unidade = "select u.id_unidade, u.nome
                                       from unidade u, unidade_has_profissional p
                                       where u.id_unidade = p.unidade_id_unidade
                                       and p.profissional_id_profissional = '$_GET[codigo]'";

                                       $unidade = mysqli_query($db, $sql_unidade);
                                       erro_sql("Select Código/Unidade", $db, "");
                                       $cont = 0;
                                       while ($listaunidade = mysqli_fetch_object($unidade))
                                       {
                                         $nome_linha = "linha_db".$cont;
                                    ?>
                                   	      <tr class="campo_tabela" id='<?=$nome_linha?>' valign="center" bgcolor="<?php echo $cor_linha;?>" onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?php echo $cor_linha; ?>';">
                                             <input type="hidden" name="id_unidade[]" value="<?php echo $listaunidade->id_unidade; ?>">
  										     <td align="center"><?php echo $listaunidade->id_unidade; ?></td>
										     <td align="left"><?php echo $listaunidade->nome; ?></td>
                                             <td align="center">
                                               <a href="JavaScript:document.getElementById('tabela').deleteRow(<?=$nome_linha?>.rowIndex);"><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Remover Registro"></a>
                                             </td>
									      </tr>
                                          <?php
                                          $cont++;
                                       }
                                       if ($cor_linha == "#CCCCCC")
                                       {
                                          $cor_linha = "#EEEEEE";
                                       }
                                       else
                                       {
                                          $cor_linha = "#CCCCCC";
                                       }
                                       ?>
								</table>
							</td>
						</tr>
					</table>
					</div>
				</TD>

			</TR>
			
			<!-- -->
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/profissional/profissional_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_alteracao.inscricao, document.form_alteracao.conselho, document.form_alteracao.nome, document.form_alteracao.prescritor, document.form_alteracao.uf, document.form_alteracao.flag)){salvar_dados(); document.form_alteracao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="codigo_antigo" value="<?php if(isset($_POST[codigo_antigo])){echo $_POST[codigo_antigo];}else{echo $_GET[codigo];}?>">
                    <input type="hidden" name="lista_unidade" id="lista_unidade" value="<?=$lista_unidade?>">
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="inscricao_atual" value="<?php if(isset($_POST[inscricao_atual])){echo $_POST[inscricao_atual];}else{echo $consulta->inscricao;}?>">
                    <input type="hidden" name="conselho_atual" value="<?php if(isset($_POST[conselho_atual])){echo $_POST[conselho_atual];}else{echo $consulta->tipo_conselho_id_tipo_conselho;}?>">
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET['pesquisa']?>">
                  </form>
                  
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
    <script>
    <!--
      //Instanciar objeto Combo
       var AC = new carregarCombo(document.form_alteracao.conselho.value, '../../xml/conselho_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');

   //-->

  </script>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";

    if($_GET[a]=='f'){echo "<script>window.alert('Profissional ja existe!')</script>";}
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
