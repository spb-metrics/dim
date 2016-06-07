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
  if (file_exists("../config/config.inc.php"))
  {
    require "../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require "../verifica_acesso.php";

    $_SESSION[APLICACAO]=$_GET[aplicacao];

    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
    ?>
<script language="javascript">
 function importar_prof()
 {
  document.imp_prof.salvar.disabled = true;
  document.imp_prof.submit();
 }
</script>

    <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
            <tr><td> <?php echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='1' width='100%' >
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                   <form action="upload.php" method="post" name="imp_prof" enctype="multipart/form-data">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <?php echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr class="opcao_tabela">
                       <td valign="middle" width="70%">
                          Arquivo de Profissionais
                          <?php
                            if($inclusao_perfil!=""){
                          ?>
                            <input type="file" name="arquivo" style="width: 400px" >
                          <?php
                            }
                            else{
                          ?>
                            <input type="file" name="arquivo" style="width: 400px" disabled>
                          <?php
                            }
                          ?>
                       </td>
                       <td  valign="right" align="center" width="70%">
                          <?php
                            if($inclusao_perfil!=""){
                          ?>
                            <input type="button" value="Importar" name="salvar" onClick="importar_prof()">
                          <?php
                            }
                            else{
                          ?>
                            <input type="submit" value="Importar" disabled>
                          <?php
                            }
                          ?>
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

<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
