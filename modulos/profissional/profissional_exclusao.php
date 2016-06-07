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
  //  Arquivo..: profissional_exclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de exclusao de profissional
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


    if(isset($_POST[codigo_atual])){
      $data=date("Y-m-d H:i:s");
      $sql="update profissional ";
      $sql.="set status_2='I', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]' ";
      $sql.="where id_profissional='$_POST[codigo_atual]'";
      mysqli_query($db, $sql);
      erro_sql("Update Profissional", $db, "");

     $atualizacao="";
     if(mysqli_errno($db)!="0"){
       $atualizacao="erro";
     }
     if (mysqli_errno($db) == 0)
     {
        $sql_exc_unidade = "delete from unidade_has_profissional where profissional_id_profissional = '$_POST[codigo_atual]'";
        mysqli_query($db, $sql_exc_unidade);
        erro_sql("Delete Unidade Profissional", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
     }

     if($atualizacao=="")
     {
        mysqli_commit($db);
        $aux=$_POST[aux];
        header("Location: ". URL."/modulos/profissional/profissional_inicial.php?e=t&".$aux);
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/profissional/profissional_inicial.php?e=f");
      }
    }
    else{
      if($_GET[codigo]==""){
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
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
    <script language="javascript">
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
                  <form name="form_exclusao" action="./profissional_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao; ?>: Excluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Inscrição
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="inscricao" size="30" style="width: 200px" disabled value="<?php echo $consulta->inscricao?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Conselho Profissional
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <select name="conselho" size="1" style="width: 200px" disabled>
                          <option value="0"> Selecione um Conselho </option>
                          <?php
                            $sql="select c.id_tipo_conselho, c.descricao from tipo_prescritor as p, tipo_conselho as c where p.tipo_conselho_id_tipo_conselho=c.id_tipo_conselho and p.status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Conselho", $db, "");
                            while($conselho_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $conselho_info->id_tipo_conselho;?>" <?php if($consulta->tipo_conselho_id_tipo_conselho==$conselho_info->id_tipo_conselho){echo "selected";}?>> <?php echo $conselho_info->descricao;?> </option>
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
                        <input type="text" name="nome" size="30" style="width: 450px" disabled value="<?php echo $consulta->nome?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Profissional
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="prescritor" size="1" style="width: 200px" disabled>
                          <option value="0"> Selecione um Profissional </option>
                          <?php
                            $sql="select id_tipo_prescritor, descricao from tipo_prescritor where tipo_conselho_id_tipo_conselho='$consulta->tipo_conselho_id_tipo_conselho'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Tipo Prescritor", $db, "");
                            while($prescritor_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $prescritor_info->id_tipo_prescritor;?>" <?php if($consulta->tipo_prescritor_id_tipo_prescritor==$prescritor_info->id_tipo_prescritor){echo "selected";}?>> <?php echo $prescritor_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Especialidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="especialidade" size="30" style="width: 200px" disabled value="<?php echo $consulta->especialidade;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        UF
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <select name="uf" size="1" style="width: 200px" disabled>
                          <option value="0"> Selecione uma UF </option>
                          <?php
                            $sql="select id_estado, uf from estado";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select UF", $db, "");
                            while($uf_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $uf_info->id_estado;?>" <?php if($consulta->estado_id_estado==$uf_info->id_estado){echo "selected";}?>> <?php echo $uf_info->uf;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    
                    
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
                                               <img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Remover Registro">
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
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/profissional/profissional_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
                        <input type="submit" name="excluir" style="font-size: 12px;" value="Excluir >>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="codigo_atual" value="<?php echo $_GET[codigo];?>">
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET[pesquisa]?>">
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

  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
