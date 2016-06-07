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
  //  Arquivo..: lote_detalhado.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de detalhacao de lote
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }


    if($_GET[codigo]=="" && $_GET[lote]=="" && $_GET[fabr]){
      header("Location: ". URL."/modulos/lote/lote_inicial.php");
      exit();
    }
    else{
      $sql="select m.codigo_material, m.id_material, m.descricao, u.unidade, e.lote, f.id_fabricante, e.validade, e.quantidade, e.flg_bloqueado, e.motivo_bloqueio ";
      $sql.="from estoque as e, material as m, fabricante as f, unidade_material as u ";
      $sql.="where e.material_id_material=m.id_material and e.fabricante_id_fabricante=f.id_fabricante ";
      $sql.="and m.unidade_material_id_unidade_material=u.id_unidade_material and f.status_2='A' and m.status_2='A' and e.material_id_material='$_GET[codigo]' and e.lote='$_GET[lote]' and e.fabricante_id_fabricante='$_GET[fabr]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Material Escolhido", $db, "");
      if(mysqli_num_rows($res)>0){
        $consulta=mysqli_fetch_object($res);
      }
      $sql_unidades="select u.nome, e.quantidade ";
      $sql_unidades.="from estoque as e, unidade as u ";
      $sql_unidades.="where e.unidade_id_unidade=u.id_unidade and u.status_2='A' and ";
      $sql_unidades.="e.material_id_material='$consulta->id_material' and ";
      $sql_unidades.="e.fabricante_id_fabricante='$consulta->id_fabricante' and ";
      $sql_unidades.="e.lote='$consulta->lote' and ";
      $sql_unidades.="e.flg_bloqueado='S' and e.quantidade>0";
      $res=mysqli_query($db, $sql_unidades);
      erro_sql("Select Unidades", $db, "");
      $soma_quantidade=0;
      while($quantidade_bloqueado=mysqli_fetch_object($res)){
        $soma_quantidade+=(int)$quantidade_bloqueado->quantidade;
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
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
                  <form name="form_detalhado" action="./lote_detalhado.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Detalhar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="descricao" size="30" style="width: 500px" disabled value="<?php echo $consulta->descricao?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Lote
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="lote" size="1" style="width: 500px" disabled>
                          <option value="0"> Selecione um Lote </option>
                          <?php
                              $sql="select distinct e.lote, f.id_fabricante, f.descricao, e.validade ";
                              $sql.="from estoque as e, material as m, fabricante as f ";
                              $sql.="where e.fabricante_id_fabricante=f.id_fabricante and ";
                              $sql.="e.material_id_material=m.id_material and e.material_id_material='$consulta->id_material' ";
                              $sql.="and m.status_2='A' and e.quantidade>0 and flg_bloqueado='S'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Lote", $db, "");
                            while($lote_info=mysqli_fetch_object($res)){
                              $lote_value=$lote_info->lote . "|" . $lote_info->id_fabricante . "|" . $lote_info->validade;
                              $pos1=strpos($lote_info->validade, "-");
                              $pos2=strrpos($lote_info->validade, "-");
                              $validade_info=substr($lote_info->validade, $pos2+1, strlen($lote_info->validade)) . "/" . substr($lote_info->validade, $pos1+1, 2) . "/" . substr($lote_info->validade, 0, 4);
                              $lote_descricao="Lote:" . $lote_info->lote . " --- Fabricante:" . $lote_info->descricao . " --- Validade:" . $validade_info;
                              $lote_consulta=$consulta->lote . "|" . $consulta->id_fabricante . "|" . $consulta->validade;
                          ?>
                              <option value="<?php echo $lote_value;?>" <?php if($lote_value==$lote_consulta){echo "selected";}?>> <?php echo $lote_descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="quantidade" size="30" style="width: 200px" disabled value="<?php echo $soma_quantidade;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Bloqueado
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="radio" value="S" name="bloqueado" disabled <?php if($consulta->flg_bloqueado=="S"){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="" name="bloqueado" disabled <?php if($consulta->flg_bloqueado==""){echo "checked";}?>> Não
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Motivo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <textarea name="motivo" row="2" cols="31" disabled style="width: 500px"><?php echo $consulta->motivo_bloqueio;?></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="50%" align="center"> Unidade </td>
                            <td width="50%" align="center"> Quantidade </td>
                          </tr>
                          <?php
                            $cor_linha = "#CCCCCC";
                            ///////////////////////////////////////
                            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
                            ///////////////////////////////////////

                            $res=mysqli_query($db, $sql_unidades);
                            erro_sql("Select Lista", $db, "");
                            while($estoque_bloqueado=mysqli_fetch_object($res)){
                          ?>
                              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>'>
                                <td width="50%" align="left"> <?php echo $estoque_bloqueado->nome;?> </td>
                                <td width="50%" align="right"> <?php echo (int)$estoque_bloqueado->quantidade;?> </td>
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
                          ?>
                          <tr>
                            <td colspan="2" height="100%"></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/lote/lote_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
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

  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else{
    include_once "../../config/erro_config.php";
  }
?>
