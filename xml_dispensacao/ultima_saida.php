<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  
  //header("Cache-Control: no-cache, must-revalidate");
  //header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);


  $material=$_GET[material];
  $paciente=$_GET[paciente];

  $sql_estoque ="select r.id_receita, r.ano, r.unidade_id_unidade, r.numero,
                        r.status_2, r.paciente_id_paciente, r.profissional_id_profissional,
                        ir.data_ult_disp
                 from
                        itens_receita ir,
                        receita r
                 where
                        ir.material_id_material = $material
                        and r.paciente_id_paciente = $paciente
                        and ir.receita_id_receita = r.id_receita
                 order by
                        ir.data_ult_disp desc";
  $resultado=mysqli_query($db, $sql_estoque);

  //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
  $linhas=mysqli_num_rows($resultado);

  if($linhas>0)
  {
   $pegar=mysqli_fetch_array($resultado);
   echo "<table bgcolor='#D0D0D0' width='100%' cellpadding='0' cellspacing='0' border='0'>";
   echo "<tr>";
   echo "<td colspan='2' align='right' bgcolor='#D8DDE3'>";
   echo "<input style='font-size: 10px;' type='button' name='adiciona' id='adiciona' value='Adiciona' onClick='if (adicionar_medicamentos()){monta_lista();}'>";
   echo "</td>";
   echo "</tr><tr>";
   echo "<td align='left' bgcolor='#D8DDE3' width='70%'>�ltima retirada deste medicamento por este paciente: ".substr($pegar[data_ult_disp],8,2)."/".substr($pegar[data_ult_disp],5,2)."/".substr($pegar[data_ult_disp],0,4);
   echo "</td>";
   echo "<td align='left' bgcolor='#D8DDE3' width='30%'>";
   echo "<input style='font-size: 10px;' type='button' name='visualizar' id='visualizar' value='Visualizar'  onClick='visualizar_receita($pegar[id_receita],  $pegar[ano], $pegar[unidade_id_unidade], $pegar[numero], \"$pegar[status_2]\", $pegar[paciente_id_paciente], $pegar[profissional_id_profissional]);'>";
   echo "</td>";
   echo "</tr>";
   echo "</table>";
  }
  else
  {
   echo "<table bgcolor='#D0D0D0' width='100%' cellpadding='0' cellspacing='0' border='0'>";
   echo "<tr>";
   echo "<td align='right' bgcolor='#D8DDE3'>";
   echo "<input style='font-size: 10px;' type='button' name='adiciona' id='adiciona' value='Adiciona' onClick='if (adicionar_medicamentos()){monta_lista();}'>";
   echo "</td>";
   echo "</tr>";
   echo "</table>";
  }
?>
