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
    $usuario = $_SESSION[id_usuario_sistema];


  $id_unidade=$_GET[unidade];
  $grupo=$_GET[grupo];
  $principal = $_GET[principal];
  $data_incl = date("Y-m-d H:i:s");
  $delecao = $_GET[ids];

  $atualizacao="";
  
  
  $sql="delete
        from unidade_grupo
        where unidade_id_unidade ='$id_unidade' and unidade_grupo_id_unidade_grupo in ($delecao)";
		
		//echo $sql;
  mysqli_query($db, $sql);
  if(mysqli_errno($db)!="0"){
    $atualizacao="erro";
  }
  
 // echo "valor de delecao".$delecao;
 
  
  if($grupo!=""){

    $valores=split("[|]", $grupo);
    for($i=0; $i<count($valores); $i++){
		$novo_grupo = split(",", $valores[$i]);
		  print_r($novo_grupo);
      $sql_cadastro = "insert into unidade_grupo
                       (unidade_id_unidade,
                        grupo_id_grupo,
						principal,
						data_incl,
						usua_incl
						)
                        values ('$id_unidade',
						'$novo_grupo[0]',
						'$novo_grupo[1]',
						'$data_incl',
						'$usuario'
						)";
						//echo $valores;
						//echo  $sql_cadastro;
						
						//echo $usuario;
						
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
