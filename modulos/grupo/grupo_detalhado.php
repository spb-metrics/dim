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
  //  Arquivo..: grupo_detalhado.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de detalhacao de grupo
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
      header("Location: ". URL."/modulos/grupo/grupo_inicial.php");
      exit();
    }
    else{
      $sql="select id_grupo, descricao, flg_tipo_obrigatorio from grupo where id_grupo='$_GET[codigo]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Grupo Escolhido", $db, "");
      if(mysqli_num_rows($res)>0){
        $grupo_info=mysqli_fetch_object($res);
      }
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
                  <form name="form_detalhado" action="./grupo_detalhado.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Detalhar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        C�digo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" style="width: 200px" disabled value="<?php echo $grupo_info->id_grupo?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Grupo
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="descricao" size="30" style="width: 200px" disabled value="<?php echo $grupo_info->descricao?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Tipo Obrigat�rio
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="radio" value="S" name="tipo" disabled <?php if($grupo_info->flg_tipo_obrigatorio=="S"){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" name="tipo" disabled <?php if($grupo_info->flg_tipo_obrigatorio=="N"){echo "checked";}?>> N�o
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/grupo/grupo_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigat�rios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos N�o Obrigat�rios
                      </td>
                    </tr>
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>">
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
