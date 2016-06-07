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

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: pesquisa_material.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de pesquisa de material
  //////////////////////////////////////////////////////////////////

    function soma_data($pData, $pDias)//formato BR
    {
      if(ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $pData, $vetData))
      {
        $fAno = $vetData[1];
        $fMes = $vetData[2];
        $fDia = $vetData[3];

        for($x = 1; $x <= $pDias; $x++){
          if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12){
            $fMaxDia = 31;
          }
          elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11){
            $fMaxDia = 30;
          }
          else{
            if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0){
              $fMaxDia = 29;
            }
            elseif($fMes == 2){
              $fMaxDia = 28;
            }
          }
          $fDia++;
          if($fDia > $fMaxDia){
            if($fMes == 12){
              $fAno++;
              $fMes = 1;
              $fDia = 1;
            }
            else{
              $fMes++;
              $fDia = 1;
            }
          }
        }
        if(strlen($fDia) == 1)
          $fDia = "0" . $fDia;
        if(strlen($fMes) == 1)
          $fMes = "0" . $fMes;
        return "$fAno-$fMes-$fDia";
      }
    }

  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if(isset($_POST[pesquisar]) && $_POST[pesquisar]!=""){
      if($_POST[id_operacao_atual]=="restringir"){
        if($_POST[lista_itens_atual]==""){
          $sql = "select id_material, codigo_material, descricao
                  from material
                  where status_2='A'
                        and descricao like '%" . trim($_POST[pesquisar]) . "%'
                  order by descricao";
        }
        else{
          $sql = "select id_material, codigo_material, descricao
                  from material
                  where id_material not in ($_POST[lista_itens_atual])
                        and descricao like '%" . trim($_POST[pesquisar]) . "%'
                        and status_2='A'
                  order by descricao";
        }
        $operacao=$_POST[id_operacao_atual];
      }
      if($_POST[id_operacao_atual]=="entrada" || $_POST[id_operacao_atual]=="lote" || $_POST[id_operacao_atual]=="remanejamento"){
        $sql = "select distinct mat.codigo_material, mat.descricao,
                       udm.unidade, mat.id_material
                from material mat
                     inner join unidade_material udm
                                on mat.unidade_material_id_unidade_material = udm.id_unidade_material
                where mat.status_2='A' and mat.descricao like '%" . trim($_POST[pesquisar]) ."%'
                order by mat.descricao";
        $operacao=$_POST[id_operacao_atual];
      }
      if($_POST[id_operacao_atual]!="entrada" && $_POST[id_operacao_atual]!="lote" && $_POST[id_operacao_atual]!="remanejamento" && $_POST[id_operacao_atual]!="restringir"){
        $sql="select operacao, flg_movto_bloqueado from tipo_movto where id_tipo_movto='$_POST[id_operacao_atual]'";
        $result=mysqli_query($db, $sql);
        erro_sql("Id Operação", $db, "");
        if(mysqli_num_rows($result)>0){
          $movto=mysqli_fetch_object($result);
          $operacao=$movto->operacao;
          $flag_at_b=strtoupper($movto->flg_movto_bloqueado);
          $flag_at_v=strtoupper($movto->flg_movto_vencido);
        }

        if (($operacao == "saida") or ($operacao == "perda"))
        {
          $sql = "select distinct mat.codigo_material, mat.descricao, udm.unidade, mat.id_material
                  from material mat
                       inner join unidade_material udm
                                  on mat.unidade_material_id_unidade_material = udm.id_unidade_material
                       inner join estoque est
                                  on mat.id_material = est.material_id_material
                  where mat.status_2='A'
                        and est.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                        and est.quantidade > 0
                        and mat.descricao like '%" . trim($_POST[pesquisar]) . "%'";
          if ($flag_at_b == "S")
          {
            if ($flag_at_v == "S")
              $sql = $sql." and (est.flg_bloqueado = 'S'";
            else
              $sql = $sql." and est.flg_bloqueado = 'S'";
          }
          else if ($flag_at_b == "N")
          {
            $sql = $sql." and est.flg_bloqueado <> 'S'";
          }

          $sql_param = "select dias_vencto_material from parametro";
          $res_param = mysqli_query($db, $sql_param);
          erro_sql("Select Parâmetro", $db, "");
          if(mysqli_num_rows($res_param) > 0)
          {
            $info_param = mysqli_fetch_object($res_param);
            $vencimento = soma_data(date("Y-m-d"), $info_param->dias_vencto_material) ;
          }

          if ($flag_at_v == "S")
          {
              if ($flag_at_b == "S")
                $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento')";
              else
                $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento'";
          }
          else if ($flag_at_v == "N")
          {
            $vencimento = date("Y-m-d");
            $sql = $sql." and SUBSTRING(est.validade,1,10) > '$vencimento'";
          }
          $sql = $sql." order by mat.descricao";
        }

        if ($operacao == "entrada")
        {
           $sql = "select distinct mat.codigo_material, mat.descricao,
                          udm.unidade, mat.id_material
                   from material mat
                        inner join unidade_material udm
                                   on mat.unidade_material_id_unidade_material = udm.id_unidade_material
                   where mat.status_2='A' and mat.descricao like '%" . trim($_POST[pesquisar]) ."%'
                   order by mat.descricao";
        }
      }
      $res=mysqli_query($db, $sql);
      erro_sql("Select Pesquisa", $db, "");

      if ($_POST[pesquisar]!="")
      {
        if(mysqli_num_rows($res)==0){
          $pesq="f";
        }
      }
    }
  }
?>

<html>
  <head><title> Seleção de Material </title></head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<head>
 <BASE target="_self">
</head>
<script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
<script language="javascript">
  <!--
  function preencheCampos(id, descr, oper, cod){
    if(oper=="restringir" || oper=="remanejamento"){
      args=oper + "|" + id + "|" + cod + "|" + descr;
    }
    if(oper=="entrada" || oper=="saida" || oper=="perda" || oper=="lote"){
     args=oper + "|" + id + "|" + descr;
    }

	if (window.showModalDialog)
	{
		var _R = new Object()
        _R.strArgs=args;
		window.returnValue=_R;
	}
	else
	{
		if (window.opener.SetNameMedicamento)
		{
			window.opener.SetNameMedicamento(args);
		}
	}
	window.close();
  }

  function validarCampo(){
    var x=document.form_pesquisa;
    if(x.pesquisar.value==""){
      window.alert("Favor Preencher Campo Pesquisar!");
      x.pesquisar.focus();
      return false;
    }
    x.submit();
    return true;
  }
  //-->
</script>
<body onload="document.form_pesquisa.pesquisar.focus();">
  <table border="1" cellspacing="0" cellpadding="0" width="100%" height="100%">
    <form name="form_pesquisa" action="pesquisa_material.php" method="POST" enctype="application/x-www-form-urlencoded">
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
            <tr class="titulo_tabela">
              <td colspan="5" valign="middle" align="center" width="100%" height="21"> Pesquisar Material </td>
            </tr>
            <tr class="opcao_tabela">
              <td align="center" width="100%">Material: <input type="text" name="pesquisar" size="20" style="width: 200px" maxlenght="50" onkeypress="return VerificarEnter(event);">
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="salvar" value=" Pesquisar " onclick="return validarCampo();"></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table border="0" cellspacing="1" cellpadding="0"  width="100%">
            <tr class="coluna_tabela" height="21">
              <td align="center" width="15%"> Código </td>
              <td align="center" width="75%"> Material </td>
              <td align="center" width="10%"> Seleção </td>
            </tr>
<?php
            $cor_linha = "#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
            ///////////////////////////////////////
            if(isset($_POST[pesquisar]) && $_POST[pesquisar]!="")
            {
              while ($consulta = mysqli_fetch_object($res))
              {
?>
                <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                  <td><?php echo $consulta->codigo_material;?></td>
                  <td><?php echo $consulta->descricao;?></td>
                  <td align="center"><input type="radio" name="selecao" onclick="preencheCampos('<?php echo $consulta->id_material;?>', '<?php echo $consulta->descricao;?>', '<?php echo $operacao;?>', '<?php echo $consulta->codigo_material;?>');"></td>
                </tr>
<?php
                ////////////////////////
                //MUDANDO COR DA LINHA//
                ////////////////////////
                if($cor_linha=="#EEEEEE"){
                  $cor_linha="#CCCCCC";
                }
                else{
                  $cor_linha="#EEEEEE";
                }
              }
            }
?>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr class="campo_botao_tabela" align="center" class="campo_botao_tabela">
              <td><input type="button" name="fechar" value="Fechar" onclick="window.close();"></td>
            </tr>
          </table>
        </td>
      </tr>
      <input type="hidden" name="id_operacao_atual" value="<?php if(isset($_POST[id_operacao_atual])){echo $_POST[id_operacao_atual];}else{echo $_GET[id_operacao];}?>">
      <input type="hidden" name="lista_itens_atual" value="<?php if(isset($_POST[lista_itens_atual])){echo $_POST[lista_itens_atual];}else{echo $_GET[itens];}?>">
    </form>
  </table>
</body>
<?php
    if($pesq=='f'){echo "<script>window.alert('Não foi encontrado dados para a pesquisa!')</script>";}
?>
