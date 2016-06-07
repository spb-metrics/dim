<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();
/////////////////////////////////////////////////////////////////
//  Sistema..: DIM - Dispensação Individualizada de Medicamentos
//  Arquivo..: start.php
//  Bancos...: dbtdim
//  Data.....: 06/11/2006
//  Analista.: Denise Ike
//  Função...: Tela de início do sistema
//////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////
//TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
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
             echo "<script>window.alert('Existe Pedido de Remanejamento Aguardando Efetivação - Tela: ".$aplicacao1." \\n e Pedido de Remanejamento Para Ser Atendido - Tela: ".$aplicacao2."');</script>";
            }
            else{
                 if ($rem_info->status_2=="RESERVADA"){echo "<script>window.alert('Existe Pedido de Remanejamento Aguardando Efetivação - Tela: ".$aplicacao1."');</script>";}
                 if ($rem_info->status_2=="SOLICITADA"){echo "<script>window.alert('Existe Pedido de Remanejamento Para Ser Atendido por Essa Unidade - Tela: ".$aplicacao2." ');</script>";}
            }
         }
         $_SESSION[flag_remanj]="S";
       }
  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
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
    echo "<script>window.alert('Base no " . $_GET[nome] . " não selecionada!');</script>";
  }
  if($_GET[erro]=="f"){
    echo "<script>window.alert('Endereço IP inválido: " . $_GET[dns] . "!');</script>";
  }
  if($_GET[conexao]=="f"){
    echo "<script>window.alert('Conexão não informada para a unidade!');</script>";
  }
  if($_GET[servidor]=="f"){
    echo "<script>window.alert('Conexão com servidor do ". $_GET[nome] ." não efetuada!');</script>";
  }
  if($_GET[banco]=="f"){
    echo "<script>window.alert('Conexão com banco de ". $_GET[nome] ." não efetuada!');</script>";
  }

}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else
{
  include_once("./config/erro_config.php");
}
?>
