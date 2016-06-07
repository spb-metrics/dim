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
  //  Arquivo..: remanejamento_registrado.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de registração de remanejamento
  //////////////////////////////////////////////////////////////////

  //CRIANDO NUMERO DE CONTROLE PARA EVITAR DUPLICIDADE NA GRAVAÇÃO
  session_regenerate_id();
  $idSessao = session_id();
  $numControle = date("Y-m-d H:i:s").$id_unidade_sistema.$idSessao;
  
  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    $mostrar_responsavel_dispensacao=$_GET[responsavel];

    if($_POST[flag]=="t"){
      if($_POST[id_login]==""){
        $_POST[id_login]=$_SESSION[id_usuario_sistema];
      }
      //obtem nome da unidade solicitada
      $sql="select u.nome from solicita_remanej as sol, unidade as u ";
      $sql.="where sol.id_unid_solicitada=u.id_unidade and ";
      $sql.="sol.id_solicita_remanej='$_POST[codigo_atual]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Unidade Solicitada", $db, "");
      if(mysqli_num_rows($res)>0){
        $unidade_solicitada=mysqli_fetch_object($res);
      }
      //obtem data do sistema
      $data=date("Y-m-d H:i:s");
      //insercao de um registro por remanejamento na tabela movto_geral
      $sql="insert into movto_geral ";
      $sql.="(tipo_movto_id_tipo_movto, usuario_id_usuario, unidade_id_unidade, data_movto, data_incl, num_controle) ";
      $sql.="values ('5', '$_POST[id_login]', '$_SESSION[id_unidade_sistema]', '$data', '$data', '$numControle')";
      mysqli_query($db, $sql);
      erro_sql("Insert Movto Geral", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      $sql="select id_movto_geral from movto_geral ";
      $sql.="where tipo_movto_id_tipo_movto='5' and usuario_id_usuario='$_POST[id_login]' and ";
      $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and data_movto='$data' and data_incl='$data'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Id Movto Geral", $db, "");
      if(mysqli_num_rows($res)>0){
        $chave=mysqli_fetch_object($res);
      }
      //obtem os materias atendidos
      $sql="select it_mov.item_solicita_remanej, it_mov.material_id_material, it_mov.lote, it_mov.fabricante_id_fabricante, ";
      $sql.="it_mov.qtde, it_mov.validade ";
      $sql.="from item_solicita_remanej as it, itens_movto_geral as it_mov ";
      $sql.="where it.id_item_solicita_remanej=it_mov.item_solicita_remanej and ";
      $sql.="id_solicita_remanej='$_POST[codigo_atual]'";
      $result=mysqli_query($db, $sql);
      erro_sql("Select Materiais Atendidos", $db, "");
      while($mat=mysqli_fetch_object($result)){
        //insercao na tabela itens_movto_geral
        $sql="insert into itens_movto_geral ";
        $sql.="(movto_geral_id_movto_geral, material_id_material, fabricante_id_fabricante, lote, validade, qtde, item_solicita_remanej) ";
        $sql.="values ('$chave->id_movto_geral', '$mat->material_id_material', '$mat->fabricante_id_fabricante', '" . strtoupper($mat->lote) . "', '$mat->validade', '$mat->qtde', '$mat->item_solicita_remanej')";
        mysqli_query($db, $sql);
        erro_sql("Insert Itens Movto Geral", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
        //obtem a quantidade de material de uma unidade no estoque
        $sql="select quantidade from estoque where unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
        $sql.="and material_id_material='$mat->material_id_material' and fabricante_id_fabricante='$mat->fabricante_id_fabricante' ";
        $sql.="and lote='$mat->lote' and flg_bloqueado=''";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Qtde Material Unidade", $db, "");
        if(mysqli_num_rows($res)>0){
          $estoque_info=mysqli_fetch_object($res);
          $qtde_estoque_unidade=$estoque_info->quantidade;
        }
        else{
          $qtde_estoque_unidade=0;
        }
        //obtem o saldo anterior de um material no estoque
        $sql="select quantidade from estoque where material_id_material='$mat->material_id_material' and ";
        $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Saldo Anterior Material", $db, "");
        $saldo_anterior=0;
        if(mysqli_num_rows($res)>0){
          while($qtde_estoque_material=mysqli_fetch_object($res)){
            $saldo_anterior+=(int)$qtde_estoque_material->quantidade;
          }
        }
        //verificando se eh uma atualizacao ou insercao no estoque
        $sql="select material_id_material from estoque where unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
        $sql.="and material_id_material='$mat->material_id_material' and ";
        $sql.="fabricante_id_fabricante='$mat->fabricante_id_fabricante' and ";
        $sql.="lote='$mat->lote' and flg_bloqueado=''";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Estoque", $db, "");
        if(mysqli_num_rows($res)>0){
          //eh uma atualizacao
          $mat_estoque=mysqli_fetch_object($res);
          $qtde=(int)$mat->qtde+(int)$qtde_estoque_unidade;
          $sql="update estoque set quantidade='$qtde', data_alt='$data', usua_alt='$_POST[id_login]'  ";
          $sql.="where material_id_material='$mat->material_id_material' and ";
          $sql.="fabricante_id_fabricante='$mat->fabricante_id_fabricante' and ";
          $sql.="lote='$mat->lote' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        }
        else{
          //verificando se existe material/lote/fabricante bloqueado para alguma unidade
          $sql="select material_id_material from estoque where fabricante_id_fabricante='$mat->fabricante_id_fabricante' ";
          $sql.="and material_id_material='$mat->material_id_material' and lote='$mat->lote' and ";
          $sql.="flg_bloqueado='S'";
          $res=mysqli_query($db, $sql);
          erro_sql("Select Material/Lote/Fabricante Bloqueado", $db, "");
          //existe material/fabricante/lote bloqueado para alguma unidade
          if(mysqli_num_rows($res)>0){
            $sql="insert into estoque ";
            $sql.="(fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote, validade, quantidade, data_incl, usua_incl, flg_bloqueado) ";
            $sql.="values ('$mat->fabricante_id_fabricante', '$mat->material_id_material', '$_SESSION[id_unidade_sistema]' ,'" . strtoupper($mat->lote) . "', '$mat->validade', '$mat->qtde', '$data', '$_POST[id_login]', 'S')";
          }
          else{
            //nao existe material/fabricante/lote bloqueado para alguma unidade
            $sql="insert into estoque ";
            $sql.="(fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote, validade, quantidade, data_incl, usua_incl, flg_bloqueado) ";
            $sql.="values ('$mat->fabricante_id_fabricante', '$mat->material_id_material', '$_SESSION[id_unidade_sistema]' ,'" . strtoupper($mat->lote) . "', '$mat->validade', '$mat->qtde', '$data', '$_POST[id_login]', '')";
          }
        }
        mysqli_query($db, $sql);
        erro_sql("Update/Insert Estoque", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
        //obtem o saldo atual de um material no estoque
        $sql="select quantidade from estoque where material_id_material='$mat->material_id_material' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Saldo Atual Material", $db, "");
        if(mysqli_num_rows($res)>0){
          $saldo_atual=0;
          while($qtde_estoque_material=mysqli_fetch_object($res)){
            $saldo_atual+=(int)$qtde_estoque_material->quantidade;
          }
        }
        //verificando se eh uma atualizacao ou insercao
        $sql="select qtde_entrada from movto_livro where movto_geral_id_movto_geral='$chave->id_movto_geral' ";
        $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$mat->material_id_material'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Movto Livro", $db, "");
        if(mysqli_num_rows($res)>0){
          //atualizando o movimento do livro
          $livro_info=mysqli_fetch_object($res);
          $qtde=(int)$livro_info->qtde_entrada+(int)$mat->qtde;
          $sql="update movto_livro set qtde_entrada='$qtde', saldo_atual='$saldo_atual'";
          $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and ";
          $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$mat->material_id_material'";
        }
        else{
          //insercao movimento do livro
          $sql="select descricao from tipo_movto where id_tipo_movto='5'";
          $res=mysqli_query($db, $sql);
          erro_sql("Select Tipo Movto", $db, "");
          if(mysqli_num_rows($res)>0){
            $mov_info=mysqli_fetch_object($res);
          }
          $history=$mov_info->descricao . " a partir da solicitação " . $_POST[codigo_atual] . " da unidade " . $unidade_solicitada->nome;
          $sql="insert into movto_livro ";
          $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material, tipo_movto_id_tipo_movto, saldo_anterior, qtde_entrada, saldo_atual, data_movto, historico) ";
          $sql.="values ('$chave->id_movto_geral', '$_SESSION[id_unidade_sistema]', '$mat->material_id_material', '5', '$saldo_anterior', '$mat->qtde', '$saldo_atual', '$data', '" . strtoupper($history) . "')";
        }
        mysqli_query($db, $sql);
        erro_sql("Update/Insert Movto Livro", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
      }
      //atualiza a coluna status_2 para atendida na tabela solicita_remanej
      $sql="update solicita_remanej set status_2='FORNECIDO' ";
      $sql.="where id_solicita_remanej='$_POST[codigo_atual]'";
      mysqli_query($db, $sql);
      erro_sql("Update Solicita Remanej", $db, "");
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if($atualizacao==""){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial.php?r=t&aplicacao=$_SESSION[APLICACAO]");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial.php?r=f&aplicacao=$_SESSION[APLICACAO]");
      }
      exit();
    }
    else{
      if($_GET[codigo]==""){
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial.php");
        exit();
      }
      else{
        //obtem numero da solicitacao, unidade solicitante, unidade solicitada
        $sql="select sol.id_solicita_remanej, u.nome, u.id_unidade, uni.id_unidade as idunidade, sol.status_2 ";
        $sql.="from solicita_remanej as sol, unidade as u, unidade as uni ";
        $sql.="where sol.id_unid_solicitante=u.id_unidade and sol.id_unid_solicitada=uni.id_unidade ";
        $sql.="and id_solicita_remanej='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Solicitação", $db, "");
        if(mysqli_num_rows($res)>0){
          $solicitacao=mysqli_fetch_object($res);
        }
        //obtem os materias atendidos
        $sql_itens="select m.codigo_material, m.descricao as mdescricao, it_mov.lote, ";
        $sql_itens.="f.descricao as fdescricao, it_mov.qtde, it.qtde_solicita ";
        $sql_itens.="from item_solicita_remanej as it,  itens_movto_geral as it_mov, ";
        $sql_itens.="fabricante as f, material as m ";
        $sql_itens.="where it.id_item_solicita_remanej=it_mov.item_solicita_remanej and ";
        $sql_itens.="it_mov.fabricante_id_fabricante=f.id_fabricante and ";
        $sql_itens.="m.id_material=it_mov.material_id_material and ";
        $sql_itens.="id_solicita_remanej='$_GET[codigo]'";
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript">
    <!--
      function habilitaBotaoSalvar(){
        var x=document.form_registrado;
        if(Trim(x.login.value)=="" || Trim(x.senha.value)==""){
          x.salvar.disabled=true;
        }
        else{
          x.salvar.disabled=false;
        }
      }

      function desabilitaBotaoSalvar(){
        var x=document.form_registrado;
        x.salvar.disabled=true;
      }

      function Trim(str){
        return str.replace(/^\s+|\s+$/g,"");
      }

      function salvarMovimento(){
        var x=document.form_registrado;
        if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
          verificaLoginSenhaResponsavelDispensacao();
        }
        else{
          salvarDados();
        }
      }

      function verificaLoginSenhaResponsavelDispensacao(){
        var x=document.form_registrado;
        var url = "../../xml_dispensacao/verificar_login_senha_responsavel_dispensacao.php?login="+x.login.value+"&senha="+x.senha.value;
        requisicaoHTTP("GET", url, true, '');
      }

      function trataDados(){
        var x=document.form_registrado;
	    var info = ajax.responseText;  // obtém a resposta como string
        var login_senha=info.split("@");
        if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
          window.alert("Login e/ou Senha Inválidos!");
          x.login.focus();
          return;
        }
        if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
          x.id_login.value=login_senha[1];
          salvarDados();
          return;
        }
      }
      
      function salvarDados(){
        var x=document.form_registrado;
        x.salvar.disabled='true';
        x.flag.value='t';
        x.submit();
      }
    //-->
    </script>
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
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_registrado" action="./remanejamento_registrado.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?>: Efetivar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nº da Solicitação
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" size="30" style="width: 200px" disabled value="<?php echo $solicitacao->id_solicita_remanej;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitante
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="unidade_solicitante" size="30" disabled style="width: 200px" value="<?php echo $solicitacao->nome;?>">
                      </td>
                    </tr>
                    <tr>
                      <?php
                        $sql="select id_unidade, nome from unidade where id_unidade!='$solicitacao->id_unidade' order by nome";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Unidade Solicitada", $db, "");
                      ?>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitada
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="unidade_solicitada" size="1" style="width: 200px" disabled>
                        <option> Selecione uma Unidade </option>
                        <?php
                          while($unidade_solic=mysqli_fetch_object($res)){
                        ?>
                            <option value="<?php echo $unidade_solic->id_unidade;?>" <?php if($unidade_solic->id_unidade==$solicitacao->idunidade){echo "selected";}?>> <?php echo $unidade_solic->nome;?> </option>
                        <?php
                          }
                        ?>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Status
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="status" size="30" style="width: 200px" disabled value="<?php echo $solicitacao->status_2;?>">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="10%" align="center"> Código </td>
                            <td width="50%" align="center"> Material </td>
                            <td width="10%" align="center"> Lote </td>
                            <td width="10%" align="center"> Fabricante </td>
                            <td width="10%" align="center"> Qtde Solic </td>
                            <td width="10%" align="center"> Qtde Atend </td>
                          </tr>
                          <?php
                            $cor_linha = "#CCCCCC";
                            ///////////////////////////////////////
                            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
                            ///////////////////////////////////////

                            $res=mysqli_query($db, $sql_itens);
                            erro_sql("Select Lista", $db, "");
                            while($mat_atend=mysqli_fetch_object($res)){
                          ?>
                              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>'>
                                <td align="left"> <?php echo $mat_atend->codigo_material;?> </td>
                                <td align="left"> <?php echo $mat_atend->mdescricao;?> </td>
                                <td align="left"> <?php echo $mat_atend->lote;?> </td>
                                <td align="left"> <?php echo $mat_atend->fdescricao;?> </td>
                                <td align="right"> <?php echo (int)$mat_atend->qtde_solicita;?> </td>
                                <td align="right"> <?php echo (int)$mat_atend->qtde;?> </td>
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
                          ?>
                          <tr>
                            <td colspan="2" height="100%"></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3">
                        <?php
                          if($mostrar_responsavel_dispensacao!="S"){
                            $mostrar_login_senha="none";
                          }
                          else{
                            $mostrar_login_senha="''";
                          }
                        ?>
                        <div id="mostrar_responsavel_dispensacao" style="display:<?php echo $mostrar_login_senha;?>">
                          <table>
                            <tr>
                              <td class="descricao_campo_tabela" width="30%">
                                Realizado por:
                              </td>
                              <td class="descricao_campo_tabela" width="10%">
                                Login:
                              </td>
                              <td>
                                <input type="text" name="login" onblur="habilitaBotaoSalvar();" onfocus="desabilitaBotaoSalvar();">
                                <input type="hidden" name="id_login" value="">
                              </td>
                              <td class="descricao_campo_tabela" width="10%">
                                Senha:
                              </td>
                              <td>
                                <input type="password" name="senha" onblur="habilitaBotaoSalvar(); document.form_registrado.salvar.focus();" onfocus="desabilitaBotaoSalvar();">
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                      <td valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inicial.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarMovimento();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
                    </tr>
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="codigo_atual" value="<?php echo $_GET[codigo];?>">
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
    <script language="javascript">
    <!--
      if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
        document.form_registrado.salvar.disabled=true;
      }
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
  else{
    include_once "../../config/erro_config.php";
  }
?>
