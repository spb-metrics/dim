<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
  header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");                          // HTTP/1.0

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: pesquisa_material.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Fun��o...: Tela de pesquisa de material
  //////////////////////////////////////////////////////////////////

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICA��O DE SEGURAN�A//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    if(isset($_POST[pesquisar]) && $_POST[pesquisar]!=""){
      $sql="select m.codigo_material, m.descricao, u.unidade, m.id_material ";
      $sql.="from material as m, unidade_material as u ";
      $sql.="where m.unidade_material_id_unidade_material=u.id_unidade_material and m.status_2='A' and m.descricao like '%" . trim($_POST[pesquisar]) . "%'";
      $sql.=" order by m.descricao";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Pesquisa", $db, "");

      if ($_POST[pesquisar]!="")
      {
        if(mysqli_num_rows($res)==0){
          $pesq="f";
        }
      }
    }
  }
?>

<html>
  <head><title> Sele��o de Material </title></head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<script language="javascript">
  <!--
  function preencheCampos(id, descr, unid){
    window.opener.document.form_inclusao.codigo.value = id;
	window.opener.document.form_inclusao.descricao.value = descr;
	window.close();
  }
  //-->
</script>
<body onload="document.form_pesquisa.pesquisar.focus();">
  <table border="1" cellspacing="0" cellpadding="0" width="100%" height="100%">
    <form name="form_pesquisa" action="pesquisa_material.php" method="POST" enctype="application/x-www-form-urlencoded">
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
            <tr class="titulo_tabela">
              <td colspan="5" valign="middle" align="center" width="100%" height="21"> Pesquisar Material </td>
            </tr>
            <tr class="opcao_tabela">
              <td align="center" width="100%">Material: <input type="text" name="pesquisar" size="20" style="width: 200px" maxlenght="50" >
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="salvar" value=" Pesquisar "></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table border="0" cellspacing="1" cellpadding="0"  width="100%">
            <tr class="coluna_tabela">
              <td align="center" width="15%"> C�digo </td>
              <td align="center" width="60%"> Material </td>
              <td align="center" width="15%"> Sele��o </td>
            </tr>
<?php
            $cor_linha = "#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINI��ES DE CADA LINHA//
            ///////////////////////////////////////
            if(isset($_POST[pesquisar]) && $_POST[pesquisar]!="")
            {
              while ($consulta = mysqli_fetch_object($res))
              {
?>
                <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                  <td><?php echo $consulta->codigo_material;?></td>
                  <td><?php echo $consulta->descricao;?></td>
                  <td align="center"><input type="radio" name="selecao" onclick="preencheCampos('<?php echo $consulta->id_material;?>', '<?php echo $consulta->descricao;?>', '<?php echo $consulta->unidade;?>');window.close();"></td>
                </tr>
<?php
                ////////////////////////
                //MUDANDO COR DA LINHA//
                ////////////////////////
                if ($cor_linha == "#EEEEEE")
                {
                  $cor_linha = "#CCCCCC";
                }
                else
                {
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
            <tr class="campo_botao_tabela" align="center">
              <td><input type="button" name="fechar" value="Fechar" onclick="window.close();"></td>
            </tr>
          </table>
        </td>
      </tr>
    </form>
  </table>
</body>
<?php
    if($pesq=='f'){echo "<script>window.alert('N�o foi encontrado dados para a pesquisa!')</script>";}
?>
