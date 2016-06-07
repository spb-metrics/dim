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
if (file_exists("./config.inc.php"))
{
  require "./config.inc.php";
  
  //////////////////////////////////////////
  //PROCURA POR USUÁRIO E SENHA INFORMADOS//
  //////////////////////////////////////////
  
  $sql = "select *
          from usuario where login = '$_POST[login]'";
  $login = mysqli_query($db, $sql);
  erro_sql("usuário", $db, "");
  
  ///////////////////////
  //SE USUÁRIO É VÁLIDO//
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
     erro_sql("Usuário Válido", $db, "");

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
           erro_sql("Usuário Ativo", $db, "");
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
             erro_sql("Unidade Usuário", $db, "");

             if(mysqli_num_rows($perfil)>0)
             {
                $perfil_info         = mysqli_fetch_object($perfil);
                $_SESSION[id_perfil_sistema] = $perfil_info->perfil_id_perfil;
             }
             else
             {
               	   $_SESSION["MSG_LOGIN"] = "Usuário sem perfil!";
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
      	   $_SESSION["MSG_LOGIN"] = "Usuário inativo!";
           header("Location: ". URL);
          }
     }
     else
     {
  	   $_SESSION["MSG_LOGIN"] = "Senha não confere!";
  	   header("Location: ". URL);
     }
  }
  /////////////////////////////////
  //SE NÃO EXISTIR USUÁRIO VÁLIDO//
  /////////////////////////////////
  else 
  {
  	$_SESSION["MSG_LOGIN"] = "Login não confere!";
  	header("Location: ". URL);
  }
  
}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else 
{
  include "./erro_config.php";
}
