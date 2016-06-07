<?
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//AD0011 atualizado em 04/05/2011
  session_start();
  
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

if ($_POST[grupo]!=0)
{
$sql = "select m.id_material, m.unidade_material_id_unidade_material,
                   m.grupo_id_grupo, m.subgrupo_id_subgrupo, m.tipo_material_id_tipo_material,
                   m.familia_id_familia, m.lista_especial_id_lista_especial, m.codigo_material,
                   m.descricao, m.dias_limite_disp, m.flg_autorizacao_disp,
                   u.unidade
            from
                   material m,
                   unidade_material u,
                   unidade_grupo ug
            where
                   m.flg_dispensavel = 'S'
                   and m.status_2 = 'A'
                   and descricao like '%$_POST[medicamento01]%'
                   and ug.grupo_id_grupo = $_POST[grupo]
                   and ug.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                   and m.unidade_material_id_unidade_material = u.id_unidade_material
                   and m.grupo_id_grupo = ug.grupo_id_grupo
            order by
                   descricao";
}
    //echo $sql;
    //exit;
    
    $results=mysqli_query($db, $sql);
    erro_sql("Pesquisa medicamento", $db, "");
    while ($row=mysqli_fetch_object($results))
    {

 	 //Cadastrar na lista
	 $item = $xmlDoc->createElement('item');
	 $item = $root->appendChild($item);
	 $item->setAttribute('id',$row->id_material.'|'.$row->unidade);
	 $texto = $row->descricao;
	 $label = destacaTexto($_POST['medicamento01'],$texto);
	 $item->setAttribute('label',rawurlencode($label));
	 $item->setAttribute('flabel',rawurlencode($texto));
    }
	 
} catch (PDOException $e) {
	$item = $xmlDoc->createElement('item');
	$item = $root->appendChild($item);
	$item->setAttribute('id','0|0');
	$label = $e->getMessage();
	$item->setAttribute('label',rawurlencode($label));
}


//Retornar XML de resultado para AJAX
//Return XML code for AJAX Request
header("Content-type:application/xml; charset=utf-8");
echo $xmlDoc->saveXML();

?>
