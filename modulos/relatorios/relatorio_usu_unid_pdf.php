<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2011             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: relatorio_usu_unid_pdf.php                                |
// | Autor ..............: Leon Watanabe <leon.watanabe@ima.sp.gov.br>               |
// +---------------------------------------------------------------------------------+
// | Função .............: Relatório de Usuários por Unidade (.pdf)                  |
// | Data de Criação ....: 28/03/2011												 |
// | Última Atualização .: 29/03/2011  												 |
// | Versão .............: 1.0.0                                                     |
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
	erro_sql("Busca Nível", $link, "");
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


$header = array('Login', 'Usuário', 'Situação');
$w = array(50, 100, 0); //tamanho do campo na tela

function cabecalho($nome_und_at, $perfil, $filtro)
{
  global $pdf, $descUnidade, $descPerfil, $descUsuario, $header, $w;

  if ($filtro) {
	$pdf->AddPage(); 
	$pdf->Ln();

	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(22,5,"CRITÉRIOS DE PESQUISA",0,1,"L");
	$pdf->SetFont('Arial','',9);

	$pdf->Cell(35,5,"     Unidade:",0,0,"L");
	$pdf->Cell(0,5,$descUnidade,0,1,"L");

	$pdf->Cell(35,5,"     Perfil:",0,0,"L");
	$pdf->Cell(0,5,$descPerfil,0,1,"L");

	$pdf->Cell(35,5,"     Usuário:",0,0,"L");
	$pdf->Cell(0,5,$descUsuario,0,1,"L");

	$pdf->SetX(-10);
	$pdf->Line(10,$pdf->GetY()+2,$pdf->GetX(),$pdf->GetY()+2);
	$pdf->Ln(4);
	$pdf->SetFont('','B');
	$pdf->Cell(22,5,"Unidade:",0,0,"L");
	$pdf->SetFont('','');
	$pdf->Cell(0,5,$nome_und_at,0,1,"L");
  } else {
	$pdf->Ln(5);
	if ($pdf->GetY() > 270) {
		$pdf->AddPage();
		$pdf->Ln();
	}
  }
  
  $pdf->SetFont('','B');
  $pdf->Cell(22,5,"Perfil:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(0,5,$perfil,0,0,"L");
  $pdf->Ln(6);

  //Colors, line width and bold font
  $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(0);  // cor do texto
 
  $pdf->SetLineWidth(.3);
  $pdf->SetFont('','B');

  if ($pdf->GetY() > 270) {
	$pdf->AddPage();
	$pdf->Ln();
  }
  //Header
  for($i = 0; $i < count($header); $i++)
    $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
  $pdf->Ln(5);

  //Color and font restoration
  /*$pdf->SetFillColor(224,235,255);
  $pdf->SetTextColor(0);*/
  $pdf->SetFont('');
}

if (file_exists("../../config/config.inc.php")) {
	require "../../config/config.inc.php";
    require "../../fpdf152/Class.Pdf.inc.php";
    DEFINE("FPDF_FONTPATH","font/");
	
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
	
	/*exit('idUnidade = '.$idUnidade.' <br> idPerfil = '.$idPerfil.' <br> idUsuario = '.$idUsuario
		.' <br>descUnidade = '.$descUnidade
		.' <br>descPerfil = '.$descPerfil
		.' <br>descUsuario = '.$descUsuario);*/
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
  
    $pdf = new PDF('P','cm','A4'); //P: Portrait (Retrato) / L = Landscape (Paisagem)
	
    $sql = "select apl.executavel, ime.descricao
            from aplicacao apl, item_menu ime
            where apl.id_aplicacao = $aplicacao
                  and ime.aplicacao_id_aplicacao = $aplicacao";

    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplicação", $db, "");
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

    $pdf->SetName($nome_rel);
    $pdf->SetUnd($descUnidadeLogada);
    $pdf->SetNomeAplic($aplic);
    $pdf->Open();
	
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
    erro_sql("Usuários por Unidade", $db, "");
    echo mysqli_error($db);
    
    if (mysqli_num_rows($sql_query) > 0){
		$unidadeAnterior = '';
		$perfilAnterior = '';
		
		while($linha = mysqli_fetch_array($sql_query)){
			$unidadeNome = $linha['unidade_nome'];
			$perfilNome = $linha['perfil_nome'];
			$usuarioNome = $linha['usuario_nome'];
			$usuarioLogin = $linha['login'];
			$usuarioSituacao = $linha['usuario_situacao'];

			$dadosUsuario = array($usuarioLogin, $usuarioNome, $usuarioSituacao);
			
			if (($unidadeAnterior != $unidadeNome)
				|| $perfilAnterior != $perfilNome) {				
				
				if ($unidadeAnterior != $unidadeNome) {
					$filtro = true;
				} else {
					$filtro = false;
				}
				
				$unidadeAnterior = $unidadeNome;
				$perfilAnterior = $perfilNome;

				cabecalho($unidadeNome, $perfilNome, $filtro);
			}
			
			$w = array(50, 100, 40);
			$a = array('L', 'L', 'C');
			
			$y = $pdf->GetY();
			
			if ($y > 270) {
				$pdf->AddPage();
				$pdf->Ln();
			}
			
			$y = $pdf->GetY();
			
			for($i = 0; $i < count($dadosUsuario); $i++) {
				$x = $pdf->GetX();				
				
				$largura = $w[$i];
				
				$pdf->MultiCell($largura,
                    5,
                    $dadosUsuario[$i],
                    1,
                    $a[$i],
                    false);

				$pdf->SetXY(($x + $largura),$y);
			}
			
			$pdf->Ln(5);
		}
	}
	
    $pdf->Output();
    $pdf->Close();
}
?>
