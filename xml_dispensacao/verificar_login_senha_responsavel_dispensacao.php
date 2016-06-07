<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

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
  
  $login=$_GET[login];
  $senha=$_GET[senha];
  $sql="select
              id_usuario
        from
              usuario
        where
              situacao='A'
              and login='$login'
              and senha=old_password('$senha')";
  $res=mysqli_query($db, $sql);
  erro_sql("Unidade", $db, "");
  if(mysqli_num_rows($res)>0)
  {
    $id_login=mysqli_fetch_object($res);
    $msg="sim_login_senha_responsavel_dispensacao@$id_login->id_usuario";
  }
  else
  {
    $msg="nao_login_senha_responsavel_dispensacao@";
  }
  echo $msg;
?>
