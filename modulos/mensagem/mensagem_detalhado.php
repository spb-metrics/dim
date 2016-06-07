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
  //  Arquivo..: mensagem_detalhado.php
  //  Bancos...: dbtdim
  //  Data.....: 04/05/2009
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela inicial do m�dulo de mensagem
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
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
                        C�digo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" value="<?php echo $codigo;?>" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Data In�cio
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
                        <input type="radio" name="exibir" value="N" disabled <?php if($imagem==""){echo "checked";}?>> N�o
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
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Situa��o
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
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
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
    //RODAP� DA P�GINA//
    ////////////////////
    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
