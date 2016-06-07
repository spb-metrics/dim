<?
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

$ARQ_CONFIG="../config/config.inc.php";
if(!file_exists($ARQ_CONFIG)){
    exit("Não existe arquivo de configuração: $ARQ_CONFIG");
}
require $ARQ_CONFIG;


function destacaTexto($highlite,$string){
	return str_ireplace($highlite,"<b>".$highlite."</b>",$string);
}

//Criar documento XML atraves de DOM
$xmlDoc = new DOMDocument('1.0', 'utf-8');
$xmlDoc->formatOutput = true;

//Criar elementos Raíz do XML
$root = $xmlDoc->createElement('root');
$root = $xmlDoc->appendChild($root);

try {

	$valores = split("[|]", $_POST[usuario01]);
    $destaca = $_POST[usuario01];
	
   $descricao=$valores[0]; 

   $sql="select * from usuario where situacao = 'A' and nome like '%$descricao%'";
   
   $sql.=" order by nome";


   $results=mysqli_query($db, $sql);
   erro_sql("Pesquisa usuario", $db, "");

   while ($row=mysqli_fetch_object($results)){

		//Cadastrar na lista
		$item = $xmlDoc->createElement('item');
		$item = $root->appendChild($item);
		$item->setAttribute('id',$row->id_usuario);
		$texto = $row->nome;
		$label = destacaTexto($descricao,$texto);
		$item->setAttribute('label',rawurlencode($label));
		$item->setAttribute('flabel',rawurlencode($texto));
	}
} catch (PDOException $e) {
	$item = $xmlDoc->createElement('item');
	$item = $root->appendChild($item);
	$item->setAttribute('id','0');
	$label = $e->getMessage();
	$item->setAttribute('label',rawurlencode($label));
}


//Retornar XML de resultado para AJAX
//Return XML code for AJAX Request
header("Content-type:application/xml; charset=utf-8");
echo $xmlDoc->saveXML();

?>
