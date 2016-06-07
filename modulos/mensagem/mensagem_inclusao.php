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
  //  Arquivo..: mensagem_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 04/05/2009
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela inicial do módulo de mensagem
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
      exit();
    }

    if(isset($_POST[mensagem]) || isset($_FILES[imagem][name])){
      $atualizacao="";
      $data_in=split("[/]", $_POST[data_inicio]);
      $data_fn=split("[/]", $_POST[data_fim]);
      $_POST[data_inicio]=$data_in[2] . "-" . $data_in[1] . "-" . $data_in[0];
      $_POST[data_fim]=$data_fn[2] . "-" . $data_fn[1] . "-" . $data_fn[0];
      if($_POST[exibir]=="S"){
        $nome_imagem=$_FILES[imagem][name];
        $imagem=$_FILES[imagem][tmp_name];
        if($imagem==""){
          $imgdata="";
        }
        else{
          require "../../config/conexaoFtp.php";
          if(!ftp_put($connFtp, $nome_imagem, $imagem, FTP_BINARY)){
            exit("Erro ao transferir arquivo!");
          }
          $filename="ftp://$usuario:$senha@$server/$nome_imagem";
          $hndl=fopen($filename,"r");
          ftp_close($connFtp);
          $imgdata = stream_get_contents($hndl);
          $imgdata=addslashes($imgdata);
          fclose($hndl);
        }
        $sql="insert into mensagem (mensagem,
                                    data_inicio,
                                    data_fim,
                                    usua_incl,
                                    data_incl,
                                    status_2,
                                    imagem)
              values ('$_POST[mensagem]',
                      '$_POST[data_inicio]',
                      '$_POST[data_fim]',
                      '$_SESSION[id_usuario_sistema]',
                      '" . date("Y-m-d") . "',
                      '$_POST[situacao]',
                      '$imgdata')";
      }
      else{
        $sql="insert into mensagem (mensagem,
                                    data_inicio,
                                    data_fim,
                                    usua_incl,
                                    data_incl,
                                    status_2)
              values ('$_POST[mensagem]',
                      '$_POST[data_inicio]',
                      '$_POST[data_fim]',
                      '$_SESSION[id_usuario_sistema]',
                      '" . date("Y-m-d") . "',
                      '$_POST[situacao]')";
      }
      mysqli_query($db, $sql);
      erro_sql("Insert Mensagem", $db, "");
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      if($atualizacao==""){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/mensagem/mensagem_inicial.php?i=t");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/mensagem/mensagem_inicial.php?i=f");
      }
      exit;
    }
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var data_inicio=x.data_inicio;
        var data_fim=x.data_fim;
        if(data_inicio.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          data_inicio.focus();
          data_inicio.select();
          return false;
        }
        if(data_fim.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          data_fim.focus();
          data_fim.select();
          return false;
        }
        var data_inicial=x.data_inicio.value.split("/");
        var data_final=x.data_fim.value.split("/");
        if(data_final[2]<data_inicial[2]){
          window.alert("Data Fim deve ser maior ou igual a Data Início!");
          x.data_fim.focus();
          return false;
        }
        else{
          if(data_final[2]==data_inicial[2]){
            if(data_final[1]<data_inicial[1]){
              window.alert("Data Fim deve ser maior ou igual a Data Início!");
              x.data_fim.focus();
              return false;
            }
            else{
              if(data_final[1]==data_inicial[1] && data_final[0]<data_inicial[0]){
                window.alert("Data Fim deve ser maior ou igual a Data Início!");
                x.data_fim.focus();
                return false;
              }
            }
          }
        }

        if(x.data_inicio.value!="" && !verificaData(x.data_inicio, x.data_inicio.value)){
          return false;
        }
        if(x.data_fim.value!="" && !verificaData(x.data_fim, x.data_fim.value)){
          return false;
        }
        if(x.mensagem.value=="" && x.imagem.value==""){
          window.alert("Favor preencher campo Mensagem ou Imagem!");
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
            <tr><td> <?php echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./mensagem_inclusao.php" method="POST" enctype="multipart/form-data">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" readonly>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Data Início
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                        <input type="text" name="data_inicio" size="15" style="width: 80px" onKeyPress="return mascara_data(event,this);" value="<?php echo date("d/m/Y");?>">
                      </td>

                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>">Data Fim
                      </td>
                      <td align="left" width="30%" class="campo_tabela">
                        <input type="text" name="data_fim" size="15" style="width: 80px" onKeyPress="return mascara_data(event,this);" value="<?php echo date("d/m/Y");?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Mensagem
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <textarea name="mensagem" row="2" cols="31" style="width: 500px; height: 200px"></textarea>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="30%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Exibir Imagem
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="radio" name="exibir" value="N" checked onclick="document.form_inclusao.imagem.disabled=true;"> Não
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" name="exibir" value="S" onclick="document.form_inclusao.imagem.disabled=false;"> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="file" name="imagem" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td align="left" width="30%" class="descricao_campo_tabela">
                          <img src="<? echo URL."/imagens/obrigat.gif";?>"> Situação
                      </td>
                      <td align="left" width="30%" class="campo_tabela" colspan="3">
                          <input type="radio" name="situacao" value="A"> Ativo
                          &nbsp; &nbsp; &nbsp; &nbsp;
                          <input type="radio" name="situacao" value="I" checked> Inativo
                      </td>
                    </tr>

                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/mensagem/mensagem_inicial.php'">
                        <input type="submit" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="return validarCampos();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                        </font>
                      </td>
                    </tr>
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>

    <script language='javascript'>
    <!--
      document.form_inclusao.data_inicio.focus();
    //-->
    </script>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
