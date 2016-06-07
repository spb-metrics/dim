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
  if (!file_exists($configuracao)){
    exit("N�o existe arquivo de configura��o!");
  }
  require $configuracao;


  $unidade=$_GET[unidade];
  $aplicacao=$_GET[aplicacao];
  $atualizacao="";
  $sql="delete
        from unidade_has_aplicacao
        where unidade_id_unidade='$unidade'";
  mysqli_query($db, $sql);
  if(mysqli_errno($db)!="0"){
    $atualizacao="erro";
  }
  if($aplicacao!=""){
    $valores=split("[|]", $aplicacao);
    for($i=0; $i<count($valores); $i++){
      $sql_cadastro = "insert into unidade_has_aplicacao
                       (unidade_id_unidade,
                        aplicacao_id_aplicacao)
                        values (
                        '$unidade',
                        '$valores[$i]')";
      mysqli_query($db, $sql_cadastro);
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
    }
  }
  if($atualizacao==""){
    mysqli_commit($db);
  }
  else{
    mysqli_rollback($db);
  }
  echo $atualizacao;
?>
