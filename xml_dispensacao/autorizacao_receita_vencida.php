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

  $material   = $_GET[material];

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU

  $sql_estoque = "select e.id_estoque,
                         f.descricao
                  from
                         estoque e,
                         fabricante f
                  where
                         material_id_material = $material
                         and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                         and e.quantidade > 0
                         and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                         and e.validade > '".date("Y-m-d")."'
                         and e.fabricante_id_fabricante = f.id_fabricante";
  $resultado=mysqli_query($db, $sql_estoque);

  //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
  $linhas=mysqli_num_rows($resultado);

  if($linhas==0)
  {
   echo "sem_estoque";
  }
  else
  {
   echo "com_estoque";
  }
?>
