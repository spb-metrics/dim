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
  //  Arquivo..: status_paciente_inicial.php
  //  Bancos...: dbtdim
  //  Data.....: 13/12/2007
  //  Analista.: Ricieri Rocha Conz
  //  Função...: Tela inicial Tipo Conselho Profissional
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    require "../../verifica_acesso.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/Mult_Pag.php";
    require "../../verifica_acesso.php";
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
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
<?

          ///////////////////////////////////////////////////////////////
          //INICIO DA SELEÇÃO DO SELECT USADO PARA VISUALIZAR REGISTROS//
          //        AQUI COMEÇA A DEFINIÇÃO DA TELA EM QUESTÃO         //
          ///////////////////////////////////////////////////////////////
        
          if(isset($_GET[indice])){$_POST[indice] = $_GET[indice];}
          if(isset($_GET[buscar])) {$_POST[buscar]  = $_GET[buscar];}
        
          /////////////////////////////////////////
          //DE ACORDO COM OPÇÃO, SELECIONAR QUERY//
          /////////////////////////////////////////
          if ($_POST['indice']!='' and $_POST['buscar']!='')
          {
             $string_query_registros = "select id_status_paciente, descricao from status_paciente
                                        where ".$_POST['indice']." like '%".trim($_POST['buscar'])."%' and status_2='A'
                                        order by ".$_POST['indice'];
          }
          ///////////////////////////////////////////////////
          //SE $BUSCA ESTIVER VAZIA, SIGNIFICA BUSCA PADRÃO//
          ///////////////////////////////////////////////////
          else
          {
            if(!$_POST['indice']){$_POST['indice']="descricao";}
            $string_query_registros = "select id_status_paciente, descricao from status_paciente where status_2='A' order by ".$_POST['indice'];
          }

          //////////////////////////////
          //EXECUTAR QUERY SELECIONADA//
          //////////////////////////////
          $resultado_query_registros = mysqli_query($db, $string_query_registros);
          erro_sql("Select Inicial", $db, "");

          if ($_POST['indice']!='' and $_POST['buscar']!='')
          {
            if(mysqli_num_rows($resultado_query_registros)==0){
              $pesq="f";
            }
          }

          ////////////////////////////////////////////////////////////////
          //INICIO DE DEFINIÇÃO DE VARIÁVEIS PARA PAGINAÇÃO DE REGISTROS//
          ////////////////////////////////////////////////////////////////
          $max_links = 5; // máximo de links à serem exibidos
          $total_registros = mysqli_num_rows($resultado_query_registros);
          $paginacao       = 16; //quantidade de registros por página
          $total_paginas   = ceil($total_registros / $paginacao);
          //total de páginas necessárias para exibir estes registros,
          //ceil() arredonda 'para cima'

          /////////////////////////////////////////
          //SE PÁGINA A EXIBIR NÃO ESTIVER SETADA//
          /////////////////////////////////////////
          if (!$pagina_exibicao)
          {
             $pagina_exibicao = "1";  //defina como 1, pois é a primeira página
          }

		  $pagina_a_exibir = $_GET['pagina_a_exibir'];
          if ($pagina_a_exibir) //se recebeu (via URL) uma página a exibir
          {
             $pagina_exibicao = $pagina_a_exibir; //pagina de exibição recebe a página a ser exibida
          }

          //////////////////////////////////////////////////////////
          //DEFINE O INDICE DE INÍCIO DO SELECT CORRENTE, LIMITADO//
          //     PELO VALOR ATRIBUÍDO À VARIÁVEL "$PAGINACAO"     //
          //////////////////////////////////////////////////////////
          $inicio                 = $pagina_exibicao - 1;
          $inicio                 = $inicio * $paginacao;
          $string_query_limite    = "$string_query_registros LIMIT $inicio,$paginacao";
          $resultado_query_limite = mysqli_query($db, $string_query_limite);
          erro_sql("Select Inicial Limitado", $db, "");


          // definicoes de variaveis
          $max_res = $paginacao; // máximo de resultados à serem exibidos por tela ou pagina
          //$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
          $mult_pag = new Mult_Pag($pagina_exibicao-1); // cria um novo objeto navbar
          $mult_pag->num_pesq_pag = $max_res; // define o número de pesquisas (detalhada ou não) por página

?>

          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_inicial" action="./status_paciente_inicial.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr class="opcao_tabela">
                      <td valign="middle" width="40%">
                        Ordenar Lista:
                        <select name="indice" style="width: 200px">
                          <option value="id_status_paciente" <?php if($_POST[indice]=="id_status_paciente"){echo "selected";}?>> Código </option>
                          <option value="descricao" <?php if($_POST[indice]=="descricao"){echo "selected";}?>> Situação Paciente </option>
                        </select>
                      </td>
                      <td valign="middle" width="40%">
                        Pesquisar:
                        <input type="text" name="buscar" size="30" style="width: 200px" <?php if (isset($_POST[buscar])){echo "value='".$_POST[buscar]."'";}?>>
                        <input type="submit" name="ok" style="font-size: 12px;" value=" OK ">
                      </td>
                      <td valign="middle" align="right" width="20%">
                        <?php
                          if($inclusao_perfil!=""){
                        ?>
                            <input type="button" style="font-size: 12px;" name="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/statuspaciente/status_paciente_inclusao.php'">
                        <?php
                          }
                          else{
                        ?>
                            <input type="button" style="font-size: 12px;" name="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/statuspaciente/status_paciente_inclusao.php'" disabled>
                        <?php
                          }
                        ?>
                      </td>
                    </tr>
                  </form>
                </table>
              </td>
            </tr>
            <tr class="coluna_tabela">
              <td width='15%'align='center'>
                Código
              </td>
              <td width='73%'align='center'>
                Situação Paciente
              </td>
              <td width='12%' align='center'></td>
            </tr>
     
<?php
            $cor_linha = "#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
            ///////////////////////////////////////

            // metodo que realiza a pesquisa
            $resultado = $mult_pag->Executar($string_query_registros, $db, "otimizada", "mysqli");

            while ($grupo_info = mysqli_fetch_object($resultado_query_limite))
            {
?>
         
               <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                 <td align='left'>
                    <?php echo $grupo_info->id_status_paciente;?>
                 </td>

                 <td align='left'>
                    <?php echo $grupo_info->descricao;?>
                 </td>
                 <td align='center'>
                   <?php
                     if($consulta_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/statuspaciente/status_paciente_detalhado.php?codigo=<?php echo $grupo_info->id_status_paciente;?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Registro"></a>&nbsp&nbsp&nbsp
                   <?php
                     }
                   ?>
                   <?php
                     if($alteracao_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/statuspaciente/status_paciente_alteracao.php?codigo=<?php echo $grupo_info->id_status_paciente;?>'><img src="<?php echo URL;?>/imagens/b_edit.gif" border="0" title="Editar Registro"></a>&nbsp&nbsp&nbsp
                   <?php
                     }
                   ?>
                   <?php
                     if($exclusao_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/statuspaciente/status_paciente_exclusao.php?codigo=<?php echo $grupo_info->id_status_paciente;?>'><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Excluir Registro"></a>
                   <?php
                     }
                   ?>
                 </td>
               </tr>

<?php
               ////////////////////////
               //MUDANDO COR DA LINHA//
               ////////////////////////
               if ($cor_linha == "#EEEEEE")
               {
                 $cor_linha = "#CCCCCC";
               }
               else
               {
                 $cor_linha = "#EEEEEE";
               }
            }
          
            ////////////////////////////////////////////////
            //RODAPÉ DE NAVEGAÇÃO DE REGISTROS ENCONTRADOS//
            ////////////////////////////////////////////////
?>
            <tr><td colspan="7" height="100%"></td></tr>
            <tr>
              <td colspan='7' valign='bottom'>
         
                <TABLE name='4' width='100% 'border='0' align='center' valign=bottom cellspacing='0' cellspacing='0'>

                  <TR align='center' valign='top' class="navegacao_tabela">

                    <TD align='right'>

<?
                      ////////////////////////////////////////
                      //DEFININDO BOTÃO PARA PRIMEIRA PÁGINA//
                      ////////////////////////////////////////
                      $parte_url="/modulos/statuspaciente/status_paciente_inicial.php";
                      $mult_pag->primeria_pagina(URL, $parte_url);
?>

                    </td>
                    <td align='right' width='2%'>

<?php
                      //////////////////////////////////////
                      //DEFININDO BOTÃO DE PÁGINA ANTERIOR//
                      //////////////////////////////////////
                      $mult_pag->pagina_anterior(URL, $parte_url, $pagina_exibicao);
?>

                    </td>
                    <td align='center' width='<?php $mult_pag->tamanho_links($max_links);?>%'>
<?php
                      /////////////////////////////
                      //DEFININDO TEXTO DO CENTRO//
                      /////////////////////////////

                      // pega todos os links e define que 'Próxima' e 'Anterior' serão exibidos como texto plano
                      $mult_pag->numeracao_paginas($max_links, $pagina_exibicao);
?>
                    </td>
                    <td align='left' width='2%'>

<?php
                     ///////////////////////////////////////
                     //DEFININDO O BOTÃO DE PRÓXIMA PÁGINA//
                     ///////////////////////////////////////
                     $mult_pag->proxima_pagina(URL, $parte_url, $pagina_exibicao, $total_paginas);
?>

                    </td>
                    <td align='left'>

<?
                     //////////////////////////////////////
                     //DEFININDO BOTÃO PARA ULTIMA PÁGINA//
                     //////////////////////////////////////
                     $mult_pag->ultima_pagina(URL, $parte_url, $total_paginas);
?>
                   </TD>
                 </TR>
               </TABLE name='4'>
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
      var x=document.form_inicial;
      if(x.buscar.value==""){
        x.buscar.focus();
      }
    //-->
    </script>

<?php

    ///////////////////////////////////////////////
    //MENSAGENS DE EXCLUSAO, INCLUSÃO E ALTERAÇÃO//
    ///////////////////////////////////////////////
    if(isset($pesq)=='f'){echo "<script>window.alert('Não foi encontrado dados para a pesquisa!');document.form_inicial.buscar.focus();</script>";}

    if($_GET[e]=='t'){echo "<script>window.alert('Operação realizada com sucesso')</script>";}
    if($_GET[e]=='f'){echo "<script>window.alert('Não foi possível excluir o Situação Paciente!')</script>";}
    if($_GET[e]=='r'){echo "<script>window.alert('Não é possível excluir Situação Paciente, pois existe Paciente associado!')</script>";}

    if($_GET[i]=='t'){echo "<script>window.alert('Operação realizada com sucesso!')</script>";}
    if($_GET[i]=='f'){echo "<script>window.alert('Não foi possível cadastrar o Situação Paciente!')</script>";}

    if($_GET[a]=='t'){echo "<script>window.alert('Operação realizada com sucesso')</script>";}
    if($_GET[a]=='f'){echo "<script>window.alert('Não foi possível alterar o Situação Paciente!')</script>";}
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
