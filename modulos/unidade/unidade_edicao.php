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
  if($_SESSION[id_usuario_sistema]==''){
    header("Location: ". URL."/start.php");
    exit();
  }

  if($_GET[id_unidade]!=""){
    $sql_select = "select id_unidade, sigla, cnes, nome, unidade_id_unidade, flg_nivel_superior,
                   coordenador, rua, numero, complemento, bairro, municipio, uf,
                   cep, telefone, e_mail, cod_estabelecimento,flg_banco, dns_local, usuario_integra_local,
                   senha_integra_local,flg_transf_almo,base_integra_ima
                   from unidade where id_unidade = '".$_GET[id_unidade]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Unidade", $db, "");
    $unidade    = mysqli_fetch_object($res);

    $id_unidade            = $unidade->id_unidade;
    $sigla                 = $unidade->sigla;
    $cnes                  = $unidade->cnes;
    $nome                  = $unidade->nome;
    $nome_und_sup          = $unidade->unidade_id_unidade;
    $flg_nivelsuperior     = $unidade->flg_nivel_superior;
    $coordenador           = $unidade->coordenador;
    $rua                   = $unidade->rua;
    $numero                = $unidade->numero;
    $complemento           = $unidade->complemento;
    $bairro                = $unidade->bairro;
    $municipio             = $unidade->municipio;
    $uf                    = $unidade->uf;
    $cep                   = $unidade->cep;
    $telefone              = $unidade->telefone;
    $e_mail                = $unidade->e_mail;
    $cod_estabelecimento   = $unidade->cod_estabelecimento;
    $flgBanco              = $unidade->flg_banco;
    $dns_local             = $unidade->dns_local;
    $usuario_integra_local = $unidade->usuario_integra_local;
    $senha_integra_local   = $unidade->senha_integra_local;
    $flg_transfalmo        = $unidade->flg_transf_almo;
    $base_integra_ima      = $unidade->base_integra_ima;

    
    
    
  }

  if($_POST[id_unidade]!=""){
    $sql_alteracao = "update unidade
                      set
                      sigla                 = '" . strtoupper($_POST[sigla]) . "',
                      cnes                  = '$_POST[cnes]',
                      nome                  = '" . strtoupper($_POST[nome]) . "',
                      unidade_id_unidade    = '$_POST[nome_und_sup]',
                      flg_nivel_superior    = '$_POST[flg_nivelsuperior]',
                      coordenador           = '" . strtoupper($_POST[coordenador]) . "',
                      rua                   = '" . strtoupper($_POST[rua]) . "',
                      numero                = '" . strtoupper($_POST[numero]) ."',
                      complemento           = '" . strtoupper($_POST[complemento]) . "',
                      bairro                = '" . strtoupper($_POST[bairro]) . "',
                      municipio             = '" . strtoupper($_POST[municipio]) . "',
                      uf                    = '$_POST[uf]',
                      cep                   = '" . strtoupper($_POST[cep]) . "',
                      telefone              = '" . strtoupper($_POST[telefone]) . "',
                      e_mail                = '" . strtoupper($_POST[e_mail]) . "',
                      status_2              = 'A',
                      data_alt              = '".date("Y-m-d H:m:s")."',
                      usua_alt              = '$_SESSION[id_usuario_sistema]',
                      cod_estabelecimento   = '$_POST[cod_estabelecimento]',
                      flg_banco             = '$_POST[flgBanco]',
                      dns_local             = '". $_POST[dns_local]."',
                      usuario_integra_local = '". $_POST[usuario_integra_local]."',
                      senha_integra_local   = '". $_POST[senha_integra_local]."',
                      flg_transf_almo       = '". $_POST[flg_transfalmo]."',
                      base_integra_ima      = '". $_POST[base_integra_ima]."'

                      where id_unidade      = '$_POST[id_unidade]';";
      // echo $sql_alteracao;
       //echo exit;
    mysqli_query($db, $sql_alteracao);
    erro_sql("Update Unidade", $db, "");
    if(mysqli_errno($db)=="0"){
      mysqli_commit($db);
      $aux=$_POST[aux];
      header("Location: ". URL."/modulos/unidade/unidade_inicial.php?a=t&".$aux);
    }
    else{
      mysqli_rollback($db);
      header("Location: ". URL."/modulos/unidade/unidade_inicial.php?a=f");
    }
    exit();
  }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";
  require DIR."/buscar_aplic.php";
?>
  <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
  <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
  <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
  <script language="JavaScript" type="text/JavaScript">
  <!--
  function retirarEspaco(){
    var x=document.form_alteracao;
    var sigla=x.sigla.value;
    while(sigla.match("  ")){
      sigla=sigla.replace("  ", " ");
    }
    if(sigla.charAt(0)==" "){
      sigla=sigla.substr(1, sigla.length);
    }
    if(sigla.charAt(sigla.length-1)==" "){
      sigla=sigla.substr(0, sigla.length-1);
    }
    x.sigla.value=sigla;
  }

  function trataDados(){
      var auxinfo="";
      var auxok="";
      var x=document.form_alteracao;
      var info1 = ajax.responseText;  // obtém a resposta como string
      var info=info1.substr(0, 3);

      if(info=="NAO"){
        var msg="Unidade já cadastrada!\n";
        window.alert(msg);
        x.sigla.focus();
        x.sigla.select();
      }

      if(info=="SAV"){
         auxinfo='S';
         if(x.checarCodEst.value=='S')
         {
           verificarEstabelecimento();
         }
      }


       var pos = info1.indexOf("|");
       var verifica = info1.substr(0, pos);
       var cnes = info1.substr(pos+1);
       if(info=="NOK"){
          alert("Já existe um estabelecimento cadastrado com esse número de CNES e CMES");
          x.cod_estabelecimento.focus();
          x.cod_estabelecimento.select();
       }



    if(x.checarCodEst.value=='S')
    {
        if(info=="OK!") {
           x.submit();
        }
    }
    else if(x.checarCodEst.value=='N')
    {
        if(info=="SAV"){
          x.submit();
        }
    }
  }

  function mudou_nivel(){
    var x=document.form_alteracao;
    x.nome_und_sup[0].selected=true;
  }

  function verificarSigla(){
    retirarEspaco();
    var x=document.form_alteracao;
    var sigla=x.sigla.value;
    var id=x.id_unidade.value;

    var url = "../../xml/unidadeSigla.php?sigla=" + sigla + "&id=" + id;
    requisicaoHTTP("GET", url, true);
  }

  function verificarEstabelecimento(){
    var x=document.form_alteracao;
    if(x.checarCodEst.value=='S')
      {
         retirarEspaco();
         var cod_estabelecimento=x.cod_estabelecimento.value;
         var est_antigo = x.est_antigo.value;
         var cnes = x.cnes.value;
         var url = "../../xml/unidadeEstabelecimento.php?cod_estabelecimento=" + cod_estabelecimento + "&cnes=" + cnes +
         "&est_antigo=" + est_antigo +
         "&operacao=A";
         requisicaoHTTP("GET", url, true);
      }
  }
  
  function salvarDados(){
    var x=document.form_alteracao;

    if(x.checarCodEst.value=='S'){
      if(validarCampos()==true){
        if(validarCampos_codEstab()==true){
           verificarSigla();
//           verificarEstabelecimento();
        }
      }
    }
    else
    {
      if(validarCampos()==true){
       verificarSigla();
      }
    }

  }

  function validarCampos(){
    var x=document.form_alteracao;
    if(x.sigla.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.sigla.focus();
      return false;
    }
    if(x.nome.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.nome.focus();
      return false;
    }
    if(x.flg_nivelsuperior[1].checked){
      if(x.nome_und_sup.value==""){
        window.alert("É necessário informar uma Unidade Superior!");
        x.nome_und_sup.focus();
        return false;
      }
    }
    return true;
  }
  
  function validarCampos_codEstab(){
    var x=document.form_alteracao;
    if(x.cnes.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.cnes.focus();
      return false;
    }
    if(x.cod_estabelecimento.value==""){
      window.alert("Favor preencher o campos obrigatórios!");
      x.cod_estabelecimento.focus();
      return false;
    }
   return true;
  }
  //-->
  </script>
  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
    <tr><td><?php echo $caminho;?></td></tr>
  </table>
  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr height="5%">
      <td>
        <table width="100%" class="titulo_tabela" height="21">
          <tr><td align="center"><? echo $nome_aplicacao; ?>: Alterar</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="1">
          <form name="form_alteracao" action="./unidade_edicao.php" method="POST" enctype="application/x-www-form-urlencoded">
            <tr>
              <td align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat.gif";?>">Sigla
              </td>
              <td align="left" colspan="6" width="75%" class="campo_tabela">
                <input type="text" name="sigla" id="sigla" size="30" maxlength="10" value="<?php echo $sigla;?>">
              </td>
            </tr>
            <tr>
              <td align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat.gif";?>">Unidade
              </td>
              <td align="left" colspan="6" width="75%" class="campo_tabela">
                <input type="text" name="nome" id="nome" size="102" maxlength="40" value="<?php echo $nome;?>">
              </td>
            </tr>
            <tr>
              <td align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat.gif";?>">Nível Superior
              </td>
              <td align="left" width="25%" class="campo_tabela">
                <input type="radio" name="flg_nivelsuperior" value="1" <?php if ($flg_nivelsuperior=='1'){echo "checked";}?> >Sim &nbsp&nbsp&nbsp
                <input type="radio" name="flg_nivelsuperior" value="0" <?php if ($flg_nivelsuperior!='1'){echo "checked";}?> >Não
              </td>
              <td align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Unidade Superior
              </td>
              <td align="left" width="25%" class="campo_tabela">
                <select name="nome_und_sup" style="width:205px;" >
                   <option value=""></option>
                     <?php
                       $sql = "select id_unidade, nome from unidade where flg_nivel_superior = '1' and status_2 = 'A' order by nome";
                       $nivel = mysqli_query($db, $sql);
                       erro_sql("Select Unidade Superior", $db, "");
                       if(mysqli_num_rows($nivel)>0){
                         while($lista_nivel = mysqli_fetch_object($nivel)){
                           if($lista_nivel->id_unidade == $nome_und_sup){
                     ?>
                             <option value="<?php echo $lista_nivel->id_unidade; ?>" selected><?php echo $lista_nivel->nome; ?></option>
                     <?php
                           }
                           else{
                     ?>
                             <option value="<?php echo $lista_nivel->id_unidade; ?>"><?php echo $lista_nivel->nome; ?></option>
                     <?php
                           }
                         }
                       }
                     ?>
                </select>
              </td>
            </tr>
            <tr>


              <?php
                     $sql="select mostrar_cod_estab, nome_cod_estab from parametro";
                     $param = mysqli_query($db, $sql);
                     erro_sql("Tabela Parametro", $db, "");
                     if($tb_parametro = mysqli_fetch_object($param)){
                          $tb_parametro->mostrar_cod_estab;
                          $tb_parametro->nome_cod_estab;
                          if (strtoupper($tb_parametro->mostrar_cod_estab)=='S')
                          {  ?>
                           <td align="left" width="25%" class="descricao_campo_tabela">
                               <img src="<? echo URL."/imagens/obrigat.gif";?>">Cnes
                           </td>
                           <td align="left" width="25%"  class="campo_tabela">
                               <input type="text" name="cnes" id="cnes" size="30" maxlength="10" value="<?php echo $cnes;?>" onKeyPress="return isNumberKey(event);">
                           </td>
                           <td align="left" width="25%" class="descricao_campo_tabela">
                             <img src="<? echo URL."/imagens/obrigat.gif";?>"><? echo $tb_parametro->nome_cod_estab;?>
                           </td>
                           <td align="left" width="25%"  class="campo_tabela">
                             <input type="text" name="cod_estabelecimento" id="cod_estabelecimento" size="30" maxlength="10" value="<?php echo $cod_estabelecimento;?>" onKeyPress="return isNumberKey(event);" >
                             <input type="hidden" name="est_antigo" value="<?php echo $cod_estabelecimento;?>" >
                             <input type="hidden" name="checarCodEst" id="checarCodEst" value="S">
                           </td>

                         <?
                          }
                          else
                          {?>
                            <td align="left" width="25%" class="descricao_campo_tabela">
                              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Cnes
                            </td>
                            <td align="left" width="25%"  class="campo_tabela">
                              <input type="text" name="cnes" id="cnes" size="30" maxlength="10" value="<?php echo $cnes;?>" onKeyPress="return isNumberKey(event);">
                            </td>
                            <td align="left" width="25%"  class="campo_tabela" colspan="2">
                              <input type="hidden" name="checarCodEst" id="checarCodEst" value="N">
                            </td>
                          <?
                          }
                     }
                    ?>

            </tr>
             <tr>
              <td align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Coordenador
              </td>
             <td align="left" colspan="6" width="75%" class="campo_tabela">
               <input type="text" name="coordenador" id="coordenador" size="102" maxlength="100" value="<?php echo $coordenador;?>">
              </td>
            </tr>
            <tr>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Logradouro
              </td>
              <td align="left" colspan="6" class="campo_tabela">
                <input type="text" name="rua" id="rua" size="102" maxlength="40" value="<?php echo $rua;?>">
              </td>
            </tr>
            <tr>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Número
              </td>
              <td align="left" class="campo_tabela">
                <input type="text" name="numero" id="numero" size="10" maxlength="10" value="<?php echo $numero;?>">
              </td>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Complemento
              </td>
              <td align="left" class="campo_tabela">
                <input type="text" name="complemento" id="complemento" size="30" maxlength="20" value="<?php echo $complemento;?>">
              </td>
            </tr>
            <tr>
              <td align="left" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Bairro
              </td>
              <td align="left" class="campo_tabela" colspan="3">
                <input type="text" name="bairro" id="bairro" size="102" maxlength="20" value="<?php echo $bairro;?>">
              </td>
            </tr>
            <tr>
              <td align="left" class="descricao_campo_tabela">
               <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Cidade
              </td>
              <td align="left" class="campo_tabela">
                <input type="text" name="municipio" size="30" maxlength="20" value="<?php echo $municipio;?>">
              </td>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">UF
              <td align="left" class="campo_tabela">
                <select name="uf" style="width:50px;">
                  <option value=""></option>
                    <?php
                      $sql = "select uf from estado order by uf";
                      $estado = mysqli_query($db, $sql);
                      erro_sql("Select UF", $db, "");
                      if(mysqli_num_rows($estado)>0){
                        while($lista_estado = mysqli_fetch_object($estado)){
                          if($lista_estado->uf == $uf){
                    ?>
                            <option value="<?php echo $lista_estado->uf; ?>" selected><?php echo $lista_estado->uf; ?></option>
                    <?php
                          }
                          else{
                    ?>
                            <option value="<?php echo $lista_estado->uf; ?>"><?php echo $lista_estado->uf; ?></option>
                    <?php
                          }
                        }
                      }
                    ?>
                </select>
              </td>
            </tr>
            <tr>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Cep
              </td>
              <td align="left" class="campo_tabela">
                <input type="text" name="cep" id="cep" size="20" maxlength="20" value="<?php echo $cep;?>">
              </td>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Telefone
              </td>
              <td align="left" class="campo_tabela">
                <input type="text" name="telefone" id="telefone" size="30" maxlength="15" value="<?php echo $telefone;?>">
              </td>
            </tr>
            <tr>
              <td align="left" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Email
              </td>
              <td align="left" class="campo_tabela" colspan="3">
                <input type="text" name="e_mail" id="e_mail" size="102" maxlength="40" value="<?php echo $e_mail;?>">
              </td>
            </tr>
            <!-- Glaison - Inicio  -->
     	          <table  width="100%" class="titulo_tabela" cellpadding="0" cellspacing="1">
		            <TR align="center">
				      <TD colspan = "4">Configurações</TD>
				      <TD  width="10"><A href="javascript:showFrame('unidades');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Configurações"></A></TD>
				    </TR>
                 </TABLE>
                      </TD>
		      </TR>
			  <TR>
			    <TD colspan="4">
			      <div id="unidades" style="display:'';">

			        <table border="0" width="100%" cellpadding="0" cellspacing="1">
                    <tr>
                        <td  colspan = "4" align="left" width="25%" class="descricao_campo_tabela">
                            <img src="<? echo URL."/imagens/obrigat_1.gif";?>">SIG2M
                        </td>
                         <td  colspan = "4" align="left" width="25%"  class="campo_tabela">
                            <input type="radio" name="flgBanco" value="1" <?php if ($flgBanco =='1'){echo "checked";}?> >IMA &nbsp&nbsp&nbsp
                            <input type="radio" name="flgBanco" value="0" <?php if ($flgBanco!='1'){echo "checked";}?> >Unidades
                         </td>
                   </TR>

              <TR>
                     <td colspan = "4" align="left" width="30%" class="descricao_campo_tabela">
                         <img src="<? echo URL."/imagens/obrigat_1.gif";?>">DNS
                     </td>
                     <td  colspan = "4" align="left" width="70%" class="campo_tabela">
                          <input type="text" name="dns_local" id= "dns_local" size="102" maxlength="70" value="<?php echo $dns_local;?>">
                     </td>
              </TR>
                   <!--Inicio novo campo para informar o nome do banco de dados -->
               <tr>
				   <td colspan="4" align="left" class="descricao_campo_tabela"><img
				   	    src="<? echo URL."/imagens/obrigat_1.gif";?>" alt="" />Banco de Dados
                    </td>
				    <td colspan="4" align="left" class="campo_tabela">
                    <input type="text" name="base_integra_ima" id="base_integra_ima" size="102"	maxlength="20"  value="<?php echo $base_integra_ima;?>">
                    </td>
			  </tr>


             <!-- Fim novo campo-->
              
          <tr>
              <td  COLSPAN = "4" align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Usuário
              </td>
              <td    align="left"  width="25%" class="campo_tabela">
                <input type="text" name="usuario_integra_local" id="usuario_integra_local" size="30" maxlength="15"  value="<?php echo $usuario_integra_local;?>">
              </td>
              <td   align="left" width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Senha
              </td>
              <td  align="left" width="25%" class="campo_tabela">
                <input type="password" name="senha_integra_local" id="senha_integra_local" size="31" maxlength="15" value="<?php echo $senha_integra_local;?>">
              </td>
          </tr>
          <tr>
              <td  colspan = "4" align="left" width="25%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Integração <br>
                  &nbsp&nbsp&nbsp Almox. Central
              </td>
              <td  colspan = "4" align="left" width="25%"  class="campo_tabela">
                  <input type="radio" name="flg_transfalmo" value="s" <?php if($flg_transfalmo =='s'){echo "checked";}?> ">Sim &nbsp&nbsp&nbsp
                  <input type="radio" name="flg_transfalmo" value="n" <?php if($flg_transfalmo != 's') {echo "checked";} ?> ">Não
              </td>
          </tr>
        </TABLE>
      </div>
            <!-- Glaison - Fim  -->
            <tr>
              <td colspan="4" align="right" class="descricao_campo_tabela" height="35">
                <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/unidade/unidade_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
                <input style="font-size: 12px;" type="button" name="salvar"  value="Salvar >>" onClick="salvarDados();">
                <input type="hidden" name="id_unidade" id="id_unidade" value="<?php echo $id_unidade;?>">
              </td>
            </tr>
    		<tr>
			  <td colspan="4" class="descricao_campo_tabela">
				<table align="center" border="0">
                  <tr valign="center" class="descricao_campo_tabela" height="21">
				    <td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
				    <td>&nbsp&nbsp&nbsp</td>
                    <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
				  </tr>
				</table>
              </td>
		    </tr>
		    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET['pesquisa']?>">
		  </form>
        </table>
      </td>
    </tr>

  <script language="javascript">
  <!--
    document.form_alteracao.sigla.focus();
  //-->
  </script>
       <div align="center">
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
</div>
