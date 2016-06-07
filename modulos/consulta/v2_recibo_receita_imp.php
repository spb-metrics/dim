<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: recibo_receita_imp.php                                    |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Recibo da Receita para Impressão (.php)                   |
// | Data de Criação ....: 12/02/2007 - 10:35                                        |
// | Última Atualização .: 16/02/2007 - 10:10                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
   //   $dis = $_SESSION['id_usuario_sistema'];
    }
  if ($id_movto_geral =='')
   {
    $sql = "select rec.id_receita, rec.ano, rec.unidade_id_unidade, rec.numero, rec.data_emissao,
                   rec.data_ult_disp, pro.nome as prescritor, pac.nome as paciente, pac.id_paciente as id_paciente,
                   usr.nome as dispensador, und.nome as nome_unidade_sistema, usr.matricula
            from receita rec
                 inner join profissional pro on rec.profissional_id_profissional = pro.id_profissional
                 inner join paciente pac on rec.paciente_id_paciente = pac.id_paciente
                 inner join usuario usr on rec.usua_incl = usr.id_usuario
                 inner join unidade und on rec.unidade_id_unidade = und.id_unidade
            where rec.id_receita = $_GET[id_receita] ";
             }
              else
              {
    $sql = "select rec.id_receita, rec.ano, rec.unidade_id_unidade, rec.numero, rec.data_emissao,
                   rec.data_ult_disp, pro.nome as prescritor, pac.nome as paciente, pac.id_paciente as id_paciente,
                   usr.nome as dispensador, und.nome as nome_unidade_sistema, usr.matricula
            from receita rec
                 inner join profissional pro on rec.profissional_id_profissional = pro.id_profissional
                 inner join paciente pac on rec.paciente_id_paciente = pac.id_paciente
                 inner join unidade und on rec.unidade_id_unidade = und.id_unidade
                 left  join movto_geral mov on rec.id_receita = mov.receita_id_receita
                 inner join usuario usr on mov.usuario_id_usuario = usr.id_usuario
            where mov.id_movto_geral = $_GET[id_movto_geral]";
}
    //echo $sql;
    //echo exit;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Receita", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $dados_receita = mysqli_fetch_array($sql_query);

      $id_receita = $dados_receita['id_receita'];
      $nr_receita = $dados_receita['ano']."-".$dados_receita['unidade_id_unidade']."-".$dados_receita['numero'];
      $nome = $dados_receita['paciente'];
      $id_paciente = $dados_receita['id_paciente'];
      
      $sql_cartao = "select cartao_sus from cartao_sus where paciente_id_paciente = '$id_paciente'";
      $res = mysqli_query($db, $sql_cartao);
      erro_sql("Cartão", $db, "");
      echo mysqli_error($db);
      if (mysqli_num_rows($res) > 0)
      {
       $dados_cartao = mysqli_fetch_array($res);
       $cartao_sus = $dados_cartao['cartao_sus'];
      }
      else
      {
       $cartao_sus = '';
      }

      $data_emissao = $dados_receita['data_emissao'];
      $data_dispensasao = $dados_receita['data_ult_disp'];
      $nomeprescritor = $dados_receita['prescritor'];
      $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);
      $data_dispensasao = substr($data_dispensasao,8,2)."/".substr($data_dispensasao,5,2)."/".substr($data_dispensasao,0,4);
      $dispensado = $dados_receita['dispensador'];
      $unidade = $dados_receita['unidade_id_unidade'];
      $und_user = $dados_receita['nome_unidade_sistema'];
      $id_movto_geral = $_GET[id_movto_geral];
      $matricula=$dados_receita[matricula];
    }

?>

<html>
<head>
 <title> Recibo da Receita - Impressão </title>
 <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script type="text/javascript">
 <!--
  function impr_recibo()
  {
    window.print()
  }
  //-->
</script>
<style>
<!--
.cabecalho_recibo
{
  font-family         : Arial, Verdana, Helvetica, sans-serif;
  font-size           : 12px;
  font-weight         : bold;
  color               : #A50000;
}

.cabecalho_recibo01
{
  font-family         : Arial, Verdana, Helvetica, sans-serif;
  font-size           : 10px;
  color               : #000000;
}

.linha_recibo
{
  font-family         : Arial, Verdana, Helvetica, sans-serif;
  font-size           : 14px;
  color               : #000000;
}

.linha_cabecalho
{
  border-right: 1px solid #000000;
  border-bottom: 1px solid #000000;
  border-left: 1px solid #000000;
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}

.linha_cabecalho02
{
  
  border-left: 1px solid #000000;
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.linha_cabecalho03
{
 
  border-top: 1px solid #000000;
  border-right: 1px solid #000000;
  border-left: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_t
{
 
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_b
{
  border-bottom: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_r
{
  border-right: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_l
{
  border-left: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_rb
{
  border-right: 1px solid #000000;
  border-bottom: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_tr
{
  border-right: 1px solid #000000;
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_lb
{
  border-bottom: 1px solid #000000;
  border-left: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}

.celula_lbt
{
  border-bottom: 1px solid #000000;
  border-left: 1px solid #000000;
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula_rbt
{
  border-bottom: 1px solid #000000;
  border-right: 1px solid #000000;
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}

.celula_bt
{
  border-bottom: 1px solid #000000;
  border-top: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula
{
  border-right: 1px solid #000000;
  border-left: 1px solid #000000;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}
.celula01
{
  border-right: 1px solid #FFFFFF;
  border-bottom: 1px solid #FFFFFF;
  border-left: 1px solid #FFFFFF;
  border-top: 1px solid #FFFFFF;
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}



.linha_tabela01{
  font-family: Arial, Verdana, Helvetica, sans-serif;
  font-size: 10px;
  color: black;
}

.linha_pontilhada{
  color: gray;
  border: 2px dashed;
}
//-->
</style>
</head>
<body>
  <table border="0" cellspacing="0" cellpadding="0" width="98%" height="20%" align="center">
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr class="cabecalho_recibo">
              <td rowspan="2" align="center" width="10%">
                <img src="../../imagens/brasao_peqno.jpg" width="66" height="69" border="0">
              </td>
              <td align="center" width="80%">
                Unidade: <?=$und_user?></td>
              <td rowspan="2" align="center" width="10%">
                <img src="../../imagens/DIM_logo_pequeno.jpg" width="63" height="48" border="0">
              </td>
            </tr>
            <tr class="cabecalho_recibo">
              <td valign="top" align="center" width="80%">
                Recibo da Receita
              </td>
            </tr>
          </table>
          <hr>
        </td>
      </tr>
      <tr>
        <td>

        <? include "v2_receita_itens.php"; ?>

        <hr class="linha_pontilhada">
        <? if($qtde_linhas == 1)
              include "v2_receita_item.php";
           else
           {
           $med_anterior = '';
           include "v2_receita_itens.php";
           }
        ?>

        <tr class="cabecalho_recibo01">
         <table border="0" cellspacing="0" cellpadding="0" width="100%">
          <tr class="cabecalho_recibo01">
           <td align="center" width="50%">
              <input type="button" value="Imprimir" onClick="window.print();">
           </td>
           <td align="center" width="50%">
              <input type="button" value="Fechar" onClick="window.close();">
           </td>
          </tr>
         </table>
        </tr>

</body>
<?php
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
</html>
