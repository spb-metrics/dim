<?
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

$ARQ_CONFIG="../config/config.inc.php";
if(!file_exists($ARQ_CONFIG)){
    exit("N�o existe arquivo de configura��o: $ARQ_CONFIG");
}
require $ARQ_CONFIG;


function destacaTexto($highlite,$string){
	return str_ireplace($highlite,"<b>".$highlite."</b>",$string);
}

//Criar documento XML atraves de DOM
$xmlDoc = new DOMDocument('1.0', 'utf-8');
$xmlDoc->formatOutput = true;

//Criar elementos Ra�z do XML
$root = $xmlDoc->createElement('root');
$root = $xmlDoc->appendChild($root);

try {
   if (isset($_POST[fabricante01]))
   {
      $valores=split("[|]", $_POST[fabricante01]);
   }

   $descricao=$valores[0];
//   $id_un_sist_medic=$valores[1];
//   $id_unidade=$valores[2];
//   $aplicacao="medvencidos";
//   $lote=$valores[4];

        $sql = "select distinct(fab.descricao), fab.id_fabricante
        from fabricante fab
             inner join estoque est on est.fabricante_id_fabricante = fab.id_fabricante
        where fab.status_2 = 'A' and fab.descricao like '$descricao%'
        order by fab.descricao";

  
 /*  if($aplicacao=="unidadesuperior")
   {
        $sql = "select fab.id_fabricante, fab.descricao
        from fabricante fab
             inner join estoque est on est.fabricante_id_fabricante = fab.id_fabricante
        where fab.status_2 = 'A'
              and est.material_id_material = $id_un_sist_medic
              and est.unidade_id_unidade = $id_unidade
              and est.lote = '$lote' order by fab.descricao";
  }
  
  else if($aplicacao=="medvencidos")
  {

      $sql = "select id_fabricante, descricao
              from fabricante where descricao like '$descricao%'
              and status_2 = 'A' order by descricao";
  }
*/
    $results=mysqli_query($db, $sql);
    erro_sql("Pesquisa Fabricante", $db, "");

    while ($row=mysqli_fetch_object($results)){
        $descricao = ereg_replace("&", "&", $row->descricao);
		//Cadastrar na lista
		$item = $xmlDoc->createElement('item');
		$item = $root->appendChild($item);
		$item->setAttribute('id',$row->id_fabricante);
		$texto = $descricao;
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
