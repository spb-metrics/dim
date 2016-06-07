<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  // +---------------------------------------------------------------------------------+
  // | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
  // +---------------------------------------------------------------------------------+
  // | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
  // | Arquivo ............: pacientes_cadastrados_dim.php                             |
  // | Autor ..............: Fabio Hitoshi Ide
  // +---------------------------------------------------------------------------------+
  // | Função .............: Tela de argumentos do Relatório Pacientes Atenção Contin  |
  // | Data de Criação ....: 23/01/2007 - 09:15                                        |
  // | Última Atualização .: 15/03/2007 - 09:15                                        |
  // | Versão .............: 1.0.0                                                     |
  // +---------------------------------------------------------------------------------+

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>

    <script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript" type="text/javascript" src = "../../scripts/prototype.js"></script>
    <script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
    <script language="javascript" type="text/javascript" src = "../../scripts/auto_completar_unidade.js"></script>
    <script language="JavaScript" type="text/javascript">
    <!--
    
    function exportar01()
    {
      //document.form_argumentos.action = "relatorio_pac_aten_cont_csv.php";
      document.form_argumentos.method = "POST";
      document.form_argumentos.target = "_blank";
      document.form_argumentos.submit();
      return false;
    }


    function TrimJS()
    {
        String = document.form_argumentos.unidade01.value;
        Resultado = String

        //Retira os espaços do inicio
        //Enquanto o primeiro caracter for igual à "Espaço"
        //1 caracter do inicio é removido

        var i
        i = 0

        while (Resultado.charCodeAt(0) == '32')
        {
           Resultado = String.substring(i,String.length);
           i++;
        }

        //Pega a string já formatada e agora retira os espaços do final
        //mesmo esquema, enquanto o ultimo caracter for um espaço,
        //ele retira 1 caracter do final...

        while(Resultado.charCodeAt(Resultado.length-1) == "32")
        {
            Resultado = Resultado.substring(0,Resultado.length-1);
        }
        document.form_argumentos.unidade01.value = Resultado;

        String = ""
    }
    
    function procura_unidade_nome()
    {
        var unidade = document.form_argumentos.unidade01.value;
        var tam = unidade.length;

        for (var i=0; i<tam; i++)
        {
            unidade = unidade.replace("+","~");
        }
        var url = "../../xml/procura_unidade_nome.php?descricao="+unidade;
        requisicaoHTTP("GET", url, true, '');
        
    }

    function trataDados()
    {
	    var info = ajax.responseText;  // obtém a resposta como string
        if (info == 'uninao')
        {
          alert('Unidade Inválida!');
          document.form_argumentos.unidade01.select();
          document.form_argumentos.unidade01.setfocus();
        }
        else
        {
          if (info.substring(3) != 'uni')
          {
            document.form_argumentos.unidade.value = info.substring(3);
            exportar01();
          }
        }
    }
    
    -->
    </script>
<?php
    $sql = "select im01.descricao as menu_sec, im02.descricao as menu_pri
           from item_menu im01
           inner join item_menu im02 on im02.id_item_menu = im01.item_menu_id_item_menu
           where im01.aplicacao_id_aplicacao = $aplicacao";
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Aplicação", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $menu_pri = $linha['menu_pri'];
      $menu_sec = $linha['menu_sec'];
    }
?>
    <input type="hidden" name="id_unidade_sistema" id="id_unidade_sistema" value="<?echo $_SESSION[id_unidade_sistema]?>">
    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
            <tr><td> <? echo $caminho; ?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='1' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_argumentos" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao; ?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Unidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">
                            <input type="hidden" name="unidade" id="unidade" value="<?=$unidade?>">
                            <input type="textBox" name="unidade01" id="unidade01" style="width: 250px" style="text-transform:uppercase"
                                        value="<?php echo $unidade01;?>">
                            <div id="acDiv"></div>
                          <?
                      //  }
                        ?>
                      </td>
                    </tr>
                    <?php
                      $sql="select * from atencao_continuada where status_2='A' order by descricao";
                      $res=mysqli_query($db, $sql);
                      erro_sql("Atenção Continuada", $db, "");
                    ?>
                    <tr>
                      <script>
                          document.form_argumentos.unidade01.focus();
                          teclaTab('unidade01','atencao');
                      </script>
                      <td class="descricao_campo_tabela" valign="center" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Atenção Continuada
                      </td>
                      <td class="campo_tabela" colspan="3" valign="center" width="20%">
                        <select size="1" name="atencao" style="width: 150px">
                          <option value='todos'>Todas</option>
                          <?php
                            while($atencao_info=mysqli_fetch_object($res)){
                          ?>
                              <option value='<?php echo $atencao_info->id_atencao_continuada;?>'><?php echo $atencao_info->descricao;?></option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="center" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Exibir Paciente
                      </td>
                      <td class="campo_tabela" colspan="3" valign="center" width="20%">
                        <select size="1" name="paciente" style="width: 150px">
                          <option value='1'>Com Dispensação</option>
                          <option value='2' selected>Sem Dispensação</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="center" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Ordenado por
                      </td>
                      <td class="campo_tabela" valign="center" width="80%" colspan="3" height="21" value="<?=$ordem?>">
                        <select size="1" name="ordem" style="width: 150px">
                          <option value='2' selected>Data Última Dispensação</option>
                          <option value='1'>Paciente</option>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                        <input type="button" style="font-size: 12px;" name="csv" value="  Exportar CSV  " onClick="if (document.form_argumentos.unidade01.value==''){document.form_argumentos.action = 'relatorio_pac_aten_cont_csv.php';exportar01()}else{document.form_argumentos.action = 'relatorio_pac_aten_cont_csv.php';TrimJS(); procura_unidade_nome();};">
                        <input type="button" style="font-size: 12px;" name="pdf" value=" Visualizar PDF " onClick="if (document.form_argumentos.unidade01.value==''){document.form_argumentos.action = 'relatorio_pac_aten_cont_pdf.php';exportar01()}else{document.form_argumentos.action = 'relatorio_pac_aten_cont_pdf.php';TrimJS(); procura_unidade_nome();};">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="aplicacao" value="<?=$_GET['aplicacao']?>">
                    <input type="hidden" name="nome_und" value="<?=$_SESSION[nome_unidade_sistema]?>">
                    <input type="hidden" name="codigos" value="<?=$codigos?>">
                  </form>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <style type="text/css">
    <!--
      /* Definição dos estilos do DIV */
      /* CSS for the DIV */
      #acDiv{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDiv UL{ list-style:none; margin: 0; padding: 0; }
      #acDiv UL LI{ display:block;}
      #acDiv A{ color:#000000; text-decoration:none; }
      #acDiv A:hover{ color:#000000; }
      #acDiv LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>
    <script language="javascript" type="text/javascript" src="../../scripts/auto_completar_unidade.js"></script>
    <script>
    <!--
      //Instanciar objeto AutoComplete
      var AC = new dmsAutoComplete('unidade01','acDiv', 'id_unidade_sistema', 'unidade');
      //
      AC.ajaxTarget = '../../xml/dmsUnidadeRelatorio.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       AC.chooseFunc = function(id,label)
       {
         document.form_argumentos.unidade.value = id;

         if (id == '')
         {
          TrimJS();
          procura_unidade_nome();
         }
       }

       //teclaTab('unidade01','atencao');
//-->

  </script>
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
