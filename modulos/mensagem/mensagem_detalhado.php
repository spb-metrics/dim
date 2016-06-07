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
  //  Arquivo..: mensagem_detalhado.php
  //  Bancos...: dbtdim
  //  Data.....: 04/05/2009
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela inicial do módulo de mensagem
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if($_GET[codigo]!=""){
      $sql="select id_mensagem,
                   date_format(data_inicio, '%d/%m/%Y') as data_inicio,
                   date_format(data_fim, '%d/%m/%Y') as data_fim,
                   mensagem,
                   imagem,
                   status_2
            from mensagem
            where id_mensagem=$_GET[codigo]";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Mensagem", $db, "");
      $message=mysqli_fetch_object($res);
      $codigo=$message->id_mensagem;
      $mensagem=$message->mensagem;
      $data_inicio=$message->data_inicio;
      $data_fim=$message->data_fim;
      $situacao=$message->status_2;
      $imagem=$message->imagem;
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
                  <form name="form_inclusao" action="./mensagem_detalhado.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Detalhar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" value="<?php echo $codigo;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Data Início
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                        <input type="text" name="data_inicio" size="15" style="width: 80px" onKeyPress="return mascara_data(event,this);" value="<?php echo $data_inicio;?>" disabled>
                      </td>

                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>">Data Fim
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                        <input type="text" name="data_fim" size="15" style="width: 80px" onKeyPress="return mascara_data(event,this);" value="<?php echo $data_fim;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Mensagem
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <textarea name="mensagem" row="2" cols="31" style="width: 500px; height: 200px" disabled><?php echo $mensagem;?></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Exibir Imagem
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="radio" name="exibir" value="N" disabled <?php if($imagem==""){echo "checked";}?>> Não
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" name="exibir" value="S" disabled <?php if($imagem!=""){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="file" name="imagem" disabled>
                      </td>
                    </tr>
                    <tr height="50">
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Imagem
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <?php
                          if($imagem!=""){
                        ?>
                          <img src="criar_imagem.php?id_mensagem=<?php echo $codigo;?>" width="50" height="50">
                        <?php
                          }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Situação
                      </td>
                      <td align="left" width="30%" class="campo_tabela" colspan="3">
                          <input type="radio" name="situacao" value="A" <?php if($situacao=="A"){echo "checked";}?> disabled> Ativo
                          &nbsp; &nbsp; &nbsp; &nbsp;
                          <input type="radio" name="situacao" value="I" <?php if($situacao=="I"){echo "checked";}?> disabled> Inativo
                      </td>
                    </tr>

                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/mensagem/mensagem_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                        </font>
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

    <script language='javascript'>
    <!--
      document.form_inclusao.data_inicio.focus();
    //-->
    </script>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
