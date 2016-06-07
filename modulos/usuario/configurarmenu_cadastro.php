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

 if (isset($_POST[descricao]))
 {
    $sql_cadastro = "insert into item_menu
                  (item_menu_id_item_menu,
                   aplicacao_id_aplicacao,
                   descricao,
                   ordem,
                   bloqueado,
                   status_2,
                   data_incl,
                   usua_incl) values (
                   '$_POST[item_menu_id_item_menu]',
                   '$_POST[aplicacao]',
                   '$_POST[descricao]',
                   '$_POST[ordem]',
                   '$_POST[bloqueado]',
                   'A',
                   '".date("Y-m-d H:m:s")."',
                   '$_SESSION[id_usuario_sistema]');";
    //echo $sql_cadastro;
    //echo exit;
    mysqli_query($db, $sql_cadastro);
    erro_sql("Insert Item Menu", $db, "");

         if(mysqli_errno($db)=="0")
         {
          mysqli_commit($db);
          header("Location: ". URL."/modulos/usuario/configurarmenu_inicial.php?i=t");
         }
         else
         {
          mysqli_rollback($db);
          header("Location: ". URL."/modulos/usuario/configurarmenu_inicial.php?i=f");
         }
 }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";

  require DIR."/buscar_aplic.php";
?>
  <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
  <script language="JavaScript" type="text/JavaScript">

  function enviar()  // type=submit
  {
     // consistencias

     if (document.cadastro.descricao.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.descricao.focus();
        return false;
     }
     if (document.cadastro.aplicacao.selectedIndex == 0)
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.aplicacao.focus();
        return false;
     }
     if (document.cadastro.ordem.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.ordem.focus();
        return false;
     }

     return true;   // envia formulario
  }
  </script>
  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
     <tr><td> <?php echo $caminho;?> </td></tr>
  </table>

  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr height="5%">
      <td>
        <table width="100%" class="titulo_tabela" height="21">
          <tr><td align="center"> <?php echo $nome_aplicacao;?>: Incluir</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <form name="cadastro" id="cadastro" action="./configurarmenu_cadastro.php" method="POST" enctype="application/x-www-form-urlencoded">

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Código
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="id_item_menu" id="id_item_menu" size="30" maxlength="10" disabled>
              </td>

            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Módulo
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="descricao" id="descricao" size="80" maxlength="120">
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" height="25" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Aplicação
              </td>
              <td align="left" width="70%" bgcolor="#D4DFED">
                <select size="1" name="aplicacao" style="width:200px;">
                     <option value="">Selecione uma aplicação</option>
                      <?php
                           $sql = "select id_aplicacao, descricao from aplicacao where status_2 = 'A' order by descricao;";
                           $aplicacao = mysqli_query($db, $sql);
                           erro_sql("Select Aplicação", $db, "");

                       if(mysqli_num_rows($aplicacao)>0)
                       {
                          while ($lista_aplicacao = mysqli_fetch_object($aplicacao))
                          {?>
                                <option value="<?php echo $lista_aplicacao->id_aplicacao; ?>"><?php echo $lista_aplicacao->descricao; ?></option>
                          <?}
                      }?>
                </select>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" height="25" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Módulo Pai
              </td>

              <td align="left" width="70%" class="campo_tabela">
                <select size="1" name="item_menu_id_item_menu" style="width:200px;">
                     <option value="">Selecione um Pai</option>
                     <option value="0"></option>
                      <?php
                           $sql = "select id_item_menu, descricao from item_menu where status_2 = 'A' order by descricao;";
                           $item_menu = mysqli_query($db, $sql);
                           erro_sql("Select Módulo Pai", $db, "");

                       if(mysqli_num_rows($item_menu)>0)
                       {
                          while ($lista_item_menu = mysqli_fetch_object($item_menu))
                          {?>
                                <option value="<?php echo $lista_item_menu->id_item_menu; ?>"><?php echo $lista_item_menu->descricao; ?></option>
                          <?}
                      }?>
                </select>
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Num. Ordem
              </td>
              <td align="left" width="70%" class="campo_tabela">
                <input type="text" name="ordem" id="ordem" size="30" maxlength="10" onKeyPress="return isNumberKey(event);" >
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Bloqueado
              </td>
              <td align="left" width="70%" height="25" class="campo_tabela">
                <select size="1" name="bloqueado" style="width:100px;">
                      <option value="N">Não</option>
                      <option value="S">Sim</option>
                </select>
              </td>
            </tr>

            <tr>
              <td colspan="2" align="right" class="descricao_campo_tabela" height="35">
                <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/usuario/configurarmenu_inicial.php'">
                <input style="font-size: 12px;" type="button" name="salvar" value="Salvar >>" onClick="if(enviar()){document.cadastro.submit();}">
              </td>
            </tr>

    		<tr >
			  <td colspan="2" class="descricao_campo_tabela"  height="21">
				<table align="center" border="0">
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
    cadastro.descricao.focus();
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
