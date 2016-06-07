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
  //  Sistema..: DIM
  //  Arquivo..: entrada_alteracao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de alteracao de entrada manual
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
              $aux[][]=$_POST[fabricante];
              $aux[][]=$_POST[lote];
              $aux[][]=$_POST[validade];
              $aux[][]=$_POST[quantidade];
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
      header("Location: ". URL."/modulos/entrada/entrada_inclusao.php?nro=$_POST[numero_atual]&aplicacao=$_SESSION[APLICACAO]");
    }
    else{
      if($_GET[linha]=="" && !isset($_POST[flag])){
        header("Location: ". URL."/modulos/entrada/entrada_inclusao.php");
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
      //Validacao de fabricante e lote:        //
      ///////////////////////////////////////////
      function validarLote(lot, fabr, valor){
        var str=lot.value;
        var pos=str.indexOf("#");
        if(pos==-1){
          lot.value="001#" + str;
        }
        else{
          if(pos!=3){
            window.alert("Lote inválido!");
            lot.focus();
            lot.select();
            return false;
          }
          else{
            if(str.lastIndexOf("#")!=3){
              window.alert("Lote inválido!");
              lot.focus();
              lot.select();
              return false;
            }
          }
        }
        str=lot.value;
        var codFabr=str.split("#");
        if(parseInt(codFabr[0])!=fabr.value){
          window.alert("Fabricante e/ou Lote inválidos!");
          if(valor==0){
            lot.focus();
            lot.select();
          }
          else{
            fabr.focus();
          }
          return false;
        }
        return true;
      }
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(fabr, lot, valid, qtde){
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
                  <form name="form_alteracao" action="./entrada_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> Entrada </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Num documento
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" size="30" style="width: 200px" disabled value="<?php echo $valores[0];?>">
                      </td>
                    </tr>
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Alteração </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="descricao" size="30" style="width: 500px" disabled value="<?php echo $material_info->descricao;?>">
                      </td>
                    </tr>
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
                              <option value="<?php echo $fabricante_info->id_fabricante;?>" <?php if(isset($_POST[fabricante])){if($_POST[fabricante]==$fabricante_info->id_fabricante){echo "selected";}}else{if($fabricante_info->id_fabricante==$valores[2]){echo "selected";}}?>> <?php echo $fabricante_info->descricao;?> </option>
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
                        <input type="text" name="lote" size="30" style="width: 200px" value="<?php if(isset($_POST[lote])){echo $_POST[lote];}else{echo $valores[3];}?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Validade
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="validade" size="30" style="width: 200px" onKeyPress="return mascara_data(event,this);" value="<?php if(isset($_POST[validade])){echo $_POST[validade];}else{echo $valores[4];}?>" onblur="verificaData(this,this.value);">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="quantidade" size="30" style="width: 200px"  onKeyPress="return isNumberKey(event);" value="<?php if(isset($_POST[quantidade])){echo $_POST[quantidade];}else{echo $valores[5];}?>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/entrada/entrada_inclusao.php?nro=<?php echo $valores[0];?>&aplicacao=<?php echo $_SESSION[APLICACAO]?>'">
                        <input type="submit" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_alteracao.fabricante, document.form_alteracao.lote, document.form_alteracao.validade, document.form_alteracao.quantidade)){document.form_alteracao.flag.value='t';return true;}else{return false;}">
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
      x.fabricante.focus();
    //-->
    </script>

<?php

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
