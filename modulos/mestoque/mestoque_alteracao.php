<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

function soma_data($pData, $pDias)//formato BR
{
  if(ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})", $pData, $vetData))
  {
    $fAno = $vetData[1];
    $fMes = $vetData[2];
    $fDia = $vetData[3];

    for($x = 1; $x <= $pDias; $x++)
    {
      if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12)
      {
        $fMaxDia = 31;
      }
      elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11)
      {
        $fMaxDia = 30;
      }
      else
      {
        if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0)
        {
          $fMaxDia = 29;
        }
        elseif($fMes == 2)
        {
          $fMaxDia = 28;
        }
      }
      $fDia++;
      if($fDia > $fMaxDia)
      {
        if($fMes == 12)
        {
          $fAno++;
          $fMes = 1;
          $fDia = 1;
        }
        else
        {
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

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: mestoque_alteracao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de alteracao de movimento de estoque
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }


    if($_POST[flag]=="t"){
      $sql="select id_material from material where codigo_material='$_POST[codigo_atual]' and status_2='A'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Material Selecionado - Salvar", $db, "");
      if(mysqli_num_rows($res)>0){
        $cod_mat=mysqli_fetch_object($res);
      }
      //verificando se a quantidade em estoque eh suficiente
      if($_POST[flag2]!="entrada"){
        $valor_aux=split("[|]", $_POST[lote]);
        $sql="select id_estoque from estoque where fabricante_id_fabricante='$valor_aux[1]' and material_id_material='$cod_mat->id_material' and lote='$valor_aux[0]' and quantidade>='$_POST[quantidade]' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
      }
      $res=mysqli_query($db, $sql);
      erro_sql("Select Qtde Suficiente", $db, "");
      if(mysqli_num_rows($res)<=0 && $_POST[flag2]!="entrada"){
        header("Location: ". URL."/modulos/mestoque/mestoque_alteracao.php?s=f&linha=$_POST[linha_atual]&motivo=$_POST[motivo_atual]&aplicacao=$_SESSION[APLICACAO]");
      }
      else{
        $index=0;
        $str="";
        $lista_materiais=$_SESSION["MATERIAIS"];
        foreach($lista_materiais as $line){
          foreach($line as $colum){
            if($index<QTDE_COLUNA){
              $str.=$colum . "|";
            }
            else{
              $str.=$colum;
            }
            if($index==QTDE_COLUNA){
              $valores=split("[|]", $str);
              $aux[][]=$valores[0];
              $aux[][]=$valores[1];
              $aux[][]=$valores[2];
              if($valores[0]==$_POST[linha_atual]){
                if($_POST[flag2]=="entrada"){
                  $aux[][]=$_POST[fabricante];
                  $aux[][]=$_POST[lote];
                  $aux[][]=$_POST[validade];
                  $aux[][]=$_POST[quantidade];
                }
                else{
                  $data_recuperada=$valor_aux[2];
                  $pos1=strpos($data_recuperada, "-");
                  $pos2=strrpos($data_recuperada, "-");
                  $data_validade=substr($data_recuperada, $pos2+1, strlen($data_recuperada)) . "/" . substr($data_recuperada, $pos1+1, 2) . "/" . substr($data_recuperada, 0, 4);
                  $aux[][]=$valor_aux[1];
                  $aux[][]=$valor_aux[0];
                  $aux[][]=$data_validade;
                  $aux[][]=$_POST[quantidade];
                }
              }
              else{
                $aux[][]=$valores[3];
                $aux[][]=$valores[4];
                $aux[][]=$valores[5];
                $aux[][]=$valores[6];
              }
              $str="";
              $index=0;
            }
            else{
              $index++;
            }
          }
        }
        $_SESSION["MATERIAIS"]=$aux;
        header("Location: ". URL."/modulos/mestoque/mestoque_inclusao.php?numero=$_POST[numero_atual]&motivo=$_POST[motivo_atual]&aplicacao=$_SESSION[APLICACAO]");
      }
    }
    else{
      if(($_GET[linha]=="" || $_GET[motivo]=="") && !isset($_POST[flag])){
        header("Location: ". URL."/modulos/mestoque/mestoque_inclusao.php");
      }
      else{
        if(session_is_registered("MATERIAIS")){
          $lista_materiais=$_SESSION["MATERIAIS"];
          $index=0;
          $str="";
          foreach($lista_materiais as $line){
            foreach($line as $colum){
              if($index==0){
                $nro_linha=$colum;
              }
              else{
                if($index<QTDE_COLUNA){
                  $str.=$colum . "|";
                }
                else{
                  $str.=$colum;
                }
              }
              if($index==QTDE_COLUNA){
                if(isset($_GET[linha])){
                  if($nro_linha==$_GET[linha]){
                    break 2;
                  }
                  else{
                    $str="";
                    $index=0;
                  }
                }
                else{
                  if($nro_linha==$_POST[linha_atual]){
                    break 2;
                  }
                  else{
                    $str="";
                    $index=0;
                  }
                }
              }
              else{
                $index++;
              }
            }
          }
          $valores=split("[|]", $str);
          $sql="select m.codigo_material, m.descricao, u.unidade from material as m, unidade_material as u ";
          $sql.="where m.unidade_material_id_unidade_material=u.id_unidade_material and id_material='$valores[1]' and m.status_2='A'";
          $res=mysqli_query($db, $sql);
          erro_sql("Select Material Selecionado", $db, "");
          $material_info=mysqli_fetch_object($res);
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCamposEntrada(fabr, lot, valid, qtde){
        if(fabr.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          fabr.focus();
          return false;
        }
        if(lot.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          lot.focus();
          lot.select();
          return false;
        }
        if(valid.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          valid.focus();
          valid.select();
          return false;
        }
        if(qtde.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          qtde.focus();
          qtde.select();
          return false;
        }
        return true;
      }
      function validarCamposSaida(lot, qtde){
        if(lot.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          lot.focus();
          return false;
        }
        if(qtde.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          qtde.focus();
          qtde.select();
          return false;
        }
        return true;
      }
      //-->
    </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td> <?php echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%'height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="50%">
                  <form name="form_alteracao" action="./mestoque_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Tipo de Movimento
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%" colspan="3">
                        <select name="numero" size="1" style="width: 200px" disabled>
                          <option> Selecione uma Descrição </option>
                          <?php
                            $sql="select id_tipo_movto, id_tipo_movto, descricao from tipo_movto where flg_movto='s'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Descrição", $db, "");
                            while($numero_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $numero_info->id_tipo_movto;?>" <?php if($numero_info->id_tipo_movto==$valores[0]){echo "selected";}?>> <?php echo $numero_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                      <?php
                        $sql="select operacao, flg_movto_bloqueado, flg_movto_vencido, flg_movto_vencido from tipo_movto where id_tipo_movto='$valores[0]'";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Tipo Movto Selecionado", $db, "");
                        if(mysqli_num_rows($res)>0){
                          $tipo_info=mysqli_fetch_object($res);
                        }
                      ?>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Motivo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <textarea name="motivo" row="2" cols="31" disabled style="width: 500px"><?php if(isset($_POST[motivo_atual])){echo $_POST[motivo_atual];}else{echo $_GET[motivo];}?></textarea>
                      </td>
                    </tr>
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%"> Material: Alteração </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%" colspan="3">
                        <input type="text" name="descricao" size="30" style="width: 500px" disabled value="<?php echo $material_info->descricao;?>">
                      </td>
                    </tr>
                    <?php
                      if($tipo_info->operacao=="entrada"){
                    ?>
                        <tr>
                          <td class="descricao_campo_tabela" valign="middle" width="20%">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Fabricante
                          </td>
                          <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                            <select name="fabricante" size="1" style="width: 200px">
                              <option value="0"> Selecione um Fabricante </option>
                              <?php
                                $sql="select id_fabricante, descricao from fabricante where status_2='A'";
                                $res=mysqli_query($db, $sql);
                                erro_sql("Select Fabricante", $db, "");
                                while($fabricante_info=mysqli_fetch_object($res)){
                              ?>
                                  <option value="<?php echo $fabricante_info->id_fabricante;?>" <?php if($fabricante_info->id_fabricante==$valores[2]){echo "selected";}?>> <?php echo $fabricante_info->descricao;?> </option>
                              <?php
                                }
                              ?>
                            </select>
                          </td>
                        </tr>
                        <tr>
                          <td class="descricao_campo_tabela" valign="middle" width="20%">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Lote
                          </td>
                          <td class="campo_tabela" valign="middle" width="30%">
                            <input type="text" name="lote" size="30" style="width: 200px" value="<?php echo $valores[3];?>">
                          </td>
                          <td class="descricao_campo_tabela" valign="middle" width="15%">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Validade
                          </td>
                          <td class="campo_tabela" valign="middle" width="100%">
                            <input type="text" name="validade" size="30" style="width: 200px" onKeyPress="return mascara_data(event,this);" value="<?php echo $valores[4];?>" onblur="verificaData(this,this.value);">
                          </td>
                        </tr>
                        <tr>
                          <td class="descricao_campo_tabela" valign="middle" width="20%">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Quantidade
                          </td>
                          <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                            <input type="text" name="quantidade" size="30" style="width: 200px"  onKeyPress="return isNumberKey(event);" value="<?php echo $valores[5];?>">
                          </td>
                        </tr>
                    <?php
                      }
                      else{
                    ?>
                        <tr>
                          <td class="descricao_campo_tabela" valign="middle" width="20%">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Lote
                          </td>
                          <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                            <select name="lote" size="1" style="width:500px">
                              <option value="0"> Selecione um Lote </option>
                              <?php
                                $sql = "select distinct est.lote, est.validade, est.quantidade, fab.id_fabricante, fab.descricao
                                        from estoque est
                                             inner join material mat on est.material_id_material = mat.id_material
                                             inner join fabricante fab on est.fabricante_id_fabricante = fab.id_fabricante
                                        where est.material_id_material = '$valores[1]'
                                              and est.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                                              and mat.status_2 = 'A'";

                                if (strtoupper($tipo_info->flg_movto_bloqueado) == "S")
                                {
                                  if (strtoupper($tipo_info->flg_movto_vencido) == "S")
                                    $sql = $sql." and (est.flg_bloqueado = 'S'";
                                  else
                                    $sql = $sql." and est.flg_bloqueado = 'S'";
                                }
                                else if (strtoupper($tipo_info->flg_movto_bloqueado) == "N")
                                {
                                  $sql = $sql." and est.flg_bloqueado <> 'S'";
                                }

                                if (strtoupper($tipo_info->flg_movto_vencido) == "S")
                                {
                                  $sql_param = "select dias_vencto_material from parametro";
                                  $res_param = mysqli_query($db, $sql_param);
                                  erro_sql("Select Parâmetro", $db, "");
                                  if(mysqli_num_rows($res_param) > 0)
                                  {
                                    $info_param = mysqli_fetch_object($res_param);
                                    $vencimento = soma_data(date("Y-m-d"), $info_param->dias_vencto_material) ;
                                    if (strtoupper($tipo_info->flg_movto_bloqueado) == "S")
                                      $sql = $sql." or SUBSTRING(est.validade,1,10) <= '$vencimento')";
                                    else
                                      $sql = $sql." and SUBSTRING(est.validade,1,10) <= '$vencimento'";
                                  }
                                }
                                else if (strtoupper($tipo_info->flg_movto_vencido) == "N")
                                {
                                  $vencimento = date("Y-m-d");
                                  $sql = $sql." and SUBSTRING(est.validade,1,10) > '$vencimento'";
                                }

                                if($tipo_info->operacao != "entrada")
                                {
                                  $sql = $sql." and est.quantidade > 0";
                                }

                                $sql = $sql." order by est.validade";



                                  
                                $res=mysqli_query($db, $sql);
                                erro_sql("Select Lote", $db, "");
                                while($lote_info=mysqli_fetch_object($res)){
                                  $lote_value=$lote_info->lote . "|" . $lote_info->id_fabricante . "|" . $lote_info->validade;
                                  $pos1=strpos($lote_info->validade, "-");
                                  $pos2=strrpos($lote_info->validade, "-");
                                  $validade_info=substr($lote_info->validade, $pos2+1, strlen($lote_info->validade)) . "/" . substr($lote_info->validade, $pos1+1, 2) . "/" . substr($lote_info->validade, 0, 4);
                                  $lote_descricao="Lote:" . $lote_info->lote . " --- Fabricante:" . $lote_info->descricao . " --- Validade:" . $validade_info . " --- Quantidade:" . (int)$lote_info->quantidade;
                                  $data_recuperada=$valores[4];
                                  $pos1=strpos($data_recuperada, "/");
                                  $pos2=strrpos($data_recuperada, "/");
                                  $validade_aux=substr($data_recuperada, $pos2+1, strlen($data_recuperada)) . "-" . substr($data_recuperada, $pos1+1, 2) . "-" . substr($data_recuperada, 0, 2);
                                  $lote_aux=$valores[3] . "|" . $valores[2] . "|" . $validade_aux;
                              ?>
                                  <option value="<?php echo $lote_value;?>" <?php if(isset($_POST[lote])){if($_POST[lote]==$lote_value){echo "selected";}}else{if($lote_value==$lote_aux){echo "selected";}}?>> <?php echo $lote_descricao;?> </option>
                              <?php
                                }
                              ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="descricao_campo_tabela" valign="middle" width="20%">
                            <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                            Quantidade
                          </td>
                          <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                            <input type="text" name="quantidade" size="30" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php if(isset($_POST[quantidade])){echo $_POST[quantidade];}else{echo $valores[5];}?>">
                          </td>
                        </tr>
                    <?php
                      }
                    ?>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/mestoque/mestoque_inclusao.php?numero=<?php echo $valores[0];?>&aplicacao=<?php echo $_SESSION[APLICACAO]?>&motivo=<?php if(isset($_POST[motivo_atual])){echo $_POST[motivo_atual];}else{echo $_GET[motivo];}?>'">
                        <input type="submit" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(document.form_alteracao.flag2.value=='entrada'){if(validarCamposEntrada(document.form_alteracao.fabricante, document.form_alteracao.lote, document.form_alteracao.validade, document.form_alteracao.quantidade)){document.form_alteracao.flag.value='t';return true;}else{return false;}}else{if(validarCamposSaida(document.form_alteracao.lote, document.form_alteracao.quantidade, document.form_alteracao.fabricante)){document.form_alteracao.flag.value='t';return true;}else{return false;}}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="linha_atual" value="<?php if(isset($_POST[linha_atual])){echo $_POST[linha_atual];}else{echo $_GET[linha];}?>">
                    <input type="hidden" name="numero_atual" value="<?php if(isset($_POST[numero_atual])){echo $_POST[numero_atual];}else{echo $valores[0];}?>">
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="flag2" value="<?php echo $tipo_info->operacao;?>">
                    <input type="hidden" name="flag3" value="f">
                    <input type="hidden" name="codigo_atual" value="<?php echo $material_info->codigo_material;?>">
                    <input type="hidden" name="flag4" value="f">
                    <input type="hidden" name="motivo_atual" value="<?php if(isset($_POST[motivo_atual])){echo $_POST[motivo_atual];}else{echo $_GET[motivo];}?>">
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
?>

    <script language="javascript">
    <!--
      var x=document.form_alteracao;
      if(x.flag2.value!="entrada"){
        x.lote.focus();
      }
      else{
        x.fabricante.focus();
      }
    //-->
    </script>

<?php
    if($_GET[s]=='f'){echo "<script>window.alert('Quantidade em estoque insuficiente!');document.form_alteracao.quantidade.focus();</script>";}

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
