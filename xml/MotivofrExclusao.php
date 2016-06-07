<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//////////
//HEADER//
//////////
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();

  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  $id=$_GET[codigo];
  $sql="Select motivo_fim_receita_id_motivo_fim_receita, status from itens_receita where motivo_fim_receita_id_motivo_fim_receita = $id";
 
  $result=mysqli_query($db, $sql);

  if(mysqli_num_rows($result)>0){
    $mensagem="NAO";
  }
  else{
      $data=date("Y-m-d H:i:s");
      $sql="update motivo_fim_receita set
                   status_2='I',
                   data_alt='$data',
                   usua_alt='$_SESSION[id_usuario_sistema]'
            where idmotivo_fim_receita=$id";
      mysqli_query($db, $sql);

        /////////////////////////////////////
        //SE INCLUS�O OCORREU SEM PROBLEMAS//
        /////////////////////////////////////
        if(mysqli_errno($db)=="0"){
          $mensagem="SAV";
          mysqli_commit($db);
        }
        else{
           $mensagem="ERR";
           mysqli_rollback($db);
           }
      }
  echo $mensagem;
?>

