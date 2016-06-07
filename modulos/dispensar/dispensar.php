<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $_SESSION[APLICACAO]=$_GET[aplicacao];

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

    //executando botão pesquisar

    if (isset($_GET[id_paciente]))      //paciente do dim
    {
       $id_paciente = $_GET[id_paciente];

       $sql = "select * from paciente where id_paciente = '$id_paciente' and status_2 = 'A' ";
       $listapaciente = mysqli_fetch_object(mysqli_query($db, $sql));

       $id_status_paciente = $listapaciente->id_status_paciente;
       $nome = $listapaciente->nome;
       $mae = $listapaciente->nome_mae;
       $data_nasc = substr($listapaciente->data_nasc,-2)."/".substr($listapaciente->data_nasc,5,2)."/".substr($listapaciente->data_nasc,0,4);
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
       $tipo_logradouro = $listapaciente->tipo_logradouro;
       $nome_logradouro = $listapaciente->nome_logradouro;
       $numero = $listapaciente->numero;
       $complemento = $listapaciente->complemento;

    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    //permissão
    require "../../verifica_acesso.php";

    //caminho
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>
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
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='4'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./inicial.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> Dispensar Medicamentos </td>
                    </tr>

                   <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Nome
                      </td>
                      <td class="campo_tabela" valign="middle" width="35%">
                        <input type="text" name="nome" size="45"  maxlength="70" value="<?php echo $nome;?>" disabled>
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

                    <tr>
                        <td colspan="4" align="right" bgcolor="#D8DDE3">
                        <? //echo $_GET[nome_tela]; ?>
                            <input style="font-size: 10px;" type="button" name="voltar" value="<< Voltar" onClick="window.location='<?php echo URL;?>/modulos/dispensar/inicial.php?aplicacao=<?php echo $_SESSION[DISP_INICIAL];?>&id_paciente=<?php echo $id_paciente;?>'">
                            <?php
                             if($inclusao_perfil!="")
                             {?>
                              <input style="font-size: 10px;" type="button" name="nova_receita" value="Nova Receita" onClick="window.location='<?php echo URL;?>/modulos/dispensar/nova_receita.php?id_paciente=<?php echo $id_paciente;?>'">
                             <?}
                             else
                             {?>
                              <input style="font-size: 10px;" type="button" name="nova_receita" value="Nova Receita" onClick="window.location='<?php echo URL;?>/modulos/dispensar/nova_receita.php?id_paciente=<?php echo $id_paciente;?>'" disabled>
                             <?}?>
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

                  </tr>
                  <?php

                    if ($id_paciente!="")
                    {
                       /*$sql_parametro = "select num_receitas_paciente from parametro";
                       $parametro = mysqli_query($db, $sql_parametro);
                       $info_parametro = mysqli_fetch_object($parametro);
                       $num_receitas_paciente = $info_parametro->num_receitas_paciente;
                       
                       $sql="select * from receita where paciente_id_paciente = '$id_paciente'
                                    order by status_2, data_emissao desc";
                       if ($num_receitas_paciente!='')
                       {
                        $sql = $sql. " limit $num_receitas_paciente";
                       }
                       */
                       $sql = "select r.*, p. num_receitas_paciente from receita r, parametro p
                                      where paciente_id_paciente = $id_paciente
                                      and CURRENT_DATE <= DATE_ADD(data_emissao, INTERVAL num_receitas_paciente month)
                                      order by status_2, data_emissao desc";
                      // echo $sql_parametro;
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
                             $sql = "select * from unidade where id_unidade = $listareceita->unidade_id_unidade";
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
                             <?}
                             else
                             {?>
                              <td align='center'>
                              <?php echo "--" ;?>
                             <?}?>
                           </td>

                          <td align='left'>
                            <?php echo substr($listareceita->data_emissao,8,2)."/". substr($listareceita->data_emissao,5,2)."/".substr($listareceita->data_emissao,0,4) ;?>
                          </td>

                          <td align='center'>
                          <?php
                           if ($listareceita->status_2 == 'ABERTA')
                           {
                             if($alteracao_perfil!="")
                             {?>
                              <a href='<?php echo URL;?>/modulos/dispensar/altera_receita.php?id_receita=<?php echo $listareceita->id_receita;?>'><img src="<?php echo URL;?>/imagens/b_edit.png" border="0" title="Editar Receita"></a>
                             <?}
                           }?>
                          </td>

                          <td align='center'>
                          <?php
                           if($consulta_perfil!="")
                           {?>
                            <a href='<?php echo URL;?>/modulos/dispensar/consulta_receita.php?id_receita=<?php echo $listareceita->id_receita;?>'><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Detalhar Receita"></a>
                           <?}?>
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
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";

    //verificar se paciente está morto
    $sql = "select * from status_paciente where descricao like 'morto'";
    $statuspaciente = mysqli_fetch_object(mysqli_query($db, $sql));
    $statusmorto = $statuspaciente->id_status_paciente;

    if ($id_status_paciente == $statusmorto)
    {
      echo "<script>";
      echo "alert('Paciente com situação irregular, favor acertar o cadastro!');";
      //echo "history.go(-1)";
      echo "window.location='inicial.php?aplicacao=$_SESSION[DISP_INICIAL]';";
      echo "</script>";
    }

    if ($id_status_paciente == $statusbloqueado)
    {
       //cadastro perdeu a validade ....
       echo "<script>";
       echo "alert('Cadastro perdeu a validade. Dirija-se ao atendimento para ser recadastrado!');";
       echo "window.location='inicial.php?aplicacao=$_SESSION[DISP_INICIAL]';";
       //echo "history.go(-1)";
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
