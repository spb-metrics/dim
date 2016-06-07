<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);

  $material=$_GET[material];

  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU

  $sql_receita_controlada = "select l.id_lista_especial, m.lista_especial_id_lista_especial
                             from
                                   lista_especial l,
                                   material m
                             where
                                   m.id_material = '$material'
                                   and l.flg_receita_controlada like 'S'
                                   and m.lista_especial_id_lista_especial = l.id_lista_especial";
  $resultado=mysqli_query($db, $sql_receita_controlada);

  //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
  $linhas=mysqli_num_rows($resultado);

  if($linhas>0)
  {
   //EXECUTA UM LOOP PARA MOSTRAR OS LOTES
   // DENTRO DO DIV 'controlada'
   echo "<td><img src='";
   echo URL."/imagens/obrigat.gif' BORDER='0'>N. Notifica��o <input type='text' name='rec_controlada' size='10' maxlength='20' onBlur='retirar_brancos();'></td>";
  }
  else
  {
   //EXECUTA UM LOOP PARA MOSTRAR OS LOTES
   // DENTRO DO DIV 'controlada'
   echo "<td><input type='hidden' name='rec_controlada' size='10' maxlength='20' value=0></td>";
  }
?>
