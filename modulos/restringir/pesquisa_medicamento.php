<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
  header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");                          // HTTP/1.0

  session_start();

  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    $pesq = "";
    if($_POST[flag] == "t")
    {
      $lista = $_SESSION["MATERIAIS"];
      foreach($selecao as $opcao)
      {
        $valores = split("[|]", $opcao);
        $lista[][] = $valores[0];
        $lista[][] = $valores[1];
        $lista[][] = $valores[2];
      }
      $_SESSION["MATERIAIS"] = $lista;
      echo "<script>window.opener.document.form_inclusao.flag.value='t';window.opener.document.form_inclusao.submit();window.close();</script>";
    }
    else
    {
      $lista = $_SESSION["MATERIAIS"];

      if(count($lista)>0)
      {
        $index=0;
        $info="";
        foreach($lista as $linha)
        {
          foreach($linha as $coluna)
          {
            if($index==0)
            {
              $info.=$coluna . ",";
            }
            if($index==(QTDE_COLUNA-4))
            {
              $index=0;
            }
            else
            {
              $index++;
            }
          }
        }
        $_SESSION["RESTRINGIDOS"] = $info;
        
        if(isset($_POST[pesquisar]) && $_POST[pesquisar]!="")
        {
          $sql = "select *
                  from material
                  where id_material not in (". substr($info, 0, strlen($info)-1) . ")
                        and descricao like '%" . trim($_POST[pesquisar]) . "%' order by descricao";

          $res=mysqli_query($db, $sql);
          erro_sql("Select Pesquisa Material", $db, "");

          if ($_POST[pesquisar]!="")
          {
            if(mysqli_num_rows($res)==0){
              $pesq="f";
            }
          }
        }
      }
      else
      {
        if(isset($_POST[pesquisar]) && $_POST[pesquisar]!="")
        {
          $sql = "select *
                  from material
                  where status_2='A'
                        and descricao like '%" . trim($_POST[pesquisar]) . "%'
                  order by descricao";


          $res=mysqli_query($db, $sql);
          erro_sql("Select Pesquisa Material com Count<=0", $db, "");

          if ($_POST[pesquisar]!="")
          {
            if(mysqli_num_rows($res)==0){
              $pesq="f";
            }
          }
        }
      }
    }
  }
?>

<html>
  <head><title> Seleção de Material </title></head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<body onload="document.form_pesquisa.pesquisar.focus();">
  <table border="1" width="100%">
    <form name="form_pesquisa" action="pesquisa_medicamento.php" method="POST" enctype="application/x-www-form-urlencoded">
      <tr>
        <table border="0" width="100%">
          <tr class="titulo_tabela">
              <td colspan="5" valign="middle" align="center" width="100%" height="21"> Pesquisar Material </td>
            </tr>
            <tr class="opcao_tabela">
              <td align="center" width="100%">Material: <input type="text" name="pesquisar" size="20" style="width: 200px" maxlenght="50" >
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="salvar" value=" Pesquisar "></td>
            </tr>
        </table>
      </tr>
      <tr>
        <table border="0" width="100%">
          <tr class="coluna_tabela">
            <td align="center" width="15%"> Código </td>
            <td align="center" width="70%"> Material </td>
            <td align="center" width="15%"> Seleção </td>
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
                <td align="center"><?php echo $consulta->codigo_material;?></td>
                <td align="left"><?php echo $consulta->descricao;?></td>
                <td align="center"><input type="checkbox" name="selecao[]" value="<?php echo $consulta->id_material . "|" . $consulta->codigo_material. "|" .$consulta->descricao;?>"</td>
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
      </tr>
      <tr>
        <table border="0" width="100%">
          <tr align="center" class="campo_botao_tabela">
            <td align="center" width="50%"><input type="button" name="fechar" value="Fechar" onclick="window.close();"</td>
            <td align="center" width="50%"><input type="button" name="salvar" value="  OK  " onclick="document.form_pesquisa.flag.value='t';document.form_pesquisa.submit();"></td>
            <input type="hidden" name="flag" value="f">
            <input type="hidden" name="prescritor_atual" value="<?php if(isset($_POST[prescritor])){echo $_POST[prescritor];}else{echo $_GET[prescritor];}?>">
          </tr>
        </table>
      </tr>
    </form>
  </table>
</body>
<?php
    if($pesq=='f'){echo "<script>window.alert('Não foi encontrado dados para a pesquisa!')</script>";}
?>
