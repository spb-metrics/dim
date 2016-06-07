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
  //  Arquivo..: usuario_cadastro.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de cadastro de usuario
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php")){

    session_unregister("LISTA");

    require "../../config/config.inc.php";

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
    }
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

    //verificando se unidade é distrito
    $sql="select flg_nivel_superior from unidade where id_unidade = '$_SESSION[id_unidade_sistema]'";

    $res=mysqli_query($db, $sql);
    erro_sql("Select Distrito", $db, "");
    $nivelsuperior    = mysqli_fetch_object($res);

    if ($nivelsuperior->flg_nivel_superior=='1'){
      $sql="select id_unidade from unidade where unidade_id_unidade = '$_SESSION[id_unidade_sistema]'";
      $result = mysqli_query($db, $sql);
      erro_sql("Select Nível Superior", $db, "");
      while ($unidades  = mysqli_fetch_object($result)){
        $lista_unidades = $lista_unidades.$unidades->id_unidade.",";
     
        $sql2="select id_unidade from unidade where unidade_id_unidade = '$unidades->id_unidade'";
        $result2 = mysqli_query($db, $sql2);
        erro_sql("Select Unidade/SubUnidade", $db, "");
        while ($unidades2  = mysqli_fetch_object($result2)){
          $lista_unidades = $lista_unidades.$unidades2->id_unidade.",";
        }
      }
      $lista_unidades = $lista_unidades.$_SESSION[id_unidade_sistema].",";
      $lista_unidades="(".substr($lista_unidades,0, strlen($lista_unidades)-1).")";
    }
    else
    {
      $lista_unidades="(".$_SESSION[id_unidade_sistema].")";
    }

    ///////////////////////////////////////////////////////////////
    //INICIO DA SELEÇÃO DO SELECT USADO PARA VISUALIZAR REGISTROS//
    //        AQUI COMEÇA A DEFINIÇÃO DA TELA EM QUESTÃO         //
    ///////////////////////////////////////////////////////////////
    if(isset($_GET[indice])){$_POST[indice] = $_GET[indice];}
    if(isset($_GET[pesquisa])){$_POST[pesquisa] = $_GET[pesquisa];}
    if(isset($_GET[buscar])) {$_POST[buscar] = $_GET[buscar];}

    /////////////////////////////////////////
    //DE ACORDO COM OPÇÃO, SELECIONAR QUERY//
    /////////////////////////////////////////
        
    if ($_POST['pesquisa']!='' and $_POST['indice']!='' and $_POST['buscar']!=''){
      if($_POST[pesquisa]=="situacao"){
        $_POST[buscar]=substr($_POST[buscar], 0, 1);
      }

      $string_query_registros = "select id_usuario, nome, login, if(situacao='A', 'ATIVO', 'INATIVO') as situacao
                                 from usuario
                                 where ".$_POST['pesquisa']." like '".trim($_POST['buscar'])."%'
                                 order by ".$_POST['indice'];

    }
    ///////////////////////////////////////////////////
    //SE $BUSCA ESTIVER VAZIA, SIGNIFICA BUSCA PADRÃO//
    ///////////////////////////////////////////////////
    else{
      if(!$_POST['indice']){$_POST['indice']="nome";}
        $string_query_registros = "select id_usuario, nome, login, if(situacao='A', 'ATIVO', 'INATIVO') as situacao
                                   from usuario order by ".$_POST['indice'];

      }
      //////////////////////////////
      //EXECUTAR QUERY SELECIONADA//
      //////////////////////////////

      $resultado_query_registros = mysqli_query($db, $string_query_registros);
      erro_sql("Select Inicial", $db, "");
      if($_POST['indice']!='' and $_POST['buscar']!=''){
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
      if(!$pagina_exibicao)
      {
        $pagina_exibicao = "1";  //defina como 1, pois é a primeira página
      }

	  $pagina_a_exibir = $_GET['pagina_a_exibir'];
      if($pagina_a_exibir) //se recebeu (via URL) uma página a exibir
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
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_inicial" action="./usuario_inicial.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr class="opcao_tabela">
                  <td valign="middle" width="60%">
                        Pesquisar por:
                        <select name="pesquisa" style="width:150px;">
                          <option <?php if ($_POST[pesquisa]=='login'){echo "selected";}?> value="login">Login</option>
                          <option <?php if ($_POST[pesquisa]=='nome'){echo "selected";}?> value="nome">Nome</option>
                          <option <?php if ($_POST[pesquisa]=='situacao'){echo "selected";}?> value="situacao">Situação</option>
                        </select>
                        <input type="text" name="buscar" size="30" <?php if (isset($_POST[buscar])){echo "value='".$_POST[buscar]."'";}?>>
                </td>
                <td valign="middle" width="40%">
                  Ordenar Lista:
                  <select name="indice" style="width:150px;">
                    <option <?php if ($_POST[indice]=='login'){echo "selected";}?> value="login">Login</option>
                    <option <?php if ($_POST[indice]=='nome'){echo "selected";}?> value="nome">Nome</option>
                    <option <?php if ($_POST[indice]=='situacao'){echo "selected";}?> value="situacao">Situação</option>
                  </select>
                  <input type="submit" name="submit" style="font-size: 12px;" value=" OK ">
                </td>
                <td valign="middle" align="right" width="20%">
                  <?php
                    if($inclusao_perfil!=""){
                  ?>
                      <input type="button" style="font-size: 12px;" name="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/usuario/usuario_cadastro.php'">
                  <?php
                    }
                    else{
                  ?>
                      <input type="button" style="font-size: 12px;" name="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/usuario/usuario_cadastro.php'" disabled>
                  <?php
                    }
                  ?>
                </td>

              </tr>
            </table>
          </td>
        </tr>
        </form>

         <tr bgcolor='#6B6C8F' class="coluna_tabela" height="16">
        
          <td width='20%' align='center'>
            Login
          </td>

          <td width='55%' align='center'>
            Nome
          </td>

          <td width='15%' align='center'>
            Situação
          </td>

          <td width='10%' align='center'>
          </td>
        </tr>

    <?php
      $cor_linha = "#CCCCCC";
      ///////////////////////////////////////
      //INICIO DAS DEFINIÇÕES DE CADA LINHA//
      ///////////////////////////////////////
      
      $resultado = $mult_pag->Executar($string_query_registros, $db, "otimizada", "mysqli");
      
      while ($usuario = mysqli_fetch_object($resultado_query_limite)){
    ?>
        <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?echo $cor_linha; ?>';">
          <td align='left'>
            <?php echo $usuario->login;?>
          </td>

          <td align='left'>
             <?php echo $usuario->nome;?>
          </td>

          <td align='left'>
             <?php echo $usuario->situacao;?>
          </td>

          <td align='center'>
            <?php
              if ($pagina_a_exibir=="")
                         $pagina_a_exibir=1;
                         
              if($consulta_perfil!=""){
            ?>
              <a href='<?php echo URL;?>/modulos/usuario/usuario_detalhe.php?pagina=<?=$pagina_a_exibir-1?>&pagina_a_exibir=<?=$pagina_a_exibir?>&indice=<?=$_POST['indice']?>&buscar=<?=$_POST['buscar']?>&pesquisa=<?=$_POST['pesquisa']?>&id_usuario=<?php echo $usuario->id_usuario;?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Registro"></a>&nbsp;&nbsp;&nbsp;
            <?php
              }
            ?>
            <?php
              if($alteracao_perfil!=""){
            ?>
              <a href='<?php echo URL;?>/modulos/usuario/usuario_edicao.php?pagina=<?=$pagina_a_exibir-1?>&pagina_a_exibir=<?=$pagina_a_exibir?>&indice=<?=$_POST['indice']?>&buscar=<?=$_POST['buscar']?>&pesquisa=<?=$_POST['pesquisa']?>&id_usuario=<?php echo $usuario->id_usuario;?>'><img src="<?php echo URL;?>/imagens/b_edit.gif" border="0" title="Editar Registro"></a>
            <?php
              }
            ?>
          </td>
        </tr>

    <?php
        ////////////////////////
        //MUDANDO COR DA LINHA//
        ////////////////////////
        if($cor_linha == "#CCCCCC"){
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

                <TABLE name='4' width='100% 'border='0' align='center' valign=bottom cellspacing='0' cellspacing='0'>

                  <TR align='center' valign='top' class="navegacao_tabela">

                    <TD align='right'>
<?
                      ////////////////////////////////////////
                      //DEFININDO BOTÃO PARA PRIMEIRA PÁGINA//
                      ////////////////////////////////////////
                      $parte_url="/modulos/usuario/usuario_inicial.php";
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
    if(isset($pesq)=='f')
    {
      echo "<script>";
      echo "window.alert('Não foi encontrado dados para a pesquisa!');document.form_inicial.buscar.focus();";
      echo "</script>";
    }

    if($_GET[e]=='t'){echo "<script>alert('Operação efetuada com sucesso!')</script>";}
    if($_GET[e]=='f'){echo "<script>alert('Não foi possível excluir o usuário')</script>";}

    if($_GET[i]=='t'){echo "<script>alert('Operação efetuada com sucesso!')</script>";}
    if($_GET[i]=='f'){echo "<script>alert('Não foi possível cadastrar o usuário')</script>";}

    if($_GET[a]=='t'){echo "<script>alert('Operação efetuada com sucesso!')</script>";}
    if($_GET[a]=='f'){echo "<script>alert('Não foi possível alterar o usuário')</script>";}
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
