<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
    exit("Não existe arquivo de configuração!");
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

