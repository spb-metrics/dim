<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    //excluir paciente
    if($_POST[id_paciente]!=""){
      $data=date("Y-m-d H:m:s");
      $atualizacao="";
      //atualizacao do status do paciente para status_2=I
      $sql="update paciente
            set status_2='I', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]'
            where id_paciente='$_POST[id_paciente]'";
      mysqli_query($db, $sql);
      erro_sql("Update Paciente", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!=0){
       $atualizacao="erro";
      }
      //deletando as atencoes continuadas
      $sql="delete from atencao_continuada_paciente
            where id_paciente='$_POST[id_paciente]'";
      mysqli_query($db, $sql);
      erro_sql("Delete Atenção Continuada Paciente", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      //deletando os cartoes sus
      $sql="delete from cartao_sus
            where paciente_id_paciente='$_POST[id_paciente]'";
      mysqli_query($db, $sql);
      erro_sql("Delete Cartão SUS", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      //deletando as prontuarios
      $sql="delete from prontuario
            where paciente_id_paciente='$_POST[id_paciente]'";
      mysqli_query($db, $sql);
      erro_sql("Delete Prontuário", $db, "");
      if(mysqli_errno($db)!=0){
        $atualizacao="erro";
      }
      if($atualizacao==""){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/paciente/paciente_inicial.php?e=t");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/paciente/paciente_inicial.php?e=f");
      }
      exit();
    }

    //mostrar informacoes do paciente
    if($_GET[id_paciente]!=""){
       $sql="select id_paciente, id_status_paciente, unidade_cadastro, unidade_referida, cidade_id_cidade,
             nome, tipo_logradouro, nome_logradouro, numero, complemento, bairro,
             nome_mae, sexo, data_nasc, telefone, cpf
             from paciente
             where id_paciente='$_GET[id_paciente]' and status_2='A'";
       $res=mysqli_query($db, $sql);
       erro_sql("Select Paciente Escolhido", $db, "");
       $paciente=mysqli_fetch_object($res);

       $id_paciente=$paciente->id_paciente;
       $id_status_paciente=$paciente->id_status_paciente;
       $unidade_cadastro=$paciente->unidade_cadastro;
       $unidade_referida=$paciente->unidade_referida;
       $cidade_id_cidade=$paciente->cidade_id_cidade;
       $nome=$paciente->nome;
       $tipo_logradouro=$paciente->tipo_logradouro;
       $logradouro=$paciente->nome_logradouro;
       $numero=$paciente->numero;
       $complemento=$paciente->complemento;
       $bairro=$paciente->bairro;
       $mae=$paciente->nome_mae;
       $sexo= $paciente->sexo;
       $data_nasc=substr($paciente->data_nasc,-2)."/".substr($paciente->data_nasc,5,2)."/".substr($paciente->data_nasc,0,4);
       $telefone=$paciente->telefone;
       $cpf=$paciente->cpf;
    }
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>

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
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='4'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_exclusao" action="./paciente_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="6" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Excluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" name="nome" size="60"  maxlength="70" value="<?php echo $nome;?>" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="18%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Dt. Nascimento
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <input type="text" name="data_nasc" size="10"  maxlength="10" value="<?php echo $data_nasc;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nome Mãe
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="50%">
                        <input type="text" name="mae" size="60" maxlength="70" value="<?php echo $mae;?>" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="18%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                         Sexo
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <select name="sexo" size="1"  style="width:85px;" disabled>
                          <option value="">Selecione</option>
                          <option value="F" <?php if($sexo=="F"){echo "selected";}?>>Feminino</option>
                          <option value="M" <?php if($sexo=="M"){echo "selected";}?>>Masculino</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        CPF
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                        <input type="text" name="cpf" size="30" maxlength="30" value="<?php echo $cpf;?>" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Telefone
                      </td>
                      <td class="campo_tabela" valign="middle" width="12%">
                        <input type="text" name="telefone" size="10" maxlength="12" value="<?php echo $telefone;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Tipo Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <select name="tipo_logradouro" size="1" style="width:100px;" disabled >
                          <option value="">Selecione</option>
                          <option value="Avenida" <?php if($tipo_logradouro=="Avenida"){echo "selected";}?>>Avenida</option>
                          <option value="Beco" <?php if($tipo_logradouro=="Beco"){echo "selected";}?>>Beco</option>
                          <option value="Caminho" <?php if($tipo_logradouro=="Caminho"){echo "selected";}?>>Caminho</option>
                          <option value="Estrada" <?php if($tipo_logradouro=="Estrada"){echo "selected";}?>>Estrada</option>
                          <option value="Ladeira" <?php if($tipo_logradouro=="Ladeira"){echo "selected";}?>>Ladeira</option>
                          <option value="Largo" <?php if($tipo_logradouro=="Largo"){echo "selected";}?>>Largo</option>
                          <option value="Lote" <?php if($tipo_logradouro=="Lote"){echo "selected";}?>>Lote</option>
                          <option value="Outro" <?php if($tipo_logradouro=="Outro"){echo "selected";}?>>Outro</option>
                          <option value="Praça" <?php if($tipo_logradouro=="Praça"){echo "selected";}?>>Praça</option>
                          <option value="Quadra" <?php if($tipo_logradouro=="Quadra"){echo "selected";}?>>Quadra</option>
                          <option value="Rodovia" <?php if($tipo_logradouro=="Rodovia"){echo "selected";}?>>Rodovia</option>
                          <option value="Rua" <?php if($tipo_logradouro=="Rua"){echo "selected";}?>>Rua</option>
                          <option value="Travessa" <?php if($tipo_logradouro=="Travessa"){echo "selected";}?>>Travessa</option>
                          <option value="Via" <?php if($tipo_logradouro=="Via"){echo "selected";}?>>Via</option>
                          <option value="Vila" <?php if($tipo_logradouro=="Vila"){echo "selected";}?>>Vila</option>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                         Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="50%" colspan="3">
                        <input type="text" name="logradouro" size="63" maxlength="50" value="<?php echo $logradouro;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                         Número
                      </td>
                      <td class="campo_tabela" valign="middle" width="15%">
                        <input type="text" name="numero" size="12" maxlength="7" value="<?php echo $numero;?>" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Complemento
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3">
                        <input type="text" name="complemento" size="63" maxlength="15" value="<?php echo $complemento;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Bairro
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                        <input type="text" name="bairro" size="45" maxlength="30" value="<?php echo $bairro;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Cidade
                      </td>
                      <td class="campo_tabela" valign="middle" width="80%" colspan="5">
                        <?php
                            $sql="select concat(cid.nome,'/',est.uf) as cidade
                                  from cidade cid
                                       inner join estado est on cid.estado_id_estado=est.id_estado
                                  where id_cidade = '$cidade_id_cidade'";
                            $cidade=mysqli_query($db, $sql);
                            erro_sql("Select Cidade", $db, "");
                            if(mysqli_num_rows($cidade)>0){
                              $listacidade=mysqli_fetch_object($cidade);
                            }
                        ?>
                        <input type="text" size="45" name="cidade_receita" value="<?php echo $listacidade->cidade; ?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CS Cadastro
                      </td>
                      <?php
                        $sql="select nome
                              from unidade
                              where id_unidade='$unidade_cadastro' and status_2='A'";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select CS Cadastro", $db, "");
                        if(mysqli_num_rows($res)>0){
                          $cs_cadastro=mysqli_fetch_object($res);
                        }
                      ?>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="2">
                        <input type="text" name="unidade_cadastro" value="<?php echo $cs_cadastro->nome;?>" size="30" disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        CS Unidade
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%" colspan="2">
                        <select name="unidade_referida" size="1" style="width:200px;" disabled>
                          <option value="">Selecione</option>
                            <?php
                              $sql="select id_unidade, nome
                                    from unidade
                                    where status_2 = 'A'  and flg_nivel_superior=0
                                    order by nome";
                              $unidade= mysqli_query($db, $sql);
                              erro_sql("Select CS Unidade", $db, "");
                              while($listaunidade=mysqli_fetch_object($unidade)){
                                $selecionado="";
                                if($listaunidade->id_unidade==$unidade_referida){
                                  $selecionado="selected";
                                }
                            ?>
                                <option value="<?php echo $listaunidade->id_unidade;?>" <?php echo $selecionado;?>> <?php echo $listaunidade->nome;?></option>
                            <?php
                              }
                            ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Situação
                      </td>
                      <td class="campo_tabela" colspan="6" valign="middle" width="80%">
                        <select name="situacao" size="1" style="width:205px;" disabled>
                          <option value=""> Selecione</option>
                            <?php
                              $sql="select id_status_paciente, descricao
                                    from status_paciente
                                    order by descricao";
                              $status=mysqli_query($db, $sql);
                              erro_sql("Select Situacao", $db, "");
                              while($listastatus=mysqli_fetch_object($status)){
                                $selecionado="";
                                if($listastatus->id_status_paciente==$id_status_paciente){
                                  $selecionado="selected";
                                }
                            ?>
                                <option value="<?php echo $listastatus->id_status_paciente;?>" <?php echo $selecionado;?>> <?php echo $listastatus->descricao;?></option>
                            <?php
                              }
                            ?>
                        </select>
                      </td>
                    </tr>
                    <TR>
		              <TD colspan="6">
     			        <table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="0">
					      <TR align="center">
						    <TD>Prontuário</TD>
                            <TD width="10">
                              <A href="javascript:showFrame('show_prontuario');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Prontuário"></A>
                            </TD>
				          </TR>
					    </TABLE>
		              </TD>
			        </TR>
			        <TR>
				      <TD colspan="6">
					    <div id="show_prontuario" style="display:none;">
					      <table border="0" width="100%" cellpadding="0" cellspacing="0">
						    <tr>
							  <td colspan="6">
							    <table width="100%" cellpadding="0" cellspacing="1">
                                  <tr bgcolor=#0E5A98>
								    <td width="15%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Nro</b></font></td>
								    <td width="75%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Unidade</b></font></td>
								  </tr>
                                  <?php
                                    $sql_prontuario= "select p.num_prontuario, u.nome, u.id_unidade
                                                  from prontuario as p, unidade as u
                                                  where p.paciente_id_paciente='$id_paciente'
                                                  and p.unidade_id_unidade=u.id_unidade
                                                  order by p.num_prontuario";
                                    $prontuarios=mysqli_query($db, $sql_prontuario);
                                    $qtde_prontuarios=mysqli_num_rows($prontuarios);
                                    
                                    erro_sql("Select Prontuário", $db, "");
                                    $contpront=1;
                                    $mesmaUnidade="S";
                                    while($listaprontuario = mysqli_fetch_object($prontuarios)){
                                  ?>
                                      <tr class="campo_tabela">
  								        <td align="left"><?php echo $listaprontuario->num_prontuario;?></td>
									    <td align="left"><?php echo $listaprontuario->nome;?></td>
  						              </tr>
                                  <?php
                                      $contpront++;
                                      
                                      if($listaprontuario->id_unidade != $_SESSION[id_unidade_sistema])
                                      {
                                        $mesmaUnidade="N";
                                      }
                                    }
                                  ?>
						        </table>
						      </td>
						    </tr>
					      </table>
				        </div>
				      </TD>
                    </TR>
                    
                    <TR>
		              <TD colspan="6">
     			        <table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="0">
					      <TR align="center">
						    <TD>Cartões SUS</TD>
                            <TD width="10">
                              <A href="javascript:showFrame('show_cartao');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Cartão SUS"></A>
                            </TD>
				          </TR>
					    </TABLE>
		              </TD>
			        </TR>
			        <TR>
				      <TD colspan="6">
					    <div id="show_cartao" style="display:none;">
					      <table border="0" width="100%" cellpadding="0" cellspacing="0">
						    <tr>
							  <td colspan="6">
							    <table width="100%" cellpadding="0" cellspacing="1">
                                  <tr bgcolor=#0E5A98>
								    <td width="15%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Nro</b></font></td>
								    <td width="75%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Cartão SUS</b></font></td>
								  </tr>
                                  <?php
                                    $sql_cartao="select cartao_sus
                                                  from cartao_sus
                                                  where paciente_id_paciente='$id_paciente'
                                                  order by cartao_sus";
                                    $cartoes=mysqli_query($db, $sql_cartao);
                                    erro_sql("Select Cartão SUS", $db, "");
                                    $contador=1;
                                    while($listacartao = mysqli_fetch_object($cartoes)){
                                  ?>
                                      <tr class="campo_tabela">
  								        <td align="left"><?php echo $contador;?></td>
									    <td align="left"><?php echo $listacartao->cartao_sus;?></td>
  						              </tr>
                                  <?php
                                      $contador++;
                                    }
                                  ?>
						        </table>
						      </td>
						    </tr>
					      </table>
				        </div>
				      </TD>
                    </TR>
                    <TR>
		              <TD colspan="6">
     			        <table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="0">
					      <TR align="center">
						    <TD>Atenção Continuada</TD>
                            <TD width="10"><A href="javascript:showFrame('show_atencao');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Atenção Continuada"></A></TD>
				          </TR>
					    </TABLE>
		              </TD>
			        </TR>
			        <TR>
				      <TD colspan="6">
					    <div id="show_atencao" style="display:none;">
					      <table border="0" width="100%" cellpadding="0" cellspacing="0">
						    <tr>
							  <td colspan="6">
							    <table width="100%" cellpadding="0" cellspacing="1">
                                  <tr bgcolor=#0E5A98>
								    <td width="15%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Código</b></font></td>
								    <td width="75%" align="center"><font color="#FFFFFF" face="arial" size="2"><b>Atenção Continuada</b></font></td>
								  </tr>
                                  <?php
                                    $sql_atencao="select a.id_atencao_continuada, a.descricao
                                                  from atencao_continuada a,
                                                       atencao_continuada_paciente p
                                                  where a.id_atencao_continuada=p.id_atencao_continuada
                                                        and p.id_paciente='$id_paciente'
                                                  order by a.descricao";
                                    $atencao=mysqli_query($db, $sql_atencao);
                                    erro_sql("Select Código/Atenção Continuada", $db, "");
                                    while($listaatencao = mysqli_fetch_object($atencao)){
                                  ?>
                                      <tr class="campo_tabela">
  								        <td align="left"><?php echo $listaatencao->id_atencao_continuada; ?></td>
									    <td align="left"><?php echo $listaatencao->descricao; ?></td>
  						              </tr>
                                  <?php
                                    }
                                  ?>
						        </table>
						      </td>
						    </tr>
					      </table>
				        </div>
				      </TD>
                    </TR>
                    <tr height="35">
                      <td colspan="6" align="right" class="descricao_campo_tabela">
                        <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/paciente/paciente_inicial.php?id_paciente=<?php echo $_GET[id_paciente];?>'">
                        <? if(($qtde_prontuarios==0) || ($mesmaUnidade=="S"))
                           {?>
                             <input style="font-size: 12px;" type="button" name="excluir"  value="Excluir >>"  onClick="document.form_exclusao.submit();">
                         <?}
                           else {?>
                             <input style="font-size: 12px;" type="button" name="excluir"  value="Excluir >>"  onClick="alert('Não é possível excluir! Há prontuários de outras unidades vinculados ao paciente!');">
                           <?}?>
                        <input type="hidden" name="id_paciente" value="<?php echo $id_paciente;?>">
                      </td>
                    </tr>
                    <tr height="21">
                      <td colspan="6" class="descricao_campo_tabela">
                        <table align="center" border="0" cellpadding="0" cellspacing="0">
				          <tr valign="top" class="descricao_campo_tabela">
					        <td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
                            <td>&nbsp&nbsp&nbsp</td>
                            <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
					      </tr>
				        </table>
                      </td>
			        </tr>
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

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
