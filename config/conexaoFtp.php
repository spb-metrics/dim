<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//DADOS PARA CONEXÃO
$sql_ftp="select caminho_servidor_ftp,
             usuario_ftp,
             senha_ftp
      from parametro";
$parametro_ftp=mysqli_query($db, $sql_ftp);
if(mysqli_errno($db)!=0){
  echo "Erro ao buscar SERVIDOR FTP/SENHA/USUÁRIO na tabela PARAMETRO no DIM.<br>";
  exit;
}
else{
  $param_ftp=mysqli_fetch_object($parametro_ftp);
  $server=$param_ftp->caminho_servidor_ftp;
  $senha=$param_ftp->senha_ftp;
  $usuario=$param_ftp->usuario_ftp;
}

//CONECTA AO FTP
$connFtp = ftp_connect($server)
           or die ("ERRO AO CONECTAR AO SERVIDOR DE FTP ".$server);

//TENTA EFETUAR O LOGIN COM USUÁRIO E SENHA DE ACESSO
if(!@ftp_login($connFtp, $usuario, $senha) ) {
   exit("Não foi possível efetuar a conexão. Verifique o usuário e a senha de acesso.");
}
?>
