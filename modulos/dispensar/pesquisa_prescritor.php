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

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    if(isset($_POST[pesquisar]) && $_POST[pesquisar]!=""){
      $sql="select
                  p.inscricao, p.nome, p.id_profissional, p.tipo_prescritor_id_tipo_prescritor,
                  e.uf
            from
                  profissional p,
                  estado e
            where
                  p.status_2='A'
                  and p.nome like '$_POST[pesquisar]%'
                  and p.estado_id_estado = e.id_estado";
      $res=mysqli_query($db, $sql);
    }

    if ($_POST[pesquisar]!="")
    {
      if(mysqli_num_rows($res)==0){
        $pesq="f";
      }
    }
  }
?>

<html>
  <head><title> Sele��o de Prescritor </title></head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<head>
 <BASE target="_self">
</head>
<script language="javascript">

function preencheCampos(id, tipo, insc, nome, uf)
{
    var args = id+'|'+tipo+'|'+insc+'|'+nome+'/'+uf;
	if (window.showModalDialog)
	{
		var _R = new Object()
        _R.strArgs=args;
		window.returnValue=_R;
	}
	else
	{
		if (window.opener.SetNamePrescritor)
		{
			window.opener.SetNamePrescritor(args);
		}
	}
	window.close();
}
</script>

<body>
  <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
    <form name="form_pesquisa" action="pesquisa_prescritor.php" method="POST" enctype="application/x-www-form-urlencoded">
      <tr>
        <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
            <tr class="titulo_tabela">
              <td colspan="5" valign="middle" align="center" width="100%" height="21"> Pesquisar Prescritor </td>
            </tr>
            <tr class="opcao_tabela">
              <td align="center" width="100%">Prescritor: <input type="text" name="pesquisar" size="20" style="width: 200px" maxlenght="50" >
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="salvar" value=" Pesquisar "></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table border="0" cellspacing="1" cellpadding="0"  width="100%">
            <tr class="coluna_tabela" height="21">
              <td align="center" width="20%"> Inscri��o </td>
              <td align="center" width="70%"> Prescritor </td>
              <td align="center" width="10%"> Sele��o </td>
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
                <td align="center"><?php echo $consulta->inscricao;?></td>
                <td align="left"><?php echo $consulta->nome;?></td>
                <td align="center"><input type="radio" name="selecao" onclick="preencheCampos('<?php echo $consulta->id_profissional;?>','<?php echo $consulta->tipo_prescritor_id_tipo_prescritor?>','<?php echo $consulta->inscricao;?>','<?php echo $consulta->nome;?>','<?php echo $consulta->uf;?>');"></td>
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
    </form>
  </table>
</body>
<?php
    if(isset($pesq)=='f'){echo "<script>window.alert('N�o foi encontrado dados para a pesquisa!')</script>";}
?>
