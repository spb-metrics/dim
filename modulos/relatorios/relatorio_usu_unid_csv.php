<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2011             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: relatorio_usu_unid_csv.php                                |
// | Autor ..............: Leon Watanabe <leon.watanabe@ima.sp.gov.br>               |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Relat�rio de Usu�rios por unidade (.csv)					 |
// | Data de Cria��o ....: 28/03/2011												 |
// | �ltima Atualiza��o .: 30/03/2011												 |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

function busca_nivel($und_sup, $link, &$unidades)
{
	//global $unidades;
	if (empty($und_sup)) {
		$und_sup = $_SESSION[id_unidade_sistema];		
	}
	
	$sql = "select id_unidade, unidade_id_unidade, sigla, nome, flg_nivel_superior
	from unidade
	where unidade_id_unidade = '$und_sup'
	and status_2 = 'A'";
	  
	$sql_query = mysqli_query($link, $sql);
	erro_sql("Busca N�vel", $link, "");
	echo mysqli_error($link);
	while ($linha = mysqli_fetch_array($sql_query)){
		$und_sup01 = $linha['id_unidade'];
		
		if (!empty($unidades)) {
			$unidades .= ",".$und_sup01;
		} else {
			$unidades .= $und_sup01;
		}
		   
		busca_nivel($und_sup01, $link, $unidades);
	}
	
	return $und_sup;
}

function montaLinha(&$file, $dados) {
	foreach($dados as $dado) {
		$file .= $dado.";";
	}
	
	$file .= "\n";
}


if (file_exists("../../config/config.inc.php")) {
	require "../../config/config.inc.php";
	
	set_time_limit(0);

	//(1) Obtencao dos criterios de busca(inicio)
	$idUnidade = $_POST['unidade'];
	$descUnidade = trim($_POST['unidade01']);
	$descUnidadeLogada = $_POST['unidade02'];
	$descUnidade = empty($descUnidade) ? 'TODAS ('.$descUnidadeLogada.')' : $descUnidade;
	$perfil = explode(';', $_POST['operacao']);
	$idPerfil = $perfil[0];
	$descPerfil = $perfil[1];
	$idUsuario = $_POST['usuario'];
	$descUsuario = trim($_POST['usuario01']);
	$descUsuario = empty($descUsuario) ? 'TODOS' : $descUsuario;
	$aplicacao = $_POST['aplicacao'];
	//(1) Obtencao dos criterios de busca(fim)

	$unidades = '';
	$where = '1 = 1';

	$idUnidade = busca_nivel($idUnidade, $db, $unidades);
	
	if (!empty($unidades)) {
		$unidades = $idUnidade.','.$unidades;
	} else {
		$unidades = $idUnidade;
	}

	$where = ' AND uni.id_unidade IN('.$unidades.') ';
	
	if (!is_null($idPerfil) && !empty($idPerfil) && $idPerfil != 0) {
		$where .= ' AND per.id_perfil = '.$idPerfil.' ';
	}
	
	if (!is_null($idUsuario) && !empty($idUsuario) && $idUsuario != 0) {
		$where .= ' AND usu.id_usuario = '.$idUsuario.' ';
	}
	
    $sql = "select apl.executavel, ime.descricao
            from aplicacao apl, item_menu ime
            where apl.id_aplicacao = $aplicacao
                  and ime.aplicacao_id_aplicacao = $aplicacao";

    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplica��o", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0){
		$linha = mysqli_fetch_array($sql_query);
		$executavel = $linha['executavel'];
		$nome_rel = $linha['descricao'];
    }
    
	$pos = strrpos($executavel, "/");
    
	if($pos === false){
		$aplic = $executavel;
    } else {
		$aplic = substr($executavel, $pos+1);
    }

	$file = "Unidade: ".$descUnidadeLogada."\n";
	$file .= $nome_rel."\n\n";
	
	$sql ="SELECT uni.nome as unidade_nome
			, per.descricao as perfil_nome
			, usu.nome as usuario_nome, usu.login
			, CASE usu.situacao 
				WHEN 'A' THEN 'Ativo' 
				WHEN 'I' THEN 'Inativo' 
				ELSE '-' END as usuario_situacao
			FROM unidade_has_usuario uhu
				INNER JOIN unidade uni
					ON uhu.unidade_id_unidade = uni.id_unidade
				INNER JOIN usuario usu
					ON uhu.usuario_id_usuario = usu.id_usuario
				INNER JOIN perfil per
					ON uhu.perfil_id_perfil = per.id_perfil
			".$where.
			"ORDER BY unidade_nome, perfil_nome, usuario_nome";


    $sql_query = mysqli_query($db, $sql);
    erro_sql("Usu�rios por Unidade", $db, "");
    echo mysqli_error($db);
    
	$file .= '';
    if (mysqli_num_rows($sql_query) > 0){
		$cabecalho = array('Unidade', 'Perfil', 'Login', 'Usu�rio', 'Perfil');
		montaLinha($file, $cabecalho);
		
		$unidadeAnterior = '';
		$perfilAnterior = '';
		
		while($linha = mysqli_fetch_array($sql_query)){
			$unidadeNome = $linha['unidade_nome'];
			$perfilNome = $linha['perfil_nome'];
			$usuarioNome = $linha['usuario_nome'];
			$usuarioLogin = $linha['login'];
			$usuarioSituacao = $linha['usuario_situacao'];

			$dadosUsuario = array($unidadeNome, $perfilNome, $usuarioLogin, $usuarioNome, $usuarioSituacao);
			montaLinha($file, $dadosUsuario);
		}
	}
	
	$file .= "\n\n";
	$file .= $aplic;

    $filename = "Relatorio_Usuarios_Unidade.csv";
    
	header("Pragma: cache");
    header("Expires: 0");
    header("Content-Type: text/comma-separated-values");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: inline; filename=$filename");	
	
	print $file;
}
?>
