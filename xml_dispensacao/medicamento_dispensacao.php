<?
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//AD0011 atualizado em 04/05/2011
  session_start();
  
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
