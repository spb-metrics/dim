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
if (file_exists("./config.inc.php"))
{
  require "./config.inc.php";
  
  //////////////////////////////////////////
  //PROCURA POR USU�RIO E SENHA INFORMADOS//
  //////////////////////////////////////////
  
  $sql = "select *
          from usuario where login = '$_POST[login]'";
  $login = mysqli_query($db, $sql);
  erro_sql("usu�rio", $db, "");
  
  ///////////////////////
  //SE USU�RIO � V�LIDO//
  ///////////////////////
  if(mysqli_num_rows($login)>0)
  {
     $user = mysqli_real_escape_string($db, $_POST['login']);
     $pass = mysqli_real_escape_string($db, $_POST['senha']);
     $sql = "select * from usuario where login = '$user' and senha=old_password('$pass')";
 //echo "Post: ".$_POST[senha]." REAL_ESCAPE:". $pass;
 //    echo $sql;
 //    echo exit;
     
     $senha = mysqli_query($db, $sql);
     erro_sql("Usu�rio V�lido", $db, "");

     if(mysqli_num_rows($senha)>0)
     {

          $senha_usuario=mysqli_fetch_object($senha);
          
          //echo "**"$user_info->situacao;
//echo exit;
          if ($senha_usuario->situacao=="A")
          {
           $sql = "select us.situacao, us.id_usuario, us.login, us.senha, us.nome, un.id_unidade, un.nome as unidade
                   from usuario us, unidade un, unidade_has_usuario uu where
                   us.login = '$user'
                   and us.senha = old_password('$pass')
                   and un.id_unidade = uu.unidade_id_unidade
                   and us.id_usuario = uu.usuario_id_usuario";
//                 and us.situacao = 'A'

           $usuario_unidade=mysqli_query($db, $sql);
           erro_sql("Usu�rio Ativo", $db, "");
           $total = mysqli_num_rows($usuario_unidade);

           $user_info                        = mysqli_fetch_object($usuario_unidade);
  	       $_SESSION[nome_usuario_sistema]   = $user_info->nome;
           $_SESSION[id_usuario_sistema]     = $user_info->id_usuario;
           $_SESSION[login_sistema]          = $user_info->login;
           $_SESSION[senha_sistema]          = $user_info->senha;

           if($total==1)
           {

             $_SESSION[nome_unidade_sistema] = $user_info->unidade;
             $_SESSION[id_unidade_sistema]   = $user_info->id_unidade;
             
             $sql = "select * from unidade_has_usuario
                  where unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                  and usuario_id_usuario = '$_SESSION[id_usuario_sistema]'";
             $perfil = mysqli_query($db, $sql);
             erro_sql("Unidade Usu�rio", $db, "");

             if(mysqli_num_rows($perfil)>0)
             {
                $perfil_info         = mysqli_fetch_object($perfil);
                $_SESSION[id_perfil_sistema] = $perfil_info->perfil_id_perfil;
             }
             else
             {
               	   $_SESSION["MSG_LOGIN"] = "Usu�rio sem perfil!";
                   header("Location: ". URL);
             }
             
    	     header("Location: ". URL."/start.php");
           }
           else
           {
              $_SESSION["MSG_LOGIN"] = "";
    	      header("Location: ". URL."/login_unidade.php");
           }
          }
          else //usuario inativo
          {
      	   $_SESSION["MSG_LOGIN"] = "Usu�rio inativo!";
           header("Location: ". URL);
          }
     }
     else
     {
  	   $_SESSION["MSG_LOGIN"] = "Senha n�o confere!";
  	   header("Location: ". URL);
     }
  }
  /////////////////////////////////
  //SE N�O EXISTIR USU�RIO V�LIDO//
  /////////////////////////////////
  else 
  {
  	$_SESSION["MSG_LOGIN"] = "Login n�o confere!";
  	header("Location: ". URL);
  }
  
}
////////////////////////////////////////////
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else 
{
  include "./erro_config.php";
}
