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
	require ($configuracao);

	$descricao  = $_GET[descricao];
	$substituir = "/";
	$descricao = ereg_replace("_", $substituir, $descricao);
	$descricao = trim($descricao);

	// EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU

	$sql = "select TRIM(CONCAT(c.nome, CONCAT('/',e.uf))) as descricao
		from cidade c
			inner join estado e
				on c.estado_id_estado = e.id_estado
		where CONCAT(c.nome, CONCAT('/',e.uf)) = '$descricao'"
	;

	$rsCidade = mysqli_query($db, $sql);

	if(mysqli_num_rows($rsCidade)>0){
		echo 'cidade_found';
	} else {
		echo 'cidade_not_found';
	}

?>
