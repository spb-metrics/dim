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

  $configuracao="../config/config.inc.php";
  if(!file_exists($configuracao)){
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $numero=$_GET["numero"];
  $unidade=$_GET["unidade"];
  if($unidade==""){
    $sql="select * from movto_geral where id_movto_estornado='$numero'";
  }
  else{
    $sql="select m.id_material, m.codigo_material, m.descricao as mdescricao, f.id_fabricante,
                 f.descricao as fdescricao, i.lote, i.qtde, i.validade
          from movto_geral as mov, itens_movto_geral as i, tipo_movto as t, material as m,
               fabricante as f
          where mov.id_movto_geral=i.movto_geral_id_movto_geral and
                t.id_tipo_movto=mov.tipo_movto_id_tipo_movto
                and f.id_fabricante=i.fabricante_id_fabricante and f.status_2='A' and
                m.id_material=i.material_id_material and m.status_2='A'
                and t.flg_movto='s' and mov.id_movto_geral='$numero' and
                mov.unidade_id_unidade='$unidade'";
  }
  $result=mysqli_query($db, $sql);
  erro_sql("Select Documento Revertido", $db, "");
  if($unidade==""){
    if(mysqli_num_rows($result)>0){
      $mensagem="REV";
    }
    else{
      $mensagem="NRE";
    }
    echo $mensagem;
  }
  else{
    if(mysqli_num_rows($result)>0){
     $cor_linha="#CCCCCC";
     $msg="<table id='tabela_aux' cellpadding='0' cellspacing='1' border='0' width='100%'>";
     $cont=0;
      while($documento_info=mysqli_fetch_object($result)){
        $info=$documento_info->id_material . "|" . $documento_info->id_fabricante . "|" . $documento_info->lote . "|" . $documento_info->qtde . "|" . $documento_info->validade;
        $qtde=intval($documento_info->qtde);
        $msg.="<tr class='linha_tabela' bgcolor='$cor_linha' onMouseOver='this.bgColor=\"#D4DFED\";' onMouseOut='this.bgColor=\"$cor_linha\"'>
                 <td width='10%' align='left'>
                   $documento_info->codigo_material
                 </td>
                 <td width='40%' align='left'>
                   $documento_info->mdescricao
                 </td>
                 <td width='15%' align='left'>
                   $documento_info->lote
                 </td>
                 <td width='15%' align='left'>
                   $documento_info->fdescricao
                 </td>
                 <td width='15%' align='right'>
                   $qtde
                 </td>
                 <td width='5%' align='center'>
                    <input type='checkbox' name='opcao[]' id='$cont' value='$info' onclick='desabilitarTodos();'>
                 </td>
               </tr>";
        ////////////////////////
        //MUDANDO COR DA LINHA//
        ////////////////////////
        if($cor_linha=="#EEEEEE"){
          $cor_linha="#CCCCCC";
        }
        else{
          $cor_linha="#EEEEEE";
        }
        $cont++;
      }
      $msg.="</table>";
      $mensagem=$msg;
    }
    else{
      $mensagem="NAO";
    }
    echo $mensagem;
  }
?>

