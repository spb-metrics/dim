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
  //  Arquivo..: sbgorigem_exclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de exclusao de subgrupo origem
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


    if(isset($_POST[codigo_atual])){
      $data=date("Y-m-d H:i:s");
      $sql="update subgrupo_origem ";
      $sql.="set status_2='I', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]' ";
      $sql.="where id_subgrupo_origem='$_POST[codigo_atual]'";
      mysqli_query($db, $sql);
      erro_sql("Update Subgrupo Origem", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0")
      {
        mysqli_commit($db);
        $aux=$_POST[aux];
        header("Location: ". URL."/modulos/sbgorigem/sbgorigem_inicial.php?e=t&".$aux);
      }
      else
      {
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/sbgorigem/sbgorigem_inicial.php?e=f");
      }
    }
    else{
      if($_GET[codigo]==""){
        header("Location: ". URL."/modulos/sbgorigem/sbgorigem_inicial.php");
      }
      else{
        $sql="select id_subgrupo_origem, grupo_origem_id_grupo_origem, descricao, st_exibir_cidade from subgrupo_origem where id_subgrupo_origem='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Subgrupo Origem Escolhido", $db, "");
        if(mysqli_num_rows($res)>0){
          $consulta=mysqli_fetch_object($res);
        }
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
                  <form name="form_exclusao"action="./sbgorigem_exclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Excluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" style="width: 200px" disabled value="<?php echo $consulta->id_subgrupo_origem;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Grupo Origem de Receita
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <select name="grupo" size="1" disabled style="width: 200px">
                          <option value="0"> Selecione um Grupo </option>
                          <?php
                            $sql="select id_grupo_origem, descricao from grupo_origem where status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo Origem Receita", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $grupo_info->id_grupo_origem;?>" <?php if($consulta->grupo_origem_id_grupo_origem==$grupo_info->id_grupo_origem){echo "selected";}?>> <?php echo $grupo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Sub - Grupo Origem de Receita
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="subgrupo" size="30" disabled value="<?php echo $consulta->descricao;?>">
                      </td>
                    </tr>
					<tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Exibir Cidade:
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="70%">
                        <input id="cidadeSim" type="radio" name="cidadeRadio" value="S" <? echo ($consulta->st_exibir_cidade == "S") ? "checked" : "" ?> disabled /><label for="cidadeSim">Sim</label>&nbsp;&nbsp;&nbsp;
						<input id="cidadeNao" type="radio" name="cidadeRadio" value="N" <? echo ($consulta->st_exibir_cidade == "N") ? "checked" : "" ?> disabled /><label for="cidadeNao">N&atilde;o</label>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/sbgorigem/sbgorigem_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                        <input type="submit" name="excluir" style="font-size: 12px;" value="Excluir >>">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="codigo_atual" value="<?php echo $_GET[codigo];?>">
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET[pesquisa]?>">
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
