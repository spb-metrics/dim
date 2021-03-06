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
  //  Arquivo..: remanejamento_inicial.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela inicial do m�dulo de remanejamento
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

    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR . "/Mult_Pag.php";

    require "../../verifica_acesso.php";

    if($_GET[aplicacao] <> ''){
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
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
<?

          ///////////////////////////////////////////////////////////////
          //INICIO DA SELE��O DO SELECT USADO PARA VISUALIZAR REGISTROS//
          //        AQUI COME�A A DEFINI��O DA TELA EM QUEST�O         //
          ///////////////////////////////////////////////////////////////
        
          if(isset($_GET[indice])){$_POST[indice] = $_GET[indice];}
          if(isset($_GET[pesquisa])){$_POST[pesquisa] = $_GET[pesquisa];}
          if(isset($_GET[buscar])) {$_POST[buscar]  = $_GET[buscar];}
        
          /////////////////////////////////////////
          //DE ACORDO COM OP��O, SELECIONAR QUERY//
          /////////////////////////////////////////
          if ($_POST['pesquisa']!='' and $_POST['indice']!='' and $_POST['buscar']!='')
          {
            if($_POST[indice]=="status_2"){
              $_POST[indice]="sol.status_2";
            }
            if($_POST[indice]=="data_incl"){
              $data_aux=$_POST[buscar];
              $pos1=strpos($_POST[buscar], "/");
              $pos2=strrpos($_POST[buscar], "/");
              if($pos1==2 && $pos2==5){
                $data_busca=substr($_POST[buscar], $pos2+1, strlen($_POST[buscar])) . "-" . substr($_POST[buscar], $pos1+1, 2) . "-" . substr($_POST[buscar], 0, 2);
                $_POST[buscar]=$data_busca;
              }
              $_POST[indice]="sol.data_incl";
            }
            if($_POST[indice]=="unidade_solicitante_nome"){
              $_POST[indice]="u.nome";
            }
            if($_POST[indice]=="unidade_solicitada_nome"){
              $_POST[indice]="uni.nome";
            }
            
            if($_POST[pesquisa]=="status_2"){
              $_POST[pesquisa]="sol.status_2";
            }
            if($_POST[pesquisa]=="data_incl"){
              $data_aux=$_POST[buscar];
              $pos1=strpos($_POST[buscar], "/");
              $pos2=strrpos($_POST[buscar], "/");
              if($pos1==2 && $pos2==5){
                $data_busca=substr($_POST[buscar], $pos2+1, strlen($_POST[buscar])) . "-" . substr($_POST[buscar], $pos1+1, 2) . "-" . substr($_POST[buscar], 0, 2);
                $_POST[buscar]=$data_busca;
              }
              $_POST[pesquisa]="sol.data_incl";
            }
            if($_POST[pesquisa]=="unidade_solicitante_nome"){
              $_POST[pesquisa]="u.nome";
            }
            if($_POST[pesquisa]=="unidade_solicitada_nome"){
              $_POST[pesquisa]="uni.nome";
            }
            $string_query_registros = "select sol.data_incl, u.nome, uni.nome as unidnome,
                                       sol.status_2, sol.id_solicita_remanej,u.id_unidade as idsolicitante,
                                       uni.id_unidade as idsolicitada
                                       from solicita_remanej as sol, unidade as u, unidade as uni
                                       where $_POST[pesquisa] like '%" . trim($_POST[buscar]) . "%'
                                       and sol.id_unid_solicitante=u.id_unidade and
                                       sol.id_unid_solicitada=uni.id_unidade and
                                       sol.id_unid_solicitante='$_SESSION[id_unidade_sistema]'
                                       order by $_POST[indice] desc";
            if($_POST[indice]=="u.nome"){
              $_POST[indice]="unidade_solicitante_nome";
            }
            if($_POST[indice]=="uni.nome"){
              $_POST[indice]="unidade_solicitada_nome";
            }
            if($_POST[indice]=="sol.status_2"){
              $_POST[indice]="status_2";
            }
            if($_POST[indice]=="sol.data_incl"){
              $_POST[buscar]=$data_aux;
              $_POST[indice]="data_incl";
            }
            
            if($_POST[pesquisa]=="u.nome"){
              $_POST[pesquisa]="unidade_solicitante_nome";
            }
            if($_POST[pesquisa]=="uni.nome"){
              $_POST[pesquisa]="unidade_solicitada_nome";
            }
            if($_POST[pesquisa]=="sol.status_2"){
              $_POST[pesquisa]="status_2";
            }
            if($_POST[pesquisa]=="sol.data_incl"){
              $_POST[buscar]=$data_aux;
              $_POST[pesquisa]="data_incl";
            }
          }
          ///////////////////////////////////////////////////
          //SE $BUSCA ESTIVER VAZIA, SIGNIFICA BUSCA PADR�O//
          ///////////////////////////////////////////////////
          else
          {
            if(!$_POST['indice']){$_POST['indice']="data_incl";}
            if($_POST[indice]=="status_2"){
              $_POST[indice]="sol.status_2";
            }
            if($_POST[indice]=="data_incl"){
              $_POST[indice]="sol.data_incl";
            }
            if($_POST[indice]=="unidade_solicitante_nome"){
              $_POST[indice]="u.nome";
            }
            if($_POST[indice]=="unidade_solicitada_nome"){
              $_POST[indice]="uni.nome";
            }
            $string_query_registros = "select sol.data_incl, u.nome, uni.nome as unidnome,
                                       sol.status_2, sol.id_solicita_remanej, u.id_unidade as idsolicitante,
                                       uni.id_unidade as idsolicitada
                                       from solicita_remanej as sol, unidade as u, unidade as uni
                                       where sol.id_unid_solicitante=u.id_unidade and
                                       sol.id_unid_solicitada=uni.id_unidade and
                                       sol.id_unid_solicitante='$_SESSION[id_unidade_sistema]'
                                       order by $_POST[indice] desc";
            if($_POST[indice]=="u.nome"){
              $_POST[indice]="unidade_solicitante_nome";
            }
            if($_POST[indice]=="uni.nome"){
              $_POST[indice]="unidade_solicitada_nome";
            }
            if($_POST[indice]=="sol.status_2"){
              $_POST[indice]="status_2";
            }
            if($_POST[indice]=="sol.data_incl"){
              $_POST[indice]="data_incl";
            }
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
          //INICIO DE DEFINI��O DE VARI�VEIS PARA PAGINA��O DE REGISTROS//
          ////////////////////////////////////////////////////////////////
          $max_links = 10; // m�ximo de links � serem exibidos
          $total_registros = mysqli_num_rows($resultado_query_registros);
          $paginacao       = 15; //quantidade de registros por p�gina
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
          $max_res = $paginacao; // m�ximo de resultados � serem exibidos por tela ou pagina
          //$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
		  $mult_pag = new Mult_Pag($pagina_exibicao-1); // cria um novo objeto navbar
          $mult_pag->num_pesq_pag = $max_res; // define o n�mero de pesquisas (detalhada ou n�o) por p�gina

?>

          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                  <form name="form_inicial" action="./remanejamento_inicial.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="3" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr class="opcao_tabela">
                      <td valign="middle" width="60%">
                        Pesquisar por:
                        <select name="pesquisa" style="width: 150px" onchange="if(this.selectedIndex==0){document.form_inicial.buscar.value='';}">
                            <option value="data_incl" <?php if($_POST[pesquisa]=="data_incl"){echo "selected";}?>> Data Solicita��o </option>
                            <option value="unidade_solicitante_nome" <?php if($_POST[pesquisa]=="unidade_solicitante_nome"){echo "selected";}?>> Unidade Solicitante </option>
                            <option value="unidade_solicitada_nome" <?php if($_POST[pesquisa]=="unidade_solicitada_nome"){echo "selected";}?>> Unidade Solicitada </option>
                            <option value="status_2" <?php if($_POST[pesquisa]=="status_2"){echo "selected";}?>> Status </option>
                            <option value="id_solicita_remanej" <?php if($_POST[pesquisa]=="id_solicita_remanej"){echo "selected";}?>> N� da Solicita��o </option>
                        </select>
                        <input type="text" name="buscar" size="30" style="width: 200px" <?php if (isset($_POST[buscar])){echo "value='".$_POST[buscar]."'";}?> onblur="if(document.form_inicial.pesquisa.selectedIndex==0){verificaData(this,this.value);}" onKeyPress="if(document.form_inicial.pesquisa.selectedIndex==0){return mascara_data_dispensacao(event,this);}" onkeydown=" return VerificarEnter(event);">
                      </td>
                      <td valign="middle" width="40%">
                        Ordenar Lista:
                        <select name="indice" style="width: 150px">
                            <option value="data_incl" <?php if($_POST[indice]=="data_incl"){echo "selected";}?>> Data Solicita��o </option>
                            <option value="unidade_solicitante_nome" <?php if($_POST[indice]=="unidade_solicitante_nome"){echo "selected";}?>> Unidade Solicitante </option>
                            <option value="unidade_solicitada_nome" <?php if($_POST[indice]=="unidade_solicitada_nome"){echo "selected";}?>> Unidade Solicitada </option>
                            <option value="status_2" <?php if($_POST[indice]=="status_2"){echo "selected";}?>> Status </option>
                            <option value="id_solicita_remanej" <?php if($_POST[indice]=="id_solicita_remanej"){echo "selected";}?>> N� da Solicita��o </option>
                        </select>
                        <input type="submit" name="ok" style="font-size: 12px;" value="OK">
                      </td>
                      <td valign="middle" align="right" width="20%">
                        <?php
                          if($inclusao_perfil!=""){
                        ?>
                            <input type="button" style="font-size: 12px;" name="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inclusao.php?responsavel=<?php echo $mostrar_responsavel_dispensacao;?>'">
                        <?php
                          }
                          else{
                        ?>
                            <input type="button" style="font-size: 12px;" name="cadastrar" value="Novo >>" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inclusao.php'" disabled>
                        <?php
                          }
                        ?>
                      </td>
                    </tr>
                    <input type="hidden" name="aplicacao" value="<?php echo $_SESSION[APLICACAO];?>">
                  </form>
                </table>
              </td>
            </tr>
            <tr class="coluna_tabela">
              <td width='18%'align='center'>
                Data da Solicit��o
              </td>
              <td width='18%'align='center'>
                Unidade Solicitante
              </td>
              <td width='18%'align='center'>
                Unidade Solicitada
              </td>
              <td width='15%'align='center'>
                Status
              </td>
              <td width='5%'align='center'>
                Solic
              </td>
              <td width='10%'align='center'>
                N� Solic
              </td>
              <td width='10%' align='center'></td>
            </tr>
     
<?php
            $cor_linha = "#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINI��ES DE CADA LINHA//
            ///////////////////////////////////////

            // metodo que realiza a pesquisa
            $resultado = $mult_pag->Executar($string_query_registros, $db, "otimizada", "mysqli");


            while ($solicitacao_info = mysqli_fetch_object($resultado_query_limite))
            {
?>
         
               <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                 <td align='center'>
                   <?php
                     $pos1=strpos($solicitacao_info->data_incl, "-");
                     $pos2=strrpos($solicitacao_info->data_incl, "-");
                     $data_solic=substr($solicitacao_info->data_incl, $pos2+1, 2) . "/" . substr($solicitacao_info->data_incl, $pos1+1, 2) . "/" . substr($solicitacao_info->data_incl, 0, 4);
                   ?>
                   <?php echo $data_solic;?>
                 </td>

                 <td align='left'>
                    <?php echo $solicitacao_info->nome;?>
                 </td>
                 <td align='left'>
                    <?php echo $solicitacao_info->unidnome;?>
                 </td>
                 <td align='left'>
                    <?php echo $solicitacao_info->status_2;?>
                 </td>
                 <?php
                   if($solicitacao_info->status_2!="SOLICITADA"){
                     $sql="select sum(qtde_solicita) as qtde_solicita, sum(qtde_atendida) as qtde_atendida ";
                     $sql.="from item_solicita_remanej ";
                     $sql.="where id_solicita_remanej='$solicitacao_info->id_solicita_remanej'";
                     $res=mysqli_query($db, $sql);
                     erro_sql("Select Status", $db, "");
                     $status_info=mysqli_fetch_object($res);
                     if((int)$status_info->qtde_atendida==0){
                       $img=URL . "/imagens/bolinhas/ball_vermelha.gif";
                       $txt="Solicita��o N�o Atendida";
                     }
                     else{
                       if($status_info->qtde_solicita==$status_info->qtde_atendida){
                         $img=URL . "/imagens/bolinhas/ball_verde.gif";
                         $txt="Solicita��o Totalmente Atendida";
                       }
                       else{
                         $img=URL . "/imagens/bolinhas/ball_amarela.gif";
                         $txt="Solicita��o Parcialmente Atendida";
                       }
                     }
                   }
                 ?>
                 <td align='center'>
                   <?php
                     if($solicitacao_info->status_2!="SOLICITADA"){
                   ?>
                       <img src="<?php echo $img;?>" border="0"title="<?php echo $txt;?>">
                   <?php
                     }
                   ?>
                 </td>
                 <td align='left'>
                    <?php echo $solicitacao_info->id_solicita_remanej;?>
                 </td>
                 <td align='center'>
                   <?php
                     $status_solicitante="";
                     $status_solicitada="";
                     if($_SESSION[id_unidade_sistema]==$solicitacao_info->idsolicitante && $solicitacao_info->status_2=="RESERVADA"){
                       $status_solicitante="habilitada";
                     }
                     if($_SESSION[id_unidade_sistema]==$solicitacao_info->idsolicitada && $solicitacao_info->status_2=="SOLICITADA"){
                       $status_solicitada="habilitada";
                     }
                   ?>
                   <?php
                     if ($pagina_a_exibir=='')
                         $pagina_a_exibir=1;
                         
                     if($consulta_perfil!=""){
                   ?>
                     <a href='<?php echo URL;?>/modulos/remanejamento/remanejamento_detalhado.php?pagina=<?=$pagina_a_exibir-1?>&pagina_a_exibir=<?=$pagina_a_exibir?>&indice=<?=$_POST['indice']?>&buscar=<?=$_POST['buscar']?>&pesquisa=<?=$_POST['pesquisa']?>&codigo=<?php echo $solicitacao_info->id_solicita_remanej;?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Registro"></a>&nbsp&nbsp&nbsp
                   <?php
                     }
                   ?>
                   <?php
                     if($inclusao_perfil!=""){
                   ?>
                     <a <?php if($status_solicitante!=""){echo "href='" . URL . "/modulos/remanejamento/remanejamento_registrado.php?codigo=" . $solicitacao_info->id_solicita_remanej. "&responsavel=$mostrar_responsavel_dispensacao'";}?>><?php if($status_solicitante!=""){echo "<img src='" . URL . "/imagens/quote.gif' border='0' title='Efetivar Remanejamento'>";}else{echo "<img src='" . URL . "/imagens/gray/quote.gif' border='0' title='Efetivar Remanejamento'>";}?></a>
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
            //RODAP� DE NAVEGA��O DE REGISTROS ENCONTRADOS//
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
                      //DEFININDO BOT�O PARA PRIMEIRA P�GINA//
                      ////////////////////////////////////////
                      $parte_url="/modulos/remanejamento/remanejamento_inicial.php";
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

<?php
                     //////////////////////////////////////
                     //DEFININDO BOT�O PARA ULTIMA P�GINA//
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
    //RODAP� DA P�GINA//
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
    //MENSAGENS DE EXCLUSAO, INCLUS�O E ALTERA��O//
    ///////////////////////////////////////////////

    if(isset($pesq)=='f'){echo "<script>window.alert('N�o foi encontrado dados para a pesquisa!');document.form_inicial.buscar.focus();</script>";}

    if($_GET[i]=='t'){echo "<script>window.alert('Opera��o efetuada com sucesso!')</script>";}
    if($_GET[i]=='f'){echo "<script>window.alert('N�o foi poss�vel realizar a solicita��o de remanejamento!')</script>";}

    if($_GET[r]=='t'){echo "<script>window.alert('Opera��o efetuada com sucesso!')</script>";}
    if($_GET[r]=='f'){echo "<script>window.alert('N�o foi poss�vel realizar a efetiva��o do remanejamento!')</script>";}
  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else{
    include_once "../../config/erro_config.php";
  }
?>
