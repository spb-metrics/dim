<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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

  /******************************************************************
  // ARQUIVO ...: Monta o XML dos Lotes
  // BY ........: Fabio Hitoshi Ide
  // DATA ......: 15/06/2007
  /******************************************************************/
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

  $arq_conf="../config/config.inc.php";
  if(!file_exists($arq_conf)){
    exit("Não existe arquivo de configuração: $arq_conf!");
  }
  require($arq_conf);
  
  $valores=split("[|]", $_POST["id_material"]);
  $id_material=$valores[0];
  $id_tipo_movto=$valores[1];
  $id_unidade=$valores[2];
  $aplicacao=$valores[3];
  if($aplicacao=="lote"){
    $sql="select distinct e.lote, f.id_fabricante, f.descricao, e.validade ";
    $sql.="from estoque as e, material as m, fabricante as f ";
    $sql.="where e.fabricante_id_fabricante=f.id_fabricante and ";
    $sql.="e.material_id_material=m.id_material and e.material_id_material='$id_material' ";
    $sql.="and m.status_2='A' and e.quantidade>0 and flg_bloqueado=''";
  }
  if($aplicacao=="mestoque"){
    $sql="select * from tipo_movto where id_tipo_movto='$id_tipo_movto'";
    $result=mysqli_query($db, $sql);
    erro_sql("Tabela Tipo Movto", $db, "");
    if(mysqli_num_rows($result)>0){
      $movto=mysqli_fetch_object($result);
      $flg_bloqueado=$movto->flg_movto_bloqueado;
      $flg_vencido=$movto->flg_movto_vencido;
    }
    $sql_param = "select dias_vencto_material from parametro";
    $res_param = mysqli_query($db, $sql_param);
    erro_sql("Select Parâmetro", $db, "");
    if(mysqli_num_rows($res_param) > 0)
    {
      $info_param = mysqli_fetch_object($res_param);
      $vencimento = soma_data(date("Y-m-d"), $info_param->dias_vencto_material) ;
    }
    $sql = "select distinct est.lote, est.validade, est.quantidade, fab.id_fabricante, fab.descricao
            from estoque est
                 inner join material mat on est.material_id_material = mat.id_material
                 inner join fabricante fab on est.fabricante_id_fabricante = fab.id_fabricante
            where est.material_id_material = '$id_material'
                  and est.unidade_id_unidade = '$id_unidade'
                  and mat.status_2 = 'A'";

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

    if (strtoupper($flg_vencido) == "S")
    {
      if (strtoupper($flg_bloqueado) == "S")
        $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento')";
      else
        $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento'";
    }
    else if (strtoupper($flg_vencido) == "N")
    {
      $sql = $sql." and SUBSTRING(est.validade,1,10) > '$vencimento'";
    }

    $sql = $sql." and est.quantidade > 0";
    $sql = $sql." order by est.validade";
}

  $result=mysqli_query($db, $sql);
  erro_sql("Tabela estoque", $db, "");

  $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
  $xml= str_replace("\>", ">", $xml);
  $xml.="<lotes>\n";
  while($lote_info=mysqli_fetch_object($result)){
    if($aplicacao=="mestoque"){
      $lote_value=$lote_info->lote . "|" . $lote_info->id_fabricante . "|" . $lote_info->validade;
      $pos1=strpos($lote_info->validade, "-");
      $pos2=strrpos($lote_info->validade, "-");
      $validade_info=substr($lote_info->validade, $pos2+1, strlen($lote_info->validade)) . "/" . substr($lote_info->validade, $pos1+1, 2) . "/" . substr($lote_info->validade, 0, 4);
      $lote_descricao="Lote: ".$lote_info->lote." --- Validade: ".$validade_info." --- Fabricante: ".$lote_info->descricao." --- Quantidade: ".(int)$lote_info->quantidade;
      $lote_descricao=str_replace("&", "&amp;", $lote_descricao);
      $xml.="<lote>\n";
      $xml.="<codigo>" . $lote_value . "</codigo>\n";
      $xml.="<descricao>" . $lote_descricao . "</descricao>\n";
      $xml.="</lote>\n";
    }
    if($aplicacao=="lote"){
      $lote_value=$lote_info->lote . "|" . $lote_info->id_fabricante;
      $pos1=strpos($lote_info->validade, "-");
      $pos2=strrpos($lote_info->validade, "-");
      $validade_info=substr($lote_info->validade, $pos2+1, strlen($lote_info->validade)) . "/" . substr($lote_info->validade, $pos1+1, 2) . "/" . substr($lote_info->validade, 0, 4);
      $lote_descricao="Lote: ".$lote_info->lote." --- Validade: ".$validade_info." --- Fabricante: ".$lote_info->descricao;
      $lote_descricao=str_replace("&", "&amp;", $lote_descricao);
      $xml.="<lote>\n";
      $xml.="<codigo>" . $lote_value . "</codigo>\n";
      $xml.="<descricao>" . $lote_descricao . "</descricao>\n";
      $xml.="</lote>\n";
    }
  }
  $xml.="</lotes>";

  Header("Content-type: application/xml; charset=iso-8859-1");

  echo $xml;
?>

