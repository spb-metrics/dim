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

 if ($_GET[id_item_menu]!="")
 {
    $sql_select = "select id_item_menu, item_menu_id_item_menu, aplicacao_id_aplicacao, descricao, ordem, bloqueado from item_menu where id_item_menu = '".$_GET[id_item_menu]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Item Menu Escolhido", $db, "");
    $item_menu    = mysqli_fetch_object($res);

    $id_item_menu             = $item_menu->id_item_menu;
    $item_menu_id_item_menu   = $item_menu->item_menu_id_item_menu;
    $aplicacao_id_aplicacao   = $item_menu->aplicacao_id_aplicacao;
    $descricao                = $item_menu->descricao;
    $ordem                    = $item_menu->ordem;
    $bloqueado                = $item_menu->bloqueado;
 }
 else
 {
    if(isset($_POST[id_item_menu]))
    {
   	     $sql_exclusao = "update item_menu
   	                   set status_2       = 'I',
                       data_alt           =  '".date("Y-m-d H:m:s")."',
                       usua_alt           = '$_SESSION[id_usuario_sistema]'
                       where id_item_menu = '$_POST[id_item_menu]';";
         mysqli_query($db, $sql_exclusao);
         erro_sql("Update Item Menu", $db, "");
         if(mysqli_errno($db)=="0")
         {
          mysqli_commit($db);
          $aux=$_POST[aux];
          header("Location: ". URL."/modulos/usuario/configurarmenu_inicial.php?e=t");
         }
         else
         {
          mysqli_rollback($db);
          header("Location: ". URL."/modulos/usuario/configurarmenu_inicial.php?e=f");
         }
    }
 }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
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
              <form name="cadastro" id="cadastro" action="./configurarmenu_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Código
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="id_item_menu" id="id_item_menu" size="30" maxlength="10" <?php if (isset($id_item_menu)){echo "value='".$id_item_menu."'";}?> readonly>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Módulo
              </td>
              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="descricao" id="descricao" size="80" maxlength="120" <?php if (isset($descricao)){echo "value='".$descricao."'";}?> disabled>
              </td>
            </tr>
            
            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Aplicação
              </td>
              <td align="left" width="70%" height="25" class="campo_tabela">
                <select size="1" name="aplicacao" style="width:200px;" disabled>
                      <?php
                           $sql = "select id_aplicacao, descricao from aplicacao where status_2 = 'A' order by descricao";
                           $aplicacao = mysqli_query($db, $sql);
                           erro_sql("Select Aplicação", $db, "");
                       if(mysqli_num_rows($aplicacao)>0)
                       {
                          while ($lista_aplicacao = mysqli_fetch_object($aplicacao))
                          {

                             if ($lista_aplicacao->id_aplicacao == $aplicacao_id_aplicacao)
                          {?>
                                <option value="<?php echo $lista_aplicacao->id_aplicacao; ?>" selected><?php echo $lista_aplicacao->descricao; ?></option>
                          <?}
                            else{?>
                                <option value="<?php echo $lista_aplicacao->id_aplicacao; ?>"><?php echo $lista_aplicacao->descricao; ?></option>
                              <?}
                          }
                      }?>

                </select>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Módulo Pai
              </td>
              <td align="left" width="70%" height="25" class="campo_tabela">
                <select size="1" name="item_menu_id_item_menu" style="width:200px;" disabled>
                      <?php
                           $sql = "select id_item_menu, descricao from item_menu where status_2 = 'A' order by id_item_menu;";
                           $item_menu = mysqli_query($db, $sql);
                           erro_sql("Select módulo Pai", $db, "");

                       if(mysqli_num_rows($item_menu)>0)
                       {
                          while ($lista_item_menu = mysqli_fetch_object($item_menu))
                          {
                           if ($item_menu_id_item_menu == "0" )
                           {?>
                            <option value="0" selected></option>
                           <?}
                           else
                           {
                             if ($lista_item_menu->id_item_menu == $item_menu_id_item_menu )
                             {?>
                              <option value="<?php echo $lista_item_menu->id_item_menu; ?>" selected><?php echo $lista_item_menu->descricao; ?></option>
                              <?}
                              else
                              {?>
                                 <option value="<?php echo $lista_item_menu->id_item_menu; ?>"><?php echo $lista_item_menu->descricao; ?></option>
                              <?}
                           }
                          }
                      }?>
                </select>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Num. Ordem
              </td>
              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="ordem" id="ordem" size="30" maxlength="10" <?php if (isset($ordem)){echo "value='".$ordem."'";}?> disabled>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Bloqueado
              </td>
              <td align="left" width="70%" height="25" class="campo_tabela">
                <select size="1" name="bloqueado" style="width:100px;" disabled>
                      <option value="N" <?php if ($bloqueado=="N") { echo "selected";} ?>>Não</option>
                      <option value="S" <?php if ($bloqueado=="S") { echo "selected";} ?>>Sim</option>
                </select>
              </td>
            </tr>

            <tr>
              <td colspan="2" align="right" class="descricao_campo_tabela" height="35">
                <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/usuario/configurarmenu_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                <input style="font-size: 12px;" type="submit" name="excluir"  value="Excluir >>" >
              </td>
            </tr>

    		<tr >
			  <td colspan="2" class="descricao_campo_tabela" height="21">
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
			</form>
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
    //cadastro.modulo.focus();
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
