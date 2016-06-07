<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
   
  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);

  $inscricao = $_GET[inscricao];
  $conselho  = $_GET[conselho];
  $uf  = $_GET[uf];

  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU
   $sql_profissional = "select id_profissional
                        from
                               profissional
                        where
                               inscricao = '$inscricao'
                               and tipo_conselho_id_tipo_conselho = '$conselho'
                               and estado_id_estado='$uf'
                               and status_2 = 'A'";
                     
   $res=mysqli_query($db, $sql_profissional);
   //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
   $linhas_res=mysqli_num_rows($res);

   if ($linhas_res==0)
   {
     echo "nao_existe_profissional";
   }
   else
   {
     echo "existe_profissional";
   }
?>
