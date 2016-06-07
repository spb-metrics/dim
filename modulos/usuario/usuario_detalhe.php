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
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
  if($_SESSION[id_usuario_sistema]==''){
    header("Location: ". URL."/start.php");
  }

  if($_GET[id_usuario]!=""){
    $sql_select = "select id_usuario, nome, matricula, login, situacao from usuario where id_usuario = '".$_GET[id_usuario]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Usuário Escolhido", $db, "");
    $usuario    = mysqli_fetch_object($res);

    $id_usuario          = $usuario->id_usuario;
    $nome                = $usuario->nome;
    $matricula           = $usuario->matricula;
    $login               = $usuario->login;
    $situacao            = $usuario->situacao;
  }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";

  require DIR."/buscar_aplic.php";
?>

  <script language="JavaScript" type="text/JavaScript">
  <?php
    require "../../scripts/frame.js"; ?>
  </script>
     <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
    <tr><td> <?php echo $caminho;?> </td></tr>
  </table>

  <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
    <tr height="5%">
      <td>
        <table width="100%" class="titulo_tabela">
          <tr><td align="center"> <?php echo $nome_aplicacao;?>: Detalhar</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="1">
          <tr>
            <td align="left" width="30%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Nome
            </td>
            <td align="left" width="70%" class="campo_tabela">
              <input type="text" name="nome" id="nome" size="50" maxlength="60" <?php if (isset($nome)){echo "value='".$nome."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" width="30%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Matrícula
            </td>
            <td align="left" width="70%" class="campo_tabela">
              <input type="text" name="matricula" id="matricula" size="50" maxlength="10" <?php if (isset($matricula)){echo "value='".$matricula."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" width="30%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Login
            </td>
            <td align="left" width="70%" class="campo_tabela">
              <input type="text" name="login" id="login" size="50" maxlength="60" <?php if (isset($login)){echo "value='".$login."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" width="30%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Senha
            </td>
            <td align="left" width="70%" class="campo_tabela">
              <input type="password" name="senha" id="senha" size="50" maxlength="12" disabled>
            </td>
          </tr>
          <tr>
            <td align="left" width="30%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Confirma Senha
            </td>
            <td align="left" width="70%" class="campo_tabela">
              <input type="password" name="confirmasenha" id="confirmasenha" size="50" maxlength="12" disabled>
            </td>
          </tr>
          <tr>
            <td align="left" width="30%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Situação
            </td>
            <td align="left" width="70%" class="descricao_campo_tabela">
              <select size="1" name="situacao"  style="width:200px;" disabled>
                <option value="" >Selecione Situação</option>
                <option value="A" <?php if($situacao=="A"){echo "selected";}?>>Ativo</option>
                <option value="I"<?php if($situacao=="I"){echo "selected";}?>>Inativo</option>
              </select>
            </td>
          </tr>
          <TR>
            <TD colspan="2"></TD>
          </TR>
          <TR valign="top" >
            <TD colspan="2">
              <table width="100%" cellpadding="0" cellspacing="0">
		        <TR align="center">
		          <TD class="titulo_tabela">Unidades</TD>
		          <TD class="titulo_tabela" width="10"><A href="javascript:showFrame('unidade');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Unidade"></A></TD>
                </TR>
		      </TABLE>
            </TD>
          </TR>
          <TR>
            <TD colspan="2">
			  <div id="unidade" style="display:;">
                <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
				    <td colspan="2">
				      <table width="100%" border="0" cellpadding="0" cellspacing="1">
                        <tr>
				          <td class="coluna_tabela" widht="70%" align="center">Sigla</td>
				          <td class="coluna_tabela" widht="10%" align="center">Unidade Associada</td>
				          <td class="coluna_tabela" widht="10%" align="center">Perfil</td>
				        </tr>
                        <?php
                           $sql_aplicacao = "select u.id_unidade, u.nome, u.sigla, uu.perfil_id_perfil
                                             from unidade_has_usuario uu, unidade u
                                             where uu.unidade_id_unidade = u.id_unidade
                                             and uu.usuario_id_usuario = '$_GET[id_usuario]'";

                           $aplicacao = mysqli_query($db, $sql_aplicacao);
                           erro_sql("Select Sigla/Unidade Associada/Perfil", $db, "");
                           $cor_linha = "#CCCCCC";
                           while ($listaaplicacao = mysqli_fetch_object($aplicacao)){
                         ?>
                             <tr valign="center" bgcolor="<?php echo $cor_linha;?>">
  						       <td class='linha_tabela' widht="70%" align="left"><?php echo $listaaplicacao->sigla; ?></td>
						       <td class='linha_tabela' widht="10%" align="left"><?php echo $listaaplicacao->nome; ?></td>
							   <td class='linha_tabela' widht="10%" align="center">
							     <select size="1" name="perfil" disabled>
                                   <option value="">Selecione</option>
								   <?php
								     //montar combo de perfil
								     $sql = "select id_perfil, descricao from perfil order by descricao";
                                     $perfil = mysqli_query($db, $sql);
                                     erro_sql("Select Perfil", $db, "");
                                     while ($listaperfil = mysqli_fetch_object($perfil)){
                                       if($listaaplicacao->perfil_id_perfil == $listaperfil->id_perfil){
                                   ?>
                                         <option value="<?php echo $listaperfil->id_perfil ;?>" selected><?php echo $listaperfil->descricao ;?></option>
                                   <?php
                                       }
                                       else{
                                   ?>
                                         <option value="<?php echo $listaperfil->id_perfil ;?>"><?php echo $listaperfil->descricao ;?></option>
                                   <?php
                                       }
                                     }
								   ?>
                                 </select>
                               </td>
                             </tr>
                         <?php
                             if ($cor_linha == "#CCCCCC")
                             {
                               $cor_linha = "#EEEEEE";
                             }
                             else
                             {
                               $cor_linha = "#CCCCCC";
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
            <td colspan="4" align="right" class="descricao_campo_tabela" height="35">
              <input style="font-size: 12px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/usuario/usuario_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET[pesquisa]?>'">
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
        </table>
      </td>
    </tr>
  </table>


<?php
  ////////////////////
  //RODAPÉ DA PÁGINA//
  ////////////////////
  ?>
  <?php
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
