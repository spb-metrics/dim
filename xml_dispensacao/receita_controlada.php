<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $material=$_GET[material];

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU

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
   echo URL."/imagens/obrigat.gif' BORDER='0'>N. Notificação <input type='text' name='rec_controlada' size='10' maxlength='20' onBlur='retirar_brancos();'></td>";
  }
  else
  {
   //EXECUTA UM LOOP PARA MOSTRAR OS LOTES
   // DENTRO DO DIV 'controlada'
   echo "<td><input type='hidden' name='rec_controlada' size='10' maxlength='20' value=0></td>";
  }
?>
