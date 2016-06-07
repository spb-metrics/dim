<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: consulta_estoque_unidade.php                              |
// | Autor ..............: Fabio Hitoshi Ide                                         |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Tela de pesquisa de estoque                               |
// | Data de Cria��o ....: 16/01/2007 - 09:28                                        |
// | �ltima Atualiza��o .: 22/02/2007 - 18:10                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if($_SESSION['id_usuario_sistema']=='')
    {
      header("Location: ". URL."/start.php");
    }
    
    $aplicacao = $_GET['aplicacao'];
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$material = $_GET['material'];
		$medicamento01 = $_GET['material01'];
	} else {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$material = $_POST['material'];
			$medicamento01 = $_POST['material01'];
		}
	}

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/Mult_Pag.php";
    
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
?>

<script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
<script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left" valign='top'>
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
        //INICIO DA SELE��O DO SELECT USADO PARA VISUALIZAR REGISTROS//
        //        AQUI COME�A A DEFINI��O DA TELA EM QUEST�O         //
        ///////////////////////////////////////////////////////////////
        if(isset($_GET[indice])){$_POST[indice] = $_GET[indice];}
        if(isset($_GET[buscar])) {$_POST[buscar] = $_GET[buscar];}
?>
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_busca" action="./consulta_estoque_unidade.php?aplicacao=<?=$_GET['aplicacao']?>" method="POST" enctype="application/x-www-form-urlencoded">
                    <input type="hidden" name="aplicacao" value="<?=$_GET['aplicacao']?>">
                    <tr class="titulo_tabela" class="opcao_tabela">
                      <td colspan="6" valign="middle" align="center" width="100%" height="21"> <? echo $nome_aplicacao?> </td>
                    </tr>
                    <tr class="opcao_tabela" width="21">
                      <td valign="center" width="50%">Material
                        <input type="hidden" name="material" id="material" value="<?=$material?>">
                        <input type="text" name="material01" id="material01" style="width: 350px" onFocus="nextfield ='indice'" onchange="if (this.value == ''){ document.form_argumentos.medicamento.value = '';}" value="<?=$medicamento01?>">
                        <div id="acDiv"></div>
                        <?
                          if ($_POST[material01] <> '')
                          {
                        ?>
                            <script>
                              document.getElementById("material01").value = "<?=$_POST[material01]?>";
                              document.getElementById("material").value = "<?=$_POST[material]?>";
                            </script>
                        <?
                          }
                          else{
                            if(isset($_GET[material])){
                              $sql="select id_material, descricao from material where id_material='$_GET[material]' and status_2='A'";
                              $res=mysqli_query($db, $sql);
                              erro_sql("Material", $db, "");
                              if(mysqli_num_rows($res)>0){
                                $material_info=mysqli_fetch_object($res);
                        ?>
                                <script>
                                  document.getElementById("material01").value = "<?php echo $material_info->descricao;?>";
                                  document.getElementById("material").value = "<?php echo $material_info->id_material;?>";
                                </script>
                        <?php
                              }
                            }
                          }
                        ?>
                      </td>

                      <td valign="center" width="25%" height="21"> Ordenar Lista
                        <select size="1" name="indice" style="width: 100px">
                          <option value='0' <?=($_POST[indice] == '0')?"selected":""?>>Unidade</option>
                          <option value='1' <?php if(!isset($_POST[indice])){echo "selected";}else{if($_POST[indice] == '1'){echo "selected";}}?>>Estoque</option>
                        </select>
                      </td>
                      <td valign="center" width="100%">
                        <input type="submit" name="submit" style="font-size: 12px;" value=" OK ">
                      </td>
        <?
        /////////////////////////////////////////
        //DE ACORDO COM OP��O, SELECIONAR QUERY//
        /////////////////////////////////////////
        if(isset($_GET[material])){
          $_POST[material]=$material_info->id_material;
          $_POST[material01]=$material_info->descricao;
        }
        $string_navegacao = "select distinct(uni.id_unidade),
                                          uni.nome,
                                          est.material_id_material
                                  from unidade as uni,
                                       estoque as est,
                                       material as mat
                                  where uni.id_unidade=est.unidade_id_unidade and
                                        est.material_id_material=mat.id_material and
                                        uni.status_2='A' and
                                        mat.status_2='A' and
                                        mat.flg_dispensavel='S' and
                                        est.material_id_material='$_POST[material]'";
        $string_query_registros = "select uni.id_unidade,
                                          uni.nome,
                                          est.material_id_material,
                                          sum(est.quantidade) as quantidade
                                  from unidade as uni,
                                       estoque as est,
                                       material as mat
                                  where uni.id_unidade=est.unidade_id_unidade and
                                        est.material_id_material=mat.id_material and
                                        uni.status_2='A' and
                                        mat.status_2='A' and
                                        mat.flg_dispensavel='S' and
                                        est.material_id_material='$_POST[material]'";

        if (($_POST[material] <> '') and ($_POST[material01] <> ''))
        {
          $material01 = $_POST[material01];
          $material = $_POST[material];
        }


        switch ($indice)
        {
          case 0:
/*
            if ($_POST['buscar']!='')
            {
              $string_query_registros = $string_query_registros." and uni.nome like '%".$_POST['buscar']."%'";
              $string_navegacao .= " and uni.nome like '%".$_POST['buscar']."%'";
            }
            else{
              $string_query_registros = $string_query_registros." and uni.nome ='".$_POST['buscar']."'";
              $string_navegacao .= " and uni.nome like '%".$_POST['buscar']."%'";
            }
*/
            $string_query_registros = $string_query_registros." group by uni.nome, est.material_id_material";
            $string_query_registros = $string_query_registros." order by uni.nome";
            $string_navegacao .= " order by uni.nome";
            break;
          case 1:
            $string_query_registros = $string_query_registros." group by uni.nome, est.material_id_material";
            $string_query_registros .= " order by quantidade";
            $string_navegacao .= " order by quantidade";
            break;
        }
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
          //INICIO DE DEFINI��O DE VARI�VEIS PARA PAGINA��O DE REGISTROS//
          ////////////////////////////////////////////////////////////////
          $max_links = 5; // m�ximo de links � serem exibidos
          $total_registros = mysqli_num_rows($resultado_query_registros);
          $paginacao       = 16; //quantidade de registros por p�gina
          $total_paginas   = ceil($total_registros / $paginacao);
          //total de p�ginas necess�rias para exibir estes registros,
          //ceil() arredonda 'para cima'

          /////////////////////////////////////////
          //SE P�GINA A EXIBIR N�O ESTIVER SETADA//
          /////////////////////////////////////////
          if (!$pagina_exibicao)
          {
             $pagina_exibicao = "1";  //defina como 1, pois � a primeira p�gina
          }

		  $pagina_a_exibir = $_GET['pagina_a_exibir'];
          if ($pagina_a_exibir) //se recebeu (via URL) uma p�gina a exibir
          {
             $pagina_exibicao = $pagina_a_exibir; //pagina de exibi��o recebe a p�gina a ser exibida
          }

          //////////////////////////////////////////////////////////
          //DEFINE O INDICE DE IN�CIO DO SELECT CORRENTE, LIMITADO//
          //     PELO VALOR ATRIBU�DO � VARI�VEL "$PAGINACAO"     //
          //////////////////////////////////////////////////////////
          $inicio                 = $pagina_exibicao - 1;
          $inicio                 = $inicio * $paginacao;
          $string_query_limite    = "$string_query_registros LIMIT $inicio,$paginacao";
          $resultado_query_limite = mysqli_query($db, $string_query_limite);
          erro_sql("Select Inicial Limitado", $db, "");

          // definicoes de variaveis
//          if($_POST[material]!="" && $_POST[material01]!=""){
            $max_res = $paginacao; // m�ximo de resultados � serem exibidos por tela ou pagina
            //$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
			$mult_pag = new Mult_Pag($pagina_exibicao-1); // cria um novo objeto navbar
            $mult_pag->num_pesq_pag = $max_res; // define o n�mero de pesquisas (detalhada ou n�o) por p�gina
//          }
      ?>
</tr>
            </table>
          </td>
        </tr>
        </form>

         <tr bgcolor='#6B6C8F' class="coluna_tabela" height="21">
          <td width='65%' align='center'>Unidade</td>
          <td width='15%' align='center'>Estoque</td>
          <td width='10%' align='center'>Validade</td>
          <td width='10%' align='center'>Status</td>
          <td width='10%' align='center'></td>
        </tr>

    <?php
      $cor_linha = "#CCCCCC";
      // cinza claro = #CCCCCC
      // cinza escuro = #EEEEEE
      $num_linha = 0;
      ///////////////////////////////////////
      //INICIO DAS DEFINI��ES DE CADA LINHA//
      ///////////////////////////////////////

//      if($_POST[material]!="" && $_POST[material01]!=""){
        $resultado = $mult_pag->Executar($string_navegacao, $db, "consulta_estoque", "mysqli");
//      }

      $data = date("Y-m-d");
      while ($estoque = mysqli_fetch_object($resultado_query_limite))
      {
         $num_linha = $num_linha + 1;
         $img_bloq = URL."/imagens/bolinhas/ball_verde.gif";  $txt_bloq = 'Lotes Liberados';
         $img_val = URL."/imagens/bolinhas/ball_verde.gif";   $txt_val = 'Lotes V�lidos';
               
         $sql = "select est.flg_bloqueado, est.validade
                 from estoque est
                      inner join material mat on est.material_id_material = mat.id_material
                      inner join unidade und on est.unidade_id_unidade = und.id_unidade
                 where mat.status_2 = 'A'
                       and mat.flg_dispensavel = 'S'
                       and und.status_2 = 'A'
                       and und.id_unidade = $estoque->id_unidade
                       and mat.id_material = $estoque->material_id_material
                       and est.quantidade>0";
         $sql_query = mysqli_query($db, $sql);
         erro_sql("Select Bloqueado/Validade", $db, "");
         echo mysqli_error($db);
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
           <td align='left'><?php echo $estoque->nome;?></td>
           <td align='right'><?php echo intval($estoque->quantidade);?></td>
        <?
           if (intval($estoque->quantidade)>0)
           {
        ?>
           <td align='center'>
             <img src="<?=$img_val?>" border="0" title="<?=$txt_val?>">
           </td>
           <td align='center'>
             <img src="<?=$img_bloq?>" border="0" title="<?=$txt_bloq?>">
           </td>
        <? }
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
           <? if ($pagina_a_exibir=="")
                 $pagina_a_exibir=1;

              if (intval($estoque->quantidade)>0)
              {?>
             <a href='<?php echo URL;?>/modulos/consulta/consultar_estoque_unidade_lista.php?paginav=<?=$pagina_a_exibir-1?>&pagina_a_exibirv=<?=$pagina_a_exibir?>&codigo=<?=$estoque->material_id_material?>&aplicacao=<?=$aplicacao?>&unidade=<?=$estoque->id_unidade?>&indice=<?=$_POST['indice']?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Registro"></a>
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
      //RODAP� DE NAVEGA��O DE REGISTROS ENCONTRADOS//
      ////////////////////////////////////////////////?>
            <tr><td colspan="7" height="100%"></td></tr>
            <tr>
              <td colspan='7' valign='bottom'>
                <TABLE name='4' width='100% 'border='0' align='center' valign=bottom cellspacing='0' cellspacing='0'>
                  <TR align='center' valign='top' class="navegacao_tabela">
                    <TD align='right'>
<?
                      ////////////////////////////////////////
                      //DEFININDO BOT�O PARA PRIMEIRA P�GINA//
                      ////////////////////////////////////////
                      $parte_url="/modulos/consulta/consulta_estoque_unidade.php";
                      $mult_pag->primeria_pagina(URL, $parte_url);
?>
                    </td>
                    <td align='right' width='2%'>
<?php
                      //////////////////////////////////////
                      //DEFININDO BOT�O DE P�GINA ANTERIOR//
                      //////////////////////////////////////
                      $mult_pag->pagina_anterior(URL, $parte_url, $pagina_exibicao);
?>
                    </td>
                    <td align='center' width='<?php $mult_pag->tamanho_links($max_links);?>%'>
<?php
                      /////////////////////////////
                      //DEFININDO TEXTO DO CENTRO//
                      /////////////////////////////

                      // pega todos os links e define que 'Pr�xima' e 'Anterior' ser�o exibidos como texto plano
                      $mult_pag->numeracao_paginas($max_links, $pagina_exibicao);
?>
                    </td>
                    <td align='left' width='2%'>
<?php
                     ///////////////////////////////////////
                     //DEFININDO O BOT�O DE PR�XIMA P�GINA//
                     ///////////////////////////////////////
                     $mult_pag->proxima_pagina(URL, $parte_url, $pagina_exibicao, $total_paginas);
?>
                    </td>
                    <td align='left'>
<?
                     //////////////////////////////////////
                     //DEFININDO BOT�O PARA ULTIMA P�GINA//
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
      /* Defini��o dos estilos do DIV */
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
    <script language="JavaScript" type="text/javascript">
    <!--
      //Instanciar objeto AutoComplete Medicamento
      var ACM = new dmsAutoComplete('material01','acDiv');

      ACM.ajaxTarget = '../../xml/dmsMedicamento.php';
      //Definir fun��o de retorno
      //Esta fun��o ser� executada ao se escolher a palavra
       ACM.chooseFunc = function(id, label){
         document.form_busca.material.value = id;
       }
       teclaTab('material01','indice');
   //-->
   </script>
<?php
  ////////////////////
  //RODAP� DA P�GINA//
  ////////////////////
  require DIR."/footer.php";

?>
  <script language="javascript">
  <!--
    var x=document.form_busca;
    if(x.material.value==""){
      x.material01.focus();
    }
/*
    if(x.material.value==""){
      x.material01.focus();
    }
    else{
      if(x.buscar.value==""){
        x.buscar.focus();
      }
    }
    desabilitarCampo();
*/
  //-->
  </script>
<?php

  ///////////////////////////////////////////////
  //MENSAGENS DE EXCLUSAO, INCLUS�O E ALTERA��O//
  ///////////////////////////////////////////////
  if(isset($pesq)=='f')
  {
     echo "<script>";
     echo "window.alert('N�o foi encontrado dados para a pesquisa!')";
//     echo "window.alert('N�o foi encontrado dados para a pesquisa!');if(document.form_busca.material01.value!=''){document.form_busca.buscar.focus();}";
     echo "</script>";
  }
}

////////////////////////////////////////////
//SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
////////////////////////////////////////////
else
{
  include_once "../../config/erro_config.php";
}
?>
</body>
</html>
