<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
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
