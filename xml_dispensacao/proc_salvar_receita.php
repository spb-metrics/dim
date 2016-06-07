<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

    session_start();

    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

    $configuracao = "../config/config.inc.php";
    
    if (!file_exists($configuracao))
    {
      exit("N�o existe arquivo de configura��o!");
    }
    require ($configuracao);

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
      exit();
    }

    $num_controle      = $_GET[num_controle];
    $ano               = $_GET[ano];
    $unidade           = $_GET[unidade];
    $data_emissao      = $_GET[data_emissao];
    $origem            = $_GET[origem];
    $cidade            = $_GET[cidade];
    $paciente          = $_GET[paciente];
    $prescritor        = $_GET[prescritor];
    $itens_receita     = $_GET[itens_receita];

    if($_GET[id_login]==""){
      $id_usuario_sistema=$_SESSION[id_usuario_sistema];
    }
    else{
      $id_usuario_sistema = $_GET[id_login];
    }

    $lista_insuficientes = "";
    
    //verificar se est� havendo duplica��o via brower
    $sql = "select receita_id_receita
            from
                   movto_geral
            where
                   num_controle = '$num_controle'";
    $rec_dupl = mysqli_query($db, $sql);
    if (mysqli_num_rows($rec_dupl)>0)
    {
     $numero = mysqli_fetch_object($rec_dupl);
     $numero_receita = $numero->receita_id_receita;

     //duplica��o do browser
     ////////// Email
     $email="saude.ima@ima.sp.gov.br";
     $email_dest="saude.ima@ima.sp.gov.br";

     $headers = "From: Duplicacao Browser Producao(Nova Receita)<".$email.">\n";

     $msg ="ID da Unidade que gerou: ". $id_unidade_sistema."\n";
     $msg .="Horario: ".date('d-m-Y H:i:s'). "\n";
     $msg .="Receita: ".$numero_receita. "\n";
     
     mail($email_dest, "Duplicacao Browser Producao", $msg, $headers);
     echo 'duplicacao_browser';
     exit;
    }
    else
    {
     //salvar receita
     $sql = "select max(numero) as numero
             from
                    receita
             where
                    ano = '$ano'
                    and unidade_id_unidade = '$_SESSION[id_unidade_sistema]'";
     $sql_numero = mysqli_query($db, $sql);
     $numero = mysqli_fetch_object($sql_numero);
     $numero_receita = $numero->numero;
	 
	$_SESSION ['id_paciente']= $paciente; 
	 
	 
     if (($numero_receita)=='')
     {
      $numero_receita = 1;
     }
     else
     {
      $numero_receita = $numero->numero + 1;
     }

     //salvar receita
     $sql = "insert into receita(
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
             values(
                    '$_SESSION[id_unidade_sistema]',
                    '$cidade',
                    '$prescritor',
	                '$paciente',
	                '$origem',
	                '$ano',
	                '$numero_receita',
	                '".substr($data_emissao,-4)."-".substr($data_emissao,3,2)."-".substr($data_emissao,0,2)."',
                    '$id_usuario_sistema',
	                '".date("Y-m-d H:i:s")."',
	                'ABERTA')";
     mysqli_query($db, $sql);

     if (mysqli_errno($db)==0)
     {
	 
	 
	 //$_SESSION ['id_paciente']= paciente; 
	 
      $sql = "select max(id_receita) as id_receita
              from
                     receita";
      $receita = mysqli_fetch_object(mysqli_query($db, $sql));
      $id_receita = $receita->id_receita;
      //incluir em itens_receita

      $vet_item = split('[|]',$itens_receita);
      $qtde_itens = count($vet_item);
      // 0 - material
      // 1 - id_estoque
      // 2 - qtde_lote
      // 3 - qtde_prescrita
      // 4 - tempo_tratamento
      // 5 - qtde_anterior
      // 6 - qtde_dispensada
      // 7 - rec_controlada
      // 8 - id_autorizador

      //itens da receita
      $indice = "";

      for($i=0; $i<$qtde_itens; $i++)
      {
       $vet_info_item = split('[,]',$vet_item[$i]);
       if(($vet_info_item[1]!=0) && ($vet_info_item[1]!=""))
       {
        $sql1 = "select material_id_material, fabricante_id_fabricante, lote, validade
                 from
                        estoque
                 where
                        id_estoque = $vet_info_item[1]";
        $lotes = mysqli_fetch_object(mysqli_query($db, $sql1));
        $lote = $lotes->lote;
        $fabricante = $lotes->fabricante_id_fabricante;
        $validade = $lotes->validade;
        $material = $lotes->material_id_material;
        $date_incl=date("Y-m-d H:i:s");
       }
       else
       {
        $material = $vet_info_item[0];
        $date_incl="--";
       }

       //formatando num_receita_controlada
       if ($vet_info_item[7]=='0')
       {
        $vet_info_item[7] = '';
       }
       else
       {
        $vet_info_item[7] = strtoupper($vet_info_item[7]);
       }

       if ($material!=$indice)
       {
        if ($vet_info_item[4]=='0' or $vet_info_item[4]=='')
        {
         $desc_erro = "Erro Erro na Inclus�o de Itens da Receita";
         mysqli_rollback($db);
         echo $desc_erro;
         exit;
        }
		
		if ($vet_info_item[3] > $vet_info_item[6]){
			$status_itens = "ABERTO";
			$data_fim =NULL;
			$id_usuario_fim=NULL;
		}else{
			$status_itens = "FINALIZADO";
			$data_fim = date("Y-m-d H:i:s");
			$id_usuario_fim = $id_usuario_sistema;
		}
		
        $sql = "insert into itens_receita(
                       material_id_material,
                       receita_id_receita,
                       qtde_prescrita,
                       tempo_tratamento,
                       qtde_disp_anterior,
                       qtde_disp_mes,
                       data_ult_disp,
                       num_receita_controlada,
					   status,
					   dt_fim_receita,
					   id_usua_fim_receita
					   )
                values (
                       '$material',
                       '$id_receita',
                       '$vet_info_item[3]',
                       '$vet_info_item[4]',
                       '$vet_info_item[5]',
                       '$vet_info_item[6]',
                       '$date_incl',
                       '$vet_info_item[7]',
					   '$status_itens',";
					   
					   if (is_null($data_fim)){							  
						$sql = $sql . "Null,";
					   }else{
						$sql = $sql . "'$data_fim',";
					   } 				
					   
						$sql = $sql ."'$id_usuario_fim')";
				
					   //'$descricao',
					   //ds_observacao,
					   					   
					   //motivo_fim_receita_id_motivo_fim_receita,
					   //'$motivo',
        mysqli_query($db, $sql);

        if (mysqli_errno($db)!=0)
        {
         $desc_erro = "Erro Erro ".str_replace("'", "�", mysqli_error($db));
         if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
         {
          $desc_erro = "Erro Erro na Inclus�o de Itens da Receita";
         }
         mysqli_rollback($db);
         echo $desc_erro;
         exit;
        }
		
		//echo "teste";
		
       }
       $indice = $material;
      } // for itens

      //verificar se vai haver movimenta��o (id_estoque<>0)
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
       $sql ="update receita
              set
                     data_ult_disp = '".date("Y-m-d H:i:s")."'
              where
                     id_receita = '$id_receita'";
       mysqli_query($db, $sql);
       if (mysqli_errno($db)!=0)
       {
        $desc_erro = str_replace("'", "�", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro na atualiza��o na data da receita";
        }
        mysqli_rollback($db);
        echo $desc_erro;
        exit;
       }
      }

	  //verificando se os itens est�o finalizados
	  //acerta a query tirar o *
	/*  

	  $sql_ver_itens = "select * from itens_receita
	  where receita_id_receita = '$id_receita'";
	  
	    $status_itens_receita = mysqli_query($db, $sql_ver_itens);
		//echo "*".$sql_ver_itens;
		//exit;
		while ($ver_status_itens_receita = mysqli_fetch_object($status_itens_receita)) {
		
				$id_iten 	 = $ver_status_itens_receita->id_itens_receita;
				$status_iten = $ver_status_itens_receita->status;
				$data = date("Y-m-d H:i:s");
				
		
				if ($ver_status_itens_receita->qtde_prescrita > $ver_status_itens_receita->qtde_disp_mes){
					   $status_item = "ABERTO";	
					   $obs ='';
					   $mot ='';
					   $data='';
					   $usuario_fim='';
					   
				}   else{			
					       $status_item = "FINALIZADO";
						   $obs ="FINALIZADO PELO SISTEMA";
						   $mot = 1;
						   $data = date("Y-m-d H:i:s");
						   $usuario_fim = $id_usuario_sistema;
					    }
		//}	
		
      $sql ="update itens_receita
             set
                    status = '$status_item',
					ds_observacao ='$obs',
					motivo_fim_receita_id_motivo_fim_receita = '$mot',
					dt_fim_receita = '$data',
					id_usua_fim_receita='$usuario_fim'
             where
                    receita_id_receita = '$id_receita' and 
					id_itens_receita ='$id_iten'";
					
				
      mysqli_query($db, $sql);
	 
      if (mysqli_errno($db)!=0)
      {
       $desc_erro = str_replace("'", "�", mysqli_error($db));
       if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
       {
        $desc_erro = "Erro na atualiza��o do status dos itens da receita";
       }
       mysqli_rollback($db);
       echo $desc_erro;
     exit;
      }
	}*/
   
      //verificando se receita est� finalizada
      $desc_status = "FINALIZADA";
      $sql = "select qtde_prescrita, qtde_disp_mes
              from
                     itens_receita
              where
                     receita_id_receita = '$id_receita'";
      $status_receita = mysqli_query($db, $sql);
      while ($ver_status_receita = mysqli_fetch_object($status_receita))
      {
       if ($ver_status_receita->qtde_prescrita > $ver_status_receita->qtde_disp_mes)
       {
        $desc_status = "ABERTA";
		
       }
	  // else{
	    //$desc_status = "FINALIZADA";
	   //}
      }

      $sql ="update receita
             set
                    status_2 = '$desc_status'
             where
                    id_receita = '$id_receita'";
      mysqli_query($db, $sql);
      if (mysqli_errno($db)!=0)
      {
       $desc_erro = str_replace("'", "�", mysqli_error($db));
       if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
       {
        $desc_erro = "Erro Erro na atualiza��o do status da receita";
       }
       mysqli_rollback($db);
       echo $desc_erro;
       exit;
      }

      //verifica se algum item foi dispensado
      if ($grava_movto)
      {
       $sql="insert into movto_geral(
                    tipo_movto_id_tipo_movto,
                    usuario_id_usuario,
                    unidade_id_unidade,
                    receita_id_receita,
                    paciente_id_paciente,
                    num_documento,
                    data_movto,
                    data_incl,
                    num_controle)
             values(
                    '3',
                    '$id_usuario_sistema',
                    '$_SESSION[id_unidade_sistema]',
                    '$id_receita',
                    '$paciente',
                    '$ano"."-"."$_SESSION[id_unidade_sistema]"."-"."$numero_receita', '".
                    date("Y-m-d H:i:s")."','".
                    date("Y-m-d H:i:s")."',
                    '$num_controle')";
       mysqli_query($db, $sql);

       if (mysqli_errno($db)!=0)
       {
        $desc_erro = str_replace("'", "�", mysqli_error($db));
        if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
        {
         $desc_erro = "Erro Erro na Inclus�o de Movto Geral";
        }
        mysqli_rollback($db);
        echo $desc_erro;
        exit;
       }
      }

      $sql = "select max(id_movto_geral) as id_movto_geral
              from
                     movto_geral";
      $movto_geral = mysqli_fetch_object(mysqli_query($db, $sql));
      $id_movto_geral = $movto_geral->id_movto_geral;
      //incluir em itens_movto_geral

      //movto materiais
      for($i=0; $i<$qtde_itens; $i++)
      {
       $vet_info_movto = split('[,]',$vet_item[$i]);
       $sql1 = "select material_id_material,  fabricante_id_fabricante, lote, validade
                from
                       estoque
                where
                       unidade_id_unidade = $unidade
                       and id_estoque = $vet_info_movto[1]";
       $lotes = mysqli_fetch_object(mysqli_query($db, $sql1));
       $lote_movto       = $lotes->lote;
       $fabricante_movto = $lotes->fabricante_id_fabricante;
       $validade_movto   = $lotes->validade;
       $material_movto   = $lotes->material_id_material;

       if ($vet_info_movto[1]!='0')
       {
        //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
        //pegar id_itens_receita
        $sql = "select id_itens_receita, num_receita_controlada
                from
                       itens_receita
                where
                       receita_id_receita = '$id_receita'
                       and material_id_material = '$material_movto'";
        $item_sql=mysqli_query($db, $sql);
        $id_itemreceita = mysqli_fetch_object($item_sql);
        $id_item_receita    = $id_itemreceita->id_itens_receita;
        $num_rec_controlada = $id_itemreceita->num_receita_controlada;

        if($vet_info_movto[8]=='')
        {
         $vet_info_movto[8]='Null';
        }

        $sql="insert into itens_movto_geral(
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
                     '$material_movto',
                     '$fabricante_movto',
                     '$lote_movto',
                     '$validade_movto',
                     '$vet_info_movto[2]',
                     '$id_item_receita',
                     $vet_info_movto[8])";
        mysqli_query($db, $sql);

        if (mysqli_errno($db)!=0)
        {
         $desc_erro = str_replace("'", "�", mysqli_error($db));
         if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
         {
          $desc_erro = "Erro Erro na Inclus�o de Itens Movto";
         }
         mysqli_rollback($db);
         echo $desc_erro;
         exit;
        }

        //obtem o saldo anterior de um material no estoque
        $saldo_anterior=0;
        $sql="select sum(quantidade) as quantidade
              from
                     estoque
              where
                     material_id_material='$material_movto'
                     and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        $qtde_estoque_material=mysqli_fetch_object($res);
        if(mysqli_num_rows($res)>0)
        {
         $saldo_anterior=(int)$qtde_estoque_material->quantidade;
        }

        //obtem a quantidade de material de uma unidade no estoque
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
                     e.material_id_material='$material_movto'
                     and e.unidade_id_unidade='$_SESSION[id_unidade_sistema]'
                     and e.lote='$lote_movto'
                     and e.fabricante_id_fabricante='$fabricante_movto'
                     and e.material_id_material = m.id_material";
        $res=mysqli_query($db, $sql);

        if(mysqli_num_rows($res)>0)
        {
         $estoque_info=mysqli_fetch_object($res);
         $qtde_estoque_unidade = (int)$estoque_info->quantidade;
         $desc_material        = $estoque_info->descricao;
         $id_estoque_aux       = $estoque_info->id_estoque;
        }
        else
        {
         $qtde_estoque_unidade=0;
        }

        //*** verifica se quantidade existente em estoque maior que zero
        if ($qtde_estoque_unidade>0)
        {
         $qtde = $qtde_estoque_unidade - (int)$vet_info_movto[2];
         if ($qtde>=0)
         {
          $sql="update estoque
                set
                       quantidade='$qtde',
                       data_alt='".date("Y-m-d H:i:s")."',
                       usua_alt='$id_usuario_sistema'
                where
                       id_estoque='$id_estoque_aux'";
          mysqli_query($db, $sql);

          if (mysqli_errno($db)!=0)
          {
           $desc_erro = str_replace("'", "�", mysqli_error($db));
           if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
           {
            $desc_erro = "Erro Erro na atualiza��o do estoque";
           }
           mysqli_rollback($db);
           echo $desc_erro;
           exit;
          }
         }
         else
         {
          if ($lista_insuficientes=="")
          {
           $lista_insuficientes=$desc_material.",".$lote_movto.",";
          }
          else
          {
           $lista_insuficientes=$lista_insuficientes."|".$desc_material.",".$lote_movto.",";
          }
         }
        }
        else
        {
          $sql_material = "select descricao
                           from
                                  material
                           where
                                  id_material = $material_movto";
          $info = mysqli_query($db, $sql_material);
          $dados_material=mysqli_fetch_object($info);
          $desc_material = $dados_material->descricao;

          if ($lista_insuficientes=="")
          {
           $lista_insuficientes=$desc_material.",".$lote_movto.",";
          }
          else
          {
           $lista_insuficientes=$lista_insuficientes."|".$desc_material.",".$lote_movto.",";
          }
        }

        //obtem o saldo atual de um material no estoque
        $saldo_atual=0;
        $sql="select sum(quantidade) as quantidade
              from
                     estoque
              where
                     material_id_material='$material_movto'
                     and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        $qtde_estoque_material=mysqli_fetch_object($res);
        if(mysqli_num_rows($res)>0)
        {
         $saldo_atual=(int)$qtde_estoque_material->quantidade;
        }

        //Verifica se eh insercao ou atualizacao
        $sql="select qtde_saida
              from
                     movto_livro
              where
                     movto_geral_id_movto_geral='$id_movto_geral'
                     and unidade_id_unidade='$_SESSION[id_unidade_sistema]'
                     and material_id_material='$material_movto'";
        $res=mysqli_query($db, $sql);
        if(mysqli_num_rows($res)>0)
        {
         //atualizando o movimento do livro
         $livro_info=mysqli_fetch_object($res);
         $qtde=(int)$livro_info->qtde_saida+(int)$vet_info_movto[2];
         $sql="update movto_livro
               set
                      qtde_saida='$qtde',
                      saldo_atual='$saldo_atual'
               where
                      movto_geral_id_movto_geral='$id_movto_geral'
                      and unidade_id_unidade='$_SESSION[id_unidade_sistema]'
                      and material_id_material='$material_movto'";
        }
        else
        {
         //insercao movimento do livro
         $sql="select nome
               from
                      paciente
               where
                      id_paciente ='$paciente'";
         $res=mysqli_query($db, $sql);
         if(mysqli_num_rows($res)>0)
         {
          $mov_info=mysqli_fetch_object($res);
          $substituir = "\'";
          $paciente_nome = ereg_replace("'", $substituir, $mov_info->nome);
         }

         if ($num_rec_controlada!="")
         {
          $history=$paciente_nome . " N� da Receita: " . $ano . "-" . $_SESSION[id_unidade_sistema] . "-" . $numero_receita . " NR: " . $num_rec_controlada;
         }
         else
         {
          $history=$paciente_nome . " N� da Receita: " . $ano . "-" . $_SESSION[id_unidade_sistema] . "-" . $numero_receita;
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
                      '$_SESSION[id_unidade_sistema]',
                      '$material_movto',
                      '3',
                      '$saldo_anterior',
                      '$vet_info_movto[2]',
                      '$saldo_atual', '".
                      date("Y-m-d H:i:s")."', '".
                      strtoupper($history)."')";
        }
        mysqli_query($db, $sql);
        if (mysqli_errno($db)!=0)
        {
         $desc_erro = str_replace("'", "�", mysqli_error($db));
         if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
         {
          $desc_erro = "Erro Erro na Inclus�o do Movto Livro";
         }
         mysqli_rollback($db);
         echo $desc_erro;
         exit;
        }
       }//if
      }//for

      $sql_situacao = "select id_status_paciente
                       from
                              status_paciente
                       where
                              descricao like 'ATIVO'
                              and status_2 = 'A'";
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

      $sql_paciente = "update paciente
                       set
                              id_status_paciente = '$id_situacao'
                       where
                              id_paciente = '$paciente'";
      mysqli_query($db, $sql_paciente);

      if (mysqli_errno($db)!=0)
      {
       $desc_erro = str_replace("'", "�", mysqli_error($db));
       if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
       {
        $desc_erro = "Erro Erro na altera��o da situacao do paciente!";
       }
       mysqli_rollback($db);
       echo $desc_erro;
       exit;
      }

      if ($lista_insuficientes=="")
      {
       mysqli_commit($db);

       session_regenerate_id();
       $teste = session_id();
	   
       $num_controle = date("Y-m-d H:i:s").$id_unidade_sistema.$teste;

       echo 'RIS-'.$numero_receita.'|'.$id_receita.'*'.$num_controle;
       echo exit;
      }
      else
      {
       $desc_erro = "Erro " .$lista_insuficientes;
       mysqli_rollback($db);
       echo $desc_erro;
       exit;
      }
       
     }
     else
     {
      $desc_erro = str_replace("'", "�", mysqli_error($db));
      if ((mysqli_errno($db)!=1205) and (mysqli_errno($db)!=0))
      {
       $desc_erro = "Erro Erro na Inclus�o de Receita";
      }
      mysqli_rollback($db);
      echo $desc_erro;
      exit;
     }
    }
    ////////////////////////////////////////////
    //SE N�O ENCONTRAR ARQUIVO DE CONFIGURA��O//
    ////////////////////////////////////////////
?>
