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
// | Arquivo ............: gerar_livro.php                                           |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de argumentos do Livro de Registro                   |
// | Data de Criação ....: 23/01/2007 - 09:15                                        |
// | Última Atualização .: 23/02/2007 - 12:00                                        |
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
	$PHP_SELF = $_SERVER['PHP_SELF'];
	$livro = $_POST['livro'];
	$codigos = $_POST['codigos'];

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR . "/Mult_Pag.php";
    require "../../verifica_acesso.php";
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

function soma_data($pData, $pDias)//formato BR
{
  if(ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $pData, $vetData))
  {
    $fDia = $vetData[1];
    $fMes = $vetData[2];
    $fAno = $vetData[3];

    for($x = 1; $x <= $pDias; $x++)
    {
      if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12)
      {
        $fMaxDia = 31;
      }
      elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11)
      {
        $fMaxDia = 30;
      }
      else
      {
        if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0)
        {
          $fMaxDia = 29;
        }
        elseif($fMes == 2)
        {
          $fMaxDia = 28;
        }
      }
      $fDia++;
      if($fDia > $fMaxDia)
      {
        if($fMes == 12)
        {
          $fAno++;
          $fMes = 1;
          $fDia = 1;
        }
        else
        {
          $fMes++;
          $fDia = 1;
        }
      }
    }
    if(strlen($fDia) == 1)
      $fDia = "0" . $fDia;
    if(strlen($fMes) == 1)
      $fMes = "0" . $fMes;
    return "$fDia/$fMes/$fAno";
  }
}

function subtrai_data($pData, $pDias)//formato BR
{
  if(ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $pData, $vetData))
  {
    $fDia = $vetData[1];
    $fMes = $vetData[2];
    $fAno = $vetData[3];

    for($x = 1; $x <= $pDias; $x++)
    {
      $fDia--;
      if($fDia < 1)
      {
        if($fMes == 1)
        {
          $fAno--;
          $fMes = 12;
          $fDia = 31;
        }
        else
        {
          $fMes--;
          if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12)
          {
            $fMaxDia = 31;
          }
          elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11)
          {
            $fMaxDia = 30;
          }
          else
          {
            if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0)
            {
              $fMaxDia = 29;
            }
            elseif($fMes == 2)
            {
              $fMaxDia = 28;
            }
          }
          $fDia = $fMaxDia;
        }
      }
    }
    if(strlen($fDia) == 1)
      $fDia = "0" . $fDia;
    if(strlen($fMes) == 1)
      $fMes = "0" . $fMes;
    return "$fDia/$fMes/$fAno";
  }
}

function busca_nivel($und_sup, $link)
{
  global $codigos;

  $sql02 = "select id_unidade, unidade_id_unidade, sigla, nome, flg_nivel_superior
            from unidade
            where unidade_id_unidade = $und_sup
                  and status_2 = 'A'";
  $sql_query02 = mysqli_query($link, $sql02);
  erro_sql("Busca Nível", $link, "");
  echo mysqli_error($link);
  while ($linha02 = mysqli_fetch_array($sql_query02))
  {
    $und_sup01 = $linha02['id_unidade'];
    $codigos = $codigos.",".$und_sup01;
    if ($linha02['flg_nivel_superior'] == '1')
    {
      busca_nivel($und_sup01, $link);
    }
  }
}

function busca_data($link)
{
  global $und_sup, $livro;

  $sql = "select max(num_livro) as num_livro
          from controle_livro
		  where unidade_id_unidade = $und_sup
		        and livro_id_livro = $livro";
		        
  //echo $sql;
  $sql_query = mysqli_query($link, $sql);
  erro_sql("Num Livro", $link, "");
  echo mysqli_error($link);
  if (mysqli_num_rows($sql_query) > 0)
  {
    $linha = mysqli_fetch_array($sql_query);
    $num_livro = $linha['num_livro'];
    
    if ($num_livro <> NULL)
    {
      $sql = "select data_final
              from controle_livro
              where unidade_id_unidade = $und_sup
		            and livro_id_livro = $livro
                    and num_livro = $num_livro";

      //echo $sql;
      $sql_query = mysqli_query($link, $sql);
      erro_sql("Data Final", $link, "");
      echo mysqli_error($link);
      if (mysqli_num_rows($sql_query) > 0)
      {
        $linha = mysqli_fetch_array($sql_query);
        $pos1=strpos($linha[data_final], "-");
        $pos2=strrpos($linha[data_final], "-");
        $data=substr($linha[data_final], $pos2+1, 2) . "/" . substr($linha[data_final], $pos1+1, 2) . "/" . substr($linha[data_final], 0, 4);
       $data = soma_data($data, 1);
        ?>
          <script>
            document.getElementById("data_in").value = "<?=$data?>";
            document.getElementById("data_in").disabled = true;
            document.getElementById("data_in01").value = "<?=$data?>";
            document.getElementById("nr_livro").value = "<?=$num_livro?>";
          </script>
        <?
      }
    }
  }
}
?>

<html>
<head>
 <script language="JavaScript" type="text/javascript" src="../../scripts/auto_compl.js"></script>
 <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
 <script language="JavaScript" type="text/javascript">
 <!--
function simular_pdf()
{
 document.getElementById("gravar").value = "";
 document.form_argumentos.action = "gerar_livro_pdf.php";
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_blank";
 document.form_argumentos.submit();
 return false;
}

function gerar_pdf()
{
 simular_pdf();
 document.getElementById("gravar").value = "gravar";
 document.form_argumentos.action = '<? echo $PHP_SELF; ?>?aplicacao=<?=$_GET['aplicacao']?>';
 document.form_argumentos.method = "POST";
 document.form_argumentos.target = "_self";
 document.form_argumentos.submit();
 return false;
}

function atualiza()
{
  document.getElementById("gravar").value = "";
  document.form_argumentos.action = "<? echo $PHP_SELF; ?>?aplicacao=<?=$_GET['aplicacao']?>";
  document.form_argumentos.method = "POST";
  document.form_argumentos.target = "_self";
  document.form_argumentos.submit();
}
    function validarCampos(day, month, year){
      var x=document.form_argumentos;
      if(x.livro.selectedIndex==0){
        window.alert("Preencher Campos Obrigatórios!");
        x.livro.focus();
        return false;
      }
      if(x.data_in.value==""){
        window.alert("Preencher Campos Obrigatórios!");
        x.data_in.focus();
        return false;
      }
      if(x.data_fn.value==""){
        window.alert("Preencher Campos Obrigatórios!");
        x.data_fn.focus();
        return false;
      }
      var data_inicial=x.data_in.value.split("/");
      var data_final=x.data_fn.value.split("/");
      if(data_final[2]<data_inicial[2]){
        window.alert("Data Fim deve ser maior ou igual a Data Início!");
        x.data_fn.focus();
        return false;
      }
      else{
        if(data_final[2]==data_inicial[2]){
          if(data_final[1]<data_inicial[1]){
            window.alert("Data Fim deve ser maior ou igual a Data Início!");
            x.data_fn.focus();
            return false;
          }
          else{
            if(data_final[1]==data_inicial[1] && data_final[0]<data_inicial[0]){
              window.alert("Data Fim deve ser maior ou igual a Data Início!");
              x.data_fn.focus();
              return false;
            }
          }
        }
      }
      if(data_final[2]>year){
        window.alert("Data Fim deve ser menor que a Data Atual!");
        x.data_fn.focus();
        return false;
      }
      else{
        if(data_final[2]==year){
          if(data_final[1]>month){
            window.alert("Data Fim deve ser menor que a Data Atual!");
            x.data_fn.focus();
            return false;
          }
          else{
            if(data_final[1]==month && data_final[0]>=day){
              window.alert("Data Fim deve ser menor que a Data Atual!");
              x.data_fn.focus();
              return false;
            }
          }
        }
      }
      return true;
    }
 -->
 </script>
</head>
<body>
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
                      <td class="campo_tabela" valign="middle" width="30%" colspan="3" height="21">
                      <input type="text" name="unidade" id="unidade" style="width: 220px" value="<?=$_SESSION['nome_unidade_sistema']?>" disabled>
                      <?
                        if ($db == true)
	                    {
                          $und = false;
                          $sql01 = "select id_unidade, nome, flg_nivel_superior
                                    from unidade
                                    where id_unidade = $_SESSION[id_unidade_sistema]
                                          and status_2 = 'A'";
                          $sql_query01 = mysqli_query($db, $sql01);
                          erro_sql("Unidade", $db, "");
                          echo mysqli_error($db);

                          while ($linha01 = mysqli_fetch_array($sql_query01))
                          {
                            $und_sup = $linha01['id_unidade'];
                            $codigos = $und_sup;
                            if ($linha01['flg_nivel_superior'] == '1')
                            {
                              busca_nivel($und_sup, $db);
                              $und = true;
                            }
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr height="21">
                      <td class="descricao_campo_tabela" valign="center" width="20%" height="21">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Livro
                      </td>
                      
                      <td class="campo_tabela" valign="center" width="80%" colspan="3" height="21" value="<?=$livro?>">
                        <select name="livro" style="width: 220px" onChange="atualiza();">
                        <?
                          //echo "<option selected value='$id_termo'>$desc_termo</option>";
                          echo "<option selected value='0'>Selecione um Livro</option>";
                          $sql = "select id_livro, descricao
                                  from livro where status_2='A'
                                  order by descricao";
                          //echo $sql;
                          $sql_query = mysqli_query($db, $sql);
                          erro_sql("Livro", $db, "");
                          echo mysqli_error($db);
	                      while ($linha = mysqli_fetch_array($sql_query))
                          {
                            $id_livro = $linha['id_livro'];
                            $desc_livro = $linha['descricao'];
                            ?>
                            <option value="<?=$id_livro?>" <?=($livro==$id_livro)?"selected":""?>><?=$desc_livro?></option>
                        <?
                          }
                        ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Inicio
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_in" id="data_in" size="15" style="width: 80px" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);" value="<?=$data_in?>" <?=$desabilita?>>
                        <input type="hidden" name="data_in01" id="data_in01" value="<?=$data_in01?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Data Fim
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <input type="text" name="data_fn" id="data_fn" size="15" style="width: 80px" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);" value="<?if ($_POST[data_fn]){echo $data_fn;} else{echo subtrai_data(date("d/m/Y"), 1);}?>" <?=$desabilita?>>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td valign="center" align="center" width="100%" colspan="4" height="35">
                      <?
                        if (($inclusao_perfil != '') and ($und == false))
                        {
                          $desb_btn_sm = '';
                          $desb_btn_gr = '';
                          ?>
                          <script>
                            document.getElementById("data_in").disabled = false;
                            document.getElementById("data_fn").disabled = false;
                          </script>
                          <?
                        }
                        else if (($consulta_perfil != '') and ($und == false))
                        {
                          $desb_btn_sm = '';
                          $desb_btn_gr = 'disabled';
                          ?>
                          <script>
                            document.getElementById("data_in").disabled = false;
                            document.getElementById("data_fn").disabled = false;
                          </script>
                          <?
                        }
                        else
                        {
                          $desb_btn_sm = 'disabled';
                          $desb_btn_gr = 'disabled';
                          ?>
                          <script>
                            document.getElementById("data_in").disabled = true;
                            document.getElementById("data_fn").disabled = true;
                          </script>
                      <?
                        }
                      ?>
                        <?php
                          $dia_sistema=date("d");
                          $mes_sistema=date("m");
                          $ano_sistema=date("Y");
                        ?>
                        <input type="button" style="font-size: 12px;" name="simular" value=" Simular " onClick="if(validarCampos(<?php echo $dia_sistema;?>, <?php echo $mes_sistema;?>, <?php echo $ano_sistema;?>)){simular_pdf();}" <?=$desb_btn_sm?>>
                        <input type="button" style="font-size: 12px;" name="gerar" value="  Gerar  " onClick="if(validarCampos(<?php echo $dia_sistema;?>, <?php echo $mes_sistema;?>, <?php echo $ano_sistema;?>)){gerar_pdf();}" <?=$desb_btn_gr?>>
                        <input type="hidden" name="gravar" id="gravar" value="">
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
                    <input type="hidden" name="nome_und" value="<?=$_SESSION['nome_unidade_sistema']?>">
                    <input type="hidden" name="codigos" value="<?=$codigos?>">
                    <input type="hidden" name="und_sup" value="<?=$und_sup?>">
                    <input type="hidden" name="nr_livro" id="nr_livro" value="<?=$nr_livro?>">
                  </form>
                </table>
<?

  ///////////////////////////////////////////////////////////////
  //INICIO DA SELEÇÃO DO SELECT USADO PARA VISUALIZAR REGISTROS//
  //        AQUI COMEÇA A DEFINIÇÃO DA TELA EM QUESTÃO         //
  ///////////////////////////////////////////////////////////////

if($_POST['gravar'] <> '')
{
  $data_in = $_POST['data_in'];
  $data_in01 = $_POST['data_in01'];
  $data_fn = $_POST['data_fn'];
  $nome_und = $_GET['unidade'];
  $unidade = $_POST['und_sup'];
  $livro = $_POST['livro'];
  $ordem = $_POST['ordem'];
  $aplicacao = $_POST['aplicacao'];
  $und_user = $_POST['nome_und'];
  $codigos = $_POST['codigos'];
  $nr_livro = $_POST['nr_livro']+1;

  //echo $data_fn." ".$data_in." ".$data_in01;
  $campos_obr = "";

  if ($data_in == '')
  {
    if ($data_in01 == '')
      $campos_obr = "\\n - Data Início";
    else $data_in = $data_in01;
  }
  else if ($data_in01 == '')
  {
    if ($data_in == '')
      $campos_obr = "\\n - Data Início";
  }

  if ($data_fn == '')
  {
    $campos_obr = $campos_obr."\\n - Data Fim";
  }

  if ($livro == '')
  {
    $campos_obr = $campos_obr."\\n - Livro";
  }

  if ($campos_obr <> '')
  {
    $msg_erro = "Favor teste preencher os campos obrigatórios.";//.$campos_obr;
  ?>
    <script>
      alert('<?=$msg_erro?>');
    </script>
  <?
  }
  else // Geração do PDF
  {
    $data_atual = strftime("%Y-%m-%d  %T");

    $sql = "select min(mov.id_movto_livro) as id_inicio, max(mov.id_movto_livro) as id_fim
            from movto_livro mov
                 inner join material mat on mov.material_id_material = mat.id_material
                 inner join lista_especial esp on mat.lista_especial_id_lista_especial = esp.id_lista_especial
                 inner join livro liv on esp.livro_id_livro = liv.id_livro
            where mat.status_2 = 'A'
                  and mat.flg_dispensavel = 'S'
                  and esp.status_2 = 'A'";

    if ($unidade <> '')
    {
      $sql = $sql." and mov.unidade_id_unidade = $unidade";
    }

    if ($livro <> '')
      $sql = $sql." and liv.id_livro = $livro";

    $data_inicio = ((substr($data_in,6,4))."-".(substr($data_in,3,2))."-".(substr($data_in,0,2)));
    $data_fim = ((substr($data_fn,6,4))."-".(substr($data_fn,3,2))."-".(substr($data_fn,0,2)));
    $sql = $sql." and SUBSTRING(mov.data_movto,1,10) between '$data_inicio' and '$data_fim'";

    //echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Select ID Início/ID Fim", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $linha = mysqli_fetch_array($sql_query);
      $id_inicio = $linha['id_inicio'];
      $id_fim = $linha['id_fim'];

      if (($id_inicio <> NULL) and ($id_fim <> NULL))
      {

        $sql = "insert into controle_livro (unidade_id_unidade,
                                            livro_id_livro, id_movto_livro_final,
                                            id_movto_livro_incio, data_incl, usua_incl,
                                            data_final, data_inicio, num_livro, status_2)
                values ($unidade, $livro, $id_fim, $id_inicio, '$data_atual', $_SESSION[id_usuario_sistema],
                        '$data_fim', '$data_inicio', $nr_livro, 'GERADO')";

        //echo $sql;
        $sql_query = mysqli_query($db, $sql);
        erro_sql("Insert Controle Livro", $db, "");
        echo mysqli_error($db);
        if(mysqli_errno($db)=="0"){
          mysqli_commit($db);
        }
        else{
          mysqli_rollback($db);
        }
        if ($sql_query == false)
        {
          ?>
          <script>
           alert('Erro ao Gerar Livro!');
          </script>
          <?
        }
        else
        {
        ?>
        <script>
          alert('Livro Gerado com Sucesso!');
        </script>
        <?
        }
      }
      else
      {
      ?>
        <script>
          alert('Não exitem movimentações neste período!');
       </script>
      <?
      }
    }
  }
}

  if ($livro <> '')
  {
    /////////////////////////////////////////
    //DE ACORDO COM OPÇÃO, SELECIONAR QUERY//
    /////////////////////////////////////////
    $sql = "select und.id_unidade, und.nome, lvr.id_livro, lvr.descricao, ctr.num_livro,
                   ctr.data_inicio, ctr.data_final, ctr.status_2
            from controle_livro ctr
                 inner join unidade und on ctr.unidade_id_unidade = und.id_unidade
                 inner join livro lvr on ctr.livro_id_livro = lvr.id_livro
            where ctr.unidade_id_unidade in ($codigos)
                  and ctr.livro_id_livro = $livro
            order by und.nome, lvr.descricao, ctr.num_livro, ctr.data_inicio,
                  ctr.data_final, ctr.status_2 ";
//echo        $string_query_registros;
//echo exit;
  $resultado = mysqli_query($db, $sql);
  erro_sql("Select Inicial", $db, "");

  ////////////////////////////////////////////////////////////////
  //INICIO DE DEFINIÇÃO DE VARIÁVEIS PARA PAGINAÇÃO DE REGISTROS//
  ////////////////////////////////////////////////////////////////
  $total_registros = mysqli_num_rows($resultado);
  if ($total_registros > 0)
  {
    busca_data($db);
  }
  else
  {
  ?>
  <script>
    document.getElementById("data_in").value = "";
    document.getElementById("data_in01").value = "";
    document.getElementById("nr_livro").value = "0";
  </script>
  <?
  }
 
          ////////////////////////////////////////////////////////////////
          //INICIO DE DEFINIÇÃO DE VARIÁVEIS PARA PAGINAÇÃO DE REGISTROS//
          ////////////////////////////////////////////////////////////////
          $max_links = 5; // máximo de links à serem exibidos
          $total_registros = mysqli_num_rows($resultado);
          $paginacao       = 8; //quantidade de registros por página
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
          $string_query_limite    = $sql."$string_query_registros LIMIT $inicio,$paginacao";
          $resultado_query_limite = mysqli_query($db, $string_query_limite);
          erro_sql("Select Inicial Limitado", $db, "");


          // definicoes de variaveis
          $max_res = $paginacao; // máximo de resultados à serem exibidos por tela ou pagina
          //$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
		  $mult_pag = new Mult_Pag($pagina_exibicao-1); // cria um novo objeto navbar
          $mult_pag->num_pesq_pag = $max_res; // define o número de pesquisas (detalhada ou não) por página
?>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <tr class="coluna_tabela" height="21">
                    <td width='20%' align='center'>Unidade</td>
                    <td width='30%' align='center'>Livro</td>
                    <td width='10%' align='center'>Nº Livro</td>
                    <td width='12%' align='center'>Data Início</td>
                    <td width='12%' align='center'>Data Fim</td>
                    <td width='10%' align='center'>Staus</td>
                    <td width='6%' align='center'>&nbsp;</td>
                  </tr>
<?php
  $cor_linha = "#CCCCCC";
  // cinza claro = #CCCCCC
  // cinza escuro = #EEEEEE
  $num_linha = 0;
  ///////////////////////////////////////
  //INICIO DAS DEFINIÇÕES DE CADA LINHA//
  ///////////////////////////////////////

  $sql_query = $mult_pag->Executar($sql, $db, "otimizada", "mysqli");

  while ($linha = mysqli_fetch_object($sql_query))
  {
    $num_linha = $num_linha + 1;
    $pos1=strpos($linha->data_inicio, "-");
    $pos2=strrpos($linha->data_inicio, "-");
    $data_inicio=substr($linha->data_inicio, $pos2+1, 2) . "/" . substr($linha->data_inicio, $pos1+1, 2) . "/" . substr($linha->data_inicio, 0, 4);
    $pos1=strpos($linha->data_final, "-");
    $pos2=strrpos($linha->data_final, "-");
    $data_final=substr($linha->data_final, $pos2+1, 2) . "/" . substr($linha->data_final, $pos1+1, 2) . "/" . substr($linha->data_final, 0, 4);
?>
                  <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?echo $cor_linha; ?>';">
                    <td align='left'>&nbsp;<?php echo $linha->nome;?></td>
                    <td align='left'>&nbsp;<?php echo $linha->descricao;?></td>
                    <td align='right'><?php echo $linha->num_livro;?>&nbsp;</td>
                    <td align='center'><?php echo $data_inicio;?></td>
                    <td align='center'><?php echo $data_final;?></td>
                    <td align='center'><?php echo $linha->status_2;?></td>
                    <td align='center'>
                    <?
                      $caminho = "&data_in=$data_inicio&data_fn=$data_final&unidade=$linha->nome&und_sup=$linha->id_unidade&livro=$linha->id_livro&nr_livro=$linha->num_livro";
                      $caminho1 = "&data_in=&data_in01=&data_fn=&unidade=$linha->nome&und_sup=$linha->id_unidade&livro=$linha->id_livro&nr_livro=";
                    ?>
                      <a href="<?php echo URL;?>/modulos/relatorios/gerar_livro_pdf.php?flag=1<?=$caminho?>"
                         target="_blank"><img src="<?php echo URL;?>/imagens/i.p.printv.gif" border="0" title="Imprimir Livro"></a>
                    </td>
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
  ////////////////////////////////////////////////
?>
                  <tr>
                    <td colspan="7" height="100%"></td>
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
                      $parte_url="/modulos/relatorios/gerar_livro.php";
                      $valor_pesquisa="f";
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
           </table>
<?
  }
?>
         </td>
       </tr>
     </table>
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
</body>
</html>
