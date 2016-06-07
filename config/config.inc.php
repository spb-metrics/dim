<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/////////////////////////////////////////////////////////////////
//  Sistema..: Dim -  Dispensação Individualizada de Medicamentos
//  Arquivo..: config.inc.php
//  Bancos...: dbmdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Função...: Programa de configuração de ambiente:
//             - define conexão com o banco de dados
//             - define variáveis de ambiente
//////////////////////////////////////////////////////////////////

//////////
//HEADER//
//////////

  function erro_sql($tag_sql, $link, $link_dim){
    if($link==""){
      if (mysql_error()){
        $msg_erro = "Não foi possivel efetuar a operação! \\n";
        $msg_erro .= "\\nErro número: ".mysql_errno()."\\n";
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
        $msg_erro = "Não foi possivel efetuar a operação! \\n";
        $msg_erro .= "\\nErro número: ".mysqli_errno($link)."\\n";
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
//PARÂMETROS DE CONFIGURAÇÃO DO SISTEMA - ALTERE-OS DE  ACORDO COM O AMBIENTE //
////////////////////////////////////////////////////////////////////////////////

DEFINE("DIR","Diretório dos fontes do sistema no servidor");
DEFINE("URL","URL de acesso ao sistema");
DEFINE("CSS","Caminho onde estarão arquivos CSS e nome do arquivo css");

DEFINE("TIT","Dispensação Individualizada de Medicamentos");
DEFINE("SETA","<img src='".URL."/imagens/arrow.gif'>");
DEFINE("SIMBOLO", "#");
DEFINE("QTDE_COLUNA", 6);

if(!isset($_SESSION["MSG_LOGIN"])){$_SESSION["MSG_LOGIN"] = "  ";}

///////////////////////////////
//CONEXÃO COM A BASE DE DADOS//
///////////////////////////////
$db = @mysqli_connect("Caminho_Servidor","Usuario","Senha");
erro_sql("Conexão", $db, "");

//echo "db".$db."<br>";

if ($db)
{

  $bd = @mysqli_select_db($db,"Nome do Banco");
  erro_sql("Seleção BD", $db, "");
  mysqli_autocommit($db, false);
  if ($bd)
  {
    return true;
  }
  else
  {
    exit("Sem conexão com o Banco de Dados");
  }
}
else
{


  exit("Sem conexão com o Servidor");
}

?>
