<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM2
  //  Arquivo..: config_almox.inc.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi ide
  //  Fun��o...: Programa de configura��o de ambiente:
  //             - define conex�o com o banco de dados
  //             - define vari�veis de ambiente
  //////////////////////////////////////////////////////////////////

  ////////////////////////////////////////////////////////////////////////////////
  //PAR�METROS DE CONFIGURA��O DO SISTEMA - ALTERE-OS DE  ACORDO COM O AMBIENTE //
  ////////////////////////////////////////////////////////////////////////////////

  if(!isset($_SESSION["MSG_RECUPERACAO"])){$_SESSION["MSG_RECUPERACAO"] = "Aguardando inser��o de login do usuario";}

  ///////////////////////////////
  //CONEX�O COM A BASE DE DADOS//
  ///////////////////////////////
  $sql="select * from unidade where id_unidade = $_SESSION[id_unidade_sistema]";
  $res=mysqli_query($db, $sql);
  erro_sql("Select Unidade", $db, "");
  if(mysqli_num_rows($res)>0){
    $consulta=mysqli_fetch_object($res);
    $dns = $consulta->dns_local;
  }

  $sql="select * from parametro";
  $res=mysqli_query($db, $sql);
  erro_sql("Par�metro", $db, "");
  if(mysqli_num_rows($res)>0){
    $consulta=mysqli_fetch_object($res);
    $base_almox=$consulta->base_integra_almo;
    $usuario_almox=$consulta->usuario_integra_almo;
    $senha_almox=$consulta->senha_integra_almo;
    $dns_almox=$consulta->servidor_integra_almo;
  }

  if($dns_almox!="")
  {
    $dbALMOX = @mysql_connect($dns_almox, $usuario_almox, $senha_almox);
    erro_sql("Conex�o Almoxarifado", "", $db);
    if ($dbALMOX)
    {
      $base_CENTRAL=@mysql_select_db($base_almox, $dbALMOX);
      erro_sql("Sele��o BD Almoxarifado", "", $db);
      if(!$base_CENTRAL){
        echo "<script>window.location='" . URL . "/start.php?base=f&nome=Almoxarifado';</script>";
      }
    }
    else
    {
      echo "<script>window.location='" . URL . "/start.php?servidor=f&nome=Almoxarifado';</script>";
    }
  }
  else
  {
    echo "<script>window.location='" . URL . "/start.php?erro=f&dns=$dns';</script>";
  }
?>
