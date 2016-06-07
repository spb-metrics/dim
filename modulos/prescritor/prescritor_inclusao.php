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
  //  Arquivo..: prescritor_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de prescritor
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
      exit();
    }

    if(isset($_POST[descricao]) && isset($_POST[conselho])){
      $data=date("Y-m-d H:i:s");
	  
	  
	  $sql ="select * from tipo_prescritor where tipo_conselho_id_tipo_conselho = $_POST[conselho] and status_2 = 'A'";
	  
	 // echo $sql;
	  //exit;
	   $resp = mysqli_query($db, $sql);
       //erro_sql("Select Inicial", $db, "");
	   //echo $resp;
	   //exit;
	   
	   
		
		if(mysqli_num_rows($resp) !=0){    
				header("Location: ". URL."/modulos/prescritor/prescritor_inicial.php?i=f");
			}	   
			else{
			  $sql="insert into tipo_prescritor (tipo_conselho_id_tipo_conselho, descricao, status_2, date_incl, usua_incl) ";
			  $sql.="values ('$_POST[conselho]', '" . trim(strtoupper($_POST[descricao])) . "', 'A', '$data', '$_SESSION[id_usuario_sistema]')";
			  mysqli_query($db, $sql);
			  erro_sql("Insert Tipo Prescritor", $db, "");

					  /////////////////////////////////////
					  //SE INCLUSÃO OCORREU SEM PROBLEMAS//
					  /////////////////////////////////////
					//  echo $sql;
//					  exit;
					  
					  
					  if(mysqli_errno($db)=="0"){
						mysqli_commit($db);
						header("Location: ". URL."/modulos/prescritor/prescritor_inicial.php?i=t");
					  }
					  else{
						mysqli_rollback($db);
						header("Location: ". URL."/modulos/prescritor/prescritor_inicial.php?i=f");
					  }
      exit();
    
	
	}
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
      function validarCampos(descr, consel){
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        if(consel.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          consel.focus();
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
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./prescritor_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="codigo" size="30" style="width: 200px" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Profissional
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <input type="text" name="descricao" size="30" style="width: 200px">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Conselho Profissional
                      </font>
                      </td>
                      <td class="campo_tabela" colspan="2" valign="middle" width="100%">
                        <select name="conselho" size="1" style="width: 200px">
                          <option value="0"> Selecione um Conselho </option>
                          <?php
                            $sql="select id_tipo_conselho, descricao from tipo_conselho where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Conselho", $db, "");
                            while($conselho_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $conselho_info->id_tipo_conselho;?>"> <?php echo $conselho_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="3"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/prescritor/prescritor_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="if(validarCampos(document.form_inclusao.descricao, document.form_inclusao.conselho)){document.form_inclusao.submit();}">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="3" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
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
      document.form_inclusao.descricao.focus();
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
