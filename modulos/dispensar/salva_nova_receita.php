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
    //salvar receita
    $sql = "select max(numero) as numero from receita
            where ano = '$_POST[ano]'
            and unidade_id_unidade = '$_SESSION[id_unidade_sistema]'";
    //echo $sql."<br>";
    $sql_numero = mysqli_query($db, $sql);
    $numero = mysqli_fetch_object($sql_numero);
    $numero_receita = $numero->numero;
    if (($numero_receita)=='')
    {
     $numero_receita = 1;
    }
    else
    {
     $numero_receita = $numero->numero + 1;
    }

    //salvar receita
    $sql = "insert into receita (
         unidade_id_unidade,
         cidade_id_cidade,
         profissional_id_profissional,
         paciente_id_paciente,
         subgrupo_origem_id_subgrupo_origem,
         ano,
         numero,
         data_emissao,
         usua_incl,
         data_incl,
         status_2)
         values (
         '$_SESSION[id_unidade_sistema]',
         '$_POST[id_cidade_receita]',
         '$_POST[id_prescritor]',
	     '$_POST[id_paciente]',
	     '$_POST[origem_receita]',
	     '$_POST[ano]',
	     '$numero_receita',
	     '".substr($_POST[data_emissao],-4)."-".substr($_POST[data_emissao],3,2)."-".substr($_POST[data_emissao],0,2)."',
         '$_SESSION[id_usuario_sistema]',
	     '".date("Y-m-d H:i:s")."',
	     'ABERTA')";
    mysqli_query($db, $sql);
    //echo $sql."<br>";
    if (mysqli_errno($db)==0)
    {
     $sql = "select max(id_receita) as id_receita from receita";
     //echo $sql."<br>";
     $receita = mysqli_fetch_object(mysqli_query($db, $sql));
     $id_receita = $receita->id_receita;
     //incluir em itens_receita

     $vet_item = split('[|]',$_POST[itens_receita]);
     $qtde_itens = count($vet_item);
     // 0 - id_medicamento
     // 1 - id_lote
     // 2 - lote
     // 3 - id_fabricante
     // 4 - validade
     // 5 - qtde_lote
     // 6 - qtde_prescrita
     // 7 - tempo_tratamento
     // 8 - qtde_anterior
     // 9 - qtde_dispensada
     // 10- rec_controlada
     // 11- id_autorizador

     //itens da receita
     $indice = "";
     for($i=0; $i<$qtde_itens; $i++)
     {
      $vet_info_item = split('[,]',$vet_item[$i]);

      //formatando num_receita_controlada
      if ($vet_info_item[10]=='0')
      {
       $vet_info_item[10] = '';
      }
      else
      {
       $vet_info_item[10] = strtoupper($vet_info_item[10]);
      }

      if ($vet_info_item[0]!=$indice)
      {
       $sql = "insert into itens_receita (
            material_id_material,
            receita_id_receita,
            qtde_prescrita,
            tempo_tratamento,
            qtde_disp_anterior,
            qtde_disp_mes,
            data_ult_disp,
            num_receita_controlada)
            values (
            '$vet_info_item[0]',
            '$id_receita',
            '$vet_info_item[6]',
            '$vet_info_item[7]',
            '$vet_info_item[8]',
            '$vet_info_item[9]',
            '".date("Y-m-d H:i:s")."',
            '$vet_info_item[10]')";
       mysqli_query($db, $sql);
       //echo $sql."<br>";
       if (mysqli_errno($db)!=0)
       {
        $desc_erro = str_replace("'", "´", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na Inclusão de Itens da Receita";
        }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
         mysqli_rollback($db);
         exit;
       }
      }
      $indice = $vet_info_item[0];
     } // for itens
     
     //verificar se vai haver movimentação (id_estoque<>0)
     $grava_movto = false;
     for($i=0; $i<$qtde_itens; $i++)
     {
      $vet_movto = split('[,]',$vet_item[$i]);
      if ($vet_movto[1]!='0')
      {
       $grava_movto = true;
       break;
      }
     }
     
     if ($grava_movto)
     {
      $sql ="update receita set data_ult_disp = '".date("Y-m-d H:i:s")."' where id_receita = '$id_receita'";
      //echo $sql."<br>";
      mysqli_query($db, $sql);
      if (mysqli_errno($db)!=0)
      {
        $desc_erro = str_replace("'", "´", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na atualização na data da receita";
        }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
         mysqli_rollback($db);
         exit;

      }
     }
     
     //verificando se receita está finalizada
     $desc_status = "FINALIZADA";
     $sql = "select * from itens_receita where receita_id_receita = '$id_receita'";
     //echo $sql."<br>";
     $status_receita = mysqli_query($db, $sql);
     while ($ver_status_receita = mysqli_fetch_object($status_receita))
     {
      if ($ver_status_receita->qtde_prescrita <> $ver_status_receita->qtde_disp_mes)
      {
       $desc_status = "ABERTA";
      }
     }
     
     $sql ="update receita set status_2 = '$desc_status' where id_receita = '$id_receita'";
     //echo $sql."<br>";
     mysqli_query($db, $sql);
     if (mysqli_errno($db)!=0)
     {
        $desc_erro = str_replace("'", "´", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na atualização do status da receita";
        }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
         mysqli_rollback($db);
         exit;
     }
     
     //verifica se algum item foi dispensado
     if ($grava_movto)
     {
      $sql="insert into movto_geral ";
      $sql.="(tipo_movto_id_tipo_movto, ";
      $sql.="usuario_id_usuario, ";
      $sql.="unidade_id_unidade, ";
      $sql.="receita_id_receita, ";
      $sql.="paciente_id_paciente, ";
      $sql.="num_documento, ";
      $sql.="data_movto, ";
      $sql.="data_incl) ";
      $sql.="values ('3', ";
      $sql.="'$_SESSION[id_usuario_sistema]', ";
      $sql.="'$_SESSION[id_unidade_sistema]', ";
      $sql.="'$id_receita', '$_POST[id_paciente]', ";
      $sql.="'$ano"."-"."$_SESSION[id_unidade_sistema]"."-"."$numero_receita', '";
      $sql.= date("Y-m-d H:i:s")."', '";
      $sql.= date("Y-m-d H:i:s")."')";
      //echo $sql."<br>";
      //echo $sql;
      //echo exit;
      mysqli_query($db, $sql);

      if (mysqli_errno($db)!=0)
      {
        $desc_erro = str_replace("'", "´", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na Inclusão de Movto Geral";
        }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
         mysqli_rollback($db);
         exit;
      }
     }
     
     $sql = "select max(id_movto_geral) as id_movto_geral from movto_geral";
     //echo $sql."<br>";
     $movto_geral = mysqli_fetch_object(mysqli_query($db, $sql));
     $id_movto_geral = $movto_geral->id_movto_geral;
     //incluir em itens_movto_geral

     //movto materiais
     for($i=0; $i<$qtde_itens; $i++)
     {
      $vet_info_movto = split('[,]',$vet_item[$i]);
      
      if ($vet_info_movto[1]!='0')
      {
       //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
       //pegar id_itens_receita
       $sql = "select * from itens_receita
            where receita_id_receita = '$id_receita'
            and material_id_material = '$vet_info_movto[0]'";
       //echo $sql."<br>";
       //echo $sql;
       //echo exit;
       $item_sql=mysqli_query($db, $sql);
       $id_itemreceita = mysqli_fetch_object($item_sql);
       $id_item_receita = $id_itemreceita->id_itens_receita;
       $num_rec_controlada = $id_itemreceita->num_receita_controlada;
       
       if($vet_info_movto[11]=='')
       {
        $vet_info_movto[11]='Null';
       }

       $sql="insert into itens_movto_geral (
          movto_geral_id_movto_geral,
          material_id_material,
          fabricante_id_fabricante,
          lote,
          validade,
          qtde,
          itens_receita_id_itens_receita,
          usuario_autorizador)
          values (
          '$id_movto_geral',
          '$vet_info_movto[0]',
          '$vet_info_movto[3]',
          '$vet_info_movto[2]',
          '".substr($vet_info_movto[4],-4)."-".substr($vet_info_movto[4],3,2)."-".substr($vet_info_movto[4],0,2)."',
          '$vet_info_movto[5]',
          '$id_item_receita',
          $vet_info_movto[11])";
          //echo $sql;
          //echo exit;
       //echo $sql."<br>";
       mysqli_query($db, $sql);

       if (mysqli_errno($db)!=0)
       {
        $desc_erro = str_replace("'", "´", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na Inclusão de Itens Movto";
        }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
         mysqli_rollback($db);
         exit;
       }
       
       //obtem o saldo anterior de um material no estoque
       $saldo_anterior=0;
       $sql="select sum(quantidade) as quantidade from estoque
                    where material_id_material='$vet_info_movto[0]'
                    and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
       //echo $sql."<br>";
       $res=mysqli_query($db, $sql);
       $qtde_estoque_material=mysqli_fetch_object($res);
       if(mysqli_num_rows($res)>0)
       {
        $saldo_anterior=(int)$qtde_estoque_material->quantidade;
       }

       //obtem a quantidade de material de uma unidade no estoque
       $sql="select * from estoque ";
       $sql.="where fabricante_id_fabricante='$vet_info_movto[3]' and material_id_material='$vet_info_movto[0]' ";
       $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and lote='$vet_info_movto[2]'";
       //echo $sql."<br>";
       $res=mysqli_query($db, $sql);
       if(mysqli_num_rows($res)>0)
       {
        $estoque_info=mysqli_fetch_object($res);
        $qtde_estoque_unidade=(int)$estoque_info->quantidade;
       }
       else
       {
        $qtde_estoque_unidade=0;
       }

       //*** verifica se quantidade existente em estoque maior que zero

       if ($qtde_estoque_unidade>0)
       {
        $qtde = $qtde_estoque_unidade - (int)$vet_info_movto[5];
        $sql="update estoque ";
        $sql.="set quantidade='$qtde', data_alt='".date("Y-m-d H:i:s")."', usua_alt='$_SESSION[id_usuario_sistema]' ";
        $sql.="where fabricante_id_fabricante='$vet_info_movto[3]' and material_id_material='$vet_info_movto[0]' ";
        $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and lote='$vet_info_movto[2]'";
        //echo $sql;
        //echo exit;
        mysqli_query($db, $sql);
        //echo $sql."<br>";
        if (mysqli_errno($db)!=0)
        {
         $desc_erro = str_replace("'", "´", mysqli_error($db));
         if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
         {
          $desc_erro = "Erro na atualização do estoque";
         }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
          mysqli_rollback($db);
          exit;
        }
       }
       else
       {
        mysqli_rollback($db);
        echo exit;
       ?>
        <script>
         alert('Quantidade insuficiente em estoque');
         location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
        </script>
       <?
        exit;
       }

       //obtem o saldo atual de um material no estoque
       $saldo_atual=0;
       $sql="select sum(quantidade) as quantidade from estoque
             where material_id_material='$vet_info_movto[0]'
             and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
       //echo $sql."<br>";
       $res=mysqli_query($db, $sql);
       $qtde_estoque_material=mysqli_fetch_object($res);
       if(mysqli_num_rows($res)>0)
       {
        $saldo_atual=(int)$qtde_estoque_material->quantidade;
       }

       //Verifica se eh insercao ou atualizacao

       $sql="select * from movto_livro
                    where movto_geral_id_movto_geral='$id_movto_geral'
                    and unidade_id_unidade='$_SESSION[id_unidade_sistema]'
                    and material_id_material='$vet_info_movto[0]'";
       //echo $sql."<br>";
       //echo exit;
       $res=mysqli_query($db, $sql);
       if(mysqli_num_rows($res)>0)
       {
        //atualizando o movimento do livro
        $livro_info=mysqli_fetch_object($res);
        $qtde=(int)$livro_info->qtde_saida+(int)$vet_info_movto[5];
        $sql="update movto_livro set qtde_saida='$qtde', saldo_atual='$saldo_atual'";
        $sql.=" where movto_geral_id_movto_geral='$id_movto_geral' and ";
        $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$vet_info_movto[0]'";
        //echo $sql."<br>";
        //echo exit;
       }
       else
       {
        //insercao movimento do livro
        $sql="select * from paciente where id_paciente =$_POST[id_paciente]";
        //echo $sql."<br>";
        $res=mysqli_query($db, $sql);
        if(mysqli_num_rows($res)>0)
        {
         $mov_info=mysqli_fetch_object($res);
         $substituir = "\'";
         $paciente = ereg_replace("'", $substituir, $mov_info->nome);
        }

        if ($num_rec_controlada!="")
        {
         $history=$paciente . " Nº da Receita: " . $_POST[ano] . "-" . $_SESSION[id_unidade_sistema] . "-" . $numero_receita . " NR: " . $num_rec_controlada;
        }
        else
        {
         $history=$paciente . " Nº da Receita: " . $_POST[ano] . "-" . $_SESSION[id_unidade_sistema] . "-" . $numero_receita;
        }

        $sql="insert into movto_livro ";
        $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material, tipo_movto_id_tipo_movto, saldo_anterior, qtde_saida, saldo_atual, data_movto, historico) ";
        $sql.="values ('$id_movto_geral', '$_SESSION[id_unidade_sistema]', '$vet_info_movto[0]', '3', '$saldo_anterior', '$vet_info_movto[5]', '$saldo_atual', '".date("Y-m-d H:i:s")."', '" . strtoupper($history) . "')";
        //echo $sql."<br>";
        //echo exit;
       }
       mysqli_query($db, $sql);
       if (mysqli_errno($db)!=0)
       {
        $desc_erro = str_replace("'", "´", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na Inclusão do Movto Livro";
        }
       ?>
         <script>
          alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
          location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
          </script>
       <?
         mysqli_rollback($db);
         exit;
       }
      }//if
     }//for
     
     $sql_situacao = "select * from status_paciente where descricao like 'ATIVO' and status_2 = 'A'";
     //echo $sql_situacao."<br>";
     $res=mysqli_query($db, $sql_situacao);
     if(mysqli_num_rows($res)>0)
     {
      $situacao=mysqli_fetch_object($res);
      
      $id_situacao = $situacao->id_status_paciente;
     }
     else
     {
      $id_situacao = 0;
     }
     
     $sql_paciente = "update paciente set id_status_paciente = '$id_situacao' where id_paciente = '$_POST[id_paciente]'";
     //echo $sql_paciente."<br>";
     //mysqli_rollback($db);
     //echo exit;
     mysqli_query($db, $sql_paciente);
     
     if (mysqli_errno($db)!=0)
     {
      $desc_erro = str_replace("'", "´", mysqli_error($db));
      if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
      {
       $desc_erro = "Erro na alteração da situacao do paciente!";
      }
    ?>
      <script>
       alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
       location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
      </script>
    <?
      mysqli_rollback($db);
      exit;
     }

     mysqli_commit($db);
     //echo exit;
    ?>
    <script language="JavaScript">
     location.href="<?php echo URL.'/modulos/dispensar/consulta_receita_salva.php?id_receita='.$id_receita;?>";
    </script>
    <?
   }
   else
   {
    $desc_erro = str_replace("'", "´", mysqli_error($db));
    if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
    {
     $desc_erro = "Erro na Inclusão de Receita";
    }
    ?>
    <script>
     alert('<?php echo mysqli_errno($db) ."-". $desc_erro;?>');
     location.href="<?php echo URL.'/modulos/dispensar/inicial.php?aplicacao='.$_SESSION[DISP_INICIAL]?>";
    </script>
    <?
     mysqli_rollback($db);
     exit;
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
