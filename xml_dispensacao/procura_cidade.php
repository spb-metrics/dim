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
	require ($configuracao);

	$descricao  = $_GET[descricao];
	$substituir = "/";
	$descricao = ereg_replace("_", $substituir, $descricao);
	$descricao = trim($descricao);

	// EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU

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
