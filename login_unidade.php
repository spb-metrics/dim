<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
//////////
//HEADER//
//////////
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//////////////////////////////////////////////////
//TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
//////////////////////////////////////////////////
if (file_exists("./config/config.inc.php"))
{
  require "./config/config.inc.php";

  ///////////////////////////////////////////
  //VERIFICANDO SE O USU�RIO J� EST� LOGADO//
  ///////////////////////////////////////////

 if($_SESSION[id_usuario_sistema]=='')
  {
	  header("Location: ". URL."/start.php");
  }

  ?>

     <HEAD>
       <SCRIPT LANGUAGE="JavaScript">

       function valida_campos()
       {
          if (document.login.unidade.value=="") {
             alert ('Selecione uma Unidade');
             document.login.unidade.focus();
             return false;
          }

          return true;
       }

      </SCRIPT>

      <TITLE><?php echo TIT;?></TITLE>
      <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
      <BODY>
        <TABLE name="centralizadora" align="center" width="100%" height="100%" border="0">
        <TR><TD align="center" valign="middle">
        <form name="login" id="login" action="startunidade.php" method="POST" enctype="application/x-www-form-urlencoded" onsubmit="return valida_campos()">
        <TABLE align="center" cellpadding="5" cellspacing="1" width="600" Bgcolor="#990000" border="0">
          <TR Bgcolor="#FFFFFF"">
            <TD>
              <TABLE width="100%">
                <TR>
                  <TD align="center" valign="bottom"><img src="./imagens/LOGOIMA.bmp"></TD>
                </TR>
              </TABLE>
            </TD>
          </TR>

          <TR>
            <TD align="center">
              <TABLE align="center" cellpadding="3" width="600" Border="0">
                <TR>
                  <TD width="50%" align="center">
                    <FONT face="arial" size="2"></FONT>
                  </TD>
                </TR>

                <TR>
                  <TD align="center">
                    <FONT class="aviso" color="#FFFFFF">Para efetuar login, informe o seu login e sua senha de acesso ao sistema</font>
                  </TD>
                </TR>

                <TR>
                  <TD align="right">
                    <FONT class="login">Login</FONT>
                       <input class="fields" type="text" name="login" size="20" maxlength="20" value="<?php echo $_SESSION[login_sistema];?>" disabled>
                  </TD>
                </TR>

                <TR>
                  <TD align="right">
                    <FONT class="login">Senha</FONT>
                    <input class="fields" type="password" name="senha" size="20" maxlength="20" value="<?php echo $_SESSION[senha_sistema];?>" disabled>
                  </TD>
                </TR>
                <TR>
                  <TD align="right">
                    <FONT class="login">Unidade</FONT>
                    <select size="1" name="unidade">
                       <option value="">Selecione uma unidade</option>
                    <?php
                         $sqlUnidade="select uu.*, un.nome
                                      from unidade un, unidade_has_usuario uu
                                      where uu.usuario_id_usuario = '$_SESSION[id_usuario_sistema]'
                                      and un.id_unidade = uu.unidade_id_unidade
                                      order by un.nome";
                         $unidade = mysqli_query($db, $sqlUnidade);
                         erro_sql("Login Unidade", $db, "");
                         while ($lista_unidade = mysqli_fetch_object($unidade))
                         {
                         ?>
                             <option value="<?php echo $lista_unidade->unidade_id_unidade; ?>"><?php echo $lista_unidade->nome; ?></option>
                         <?
                         }
                    ?>

                    </select>
                  </TD>
                </TR>


                <TR>
                  <TD align="right">
                    <input class="buttons" type="submit" name="acessar" value="acessar">
                  </TD>
                </TR>
                </TABLE>
                <TR>
                  <TD height="25" Bgcolor="#FFFFCC" align="center">
                    <FONT class="aviso" color="#990000">
                      <?php
                        echo $_SESSION["MSG_LOGIN"];
                      ?>
                    </FONT>
                  </TD>
                </TR>

                <TR>
                  <TD Bgcolor="#FFFFFF" align="center">
                    <FONT class="aviso" color="#990000">
                      <?php
                        echo "dia ".date("d/m/Y - H")."h".date("i")."m".date("s")."s";
                      ?>
                    </FONT>
                  </TD>
                </TR>
              </TABLE>
            </TD>
          </TR>
		</TABLE>
      <script language="JavaScript" type="text/JavaScript">
      //////////////////////////
      //DEFININDO FOCO INICIAL//
      //////////////////////////
      login.unidade.focus();
      </script>

	    </form name="login">
	    </TD></TR></TABLE name="centralizadora">
      </BODY>

  <?}

