<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

 // session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
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
    echo "<td align='center' width='100%'>Cart�o SUS</td>";
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
