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
  //  Arquivo..: material_detalhado.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de detalhacao de material
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


    if($_GET[codigo]==""){
      if($_GET[flag]=="1"){
        header("Location: ". URL."/modulos/consulta/consulta_estoque.php");
      }
      else{
        header("Location: ". URL."/modulos/material/material_inicial.php");
      }
      exit();
    }
    else{
      $sql="select codigo_material, descricao, flg_dispensavel, id_material,
              unidade_material_id_unidade_material,  dias_limite_disp,
              flg_autorizacao_disp, lista_especial_id_lista_especial,
              grupo_id_grupo, subgrupo_id_subgrupo, familia_id_familia,
              tipo_material_id_tipo_material ";
      $sql.="from material ";
      $sql.="where id_material='$_GET[codigo]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Material Escolhido", $db, "");
      if(mysqli_num_rows($res)>0){
        $consulta=mysqli_fetch_object($res);
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
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_detalhado" action="./material_detalhado.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Detalhar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" disabled value="<?php echo $consulta->codigo_material;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="80%">
                        <input type="text" name="descricao" maxlength="60" style="width: 500px" disabled value="<?php echo $consulta->descricao;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Dispensada
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <select name="unidade" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione uma Unidade </option>
                          <?php
                            $sql="select id_unidade_material, unidade from unidade_material";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Unidade Dispensada", $db, "");
                            while($unidade_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $unidade_info->id_unidade_material;?>" <?php if($consulta->unidade_material_id_unidade_material==$unidade_info->id_unidade_material){echo "selected";}?>> <?php echo $unidade_info->unidade;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Dispensável
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="radio" value="S" name="dispensavel" disabled <?php if($consulta->flg_dispensavel=="S"){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" name="dispensavel" disabled <?php if($consulta->flg_dispensavel=="N"){echo "checked";}?>> Não
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Período Dispensável
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="prazo" disabled maxlength="5" style="width: 200px" value="<?php echo $consulta->dias_limite_disp;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Necessita Autorização?
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="radio" value="S" disabled name="autorizacao" <?php if($consulta->flg_autorizacao_disp=="S"){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" disabled name="autorizacao" <?php if($consulta->flg_autorizacao_disp!="S"){echo "checked";}?>> Não
                     </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Lista
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="lista" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione uma Lista </option>
                          <?php
                            $sql="select id_lista_especial, lista from lista_especial where status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Lista", $db, "");
                            while($lista_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $lista_info->id_lista_especial;?>" <?php if($consulta->lista_especial_id_lista_especial==$lista_info->id_lista_especial){echo "selected";}?>> <?php echo $lista_info->lista;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Grupo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="grupo" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione um Grupo </option>
                          <?php
                            $sql="select id_grupo, descricao from grupo where status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $grupo_info->id_grupo;?>" <?php if($consulta->grupo_id_grupo==$grupo_info->id_grupo){echo "selected";}?>> <?php echo $grupo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Subgrupo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="subgrupo" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione um Sub-Grupo</option>
                          <?php
                            $sql="select id_subgrupo, descricao from subgrupo where grupo_id_grupo='$consulta->grupo_id_grupo' and status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Subgrupo", $db, "");
                            while($sbgrupo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $sbgrupo_info->id_subgrupo;?>" <?php if($consulta->subgrupo_id_subgrupo==$sbgrupo_info->id_subgrupo){echo "selected";}?>> <?php echo $sbgrupo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Família
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="familia" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione um Família </option>
                          <?php
                            $sql="select id_familia, descricao from familia where status_2='A' and subgrupo_id_subgrupo='$consulta->subgrupo_id_subgrupo'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Família", $db, "");
                            while($familia_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $familia_info->id_familia;?>" <?php if($consulta->familia_id_familia==$familia_info->id_familia){echo "selected";}?>> <?php echo $familia_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Tipo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="tipo" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione um Tipo </option>
                          <?php
                            $sql="select id_tipo_material, descricao from tipo_material where status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Tipo", $db, "");
                            while($tipo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $tipo_info->id_tipo_material;?>" <?php if($consulta->tipo_material_id_tipo_material==$tipo_info->id_tipo_material){echo "selected";}?>> <?php echo $tipo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <?
                          if($_GET[flag]=="1")
                          {
                            $caminho = URL."/modulos/consulta/consulta_estoque.php";
                          }
                          else
                          {
                            $caminho = URL."/modulos/material/material_inicial.php";
                          }
                        ?>
                      
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/material/material_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
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
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
