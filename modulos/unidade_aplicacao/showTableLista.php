<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

    if (file_exists("../../config/config.inc.php"))
    {
      require "../../config/config.inc.php";
    }
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $unidade  = $_GET["unidade"];
    $excluir=$_GET[excluir];
    //$unidade  = 81;

    if($unidade!="")
    {
      $sql = "select u.aplicacao_id_aplicacao, a.descricao, un.nome
              from unidade_has_aplicacao u,
                   aplicacao a,
                   unidade un
              where u.unidade_id_unidade= '$unidade'
                    and u.aplicacao_id_aplicacao = a.id_aplicacao
                    and u.unidade_id_unidade= un.id_unidade";
      $obj = mysqli_query($db, $sql);
      echo"<table id='tabela' width='100%' cellpadding='0' cellspacing='1' border='0'>";
      if (isset($obj))
      {
        if (mysqli_num_rows($obj) >0)
        {
          while($row=mysqli_fetch_array($obj))
          {
  		    $descricao_aplicacao= $row['descricao'];
  			$nome_unidade= $row['nome'];
        	echo "<tr class='campo_tabela' id='$row[aplicacao_id_aplicacao]'>";
         	echo "<td width='30%' align='left'>$nome_unidade</td>";
        	echo "<td width='30%' align='left'>$descricao_aplicacao</td>";
        	if($excluir!=""){
              echo "<td width='3%' align='center'><a><img src='".URL."/imagens/trash.gif' onclick='excluir_linha(".$row[aplicacao_id_aplicacao].");' border='0' title='Excluir'></a></td>";
            }
            else{
              echo "<td width='3%' align='center'></td>";
            }
            echo "</tr>";
		  }
        }
      }
      echo "</table>";
      echo exit;
    }
?>
