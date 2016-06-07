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
  require $configuracao;

  $inscricao     = $_GET[inscricao];
  $conselho      = $_GET[conselho];
  $nome          = $_GET[nome];
  $profissional  = $_GET[profissional];
  $especialidade = $_GET[especialidade];
  $uf            = $_GET[uf];

  // caso o usuario digite varios espaços em branco
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
