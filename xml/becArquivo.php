<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//error_reporting(E_ALL);
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");

  $id_unidade=$_GET["id"];
  //verificando se a aplicacao do CRONTAB esta em execucao
  $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
  //$ARQ_TRAVA="/home/dike/public_html/arquivos_conversao_tabnet/arquivos_novos/ARQUIVO_TRAVA_UNIDADE_";
  $ARQ_EXTENSAO=".TXT";
  $str=$ARQ_TRAVA  . $id_unidade . $ARQ_EXTENSAO;
  if(file_exists($str)){
    $msg="EXI";
  }
  else{
    //criando o arquivo de trava
    $arq_aux=@fopen($str, "w");
    if($arq_aux){
      fclose($arq_aux);
      $msg="CRI";
    }
    else
    {
     $msg="Err";
    }
  }
  echo $msg;
?>
