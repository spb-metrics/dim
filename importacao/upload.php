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
  if (file_exists("../config/config.inc.php"))
  {
    require "../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////


    // Repassa a variável do upload
    $filename = $_FILES[arquivo][tmp_name];
    
        //click no botão importar - inclusão de profissionais
        $sql = "select * from parametro ";
        $res=mysqli_query($db, $sql);
        erro_sql("Parâmetro", $db, "");
        $parametro = mysqli_fetch_object($res);

        $validade = $parametro->validade_arq_crm; //em dias
        $email    = $parametro->email_msg_erro;
        $tot_incl  = 0;
        $tot_alt   = 0;
        $tot_erro  = 0;

        //$arquivo  = $parametro->nome_arquivo;
        if (file_exists($filename))
        {
          //verificar validade do arquivo
          $data_arquivo = date("Y-m-d", filemtime($filename));
          $sql = "select date_add('$data_arquivo', INTERVAL '$validade' DAY) as data_limite";
          $res=mysqli_query($db, $sql);
          erro_sql("Validade Arquivo", $db, "");
          $data = mysqli_fetch_object($res);

          $data_limite = $data->data_limite;


          if ($data_limite>date("Y-m-d"))
          {
            //Abre arquivo para gravação de futuros erros
            $erro = fopen("ERRO_CRM.TXT", "w");

            //Abre o arquivo .txt de profissionais
            $fp = fopen($filename,'r');
            $linha = fscanf($fp,"%[^,]");
            $atualizacao="";
            while(!feof($fp) )
            {
         	  $linha = fscanf($fp,"%[^,]");
	          $codigo         = rtrim(substr($linha[0],0,6));
	          $nome           = substr($linha[0],7,42);
	          $nome           = rtrim(str_replace("'", "´", $nome));
              $status         = substr($linha[0],50,1);
              $data_inscricao = substr($linha[0],52,10);
              $data_inativo   = substr($linha[0],63,10);
              $cidade         = rtrim(substr($linha[0],74,37));
              $uf             = rtrim(substr($linha[0],112,2));
              $especialidade  = substr($linha[0],115);
              $especialidade  = rtrim(str_replace("¨", "", $especialidade));
              $total = $total + 1;

  	          //verifica estado
  	          if ($uf!="")
  	          {
  	            $sql = "select * from estado where uf like '$uf'";
                $estado = mysqli_query($db, $sql);
                erro_sql("Estado", $db, "");
                if (mysqli_num_rows($estado)==0)
                {
                   $sql = "insert into estado (uf, nome) values ('$uf', '$uf')";
                   mysqli_query($db, $sql);
                   erro_sql("Insert Estado", $db, "");
                   if(mysqli_errno($db)!="0"){
                     $atualizacao="erro";
                   }

                   $sql = "select max(id_estado) as codigo from estado";
                   $res=mysqli_query($db, $sql);
                   erro_sql("ID Estado", $db, "");
                   $estado    = mysqli_fetch_object($res);

                   $id_estado = $estado->codigo;
                }
                else
                {
                   $estado_info    = mysqli_fetch_object($estado);
                   $id_estado      = $estado_info->id_estado;
                }
              }

              //verifica cidade
  	          if ($cidade!="")
  	          {
  	             $sql = "select * from cidade where estado_id_estado = '$id_estado' and nome like '$cidade' ";
//  	             echo $sql;
//  	             echo exit;
                 $cid = mysqli_query($db, $sql);
                 erro_sql("Cidade", $db, "");
                 if (mysqli_num_rows($cid)==0)
                 {
                    $sql = "insert into cidade (estado_id_estado, nome) values ('$id_estado', '$cidade')";
                    mysqli_query($db, $sql);
                    erro_sql("Insert Cidade", $db, "");
                    if(mysqli_errno($db)!="0"){
                      $atualizacao="erro";
                    }

                    $sql = "select max(id_cidade) as codigo from cidade";
                    $res=mysqli_query($db, $sql);
                    erro_sql("ID Cidade", $db, "");
                    $cid = mysqli_fetch_object($res);

                    $id_cidade = $cid->codigo;
                 }
                 else
                 {
                    $cidade_info    = mysqli_fetch_object($cid);
                    $id_cidade = $cidade_info->id_cidade;
                 }
              }

              $sql = "select id_tipo_conselho from tipo_conselho where descricao like 'CRM'";
              $res=mysqli_query($db, $sql);
              erro_sql("ID Tipo Conselho", $db, "");
              $conselho = mysqli_fetch_object($res);
              $id_tipo_conselho = $conselho->id_tipo_conselho;

              $sql = "select id_tipo_prescritor from tipo_prescritor where  status_2 = 'A' and tipo_conselho_id_tipo_conselho='$id_tipo_conselho'";
              $res=mysqli_query($db, $sql);
              erro_sql("ID Tipo Prescritor", $db, "");
              $prescritor = mysqli_fetch_object($res);
              $id_tipo_prescritor = $prescritor->id_tipo_prescritor;


              //verifica se profissional já está cadastrado
              $sql = "select * from profissional where inscricao=$codigo";
              $profissional = mysqli_query($db, $sql);
              erro_sql("Profissional", $db, "");
              if (mysqli_errno($db)=="0")
              {
                if (mysqli_num_rows($profissional)==0)
                //fazer insert
                {
                   //echo "oi";

                   $sql= "insert into profissional (cidade_id_cidade,
                          estado_id_estado,
                          tipo_conselho_id_tipo_conselho,
                          tipo_prescritor_id_tipo_prescritor,
                          nome,
                          status_2,
                          inscricao,
                          data_inscricao,
                          especialidade, data_incl, usua_incl, data_alt, usua_alt) values (
                          '$id_cidade',
                          '$id_estado',
                          '$id_tipo_conselho',
                          '$id_tipo_prescritor',
                          '$nome',
                          '$status',
                          '$codigo',
                          '$data_inscricao',
                          '$especialidade',
                          '".date("Y-m-d H:m:s")."',
                          '$_SESSION[id_usuario_sistema]',
                          '$data_inativo',
                          '$_SESSION[id_usuario_sistema]')";
                   mysqli_query($db, $sql);
                   erro_sql("Insert Profissional", $db, "");
                   if(mysqli_errno($db)!="0"){
                     $atualizacao="erro";
                   }
                   if (mysqli_errno($db)=="0")
                   {
                     $tot_incl++;
                   }
                   else
                   {
                      $tot_erro++;
                      $grava = fwrite($erro, "insert: ".$sql);
                   }
                }
                else
                //update
                {
                   $sql= "update profissional set
                     cidade_id_cidade = '$id_cidade',
                     estado_id_estado = '$id_estado',
                     tipo_conselho_id_tipo_conselho = '$id_tipo_conselho',
                     tipo_prescritor_id_tipo_prescritor = '$id_tipo_prescritor',
                     nome = '$nome',
                     status_2 = '$status',
                     data_inscricao = '$data_inscricao',
                     especialidade = '$especialidade',
                     data_alt = '$data_inativo',
                     usua_alt = '$_SESSION[id_usuario_sistema]'
                     where inscricao='$codigo'";
                   mysqli_query($db, $sql);
                   erro_sql("Update Profssional", $db, "");
                   if(mysqli_errno($db)!="0"){
                     $atualizacao="erro";
                   }
                   if (mysqli_errno($db)=="0")
                   {
                     $tot_alt++;
                   }
                   else
                   {
                      $tot_erro++;
                      $grava = fwrite($erro, "update: ".$sql);
                   }
                }
              }
              else
              {
                 $tot_erro++;
                 $grava = fwrite($erro, "select: ".$sql);
              }

            }
            if($atualizacao==""){
              mysqli_commit($db);
            }
            else{
              mysqli_rollback($db);
            }
            fclose($fp);
            fclose($erro);
            echo "<script>";
            echo "alert('Total de linhas lidas: ' + $total + ', inclusões: ' + $tot_incl + ', alterações: ' + $tot_alt + ', erros: ' + $tot_erro);";
            echo "window.location='profissionais_inicial.php';";
            echo "</script>";
          }

          //arquivo com data de validade vencida
          else
          {
             echo "<script>";
             echo "alert('Arquivo de importação com data ultrapassada!');";
             echo "window.location='profissionais_inicial.php';";
             echo "</script>";
          }
        }
        //arquivo existe
        else
        {
           echo "<script>";
           echo "alert('Arquivo não encontrado!');";
           echo "window.location='profissionais_inicial.php';";
           echo "</script>";
        }
    }
?>



