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
   $aux_mat=0;
   $num_controle = $_GET[num_controle];

   $vet_linha = split('[|]',$dados_salvar);

   for($cont=0;$cont<count($vet_linha);$cont++)
   {
        $vet_dados[$cont] = split('[,]',$vet_linha[$cont]);
   }

   $id_receita       = $vet_dados[0][0];//0-id_receita

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
    // fazer select dos itens do movto
    $flag_receita = 'v';

    $sql_movto ="select max(id_movto_geral) as id_movto
                 from
                        movto_geral
                 where
                        receita_id_receita = $id_receita";
    $itens_movto = mysqli_query($db, $sql_movto);
    $movto = mysqli_fetch_object($itens_movto);
    $id_movto = $movto->id_movto;

    $sql_itens= "select count(*) as qtos
                 from
                        itens_movto_geral
                 where
                        movto_geral_id_movto_geral=$id_movto";
    $itens_movto = mysqli_query($db, $sql_itens);
    $total_itens = mysqli_fetch_object($itens_movto);
    $qtde_itens_movto= $total_itens->qtos;

    if ($qtde_itens == $qtde_itens_movto)
    {
        for ($cont=0;$cont<count($vet_dados);$cont++)
        {
           $id_itens_receita = $vet_dados[$cont][3];//3-id_itens_receita
           $qtd_total        = $vet_dados[$cont][10];//10-qtd_total   no mes

           if ($qtd_total!=0)
           {
            $sql = "select timediff(now(),m.data_incl) as intervalo,
                           if(i.qtde=$qtd_total,'igual', 'difer') as valor
                    from
                           movto_geral m, itens_movto_geral i
                    where
                           m.id_movto_geral = $id_movto
                           and i.itens_receita_id_itens_receita = $id_itens_receita
                           and i.movto_geral_id_movto_geral = m.id_movto_geral";

            $valida_receita = mysqli_query($db, $sql);
            $det_receita = mysqli_fetch_object($valida_receita);

            if ($det_receita->valor=='difer')
            {
               $flag_receita='v';
               break;
            }
            else if (($det_receita->intervalo<='01:00:00')&&($det_receita->valor=='igual'))
            {
               $flag_receita='f';
            }
          }
        }
    }
//echo $flag_receita;
    if ($flag_receita == 'v')
    {
//  echo "GRAVANDO TUDO";

   for($cont=0;$cont<count($vet_dados);$cont++)
   {
        $id_receita       = $vet_dados[$cont][0];//0-id_receita
        $id_paciente      = $vet_dados[$cont][1];//1-id_paciente
        $num_doc_c        = $vet_dados[$cont][2];//2-num_doc
        $id_itens_receita = $vet_dados[$cont][3];//3-id_itens_receita
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
        $numero  = $num_doc[2];
        $unidade = $num_doc[1];

        $alt_controlada = 0;

        $atualizacao = "";

        if($id_receita!="")
        {
          $flg_update = 0;

          // quantidade dispensada no movimento anterior
          
          if($aux_mat!=$material)
          {
             $sql_qtde = "select sum(i.qtde) as quantidade, m.id_movto_geral
                          from
                                 itens_movto_geral i,
                                 movto_geral m
                          where
                                 m.receita_id_receita = $id_receita
                                 and material_id_material = $material
                                 and i.movto_geral_id_movto_geral= m.id_movto_geral
                          group by
                                 m.id_movto_geral
                          order by
                                 m.id_movto_geral desc
                          limit 1";
             $aux_mat=$material;
             $q_qtde = mysqli_query($db, $sql_qtde);

             $obj_qtde = mysqli_fetch_object($q_qtde);

             $qtde_movto_anterior = (int)$obj_qtde->quantidade;
          }

          if (intVal($qtde_prescrita) - (intVal($qtde_anterior)+intVal($qtd_total))<0)
          {
             $qtd_total = 0;
			 
          }
          else  if (intVal($qtde_prescrita) - (intVal($qtde_anterior)+intVal($qtd_total))>=0)
          {
            if ($qtd_total!=0)
            {  //verifico se há em estoque
            
            //estranho !!!!
                $sql = "select id_estoque
                        from
                             estoque
                        where
                             material_id_material = '".$material."'
                             and unidade_id_unidade = '".$id_unidade_sistema."'
                             and quantidade > 0
                             and (flg_bloqueado is null or flg_bloqueado = '')
                             and validade >'".date("Y-m-d")."'";
                $lote_verifica = mysqli_query($db, $sql);
            }

			if($flag_receita_existe==0)
            {
			

					if (intVal($qtde_prescrita) <= (intVal($qtde_anterior)+intVal($qtd_total))) {			
						$dt_fim_receita=date("Y-m-d H:i:s"); 
						$status = "FINALIZADO";
						//$ds_observacao="Completar Receita";																			
					}
					else {
						$dt_fim_receita=NULL; 
						$status = "ABERTO";
						$ab = true;
						//$id_usuario_sistema=null;
					}
			

                $sql = "update itens_receita
                        set
                               qtde_disp_anterior =  '".trim($qtde_anterior)."',
                               qtde_disp_mes = '".trim($qtd_total)."',
                               data_ult_disp = '".date("Y-m-d H:i:s")."',
                               num_receita_controlada = '".trim($rec_controlada)."',";							   
							   
				if (is_null($dt_fim_receita)){							  
					$sql = $sql . "dt_fim_receita = null,";
				}else
				{
					$sql = $sql . "dt_fim_receita = '$dt_fim_receita',";
				}
				
				$sql = $sql ." status = '$status',";
				
				
				if($ab == true){
					$sql = $sql ."id_usua_fim_receita = null";

				}else{
						$sql = $sql ."id_usua_fim_receita = '$id_usuario_sistema'";
				}
				
							$sql = $sql."
						
							   
                        where
                               id_itens_receita = '".trim($id_itens_receita)."'";
							   //echo $sql;
							  //exit;
							   //,
							  //quando  na rotina de completar receita ou dispensação finalizava o item, estava colocando o motivo , segundo a Ana é pra deixar em branco
							  
								// motivo_fim_receita_id_motivo_fim_receita = '$moti'  
						

			mysqli_query($db, $sql);
                if (mysqli_errno($db)!=0)
                {
                   mysqli_rollback($db);
                   echo exit;
                }
                $flg_update = 1;
        
                if ($flg_update ==1)
                {
                   $sql ="update receita
                          set
                                 data_ult_disp = '".date("Y-m-d H:i:s")."'
                          where
                                 id_receita = '$id_receita'";
                   mysqli_query($db, $sql);
                   if (mysqli_errno($db)!=0)
                   {
                      mysqli_rollback($db);
                      echo exit;
                   }

                   //salvar movimentação
                   if (!isset($id_movto_geral))
                   {
                         $aux ="";
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
                                      '$ano"."-"."$unidade"."-"."$numero', '".
                                      date("Y-m-d H:i:s")."', '".
                                      date("Y-m-d H:i:s")."',
                                      '$num_controle')";
                         mysqli_query($db, $sql);
                         $sql = "select max(id_movto_geral) as id_movto_geral
                                 from
                                        movto_geral";
                         $movto_geral = mysqli_fetch_object(mysqli_query($db, $sql));
                         $id_movto_geral = $movto_geral->id_movto_geral;
                   }
             $sql1 = "select material_id_material, fabricante_id_fabricante,
                             lote, validade
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
                          qtde_disp_anterior,
                          qtde,
                          itens_receita_id_itens_receita,
                          usuario_autorizador)
                   values(
                          $id_movto_geral,
                          $id_material,
                          $id_fabricante,
                          '$lote',
                          '$validade',
                          $qtde_movto_anterior,
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
             
             //estranho!!!!

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
                          and e.material_id_material = m.id_material";
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
              $sql="select id_movto_livro
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
                               '$id_material', '3',
                               '$saldo_anterior',
                               '$qtd_lote',
                               '$saldo_atual', '".
                               date("Y-m-d H:i:s")."', '".
                               strtoupper($history) . "')";
              }
             mysqli_query($db, $sql);
             if (mysqli_errno($db)!=0)
             {
                  mysqli_rollback($db);
                  echo exit;
             }
         }//if ($flg-update)
       }
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
						//Quando utilizava o completar receita, estava colocando sempre 1 mesmo nao finalizando o tem   
						  //motivo_fim_receita_id_motivo_fim_receita = 1
            mysqli_query($db, $sql);
            if (mysqli_errno($db)!=0)
            {
            mysqli_rollback($db);
            echo exit;
            }
        }
     mysqli_commit($db);
     echo "IdMovto-".$id_movto_geral;
    }
    else
    {
     echo "duplicacao_usuario";
    }
    } ///
 ?>
