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

 $_SESSION[APLICACAO]=$_GET[aplicacao];

  ////////////////////////////
  //VERIFICAÇÃO DE SEGURANÇA//
  ////////////////////////////
 if($_SESSION[id_usuario_sistema]=='')
 {
  header("Location: ". URL."/start.php");
 }

 if (!isset($_POST[senha]))
 {
    $sql_select = "select id_usuario, nome, login, senha from usuario where id_usuario = '".$_SESSION[id_usuario_sistema]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Usuário Escolhido", $db, "");
    $usuario    = mysqli_fetch_object($res);

    $id_usuario            = $usuario->id_usuario;
    $nome                  = $usuario->nome;
    $login                 = $usuario->login;
    $senhabanco            = $usuario->senha;
 }
 else
 {

    //conferir se senha confere
    $sql_select = "select id_usuario, nome, login, senha from usuario where id_usuario = '".$_SESSION[id_usuario_sistema]."' and senha = old_password('".$_POST[senha]."')";
    $resultado = mysqli_query($db, $sql_select);
    erro_sql("Select Senha Não Confere", $db, "");
    if(mysqli_num_rows($resultado)==0)
    {
      echo"<script language='JavaScript' type='text/JavaScript'>";
      echo "alert('Senha não confere com a senha atual!');";
      echo "window.location='usuario_alterarsenha.php';";
      echo "</script>";
    }
    else
    {
     //alterar informações
     $sql_alteracao = "update usuario
                  set senha = old_password('$_POST[novasenha]')
                  where id_usuario = '$_SESSION[id_usuario_sistema]';";

     mysqli_query($db, $sql_alteracao);
     erro_sql("Update usuário", $db, "");
     if(mysqli_errno($db)=="0")
     {
        mysqli_commit($db);
        echo"<script language='JavaScript' type='text/JavaScript'>";
        echo "alert('Senha alterada com sucesso!');";
        echo "window.location='../../start.php';";
        echo "</script>";
     }
     else
     {
        mysqli_rollback($db);
        echo"<script language='JavaScript' type='text/JavaScript'>";
        echo "alert('Erro ao alterar senha!');";
        echo "window.location='usuario_alterarsenha.php';";
        echo "</script>";
     }
    }
  }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";

  if ($_GET[aplicacao] <> '')
  {
    $_SESSION[cod_aplicacao] = $_GET[aplicacao];
  }
  require DIR."/buscar_aplic.php";

  require "../../verifica_acesso.php";

?>

  <script language="JavaScript" type="text/JavaScript">

  function enviar()  // type=submit
  {
     // consistencias
     if (document.cadastro.senha.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.senha.focus();
        return false;
     }

     if (document.cadastro.novasenha.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.novasenha.focus();
        return false;
     }
     else
     {
       var novasenha = document.cadastro.novasenha.value;
       if (novasenha.length < 6)
       {
        alert ("A senha deve ter no mínimo 6 dígitos!");
        document.cadastro.novasenha.focus();
        return false;
       }
     }

     if (document.cadastro.confirmarsenha.value == "")
     {
        alert ("Preencher os campos obrigatórios!");
        document.cadastro.confirmarsenha.focus();
        return false;
     }

     if (document.cadastro.senha.value == document.cadastro.novasenha.value)
     {
        alert ("Senha Inválida!");
        document.cadastro.novasenha.focus();
        return false;
     }

     if (document.cadastro.novasenha.value != document.cadastro.confirmarsenha.value)
     {
        alert ("Senha Inválida!");
        document.cadastro.confirmarsenha.focus();
        return false;
     }

     return true;   // envia formulario
  }
  </script>

  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
     <tr><td> <?php echo $caminho;?> </td></tr>
  </table>

  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr class="titulo_tabela" height="21">
      <td colspan="3" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?> </td>
    </tr>
    <tr>
      <td height="100%" align="center" valign="top">

          <table border="0" cellpadding="0" cellspacing="1">
            <form name="cadastro" id="cadastro" action="./usuario_alterarsenha.php" method="POST" enctype="application/x-www-form-urlencoded">

            <tr>
              <td align="left" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Nome
              </td>

              <td align="left" colspan="3" class="campo_tabela">
                <input type="text" name="nome" id="nome" size="50" maxlength="60" <?php if (isset($nome)){echo "value='".$nome."'";}?> disabled>
              </td>

            </tr>

            <tr>
              <td align="left" width="35%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Login
              </td>

              <td align="left" width="15" class="campo_tabela">
                <input type="text" name="login" id="login" size="30" maxlength="15"<?php if (isset($login)){echo "value='".$login."'";}?> disabled>
              </td>

              <td align="left" width="35%" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Senha
              </td>

              <td align="left" width="15%" class="campo_tabela">
                <input type="password" name="senha" id="senha" size="30" maxlength="12">
                <input type="hidden" name="senhabanco" id="senhabanco" size="30" maxlength="12" <?php if (isset($senhabanco)){echo "value='".$senhabanco."'";}?> >
              </td>

            </tr>

            <tr>
              <td align="left" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Nova Senha
              </td>

              <td align="left" class="campo_tabela">
                <input type="password" name="novasenha" id="novasenha" size="30" maxlength="12" onKeyPress="return isCharAndNumKey(event);"> (apenas numeros ou letras)
              </td>

              <td align="left" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat.gif";?>">Confirmar Senha
              </td>

              <td align="left" class="campo_tabela">
                <input type="password" name="confirmarsenha" id="confirmarsenha" size="30" maxlength="12" onKeyPress="return isCharAndNumKey(event);"> (apenas numeros ou letras)
              </td>

            </tr>

            <tr>
              <td colspan="4" align="right" class="descricao_campo_tabela" height="35">
                <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/start.php'">
                <input style="font-size: 12px;" type="button" name="salvar" value="Salvar >>" onClick="if(enviar()){document.cadastro.submit();}" <?php if($alteracao_perfil==""){echo "disabled";}?>>
              </td>
            </tr>

    		<tr >
			  <td colspan="4" class="descricao_campo_tabela" height="21">
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
    cadastro.senha.focus();
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
