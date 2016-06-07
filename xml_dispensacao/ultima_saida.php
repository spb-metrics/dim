<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  
  //header("Cache-Control: no-cache, must-revalidate");
  //header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
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
   echo "<td align='left' bgcolor='#D8DDE3' width='70%'>Última retirada deste medicamento por este paciente: ".substr($pegar[data_ult_disp],8,2)."/".substr($pegar[data_ult_disp],5,2)."/".substr($pegar[data_ult_disp],0,4);
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
