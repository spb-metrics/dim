<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

 // session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $operacao=$_GET[id_paciente];  // valor vindo da variavel params de combo.js
      $sql = "select cartao_sus
               from cartao_sus
               where paciente_id_paciente =$operacao";

  $resultado=mysqli_query($db, $sql);

  //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
  $linhas=mysqli_num_rows($resultado);

  if($linhas>0)
  {
   //EXECUTA UM LOOP PARA MOSTRAR OS LOTES
   // DENTRO DO DIV 'PAGINA'
   
    echo "<table id='$operacao' bgcolor='#808080' align='center' width='100%' border='0' cellpadding='0' cellspacing='1'>";
    echo "<tr class='coluna_tabela'>";
    echo "<td align='center' width='100%'>Cartão SUS</td>";
    echo "</tr>";

   while($pegar=mysqli_fetch_array($resultado))
   {
     echo "<tr class='linha_tabela'>";
     echo "<td bgcolor='#FFFFFF' align='left'>".$pegar[cartao_sus]."</td>";
     echo "</tr>";
   }
   echo "</table>";

  }
?>
