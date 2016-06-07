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

// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: pesquisa_paciente.php                                     |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Tela de Pesquisa de Pacientes                             |
// | Data de Criação ....: 15/01/2007 - 15:05                                        |
// | Última Atualização .: 16/03/2007 - 11:50                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

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

    $sql = '';
    
    $cartao_sus    = $_POST[cartao_sus];
    $cpf           = $_POST[cpf];
    $prontuario    = $_POST[prontuario];
    $nome_sem_esp  = ereg_replace(' ', '', $_POST[nome]);
    $mae_sem_esp   = ereg_replace(' ', '', $_POST[nome_mae]);
    
    
    $data_nasc = $_POST[dt_nasc];
    $data = ((substr($data_nasc,6,4))."-".(substr($data_nasc,3,2))."-".(substr($data_nasc,0,2)));
            
    if(isset($cartao_sus) && $cartao_sus != "")
    {
      $sql = "select pac.id_paciente, cart.cartao_sus, pac.nome, pac.data_nasc, pac.nome_mae, pac.tipo_logradouro,
                     pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, stp.descricao
              from paciente pac
                   inner join status_paciente stp on pac.id_status_paciente = stp.id_status_paciente
                   inner join cartao_sus as cart on pac.id_paciente=cart.paciente_id_paciente
              where pac.status_2 = 'A'
                    and cart.cartao_sus = '$cartao_sus'";
      $tag = "cartao_sus";
      $obj = mysqli_query($db, $sql);
      
      if (mysqli_num_rows($obj)== 0)
      {
        if ($cpf!="")
        {
          $sql = "select id_paciente, nome,
                data_nasc,
                nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                from paciente
                where status_2 = 'A' and cpf= $cpf";
          
          $obj = mysqli_query($db, $sql);
          
          if (mysqli_num_rows($obj) == 0)
          {
             if($prontuario!="")
              {
                  $sql_pront = "select num_prontuario, paciente_id_paciente from prontuario where substring(num_prontuario,1,15)= '$prontuario' and unidade_id_unidade = $id_unidade_sistema";
                  $prontuario = mysqli_query($db, $sql_pront);
                  if (mysqli_num_rows($prontuario) > 0)
                  {
                     while($lista = mysqli_fetch_object($prontuario))
                     {
                        $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
                     }

                    $ids_pacientes = substr($ids_pacientes, 0, -1);

                    $sql = "select id_paciente, nome, data_nasc, nome_mae, nome_logradouro from paciente where status_2='A' and id_paciente in ($ids_pacientes)";
                    $obj = mysqli_query($db, $sql);
                  }

                  else if($nome_sem_esp!="")
                  {
                      $sql="select id_paciente, nome,
                            data_nasc,
                            nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                            from paciente
                            where status_2 = 'A'
                            and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                      if($mae_sem_esp!="")
            		  {
                         $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                      }
                      if(isset($data_nasc) && $data_nasc != "")
                      {
                         $sql .= " and data_nasc = '$data'";
                      }
                      $sql.=" order by nome_mae_nasc";

                      $obj = mysqli_query($db, $sql);
                 }
              }
          }
      }
      
      else if($prontuario!="")
      {
          $sql = "select num_prontuario, paciente_id_paciente from prontuario where substring(num_prontuario,1,15)= '$prontuario' and unidade_id_unidade = $id_unidade_sistema";
          
          $prontuario = mysqli_query($db, $sql);
          if (mysqli_num_rows($prontuario) > 0)
          {
             while($lista = mysqli_fetch_object($prontuario))
             {
                $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
             }

            $ids_pacientes = substr($ids_pacientes, 0, -1);

            $sql = "select id_paciente, nome, data_nasc, nome_mae, nome_logradouro from paciente where status_2='A' and id_paciente in ($ids_pacientes)";
            
            $obj = mysqli_query($db, $sql);
          }
          else if($nome_mae_nasc!="")
          {
              $sql="select id_paciente, nome,
                    data_nasc,
                    nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                    from paciente
                    where status_2 = 'A'
                    and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

              if($mae_sem_esp!="")
    		  {
                  $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
              }
              if(isset($data_nasc) && $data_nasc != "")
    		  {
                  $sql.=" and data_nasc like '$data'";
              }
              $sql.=" order by nome_mae_nasc";

              $obj = mysqli_query($db, $sql);
         }
       } // prontuario

       else if(isset($nome_sem_esp) && $nome_sem_esp != "")
        {
          $sql = "select pac.id_paciente, pac.nome, pac.data_nasc, pac.nome_mae, pac.tipo_logradouro,
                         pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, stp.descricao
                  from paciente pac
                       inner join status_paciente stp on pac.id_status_paciente = stp.id_status_paciente
                  where pac.status_2 = 'A'
                        and pac.nome_mae_nasc like '".trim($nome_sem_esp)."%'";


          if(isset($data_nasc) && $data_nasc != "")
          {
            $sql .= " and pac.data_nasc = '$data'";
          }

          if(isset($mae_sem_esp) && $mae_sem_esp != "")
          {
            $sql .= " and pac.nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
          }
          $tag = "03";
          $sql.=" order by pac.nome_mae_nasc";
          $obj = mysqli_query($db, $sql);
        }
    } // cartao nao encontrado
  }
    
  //se cartão não informado
  
   else if($cpf!="")
   {
      $sql = "select id_paciente, nome,
            data_nasc,
            nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
            from paciente
            where status_2 = 'A' and cpf= $cpf";
      $obj = mysqli_query($db, $sql);
      if (mysqli_num_rows($obj) == 0)
      {
         if($prontuario!="")
          {
              $sql = "select num_prontuario, paciente_id_paciente from prontuario where substring(num_prontuario,1,15)= '$prontuario' and unidade_id_unidade = $id_unidade_sistema";
              $prontuario = mysqli_query($db, $sql);
              if (mysqli_num_rows($prontuario) > 0)
              {
                 while($lista = mysqli_fetch_object($prontuario))
                 {
                    $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
                 }

                $ids_pacientes = substr($ids_pacientes, 0, -1);

                $sql = "select id_paciente, nome, data_nasc, nome_mae, nome_logradouro from paciente where status_2='A' and id_paciente in ($ids_pacientes)";
                $obj = mysqli_query($db, $sql);
              }
              else if($nome_sem_esp!="")
              {
                  $sql = "select pac.id_paciente, pac.nome, pac.data_nasc, pac.nome_mae, pac.tipo_logradouro,
                          pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, stp.descricao
                          from paciente pac
                          inner join status_paciente stp on pac.id_status_paciente = stp.id_status_paciente
                          where pac.status_2 = 'A'
                          and pac.nome_mae_nasc like '".trim($nome_sem_esp)."%'";


                  if(isset($data_nasc) && $data_nasc != "")
                  {
                    $sql .= " and pac.data_nasc = '$data'";
                  }

                  if(isset($mae_sem_esp) && $mae_sem_esp != "")
                  {
                    $sql .= " and pac.nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                  }
                  $tag = "03";
                  $sql.=" order by pac.nome_mae_nasc";

                  $obj = mysqli_query($db, $sql);
              }
          }
          else if($nome_sem_esp!="")
          {
              $sql = "select pac.id_paciente, pac.nome, pac.data_nasc, pac.nome_mae, pac.tipo_logradouro,
                      pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, stp.descricao
                      from paciente pac
                      inner join status_paciente stp on pac.id_status_paciente = stp.id_status_paciente
                      where pac.status_2 = 'A'
                      and pac.nome_mae_nasc like '".trim($nome_sem_esp)."%'";


              if(isset($data_nasc) && $data_nasc != "")
              {
                $sql .= " and pac.data_nasc = '$data'";
              }

              if(isset($mae_sem_esp) && $mae_sem_esp != "")
              {
                $sql .= " and pac.nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
              }
              $tag = "03";
              $sql.=" order by pac.nome_mae_nasc";

              $obj = mysqli_query($db, $sql);
          }
  }
  }
  else if($prontuario!="")
  {
      $sql = "select num_prontuario, paciente_id_paciente from prontuario where substring(num_prontuario,1,15)= '$prontuario' and unidade_id_unidade = $id_unidade_sistema";
      $prontuario = mysqli_query($db, $sql);

      if (mysqli_num_rows($prontuario) > 0)
      {
         while($lista = mysqli_fetch_object($prontuario))
         {
            $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
         }

        $ids_pacientes = substr($ids_pacientes, 0, -1);

        $sql = "select id_paciente, nome, data_nasc, nome_mae, nome_logradouro from paciente where status_2='A' and id_paciente in ($ids_pacientes)";
        $obj = mysqli_query($db, $sql);
      }


      else if($nome_sem_esp!="")
      {
          $sql = "select pac.id_paciente, pac.nome, pac.data_nasc, pac.nome_mae, pac.tipo_logradouro,
                  pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, stp.descricao
                  from paciente pac
                  inner join status_paciente stp on pac.id_status_paciente = stp.id_status_paciente
                  where pac.status_2 = 'A'
                  and pac.nome_mae_nasc like '".trim($nome_sem_esp)."%'";


          if(isset($data_nasc) && $data_nasc != "")
          {
            $sql .= " and pac.data_nasc = '$data'";
          }

          if(isset($mae_sem_esp) && $mae_sem_esp != "")
          {
            $sql .= " and pac.nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
          }
          $tag = "03";
          $sql.=" order by pac.nome_mae_nasc";
          $obj = mysqli_query($db, $sql);
     }
 }
 else if($nome_sem_esp!="")
 {
      $sql = "select pac.id_paciente, pac.nome, pac.data_nasc, pac.nome_mae, pac.tipo_logradouro,
              pac.nome_logradouro, pac.numero, pac.complemento, pac.bairro, stp.descricao
              from paciente pac
              inner join status_paciente stp on pac.id_status_paciente = stp.id_status_paciente
              where pac.status_2 = 'A'
              and pac.nome_mae_nasc like '".trim($nome_sem_esp)."%'";

      if(isset($data_nasc) && $data_nasc != "")
      {
        $sql .= " and pac.data_nasc = '$data'";
      }

      if(isset($mae_sem_esp) && $mae_sem_esp != "")
      {
        $sql .= " and pac.nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
      }
      $tag = "03";
      $sql.=" order by pac.nome_mae_nasc";

      $obj = mysqli_query($db, $sql);
}


    if ($sql != '')
    {
      $res = mysqli_query($db, $sql);
      erro_sql("Pesquisa Paciente", $db, "");
      //erro("fgfdgf", erro, msg);

      if (mysqli_error($db))
      {
        $msg_erro = "Não foi possivel efetuar a operação! \\nErro número: ";
        ?>
         <script>
           alert("<?=$msg_erro.mysqli_errno()."\\n".mysqli_error()."\\n SQL nr:".$tag?>");
         </script>
        <?
      }
      else
      {
        if (($_POST[nome] != "") or ($_POST[cartao_sus] != "") or ($_POST[prontuario] != "") or ($_POST[cpf] != ""))
        {
          if (mysqli_num_rows($res) == 0)
          {
            $pesq = "f";
          }
        }
      }
    }
  }
?>

<html>
  <head>
    <title> Pesquisa de Paciente </title>
  </head>
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">
</html>
<head>
 <BASE target="_self">
</head>
<script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
<script language="javascript">
  <!--
  function preencheCampos(id)
  {
    var args = id;
	if (window.showModalDialog)
	{
		var _R = new Object()
        _R.strArgs=args;
		window.returnValue=_R;
	}
	else
	{
		if (window.opener.SetNamePaciente)
		{
			window.opener.SetNamePaciente(args);
		}
	}
    if (id!='limpar')
    {
       window.close();
    }
  }
  
  function validarCampos(){
    var cartao = document.getElementById('cartao_sus').value;
    var nome = document.getElementById('nome').value;
    var cpf = document.getElementById('cpf').value;
    var prontuario =document.getElementById('prontuario').value;
    
    var nome_aux=nome.split(" ");
    var pos = nome_aux.length;
    if(cartao=="" && nome=="" && cpf=="" && prontuario==""){
      window.alert("É necessário digitar o nome, cartão sus, prontuário ou cpf!");
      document.form_pesquisa.cartao_sus.focus();
      return false;
    }
    
    if (nome!='')
    {
        if ((nome_aux[1]==undefined)||(nome_aux[pos-1]=='')){
             if(confirm('Você informou apenas um nome. Esta consulta poderá demorar muito tempo.Tem certeza que deseja continuar?'))
             {
               document.form_pesquisa.submit();
               return true;
             }
        }
        else {
            document.form_pesquisa.submit();
            return true;
        }
    }
  }
  
  //-->
  
</script>
<body>
  <table border="1" cellspacing="1" cellpadding="0" width="100%" height="100%">
    <form name="form_pesquisa" action="pesquisa_paciente.php" method="POST" enctype="application/x-www-form-urlencoded">
      <tr>
        <td>
          <table border="0" cellspacing="1" cellpadding="0" width="100%" height="100%">
            <tr class="titulo_tabela">
              <td colspan="5" valign="middle" align="center" width="100%" height="21">Pesquisar Paciente</td>
            </tr>
            <tr>
              <td class="descricao_campo_tabela" align="left" width="18%"><IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Cartão SUS</td>
              <td class="campo_tabela" align="left" width="35%"><input type="text" name="cartao_sus" id="cartao_sus" size="15" maxlength="15" onkeypress="return isNumberKey(event);" onblur="return verificarNumero(this);"></td>
              <td class="descricao_campo_tabela" valign="middle" width="20%"><IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Prontuário</td>
              <td class="campo_tabela" valign="middle" width="25%" colspan="2"><input type="text" name="prontuario" id="prontuario" size="15"  maxlength="15"></td>
            </tr>
            <tr>
              <td class="descricao_campo_tabela" align="left" width="20%"><IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Nome</td>
              <td class="campo_tabela" align="left" width="40%"><input type="text" name="nome" id="nome" size="100" style="width: 302px" maxlength="50" ></td>
              <td class="descricao_campo_tabela" valign="middle" width="20%"><IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>CPF</td>
              <td class="campo_tabela" valign="middle" width="25%" colspan="2"><input type="text" name="cpf" id="cpf" size="15"  onKeyPress="return numbers(event);" maxlength="15"></td>
            </tr>
            <tr>
              <td class="descricao_campo_tabela" align="left" width="15%"><IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Nome da Mãe</td>
              <td class="campo_tabela" align="left" width="40%"><input type="text" name="nome_mae" id="nome_mae" size="100" style="width: 302px" maxlength="100" ></td>
              <td class="descricao_campo_tabela" align="left" width="20%"><IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Data Nascimento</td>
              <td class="campo_tabela" align="left" width="20%" colspan="2"><input type="text" name="dt_nasc" id="dt_nasc" size="8" maxlength="10" onKeyPress="return mascara_data(event,this)" onblur="verificaData(this,this.value);"></td>
            </tr>
            <tr class="campo_botao_tabela" align="center">
              <td colspan="4">&nbsp;</td>
              <td><input type="button" id="salvar" name="salvar" value=" Pesquisar " onclick="validarCampos();">
              <input type="hidden" name="pesquisa" id="pesquisa" value="pesquisar"></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table border="0" cellspacing="1" cellpadding="0"  width="100%">
            <tr class="coluna_tabela" height="21">
              <td align="center" width="40%"> Nome </td>
              <td align="center" width="12%"> Data Nascimento </td>
              <td align="center" width="20%"> Nome da Mãe </td>
              <td align="center" width="20%"> Endereço </td>
              <td align="center" width="8%"> Seleção </td>
            </tr>
<?php
          if($res <> '')
          {
            $cor_linha = "#CCCCCC";
            ///////////////////////////////////////
            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
            ///////////////////////////////////////
            while ($consulta = mysqli_fetch_object($res))
            {
              $data_nasc = ((substr($consulta->data_nasc,8,2))."/".(substr($consulta->data_nasc,5,2))."/".(substr($consulta->data_nasc,0,4)));
?>
              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                <td><?php echo $consulta->nome;?></td>
                <td><?php echo $data_nasc;?></td>
                <td><?php echo $consulta->nome_mae;?></td>
                <?
                  $endereco = $consulta->tipo_logradouro;
                  $endereco .= " ".$consulta->nome_logradouro;
                  $endereco .= ", nr ".$consulta->numero;
                  $endereco .= ", ".$consulta->complemento;
                  $endereco .= ", ".$consulta->bairro;
                ?>
                <td><?php echo $endereco;?></td>
                <td align="center"><input type="radio" name="selecao" onclick="preencheCampos('<?php echo $consulta->id_paciente.';'.$consulta->nome;?>', '<?php echo $consulta->nome;?>', '<?php echo $consulta->descricao;?>');window.close();"></td>
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
if(isset($pesq)=='f')
{
  echo "<script>window.alert('Paciente não cadastrado!')</script>";
}
?>
