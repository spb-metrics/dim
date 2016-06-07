<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
  header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");                          // HTTP/1.0

  $ARQ_CONFIG="config.inc.php";
  if(!file_exists($ARQ_CONFIG)){
    exit("Não existe arquivo de configuração: $ARQ_CONFIG");
  }
    require $ARQ_CONFIG;
?>
<HTML>
  <HEAD>
    <TITLE>Buscar Unidades</TITLE>
    <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
  </HEAD>
  <BODY>
    <form name="form_argumentos" method="post" action="./busca_unidade.php"  enctype="application/x-www-form-urlencoded">
            <table>
              <tr>
                <td>Unidade</td>

                <td colspan="4" width="40%">
                  <input type="text" id="unidade01" name="unidade01" maxlength="100" size="110" onkeypress="unidade_ajax();">
                </td>

                <td>
                   <input type="textBox" name="unidade" id="unidade">
                </td>
                <div id="auto_lograd"></div>
              </tr>
            </table>
    </form>
    <style type="text/css">
    <!--
      /* Definição dos estilos do DIV */
      /* CSS for the DIV */
      #auto_lograd{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #auto_lograd UL{ list-style:none; margin: 0; padding: 0; }
      #auto_lograd UL LI{ display:block;}
      #auto_lograd A{ color:#000000; text-decoration:none; }
      #auto_lograd A:hover{ color:#000000; }
      #auto_lograd LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>
    <script language="javascript" type="text/javascript" src="scripts.js"></script>
    <script language="javascript" type="text/javascript" src="dmsAutoComplete.js"></script>
    <script language="javascript">
      <!--
      
   function unidade_ajax(){
        //Instanciar objeto AutoComplete
        var AC = new dmsAutoComplete('unidade01','auto_lograd', 'form_busca_end', 'unidade01', 'unidades_ajax.php', 'unidade');
        //Definir opções
        AC.clearField = false; //Definir que texto escolhido não deve ser removido do campo
      }

  //-->
    </script>
  </BODY>
</HTML>
