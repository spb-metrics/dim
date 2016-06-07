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
  //  Arquivo..: remanejamento_alteracao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de alteracao de remanejamento
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
          if($index<(QTDE_COLUNA-2)){
            $str.=$colum . "|";
          }
          else{
            $str.=$colum;
          }
          if($index==(QTDE_COLUNA-2)){
            $valores=split("[|]", $str);
            $aux[][]=$valores[0];
            $aux[][]=$valores[1];
            $aux[][]=$valores[2];
            $aux[][]=$valores[3];
            if($valores[0]==$_POST[linha_atual]){
              $aux[][]=$_POST[quantidade];
            }
            else{
              $aux[][]=$valores[4];
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
      header("Location: ". URL."/modulos/remanejamento/remanejamento_inclusao.php?unidade_solicitada=$_POST[unidade_solicitada_atual]");
    }
    else{
      if($_GET[linha]=="" && !isset($_POST[flag])){
        header("Location: ". URL."/modulos/renamejamento/remanejamento_inclusao.php");
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
                if($index<(QTDE_COLUNA-2)){
                  $str.=$colum . "|";
                }
                else{
                  $str.=$colum;
                }
              }
              if($index==(QTDE_COLUNA-2)){
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
          $sql.="where m.unidade_material_id_unidade_material=u.id_unidade_material and id_material='$valores[2]' and m.status_2='A'";
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
      function validarCampos(qtde){
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
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_alteracao" action="./remanejamento_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nº da Solicitação
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" size="30" style="width: 200px" disabled>
                      </td>
                    </tr>
                    <tr>
                      <?php
                        $sql="select nome from unidade where id_unidade='$valores[0]'";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Unidade Solicitante", $db, "");
                        if(mysqli_num_rows($res)>0){
                          $unidade_info=mysqli_fetch_object($res);
                        }
                      ?>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitante
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="unidade_solicitante" size="30" disabled style="width: 200px" value="<?php echo $unidade_info->nome;?>">
                      </td>
                    </tr>
                    <tr>
                      <?php
                        $sql="select id_unidade, nome from unidade where status_2='A' and nome!='$valores[0]' order by nome";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Unidade Solicitada", $db, "");
                      ?>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitada
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="unidade_solicitada" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione uma Unidade </option>
                          <?php
                            while($unidade_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $unidade_info->id_unidade;?>" <?php if($unidade_info->id_unidade==$valores[1]){echo "selected";}?>> <?php echo $unidade_info->nome;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="descricao" size="30" style="width: 450px" disabled value="<?php echo $material_info->descricao;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="quantidade" size="30" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php echo $valores[3]?>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inclusao.php?unidade_solicitada=<?php echo $valores[1];?>'">
                        <input type="submit" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_alteracao.quantidade)){document.form_alteracao.flag.value='t'; return true;}else{return false;}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="linha_atual" value="<?php echo $_GET[linha];?>">
                    <input type="hidden" name="unidade_solicitada_atual" value="<?php echo $valores[1];?>">
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
      x.quantidade.focus();
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
