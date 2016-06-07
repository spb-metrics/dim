<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
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

	//permissão para finalizar receita
    $sql = "select
                  id_aplicacao
            from
                  aplicacao
            where
                  executavel = '/modulos/dispensar/finalizar_receita.php'
                  and status_2 = 'A'";
    $res_finalizar = mysqli_fetch_object(mysqli_query($db, $sql));
    $id_aplicacao_finalizar = $res_finalizar->id_aplicacao;

    $sql = "select
                  inclusao, alteracao, exclusao, consulta
            from
                  perfil_has_aplicacao
            where
                  perfil_id_perfil = '$_SESSION[id_perfil_sistema]'
                  and aplicacao_id_aplicacao = '$id_aplicacao_finalizar'";
				  
    $acesso_finalizar = mysqli_fetch_object(mysqli_query($db, $sql));
    $inclusao_perfil_finalizar  = $acesso_finalizar->inclusao;
    $alteracao_perfil_finalizar = $acesso_finalizar->alteracao;
    $exclusao_perfil_finalizar  = $acesso_finalizar->exclusao;
    $consulta_perfil_finalizar  = $acesso_finalizar->consulta;	
	
	

    //executando botão pesquisar

    if (isset($_GET[id_paciente]))      //paciente do dim
    {
       $id_paciente = $_GET[id_paciente];

       $sql = "select id_status_paciente, nome, nome_mae, data_nasc,
                      sexo, tipo_logradouro, nome_logradouro, numero, complemento
               from
                      paciente
               where
                      id_paciente = '$id_paciente'
                      and status_2 = 'A' ";
       $listapaciente = mysqli_fetch_object(mysqli_query($db, $sql));

       $id_status_paciente = $listapaciente->id_status_paciente;
       $nome               = $listapaciente->nome;
       $mae                = $listapaciente->nome_mae;
       $data_nasc          = substr($listapaciente->data_nasc,-2)."/".substr($listapaciente->data_nasc,5,2)."/".substr($listapaciente->data_nasc,0,4);
       if ($listapaciente->sexo == "F")
       {
        $sexo = "Feminino";
       }
       else
       {
        if ($listapaciente->sexo == "M")
        {
         $sexo = "Masculino";
        }
        else
        {
         $sexo = "";
        }
       }
       $tipo_logradouro    = $listapaciente->tipo_logradouro;
       $nome_logradouro    = $listapaciente->nome_logradouro;
       $numero             = $listapaciente->numero;
       $complemento        = $listapaciente->complemento;
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////

    //permissão
    require "../../verifica_acesso.php";

    //caminho
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>
<script language="javascript">
var d = new Date();
var ID = d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

function popup_consulta_receita(receita)
{
	var height = 500;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("consulta_receita.php?id_receita="+receita, dialogArguments, "dialogWidth=1000px;dialogHeight=500px;scroll=yes;status=no;");
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("consulta_receita.php?id_receita="+receita, ID,"modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}


function popup_receita(receita)
{
	var height = 500;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("nova_receita_altera_receita.php?id_receita="+receita, dialogArguments, "dialogWidth=1000px;dialogHeight=500px;scroll=yes;status=no;");
    }
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("nova_receita_altera_receita.php?id_receita="+receita, ID,"modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
		window.close();
	}
	//return false;
}

function msg_cons(){
alert ('Usuário sem permissão para Consultar/Finalizar Receitas');

}
function msg_fin(){
alert ('Usuário sem permissão para Finalizar/Consultar Receitas');

}


function popup_final_receita(receita)
{
	var height = 500;
	var width = 1000;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("finalizar_receita.php?id_receita="+receita, dialogArguments, "dialogWidth=1000px;dialogHeight=500px;scroll=yes;status=no;");
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("consulta_receita.php?id_receita="+receita, ID,"modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}
	function resultado_final_receita(){
		var id_paciente = document.form_inclusao.id_paciente.value;
		document.form_inclusao.action = "pesquisa_receitas.php?id_paciente="+id_paciente;
		document.form_inclusao.submit(); 
	}


</script>

    <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='4'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./inicial.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> Receitas Cadastradas </td>
                    </tr>

                   <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Nome
                      </td>
                      <td class="campo_tabela" valign="middle" width="35%">
                        <input type="text" name="nome" size="45"  maxlength="70" value="<?php echo $nome;?>" disabled>
                        <input type="hidden" name="id_paciente" value="<?php echo $id_paciente;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                          <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Dt. Nascimento
                      </td>
                      <td class="campo_tabela" valign="middle" width="35%">
                        <input type="text" name="data_nasc" size="15"  maxlength="10" <?php if (isset($data_nasc)){echo "value='".$data_nasc."'";}?> disabled>
                      </td>
                   </tr>

                   <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Sexo
                      </td>
                      <td class="campo_tabela" valign="middle" width="35%">
                        <input type="text" name="sexo" size="15"  maxlength="10" <?php if (isset($sexo)){echo "value='".$sexo."'";}?> disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Nome Mãe
                      </td>
                      <td class="campo_tabela"  valign="middle" width="35%">
                        <input type="text" name="mae" size="45" maxlength="70" value="<?php if (isset($mae)){echo $mae;}?>" disabled>
                      </td>
                    </tr>

                   <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                          <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Tipo Logradouro
                      </td>
                      <td class="campo_tabela" valign="middle" width="35%">
                        <input type="text" name="tipo_logradouro" size="15" maxlength="70" <?php if (isset($tipo_logradouro)){echo "value='".$tipo_logradouro."'";}?> disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Logradouro
                      </td>
                      <td class="campo_tabela"  valign="middle" width="35%">
                        <input type="text" name="nome_logradouro" size="45" maxlength="70" <?php if (isset($nome_logradouro)){echo "value='".$nome_logradouro."'";}?> disabled >
                      </td>
                    </tr>

                   <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Número
                      </td>
                      <td class="campo_tabela"  valign="middle" width="35%">
                        <input type="text" name="numero" size="15" maxlength="70" <?php if (isset($numero)){echo "value='".$numero."'";}?> disabled>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Complemento
                      </td>
                      <td class="campo_tabela"  valign="middle" width="35%">
                        <input type="text" name="complemento" size="45" maxlength="70" <?php if (isset($complemento)){echo "value='".$complemento."'";}?> disabled>
                      </td>
                    </tr>

                  </form>
                  <tr>
                  <td colspan="4" >
                  <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <tr bgcolor='#6B6C8F' class="coluna_tabela">
                      <td width='20%' align='center'>
                          No. Receita
                      </td>

                      <td width='20%' align='center'>
                          Unidade
                      </td>

                      <td width='15%' align='center'>
                          Status
                      </td>
                      
                      <td width='15%' align='center'>
                          Dt. Dispensação
                      </td>

                      <td width='15%' align='center'>
                          Dt. Emissão
                      </td>
                      <td width='5%' align='center'>
                      </td>

                      <td width='5%' align='center'>
                      </td>
                       <td width='5%' align='center'>
                      </td>

                  </tr>
                  <?php

                    if ($id_paciente!="")
                    {
                       $sql = "select r.ano, r.unidade_id_unidade, r.numero, r.id_receita,
                                      r.status_2, r.data_ult_disp, r.data_emissao,
                                      p.num_receitas_paciente
                               from
                                      receita r,
                                      parametro p
                               where
                                      paciente_id_paciente = $id_paciente
                                      and CURRENT_DATE <= DATE_ADD(data_emissao, INTERVAL num_receitas_paciente month)
                               order by
                                      status_2,
                                      data_incl desc";
                       $receita= mysqli_query($db, $sql);
                       if (mysqli_num_rows($receita)>0)
                       {
                        $cor_linha = "#CCCCCC";
                        while ($listareceita = mysqli_fetch_object($receita))
                        {?>
                          <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'" >

                          <td align='left'>
                            <?php
                            $numero = $listareceita->ano . "-" . $listareceita->unidade_id_unidade . "-" . $listareceita->numero;
                            echo $numero;?>
                          </td>

                          <td align='left'>
                            <?php
                             $sql = "select nome
                                     from
                                            unidade
                                     where
                                            id_unidade = $listareceita->unidade_id_unidade";
                             $res = mysqli_query($db, $sql);
                             $dados_unidade = mysqli_fetch_object($res);
                             echo $dados_unidade->nome;?>
                          </td>

                          <td align='left'>
                            <?php echo $listareceita->status_2;?>
                          </td>
                           <?
                             if ($listareceita->data_ult_disp!='')
                             {?>
                              <td align='left'>
                              <?php echo substr($listareceita->data_ult_disp,8,2)."/". substr($listareceita->data_ult_disp,5,2)."/".substr($listareceita->data_ult_disp,0,4); ?>
                              </td>
                             <?}
                             else
                             {?>
                              <td align='center'><?php echo "--" ;?></td>
                             <?}?>

                          <td align='left'>
                            <?php echo substr($listareceita->data_emissao,8,2)."/". substr($listareceita->data_emissao,5,2)."/".substr($listareceita->data_emissao,0,4) ;?>
                          </td>

                          <td align='center'>
                          <?php
                           if ($listareceita->status_2 == 'ABERTA')
                           {
                             if($alteracao_perfil!="")
                             {?>
                              <img src='<?php echo URL;?>/imagens/b_edit.png' onclick='JavaScript:window.popup_receita(<?php echo $listareceita->id_receita;?>);' border='0' title='Editar Receita'></a>
                             <?}
                           }?>
                          </td>

                          <td align='center'>
                          <?php
                           //if($consulta_perfil_finalizar !="")
                           {?>
                            <img src='<?php echo URL;?>/imagens/b_search.png' onclick='JavaScript:window.popup_consulta_receita(<?php echo $listareceita->id_receita;?>);' border='0' title='Detalhar Receita'></a>
                           <?}?>
                          </td>
                          
                           <td align='center'>
                          <?php
						  
                           if($inclusao_perfil_finalizar == "" && $consulta_perfil_finalizar == "")
                           {
						   ?>
                            <img src='<?php echo URL;?>/imagens/b1_excluir.png' onclick='JavaScript:window.msg_fin();'  border='0' title='Finalizar Receita' ></a>
							
                           <?} else { ?>
						   <img src='<?php echo URL;?>/imagens/b1_excluir.png' onclick='JavaScript:window.popup_final_receita(<?php echo $listareceita->id_receita;?>);' border='0' title='Finalizar Receita'></a>
						   
						   <? }  ?>
						   
						 
						   
						   
						   
						   
						   
						   
						   
						   
						   
                          </td>

                        </tr>
                        <?
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
                    }
                  ?>
                  </table>
                  </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
    <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="10%" >
       <tr>
         <td  align="right" bgcolor="#D8DDE3">
             <input type="hidden" id="dados_salvar" name="dados_salvar">
             <input style="font-size: 10px;" type="button" name="voltar" value="<< Voltar" onClick="window.close();">
         </td>
       </tr>
    <table>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////

    //verificar se paciente está morto
    $sql = "select id_status_paciente
            from
                   status_paciente
            where
                   descricao like 'morto'";
    $statuspaciente = mysqli_fetch_object(mysqli_query($db, $sql));
    $statusmorto = $statuspaciente->id_status_paciente;

    if ($id_status_paciente == $statusmorto)
    {
      echo "<script>";
      echo "alert('Paciente com situação irregular, favor acertar o cadastro!');";
      echo "window.location='inicial.php?aplicacao=$_SESSION[DISP_INICIAL]';";
      echo "</script>";
    }

    if ($id_status_paciente == $statusbloqueado)
    {
       //cadastro perdeu a validade ....
       echo "<script>";
       echo "alert('Cadastro perdeu a validade. Dirija-se ao atendimento para ser recadastrado!');";
       echo "window.location='inicial.php?aplicacao=$_SESSION[DISP_INICIAL]';";
       echo "</script>";
    }


  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
