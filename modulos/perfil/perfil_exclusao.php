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

 if ($_GET[id_perfil]!="")
 {
    $sql_select = "select id_perfil, descricao, flg_adm from perfil where id_perfil = '".$_GET[id_perfil]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Perfil Escolhido", $db, "");
    $perfil  = mysqli_fetch_object($res);

    $id_perfil          = $perfil->id_perfil;
    $descricao          = $perfil->descricao;

 }
 else
 {
    if(isset($_POST[id_perfil]))
    {
   	   $sql_alteracao = "update perfil
   	                       set
                           status_2              = 'I',
                           data_alt              =  '".date("Y-m-d H:m:s")."',
                           usua_alt              = '$_SESSION[id_usuario_sistema]'
                        where id_perfil       = '$_POST[id_perfil]';";

       mysqli_query($db, $sql_alteracao);
       erro_sql("Update Perfil", $db, "");
       $atualizacao="";
       if(mysqli_errno($db)!="0"){
         $atualizacao="erro";
       }

       //alterar tabela perfil_has_aplicacao
       $sql_delete = "delete from perfil_has_aplicacao where perfil_id_perfil = '$_POST[id_perfil]'";
       mysqli_query($db, $sql_delete);
       erro_sql("Delete Perfil Has Aplicação", $db, "");
       if(mysqli_errno($db)!="0"){
         $atualizacao="erro";
       }

       if($atualizacao=="")
       {
        mysqli_commit($db);
        $aux=$_POST[aux];
        header("Location: ". URL."/modulos/perfil/perfil_inicial.php?e=t&".$aux);
       }
       else
       {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/perfil/perfil_inicial.php?e=f");
       }

    }
 }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";

  require DIR."/buscar_aplic.php";
?>

  <script language="JavaScript" type="text/JavaScript">

  <?php
    require "../../scripts/frame.js"; ?>

  </script>

  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
     <tr><td> <?php echo $caminho;?> </td></tr>
  </table>

  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        <table width="100%" class="titulo_tabela">
          <tr><td align="center"> <?php echo $nome_aplicacao;?>: Excluir</td></tr>
        </table>
      </td>
    </tr>

    <tr>
      <td height="100%" align="center" valign="top">

          <table  width="100%" border="0" cellpadding="0" cellspacing="1">
            <form name="cadastro" id="cadastro" action="./perfil_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Código
              </td>

              <td align="left" width="60%" class="campo_tabela">
                <input type="text" name="codigo" id="codigo" size="30" maxlength="10" <?php if (isset($id_perfil)){echo "value='".$id_perfil."'";}?> disabled>
              </td>

            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Perfil
              </td>

              <td align="left" width="60%" class="campo_tabela">
                <input type="text" name="descricao" id="descricao" size="80" maxlength="40" <?php if (isset($descricao)){echo "value='".$descricao."'";}?> disabled>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Autorizador
              </td>
              <td align="left" width="60%" class="campo_tabela">
                <input type="radio" name="autorizador" value="S" <?php if($perfil->flg_adm=="S"){echo "checked";}?> disabled> Sim
                &nbsp; &nbsp; &nbsp; &nbsp;
                <input type="radio" name="autorizador" value="" <?php if($perfil->flg_adm!="S"){echo "checked";}?> disabled> Não
              </td>
            </tr>

			<tr>
				<td colspan="2"></td>
			</tr>
			<TR valign="top">
				<TD colspan="2">
                    <table width="100%" class="titulo_tabela">
						<TR align="center">
							<TD>Aplicações</TD>
							<TD width="10"><A href="javascript:showFrame('aplicacao');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Aplicações"></A></TD>
						</TR>
					</TABLE>
				</TD>
			</TR>
            <?php
                 $cor_linha = "#CCCCCC";
                 $num_linha = 0;
            ?>
			<tr>
				<td colspan="2">
					<div id="aplicacao" style="display:'none';">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="2">
								<table width="100%">
                                    <tr class="descricao_campo_tabela">
										<td widht="60%" align="center"><b>Aplicações</b></td>
										<td widht="10%" align="center"><b>Incluir</b></td>
										<td widht="10%" align="center"><b>Alterar</b></td>
										<td widht="10%" align="center"><b>Excluir</b></td>
										<td widht="10%" align="center"><b>Consultar</b></td>
									</tr>
                                    <?php
                                       $sql_aplicacao = "select id_aplicacao, descricao from aplicacao where status_2 = 'A' order by descricao";
                                       $aplicacao = mysqli_query($db, $sql_aplicacao);
                                       erro_sql("Select Aplicação/Incluir/Alterar/Excluir/Consultar", $db, "");
                                       while ($listaaplicacao = mysqli_fetch_object($aplicacao))
                                       {
                                           $sql_perfilaplicacao = "select inclusao, alteracao, exclusao, consulta from perfil_has_aplicacao ";
                                           $sql_perfilaplicacao = $sql_perfilaplicacao . " where perfil_id_perfil = ". $id_perfil;
                                           $sql_perfilaplicacao = $sql_perfilaplicacao . " and aplicacao_id_aplicacao = ". $listaaplicacao->id_aplicacao;
                                           $perfilaplicacao    = mysqli_fetch_object(mysqli_query($db, $sql_perfilaplicacao));
                                           erro_sql("Select Perfil Has Aplicação", $db, "");

                                    ?>
                                   	      <tr valign="center" bgcolor="<?php echo $cor_linha;?>" onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?php echo $cor_linha; ?>';">
  										     <td class="campo_tabela" widht="60%" align="left"><?php echo $listaaplicacao->descricao; ?></td>

										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="inclusao"  disabled value="<?php echo $listaaplicacao->id_aplicacao;  ?>" <?php if ($perfilaplicacao->inclusao  == 'S'){echo "checked";} ?>></td>
             							     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="alteracao" disabled value="<?php echo $listaaplicacao->id_aplicacao;  ?>" <?php if ($perfilaplicacao->alteracao == 'S'){echo "checked";} ?>></td>
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="exclusao"  disabled value="<?php echo $listaaplicacao->id_aplicacao;  ?>" <?php if ($perfilaplicacao->exclusao  == 'S'){echo "checked";} ?>></td>
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="consulta"  disabled value="<?php echo $listaaplicacao->id_aplicacao;  ?>" <?php if ($perfilaplicacao->consulta  == 'S'){echo "checked";} ?>></td>

									      </tr>
                                          <?php
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
				</td>
			</tr>

            <tr>
              <td colspan="2" align="right" class="descricao_campo_tabela" height="35">
                <input type="button" name="voltar"  style="font-size: 12px;" value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/perfil/perfil_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                <input type="submit" name="excluir>>" style="font-size: 12px;" value="Excluir >>">
                <input type="hidden" name="id_perfil" id="id_perfil" <?php if (isset($id_perfil)){echo "value='".$id_perfil."'";}?> >

              </td>
            </tr>

    		<tr >
			  <td colspan="2" class="descricao_campo_tabela">
				<table align="center" border="0">
				       <tr valign="top" class="descricao_campo_tabela">
						<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
						<td>&nbsp&nbsp&nbsp</td>
                        <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
					   </tr>
				</table>
              </td>
			</tr>
            <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET[pesquisa]?>">
          </table>
      </td>
    </tr>
  </table>
<?php
  ////////////////////
  //RODAPÉ DA PÁGINA//
  ////////////////////
  ?>
    <script language="JavaScript" type="text/JavaScript">
    //////////////////////////
    //DEFININDO FOCO INICIAL//
    //////////////////////////
    //cadastro.descricao.focus();
    </script>
  <?php
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
