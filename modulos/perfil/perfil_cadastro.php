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
 
    $sql_cadastro = "insert into perfil
                  (descricao,
                   status_2,
                   data_incl,
                   usua_incl,
                   flg_adm) values (
                   '$_POST[descricao]',
                   'A',
                   '".date("Y-m-d H:m:s")."',
                   '$_SESSION[id_usuario_sistema]',
                   '$_POST[autorizador]');";
    mysqli_query($db, $sql_cadastro);
    erro_sql("Insert Perfil", $db, "");
    $atualizacao="";
    if(mysqli_errno($db)!="0"){
      $atualizacao="erro";
    }

    $sql_select = "select max(id_perfil) as codigo from perfil";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select ID Perfil", $db, "");
    $perfil = mysqli_fetch_object($res);

    $id_perfil = $perfil->codigo;

    if ($_POST["inclusao"]!="")
    {
       foreach($_POST["inclusao"] as $modulo)
       {
          $sql_insert = "insert into perfil_has_aplicacao (
                       perfil_id_perfil, aplicacao_id_aplicacao, inclusao, data_incl, usua_incl) values (
                       '$id_perfil',
                       '$modulo',
                       'S',
                       '".date("Y-m-d H:m:s")."',
                       '$_SESSION[id_usuario_sistema]');";

          mysqli_query($db, $sql_insert);
          erro_sql("Insert Peril Has Aplicação", $db, "");
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }
       }
    }

    if ($_POST["alteracao"]!="")
    {
       foreach($_POST["alteracao"] as $modulo)
       {
        $sql_select = "select perfil_id_perfil from perfil_has_aplicacao
                      where perfil_id_perfil = '$id_perfil'
                      and aplicacao_id_aplicacao = '$modulo'";
        $alteracao = mysqli_query($db, $sql_select);
        erro_sql("Select Update/Insert Perfil Has Aplicação - Alteração", $db, "");

        if(mysqli_num_rows($alteracao)>0)
        {
           $sql_update = "update perfil_has_aplicacao set
                         alteracao = 'S'
                         where perfil_id_perfil = '$id_perfil'
                         and aplicacao_id_aplicacao = '$modulo'";

           mysqli_query($db, $sql_update);
           erro_sql("Update Perfil has Aplicação - Alteração", $db, "");
        }
        else
        {
           $sql_insert = "insert into perfil_has_aplicacao (
                       perfil_id_perfil, aplicacao_id_aplicacao, alteracao, data_incl, usua_incl) values (
                       '$id_perfil',
                       '$modulo',
                       'S',
                       '".date("Y-m-d H:m:s")."',
                       '$_SESSION[id_usuario_sistema]')";

           mysqli_query($db, $sql_insert);
           erro_sql("Insert Perfil Has Aplicação - Alteração", $db, "");
        }
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }

       }
    }

       if ($_POST["exclusao"]!="")
       {
         foreach($_POST["exclusao"] as $modulo)
         {
          $sql_select = "select perfil_id_perfil from perfil_has_aplicacao
                        where perfil_id_perfil = '$id_perfil'
                        and aplicacao_id_aplicacao = '$modulo'";

          $exclusao = mysqli_query($db, $sql_select);
          erro_sql("Select Update/Insert Perfil Has Aplicação - Exclusão", $db, "");

          if(mysqli_num_rows($exclusao)>0)
          {
             $sql_update = "update perfil_has_aplicacao set
                           exclusao = 'S'
                           where perfil_id_perfil = '$id_perfil'
                           and aplicacao_id_aplicacao = '$modulo'";

             mysqli_query($db, $sql_update);
             erro_sql("Update Perfil Has Aplicação - Exclusão", $db, "");
          }
          else
          {
             $sql_insert = "insert into perfil_has_aplicacao (
                         perfil_id_perfil, aplicacao_id_aplicacao, exclusao, data_incl, usua_incl) values (
                         '$id_perfil',
                         '$modulo',
                         'S',
                         '".date("Y-m-d H:m:s")."',
                         '$_SESSION[id_usuario_sistema]')";

             mysqli_query($db, $sql_insert);
             erro_sql("Insert Perfil Has Aplicação - Exclusão", $db, "");
          }
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }

         }
       }

       if ($_POST["consulta"]!="")
       {
         foreach($_POST["consulta"] as $modulo)
         {
          $sql_select = "select perfil_id_perfil from perfil_has_aplicacao
                        where perfil_id_perfil = '$id_perfil'
                        and aplicacao_id_aplicacao = '$modulo'";
        //echo $sql_select;
        //echo exit;

          $consulta = mysqli_query($db, $sql_select);
          erro_sql("Select Update/Insert Perfil Has Aplicação - Consulta", $db, "");

          if(mysqli_num_rows($consulta)>0)
          {
             $sql_update = "update perfil_has_aplicacao set
                           consulta = 'S'
                           where perfil_id_perfil = '$id_perfil'
                           and aplicacao_id_aplicacao = '$modulo'";

             mysqli_query($db, $sql_update);
             erro_sql("Update Perfil Has Aplicação - Consulta", $db, "");
          }
          else
          {
             $sql_insert = "insert into perfil_has_aplicacao (
                         perfil_id_perfil, aplicacao_id_aplicacao, consulta, data_incl, usua_incl) values (
                         '$id_perfil',
                         '$modulo',
                         'S',
                         '".date("Y-m-d H:m:s")."',
                         '$_SESSION[id_usuario_sistema]')";

             mysqli_query($db, $sql_insert);
             erro_sql("Insert Perfil Has Aplicação - Consulta", $db, "");
          }
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }

         }
       }
       if($atualizacao=="")
       {
        mysqli_commit($db);
        header("Location: ". URL."/modulos/perfil/perfil_inicial.php?i=t");
       }
       else
       {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/perfil/perfil_inicial.php?i=f");
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

  function enviar()  // type=submit
  {  
     var ok;

     if (document.cadastro.descricao.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.descricao.focus();
        return false;
     }

   for (var i=0;i<document.cadastro.elements.length;i++)
   {
     var x = document.cadastro.elements[i];
     if (x.name == 'inclusao[]') { if (x.checked){ok = '1';}}
     if (x.name == 'alteracao[]') { if (x.checked){ok = '1';}}
     if (x.name == 'consulta[]') { if (x.checked){ok = '1';}}
     if (x.name == 'exlusao[]') { if (x.checked){ok = '1';}}
   }
   
   if (ok != '1')
   {
      alert ("Pelo menos uma Aplicação deve ser selecionada!");
      document.cadastro.descricao.focus();
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
            <form name="cadastro" id="cadastro" action="./perfil_cadastro.php" method="POST" enctype="application/x-www-form-urlencoded">
            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Código
              </td>

              <td align="left" width="60%" class="campo_tabela">
                <input type="text" name="id_perfil" id="id_perfil" size="30" maxlength="10" disabled>
              </td>

            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Perfil
              </td>

              <td align="left" width="60%" class="campo_tabela">
                <input type="text" name="descricao" id="descricao" size="80" maxlength="40">
              </td>
            </tr>

            <tr>
              <td align="left" width="30%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Autorizador
              </td>
              <td align="left" width="60%" class="campo_tabela">
                <input type="radio" name="autorizador" value="S"> Sim
                &nbsp; &nbsp; &nbsp; &nbsp;
                <input type="radio" name="autorizador" value="" checked> Não
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
                                    ?>
                                   	      <tr valign="center" bgcolor="<?php echo $cor_linha;?>" onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?php echo $cor_linha; ?>';">
  										     <td class="campo_tabela" widht="60%" align="left"><?php echo $listaaplicacao->descricao; ?></td>

										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="inclusao[]"  value="<?php echo $listaaplicacao->id_aplicacao;  ?>" ></td>
             							     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="alteracao[]" value="<?php echo $listaaplicacao->id_aplicacao;  ?>" ></td>
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="exclusao[]"  value="<?php echo $listaaplicacao->id_aplicacao;  ?>" ></td>
										     <td class="campo_tabela" widht="10%" align="center"><input type="checkbox" name="consulta[]"  value="<?php echo $listaaplicacao->id_aplicacao;  ?>" ></td>

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
              <td colspan="2" align="right" class="descricao_campo_tabela"  height="35">
                <input type="button" name="voltar"  value="<< Voltar"  style="font-size: 12px;" onClick="window.location='<?php echo URL;?>/modulos/perfil/perfil_inicial.php'">
                <input type="button" name="salvar>>" value="Salvar >>" style="font-size: 12px;"  onClick="if(enviar()){document.cadastro.submit();}">
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
