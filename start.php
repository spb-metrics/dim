<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
/////////////////////////////////////////////////////////////////
//  Sistema..: DIM - Dispensa��o Individualizada de Medicamentos
//  Arquivo..: start.php
//  Bancos...: dbtdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Fun��o...: Tela de in�cio do sistema
//////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////
//TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
//////////////////////////////////////////////////
if (file_exists("./config/config.inc.php"))
{
  require "./config/config.inc.php";
  require DIR."/header.php";

///////////////////////////////////////////////
/////Verifica Remanejamento////////////////////
///////////////////////////////////////////////
    if ($_SESSION[flag_remanj]!="S"){
      $sql_remanejamento = "select distinct status_2 from solicita_remanej where id_unid_solicitada = '$_SESSION[id_unidade_sistema]' and status_2='SOLICITADA'
                            UNION
                            select distinct status_2 from solicita_remanej where id_unid_solicitante = '$_SESSION[id_unidade_sistema]'and status_2='RESERVADA'";

         $rem = mysqli_query($db, $sql_remanejamento);
         $rem_info = mysqli_fetch_object($rem);

         if(mysqli_num_rows($rem)>0){
            $x=mysqli_num_rows($rem);
             //echo "<script>window.alert('num linhas..".$x."');</script>";

            $sql_apli = "select itm.aplicacao_id_aplicacao, itm.descricao
                                from aplicacao apli, item_menu itm
                                where apli.id_aplicacao = itm.aplicacao_id_aplicacao and apli.executavel in ('/modulos/remanejamento/remanejamento_inicial_fornec.php','/modulos/remanejamento/remanejamento_inicial.php')";

            //$sql_apli = "select aplicacao_id_aplicacao, descricao from item_menu where aplicacao_id_aplicacao in(75,76)";
            $apli_remanj = mysqli_query($db, $sql_apli);
            $id2="";


            while($apli_info = mysqli_fetch_object($apli_remanj)){
              $id1 = $apli_info->aplicacao_id_aplicacao;
              $aplicacao1 = $apli_info->descricao;
              if($id2==""){
                $id2 = $apli_info->aplicacao_id_aplicacao;
                $aplicacao2 = $apli_info->descricao;
                }
              }
            //$teste=$id1.$aplicacao1.$id2.$aplicacao2;
            // echo "<script>window.alert('Aplicao ".$teste."');</script>";
            
            if($x>1){
             echo "<script>window.alert('Existe Pedido de Remanejamento Aguardando Efetiva��o - Tela: ".$aplicacao1." \\n e Pedido de Remanejamento Para Ser Atendido - Tela: ".$aplicacao2."');</script>";
            }
            else{
                 if ($rem_info->status_2=="RESERVADA"){echo "<script>window.alert('Existe Pedido de Remanejamento Aguardando Efetiva��o - Tela: ".$aplicacao1."');</script>";}
                 if ($rem_info->status_2=="SOLICITADA"){echo "<script>window.alert('Existe Pedido de Remanejamento Para Ser Atendido por Essa Unidade - Tela: ".$aplicacao2." ');</script>";}
            }
         }
         $_SESSION[flag_remanj]="S";
       }
  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA P�GINA//
  ////////////////////////////////////
  ?>

  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
    <?php
      $sql="select mensagem,
                   id_mensagem,
                   imagem
            from mensagem
            where status_2='A' and
                  data_inicio<='". date("Y-m-d") . "' and
                  data_fim>='" . date("Y-m-d") . "'
            order by data_inicio desc, data_fim desc";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Mensagem", $db, "");
      while($result=mysqli_fetch_object($res)){
    ?>
        <tr>
          <td align="center" valign="middle">
            <?php echo $result->mensagem;?>
          </td>
        </tr>
        <tr>
          <td align="center" valign="middle">
          <?php
            if($result->imagem!=""){
          ?>
              <img src="./modulos/mensagem/criar_imagem.php?id_mensagem=<?php echo $result->id_mensagem;?>">
          <?php
            }
          ?>
          </td>
        </tr>
    <?php
      }
    ?>
  </table>

<?php
  require DIR."/footer.php";
  

     

  if($_GET[base]=="f"){
    echo "<script>window.alert('Base no " . $_GET[nome] . " n�o selecionada!');</script>";
  }
  if($_GET[erro]=="f"){
    echo "<script>window.alert('Endere�o IP inv�lido: " . $_GET[dns] . "!');</script>";
  }
  if($_GET[conexao]=="f"){
    echo "<script>window.alert('Conex�o n�o informada para a unidade!');</script>";
  }
  if($_GET[servidor]=="f"){
    echo "<script>window.alert('Conex�o com servidor do ". $_GET[nome] ." n�o efetuada!');</script>";
  }
  if($_GET[banco]=="f"){
    echo "<script>window.alert('Conex�o com banco de ". $_GET[nome] ." n�o efetuada!');</script>";
  }

}
////////////////////////////////////////////
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else
{
  include_once("./config/erro_config.php");
}
?>
