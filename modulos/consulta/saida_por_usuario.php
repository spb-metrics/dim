<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: recibo_receita_imp.php                                    |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Recibo da Receita para Impress�o (.php)                   |
// | Data de Cria��o ....: 12/02/2007 - 10:35                                        |
// | �ltima Atualiza��o .: 16/02/2007 - 10:10                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
    }

    $und_user=$_SESSION[nome_unidade_sistema];
    $id_receita=$_GET['id_receita'];
    $nr_receita=$_GET['ano']."-".$_GET['id_unidade']."-".$_GET['numero'];
    $situacao=$_GET['situacao'];
    $paciente=$_GET['paciente'];
    $prescritor=$_GET['prescritor'];

/*
    $id_receita=69720;
    $nr_receita="2008-128-291";
    $situacao="ABERTA";
    $paciente=543288;
    $prescritor=87;
*/
      
    $sql = "select nome from profissional where id_profissional = $prescritor";

    $sql_query = mysqli_query($db, $sql);
    erro_sql("profissional", $db, "");
    echo mysqli_error($db);
    
    
    if (mysqli_num_rows($sql_query) > 0)
    {
      $dados_presc = mysqli_fetch_array($sql_query);
    }
    $nome_prescritor= $dados_presc['nome'];
     
    
    $sql = "select nome from paciente where id_paciente = $paciente";

    $sql_query = mysqli_query($db, $sql);
    erro_sql("paciente", $db, "");
    echo mysqli_error($db);
    
    if (mysqli_num_rows($sql_query) > 0)
    {
      $dados_pac = mysqli_fetch_array($sql_query);
    }
    $nome_paciente=$dados_pac['nome'];


     $sql = " select cartao_sus from cartao_sus where paciente_id_paciente= $paciente";
   
     $sql_query = mysqli_query($db, $sql);
     erro_sql("cartao", $db, "");
     echo mysqli_error($db);

     if (mysqli_num_rows($sql_query) > 0)
     {
       $dados_pac = mysqli_fetch_array($sql_query);
     }
     $cartao=$dados_pac['cartao_sus'];
   }
    
?>

<html>
<head>
 <title> Relat�rio de Sa�da por Usu�rio</title>
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

.linha_resultado
{
  font: 10px Arial, Verdana, Helvetica, sans-serif;
}

.linha_cabecalho02
{
  font: 10px bold Arial, Verdana, Helvetica, sans-serif;
  text-decoration: underline;
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
                Relat�rio de Sa�da por Usu�rio
              </td>
            </tr>
          </table>
          <hr>
        </td>
      </tr>
      <tr>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr class="linha_recibo">
              <td colspan="4" align="center" width="100%" >
                <b>N�mero da Receita: <?=$nr_receita;?></b>
              </td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td colspan="4" align="center" width="100%" height="1">&nbsp;</td>
            </tr>
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Paciente:</td>
              <td valign="middle" align="left" width="50%" colspan="4"><?=$nome_paciente;?></td>
            </tr>

            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Cart�o SUS:</td>
              <td valign="middle" align="left" width="50%" colspan="4"><?=$cartao;?></td>
            </tr>
            
            <tr class="cabecalho_recibo01">
              <td valign="middle" align="left" width="15%">Prescritor:</td>
              <td valign="middle" align="left" width="50%"><?=$nome_prescritor;?></td>
              <td valign="middle" align="right" width="20%">Situa��o: &nbsp;&nbsp; </td>
              <td valign="middle" align="left" width="15%"><?=$situacao;?></td>
            </tr>
          </table>
        </td>
      </tr>
      <hr>
      <tr>
        <td>
          <table border="0" align="center" width="100%" cellspacing="0" cellpadding="0" rules=groups frame=void>
      </tr>



  <?
   $sql="select ir.data_ult_disp, u.nome as unidade, m.descricao as material, ir.qtde_prescrita,
        ir.qtde_disp_anterior, ir.qtde_disp_mes
        from itens_receita ir, receita r, unidade u, material m
        where r.id_receita = $id_receita
        and r.id_receita = ir.receita_id_receita
        and m.id_material = ir.material_id_material
        and r.unidade_id_unidade = u.id_unidade
        order by ir.data_ult_disp desc";

    $sql_query = mysqli_query($db, $sql);
    erro_sql("Saida", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {

        
   while($linha = mysqli_fetch_array($sql_query))
    {
      $data_dispensasao = $linha['data_ult_disp'];
      $unidade = $linha['unidade'];
      $material=$linha['material'];
      $qtde_pres=intval($linha['qtde_prescrita']);
      $qtde_anter=intval($linha['qtde_disp_anterior']);
      $qtde_mes=intval($linha['qtde_disp_mes']);
      $data = substr($data_dispensasao,8,2)."/".substr($data_dispensasao,5,2)."/".substr($data_dispensasao,0,4);
      $hora = substr($data_dispensasao,11,2).":".substr($data_dispensasao,14,2).":".substr($data_dispensasao,17,2);
        ?>
            <thead>
        <?

        if ($cont==''||$cont=='1')
        {
          $cont='0';
        ?>
             <tr>
                <td class="linha_cabecalho02"align="left" width="6%" height="18"><b>Data</b></td>
                <td class="linha_cabecalho02"align="left" width="10%" height="18"><b>Unidade de Sa�de</b></td>
                <td class="linha_cabecalho02"align="left" width="15%" height="18"><b>Material / Medicamento</b></td>
                <td class="linha_cabecalho02"align="center" width="6%" height="18"><b>Qtde Prescrita</b></td>
                <td class="linha_cabecalho02"align="center" width="6%" height="18"><b>Qtde Disp Anterior</b></td>
                <td class="linha_cabecalho02"align="center" width="6%" height="18"><b>Qtde Dispensada</b></td>
              </tr>
              <tr><p><p></tr>
        <?
      }
      ?>
               <tr >
                <td class="linha_resultado" width="6%" align="left"><?=$data."<br>".$hora;?></td>

                <td class="linha_resultado" width="10%" align="left"><?=$unidade;?></td>
                <td class="linha_resultado" width="15%" align="left"><?=$material;?></td>
                <td class="linha_resultado" width="6%" align="center"><?=$qtde_pres;?></td>
                <td class="linha_resultado" width="6%" align="center"><?=$qtde_anter;?></td>
                <td class="linha_resultado" width="6%" align="center"><?=$qtde_mes;?></td>
              </tr>
              <tr > <p> </tr>
              <tr > <p> </tr>
              <tr > <p> </tr>
            </thead>
        <?

    } ?>
     <tr>
      <td colspan="6" align="center">
       <br><br><br>
       <input style="font-size: 10px;" type="button" name="voltar" id="voltar" value="Voltar>>"onClick="window.close();">
      </td>
    </tr>
   </table>
</body>
<?php
  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
</html>
