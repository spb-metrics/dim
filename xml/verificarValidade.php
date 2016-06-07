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

  $mat = $_GET[mat];
  $fabr= $_GET[fabr];
  $lot = $_GET[lot];
  $valid= $_GET[valid];
  $und=$_GET[und];
  $lot=str_replace("CERQUILHA", SIMBOLO, $lot);
  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU
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
