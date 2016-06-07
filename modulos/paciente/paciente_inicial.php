<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }
    $id_unidade = $_SESSION[id_unidade_sistema];

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/Mult_Pag_Pac.php";
    require "../../verifica_acesso.php";
    if($_GET[aplicacao]<>''){
      //a linha abaixo eh usado em algum lugar que eu nao sei
      $_SESSION[cod_aplicacao]=$_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
?>

    <script language="javascript">
    <!--
      function validarCampo(){
        var x=document.form_inicial;
        if(x.buscar.value==""){
          window.alert("Favor Preencher Campo Pesquisar!");
          x.buscar.focus();
          return false;
        }
        return true;
      }
    //-->
    </script>

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
          if(isset($_GET[indice])){$_POST[indice]=$_GET[indice];}
          if(isset($_GET[buscar])){$_POST[buscar]=$_GET[buscar];}
          if(isset($_GET[busca])){$_POST[busca]=$_GET[busca];}
          if(isset($_GET[pesquisa])){$_POST[pesquisa]=$_GET[pesquisa];}

          $buscar = ereg_replace(' ', '', $_POST['buscar']);
          
          /////////////////////////////////////////
          //DE ACORDO COM OPÇÃO, SELECIONAR QUERY//
          /////////////////////////////////////////
		  
          if($_POST['busca']==''){
            if($_POST['indice']!='' and $_POST['buscar']!='' && $_POST[pesquisa]!=""){
              $string_query_registros="select p.id_paciente, p.nome, p.nome_mae, p.data_nasc, s.descricao
                                       from paciente p, status_paciente s
                                       where ".$_POST['pesquisa']." like '".trim($_POST['buscar'])."%'
                                             and p.status_2='A'
                                             and p.id_status_paciente = s.id_status_paciente
                                             and p.unidade_referida = $id_unidade
                                       order by ".$_POST['indice'];
            }
            ///////////////////////////////////////////////////
            //SE $BUSCA ESTIVER VAZIA, SIGNIFICA BUSCA PADRÃO//
            ///////////////////////////////////////////////////
            else{
              if(!$_POST['indice']){$_POST['indice']="nome_mae_nasc";}

              $string_query_registros="select id_paciente, nome, nome_mae, data_nasc from paciente where id_paciente=0";
			}
          }
          else{
            if($_POST['indice']!='' and $_POST['buscar']!='' && $_POST[pesquisa]!=""){
              $valor_aux=$_POST[buscar];
              $_POST[buscar]=str_replace(" ", "", $_POST[buscar]);
              if($_POST[pesquisa]=="data_nasc"){
                $valores=split("[/]", $_POST[buscar]);
                $_POST[buscar]=$valores[2] . "-" . $valores[1] . "-" . $valores[0];
              }
              $string_query_registros="select p.id_paciente, p.nome, p.nome_mae, p.data_nasc, s.descricao
                                       from paciente p, status_paciente s
                                       where ".$_POST['pesquisa']." like '".trim($_POST[buscar])."%'
                                             and p.status_2='A'
                                             and p.id_status_paciente = s.id_status_paciente
                                       order by ".$_POST['indice'];
//esse está ok!
              $_POST[buscar]=$valor_aux;
              $_POST[buscar]=str_replace("\'", "'", $_POST[buscar]);
            }
            ///////////////////////////////////////////////////
            //SE $BUSCA ESTIVER VAZIA, SIGNIFICA BUSCA PADRÃO//
            ///////////////////////////////////////////////////
            else{
              if(!$_POST['indice']){$_POST['indice']="nome_mae_nasc";}
              $string_query_registros="select p.id_paciente, p.nome, p.nome_mae, p.data_nasc, s.descricao
                                       from paciente p, status_paciente s
                                       where p.status_2='A'
                                             and p.id_status_paciente = s.id_status_paciente
                                       order by ".$_POST['indice'];
			}
          }
          
          if(isset($_GET[id_paciente])){
            $string_query_registros="select p.id_paciente, p.nome, p.nome_mae, p.data_nasc, s.descricao
                                     from paciente p, status_paciente s
                                     where p.id_paciente='$_GET[id_paciente]' and p.status_2='A'
                                     and p.id_status_paciente=s.id_status_paciente";
		  }

          //////////////////////////////
          //EXECUTAR QUERY SELECIONADA//
          //////////////////////////////

          $resultado_query_registros=mysqli_query($db, $string_query_registros);
          erro_sql("Select Inicial", $db, "");

          if ($_POST['indice']!='' and $_POST['buscar']!=''){
            if(mysqli_num_rows($resultado_query_registros)==0){
              $pesq="f";
            }
          }
          ////////////////////////////////////////////////////////////////
          //INICIO DE DEFINIÇÃO DE VARIÁVEIS PARA PAGINAÇÃO DE REGISTROS//
          ////////////////////////////////////////////////////////////////
          $max_links=5; // máximo de links à serem exibidos
          $total_registros=mysqli_num_rows($resultado_query_registros);
          $paginacao=16; //quantidade de registros por página
          $total_paginas=ceil($total_registros/$paginacao);
          //total de páginas necessárias para exibir estes registros,
          //ceil() arredonda 'para cima'

          /////////////////////////////////////////
          //SE PÁGINA A EXIBIR NÃO ESTIVER SETADA//
          /////////////////////////////////////////
          if(!$pagina_exibicao){
             $pagina_exibicao="1";  //defina como 1, pois é a primeira página
          }

		  $pagina_a_exibir = $_GET['pagina_a_exibir'];
          //se recebeu (via URL) uma página a exibir
          if($pagina_a_exibir){
             $pagina_exibicao=$pagina_a_exibir; //pagina de exibição recebe a página a ser exibida
          }

          //////////////////////////////////////////////////////////
          //DEFINE O INDICE DE INÍCIO DO SELECT CORRENTE, LIMITADO//
          //     PELO VALOR ATRIBUÍDO À VARIÁVEL "$PAGINACAO"     //
          //////////////////////////////////////////////////////////
          $inicio=$pagina_exibicao-1;
          $inicio=$inicio*$paginacao;
          $string_query_limite="$string_query_registros LIMIT $inicio,$paginacao";
          $resultado_query_limite=mysqli_query($db, $string_query_limite);
          erro_sql("Select Inicial Limitado", $db, "");

          // definicoes de variaveis
          $max_res=$paginacao; // máximo de resultados à serem exibidos por tela ou pagina
          //$mult_pag=new Mult_Pag_Pac(); // cria um novo objeto navbar
          $mult_pag=new Mult_Pag_Pac($pagina_exibicao-1); // cria um novo objeto navbar
          $mult_pag->num_pesq_pag=$max_res; // define o número de pesquisas (detalhada ou não) por página
?>
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_inicial" action="./paciente_inicial.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <?echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr class="opcao_tabela">
                      <td valign="middle" width="60%">
                        Pesquisar por:
                        <select name="pesquisa" style="width:150px;" onchange="if(document.form_inicial.pesquisa.selectedIndex==2){document.form_inicial.buscar.value='';}">
                            <option value="nome_mae_nasc" <?php if($_POST[pesquisa]=="nome_mae_nasc"){echo "selected";}?>> Nome </option>
                            <option value="nome_mae_sem_espaco" <?php if($_POST[pesquisa]=="nome_mae_sem_espaco"){echo "selected";}?>> Nome Mãe </option>
                            <option value="data_nasc" <?php if($_POST[pesquisa]=="data_nasc"){echo "selected";}?>> Dt. Nascimento </option>
                            <option value="descricao" <?php if($_POST[pesquisa]=="descricao"){echo "selected";}?>> Situação Paciente </option>
                        </select>
                        <input type="text" name="buscar" id="buscar" size="30" value="<?php if(isset($_POST[buscar])){echo "$_POST[buscar]";}?>" onblur="if(document.form_inicial.pesquisa.selectedIndex==2){verificaData(this,this.value);}" onKeyPress="if(document.form_inicial.pesquisa.selectedIndex==2){return mascara_data(event,this);}" onkeydown="VerificarEnter(event);">
                      </td>
                      <td valign="middle" width="40%">
                        Ordernar Lista:
                        <select name="indice" style="width:150px;">
                            <option value="nome_mae_nasc" <?php if($_POST[indice]=="nome_mae_nasc"){echo "selected";}?>> Nome </option>
                            <option value="nome_mae_sem_espaco" <?php if($_POST[indice]=="nome_mae_sem_espaco"){echo "selected";}?>> Nome Mãe </option>
                            <option value="data_nasc" <?php if($_POST[indice]=="data_nasc"){echo "selected";}?>> Dt. Nascimento </option>
                            <option value="descricao" <?php if($_POST[indice]=="descricao"){echo "selected";}?>> Situação Paciente </option>
                        </select>
                        <input type="submit" name="ok" id="ok" style="font-size: 10px;" value=" OK " onclick="return validarCampo();">
                        <input type="hidden" name="busca" id="busca" value="busca">
                      </td>
                      <td valign="middle" align="right" width="20%">
                        <?php
                          if($inclusao_perfil!=""){
                        ?>
                            <input type="button" style="font-size: 10px;" name="cadastrar" id="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/paciente/paciente_inclusao.php'">
                        <?php
                          }
                          else{
                        ?>
                            <input type="button" style="font-size: 10px;" name="cadastrar" id="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/paciente/paciente_inclusao.php'" disabled>
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
              <td width='36%'align='center'>
                Nome
              </td>
              <td width='35%'align='center'>
                Nome Mãe
              </td>
              <td width='11%'align='center'>
                Data Nasc.
              </td>
              <td width='9%'align='center'>
                Situação
              </td>
              <td width='9%' align='center'></td>
            </tr>
<?php
            $cor_linha="#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
            ///////////////////////////////////////

            // metodo que realiza a pesquisa
            $resultado=$mult_pag->Executar($string_query_registros, $db, "otimizada", "mysqli");

            while($lista_info=mysqli_fetch_object($resultado_query_limite)){
?>
               <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                 <td align='left'>
                     <?php echo $lista_info->nome;?>
                 </td>
                 <td align='left'>
                      <?php echo $lista_info->nome_mae;?>
                 </td>
                 <td align='center'>
                      <?php echo substr($lista_info->data_nasc,-2)."/".substr($lista_info->data_nasc,5,2)."/".substr($lista_info->data_nasc,0,4);?>
                 </td>
                 <td align='left'>
                      <?php echo $lista_info->descricao;?>
                 </td>

                 <td align='center'>
                   <?php
                     if($consulta_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/paciente/paciente_detalhado.php?id_paciente=<?php echo $lista_info->id_paciente;?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Registro" alt="Detalhar Registro"></a>&nbsp&nbsp
                   <?php
                     }
                   ?>
                   <?php
                     if($alteracao_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/paciente/paciente_alteracao.php?id_paciente=<?php echo $lista_info->id_paciente;?>'><img src="<?php echo URL;?>/imagens/b_edit.png" border="0" title="Editar Registro" alt="Editar Registro"></a>&nbsp&nbsp
                   <?php
                     }
                   ?>
                   <?php
                     if($exclusao_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/paciente/paciente_exclusao.php?id_paciente=<?php echo $lista_info->id_paciente;?>'><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Excluir Registro" alt="Excluir Registro"></a>
                   <?php
                     }
                   ?>
                 </td>
               </tr>
<?php
               ////////////////////////
               //MUDANDO COR DA LINHA//
               ////////////////////////
               if($cor_linha=="#EEEEEE"){
                 $cor_linha="#CCCCCC";
               }
               else{
                 $cor_linha="#EEEEEE";
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
                      $parte_url="/modulos/paciente/paciente_inicial.php";
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
    if(isset($pesq)=='f'){
      echo "<script>
              window.alert('Não foi encontrado dados para a pesquisa!');
              document.form_inicial.buscar.focus();
            </script>";
    }

    if($_GET[i]=='t' || $_GET[e]=='t' || $_GET[a]=='t'){
      echo "<script>
              window.alert('Operação efetuada com sucesso!')
            </script>";
    }
    if($_GET[i]=='f' || $_GET[e]=='f' || $_GET[a]=='f'){
      echo "<script>
              window.alert('Não foi possível excluir o paciente!')
            </script>";
    }
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else{
    include_once "../../config/erro_config.php";
  }
?>
