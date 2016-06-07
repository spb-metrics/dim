<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

//error_reporting(E_ALL);
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  
  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $login=$_GET["login"];
  $senha=$_GET["senha"];
  $sql="select *
        from usuario where login='$login'";
  $result=mysqli_query($db, $sql);
  erro_sql("usuário", $db, "");
  if(mysqli_num_rows($result)<=0){
    $msg="login";
  }
  else{
    $user=mysqli_real_escape_string($db, $login);
    $pass=mysqli_real_escape_string($db, $senha);
    $sql="select *
          from usuario
          where login='$user' and senha=old_password('$pass')";
    $result_senha=mysqli_query($db, $sql);
    erro_sql("Usuário Válido", $db, "");
    if(mysqli_num_rows($result_senha)<=0){
      $msg="senha";
    }
    else{
      $senha_usuario=mysqli_fetch_object($result_senha);
      if($senha_usuario->situacao!="A"){
        $msg="inativo";
      }
      else{
        $sql="select us.situacao, us.id_usuario, us.login, us.senha, us.nome, un.id_unidade,
                     un.nome as unidade
              from usuario us, unidade un, unidade_has_usuario uu
              where us.login='$user'
                    and us.senha=old_password('$pass')
                    and un.id_unidade=uu.unidade_id_unidade
                    and us.id_usuario=uu.usuario_id_usuario";

        $usuario_unidade=mysqli_query($db, $sql);
        erro_sql("Usuário Ativo", $db, "");
        $total=mysqli_num_rows($usuario_unidade);

        $user_info=mysqli_fetch_object($usuario_unidade);
  	    $_SESSION["nome_usuario_sistema"]=$user_info->nome;
        $_SESSION["id_usuario_sistema"]=$user_info->id_usuario;
        $_SESSION["login_sistema"]=$user_info->login;
        $_SESSION["senha_sistema"]=$user_info->senha;
        if($total==1){
          $_SESSION["nome_unidade_sistema"]=$user_info->unidade;
          $_SESSION["id_unidade_sistema"]=$user_info->id_unidade;

          $sql="select *
                from unidade_has_usuario
                where unidade_id_unidade='$_SESSION[id_unidade_sistema]'
                      and usuario_id_usuario='$_SESSION[id_usuario_sistema]'";
          $perfil=mysqli_query($db, $sql);
          erro_sql("Unidade Usuário", $db, "");

          if(mysqli_num_rows($perfil)>0){
            $perfil_info=mysqli_fetch_object($perfil);
            $_SESSION[id_perfil_sistema]=$perfil_info->perfil_id_perfil;
            $msg="start";
          }
          else{
            $msg="perfil";
          }
        }
        else{
          $msg="unidade";
        }
      }
    }
  }
  echo $msg;
?>
