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
if(file_exists("../../config/config.inc.php")){
 require "../../config/config.inc.php";

  ////////////////////////////
  //VERIFICAÇÃO DE SEGURANÇA//
  ////////////////////////////
 if($_SESSION[id_usuario_sistema]==''){
  header("Location: ". URL."/start.php");
  exit();
 }

 if($_GET[id_unidade]!=""){
    $sql_select = "select id_unidade, sigla, cnes, nome, unidade_id_unidade, flg_nivel_superior,
                   coordenador, rua, numero, complemento, bairro, municipio, uf,
                   cep, telefone, e_mail, cod_estabelecimento,flg_banco, dns_local, usuario_integra_local,
                   senha_integra_local,flg_transf_almo,base_integra_ima
                   from unidade where id_unidade = '".$_GET[id_unidade]."'";
    $res=mysqli_query($db, $sql_select);
    erro_sql("Select Unidade", $db, "");
    $unidade    = mysqli_fetch_object($res);

    $id_unidade            = $unidade->id_unidade;
    $sigla                 = $unidade->sigla;
    $cnes                  = $unidade->cnes;
    $nome                  = $unidade->nome;
    $unidade_id_unidade    = $unidade->unidade_id_unidade;
    $flg_nivelsuperior     = $unidade->flg_nivel_superior;
    $coordenador           = $unidade->coordenador;
    $rua                   = $unidade->rua;
    $numero                = $unidade->numero;
    $complemento           = $unidade->complemento;
    $bairro                = $unidade->bairro;
    $municipio             = $unidade->municipio;
    $uf                    = $unidade->uf;
    $cep                   = $unidade->cep;
    $telefone              = $unidade->telefone;
    $e_mail                = $unidade->e_mail;
    $cod_estabelecimento   = $unidade->cod_estabelecimento;
    $flg_banco             = $unidade->flg_banco;
    $dns_local             = $unidade->dns_local;
    $usuario_integra_local = $unidade->usuario_integra_local;
    $senha_integra_local   = $unidade->senha_integra_local;
    $flg_transf_almo       = $unidade->flg_transf_almo;
    $base_integra_ima      = $unidade->base_integra_ima;
    
    
    
    
 }

  ////////////////////////////////////
  //BLOCO HTML DE MONTAGEM DA PÁGINA//
  ////////////////////////////////////
  require DIR."/header.php";
  require DIR."/buscar_aplic.php";
 ?>
  
 <script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
  


  <table width="100%" class="caminho_tela" border="1" cellpadding="0" cellspacing="0">
    <tr><td><?php echo $caminho; ?></td></tr>
  </table>
  <table width="100%" height="95%" border="1" cellpadding="0" cellspacing="0">
    <tr height="5%">
      <td>
        <table width="100%" class="titulo_tabela" height="21">
          <tr><td align="center"><? echo $nome_aplicacao; ?>: Detalhar</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="1">
          <tr>
            <td align="left" width="25%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Sigla
            </td>
            <td align="left" colspan="6" width="75%" class="campo_tabela">
              <input type="text" name="sigla" id="sigla" size="30" maxlength="10" <?php if (isset($sigla)){echo "value='".$sigla."'";}?> disabled>
            </td>

          </tr>
          <tr>
            <td align="left" width="25%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Unidade
            </td>
            <td align="left" colspan="6" width="75%" class="campo_tabela">
              <input type="text" name="nome" id="nome" size="102" maxlength="40"
              <?php {echo "value='".$nome."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" width="25%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat.gif";?>">Nível Superior
            </td>
            <td align="left" width="25%" class="campo_tabela">
              <input type="radio" name="flg_nivelsuperior" value="1" disabled <?php if ($flg_nivelsuperior=='1'){echo "checked";}?>>Sim &nbsp&nbsp&nbsp
              <input type="radio" name="flg_nivelsuperior" value="0" disabled <?php if ($flg_nivelsuperior!='1'){echo "checked";}?>>Não
            </td>
            <td align="left" width="25%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Unidade Superior
            </td>
            <td align="left" width="25%" class="campo_tabela">
              <select name="nome_und_sup" style="width:205px;" disabled>
                <option value=""></option>
                <?php
                  $sql="select id_unidade, nome from unidade where flg_nivel_superior = '1' order by nome";
                  $nivel=mysqli_query($db, $sql);
                  erro_sql("Select Unidade Superior", $db, "");
                  if(mysqli_num_rows($nivel)>0){
                    while($lista_nivel = mysqli_fetch_object($nivel)){
                      if($lista_nivel->id_unidade==$unidade_id_unidade){
                ?>
                        <option value="<?php echo $lista_nivel->id_unidade; ?>" selected><?php echo $lista_nivel->nome; ?></option>
                <?php
                      }
                      else{
                ?>
                        <option value="<?php echo $lista_nivel->id_unidade; ?>"><?php echo $lista_nivel->nome; ?></option>
                <?php
                      }
                    }
                  }
                ?>
              </select>
            </td>
            </tr>
            <tr>
            <?php
                     $sql="select mostrar_cod_estab, nome_cod_estab from parametro";
                     $param = mysqli_query($db, $sql);
                     erro_sql("Tabela Parametro", $db, "");
                     if($tb_parametro = mysqli_fetch_object($param)){
                          $tb_parametro->mostrar_cod_estab;
                          $tb_parametro->nome_cod_estab;
                          if (strtoupper($tb_parametro->mostrar_cod_estab)=='S')
                          {  ?>
                           <td align="left" width="25%" class="descricao_campo_tabela">
                               <img src="<? echo URL."/imagens/obrigat.gif";?>">Cnes
                           </td>
                           <td align="left" width="25%"  class="campo_tabela">
                               <input type="text" name="cnes" id="cnes" size="30" maxlength="10" value="<?echo $cnes?>" disabled>
                           </td>
                           <td align="left" width="25%" class="descricao_campo_tabela">
                             <img src="<? echo URL."/imagens/obrigat.gif";?>"><? echo $tb_parametro->nome_cod_estab;?>
                           </td>
                           <td align="left" width="25%"  class="campo_tabela">
                             <input type="text" name="cod_estabelecimento" id="cod_estabelecimento" size="30" maxlength="10" value="<?echo $cod_estabelecimento?>" disabled>
                           </td>

                         <?
                          }
                          else
                          {?>
                            <td align="left" width="25%" class="descricao_campo_tabela">
                              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Cnes
                            </td>
                            <td align="left" width="25%"  class="campo_tabela">
                              <input type="text" name="cnes" id="cnes" size="30" maxlength="10" value="<?echo $cnes?>" disabled>
                            </td>
                            <td align="left" width="25%"  class="campo_tabela" colspan="2"></td>
                          <?
                          }
                     }
                    ?>
          </tr>
          <tr>
            <td align="left" width="25%" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Coordenador
            </td>

            
            <td align="left" colspan="6" width="75%" class="campo_tabela">
              <input type="text" name="coordenador" id="coordenador" size="102" maxlength="100" <?php if (isset($coordenador)){echo "value='".$coordenador."'";}?> disabled>
            </td>

          </tr>
          <tr>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Logradouro
            </td>
            <td align="left" colspan="6" class="campo_tabela">
              <input type="text" name="rua" id="rua" size="102" maxlength="255" <?php if (isset($rua)){echo "value='".$rua."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Número
            </td>
            <td align="left" class="campo_tabela">
              <input type="text" name="numero" id="numero" size="10" maxlength="255" <?php if (isset($numero)){echo "value='".$numero."'";}?> disabled>
            </td>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Complemento
            </td>
            <td align="left" class="campo_tabela">
              <input type="text" name="complemento" id="complemento" size="30" maxlength="255" <?php if (isset($complemento)){echo "value='".$complemento."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Bairro
            </td>
            <td align="left" class="campo_tabela" colspan="3">
              <input type="text" name="bairro" id="bairro" size="102" maxlength="255" <?php if (isset($bairro)){echo "value='".$bairro."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Cidade
            </td>
            <td align="left" class="campo_tabela">
              <input type="text" name="cidade" id="cidade" size="30" maxlength="255" <?php if (isset($municipio)){echo "value='".$municipio."'";}?> disabled>
            </td>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">UF
            </td>
            <td align="left" class="campo_tabela">
              <select name="uf"  style="width:50px;" disabled>
                <option value=""></option>
                <?
                  $sql="select uf from estado order by uf";
                  $estado=mysqli_query($db, $sql);
                  erro_sql("Select UF", $db, "");
                  if(mysqli_num_rows($estado)>0){
                    while($lista_estado=mysqli_fetch_object($estado)){
                      if($lista_estado->uf==$uf){
                ?>
                        <option value="<?php echo $lista_estado->uf; ?>" selected><?php echo $lista_estado->uf; ?></option>
                <?
                      }
                      else{
                ?>
                        <option value="<?php echo $lista_estado->uf; ?>"><?php echo $lista_estado->uf; ?></option>
                <?
                      }
                    }
                  }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Cep
            </td>
            <td align="left" class="campo_tabela">
              <input type="text" name="cep" id="cep" size="20" maxlength="255" <?php if (isset($cep)){echo "value='".$cep."'";}?> disabled>
            </td>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Telefone
            </td>
            <td align="left" class="campo_tabela">
              <input type="text" name="telefone" id="telefone" size="30" maxlength="255" <?php if (isset($telefone)){echo "value='".$telefone."'";}?> disabled>
            </td>
          </tr>
          <tr>
            <td align="left" class="descricao_campo_tabela">
              <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Email
            </td>
            <td align="left" class="campo_tabela" colspan="3">
              <input type="text" name="e_mail" id="e_mail" size="102" maxlength="255" <?php if (isset($e_mail)){echo "value='".$e_mail."'";}?> disabled>
            </td>
          </tr>
          
          
         <!-- Glaison Inicio -->


     	          <table  width="100%" class="titulo_tabela" cellpadding="0" cellspacing="1">
		            <TR align="center">
				      <TD colspan = "4">Configurações</TD>
				      <TD  width="10"><A href="javascript:showFrame('unidades');"><IMG SRC="<?php echo URL. '/imagens/b_edit.gif'; ?>" BORDER="0" TITLE="Exibir Informações de Configurações"></A></TD>
				    </TR>
                           </TABLE>

                      </TD>
		      </TR>
			  <TR>
			    <TD colspan="4">
			      <div id="unidades" style="display:'';">

			        <table border="0" width="100%" cellpadding="0" cellspacing="1">
                    <tr>
                        <td  colspan = "4" align="left" width="25%" class="descricao_campo_tabela">
                            <img src="<? echo URL."/imagens/obrigat_1.gif";?>">SIG2M
                        </td>

                         <td  colspan = "4" align="left" width="25%"  class="campo_tabela">
                           <input type="radio" name="flg_banco" value="1" disabled <?php if ($flg_banco=='1'){echo "checked";}?>>IMA &nbsp&nbsp&nbsp
                           <input type="radio" name="flg_banco" value="0" disabled <?php if ($flg_banco!='1'){echo "checked";}?>>Unidades
                         </td>
                   </TR>

              <TR>
                     <td colspan = "4" align="left" width="30%" class="descricao_campo_tabela">
                         <img src="<? echo URL."/imagens/obrigat_1.gif";?>">DNS
                     </td>

                     <td  colspan = "4" align="left" width="70%" class="campo_tabela">
                     <input type="text" name="dns_local" id="dns_local" size="102" maxlength="70" <?php if (isset($dns_local)){echo "value='".$dns_local."'";}?> disabled>

                     </td>
              </TR>
              
              <!--Inicio novo campo para informar o nome do banco de dados -->
               <tr>
				   <td colspan="4" align="left" class="descricao_campo_tabela"><img
				   	    src="<? echo URL."/imagens/obrigat_1.gif";?>" alt="" />Banco de Dados
                    </td>
				    <td colspan="4" align="left" class="campo_tabela">
                    <input type="text" name="base_integra_ima" id="base_integra_ima" size="102"	maxlength="20"  <?php if (isset($base_integra_ima)){echo "value='".$base_integra_ima."'";}?> disabled>
                    </td>
			  </tr>
              
              
             <!-- Fim novo campo-->
              
              
              

          <tr>
              <td  COLSPAN = "4" align="left"  width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Usuário
              </td>
              <td    align="left"   width="25%"  class="campo_tabela">
                <input type="text" name="usuario_integra_local" id="usuario_integra_local" size="30" <?php if (isset($usuario_integra_local)){echo "value='".$usuario_integra_local."'";}?> disabled>
              </td>
              <td  COLSPAN = "" align="left"   width="25%" class="descricao_campo_tabela">
                <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Senha
              </td>
              <td  align="left"  width="25%" class="campo_tabela">
                <input type="password" name="senha_integra_local" id="senha_integra_local" size="31" <?php if (isset($senha_integra_local)){echo "value='".$senha_integra_local."'";}?> disabled>
              </td>
          </tr>

          <tr>
              <td  colspan = "4" align="left" width="25%" class="descricao_campo_tabela">
                  <img src="<? echo URL."/imagens/obrigat_1.gif";?>">Integração <br>
                  &nbsp&nbsp&nbsp Almox. Central
              </td>
              <td  colspan = "4" align="left" width="25%"  class="campo_tabela">
                  <input type="radio" name="flg_transf_almo" value="s" disabled <?php if ($flg_transf_almo=='s'){echo "checked";}?>> Sim &nbsp&nbsp&nbsp
                  <input type="radio" name="flg_transf_almo" value="n" disabled <?php if ($flg_transf_almo!='s'){echo "checked";}?>> Não &nbsp&nbsp&nbsp
              </td>
          </tr>
          </TABLE>

                </div>

          <!-- Glaison Fim -->
          
          
          
          
          
          
          <tr>
            <td colspan="4" align="right" class="descricao_campo_tabela" height="35">
              <input style="font-size: 10px;" type="button" name="voltar"  value="<< Voltar"  onClick="window.location='<?php echo URL;?>/modulos/unidade/unidade_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
              <input type="hidden" name="id_unidade" id="id_unidade" <?php if (isset($id_unidade)){echo "value='".$id_unidade."'";}?> >
            </td>
          </tr>
          <tr>
		    <td colspan="4" class="descricao_campo_tabela">
			  <table align="center" border="0">
			    <tr valign="center" class="descricao_campo_tabela" height="21">
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

       <div align="center">
<?php
  ////////////////////
  //RODAPÉ DA PÁGINA//
  ////////////////////
  ?>
    <script language="JavaScript" type="text/JavaScript">
    //////////////////////////
    //DEFININDO FOCO INICIAL//
    //////////////////////////
    //cadastro.sigla.focus();
    </script>
  <?php
  require DIR."/footer.php";

}
////////////////////////////////////////////
//SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
////////////////////////////////////////////
else{
  include_once "../../config/erro_config.php";
}
?>
</div>
