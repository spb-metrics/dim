<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

//DADOS PARA CONEX�O
$sql_ftp="select caminho_servidor_ftp,
             usuario_ftp,
             senha_ftp
      from parametro";
$parametro_ftp=mysqli_query($db, $sql_ftp);
if(mysqli_errno($db)!=0){
  echo "Erro ao buscar SERVIDOR FTP/SENHA/USU�RIO na tabela PARAMETRO no DIM.<br>";
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

//TENTA EFETUAR O LOGIN COM USU�RIO E SENHA DE ACESSO
if(!@ftp_login($connFtp, $usuario, $senha) ) {
   exit("N�o foi poss�vel efetuar a conex�o. Verifique o usu�rio e a senha de acesso.");
}
?>
