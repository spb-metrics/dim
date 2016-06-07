<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//////////
//HEADER//
//////////

//error_reporting(E_ALL);
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

  $ARQ_CONFIG="../config/config.inc.php";
  if(!file_exists($ARQ_CONFIG)){
    exit("N�o existe arquivo de configura��o: $ARQ_CONFIG");
  }
  require $ARQ_CONFIG;

    function soma_data($pData, $pDias)//formato BR
    {
      if(ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $pData, $vetData))
      {
        $fAno = $vetData[1];
        $fMes = $vetData[2];
        $fDia = $vetData[3];

        for($x = 1; $x <= $pDias; $x++){
          if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12){
            $fMaxDia = 31;
          }
          elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11){
            $fMaxDia = 30;
          }
          else{
            if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0){
              $fMaxDia = 29;
            }
            elseif($fMes == 2){
              $fMaxDia = 28;
            }
          }
          $fDia++;
          if($fDia > $fMaxDia){
            if($fMes == 12){
              $fAno++;
              $fMes = 1;
              $fDia = 1;
            }
            else{
              $fMes++;
              $fDia = 1;
            }
          }
        }
        if(strlen($fDia) == 1)
          $fDia = "0" . $fDia;
        if(strlen($fMes) == 1)
          $fMes = "0" . $fMes;
        return "$fAno-$fMes-$fDia";
      }
    }

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
   $valores=split("[|]", $_POST["descricao"]);
   $descricao=$valores[0];
   $id_movto=$valores[1];
   $id_unidade=$valores[2];
   $aplicacao=$valores[3];
 
   if($aplicacao=="entrada" || $aplicacao=="lote" || $aplicacao=="remanejamento" || $aplicacao=="restringir"){
     

	 
	 $sql="select *
           from material
           where status_2 = 'A' and descricao like '%". trim($descricao) . "%'
           order by descricao";
		   
		   
	
 /*$sql="select *
           from material
           where status_2 = 'A' and trim('$descricao') = replace(descricao, '+', ' ')
           order by descricao";*/
		   

		   
		//echo $sql;
		   
   }
   if($aplicacao=="mestoque"){
     $sql="select * from tipo_movto where id_tipo_movto='$id_movto'";
     $result=mysqli_query($db, $sql);
     $movimento=mysqli_fetch_object($result);
     $operacao=$movimento->operacao;
     $flg_bloqueado=$movimento->flg_movto_bloqueado;
     $flg_vencido=$movimento->flg_movto_vencido;
     if ($operacao == "entrada")
     {
       $sql = "select distinct mat.codigo_material, mat.descricao,
                      udm.unidade, mat.id_material
               from material mat
                    inner join unidade_material udm
                    on mat.unidade_material_id_unidade_material = udm.id_unidade_material
               where mat.status_2='A' and mat.descricao like '%" . trim($descricao) . "%'
               order by mat.descricao";
     }
     else if (($operacao=="saida") or ($operacao=="perda"))
     {
       $sql = "select distinct mat.codigo_material, mat.descricao, udm.unidade, mat.id_material
               from material mat
                    inner join unidade_material udm
                    on mat.unidade_material_id_unidade_material = udm.id_unidade_material
                    inner join estoque est
                    on mat.id_material = est.material_id_material
               where mat.status_2='A' and mat.descricao like '%" . trim($descricao) . "%'
                     and est.unidade_id_unidade = '$id_unidade'
                     and est.quantidade > 0";

       if (strtoupper($flg_bloqueado) == "S")
       {
         if (strtoupper($flg_vencido) == "S")
           $sql = $sql." and (est.flg_bloqueado = 'S'";
         else
           $sql = $sql." and est.flg_bloqueado = 'S'";
       }
       else if (strtoupper($flg_bloqueado) == "N")
       {
         $sql = $sql." and est.flg_bloqueado <> 'S'";
       }

       $sql_param = "select dias_vencto_material from parametro";
       $res_param = mysqli_query($db, $sql_param);
       if(mysqli_num_rows($res_param) > 0)
       {
         $info_param = mysqli_fetch_object($res_param);
         $vencimento = soma_data(date("Y-m-d"), $info_param->dias_vencto_material) ;
       }
       if (strtoupper($flg_vencido) == "S")
       {
         if (strtoupper($flg_bloqueado) == "S")
           $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento')";
         else
           $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento'";
       }
       else if (strtoupper($flg_vencido) == "N")
       {
         $vencimento = date("Y-m-d");
         $sql = $sql." and SUBSTRING(est.validade,1,10) > '$vencimento'";
       }
       $sql = $sql." order by mat.descricao";
     }
   }
    $results=mysqli_query($db, $sql);

    while ($row=mysqli_fetch_object($results)){

		//Cadastrar na lista
		$item = $xmlDoc->createElement('item');
		$item = $root->appendChild($item);
		if($aplicacao=="remanejamento" || $aplicacao=="restringir"){
          $valor=$row->id_material . "|" . $row->codigo_material;
          $item->setAttribute('id',$valor);
        }
        else{
          $item->setAttribute('id',$row->id_material);
        }
		$texto = $row->descricao;
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
