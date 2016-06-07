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

    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
    }

    //verificando se unidade é distrito
    $sql="select flg_nivel_superior from unidade where id_unidade = '$_SESSION[id_unidade_sistema]' and status_2='A'";

    $res=mysqli_query($db, $sql);
    erro_sql("Select Distrito", $db, "");
    $nivelsuperior    = mysqli_fetch_object($res);

    $lista_unidade="";
    if ($nivelsuperior->flg_nivel_superior=='1'){
      $sql="select id_unidade from unidade where unidade_id_unidade = '$_SESSION[id_unidade_sistema]' and status_2='A'";
      $result = mysqli_query($db, $sql);
      erro_sql("Select Nível Superior", $db, "");
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
      erro_sql("Select Usuário Existente", $db, "");
      if(mysqli_num_rows($res)>0){
        header("Location: ". URL."/modulos/usuario/usuario_cadastro.php?l=f&nome=$_POST[nome]&matricula=$_POST[matricula]");
      }
    }
    else{
      //essa parte acontece qdo pressiona o botao salvar
      if($_POST[salvar_cadastro]=="t"){
        $perfil_aux="";
		
		//echo "perfil".$perfil;
		//exit;
		$perfil = $_POST['perfil'];
		//print_r "perfil".$perfil;
		
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
        $data=date("Y-m-d H:m:s");
        $sql="insert into usuario ";
        $sql.="(nome, matricula, login, senha, situacao, data_incl, usua_incl)";
        $sql.="values ('" . strtoupper($_POST[nome]) . "', '$_POST[matricula]', '" . strtoupper($_POST[login]) . "', OLD_PASSWORD('$_POST[senha]'), '$_POST[situacao]', '$data', '$_SESSION[id_usuario_sistema]')";
       
//echo $sql;
//exit;

	   mysqli_query($db, $sql);
        erro_sql("Insert Usuário", $db, "");
        $atualizacao="";
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
        $sql="select id_usuario from usuario where nome='$_POST[nome]' and matricula='$_POST[matricula]' ";
        $sql.="and login='$_POST[login]' and situacao='$_POST[situacao]' and senha=OLD_PASSWORD('$_POST[senha]') ";
        $sql.="and data_incl='$data' and usua_incl='$_SESSION[id_usuario_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select ID Usuário", $db, "");
        if(mysqli_num_rows($res)>0){
          $id_usuario=mysqli_fetch_object($res);
        }
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
              $sql.="values ('$valores[0]', '$id_usuario->id_usuario', '$valores[1]')";
              mysqli_query($db, $sql);
              erro_sql("Insert Usuário Has Unidade", $db, "");
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
        if($atualizacao==""){
          mysqli_commit($db);
          header("Location: ". URL."/modulos/usuario/usuario_inicial.php?i=t");
        }
        else{
          mysqli_rollback($db);
          header("Location: ". URL."/modulos/usuario/usuario_inicial.php?i=f");
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
                  $aux[][]=$perfil_info[$posicao];
                }
                else{
                  $aux[][]=$coluna;
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
            $todas_unidades="";
            $sql="select id_unidade from unidade where id_unidade in $lista_unidades and ";
            $sql.="id_unidade not in (" . substr($unidades_aux, 0, strlen($unidades_aux)-1) .  ") ";
            $sql.="order by nome";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Unidade - All, Count>0", $db, "");
            while($todas_unidades=mysqli_fetch_object($res)){
              $aux[][]=$todas_unidades->id_unidade;
              $aux[][]="";
            }
          }
          else{
            $sql="select id_unidade from unidade where id_unidade in $lista_unidades order by nome";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Unidade - All, Count<=0", $db, "");
            while($todas_unidades=mysqli_fetch_object($res)){
              $aux[][]=$todas_unidades->id_unidade;
              $aux[][]="";
            }
          }
          $_SESSION["LISTA"]=$aux;
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
                    $aux[][]=$valores[0];
                    $aux[][]=$valores[1];
                  }
                  $index=0;
                  $info="";
                }
                else{
                  $index++;
                }
              }
            }
            if(count($aux)<=0){
              session_unregister("LISTA");
            }
            else{
              $_SESSION["LISTA"]=$aux;
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
                      $aux[][]=$perfil_info[$posicao];
                    }
                    else{
                      $aux[][]=$coluna;
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
                $_SESSION["LISTA"]=$aux;
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
                  header("Location: ". URL."/modulos/usuario/usuario_cadastro.php?v=f&nome=$_POST[nome]&matricula=$_POST[matricula]&login=$_POST[login]&situacao=$_POST[situacao]");
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
          }
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
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
          alert ("Favor preencher os campos obrigatórios!");
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
          alert ("Favor preencher os campos obrigatórios!");
          document.cadastro.nome.focus();
          return false;
        }
        if (document.cadastro.matricula.value == ""){
          alert ("Favor preencher os campos obrigatórios!");
          document.cadastro.matricula.focus();
          return false;
        }
        if (document.cadastro.login.value == ""){
          alert ("Favor preencher os campos obrigatórios!");
          document.cadastro.login.focus();
          return false;
        }
        if (document.cadastro.senha.value == ""){
          alert ("Favor preencher os campos obrigatórios!");
          document.cadastro.senha.focus();
          return false;
        }
        else{
          var senha = document.cadastro.senha.value;
          if (senha.length < 6){
            alert ("A senha deve ter no mínimo 6 dígitos!");
            document.cadastro.senha.focus();
            return false;
          }
        }
        if (document.cadastro.confirmasenha.value == ""){
          alert ("Favor preencher os campos obrigatórios!");
          document.cadastro.confirmasenha.focus();
          return false;
        }
        if (document.cadastro.senha.value != document.cadastro.confirmasenha.value){
          alert ("Senha não confere!");
          document.cadastro.confirmasenha.focus();
          return false;
        }
        if (document.cadastro.situacao.value == ""){
          alert ("Favor preencher os campos obrigatórios!");
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
        if (achou==0){
          alert ("É necessário informar pelo menos uma unidade!");
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
          alert ("É necessário informar o perfil do usuario!");
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
            <tr><td align="center"> <?php echo $nome_aplicacao;?>: Incluir</td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="1" height="100%">
            <form name="cadastro" action="./usuario_cadastro.php" method="POST" enctype="application/x-www-form-urlencoded">
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
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Matrícula
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
                  <input type="text" name="login" size="50" maxlength="60" value="<?php if(isset($_GET[login])){echo $_GET[login];}else{if(isset($_POST[login])){echo $_POST[login];}}?>" onblur="if(this.value!=''){document.cadastro.novo_login.value='t';document.cadastro.submit();}">
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
                  <img src="<? echo URL."/imagens/obrigat.gif";?>">Situação
                </td>
                <td align="left" width="70%" class="campo_tabela">
                  <select size="1" name="situacao" style="width:200px;">
                    <option value="" >Selecione uma Situação</option>
                    <option value="A" <?php if($_GET[situacao]=="A"){echo "selected";}else{if($_POST[situacao]=="A" or (!isset($_POST[situacao]))){echo "selected";}}?>>Ativo</option>
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
				      <TD width="10"><A href="javascript:showFrame('unidade');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Unidade"></A></TD>
				    </TR>
	              </TABLE>
                </TD>
		      </TR>
			  <TR>
			    <TD colspan="2">
			      <div id="unidade" style="display:'';">
			        <table border="0" width="100%" cellpadding="0" cellspacing="1">
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
                                  $sql="select sigla, nome from unidade where id_unidade in $lista_unidades and ";
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
                                      erro_sql("Select Silga/Unidade Associada/Perfil - Unidade Associada", $db, "");
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
                  <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/usuario/usuario_inicial.php'">
                  <input style="font-size: 12px;" type="button" name="btAtualizarUnidadePerfil" value="Salvar >>" onclick="if(enviar_salvar()){document.cadastro.submit();}">
                  <input type="hidden" name="flag" value="f">
                  <input type="hidden" name="unidade_atual" value="">
                  <input type="hidden" name="all" value="f">
                  <input type="hidden" name="salvar_cadastro" value="f">
                  <input type="hidden" name="novo_login" value="f">
                </td>
              </tr>
              <tr>
                <td colspan="2" class="descricao_campo_tabela" height="21">
                  <table align="center" border="0">
				    <tr valign="top" class="descricao_campo_tabela">
		              <td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
	                  <td>&nbsp&nbsp&nbsp</td>
                      <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
	                </tr>
	              </table>
                </td>
	          </tr>
	        </form>
          </table>
        </td>
      </tr>
    </table>
    <script language="javascript">
    <!--
      var x=document.cadastro;
      if(x.nome.value==""){
        x.nome.focus();
      }
      else{
        if(x.matricula.value==""){
          x.matricula.focus();
        }
        else{
          if(x.senha.value==""){
            x.senha.focus();
          }
          else{
            if(x.confirmasenha.value==""){
              x.confirmasenha.focus();
            }
            else{
              if(x.situacao.selectedIndex==0){
                x.situacao.focus();
              }
              else{
                x.unidade.focus();
              }
            }
          }
        }
      }
    //-->
    </script>

<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////

    require DIR."/footer.php";

    if($_GET[v]=='f'){echo "<script>window.alert('Unidade já existe na lista!');document.cadastro.senha.focus();</script>";}

    if($_GET[l]=='f'){echo "<script>window.alert('Login já cadastrado!');document.cadastro.login.focus();</script>";}
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
