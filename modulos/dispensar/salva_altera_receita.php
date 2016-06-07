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

    $sql = "select *  from aplicacao where executavel like '/modulos/dispensar/busca_altera_receita.php'";
    $aplic = mysqli_query($db, $sql);
    $aplicacao = mysqli_fetch_object($aplic);
    
    $_SESSION[aplicacao] = $aplicacao->id_aplicacao;
    
    $alt_controlada = 0;
    
    $atualizacao = "";

    if($_POST[id_receita]!="")
    {
       $flg_update = 0;

       $id_receita = $_POST[id_receita];

       $sql="select p.id_paciente, p.nome, r.ano, r.numero, r.unidade_id_unidade, r.data_emissao
                    from receita r, paciente p
                    where r.paciente_id_paciente = p.id_paciente
                    and r.id_receita = '$id_receita'";
       $paciente = mysqli_query($db, $sql);

       $dados_paciente = mysqli_fetch_object($paciente);
       
       $id_paciente = $dados_paciente->id_paciente;
       $nome_paciente = $dados_paciente->nome;
       $ano = $dados_paciente->ano;
       $numero = $dados_paciente->numero;
       $unidade = $dados_paciente->unidade_id_unidade;
       $data_emissao = substr($dados_paciente->data_emissao,8,2)."/".substr($dados_paciente->data_emissao,5,2)."/".substr($dados_paciente->data_emissao,0,4);

       // 0 = id_itens_receita;
       // 1 = id_material;
       // 2 = qtde_prescrita;
       // 3 = qtde_disp_anterior; (anterior + mes)
       // 4 = qtde_dispensada;

       //vetor com os valores a dispensar do medicamento

       $i=0;
       if ($_POST[item]!="")
       {
        foreach($_POST[item] as $qtde_item)
        {
         //echo "*".$qtde_item."<br>";
         
         $vetQtde[$i] =  $qtde_item;
         $i=$i+1;
        }
        //echo exit;
       }

       $i=0;
       if ($_POST[rec_controlada]!="")
       {
        foreach($_POST[rec_controlada] as $num_rec_controlada)
        {
          $vetControlada[$i] = $num_rec_controlada;
          $i=$i+1;
        }
       }

       
       $control = 0;
       $i = 0;
       
       //quantidade de itens da receita
       $sql_qtde = "select * from itens_receita where receita_id_receita = $id_receita
                 and qtde_prescrita > (qtde_disp_anterior+qtde_disp_mes)";
                 
       $info_qtde_itens = mysqli_query($db, $sql_qtde);

       $qtde_itens_rec = mysqli_num_rows($info_qtde_itens);
       
       $flag_receita_existe=0;
       
       foreach($_POST[lista_itens_receita] as $item_receita)
       {
        $vetItem = explode(",",$item_receita);

        //echo "*".$item_receita."<br>";

        if ((intVal($vetItem[2])-intVal($vetItem[3]))==0) //não tem mais pra dispensar
        {
         $vetItem[4] = 0;
        }
        else
        {
        //verifico se existe em estoque
         $sql = "select * from estoque
                where
                material_id_material = '".trim($vetItem[1])."'
                and unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                and quantidade > 0
                and (flg_bloqueado is null or flg_bloqueado = '')
                and validade >'".date("Y-m-d")."'";
         //echo $sql ."<br>";
         
         $lote = mysqli_query($db, $sql);

         if ((mysqli_num_rows($lote)==0)) //existe
         {
          $vetItem[4] = 0;
          $flag_receita_existe++;
          //$vetItem[4] = $vetQtde[$i];
         }
         else
         {
          $vetItem[4] = $vetQtde[$i];

          if ($vetItem[4]!='')
          {
           //*****
           $sql_valida_receita="select timediff(now(),data_ult_disp) as intervalo,
                               if(qtde_disp_mes=".$vetItem[4].",'igual', 'difer') as valor
                               from itens_receita
                               where id_itens_receita = '".trim($vetItem[0])."'";
           //echo   $sql_valida_receita;
           //echo exit;
           $valida_receita = mysqli_query($db, $sql_valida_receita);

           $det_receita = mysqli_fetch_object($valida_receita);

           if (($det_receita->intervalo<='01:00:00')&&($det_receita->valor=='igual'))
           {
            $flag_receita_existe++;
           }
          }
          else
          {
           $flag_receita_existe++;
          }

          //else
          //{
          // $flag_receita_existe=false;
          //}
          
          
          //verificar se o item receita precisa de nr_controlada
          
          $sql = "select * from itens_receita where id_itens_receita = '".trim($vetItem[0])."'";
          $controlada = mysqli_query($db, $sql);

          $rec_controlada = mysqli_fetch_object($controlada);

          if ($rec_controlada->num_receita_controlada=="")
          {
           $sql = "select m.*, l.* from material m, lista_especial l
                where m.id_material = '$rec_controlada->material_id_material'
                and m.lista_especial_id_lista_especial = l.id_lista_especial
                and l.flg_receita_controlada='S'";
           $res = mysqli_query($db, $sql);

           if (mysqli_num_rows($res)>0)
           {
            if (trim($vetControlada[$control])!="0")
            {
             $sql = "update itens_receita set num_receita_controlada = '".strtoupper(trim($vetControlada[$control]))."'
               where id_itens_receita = '".trim($vetItem[0])."'";
             mysqli_query($db, $sql);

             if (mysqli_errno($db)!=0)
             {
                mysqli_rollback($db);
                ?>
                <script>
                        alert('Erro na Alteração de Itens Receita');
                        location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
                </script>
                <?
                echo exit;
             }

             $control = $control+1;
             if ((trim($vetControlada[$control])!="") and (trim($vetControlada[$control])!="0"))
             {
              $alt_controlada = 1;
             }
            }
           }
          }
          $i=$i+1;
         }

          //echo "*".$vetItem[4]."<br>";

         if ($vetItem[4]!=0)
         {
           $sql = "update itens_receita set qtde_disp_anterior =  '".trim($vetItem[3])."',
               qtde_disp_mes = '".trim($vetItem[4])."',
               data_ult_disp = '".date("Y-m-d H:i:s")."'
               where id_itens_receita = '".trim($vetItem[0])."'";
           mysqli_query($db, $sql);

           if (mysqli_errno($db)!=0)
           {
              mysqli_rollback($db);
              ?>
              <script>
                      alert('Erro na Alteração de Itens Receita');
                      location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
              </script>
              <?
              echo exit;
           }
           
          //echo $sql;
          //echo exit;
          $flg_update = 1;

         }
        }
        //echo "*".$vetItem[4]."<br>";
        
        
        
       } //foreach $item_receita)

       //echo exit;
       
      //verificando se receita está finalizada
      $desc_status = "FINALIZADA";
      $sql = "select * from itens_receita where receita_id_receita = '$id_receita'";
      $status_receita = mysqli_query($db, $sql);

      while ($ver_status_receita = mysqli_fetch_object($status_receita))
      {
       if ($ver_status_receita->qtde_prescrita <> ($ver_status_receita->qtde_disp_mes+$ver_status_receita->qtde_disp_anterior))
       {
        $desc_status = "ABERTA";
       }
      }
      $sql ="update receita set status_2 = '$desc_status',
           usua_alt = '$_SESSION[id_usuario_sistema]',
           data_alt = '".date("Y-m-d H:i:s")."'
           where id_receita = '$id_receita'";
      mysqli_query($db, $sql);

      if (mysqli_errno($db)!=0)
      {
       mysqli_rollback($db);
       ?>
       <script>
               alert('Erro na Alteração de Receita');
               location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
       </script>
       <?
       echo exit;
      }

      if ($flg_update ==1)
      {
      
       $sql ="update receita set
           data_ult_disp = '".date("Y-m-d H:i:s")."'
           where id_receita = '$id_receita'";
       mysqli_query($db, $sql);

       if (mysqli_errno($db)!=0)
       {
          mysqli_rollback($db);
       ?>
       <script>
               alert('Erro na Alteração de Itens Receita');
               location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
       </script>
       <?
       echo exit;
       }

       //salvar movimentação
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
       $sql.="'$id_receita', ";
       $sql.="'$id_paciente', ";
       $sql.="'$ano"."-"."$unidade"."-"."$numero', '";
       $sql.= date("Y-m-d H:i:s")."', '";
       $sql.= date("Y-m-d H:i:s")."')";
       mysqli_query($db, $sql);

       if (mysqli_errno($db)==0)
       {
        $sql = "select max(id_movto_geral) as id_movto_geral from movto_geral";
        $movto_geral = mysqli_fetch_object(mysqli_query($db, $sql));

        $id_movto_geral = $movto_geral->id_movto_geral;
        //incluir em itens_movto_geral
        
//*************
        //verificar lotes
        $j=0;
        foreach($_POST[valor] as $qtde_valor)
        {
         if (($qtde_valor == "") or ($qtde_valor == 0))
         {
          $vetQtde_Valor[$j] = 0;
         }
         else
         {
          $vetQtde_Valor[$j] = $qtde_valor;
         }
         $j=$j+1;
        }
        
        $i=0;
        if ($_POST[id_aut]!="")
        {
         foreach($_POST[id_aut] as $autor)
         {
          //echo "*".$autor."<br>";
          $vetAutorizador[$i] = $autor;
          //echo "VETOR:".$vetAutorizador[$i]."<br>";
          $i=$i+1;
         }
        }

        $z = 0;
        
        $i = 0;
        foreach($_POST[lista_itens_receita] as $item_receita)
        {
         $vetItem = explode(",",$item_receita);
         
         $vetItem[5] = $vetAutorizador[$z];
         $z=$z+1;

         
         //echo "VETOR:".$vetItem[5]."<br>";
         
         if ((intVal($vetItem[2])-intVal($vetItem[3]))==0) //não tem mais pra dispensar
         {
          $vetItem[4] = 0;
         }
         else
         {
          //verifico se existe em estoque
          $sql = "select * from estoque
                where
                material_id_material = '".trim($vetItem[1])."'
                and unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                and quantidade > 0
                and (flg_bloqueado is null or flg_bloqueado = '')
                and validade >'".date("Y-m-d")."'";
          $lote = mysqli_query($db, $sql);

          if ((mysqli_num_rows($lote)>0)) //existe
          {
           $vetItem[4] = $vetQtde[$i];
           $i+=1;
          }
          else
          {
           $vetItem[4] = 0;
          }

          if ($vetItem[4]!=0)
          {
           $j=0;
           foreach($_POST[lista_estoque] as $item_estoque)
           {
            $item_estoque=$item_estoque.','.$vetQtde_Valor[$j];

            //echo  "estoque".$item_estoque."<br>";
            
            $vetEstoque = explode(",",$item_estoque);

            // 0 = id_material;
            // 1 = id_estoque;
            // 2 = quantidade em estoque
            // 3 = qtde_dispensada por lote;

             //echo "*";
             //echo $vetEstoque[0]. "<br>";
             //echo $vetItem[1]. "<br>";
             //echo $vetEstoque[2]. "<br>";
             //echo "*";
             
            if (($vetEstoque[0]==$vetItem[1]) and ($vetEstoque[3]!=0))
            {
             //atualizações em tabelas
             $sql = "select * from estoque where id_estoque = '".trim($vetEstoque[1])."'";
             $estoque = mysqli_query($db, $sql);

             $dados_estoque = mysqli_fetch_object($estoque);

             $id_fabricante = $dados_estoque->fabricante_id_fabricante;
             $lote = $dados_estoque->lote;
             $validade = $dados_estoque->validade;

             if(($vetItem[5]=='')||($vetItem[5]=='0'))
             {
              $vetItem[5]='Null';
             }

             $sql="insert into itens_movto_geral ";
             $sql.="(movto_geral_id_movto_geral, material_id_material, fabricante_id_fabricante, lote, validade, qtde, itens_receita_id_itens_receita, usuario_autorizador) ";
             $sql.="values ('$id_movto_geral', '$vetItem[1]', '$id_fabricante', '$lote', '$validade', '$vetEstoque[3]', '$vetItem[0]', $vetItem[5])";
             mysqli_query($db, $sql);

             if (mysqli_errno($db)!=0)
             {
              mysqli_rollback($db);
              ?>
              <script>
                      alert('Erro na Inclusão de Movto Geral');
                      location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
              </script>
              <?
              echo exit;
             }
             //echo $sql."<br>";
             
             $sql="select sum(quantidade) as saldo_anterior from estoque where material_id_material='$vetItem[1]' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
             $res=mysqli_query($db, $sql);

             $qtde_estoque_material=mysqli_fetch_object($res);
             $saldo_anterior=$qtde_estoque_material->saldo_anterior;

             //obtem a quantidade de material de uma unidade no estoque
             $sql="select e.*, m.descricao from estoque e, material m ";
             $sql.= "where e.material_id_material = m.id_material and ";
             $sql.= "e.fabricante_id_fabricante='$id_fabricante' and e.material_id_material='$vetItem[1]' ";
             $sql.="and e.unidade_id_unidade='$_SESSION[id_unidade_sistema]' and e.lote='$lote'";
             $res=mysqli_query($db, $sql);

             if(mysqli_num_rows($res)>0)
             {
              $estoque_info=mysqli_fetch_object($res);
              $qtde_estoque_unidade=(int)$estoque_info->quantidade;
              $desc_material = $estoque_info->descricao;
             }
             else
             {
              $qtde_estoque_unidade=0;
             }
             //******* verifica estoque
             
             if ($qtde_estoque_unidade>0)
             {
              $qtde = $qtde_estoque_unidade - $vetEstoque[3];
              if ($qtde>=0)
              {
               $sql="update estoque ";
               $sql.="set quantidade='$qtde', data_alt='".date("Y-m-d H:i:s")."', usua_alt='$_SESSION[id_usuario_sistema]' ";
               $sql.="where fabricante_id_fabricante='$id_fabricante' and material_id_material='$vetItem[1]' ";
               $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and lote='$lote'";
               //echo $sql;
               mysqli_query($db, $sql);
               if (mysqli_errno($db)!=0)
               {
                mysqli_rollback($db);
                ?>
                <script>
                 alert('Erro na Alteração em Estoque');
                 location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
                </script>
                <?
                echo exit;
               }
              }
              else
              {
               mysqli_rollback($db);
               ?>
               <script>
                alert('Quantidade insuficiente em estoque');
                location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
               </script>
               <?
               echo exit;
              }
             }
             else
             {
               mysqli_rollback($db);
               ?>
               <script>
                alert('Quantidade insuficiente em estoque');
                location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
               </script>
               <?
               echo exit;
             }
             
             //obtem o saldo atual de um material no estoque
             $sql="select sum(quantidade) as saldo_atual from estoque where material_id_material='$vetItem[1]' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
             $res=mysqli_query($db, $sql);

             $qtde_estoque_material=mysqli_fetch_object($res);
             $saldo_atual=$qtde_estoque_material->saldo_atual;

             //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
             //Verifica se eh insercao ou atualizacao

             $sql="select * from movto_livro where movto_geral_id_movto_geral='$id_movto_geral' ";
             $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$vetItem[1]'";
             //echo $sql;
             //echo exit;
             $res=mysqli_query($db, $sql);

             if(mysqli_num_rows($res)>0)
             {
              //atualizando o movimento do livro
              $livro_info=mysqli_fetch_object($res);
              $qtde=(int)$livro_info->qtde_saida+(int)$vetEstoque[3];
              $sql="update movto_livro set qtde_saida='$qtde', saldo_atual='$saldo_atual'";
              $sql.=" where movto_geral_id_movto_geral='$id_movto_geral' and ";
              $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$vetItem[1]'";
              //echo $sql;
              //echo exit;
             }
             else
             {
              $sql = "select * from itens_receita where receita_id_receita = '$id_receita' and material_id_material = '$vetItem[1]'";
              //echo $sql;
              //echo exit;
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

              $sql="insert into movto_livro ";
              $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material, tipo_movto_id_tipo_movto, saldo_anterior, qtde_saida, saldo_atual, data_movto, historico) ";
              $sql.="values ('$id_movto_geral', '$_SESSION[id_unidade_sistema]', '$vetItem[1]', '3', '$saldo_anterior', '$vetEstoque[3]', '$saldo_atual', '".date("Y-m-d H:i:s")."', '" . strtoupper($history) . "')";
              //echo $sql;
              //echo exit;
             }
             mysqli_query($db, $sql);
             if (mysqli_errno($db)!=0)
             {
              mysqli_rollback($db);
              ?>
              <script>
               alert('Erro na Inclusão de Movto Livro');
               location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
              </script>
              <?
              echo exit;
             }

             //echo $sql;
             //echo exit;

            }//if (vetEstoque[0] ==vetUpdate[1])
            
            $j=$j+1;
            
           } //foreach estoque
           
           //echo exit;
           
          } //if ($vetUpdate[4]!=0)

         }
        } //foreach $item_receita)
        
        //echo "**".$flag_receita_existe."-".$qtde_itens_rec;
        //echo exit;
        if ($qtde_itens_rec == $flag_receita_existe)
        {
         mysqli_rollback($db);
         ?>
         <script>
               alert('Receita já alterada na data de hoje!');
               location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
         </script>
         <?
         echo exit;
        }
        else
        {
         mysqli_commit($db);
         //exit;
         header("Location: ". URL."/modulos/dispensar/consulta_receita_alterada.php?id_receita=$id_receita&id_movto_geral=$id_movto_geral");

        }

       }//if (mysqli_errno($db)==0)
       else
       {
        mysqli_rollback($db);
        ?>
        <script>
               alert('Erro na Inclusão de Movto Geral');
               location.href="<?php echo URL.'/modulos/dispensar/busca_altera_receita.php?aplicacao='.$_SESSION[aplicacao]?>";
        </script>
        <?
        echo exit;
       }
       
      }//flg_update = 1
      else
      {
       //echo "*" .$alt_controlada;
       //echo exit;
       if ($alt_controlada == 1)
       {
        header("Location: ". URL."/modulos/dispensar/altera_receita.php?id_receita=$id_receita");
       }
       else
       {
        echo "<script>";
        echo "alert('Nenhum medicamento foi dispensado!');";
        echo "history.go(-1);";
        echo "</script>";
       }
      }
      //echo exit;

   } //$_POST[id_receita]
  }
  else
  {
    include_once "../../config/erro_config.php";
  }

?>

