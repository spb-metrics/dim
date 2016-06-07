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

//error_reporting(E_ALL);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;

  $numero=$_GET["numero"];
  $unidade=$_GET["unidade"];
  $sql="select fabricante_id_fabricante
        from parametro";
  $result=mysqli_query($db, $sql);
  erro_sql("Select Documento Inserido", $db, "");
  if(mysqli_num_rows($result)>0){
    $idfabricante=mysqli_fetch_object($result);
    if((int)$idfabricante->fabricante_id_fabricante>0){
      $sql="select *
            from movto_geral
            where num_documento='$numero' and unidade_id_unidade='$unidade'
                  and tipo_movto_id_tipo_movto='1'";
      $result=mysqli_query($db, $sql);
      erro_sql("Select Documento Inserido", $db, "");
      if(mysqli_num_rows($result)>0){
        $mensagem="NAO";
      }
      else{
        $mensagem="SAV";
      }
    }
    else{
      $mensagem="FAB";
    }
  }
  echo $mensagem;
?>

