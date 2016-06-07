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
 if($_SESSION[id_usuario_sistema]=='')
 {
  header("Location: ". URL."/start.php");
 }

 if ($_GET[id_perfil]!="")
 {
    $sql_select = "select id_perfil, descricao, flg_adm from perfil where id_perfil = '".$_GET[id_perfil]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Perfil Escolhido", $db, "");
    $perfil    = mysqli_fetch_object($res);

    $id_perfil          = $perfil->id_perfil;
    $descricao          = $perfil->descricao;

 }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA P�GINA//
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
        <table width="100%" class="titulo_tabela" height="21">
          <tr><td align="center"> <?php echo $nome_aplicacao?>: Detalhar</td></tr>
        </table>
      </td>
    </tr>

    <tr>
      <td height="100%" align="center" valign="top" >

          <table  width="100%" border="0" cellpadding="0" cellspacing="1">

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">C�digo
              </td>

              <td align="left" width="60%" class="campo_tabela">
                <input type="text" name="id_perfil" id="id_perfil" size="30" maxlength="10" <?php if (isset($id_perfil)){echo "value='".$id_perfil."'";}?> disabled>
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
                <input type="radio" name="autorizador" value="" <?php if($perfil->flg_adm!="S"){echo "checked";}?> disabled> N�o
              </td>
            </tr>

        	<form name="FormularioAplicacao" action="" method="POST" enctype="multipart/form-data">
			<tr>
				<td colspan="2"></td>
			</tr>
			<TR valign="top" class="TrLog">
				<TD colspan="2">
                    <table width="100%" class="titulo_tabela">
						<TR align="center">
							<TD>Aplica��es</TD>
							<TD width="10"><A href="javascript:showFrame('unidade');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informa��es de Unidade"></A></TD>
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
					<div id="unidade" style="display:'none';">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="2">
                                  <table width="100%" border="0" >
                                    <tr class="descricao_campo_tabela">
										<td widht="60%" align="center"><b>Aplica��es</b></td>
										<td widht="10%" align="center"><b>Incluir</b></td>
										<td widht="10%" align="center"><b>Alterar</b></td>
										<td widht="10%" align="center"><b>Excluir</b></td>
										<td widht="10%" align="center"><b>Consultar</b></td>
									</tr>
                                    <?php
                                       $sql_aplicacao = "select id_aplicacao, descricao from aplicacao where status_2 = 'A' order by descricao";
                                       $aplicacao = mysqli_query($db, $sql_aplicacao);
                                       erro_sql("Select Aplica��o/Incluir/Alterar/Excluir/Consultar", $db, "");
                                       while ($listaaplicacao = mysqli_fetch_object($aplicacao))
                                       {
                                           $sql_perfilaplicacao = "select inclusao, alteracao, exclusao, consulta from perfil_has_aplicacao ";
                                           $sql_perfilaplicacao = $sql_perfilaplicacao . " where perfil_id_perfil = ". $id_perfil;
                                           $sql_perfilaplicacao = $sql_perfilaplicacao . " and aplicacao_id_aplicacao = ". $listaaplicacao->id_aplicacao;
                                           $res=mysqli_query($db, $sql_perfilaplicacao);
                                           erro_sql("Select Perfil Has Aplica��o", $db, "");
                                           $perfilaplicacao    = mysqli_fetch_object($res);

                                    ?>
                                   	      <tr valign="center" bgcolor="<?php echo $cor_linha;?>" onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?php echo $cor_linha; ?>';">
  										     <td class="campo_tabela" widht="60%" align="left"><?php echo $listaaplicacao->descricao; ?></td>
  										     
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" disabled name="inclusao" <?php if ($perfilaplicacao->inclusao  == 'S'){echo "checked";} ?>></td>
             							     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" disabled name="alteracao"<?php if ($perfilaplicacao->alteracao == 'S'){echo "checked";} ?>></td>
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" disabled name="exclusao" <?php if ($perfilaplicacao->exclusao  == 'S'){echo "checked";} ?>></td>
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" disabled name="consulta" <?php if ($perfilaplicacao->consulta  == 'S'){echo "checked";} ?>></td>
										     
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
			</form>
			
            <tr height="35">
              <td colspan="2" align="right" class="descricao_campo_tabela">
                <input type="button" name="voltar" style="font-size: 12px;" value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/perfil/perfil_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
              </td>
            </tr>
    		<tr >
			  <td colspan="2" class="descricao_campo_tabela" height="21">
				<table align="center" border="0">
				       <tr valign="top" class="descricao_campo_tabela">
						<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigat�rios</td>
						<td>&nbsp&nbsp&nbsp</td>
                        <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos n�o Obrigat�rios</td>
					   </tr>
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
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else
{
  include_once "../../config/erro_config.php";
}
?>
