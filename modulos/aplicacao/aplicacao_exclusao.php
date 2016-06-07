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

 if ($_GET[id_aplicacao]!="")
 {
    $sql_select = "select id_aplicacao, executavel, descricao, mostrar_resp_ope from aplicacao where id_aplicacao = '".$_GET[id_aplicacao]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Aplica��o Escolhida", $db, "");
    $aplicacao    = mysqli_fetch_object($res);

    $id_aplicacao          = $aplicacao->id_aplicacao;
    $executavel            = $aplicacao->executavel;
    $descricao             = $aplicacao->descricao;
    $mostrar_responsavel_aplicacao=$aplicacao->mostrar_resp_ope;

 }
 else
 {
    if(isset($_POST[id_aplicacao]))
    {
   	     $sql_exclusao = "update aplicacao
   	                   set status_2       = 'I',
                       data_alt           =  '".date("Y-m-d H:m:s")."',
                       usua_alt           = '$_SESSION[id_usuario_sistema]'
                       where id_aplicacao = '$_POST[id_aplicacao]';";
         mysqli_query($db, $sql_exclusao);
         erro_sql("Update Aplica��o", $db, "");

         if(mysqli_errno($db)=="0")
         {
          mysqli_commit($db);
          header("Location: ". URL."/modulos/aplicacao/aplicacao_inicial.php?e=t");
         }
         else
         {
          mysqli_rollback($db);
          header("Location: ". URL."/modulos/aplicacao/aplicacao_inicial.php?e=f");
         }
    }
 }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA P�GINA//
  ////////////////////////////////////
  require DIR."/header.php";

  require DIR."/buscar_aplic.php";
?>

  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
     <tr><td> <?php echo $caminho;?> </td></tr>
  </table>

  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr height="5%">
      <td>
        <table width="100%" class="titulo_tabela" height="21">
          <tr><td align="center"> <?php echo $nome_aplicacao;?>: Excluir</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <form name="cadastro" id="cadastro" action="./aplicacao_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">C�digo
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="id_aplicacao" id="id_aplicacao" size="30" maxlength="10" <?php if (isset($id_aplicacao)){echo "value='".$id_aplicacao."'";}?> disabled>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Execut�vel
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="executavel" id="executavel" size="80" maxlength="40" <?php if (isset($executavel)){echo "value='".$executavel."'";}?> disabled>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Aplica��o
              </td>
              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="descricao" id="descricao" size="80" maxlength="40" <?php if (isset($descricao)){echo "value='".$descricao."'";}?> disabled>
              </td>
            </tr>

            <tr>
              <td align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat.gif";?>"> Identifica��o do Respons�vel pela Dispensa��o
              </td>
              <td align="left" width="25%"  class="campo_tabela" colspan="3">
                <input type="radio" name="mostrar_responsavel_aplicacao" value="S" disabled <?php if($mostrar_responsavel_aplicacao=="S"){echo "checked";}?>>Sim &nbsp&nbsp&nbsp
                <input type="radio" name="mostrar_responsavel_aplicacao" value="" disabled <?php if($mostrar_responsavel_aplicacao==""){echo "checked";}?>>N�o
              </td>
            </tr>

            <tr>
              <td colspan="2" align="right" class="descricao_campo_tabela" height="35">
                <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/aplicacao/aplicacao_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                <input style="font-size: 12px;" type="submit" name="excluir"  value="Excluir >>" >
                <input type="hidden" name="id_aplicacao" id="id_aplicacao" <?php if (isset($id_aplicacao)){echo "value='".$id_aplicacao."'";}?> >
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
			<input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET[pesquisa]?>">
			</form>
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
    //cadastro.executavel.focus();
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
