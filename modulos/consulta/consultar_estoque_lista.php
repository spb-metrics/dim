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
// | Arquivo ............: consultar_estoque_lista.php                               |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de Detalhamento do Consultar Estoque                 |
// | Data de Criação ....: 24/01/2007 - 14:40                                        |
// | Última Atualização .: 22/02/2007 - 18:15                                        |
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

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
    }
    
    $cod_material = $_GET['codigo'];
    $unidade = $_GET['unidade'];
    $aplicacao = $_GET['aplicacao'];

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/Mult_Pag.php";
    require DIR."/buscar_aplic.php";
?>

<script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
<script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">
 <!--
function atualiza()
{
  document.form_busca.action = '<? echo $PHP_SELF; ?>?paginav=<?=$_GET['paginav']?>&pagina_a_exibirv=<?=$_GET['pagina_a_exibirv']?>&indice=<?=$_GET['indice']?>&buscar=<?=$_GET['buscar']?>&pesquisa=<?=$_GET['pesquisa']?>&codigo=<?=$_GET['codigo']?>&aplicacao=<?=$_GET['aplicacao']?>&unidade=<?=$_GET['unidade']?>';

  document.form_busca.method = "POST";
  document.form_busca.target = "_self";
  document.form_busca.submit();
}
 -->
 </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td> <? echo $caminho?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_busca" action="./consulta_estoque.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" class="opcao_tabela">
                      <td colspan="6" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao?> : Detalhar </td>
                    </tr>
                    <tr class="opcao_tabela" width="21">
        <?
        ///////////////////////////////////////////////////////////////
        //INICIO DA SELEÇÃO DO SELECT USADO PARA VISUALIZAR REGISTROS//
        //        AQUI COMEÇA A DEFINIÇÃO DA TELA EM QUESTÃO         //
        ///////////////////////////////////////////////////////////////
        if(isset($_GET[indice])){$_POST[indice] = $_GET[indice];}
        if(isset($_GET[buscar])) {$_POST[buscar] = $_GET[buscar];}

        /////////////////////////////////////////
        //DE ACORDO COM OPÇÃO, SELECIONAR QUERY//
        /////////////////////////////////////////
        $sql = "select distinct mat.codigo_material as codigo, mat.descricao as medicamento,
                       est.lote as lote, est.validade as validade, fab.descricao as fabricante,
                       est.quantidade as estoque, est.flg_bloqueado as status_est
                from material mat
                     inner join estoque est on mat.id_material = est.material_id_material
                     inner join fabricante fab on est.fabricante_id_fabricante = fab.id_fabricante
                     inner join unidade und on est.unidade_id_unidade = und.id_unidade
                where mat.status_2 = 'A'
                      and mat.flg_dispensavel = 'S'
                      and fab.status_2 = 'A'
                      and und.status_2 = 'A'
                      and est.quantidade <> 0";

        /*echo $unidade."\n";
        echo $unidade01."\n";
        echo "\n01".$_POST[unidade];
        echo "\n02".$_POST[unidade01];
        echo "\n03".$_SESSION[id_unidade_sistema];
        echo "\n04".$_SESSION[nome_unidade_sistema];*/

        if ($unidade <> '')
        {
          $sql = $sql." and und.id_unidade = $unidade";
        }
        //if ($codigo <> '')
        if ($cod_material <> '')
        {
          //$sql = $sql." and mat.id_material = $codigo";
          $sql = $sql." and mat.id_material = $cod_material";
        }

        if(!isset($_POST[indice])){
          $indice="1";
          $_POST[indice]="1";
        }

        switch ($indice)
        {
          case 0:

            $sql = $sql." order by est.lote";
            break;
          case 1:
            $sql = $sql." order by est.validade";
            break;
          case 2:
            $sql = $sql." order by fab.descricao";
            break;
          case 3:
            $sql = $sql." order by est.quantidade";
            break;
        }

//echo        $string_query_registros;
//echo exit;
        //////////////////////////////
        //EXECUTAR QUERY SELECIONADA//
        //////////////////////////////
        $consulta = mysqli_query($db, $sql);
        
        erro_sql("Select Inicial", $db, "");
        if ($_POST['indice']!='')
        {
          if(mysqli_num_rows($consulta) == 0)
          {
            $pesq="f";
          }
        }
         ////////////////////////////////////////////////////////////////
          //INICIO DE DEFINIÇÃO DE VARIÁVEIS PARA PAGINAÇÃO DE REGISTROS//
          ////////////////////////////////////////////////////////////////
          $total_registros = mysqli_num_rows($consulta);
          $paginacao       = 15; //quantidade de registros por página
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
          $pagina_a_exibir = 1;
          //////////////////////////////////////////////////////////
          //DEFINE O INDICE DE INÍCIO DO SELECT CORRENTE, LIMITADO//
          //     PELO VALOR ATRIBUÍDO À VARIÁVEL "$PAGINACAO"     //
          //////////////////////////////////////////////////////////
          $inicio                 = $pagina_exibicao - 1;
          $inicio                 = $inicio * $paginacao;

          $string_query_limite    = "$sql LIMIT $inicio,$paginacao";
          $resultado_query_limite = mysqli_query($db, $string_query_limite);
      
          erro_sql("Select Inicial Limitado", $db, "");

          // definicoes de variaveis
          $max_res = $paginacao; // máximo de resultados à serem exibidos por tela ou pagina
          //$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
		  $mult_pag = new Mult_Pag($pagina_exibicao-1); // cria um novo objeto navbar
          $mult_pag->num_pesq_pag = $max_res; // define o número de pesquisas (detalhada ou não) por página
      ?>
                      <td valign="center" width="72%">&nbsp; Material &nbsp;&nbsp;&nbsp;
                        <input type="text" name="cod_mat" id="cod_mat" style="width: 80px" value="<?=$cod_mat?>" disabled>
                        &nbsp;&nbsp;
                        <input type="text" name="desc_mat" id="desc_mat" style="width: 400px" value="<?=$desc_mat?>" disabled>
                      </td>
                      <td valign="center" width="33%" height="21">&nbsp; Ordenar Lista
                        <select size="1" name="indice" style="width: 100px" onChange="atualiza();">
                          <option value='0' <?=($_POST[indice] == '0')?"selected":""?>>Lote</option>
                          <option value='1' <?=($_POST[indice] == '1')?"selected":""?>>Validade</option>
                          <option value='2' <?=($_POST[indice] == '2')?"selected":""?>>Fabricante</option>
                          <option value='3' <?=($_POST[indice] == '3')?"selected":""?>>Estoque</option>
                        </select>
                      </td>
                    </tr>
             </table>
           </td>
         </tr>
        </form>

         <tr bgcolor='#6B6C8F' class="coluna_tabela" height="21">
          <td width='15%' align='center'>Lote</td>
          <td width='15%' align='center'>Validade</td>
          <td width='30%' align='center'>Fabricante</td>
          <td width='15%' align='center'>Estoque</td>
          <td width='13%' align='center'>Validade</td>
          <td width='12%' align='center'>Status</td>
        </tr>

    <?php
      $cor_linha = "#CCCCCC";
      // cinza claro = #CCCCCC
      // cinza escuro = #EEEEEE
      $num_linha = 0;
      ///////////////////////////////////////
      //INICIO DAS DEFINIÇÕES DE CADA LINHA//
      ///////////////////////////////////////

      $resultado = $mult_pag->Executar($sql, $db, "otimizada", "mysqli");

      while ($estoque = mysqli_fetch_object($resultado_query_limite))
      {
         $num_linha = $num_linha + 1;
         $pos1=strpos($estoque->validade, "-");
         $pos2=strrpos($estoque->validade, "-");
         $validade=substr($estoque->validade, $pos2+1, strlen($estoque->validade)) . "/" . substr($estoque->validade, $pos1+1, 2) . "/" . substr($estoque->validade, 0, 4);
         ?>
           <script>
             document.getElementById("cod_mat").value = "<?=$estoque->codigo?>";
             document.getElementById("desc_mat").value = "<?=$estoque->medicamento?>";
           </script>
         <tr class="linha_tabela" height="18" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?echo $cor_linha; ?>';">
           <td align='left'><?php echo $estoque->lote;?></td>
           <td align='center'><?php echo $validade;?></td>
           <td align='left'><?php echo $estoque->fabricante;?></td>
           <td align='right'><?php echo intval($estoque->estoque);?></td>
           <?php
             $data=date("Y-m-d");
             if($estoque->validade<$data ){
           ?>
               <td align="center"><img src="<?php echo URL . '/imagens/bolinhas/ball_vermelha.gif';?>" border="0" title="Lote Vencido"></td>
           <?php
             }
             else{
           ?>
               <td align="center"><img src="<?php echo URL . '/imagens/bolinhas/ball_verde.gif';?>" border="0" title="Lote Válido"></td>
           <?php
             }
           ?>
           <?
             if ($estoque->status_est == 'S')
             {
           ?>
              <td align="center"><img src="<?php echo URL . '/imagens/bolinhas/ball_vermelha.gif';?>" border="0" title="Lote Bloqueado"></td>
           <?
             }
             else
             {
           ?>
             <td align="center"><img src="<?php echo URL . '/imagens/bolinhas/ball_verde.gif';?>" border="0" title="Lote Liberado"></td>
           <?
             }
           ?>
         </tr>
      <?php
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
            <tr>
              <td colspan='7' valign='bottom'>
<?php
                // pega todos os links e define que 'Próxima' e 'Anterior' serão exibidos como texto plano

                if ($total_paginas > $paginas){$paginas++;}

                // pega todos os links e define que 'Próxima' e 'Anterior' serão exibidos como texto plano
                $todos_links = $mult_pag->Construir_Links("todos", "sim");


?>
                <TABLE name='4' width='100% 'border='0' align='center' valign=bottom cellspacing='0' cellspacing='0'>
                  <TR align='center' valign='top' class="navegacao_tabela">
                    <TD align='right'>
<?
                      ////////////////////////////////////////
                      //DEFININDO BOTÃO PARA PRIMEIRA PÁGINA//
                      ////////////////////////////////////////
                      echo "<a href='".URL."/modulos/consulta/consultar_estoque_lista.php?pagina=0&pagina_a_exibir=1&codigo=$codigo&aplicacao=$aplicacao&unidade=$unidade&indice=".$_POST['indice']."'>
                           <IMG SRC='".URL."/imagens/i.p.first.gif' BORDER='0' title='Ir para a primeira página'>
                           </a>";
?>
                    </td>
                    <td align='right' width='2%'>
<?php
                      //////////////////////////////////////
                      //DEFININDO BOTÃO DE PÁGINA ANTERIOR//
                      //////////////////////////////////////
                      if($pagina_exibicao==1){
                          $pagina_anterior=1;
                        }
                        else{
                          $pagina_anterior = $pagina_exibicao - 1;
                        }
                        echo "<a href='".URL."/modulos/consulta/consultar_estoque_lista.php?pagina=" . ($pagina_anterior-1) . "&pagina_a_exibir=$pagina_anterior&codigo=$codigo&aplicacao=$aplicacao&unidade=$unidade&indice=".$_POST['indice']."'>
                        <IMG SRC='".URL."/imagens/i.p.previous.gif' BORDER='0' title='Ir para a página anterior'>
                        </a>";
?>
                    </td>
                    <?php $tam=count($todos_links);?>
                    <?php if($tam==0){$tam++;}?>
                    <td align='center' width='<?php echo $tam?>%'>
<?php
                      /////////////////////////////
                      //DEFININDO TEXTO DO CENTRO//
                      /////////////////////////////
                      // pega todos os links e define que 'Próxima' e 'Anterior' serão exibidos como texto plano

                      if(count($todos_links) == 0)
                      {
                        echo "[1]";
                      }
                      else
                      {
                        for ($n = 0; $n < count($todos_links); $n++)
                        {
                          if(($n + 1) < count($todos_links))
                          {
                            echo "[" .$todos_links[$n]. "]" . "&nbsp;&nbsp;";
                          }
                          else
                          {
                            echo "[" . $todos_links[$n] . "]";
                          }
                        }
                      }
                      //print_r( $links_limitados);
?>
                    </td>
                    <td align='left' width='2%'>
<?php
                     ///////////////////////////////////////
                     //DEFININDO O BOTÃO DE PRÓXIMA PÁGINA//
                     ///////////////////////////////////////
                     if($pagina_exibicao==$total_paginas){
                         $proxima_pagina=$total_paginas;
                       }
                       else{
                         $proxima_pagina = $pagina_exibicao + 1;
                       }
                       echo "<a href='".URL."/modulos/consulta/consultar_estoque_lista.php?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina&codigo=$codigo&aplicacao=$aplicacao&unidade=$unidade&indice=".$_POST['indice']."'>
                       <IMG SRC='".URL."/imagens/i.p.next.gif' BORDER='0' title='Ir para a próxima página'>
                       </a>";
?>
                    </td>
                    <td align='left'>
<?
                     //////////////////////////////////////
                     //DEFININDO BOTÃO PARA ULTIMA PÁGINA//
                     //////////////////////////////////////
                    $proxima_pagina = $total_paginas;
                       echo "<a href='".URL."/modulos/consulta/consultar_estoque_lista.php?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina&codigo=$codigo&aplicacao=$aplicacao&unidade=$unidade&indice=".$_POST['indice']."'>
                       <IMG SRC='".URL."/imagens/i.p.last.gif' BORDER='0' title='Ir para a última página'>
                       </a>";
?>
                   </td>
                 </TR>
               </TABLE name='4'>
             </td>
           </tr>
         </table name='3'>
       </td>
     </tr>
     <tr>
       <td colspan="2" align="right" class="descricao_campo_tabela">
         <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/consulta/consulta_estoque.php?pagina=<?=$_GET[paginav]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibirv]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>&aplicacao=<?=$aplicacao?>&unidade=<?php echo $unidade;?>'">
       </td>
     </tr>
   </table>
<?php
  ////////////////////
  //RODAPÉ DA PÁGINA//
  ////////////////////
  require DIR."/footer.php";

  ///////////////////////////////////////////////
  //MENSAGENS DE EXCLUSAO, INCLUSÃO E ALTERAÇÃO//
  ///////////////////////////////////////////////
 if(isset($pesq)=='f')
  {
     $redirec= "http://munch.ima.sp.gov.br/~hitoshi/saude_dim/Saude_DIM/modulos/consulta/consulta_estoque.php?aplicacao=65";
     echo "<script>";
     echo "window.alert('Não foi encontrado dados para a pesquisa!')";
     echo "</script>";
     header("Location: $redirec");
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
