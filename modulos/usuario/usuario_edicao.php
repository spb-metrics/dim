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
  //  Arquivo..: usuario_edicao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de edicao de usuario
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXIST�NCIA DE ARQUIVO DE CONFIGURA��O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php")){

    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
    }
	$perfil = $_POST['perfil'];
	
	//echo "<script>alert('$perfil');</script>";

    //verificando se unidade � distrito
    $sql="select flg_nivel_superior from unidade where id_unidade = '$_SESSION[id_unidade_sistema]' and status_2='A'";

    $res=mysqli_query($db, $sql);
    erro_sql("Select Distrito", $db, "");
    $nivelsuperior    = mysqli_fetch_object($res);

    $lista_unidade="";
    if ($nivelsuperior->flg_nivel_superior=='1'){
      $sql="select id_unidade from unidade where unidade_id_unidade = '$_SESSION[id_unidade_sistema]' and status_2='A'";
      $result = mysqli_query($db, $sql);
      erro_sql("Select N�vel Superior", $db, "");
      while ($unidades  = mysqli_fetch_object($result)){
        $lista_unidades .= $unidades->id_unidade.",";

        $sql2="select id_unidade from unidade where unidade_id_unidade = '$unidades->id_unidade' and status_2='A'";
        $result2 = mysqli_query($db, $sql2);
        erro_sql("Select Unidades e SubUnidades", $db, "");
        while ($unidades2  = mysqli_fetch_object($result2)){
          $lista_unidades .= $unidades2->id_unidade.",";
        }
      }
      $lista_unidades .= $_SESSION[id_unidade_sistema].",";
      $lista_unidades="(".substr($lista_unidades,0, strlen($lista_unidades)-1).")";
    }
    else{
      $lista_unidades="(".$_SESSION[id_unidade_sistema].")";
    }

    if($_POST[novo_login]=="t"){
      $sql="select id_usuario from usuario where login='$_POST[login]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Usu�rio Existente", $db, "");
      if(mysqli_num_rows($res)>0){
        header("Location: ". URL."/modulos/usuario/usuario_edicao.php?l=f&id_usuario=$_POST[id_usuario_atual]");
      }
    }
    else{
      //essa parte acontece qdo pressiona o botao salvar
      if($_POST[salvar_cadastro]=="t"){
        if($_POST[situacao]=="I" && count($_SESSION["LISTA"])>0){
          $perfil_aux="";
          foreach($perfil as $valor){
            $perfil_aux.=$valor . "|";
          }
          $perfil_info=split("[|]", $perfil_aux);
          $lista_aux=$_SESSION["LISTA"];
          $index=0;
          $posicao=0;
          foreach($lista_aux as $linha){
            foreach($linha as $coluna){
              if($index==1){
                $aux_lista[][]=$perfil_info[$posicao];
              }
              else{
                $aux_lista[][]=$coluna;
              }
              if($index==(QTDE_COLUNA-5)){
                $index=0;
                $posicao++;
              }
              else{
                $index++;
              }
            }
          }
          $_SESSION["LISTA"]=$aux_lista;
          header("Location: ". URL."/modulos/usuario/usuario_edicao.php?s=f&id_usuario=$_POST[id_usuario_atual]");
        }
        else{
          if($_POST[situacao]=="A"){
            $perfil_aux="";
            foreach($perfil as $valor){
              $perfil_aux.=$valor . "|";
            }
            $perfil_info=split("[|]", $perfil_aux);
            $lista_aux=$_SESSION["LISTA"];
            $index=0;
            $posicao=0;
            foreach($lista_aux as $linha){
              foreach($linha as $coluna){
                if($index==1){
                  $aux_lista[][]=$perfil_info[$posicao];
                }
                else{
                  $aux_lista[][]=$coluna;
                }
                if($index==(QTDE_COLUNA-5)){
                  $index=0;
                  $posicao++;
                }
                else{
                  $index++;
                }
              }
            }
            $_SESSION["LISTA"]=$aux_lista;
          }
          $data=date("Y-m-d H:m:s");
          if($_POST[senha]==""){
            $sql="update usuario set nome='" . strtoupper($_POST[nome]) . "', matricula='$_POST[matricula]', ";
            $sql.="login='" . strtoupper($_POST[login]) . "', situacao='$_POST[situacao]', data_alt='$data', ";
            $sql.="usua_alt='$_SESSION[id_usuario_sistema]' ";
            $sql.="where id_usuario='$_POST[id_usuario_atual]'";
          }
          else{
            $sql="update usuario set nome='" . strtoupper($_POST[nome]) . "', matricula='$_POST[matricula]', ";
            $sql.="login='". strtoupper($_POST[login]) . "', situacao='$_POST[situacao]', data_alt='$data', ";
            $sql.="usua_alt='$_SESSION[id_usuario_sistema]', senha=OLD_PASSWORD('$_POST[senha]') ";
            $sql.="where id_usuario='$_POST[id_usuario_atual]'";
          }
          mysqli_query($db, $sql);
          erro_sql("Update Usu�rio", $db, "");
          $atualizacao="";
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }
          $sql="delete from unidade_has_usuario where usuario_id_usuario='$_POST[id_usuario_atual]'";
          mysqli_query($db, $sql);
          erro_sql("Delete Unidade Has Usu�rio", $db, "");
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }
          if($_POST[situacao]=="A"){
            $index=0;
            $info="";
            $lista=$_SESSION["LISTA"];
            foreach($lista as $linha){
              foreach($linha as $coluna){
                if($index<(QTDE_COLUNA-5)){
                  $info.=$coluna . "|";
                }
                else{
                  $info.=$coluna;
                }
                if($index==(QTDE_COLUNA-5)){
                  $valores=split("[|]", $info);
                  $sql="insert into unidade_has_usuario ";
                  $sql.="(unidade_id_unidade, usuario_id_usuario, perfil_id_perfil)";
                  $sql.="values ('$valores[0]', '$_POST[id_usuario_atual]', '$valores[1]')";
                  mysqli_query($db, $sql);
                  erro_sql("Insert Unidade Has Usu�rio", $db, "");
                  if(mysqli_errno($db)!="0"){
                    $atualizacao="erro";
                  }
                  $index=0;
                  $info="";
                }
                else{
                  $index++;
                }
              }
            }
          }
          if($atualizacao==""){
            mysqli_commit($db);
            $aux=$_POST[aux];
            header("Location: ". URL."/modulos/usuario/usuario_inicial.php?a=t&".$aux);
          }
          else{
            mysqli_rollback($db);
            header("Location: ". URL."/modulos/usuario/usuario_inicial.php?a=f");
          }
        }
      }
      else{
        //essa parte acontece qdo pressiona o botao todas
        if($_POST[all]=="t"){
          if(count($_SESSION["LISTA"])>0){
            $perfil_aux="";
            foreach($perfil as $valor){
              $perfil_aux.=$valor . "|";
            }
            $perfil_info=split("[|]", $perfil_aux);
            $lista_aux=$_SESSION["LISTA"];
            $index=0;
            $posicao=0;
            $unidades_aux="";
            foreach($lista_aux as $linha){
              foreach($linha as $coluna){
                if($index==1){
                  $aux_unidade_perfil[][]=$perfil_info[$posicao];
                }
                else{
                  $aux_unidade_perfil[][]=$coluna;
                  $unidades_aux.=$coluna . ",";
                }
                if($index==(QTDE_COLUNA-5)){
                  $index=0;
                  $posicao++;
                }
                else{
                  $index++;
                }
              }
            }
            $sql="select id_unidade from unidade where id_unidade in $lista_unidades and ";
            $sql.="id_unidade not in (" . substr($unidades_aux, 0, strlen($unidades_aux)-1) .  ") ";
            $sql.="order by nome";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Unidade - All, Count>0", $db, "");
            while($todas_unidades=mysqli_fetch_object($res)){
              $aux_unidade_perfil[][]=$todas_unidades->id_unidade;
              $aux_unidade_perfil[][]="";
            }
          }
          else{
            $sql="select id_unidade from unidade where id_unidade in $lista_unidades order by nome";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Unidade - All, Count<=0", $db, "");
            while($todas_unidades=mysqli_fetch_object($res)){
              $aux_unidade_perfil[][]=$todas_unidades->id_unidade;
              $aux_unidade_perfil[][]="";
            }
          }
          $_SESSION["LISTA"]=$aux_unidade_perfil;
        }
        else{
          //essa parte acontece qdo tenta excluir uma unidade
          if($_POST[unidade_atual]!=""){
            $perfil_aux="";
            foreach($perfil as $valor){
              $perfil_aux.=$valor . "|";
            }
            $perfil_info=split("[|]", $perfil_aux);
            $lista_aux=$_SESSION["LISTA"];
            $index=0;
            $posicao=0;
            foreach($lista_aux as $linha){
              foreach($linha as $coluna){
                if($index==1){
                  $aux_lista[][]=$perfil_info[$posicao];
                }
                else{
                  $aux_lista[][]=$coluna;
                }
                if($index==(QTDE_COLUNA-5)){
                  $index=0;
                  $posicao++;
                }
                else{
                  $index++;
                }
              }
            }
            $_SESSION["LISTA"]=$aux_lista;
            $lista=$_SESSION["LISTA"];
            $index=0;
            $info="";
            foreach($lista as $linha){
              foreach($linha as $coluna){
                if($index<(QTDE_COLUNA-5)){
                  $info.=$coluna . "|";
                }
                else{
                  $info.=$coluna;
                }
                if($index==(QTDE_COLUNA-5)){
                  $valores=split("[|]", $info);
                  if($valores[0]!=$_POST[unidade_atual]){
                    $aux_unidade_perfil[][]=$valores[0];
                    $aux_unidade_perfil[][]=$valores[1];
                  }
                  $index=0;
                  $info="";
                }
                else{
                  $index++;
                }
              }
            }
            if(count($aux_unidade_perfil)<=0){
              session_unregister("LISTA");
            }
            else{
              $_SESSION["LISTA"]=$aux_unidade_perfil;
            }
          }
          else{
            //essa parte acontece qdo pressiona o botao ok
            if($_POST[flag]=="t"){
              if(count($_SESSION["LISTA"])>0){
                $perfil_aux="";
                foreach($perfil as $valor){
                  $perfil_aux.=$valor . "|";
                }
                $perfil_info=split("[|]", $perfil_aux);
                $lista=$_SESSION["LISTA"];
                $index=0;
                $posicao=0;
                foreach($lista as $linha){
                  foreach($linha as $coluna){
                    if($index==1){
                      $aux_unidade_perfil[][]=$perfil_info[$posicao];
                    }
                    else{
                      $aux_unidade_perfil[][]=$coluna;
                    }
                    if($index==(QTDE_COLUNA-5)){
                      $index=0;
                      $posicao++;
                    }
                    else{
                      $index++;
                    }
                  }
                }
                $_SESSION["LISTA"]=$aux_unidade_perfil;
                $lista=$_SESSION["LISTA"];
                $index=0;
                $existe="";
                foreach($lista as $linha){
                  foreach($linha as $coluna){
                    if($index==0){
                      if($coluna==$_POST[unidade]){
                        $existe="sim";
                        break 2;
                      }
                    }
                    if($index==(QTDE_COLUNA-5)){
                      $index=0;
                    }
                    else{
                      $index++;
                    }
                  }
                }
                if($existe!=""){
                  header("Location: ". URL."/modulos/usuario/usuario_edicao.php?v=f&nome=$_POST[nome]&matricula=$_POST[matricula]&login=$_POST[login]&situacao=$_POST[situacao]&id_usuario=$_POST[id_usuario_atual]");
                }
                else{
                  $lista[][]=$_POST[unidade];
                  $lista[][]="";
                  $_SESSION["LISTA"]=$lista;
                }
              }
              else{
                $lista[][]=$_POST[unidade];
                $lista[][]="";
                $_SESSION["LISTA"]=$lista;
              }
            }
            else{
              //essa parte acontece qdo inicializa a pagina
              if($_GET[id_usuario]=="" && $_GET[v]==""){
                header("Location: ". URL."/modulos/usuario/usuario_inicial.php");
              }
              else{
                if(!isset($_GET[v])){
                  $sql="select nome, matricula, login, situacao from usuario where id_usuario='$_GET[id_usuario]'";
                  $res=mysqli_query($db, $sql);
                  erro_sql("Select Usu�rio Escolhido", $db, "");
                  if(mysqli_num_rows($res)>0){
                    $usuario_info=mysqli_fetch_object($res);
                    $_POST[nome]=$usuario_info->nome;
                    $_POST[matricula]=$usuario_info->matricula;
                    $_POST[login]=$usuario_info->login;
                    $_POST[situacao]=$usuario_info->situacao;
                  }
                  $sql = "select unidade_id_unidade, perfil_id_perfil from unidade_has_usuario where usuario_id_usuario = '$_GET[id_usuario]'";
                  $res=mysqli_query($db, $sql);
                  erro_sql("Select Unidade Has Usu�rio Escolhido", $db, "");
                  while($unidades_selecionadas=mysqli_fetch_object($res)){
                    $lista[][]=$unidades_selecionadas->unidade_id_unidade;
                    $lista[][]=$unidades_selecionadas->perfil_id_perfil;
                  }
                  if(mysqli_num_rows($res)>0){
                    $_SESSION["LISTA"]=$lista;
                  }
                }
              }
            }
          }
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P�GINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>

    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="JavaScript" type="text/JavaScript">
<?php
      require "../../scripts/frame.js";
?>

    <!--
      function enviar_ok()  // type=submit
      {
        if (document.cadastro.unidade.value == ""){
          alert ("Favor preencher os campos obrigat�rios!");
          document.cadastro.unidade.focus();
          return false;
        }
        document.cadastro.flag.value='t';
        return true;   // envia formulario
      }

      function enviar_salvar()  // type=submit
      {
        // consistencias
        if (document.cadastro.nome.value == ""){
          alert ("Favor preencher os campos obrigat�rios!");
          document.cadastro.nome.focus();
          return false;
        }
        if (document.cadastro.matricula.value == ""){
          alert ("Favor preencher os campos obrigat�rios!");
          document.cadastro.matricula.focus();
          return false;
        }
        if (document.cadastro.login.value == ""){
          alert ("Favor preencher os campos obrigat�rios!");
          document.cadastro.login.focus();
          return false;
        }
        if (document.cadastro.senha.value != ""){
          var senha = document.cadastro.senha.value;
          if (senha.length < 6){
            alert ("A senha deve ter no m�nimo 6 d�gitos!");
            document.cadastro.senha.focus();
            return false;
          }
        }
        if (document.cadastro.senha.value!="" && document.cadastro.confirmasenha.value == ""){
          alert ("Favor preencher os campos obrigat�rios!");
          document.cadastro.confirmasenha.focus();
          return false;
        }
        if (document.cadastro.senha.value != document.cadastro.confirmasenha.value){
          alert ("Senha n�o confere!");
          document.cadastro.confirmasenha.focus();
          return false;
        }
        if (document.cadastro.situacao.value == ""){
          alert ("Favor preencher os campos obrigat�rios!");
          document.cadastro.situacao.focus();
          return false;
        }
        var achou=0;
        for (var i=0;i<document.cadastro.elements.length;i++){
          var x = document.cadastro.elements[i];
          if (x.name == 'perfil[]'){
            achou = 1;
          }
        }
        if (achou==0 && document.cadastro.situacao.value=='A'){
          alert ("� necess�rio informar pelo menos uma unidade!");
          document.cadastro.unidade.focus();
          return false;
        }
        var acho=0;
        for (var i=0;i<document.cadastro.elements.length;i++){
          var x = document.cadastro.elements[i];
          if (x.name == 'perfil[]'){
            if (x.value==""){
              acho=1;
            }
          }
        }
        if(acho==1){
          alert ("� necess�rio informar o perfil do usuario!");
          return false;
        }
        document.cadastro.salvar_cadastro.value='t';
        return true;   // envia formulario
      }
    //-->
    </script>

    <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
      <tr><td> <?php echo $caminho;?> </td></tr>
    </table>

    <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
      <tr height="5%">
        <td>
          <table width="100%" class="titulo_tabela">
            <tr><td align="center"> <?php echo $nome_aplicacao;?>: Alterar </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="1" height="100%">
            <form name="cadastro" action="./usuario_edicao.php" method="POST" enctype="application/x-www-form-urlencoded">
              <tr>
                <td align="left" width="30%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Nome
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <input type="text" name="nome" size="50" maxlength="60" value="<?php if(isset($_GET[nome])){echo $_GET[nome];}else{if(isset($_POST[nome])){echo $_POST[nome];}}?>">
                </td>
              </tr>
              <tr>
                <td align="left" width="30%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Matr�cula
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <input type="text" name="matricula" size="50" maxlength="10" value="<?php if(isset($_GET[matricula])){echo $_GET[matricula];}else{if(isset($_POST[matricula])){echo $_POST[matricula];}}?>" onKeyPress="return isNumberKey(event);">
                </td>
              </tr>
              <tr>
                <td align="left" width="30%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Login
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <input type="text" name="login" size="50" maxlength="60" value="<?php if(isset($_GET[login])){echo $_GET[login];}else{if(isset($_POST[login])){echo $_POST[login];}}?>" onblur="if(this.value!='' && this.value.toUpperCase()!=document.cadastro.login_descricao.value){document.cadastro.novo_login.value='t';document.cadastro.submit();}">
                </td>
              </tr>
              <tr>
                <td align="left" width="30%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Senha
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <input type="password" name="senha" size="50" maxlength="12" value="<?php if (isset($_POST[senha])){echo $_POST[senha];}?>" onKeyPress="return isCharAndNumKey(event);"> (apenas numeros ou letras)
                </td>
              </tr>
              <tr>
                <td align="left" width="30%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Confirma Senha
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <input type="password" name="confirmasenha" size="50" maxlength="12" value="<?php if (isset($_POST[confirmasenha])){echo $_POST[confirmasenha];}?>" onKeyPress="return isCharAndNumKey(event);"> (apenas numeros ou letras)
                </td>
              </tr>
              <tr>
                <td align="left" width="30%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Situa��o
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <select size="1" name="situacao" style="width:200px;">
                    <option value="" >Selecione uma Situa��o</option>
                    <option value="A" <?php if($_GET[situacao]=="A"){echo "selected";}else{if($_POST[situacao]=="A"){echo "selected";}}?>>Ativo</option>
                    <option value="I"<?php if($_GET[situacao]=="I"){echo "selected";}else{if($_POST[situacao]=="I"){echo "selected";}}?>>Inativo</option>
                  </select>
                </td>
              </tr>
              <TR>
		        <TD colspan="2"></TD>
              </TR>
              <TR valign="top">
			    <TD colspan="2">
     	          <table width="100%" class="titulo_tabela" cellpadding="0" cellspacing="1">
		            <TR align="center">
				      <TD>Unidades</TD>
				      <TD width="10"><A href="javascript:showFrame('unidade');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informa��es de Unidade"></A></TD>
				    </TR>
	              </TABLE>
                </TD>
		      </TR>
			  <TR>
			    <TD colspan="2">
			      <div id="unidade" style="display:'';">
			        <table border="0" width="100%" cellpadding="0" cellspacing="0">
         	          <TR>
                        <TD colspan="2">
                          <TABLE width="100%" cellpadding="0" cellspacing="0">
                            <TR>
			                  <TD align="left" width="30%" class="descricao_campo_tabela">
                                <img src="<? echo URL."/imagens/obrigat.gif";?>">Unidades
                              </TD>
    	                      <td align="left" width="30%" class="campo_tabela">
                                <select size="1" name="unidade" style="width:200px;">
                                  <option value="">Selecione uma Unidade</option>
                                  <?php
                                    $sql = "select id_unidade, nome from unidade where id_unidade in $lista_unidades order by nome";
                                    $unidade = mysqli_query($db, $sql);
                                    erro_sql("Select Unidades", $db, "");
                                    while ($listaunidade = mysqli_fetch_object($unidade)){
                                  ?>
                                      <option value="<?php echo $listaunidade-> id_unidade;?>"><?php echo $listaunidade-> nome;?></option>
                                  <?
                                    }
                                  ?>
                                </select>
                                <input style="font-size: 12px;" type="submit" name="ok" value=" OK " onclick="return enviar_ok()" >
                              </td>
                              <?php
                                if(count($_SESSION["LISTA"])>0){
                                  $lista_aux=$_SESSION["LISTA"];
                                  $index=0;
                                  $unidades_aux="";
                                  foreach($lista_aux as $linha){
                                    foreach($linha as $coluna){
                                      if($index==0){
                                        $unidades_aux.=$coluna . ",";
                                      }
                                      if($index==(QTDE_COLUNA-5)){
                                        $index=0;
                                      }
                                      else{
                                        $index++;
                                      }
                                    }
                                  }
                                  $sql="select id_unidade from unidade where id_unidade in $lista_unidades and ";
                                  $sql.="id_unidade not in (" . substr($unidades_aux, 0, strlen($unidades_aux)-1) .  ") ";
                                  $sql.="order by nome";
                                  $res=mysqli_query($db, $sql);
                                  erro_sql("Select Unidades - Lista", $db, "");
                                  if(mysqli_num_rows($res)>0){
                                    $todas_unidades="";
                                  }
                                  else{
                                    $todas_unidades="sim";
                                  }
                                }
                              ?>
	     	                  <td align="center" width="30%" bgcolor="#D8DDE3" colspan="2">
                                <input style="font-size: 12px;" type="submit" name="todas" value="Todas >>" onclick="document.cadastro.all.value='t';" <?php if($todas_unidades=="sim"){echo "disabled";}?>>
                              </td>
                            </TR>
    		              </TABLE>
                        </TD>
                      </TR>
                      <tr>
				        <td colspan="2">
				          <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr class="coluna_tabela">
				              <td width="20%" align="center">Sigla</td>
				              <td width="40%" align="center">Unidade Associada</td>
				              <td width="35%" align="center">Perfil</td>
				              <td width="5%" align="center"></td>
				            </tr>
                            <?php
                              $cor_linha = "#CCCCCC";
                              if(count($_SESSION["LISTA"])>0){
                                $index=0;
                                $info="";
                                $lista=$_SESSION["LISTA"];
                                foreach($lista as $linha){
                                  foreach($linha as $coluna){
                                    if($index<(QTDE_COLUNA-5)){
                                      $info.=$coluna . "|";
                                    }
                                    else{
                                      $info.=$coluna;
                                    }
                                    if($index==(QTDE_COLUNA-5)){
                                      $sql="select id_perfil, descricao from perfil where status_2='A' order by descricao";
                                      $result=mysqli_query($db, $sql);
                                      erro_sql("Select Sigla/Unidade Associada/Perfil - Perfil", $db, "");
                                      $valores=split("[|]", $info);
                                      $sql="select sigla, nome from unidade where id_unidade='$valores[0]'";
                                      $res=mysqli_query($db, $sql);
                                      erro_sql("Select Sigla/Unidade Associada/Perfil - Unidade Associada", $db, "");
                                      if(mysqli_num_rows($res)>0){
                                        $unidade_info=mysqli_fetch_object($res);
                                      }
                            ?>
                                      <tr class='linha_tabela' bgcolor="<?php echo $cor_linha;?>" onMouseOver="this.bgColor='#D9ECFF';" onMouseOut="this.bgColor='<?php echo $cor_linha; ?>';">
                                        <td align="left"> <?php echo $unidade_info->sigla;?> </td>
                                        <td align="left"> <?php echo $unidade_info->nome;?> </td>
                                        <td align="center">
                                          <select name="perfil[]" size="1">
                                            <option value=""> Selecione um Perfil </option>
                                            <?php
                                              while($perfil_info=mysqli_fetch_object($result)){
                                            ?>
                                                <option value="<?php echo $perfil_info->id_perfil;?>" <?if($perfil_info->id_perfil==$valores[1]){echo "selected";}?>> <?php echo $perfil_info->descricao;?> </option>
                                            <?php
                                              }
                                            ?>
                                          </select>
                                        </td>
                                        <td align="center">
                                          <a onclick='document.cadastro.unidade_atual.value=<?php echo $valores[0];?>;document.cadastro.submit();'><img src="<?php echo URL;?>/imagens/trash.gif" border="0" title="Remover Registro"></a>
                                        </td>
                                      </tr>
                            <?php
                                      $index=0;
                                      $info="";
                                      if ($cor_linha == "#CCCCCC")
                                      {
                                        $cor_linha = "#EEEEEE";
                                      }
                                      else
                                      {
                                        $cor_linha = "#CCCCCC";
                                      }
                                    }
                                    else{
                                      $index++;
                                    }
                                  }
                                }
                              }
                            ?>
			              </table>
			            </td>
			          </tr>
			        </table>
                  </div>
		        </TD>
              </TR>
              <tr>
                <td colspan="2" width="100%" height="100%"></td>
              </tr>
              <tr>
                <td colspan="2" align="right" class="descricao_campo_tabela" height="35">
                  <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/usuario/usuario_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
                  <input style="font-size: 12px;" type="button" name="btAtualizarUnidadePerfil" value="Salvar >>" onclick="if(enviar_salvar()){document.cadastro.submit();}">
                  <input type="hidden" name="flag" value="f">
                  <input type="hidden" name="unidade_atual" value="">
                  <input type="hidden" name="all" value="f">
                  <input type="hidden" name="salvar_cadastro" value="f">
                  <input type="hidden" name="id_usuario_atual" value="<?php if(isset($_POST[id_usuario_atual])){echo $_POST[id_usuario_atual];}else{echo $_GET[id_usuario];}?>">
                  <input type="hidden" name="novo_login" value="f">
                  <?php
                    if(isset($_GET[id_usuario]) || isset($_POST[id_usuario_atual])){
                      if(isset($_GET[id_usuario])){
                        $sql="select login from usuario where id_usuario='$_GET[id_usuario]'";
                      }
                      else{
                        $sql="select login from usuario where id_usuario='$_POST[id_usuario_atual]'";
                      }
                      $res=mysqli_query($db, $sql);
                      erro_sql("Select Usu�rio Atual", $db, "");
                      if(mysqli_num_rows($res)>0){
                        $login_descricao=mysqli_fetch_object($res);
                      }
                    }
                  ?>
                  <input type="hidden" name="login_descricao" value="<?php if(isset($_GET[id_usuario]) || isset($_POST[id_usuario_atual])){echo $login_descricao->login;}?>">
                </td>
              </tr>
              <tr>
                <td colspan="2" class="descricao_campo_tabela" height="21">
                  <table align="center" border="0">
				    <tr valign="top" class="descricao_campo_tabela">
		              <td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigat�rios</td>
	                  <td>&nbsp&nbsp&nbsp</td>
                      <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos n�o Obrigat�rios</td>
	                </tr>
	              </table>
                </td>
	          </tr>
	          <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET[pesquisa]?>">
	        </form>
          </table>
        </td>
      </tr>
    </table>

<?php
    ////////////////////
    //RODAP� DA P�GINA//
    ////////////////////

    require DIR."/footer.php";

    if($_GET[v]=='f'){echo "<script>window.alert('Unidade j� existe na lista!');</script>";}

    if($_GET[l]=='f'){echo "<script>window.alert('Login j� cadastrado!');document.cadastro.login.focus();</script>";}

    if($_GET[s]=='f'){echo "<script>window.alert('N�o � poss�vel inativar o usu�rio, pois existe unidade configurada para esse usu�rio!');</script>";}
  }
  ////////////////////////////////////////////
  //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
