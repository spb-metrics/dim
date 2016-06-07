<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

 session_start();

   header("Cache-Control: no-cache, must-revalidate");
   header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

   $configuracao = "../config/config.inc.php";
   if (!file_exists($configuracao))
   {
     exit("Não existe arquivo de configuração!");
   }
   require ($configuracao);

   $dados_salvar = $_GET[dados_salvar];
   if($_GET[id_login]==""){
     $id_usuario_sistema=$_SESSION[id_usuario_sistema];
   }
   else{
     $id_usuario_sistema = $_GET[id_login];
   }
   $id_unidade_sistema = $_SESSION[id_unidade_sistema];
   $nome_paciente = $_GET[nome];
   $qtde_itens = $_GET[itens];
   
   $num_controle = $_GET[num_controle];

   $vet_linha = split('[|]',$dados_salvar);

   for($cont=0;$cont<count($vet_linha);$cont++)
   {
        $vet_dados[$cont] = split('[,]',$vet_linha[$cont]);
   }

   $id_receita_siga       = $vet_dados[0][0];//0-id_receita


  //verificar se está havendo duplicação via brower
  $sql = "select id_movto_geral
          from
                 movto_geral
          where
                 num_controle = '$num_controle'";
  $rec_dupl = mysqli_query($db, $sql);
  if (mysqli_num_rows($rec_dupl)<>0)
  {
   //duplicação do browser
     ////////// Email
   $email="saude.ima@ima.sp.gov.br";
   $email_dest="saude.ima@ima.sp.gov.br";

   $headers = "From: Duplicacao Browser Producao(Completar Receita) <".$email.">\n";

   $msg ="ID da Unidade que gerou: ". $id_unidade_sistema."\n";
   $msg .="Horario: ".date('d-m-Y H:i:s'). "\n";
   $msg .="ID Receita: ".$id_receita. "\n";

   mail($email_dest, "Duplicacao Browser Producao(Completar Receita) ", $msg, $headers);
   echo 'duplicacao_browser';
  }
  else
  {
    //salvar receita
    $str_receita="select paciente_id_paciente,
                         profissional_id_profissional,
                         unidade_id_unidade,
                         data_emissao,
                         usua_incl,
                         data_incl
                  from
                         receita_siga
                  where
                         id_receita='$id_receita_siga'";
    $sql_receita=mysqli_query($db, $str_receita);
    if(mysqli_errno($db)!=0){
      $desc_erro=str_replace("'", "´", mysqli_error($db));
      echo $desc_erro;
      exit;
    }
    $res_receita=mysqli_fetch_object($sql_receita);

    //obtem numero da receita
    $ano=date("Y");
    $str_numero="select max(numero) as numero
                 from
                        receita
                 where
                        ano = '$ano'
                        and unidade_id_unidade = '$res_receita->unidade_id_unidade'";
    $sql_numero=mysqli_query($db, $str_numero);
    if(mysqli_errno($db)!=0){
      $desc_erro=str_replace("'", "´", mysqli_error($db));
      echo $desc_erro;
      exit;
    }
    $numero=mysqli_fetch_object($sql_numero);
    $numero_receita=$numero->numero;
    if(($numero_receita)==''){
      $numero_receita=1;
    }
    else{
      $numero_receita=$numero->numero+1;
    }
    $numero=$numero_receita;

    //obtem id da cidade
    $str_cidade="select cidade_id_cidade
                 from
                        parametro";
    $sql_cidade=mysqli_query($db, $str_cidade);
    if(mysqli_errno($db)!=0){
      $desc_erro=str_replace("'", "´", mysqli_error($db));
      echo $desc_erro;
      exit;
    }
    $res_cidade=mysqli_fetch_object($sql_cidade);

    //insere receita
    $data=date("Y-m-d H:i:s");
    //estranho!!!!
    $insere_receita="insert into receita (
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
                            usua_alt,
                            data_alt,
                            data_ult_disp)
                     values(
                            '$res_receita->unidade_id_unidade',
                            '$res_cidade->cidade_id_cidade',
                            '$res_receita->profissional_id_profissional',
                            '$res_receita->paciente_id_paciente',
                            '67',
                            '$ano',
                            '$numero',
                            '$res_receita->data_emissao',
                            '$res_receita->usua_incl',
                            '$res_receita->data_incl',
                            '$id_usuario_sistema',
                            '$data',
                            '$data')";
    mysqli_query($db, $insere_receita);
    if(mysqli_errno($db)!=0){
      mysqli_rollback($db);
      $desc_erro=str_replace("'", "´", mysqli_error($db));
      echo $desc_erro;
      exit;
    }

    //insere itens da receita
    $str_itens="select material_id_material,
                       qtde_prescrita,
                       tempo_tratamento
                from
                       item_receita_siga
                where
                       receita_id_receita='$id_receita_siga'";
    $sql_itens=mysqli_query($db, $str_itens);
    if(mysqli_errno($db)!=0){
      $desc_erro=str_replace("'", "´", mysqli_error($db));
      echo $desc_erro;
      exit;
    }

    //obtem id da receita
    $str_id_receita = "select max(id_receita) as id_receita
                       from
                              receita";
    $receita = mysqli_fetch_object(mysqli_query($db, $str_id_receita));
    $id_receita = $receita->id_receita;

    while($res_itens=mysqli_fetch_object($sql_itens)){
      //verifica se profissional pode dispensar material
      $str_material_autorizado="select p.id_profissional
                                from
                                       material_prescritor as mp,
                                       profissional as p
                                where
                                      p.id_profissional='$res_receita->profissional_id_profissional'
                                      and p.tipo_prescritor_id_tipo_prescritor=mp.tipo_prescritor_id_tipo_prescritor";
      $sql_material_autorizado=mysqli_query($db, $str_material_autorizado);
      if(mysqli_errno($db)!=0){
        $desc_erro=str_replace("'", "´", mysqli_error($db));
        echo $desc_erro;
        exit;
      }
      if(mysqli_num_rows($sql_material_autorizado)>0){
        //verifica se profissional pode dispensar material especifico
        $str_material_especifico_autorizado="select p.id_profissional
                                             from
                                                    material_prescritor as mp,
                                                    profissional as p
                                             where
                                                    mp.material_id_material='$res_itens->material_id_material'
                                                    and p.id_profissional='$res_receita->profissional_id_profissional'
                                                    and p.tipo_prescritor_id_tipo_prescritor=mp.tipo_prescritor_id_tipo_prescritor";
        $sql_material_especifico_autorizado=mysqli_query($db, $str_material_especifico_autorizado);
        if(mysqli_errno($db)!=0){
          $desc_erro=str_replace("'", "´", mysqli_error($db));
          echo $desc_erro;
          exit;
        }
        if(mysqli_num_rows($sql_material_especifico_autorizado)>0){
          $insere_itens="insert into itens_receita(
                                material_id_material,
                                receita_id_receita,
                                qtde_prescrita,
                                tempo_tratamento,
                                qtde_disp_anterior)
                         values(
                                '$res_itens->material_id_material',
                                '$id_receita',
                                '$res_itens->qtde_prescrita',
                                '$res_itens->tempo_tratamento',
                                '0')";
          mysqli_query($db, $insere_itens);
          if(mysqli_errno($db)!=0){
            mysqli_rollback($db);
            $desc_erro=str_replace("'", "´", mysqli_error($db));
            echo $desc_erro;
            exit;
          }
        }
      }
      else{
        $insere_itens="insert into itens_receita(
                              material_id_material,
                              receita_id_receita,
                              qtde_prescrita,
                              tempo_tratamento,
                              qtde_disp_anterior)
                       values(
                              '$res_itens->material_id_material',
                              '$id_receita',
                              '$res_itens->qtde_prescrita',
                              '$res_itens->tempo_tratamento',
                              '0')";
        mysqli_query($db, $insere_itens);
        if(mysqli_errno($db)!=0){
          mysqli_rollback($db);
          $desc_erro=str_replace("'", "´", mysqli_error($db));
          echo $desc_erro;
          exit;
        }
      }
    }

   for($cont=0;$cont<count($vet_dados);$cont++)
   {
//        $id_receita       = $vet_dados[$cont][0];//0-id_receita
        $id_paciente      = $vet_dados[$cont][1];//1-id_paciente
        $num_doc_c        = $vet_dados[$cont][2];//2-num_doc
//        $id_itens_receita = $vet_dados[$cont][3];//3-id_itens_receita
        $id_estoque       = $vet_dados[$cont][4];//4-id_estoque
        $material         = $vet_dados[$cont][5];//5-id_material
        $rec_controlada   = $vet_dados[$cont][6];//6-rec_controlada
        $qtde_prescrita   = $vet_dados[$cont][7];//7-qtde_prescrita
        $qtde_anterior    = $vet_dados[$cont][8];//8-qtde_anterior
        $flg_autorizacao  = $vet_dados[$cont][9];//9-flg_autorizacao
        $qtd_total        = $vet_dados[$cont][10];//10-qtd_total   no mes
        $qtd_lote         = $vet_dados[$cont][11];//11-qtd_lote
        $usuario_autorizador = $vet_dados[$cont][12];//12-autorizador

        if ($usuario_autorizador=='0' ||$usuario_autorizador=='')
        {
        $usuario_autorizador = 'null';
        }
        if ($qtd_lote > 0)
        {
        $num_doc=split('[-]',$vet_dados[$cont][2]);
        //0-ano
        //1-unidade
        //2-numero

        $ano     = $num_doc[0];
//        $numero  = $num_doc[2];
        $unidade = $num_doc[1];

        if($id_receita!="")
        {

          //atualizar quantidade dispensada no mes e data ultima dispensacao
          $update_itens="update itens_receita
                         set
                                qtde_prescrita='$qtde_prescrita',
                                data_ult_disp='$data',
                                qtde_disp_mes='$qtd_lote'
                         where
                                receita_id_receita='$id_receita' and
                                material_id_material='$material'";
          mysqli_query($db, $update_itens);
          if(mysqli_errno($db)!=0){
            mysqli_rollback($db);
            $desc_erro=str_replace("'", "´", mysqli_error($db));
            echo $desc_erro;
            exit;
          }
          
          //obtem id do item da receita
          $str_id_item="select id_itens_receita
                        from
                               itens_receita
                        where
                               receita_id_receita='$id_receita'
                               and material_id_material='$material'";
          $sql_id_item=mysqli_query($db, $str_id_item);
          if(mysqli_errno($db)!=0){
            mysqli_rollback($db);
            $desc_erro=str_replace("'", "´", mysqli_error($db));
            echo $desc_erro;
            exit;
          }
          $res_id_itens=mysqli_fetch_object($sql_id_item);
          $id_itens_receita=$res_id_itens->id_itens_receita;

            //salvar movimentação
             if (!isset($id_movto_geral))
             {
                 $sql="insert into movto_geral(
                              tipo_movto_id_tipo_movto,
                              usuario_id_usuario,
                              unidade_id_unidade,
                              receita_id_receita,
                              paciente_id_paciente,
                              num_documento,
                              data_movto,
                              data_incl, num_controle)
                       values(
                              '3',
                              '$id_usuario_sistema',
                              '$id_unidade_sistema',
                              '$id_receita',
                              '$id_paciente',
                              '$ano"."-"."$unidade"."-"."$numero', '";
                              date("Y-m-d H:i:s")."', '";
                              date("Y-m-d H:i:s")."',
                              '$num_controle')";
                 mysqli_query($db, $sql);
                 $sql = "select max(id_movto_geral) as id_movto_geral
                         from
                                movto_geral";
                 $movto_geral = mysqli_fetch_object(mysqli_query($db, $sql));
                 $id_movto_geral = $movto_geral->id_movto_geral;
             }
             // buscar lotes
             $sql1 = "select material_id_material, fabricante_id_fabricante, lote, validade
                      from
                             estoque
                      where
                             id_estoque = $id_estoque";
             $lotes = mysqli_fetch_object(mysqli_query($db, $sql1));
             $lote = $lotes->lote;
             $id_fabricante = $lotes->fabricante_id_fabricante;
             $validade = $lotes->validade;
             $id_material = $lotes->material_id_material;

             
             //incluir em itens_movto_geral
             $sql="insert into itens_movto_geral(
                          movto_geral_id_movto_geral,
                          material_id_material,
                          fabricante_id_fabricante,
                          lote,
                          validade,
                          qtde,
                          itens_receita_id_itens_receita,
                          usuario_autorizador)
                   values(
                          $id_movto_geral,
                          $id_material,
                          $id_fabricante,
                          '$lote',
                          '$validade',
                          $qtd_lote,
                          $id_itens_receita,
                          $usuario_autorizador)";
             mysqli_query($db, $sql);
             if (mysqli_errno($db)!=0)
             {
                mysqli_rollback($db);
                echo exit;
             }
             $sql="select sum(quantidade) as saldo_anterior
                   from
                          estoque
                   where
                          material_id_material='$id_material'
                          and unidade_id_unidade='$id_unidade_sistema'";
             $res=mysqli_query($db, $sql);

             $qtde_estoque_material=mysqli_fetch_object($res);
             $saldo_anterior=$qtde_estoque_material->saldo_anterior;

             //obtem a quantidade de material de uma unidade no estoque
             //estranho
             $sql="select e.id_estoque,
                          e.fabricante_id_fabricante,
                          e.material_id_material,
                          e.unidade_id_unidade,
                          e.lote,
                          e.validade,
                          e.quantidade,
                          e.flg_bloqueado,
                          e.motivo_bloqueio,
                          m.descricao
                   from
                          estoque e,
                          material m
                   where
                          e.fabricante_id_fabricante='$id_fabricante'
                          and e.material_id_material='$id_material'
                          and e.unidade_id_unidade='$id_unidade_sistema'
                          and e.lote='$lote'
                          and m.id_material = e.material_id_material";
             $res=mysqli_query($db, $sql);
             if(mysqli_num_rows($res)>0)
              {
                  $estoque_info=mysqli_fetch_object($res);
                  $qtde_estoque_unidade=(int)$estoque_info->quantidade;
                  $desc_material = $estoque_info->descricao;
                  $lote_movto = $estoque_info->lote;
                  $qtde_lote = $estoque_info->quantidade;
                  $id_material_aux = $estoque_info->material_id_material;
              }
             else
              {
                  $qtde_estoque_unidade=0;
              }

              //******* verifica estoque
              if ($qtde_estoque_unidade>0)
              {
                  $qtde = $qtde_estoque_unidade - $qtd_lote;

                  if ($qtde>=0)
                  {
                       $sql="update estoque
                             set
                                    quantidade='$qtde',
                                    data_alt='".date("Y-m-d H:i:s")."',
                                    usua_alt='$id_usuario_sistema'
                             where
                                    fabricante_id_fabricante='$id_fabricante'
                                    and material_id_material='$id_material'
                                    and unidade_id_unidade='$id_unidade_sistema'
                                    and lote='$lote'";
                       mysqli_query($db, $sql);
                       if (mysqli_errno($db)!=0)
                       {
                          $desc_erro = str_replace("'", "´", mysqli_error($db));
                          if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
                          {
                           $desc_erro = "Erro na atualização do estoque";
                          }
                          mysqli_rollback($db);
                          echo $desc_erro;
                          exit;
                       }
                    }
                    else
                    {
                     $desc_erro = "*".$id_material_aux."|Erro Quantidade insuficiente em estoque.\nMedicamento: ".$desc_material."\nLote: ".$lote_movto. " quantidade ".$qtde_lote. "\n Selecione o lote e coloque a quantidade novamente!";
                     mysqli_rollback($db);
                     echo $desc_erro;
                     echo exit;
                    }
               }
               else
               {
                $desc_erro = "*".$id_material_aux."|Erro Quantidade insuficiente em estoque.\nMedicamento: ".$desc_material."\nLote: ".$lote_movto." quantidade ".$qtde_lote."\nExclua o medicamento e o adicione novamente!";
                mysqli_rollback($db);
                echo $desc_erro;
                echo exit;
               }

              //obtem o saldo atual de um material no estoque
              $sql="select sum(quantidade) as saldo_atual
                    from
                           estoque
                    where
                           material_id_material='$id_material'
                           and unidade_id_unidade='$id_unidade_sistema'";
              $res=mysqli_query($db, $sql);
              $qtde_estoque_material=mysqli_fetch_object($res);
              $saldo_atual=$qtde_estoque_material->saldo_atual;
              //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
              //Verifica se eh insercao ou atualizacao
              $sql="select qtde_saida
                    from
                           movto_livro
                    where
                           movto_geral_id_movto_geral='$id_movto_geral'
                           and unidade_id_unidade='$id_unidade_sistema'
                           and material_id_material='$id_material'";
              $res=mysqli_query($db, $sql);

              if(mysqli_num_rows($res)>0)
              {
                  //atualizando o movimento do livro
                  $livro_info=mysqli_fetch_object($res);
                  $qtde=(int)$livro_info->qtde_saida+(int)$qtd_lote;
                  $sql="update movto_livro
                        set
                               qtde_saida='$qtde',
                               saldo_atual='$saldo_atual'
                        where
                               movto_geral_id_movto_geral='$id_movto_geral'
                               and unidade_id_unidade='$id_unidade_sistema'
                               and material_id_material='$id_material'";
              }
              else
              {
                  $sql = "select num_receita_controlada
                          from
                                 itens_receita
                          where
                                 receita_id_receita = '$id_receita'
                                 and material_id_material = '$id_material'";
                  $res=mysqli_query($db, $sql);

                  $res_item=mysqli_fetch_object($res);
                  if ($res_item->num_receita_controlada=="")
                  {
                     $history=$nome_paciente . " Nº da Receita: " . $ano . "-" . $unidade . "-" . $numero;
                  }
                  else
                  {
                      $history=$nome_paciente . " Nº da Receita: " . $ano . "-" . $unidade . "-" . $numero  . " NR: " . $res_item->num_receita_controlada;
                  }

                  $sql="insert into movto_livro(
                               movto_geral_id_movto_geral,
                               unidade_id_unidade,
                               material_id_material,
                               tipo_movto_id_tipo_movto,
                               saldo_anterior,
                               qtde_saida,
                               saldo_atual,
                               data_movto,
                               historico)
                        values(
                               '$id_movto_geral',
                               '$id_unidade_sistema',
                               '$id_material',
                               '3',
                               '$saldo_anterior',
                               '$qtd_lote',
                               '$saldo_atual', '".
                               date("Y-m-d H:i:s")."', '".
                               strtoupper($history)."')";
              }
             mysqli_query($db, $sql);
             if (mysqli_errno($db)!=0)
             {
                  mysqli_rollback($db);
                  echo exit;
             }
     } //if(id_receita!"")
    } //if ($qtd_lote > 0)
  }

 //verificando se receita está finalizada
        if($flg_receita==0)
        {
            $desc_status = "FINALIZADA";
            $sql = "select qtde_prescrita, qtde_disp_mes, qtde_disp_anterior
                    from
                           itens_receita
                    where
                           receita_id_receita = '$id_receita'";
            $status_receita = mysqli_query($db, $sql);
            while ($ver_status_receita = mysqli_fetch_object($status_receita))
            {
                if ($ver_status_receita->qtde_prescrita <> ($ver_status_receita->qtde_disp_mes+$ver_status_receita->qtde_disp_anterior))
                {
                    $desc_status = "ABERTA";
                }
            }
            $sql ="update receita
                   set
                          status_2 = '$desc_status',
                          usua_alt = '$id_usuario_sistema',
                          data_alt = '".date("Y-m-d H:i:s")."'
                   where
                        id_receita = '$id_receita'";
            mysqli_query($db, $sql);
            if (mysqli_errno($db)!=0)
            {
            mysqli_rollback($db);
            echo exit;
            }
        }

     //apaga receita do siga
     $delete_receita="delete
                      from
                            receita_siga
                      where
                            id_receita='$id_receita_siga'";
     mysqli_query($db, $delete_receita);
     if(mysqli_errno($db)!=0){
       mysqli_rollback($db);
       $desc_erro=str_replace("'", "´", mysqli_error($db));
       echo $desc_erro;
       exit;
     }
     
     //apaga itens da receita do siga
     $delete_itens="delete
                    from
                          item_receita_siga
                    where
                          receita_id_receita='$id_receita_siga'";
     mysqli_query($db, $delete_itens);
     if(mysqli_errno($db)!=0){
       mysqli_rollback($db);
       $desc_erro=str_replace("'", "´", mysqli_error($db));
       echo $desc_erro;
       exit;
     }

     mysqli_commit($db);
     echo "IdMovto-".$id_movto_geral."-".$numero;
    } ///
 ?>
