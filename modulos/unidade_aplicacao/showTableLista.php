<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
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
