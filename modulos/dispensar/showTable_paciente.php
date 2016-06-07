<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

    if (file_exists("../../config/config.inc.php"))
    {
      require "../../config/config.inc.php";
    }
    
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $cartao_sus  = $_GET["cartao_tela"];
	 $cpf         = $_GET["cpf_tela"];
    $prontuario  = $_GET["pront_tela"];
	 $nome        = $_GET["nome_tela"];
	 $nome_mae    = $_GET["mae_tela"];
	 $data_nasc   = $_GET["data_nasc"];
    $id_unidade_sistema = $_GET["id_unidade_tela"];

    $substituir = "\'";
    $nome = ereg_replace("6e54c9a95b", $substituir, $nome);
    $nome_mae = ereg_replace("6e54c9a95b", $substituir, $nome_mae);
    $troca ="'";
    $nome_text = ereg_replace("\\\'", $troca, $nome);
    $mae_text  = ereg_replace("\\\'", $troca, $nome_mae);

    $nome_sem_esp   = ereg_replace(' ', '', $nome);
    $mae_sem_esp    = ereg_replace(' ', '', $nome_mae);
    $data    = substr(trim($data_nasc),-4)."-". substr(trim($data_nasc),3,2)."-".substr(trim($data_nasc),0,2);
    $nome_mae_nasc = $nome_sem_esp.$mae_sem_esp;

    if($cartao_sus!="")
    {
        $sql_cartao = "select cartao_sus, paciente_id_paciente
                       from
                              cartao_sus
                       where
                              substring(cartao_sus,1,15)= '$cartao_sus'";
        $cartao = mysqli_query($db, $sql_cartao);

        if (mysqli_num_rows($cartao) > 0)
        {
            $lista = mysqli_fetch_object($cartao);
            $sql = "select id_paciente, nome, data_nasc, nome_mae, nome_logradouro
                    from
                           paciente
                    where
                           status_2='A'
                           and id_paciente = $lista->paciente_id_paciente";
            $obj = mysqli_query($db, $sql);
        }
        else if($cpf!="")
        {
            $sql_cpf = "select id_paciente, nome, data_nasc, nome_mae,
                               nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                        from
                               paciente
                        where
                               status_2 = 'A'
                               and cpf= $cpf";
            $obj = mysqli_query($db, $sql_cpf);
            if (mysqli_num_rows($obj) == 0)
            {
                if($prontuario!="")
                {
                    $sql_pront = "select num_prontuario, paciente_id_paciente
                                  from
                                         prontuario
                                  where
                                         substring(num_prontuario,1,15)= '$prontuario'
                                         and unidade_id_unidade = $id_unidade_sistema";
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

                              else if($nome_mae_nasc!="")
                              {
                                  $sql="select id_paciente, nome, data_nasc,
                                               nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                                        from
                                               paciente
                                        where
                                               status_2 = 'A'
                                               and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                                  if($mae_sem_esp!="")
                        		  {
                                     $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                                  }
                                  if($data_nasc!="")
                        		  {
                                      $sql.=" and data_nasc like '$data'";
                                  }
                                  $sql.=" order by nome_mae_nasc";

                                  $obj = mysqli_query($db, $sql);
                             }
                          }
                      }
                    }
                    else if($prontuario!="")
                     {
                          $sql_pront = "select num_prontuario, paciente_id_paciente
                                        from
                                               prontuario
                                        where
                                               substring(num_prontuario,1,15)= '$prontuario'
                                               and unidade_id_unidade = $id_unidade_sistema";
                          $prontuario = mysqli_query($db, $sql_pront);
                          if (mysqli_num_rows($prontuario) > 0)
                          {
                             while($lista = mysqli_fetch_object($prontuario))
                             {
                                $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
                             }

                            $ids_pacientes = substr($ids_pacientes, 0, -1);

                            $sql = "select id_paciente, nome, data_nasc,
                                           nome_mae, nome_logradouro
                                    from
                                           paciente
                                    where
                                           status_2='A'
                                           and id_paciente in ($ids_pacientes)";
                            $obj = mysqli_query($db, $sql);
                          }
                          else if($nome_mae_nasc!="")
                          {
                              $sql="select id_paciente, nome, data_nasc,
                                           nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                                    from
                                           paciente
                                    where
                                           status_2 = 'A'
                                           and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                              if($mae_sem_esp!="")
                    		  {
                                 $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                              }
                              if($data_nasc!="")
                    		  {
                                  $sql.=" and data_nasc like '$data'";
                              }
                              $sql.=" order by nome_mae_nasc";

                              $obj = mysqli_query($db, $sql);
                         }
                      }
                     else if($nome_mae_nasc!="")
        		     {
                          $sql="select id_paciente, nome, data_nasc,
                                       nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                                from
                                       paciente
                                where
                                       status_2 = 'A'
                                       and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                          if($mae_sem_esp!="")
                		  {
                             $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                          }
                          if($data_nasc!="")
                		  {
                              $sql.=" and data_nasc like '$data'";
                          }
                          $sql.=" order by nome_mae_nasc";

                          $obj = mysqli_query($db, $sql);
                    }

     }
     ////se cartao não informado, verficar cpf e/ou prontuario
     else if($cpf!="")
     {
                  $sql_cpf = "select id_paciente, nome, data_nasc,
                                     nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                              from
                                     paciente
                              where
                                     status_2 = 'A'
                                     and cpf= $cpf";
                  $obj = mysqli_query($db, $sql_cpf);
                  if (mysqli_num_rows($obj) == 0)
                  {
                     if($prontuario!="")
                      {
                          $sql_pront = "select num_prontuario, paciente_id_paciente
                                        from
                                               prontuario
                                        where
                                               substring(num_prontuario,1,15)= '$prontuario'
                                               and unidade_id_unidade = $id_unidade_sistema";
                          $prontuario = mysqli_query($db, $sql_pront);
                          if (mysqli_num_rows($prontuario) > 0)
                          {
                             while($lista = mysqli_fetch_object($prontuario))
                             {
                                $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
                             }

                            $ids_pacientes = substr($ids_pacientes, 0, -1);

                            $sql = "select id_paciente, nome,
                                           data_nasc, nome_mae, nome_logradouro
                                    from
                                           paciente
                                    where
                                           status_2='A'
                                           and id_paciente in ($ids_pacientes)";
                            $obj = mysqli_query($db, $sql);
                          }
                          else if($nome_mae_nasc!="")
                          {
                              $sql="select id_paciente, nome, data_nasc,
                                           nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                                    from
                                           paciente
                                    where
                                           status_2 = 'A'
                                           and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                              if($mae_sem_esp!="")
                    		  {
                                 $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                              }
                              if($data_nasc!="")
                    		  {
                                  $sql.=" and data_nasc like '$data'";
                              }
                              $sql.=" order by nome_mae_nasc";

                              $obj = mysqli_query($db, $sql);
                         }
                      }
                  }
     }
     else if($prontuario!="")
     {
                  $sql_pront = "select num_prontuario, paciente_id_paciente
                                from
                                       prontuario
                                where
                                       substring(num_prontuario,1,15)= '$prontuario'
                                       and unidade_id_unidade = $id_unidade_sistema";
                  $prontuario = mysqli_query($db, $sql_pront);

                  if (mysqli_num_rows($prontuario) > 0)
                  {
                     while($lista = mysqli_fetch_object($prontuario))
                     {
                        $ids_pacientes = $lista->paciente_id_paciente.",".$ids_pacientes;
                     }

                    $ids_pacientes = substr($ids_pacientes, 0, -1);

                    $sql = "select id_paciente, nome, data_nasc,
                                   nome_mae, nome_logradouro
                            from
                                   paciente
                            where
                                   status_2='A'
                                   and id_paciente in ($ids_pacientes)";
                    $obj = mysqli_query($db, $sql);
                  }
                  
                  
                  else if($nome_mae_nasc!="")
                  {
                      $sql="select id_paciente, nome, data_nasc,
                                   nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                            from
                                   paciente
                            where
                                   status_2 = 'A'
                                   and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                      if($mae_sem_esp!="")
            		  {
                         $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                      }
                      if($data_nasc!="")
            		  {
                          $sql.=" and data_nasc like '$data'";
                      }
                      $sql.=" order by nome_mae_nasc";

                      $obj = mysqli_query($db, $sql);
                 }
     }
     else if($nome_mae_nasc!="")
	  {
                  $sql="select id_paciente, nome, data_nasc,
                               nome_mae, nome_mae_sem_espaco, nome_logradouro, id_status_paciente
                        from
                               paciente
                        where
                               status_2 = 'A'
                               and nome_mae_nasc like '".trim($nome_sem_esp)."%'";

                  if($mae_sem_esp!="")
        		  {
                     $sql.=" and nome_mae_sem_espaco like '".trim($mae_sem_esp)."%'";
                  }
                  if($data_nasc!="")
        		  {
                      $sql.=" and data_nasc like '$data'";
                  }
                  $sql.=" order by nome_mae_nasc";
                  $obj = mysqli_query($db, $sql);
     }
     $cor_linha = "#CCCCCC";
     if (isset($obj))
     {
                if (mysqli_num_rows($obj) >0)
                {
                    echo"<table width='100%' cellpadding='0' cellspacing='1' border='0'>";
                    while($row=mysqli_fetch_array($obj)) {
        			        $id_paciente= $row['id_paciente'];
        				     $nome=$row['nome'];
        				     $data_nascimento=$row['data_nasc'];
        				     $data_nasc = substr($data_nascimento,-2)."/". substr($data_nascimento,5,2)."/".substr($data_nascimento,0,4);
        				     $nome_mae=$row['nome_mae'];
        				     $endereco=$row['nome_logradouro'];

        				     $sql_cartao = "select cartao_sus
                                       from
                                              cartao_sus
                                       where
                                              paciente_id_paciente = $id_paciente
                                       order by
                                              tipo_cartao";
                       $obj_cartao = mysqli_query($db, $sql_cartao);
                        
                       if (mysqli_num_rows($obj_cartao) >0)
                       {
                          $row_cartao=mysqli_fetch_array($obj_cartao);
                          $cartao_sus= $row_cartao['cartao_sus'];
                       }
                       else
                       {
                        $cartao_sus= '';
                       }

        				     $sql_atencao = "select p.id_paciente, count(id_atencao_continuada) as total
                                       from paciente p
                                       left join atencao_continuada_paciente ap on p.id_paciente = ap.id_paciente
                                       where p.id_paciente = $id_paciente
                                       group by p.id_paciente";
                       $obj_atencao = mysqli_query($db, $sql_atencao);

                       if (mysqli_num_rows($obj_atencao) >0)
                       {
                          $row_atencao=mysqli_fetch_array($obj_atencao);
                          $atencao= $row_atencao['total'];
                       }

          		        echo "<tr class='linha_tabela' bgcolor='$cor_linha'>";
          		        
        		           echo "<tr class='linha_tabela' bgcolor='$cor_linha' onMouseOver='this.bgColor=\"#D9ECFF\";' onMouseOut='this.bgColor=\"$cor_linha\"'>";
         				  echo "<td width='27%' align='left'>$nome</td>";
        				     echo "<td width='8%' align='left'>$data_nasc</td>";
        				     echo "<td width='30%' align='left'>$nome_mae</td>";
                       echo "<td width='17%' align='left'>$endereco</td>";
                       if(($row['id_status_paciente'] == 3) ||($row['id_status_paciente'] == 2))
                       {
                             echo "<td width='3%' align='center'><input type='radio' name='selecao' onclick=\"alert('Paciente com situação irregular, favor acertar o cadastro');\"></td>";
                             echo "<td width='3%' align='center'><img src='".URL."/imagens/b_search.png' onclick=\"alert('Paciente com situação irregular, favor acertar o cadastro');\" border='0' title='Listar Receitas'></a></td>";
                       }
                       else
                       {
                            echo "<td width='3%' align='center'><input type='radio' name='selecao' onclick=\"JavaScript:verifica_cartao_siga('$id_paciente');\"></td>";
                            echo "<td width='3%' align='center'><img src='".URL."/imagens/b_search.png' onclick='JavaScript:window.popup_receitas($id_paciente);' border='0' title='Listar Receitas'></a></td>";
                       }
                           
                       echo "<td width='3%' align='center'><img src='".URL."/imagens/b_edit.png' onclick='JavaScript:window.popup_paciente($id_paciente);' border='0' title='Editar Paciente'></a></td>";

                       if ($atencao==0)
                       {
                          echo "<td width='3%' align='center'><img src='".URL."/imagens/i_002.gif' border='0' title='Não pertence ao Grupo de Atenção Continuada'></a></td>";
                       }
                       else
                       {
                          echo "<td width='3%' align='center'><img src='".URL."/imagens/i_002.gif' border='0' title='Pertence ao Grupo de Atenção Continuada'></a></td>";
                       }

                       echo "<td>";
                       echo "<div id='bubble_tooltip'>";
                    	  echo "<div class='bubble_top'><span></span></div>";
                    	  echo "<div class='bubble_middle'><span id='bubble_tooltip_content'></span></div>";
                    	  echo "<div class='bubble_bottom'></div>";
                       echo "</div>";
                    	  echo "</td>";
                       echo "<td width='6%' align='center'><IMG SRC='".URL."/imagens/folder_store.gif' onmouseover='requestInfoBallon(\"mostra_cartao.php?id_paciente=$id_paciente\",event.clientX,event.clientY);return false'  border='0'></td>";
                       echo "</tr>";
                       if ($cor_linha == "#CCCCCC")
                       {
                            $cor_linha = "#EEEEEE";
                       }
                       else
                       {
                            $cor_linha = "#CCCCCC";
                       }
        			}
               echo "</table>";
               echo "<table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height='10%' >";
               echo "<tr>";
               echo "<td  align='right' bgcolor='#D8DDE3'>";
               echo "<input style='font-size: 10px;' type='button' name='voltar' value='<< Voltar' onClick='window.close();'>";
               echo "</td>";
               echo "</tr>";
               echo "<table>";
				echo exit;
           }
     }
