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
  if (!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
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
