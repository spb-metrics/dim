<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $autorizador = $_GET[autorizador];
  $senha       = $_GET[senha];

  $unidade = $_SESSION[id_unidade_sistema];

  $sql="select id_usuario
        from
               usuario
        where
               login = '$autorizador'
               and situacao = 'A'";
  $res=mysqli_query($db, $sql);
  $dados_res = mysqli_fetch_object($res);
  if(mysqli_num_rows($res)==0)
  {
   $pesq="6";
  }
  else
  {
   $id_autorizador = $dados_res->id_usuario;
   $sql = "select id_usuario
           from
                  usuario
           where
                  id_usuario = '$id_autorizador'
                  and senha = old_password('$senha')";
   $res=mysqli_query($db, $sql);
   $dados_res = mysqli_fetch_object($res);
   if(mysqli_num_rows($res)==0)
   {
    $pesq="7";
   }
   else
   {
    $sql = "select perfil_id_perfil
            from
                   unidade_has_usuario
            where
                   usuario_id_usuario = '$id_autorizador'
                   and unidade_id_unidade = '$unidade' ";
    $res=mysqli_query($db, $sql);
    $dados_res = mysqli_fetch_object($res);
    if(mysqli_num_rows($res)==0)
    {
     $pesq="8";
    }
    else
    {
     $perfil = $dados_res->perfil_id_perfil;
     $sql = "select flg_adm
             from
                    perfil
             where
                    id_perfil = '$perfil'";
     $res=mysqli_query($db, $sql);
     $dados_res = mysqli_fetch_object($res);
     if(mysqli_num_rows($res)==0)
     {
      $pesq="9";
     }
     else
     {
      if ($dados_res->flg_adm=="S")
      {
       $pesq="OK";
      }
      else
      {
       $pesq="10";
      }
     }
    }
   }
  }

  if($pesq!="OK")
  {
   echo "invalido";
  }
  else
  {
   echo $id_autorizador;
  }
?>
