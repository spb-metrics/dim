<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    if(isset($_POST[pesquisar]) && $_POST[pesquisar]!=""){
      $sql="select * from profissional where status_2='A' and nome like '$_POST[pesquisar]%'";

      $res=mysqli_query($db, $sql);

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
  <head><title> Seleção de Prescritor </title></head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<script language="javascript">
  <!--
  self.resizeTo(600,500);
  function preencheCampos(insc){
    window.opener.document.form_inclusao.inscricao.value = insc;
    window.opener.document.form_inclusao.submit();
	window.close();
  }
  //-->
</script>
<body>
  <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
    <form name="form_pesquisa" action="pesquisa_prescritor.php" method="POST" enctype="application/x-www-form-urlencoded">
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
            <tr class="opcao_tabela">
              <td align="center" width="100%">Pesquisar:<input type="text" name="pesquisar" size="20" style="width: 200px" maxlenght="50" ></td>
              <td><input type="submit" name="salvar" value="Ok"></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table border="0" cellspacing="1" cellpadding="0"  width="100%">
            <tr class="coluna_tabela">
              <td align="center" width="20%"> Inscrição </td>
              <td align="center" width="70%"> Prescritor </td>
              <td align="center" width="10%"> </td>
            </tr>
<?php
            $cor_linha = "#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
            ///////////////////////////////////////
            if(isset($_POST[pesquisar]) && $_POST[pesquisar]!="")
            {
            while ($consulta = mysqli_fetch_object($res))
            {
?>
              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                <td><?php echo $consulta->inscricao;?></td>
                <td><?php echo $consulta->nome;?></td>
                <td><input type="radio" name="selecao" onclick="preencheCampos('<?php echo $consulta->inscricao;?>');"></td>
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
            <tr class="campo_botao_tabela" align="center" class="campo_botao_tabela">
              <td><input type="button" name="fechar" value="Fechar" onclick="window.close();"></td>
            </tr>
          </table>
        </td>
      </tr>
    </form>
  </table>
</body>
<?php
    if(isset($pesq)=='f'){echo "<script>window.alert('Não foi encontrado dados para a pesquisa!')</script>";}
?>
