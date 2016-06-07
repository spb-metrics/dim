<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/////////////////////////////////////////////////////////////////
//  Sistema..: Dim -  Dispensa��o Individualizada de Medicamentos
//  Arquivo..: config.inc.php
//  Bancos...: dbmdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Fun��o...: Programa de configura��o de ambiente:
//             - define conex�o com o banco de dados
//             - define vari�veis de ambiente
//////////////////////////////////////////////////////////////////

//////////
//HEADER//
//////////

  function erro_sql($tag_sql, $link, $link_dim){
    if($link==""){
      if (mysql_error()){
        $msg_erro = "N�o foi possivel efetuar a opera��o! \\n";
        $msg_erro .= "\\nErro n�mero: ".mysql_errno()."\\n";
        $msg_erro .= "Mensagem: ".mysql_error()."\\n";
        $msg_erro .= "Id SQL: ".$tag_sql;
        $sql="select * from aplicacao where id_aplicacao='$_SESSION[APLICACAO]'";
        $res=mysqli_query($link_dim, $sql);
        if(mysqli_num_rows($res)>0){
          $sistema="";
          $aplic_info=mysqli_fetch_object($res);
        }
        else{
          $sistema="erro";
        }
?>
        <script>
          alert("<?=$msg_erro?>");
<?php
          if($sistema==""){
            if($aplic_info->executavel=="/modulos/bec/bec_inclusao.php"){
              $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
              $ARQ_EXTENSAO=".TXT";
              $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
              if(file_exists($str)){
                unlink($str);
              }
            }
?>
            window.location="<?php echo URL . $aplic_info->executavel . "?aplicacao=$_SESSION[APLICACAO]";?>";
<?php
          }
?>
        </script>
<?
        exit();
       }
    }
    else{
      if (mysqli_error($link)){
        $msg_erro = "N�o foi possivel efetuar a opera��o! \\n";
        $msg_erro .= "\\nErro n�mero: ".mysqli_errno($link)."\\n";
        $msg_erro .= "Mensagem: ".mysqli_error($link)."\\n";
        $msg_erro .= "Id SQL: ".$tag_sql;
        $sql="select * from aplicacao where id_aplicacao='$_SESSION[APLICACAO]'";
        $res=mysqli_query($link, $sql);
        if(mysqli_num_rows($res)>0){
          $sistema="";
          $aplic_info=mysqli_fetch_object($res);
        }
        else{
          $sistema="erro";
        }
?>
        <script>
          alert("<?=$msg_erro?>");
<?php
          if($sistema==""){
            if($aplic_info->executavel=="/modulos/bec/bec_inclusao.php"){
              $ARQ_TRAVA="/tmp/ARQUIVO_TRAVA_UNIDADE_";
              $ARQ_EXTENSAO=".TXT";
              $str=$ARQ_TRAVA  . $_SESSION[id_unidade_sistema] . $ARQ_EXTENSAO;
              if(file_exists($str)){
                unlink($str);
              }
            }
?>
            window.location="<?php echo URL . $aplic_info->executavel . "?aplicacao=$_SESSION[APLICACAO]";?>";
<?php
          }
?>
        </script>
<?
        exit();
      }
    }
  }

////////////////////////////////////////////////////////////////////////////////
//PAR�METROS DE CONFIGURA��O DO SISTEMA - ALTERE-OS DE  ACORDO COM O AMBIENTE //
////////////////////////////////////////////////////////////////////////////////

DEFINE("DIR","Diret�rio dos fontes do sistema no servidor");
DEFINE("URL","URL de acesso ao sistema");
DEFINE("CSS","Caminho onde estar�o arquivos CSS e nome do arquivo css");

DEFINE("TIT","Dispensa��o Individualizada de Medicamentos");
DEFINE("SETA","<img src='".URL."/imagens/arrow.gif'>");
DEFINE("SIMBOLO", "#");
DEFINE("QTDE_COLUNA", 6);

if(!isset($_SESSION["MSG_LOGIN"])){$_SESSION["MSG_LOGIN"] = "  ";}

///////////////////////////////
//CONEX�O COM A BASE DE DADOS//
///////////////////////////////
$db = @mysqli_connect("Caminho_Servidor","Usuario","Senha");
erro_sql("Conex�o", $db, "");

//echo "db".$db."<br>";

if ($db)
{

  $bd = @mysqli_select_db($db,"Nome do Banco");
  erro_sql("Sele��o BD", $db, "");
  mysqli_autocommit($db, false);
  if ($bd)
  {
    return true;
  }
  else
  {
    exit("Sem conex�o com o Banco de Dados");
  }
}
else
{


  exit("Sem conex�o com o Servidor");
}

?>
