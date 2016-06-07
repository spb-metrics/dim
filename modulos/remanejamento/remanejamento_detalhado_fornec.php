<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: remanejamento_detalhado_fornec.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de detalhacao do m�dulo de remanejamento - fornecimento
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }


    if($_GET[codigo]==""){
      header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial_fornec.php");
      exit();
    }
    else{
      $sql="select sol.id_solicita_remanej, u.nome, u.id_unidade, uni.id_unidade as idunidade, ";
      $sql.="sol.status_2 ";
      $sql.="from solicita_remanej as sol, unidade as u, unidade as uni ";
      $sql.="where sol.id_unid_solicitante=u.id_unidade and sol.id_unid_solicitada=uni.id_unidade ";
      $sql.="and id_solicita_remanej='$_GET[codigo]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Solicita��o", $db, "");
      if(mysqli_num_rows($res)>0){
        $solicitacao=mysqli_fetch_object($res);
      }
        $sql_itens="select m.codigo_material, m.descricao, it.qtde_solicita, it.qtde_atendida ";
        $sql_itens.="from item_solicita_remanej as it, material as m ";
        $sql_itens.="where it.material_id_material=m.id_material and id_solicita_remanej='$_GET[codigo]'";
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
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
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_detalhado" action="./remanejamento_detalhado_fornec.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?>: Detalhar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        N� da Solicita��o
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" size="30" style="width: 200px" disabled value="<?php echo $solicitacao->id_solicita_remanej;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitante
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="unidade_solicitante" size="30" disabled style="width: 200px" value="<?php echo $solicitacao->nome;?>">
                      </td>
                    </tr>
                    <tr>
                      <?php
                        $sql="select id_unidade, nome from unidade where id_unidade!='$solicitacao->id_unidade' order by nome";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Unidade Solicitada", $db, "");
                      ?>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitada
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="unidade_solicitada" size="1" style="width: 200px" disabled>
                        <option> Selecione uma Unidade </option>
                        <?php
                          while($unidade_solic=mysqli_fetch_object($res)){
                        ?>
                            <option value="<?php echo $unidade_solic->id_unidade;?>" <?php if($unidade_solic->id_unidade==$solicitacao->idunidade){echo "selected";}?>> <?php echo $unidade_solic->nome;?> </option>
                        <?php
                          }
                        ?>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Status
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="status" size="30" style="width: 200px" disabled value="<?php echo $solicitacao->status_2;?>">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="10%" align="center"> C�digo </td>
                            <td width="60%" align="center"> Material </td>
                            <td width="15%" align="center"> Qtde Solicitada </td>
                            <td width="15%" align="center"> Qtde Atendida </td>
                          </tr>
                          <?php
                            $cor_linha = "#CCCCCC";
                            ///////////////////////////////////////
                            //INICIO DAS DEFINI��ES DE CADA LINHA//
                            ///////////////////////////////////////

                            $res=mysqli_query($db, $sql_itens);
                            erro_sql("Select Lista", $db, "");
                            while($itens_info=mysqli_fetch_object($res)){
                          ?>
                              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>'>
                                <td align="left"> <?php echo $itens_info->codigo_material;?> </td>
                                <td align="left"> <?php echo $itens_info->descricao;?> </td>
                                <?php
                                ?>
                                  <td align="right"> <?php echo $itens_info->qtde_solicita;?> </td>
                                <?php
                                ?>
                                  <td align="right"> <?php echo $itens_info->qtde_atendida;?> </td>
                                <?php
                                ?>
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
                        </table>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inicial_fornec.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>&aplicacao=<?php echo $_SESSION[APLICACAO];?>'">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
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
    //RODAP� DA P�GINA//
    ////////////////////
    require DIR."/footer.php";

  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else{
    include_once "../../config/erro_config.php";
  }
?>
