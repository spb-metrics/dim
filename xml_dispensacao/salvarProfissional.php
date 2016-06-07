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
  require $configuracao;

  $inscricao     = $_GET[inscricao];
  $conselho      = $_GET[conselho];
  $nome          = $_GET[nome];
  $profissional  = $_GET[profissional];
  $especialidade = $_GET[especialidade];
  $uf            = $_GET[uf];

  // caso o usuario digite varios espa�os em branco
  while (strstr($nome,"  "))
  {
   $nome = str_replace("  ", " ", $nome);
  }
  
  $data_sistema=date("Y-m-d H:i:s");

  $sql="insert into profissional(
               tipo_conselho_id_tipo_conselho,
               tipo_prescritor_id_tipo_prescritor,
               nome,
               status_2,
               inscricao,
               data_inscricao,
               estado_id_estado,
               especialidade,
               usua_incl,
               data_incl)
        values(
               '$conselho',
               '$profissional', '".
               strtoupper($nome)."',
               'A',
               '$inscricao',
               '',
               '$uf', '".
               strtoupper($especialidade)."',
               '$_SESSION[id_usuario_sistema]',
               '$data_sistema')";
  mysqli_query($db, $sql);

  if (mysqli_errno($db) == 0)
  {
   $sql = "select max(id_profissional) as codigo
           from
                  profissional";
   $res=mysqli_query($db, $sql);
   erro_sql("Select max", $db, "");
   $info_profissional=mysqli_fetch_object($res);
   $id_profissional = $info_profissional->codigo;
   mysqli_commit($db);
   echo "ID".$id_profissional;
  }
  else
  {
   mysqli_rollback($db);
   echo "ID0";
  }
?>
