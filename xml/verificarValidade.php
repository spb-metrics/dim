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

  $mat = $_GET[mat];
  $fabr= $_GET[fabr];
  $lot = $_GET[lot];
  $valid= $_GET[valid];
  $und=$_GET[und];
  $lot=str_replace("CERQUILHA", SIMBOLO, $lot);
  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU
  $sql="select * from estoque
                 where material_id_material='$mat' and unidade_id_unidade='$und' and lote='$lot' and fabricante_id_fabricante='$fabr'";

   $res=mysqli_query($db, $sql);
    if(mysqli_num_rows($res)>0){
      $info=mysqli_fetch_object($res);
      $validade=$info->validade;
      if($validade!=$valid){
        echo "validade NO";
        echo $validade;
        }
       else{
           echo "validade OK";
           }
     }
     else{
       echo "validade OK";
     }
   ?>
