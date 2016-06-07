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
// | Arquivo ............: consulta_estoque.php                                      |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de pesquisa de estoque                               |
// | Data de Criação ....: 16/01/2007 - 09:28                                        |
// | Última Atualização .: 22/02/2007 - 18:10                                        |
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
    
    $aplicacao = $_GET['aplicacao'];

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/Mult_Pag.php";
    
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
?>

    <script language="javascript">
    <!--
      function desabilitarBtImpr(){
        document.getElementById("imprimir").disabled=true;
      }

      function imprimirPDF(){
        var x=document.form_argumentos;
        var y=document.form_busca;
        var id_unidade=y.unidade.value;
        var nome_unidade=y.unidade01.value;
        var url="<?php echo URL;?>/modulos/consulta/relatorio_consulta_estoque_pdf.php?aplicacao=<?php echo $_SESSION[APLICACAO]?>&id_unidade=" + id_unidade + "&nome_unidade=" + nome_unidade;
        window.open(url);
        return false;
      }


      function desabilitarCampo(){
        var x=document.form_busca;
        if(x.indice.selectedIndex==1){
          x.buscar.value="";
        }
        else{
          x.buscar.disabled="";
        }
      }
      

      var vetUnd;
      function getUnidade()
      {
      alert (document.form_busca.unidade01.value);
        for(var j=0; vetUnd.length; j++)
         {
          for(var i=0; vetUnd[j].length; i++)
          {
             if(vetUnd[i][j]==document.form_busca.unidade01.value)
             {

               idUnidade = vetUnd[i][j];
               alert (idUnidade);
               document.form_busca.unidade.value = idUnidade;
             }
          }
        }
      }
    //-->
    </script>

<script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td> <? echo $caminho?> </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
<?php
        ///////////////////////////////////////////////////////////////
        //INICIO DA SELEÇÃO DO SELECT USADO PARA VISUALIZAR REGISTROS//
        //        AQUI COMEÇA A DEFINIÇÃO DA TELA EM QUESTÃO         //
        ///////////////////////////////////////////////////////////////
        if(isset($_GET[indice])){$_POST[indice] = $_GET[indice];}
        if(isset($_GET[pesquisa])){$_POST[pesquisa] = $_GET[pesquisa];}
        if(isset($_GET[buscar])) {$_POST[buscar] = $_GET[buscar];}
?>
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_busca" action="./consulta_estoque.php?aplicacao=<?=$_GET['aplicacao']?>" method="POST" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="aplicacao" value="<?=$_GET['aplicacao']?>">
                    <tr class="titulo_tabela" class="opcao_tabela">
                      <td colspan="6" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao?> </td>
                    </tr>
                    <tr class="opcao_tabela" width="21">
                      <td valign="center" width="15%">&nbsp;Unidade: </td>
                      <td colspan="5">
                        <input type="hidden" name="unidade" id="unidade" value="<?=$unidade?>">
                        <input type="textBox" name="unidade01" id="unidade01" style="width: 400px" style="text-transform:uppercase" onFocus="nextfield ='indice'" onchange="document.form_argumentos.unidade.value='';">
                        <div id="acDiv"></div>
                      </td>

                      </tr>
                        <tr class="opcao_tabela" width="21">
                         <td valign="center" width="15%">Pesquisar por: </td>
                         <td width="45%">
                          <select size="1" name="pesquisa" style="width: 100px" onchange="desabilitarCampo();">
                            <option value='0' <?=($_POST[pesquisa] == '0')?"selected":""?>>Material</option>
                            <option value='1' <?=($_POST[pesquisa] == '1')?"selected":""?>>Estoque</option>
                          </select>
                          <input type="text" name="buscar" id="buscar"  size="45" <?php if (isset($_POST[buscar])){echo "value='".$_POST[buscar]."'";}?>>
                         </td>
                         <td valign="center" width="26%" colspan="4" height="21"> Ordenar Lista:
                            <select size="1" name="indice" id="indice" style="width: 100px">
                              <option value='0' <?=($_POST[indice] == '0')?"selected":""?>>Material</option>
                              <option value='1' <?=($_POST[indice] == '1')?"selected":""?>>Estoque</option>
                            </select>
                         <input type="submit" name="submit" style="font-size: 12px;" value=" OK ">
                        </td>
                        </tr>
                        <?
                          $unidade = 0;
                          if ($_POST[unidade01] <> '')
                          {
                        ?>
                            <script>
                              document.getElementById("unidade01").value = "<?=$_POST[unidade01]?>";
                              document.getElementById("unidade").value = "<?=$_POST[unidade]?>";
                            </script>
                        <?
                          }
                          else
                          {
                            if(isset($_GET[unidade])){
                              $sql="select nome, id_unidade from unidade where id_unidade='$_GET[unidade]' and status_2='A'";
                              $res=mysqli_query($db, $sql);
                              erro_sql("Unidade", $db, "");
                              if(mysqli_num_rows($res)>0){
                                $unidade_info=mysqli_fetch_object($res);
                        ?>
                                  <script>
                                    document.getElementById("unidade01").value = "<?php echo $unidade_info->nome;?>";
                                    document.getElementById("unidade").value = "<?php echo $unidade_info->id_unidade;?>";
                                  </script>
                        <?
                              }
                            }
                            else{
                        ?>
                              <script>
                                document.getElementById("unidade01").value = "<?=$_SESSION[nome_unidade_sistema]?>";
                                document.getElementById("unidade").value = "<?=$_SESSION[id_unidade_sistema]?>";
                              </script>
                        <?
                            }
                          }
                        ?>
                      </td>
        <?

        /////////////////////////////////////////
        //DE ACORDO COM OPÇÃO, SELECIONAR QUERY//
        /////////////////////////////////////////
        $string_navegacao = "select mat.codigo_material , mat.descricao, sum(est.quantidade) as estoque, est.lote, est.material_id_material
                               from estoque est
                                   inner join material mat on est.material_id_material = mat.id_material
                                   inner join unidade und on est.unidade_id_unidade = und.id_unidade
                               where mat.status_2 = 'A'
                                   and mat.flg_dispensavel = 'S'
                                   and und.status_2 = 'A'";
                                   
        $string_query_registros = "select mat.codigo_material as codigo, mat.descricao as medicamento,
                                          sum(est.quantidade) as estoque, est.material_id_material as cod_material
                                   from estoque est
                                        inner join material mat on est.material_id_material = mat.id_material
                                        inner join unidade und on est.unidade_id_unidade = und.id_unidade
                                   where mat.status_2 = 'A'
                                         and mat.flg_dispensavel = 'S'
                                         and und.status_2 = 'A'";


        if (($_POST[unidade] <> '') and ($_POST[unidade01] <> ''))
        {
          $unidade01 = $_POST[unidade01];
          if ($uni_dig == 0)
            {
               $unidade = $_POST[unidade];
            }
          else $unidade = $uni_dig;
        }
        else
        {
          if(isset($_GET[unidade])){
            $unidade01=$unidade_info->nome;
            $unidade=$unidade_info->id_unidade;
          }
          else{
            $unidade01 = $_SESSION[nome_unidade_sistema];
            $unidade = $_SESSION[id_unidade_sistema];
          }
        }
        if (($unidade <> '') and ($unidade01 <> ''))
        {
          $string_query_registros = $string_query_registros." and und.id_unidade = '$unidade'";
          $string_navegacao .= " and und.id_unidade = '$unidade'";
        }

        if ($_POST['buscar']!='' and $_POST['pesquisa']=='0')
         {
              $string_query_registros = $string_query_registros." and mat.descricao like '%".trim($_POST['buscar'])."%'";
              $string_navegacao .= " and mat.descricao like '%".trim($_POST['buscar'])."%'";
         }

        switch ($indice)
        {
          case 0:
            $string_query_registros = $string_query_registros." group by medicamento";
            $string_navegacao = $string_navegacao." group by mat.descricao ";
            if ($_POST['pesquisa']=='1')
            {
                $string_query_registros = $string_query_registros." having estoque = '".trim($_POST['buscar'])."'";
                $string_navegacao = $string_navegacao."having estoque = '".trim($_POST['buscar'])."'";
            }
            $string_query_registros = $string_query_registros." order by medicamento";
            $string_navegacao .= " order by mat.descricao";
            break;
            
          case 1:
            $string_query_registros = $string_query_registros." group by medicamento";
            $string_navegacao = $string_navegacao." group by mat.descricao ";
            if ($_POST['pesquisa']=='1')
            {
                $string_query_registros = $string_query_registros." having estoque = '".trim($_POST['buscar'])."'";
                $string_navegacao = $string_navegacao." having estoque = '".trim($_POST['buscar'])."'";
            }
            $string_query_registros = $string_query_registros." order by estoque";
            $string_navegacao .= " order by mat.descricao";
            break;
        }
        
       //echo $string_navegacao;
       //echo exit;
            
//echo $string_navegacao;
//echo        $string_query_registros;
//echo exit;
        //////////////////////////////
        //EXECUTAR QUERY SELECIONADA//
        //////////////////////////////

      $resultado_query_registros = mysqli_query($db, $string_query_registros);
      erro_sql("Select Inicial", $db, "");
        if ($_POST['indice']!='')
        {
          if(mysqli_num_rows($resultado_query_registros) == 0)
          {
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
</tr>
            </table>
          </td>
        </tr>
        </form>

         <tr bgcolor='#6B6C8F' class="coluna_tabela" height="21">
          <td width='55%' align='center'>Material</td>
          <td width='15%' align='center'>Último Movimento</td>
          <td width='9%' align='center'>Estoque</td>
          <td width='9%' align='center'>Validade</td>
          <td width='7%' align='center'>Status</td>
          <td width='5%' align='center'></td>
        </tr>

    <?php
      $cor_linha = "#CCCCCC";
      // cinza claro = #CCCCCC
      // cinza escuro = #EEEEEE
      $num_linha = 0;
      ///////////////////////////////////////
      //INICIO DAS DEFINIÇÕES DE CADA LINHA//
      ///////////////////////////////////////

      $resultado = $mult_pag->Executar($string_navegacao, $db, "consulta_estoque", "mysqli");

      $data = date("Y-m-d");
      $btImprimir="disabled";
      while ($estoque = mysqli_fetch_object($resultado_query_limite))
      {
         $btImprimir="";
         $num_linha = $num_linha + 1;
         $img_bloq = URL."/imagens/bolinhas/ball_verde.gif";  $txt_bloq = 'Lotes Liberados';
         $img_val = URL."/imagens/bolinhas/ball_verde.gif";   $txt_val = 'Lotes Válidos';
               
         $sql = "select est.flg_bloqueado, est.validade
                 from estoque est
                      inner join material mat on est.material_id_material = mat.id_material
                      inner join unidade und on est.unidade_id_unidade = und.id_unidade
                 where mat.status_2 = 'A'
                       and mat.flg_dispensavel = 'S'
                       and und.status_2 = 'A'
                       and est.quantidade>0
                       and und.id_unidade = $unidade
                       and mat.id_material = $estoque->cod_material";

         $sql_query = mysqli_query($db, $sql);
         erro_sql("Select Bloqueado/Validade", $db, "");
         echo mysqli_error($db);
         
         
         
         $sql_dt=  "select max(data_alt) as data
                    from estoque
                    where material_id_material = $estoque->cod_material
                    and unidade_id_unidade = $unidade

                    union all

                    select max(data_incl) as data
                    from estoque
                    where material_id_material = $estoque->cod_material
                    and unidade_id_unidade = $unidade
                    order by data desc";
                    
         $sql_res= mysqli_query($db,$sql_dt);
         erro_sql("Select data movto", $db, "");
         echo mysqli_error($db);
         
         $linha_aux = mysqli_fetch_array($sql_res);
         $data_aux=$linha_aux['data'];
         if ($data_aux!='')
         {
           $parte = explode("-", $data_aux);
           $dia = explode(" ",$parte[2]);
           $data_ultimo_movto=$dia[0]."/".$parte[1]."/".$parte[0];
         }
         else $data_ultimo_movto='';
             
         if (mysqli_num_rows($sql_query) > 0)
         {
           while ($linha = mysqli_fetch_array($sql_query))
           {
             $flg_bloqueado = $linha['flg_bloqueado'];
             $validade = $linha['validade'];
             if ($flg_bloqueado == 'S') // Possui Lotes Bloqueados
             {
               $img_bloq = URL."/imagens/bolinhas/ball_vermelha.gif";
               $txt_bloq = 'Lotes Bloqueados';
             }
             if ($validade < $data) // Possui Lotes Vencidos
             {
               $img_val = URL."/imagens/bolinhas/ball_vermelha.gif";
               $txt_val = 'Lotes Vencidos';
             }
           }
         }
         ?>
         <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?echo $cor_linha; ?>';">
           <td align='left'><?php echo $estoque->medicamento;?></td>
           <td align='center'><?php echo $data_ultimo_movto;?></td>
           <td align='right'><?php echo intval($estoque->estoque);?></td>
           <?
           if (intval($estoque->estoque)>0)
           {
        ?>
           <td align='center'>
             <img src="<?=$img_val?>" border="0" title="<?=$txt_val?>">
           </td>
           <td align='center'>
             <img src="<?=$img_bloq?>" border="0" title="<?=$txt_bloq?>">
           </td>
        <?
         }
          else{
         ?>
         <td align='center'>
             <img src="<?php echo URL;?>/imagens/bolinhas/ball_amarela.gif" border="0" title="Sem Estoque do Medicamento">
           </td>
           <td align='center'>
             <img src="<?php echo URL;?>/imagens/bolinhas/ball_amarela.gif" border="0" title="Sem Estoque do Medicamento">
           </td>
        <?
        }
        ?>
           <td align='center'>

           <?
             if ($pagina_a_exibir=="")
                 $pagina_a_exibir=1;

              if (intval($estoque->estoque)>0)
              {?>
             <a href='<?php echo URL;?>/modulos/consulta/consultar_estoque_lista.php?paginav=<?=$pagina_a_exibir-1?>&pagina_a_exibirv=<?=$pagina_a_exibir?>&indice=<?=$_POST['indice']?>&buscar=<?=$_POST['buscar']?>&pesquisa=<?=$_POST['pesquisa']?>&codigo=<?=$estoque->cod_material?>&aplicacao=<?=$aplicacao?>&unidade=<?=$unidade?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Registro"></a>
           <? } ?>
           </td>
         </tr>
         <?
         ////////////////////////
         //MUDANDO COR DA LINHA//
         ////////////////////////
         if ($cor_linha == "#CCCCCC")
         {
            $cor_linha = "#EEEEEE";
         }
         else
         {
            $cor_linha = "#CCCCCC";
         }
      }
      ////////////////////////////////////////////////
      //RODAPÉ DE NAVEGAÇÃO DE REGISTROS ENCONTRADOS//
      ////////////////////////////////////////////////?>
            <tr><td colspan="7" height="100%"></td></tr>
            <tr align="right" valign="middle" class="navegacao_tabela">
              <td colspan="7">
                <input type="button" name="imprimir" id="imprimir" value="Imprimir" <?php echo $btImprimir;?> onclick="imprimirPDF();">
              </td>
            </tr>
            <tr>
              <td colspan='7' valign='bottom'>
                <TABLE name='4' width='100% 'border='0' align='center' valign=bottom cellspacing='0' cellspacing='0'>
                  <TR align='center' valign='top' class="navegacao_tabela">
                    <TD align='right'>
<?
                      ////////////////////////////////////////
                      //DEFININDO BOTÃO PARA PRIMEIRA PÁGINA//
                      ////////////////////////////////////////
                      $parte_url="/modulos/consulta/consulta_estoque.php";
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
                   </td>
                 </TR>
               </TABLE name='4'>
             </td>
           </tr>
         </table name='3'>
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
    <script language="javascript" type="text/javascript" src="../../scripts/dmsAutoComplete.js"></script>
    <script>
    <!--
      //Instanciar objeto AutoComplete Unidade
      var AC = new dmsAutoComplete('unidade01','acDiv');

      AC.ajaxTarget = '../../xml/dmsUnidade.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       AC.chooseFunc = function(id,label){
          document.form_busca.unidade.value = id;
       }
       teclaTab('unidade01','indice');
//-->

  </script>
<?php
  ////////////////////
  //RODAPÉ DA PÁGINA//
  ////////////////////
  require DIR."/footer.php";

?>
  <script language="javascript">
  <!--
    var x=document.form_busca;
    if(x.buscar.value==""){
      x.buscar.focus();
    }
  //-->
  </script>
<?php

  ///////////////////////////////////////////////
  //MENSAGENS DE EXCLUSAO, INCLUSÃO E ALTERAÇÃO//
  ///////////////////////////////////////////////
  if(isset($pesq)=='f')
  {
     echo "<script>";
     echo "window.alert('Não foi encontrado dados para a pesquisa!');document.form_busca.buscar.focus();";
     echo "</script>";
  }
}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else
{
  include_once "../../config/erro_config.php";
}
?>
</body>
</html>
