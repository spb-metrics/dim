<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==""){
      header("Location: ". URL."/start.php");
      exit();
    }

    if(isset($_POST[pesquisar]) && $_POST[pesquisar]!=""){
      //obtem id da cidade de campinas
      $sql="select cidade_id_cidade from parametro";
      $result=mysqli_query($db, $sql);
      erro_sql("Select ID Cidade", $db, "");
      if(mysqli_num_rows($result)>0){
        $id_cidade_info=mysqli_fetch_object($result);
        $id_cidade=$id_cidade_info->cidade_id_cidade;
      }
      
      $sql = "select id_cidade, concat(c.nome,'/',e.uf) as nome
              from
                     cidade c,
                     estado e
              where
                     c.nome like '" . trim($_POST[pesquisar]) . "%'
                     and c.estado_id_estado = e.id_estado
              order by
                     nome";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Pesquisa", $db, "");

      if ($_POST[pesquisar]!=""){
        if(mysqli_num_rows($res)==0){
          $pesq="f";
        }
      }
    }
  }
?>

  <html>
    <head><title> Sele��o de Cidade </title></head>
    <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
  </html>
  <script language="javascript">
    <!--
    self.resizeTo(450,500);
    function preencheCampos(cod, nome, id, op){
      //chamou da tela de paciente inclusao
      if(op=="i"){
        window.opener.document.form_inclusao.cidade_receita.value=nome;
        window.opener.document.form_inclusao.id_cidade_receita.value=cod;
        if(cod==id){
          window.opener.document.form_inclusao.unidade_referida.disabled='';
        }
        else{
          window.opener.document.form_inclusao.unidade_referida.selectedIndex=0;
          window.opener.document.form_inclusao.unidade_referida.disabled='true';
        }
      }
      //chamou da tela de paciente alteracao
      if(op=="a"){
        window.opener.document.form_alteracao.cidade_receita.value=nome;
        window.opener.document.form_alteracao.id_cidade_receita.value=cod;
        if(cod==id){
          window.opener.document.form_alteracao.unidade_referida.disabled='';
        }
        else{
          window.opener.document.form_alteracao.unidade_referida.selectedIndex=0;
          window.opener.document.form_alteracao.unidade_referida.disabled='true';
        }
      }
      window.close();
    }
    //-->
  </script>
  <body onload="document.form_pesquisa.pesquisar.focus();">
    <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
      <form name="form_pesquisa" action="pesquisa_cidade.php" method="POST" enctype="application/x-www-form-urlencoded">
        <tr>
          <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
              <tr class="titulo_tabela">
                <td colspan="5" valign="middle" align="center" width="100%" height="21"> Pesquisar Cidade </td>
              </tr>
              <tr class="opcao_tabela">
                <td align="center" width="100%">Cidade: <input type="text" name="pesquisar" size="20" style="width: 200px" maxlenght="50" >
                &nbsp;&nbsp;&nbsp;<input type="submit" name="salvar" value=" Pesquisar "></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td height="100%" align="center" valign="top">
            <table border="0" cellspacing="1" cellpadding="0"  width="100%">
              <tr class="coluna_tabela" height="21">
                <td align="center" width="68%"> Cidade </td>
                <td align="center" width="12%"> Sele��o </td>
              </tr>
<?php
              $cor_linha = "#CCCCCC";
              ///////////////////////////////////////
              //INICIO DAS DEFINI��ES DE CADA LINHA//
              ///////////////////////////////////////
              if(isset($_POST[pesquisar]) && $_POST[pesquisar]!=""){
                while ($consulta = mysqli_fetch_object($res)){
?>
                  <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                    <td align="left"><?php echo $consulta->nome;?></td>
                    <td align="center"><input type="radio" name="selecao" onclick="preencheCampos('<?php echo $consulta->id_cidade;?>', '<?php echo $consulta->nome;?>', '<?php echo $id_cidade;?>', '<?php echo $_POST[opcao];?>');"></td>
                  </tr>

<?php
                  ////////////////////////
                  //MUDANDO COR DA LINHA//
                  ////////////////////////
                  if ($cor_linha == "#EEEEEE"){
                    $cor_linha = "#CCCCCC";
                  }
                  else{
                    $cor_linha = "#EEEEEE";
                  }
                }
              }
?>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
              <tr class="campo_botao_tabela" align="center" class="campo_botao_tabela">
                <td><input type="button" name="fechar" value="Fechar" onclick="window.close();"></td>
                <td>
                  <?php
                   if(isset($_POST[opcao])){
                     $valor=$_POST[opcao];
                   }
                   if(isset($_GET[opcao])){
                     $valor=$_GET[opcao];
                   }
                  ?>
                  <input type="hidden" name="opcao" value="<?php echo $valor;?>">
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </form>
    </table>
  </body>
<?php
  if(isset($pesq)=='f'){echo "<script>window.alert('N�o foi encontrado dados para a pesquisa!')</script>";}
?>
