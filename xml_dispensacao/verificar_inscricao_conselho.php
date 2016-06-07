<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
   
  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $inscricao = $_GET[inscricao];
  $conselho  = $_GET[conselho];
  $uf  = $_GET[uf];

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU
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
