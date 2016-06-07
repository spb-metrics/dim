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


  $paciente   = $_GET[paciente];
  $prescritor = $_GET[prescritor];
  $data_emissao = $_GET[data];
  $itens  = $_GET[itens];
  $total_itens = $_GET[total_itens];
  $pode_gravar='ok';
  
  $vet_itens = split('[|]', $itens);

  $data_emissao = substr($data_emissao,-4)."-".substr($data_emissao,3,2)."-".substr($data_emissao,0,2);

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU

  $sql_verifica_receita = "select id_receita
                           from
                                  receita
                           where
                                  unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                                  and substring(data_emissao,1,10) = '$data_emissao'
                                  and paciente_id_paciente = '$paciente'
                                  and profissional_id_profissional = '$prescritor'
                           order by
                                  id_receita";
  $verifica_receita = mysqli_query($db, $sql_verifica_receita);
  if(mysqli_num_rows($verifica_receita)>0)
  {
    //verificar itens
    while ($consulta = mysqli_fetch_object($verifica_receita))
    {
      //echo $pode_gravar.'</br>';
      $verifica_id_receita = $consulta->id_receita;
      //verifica qtde de itens da receita em banco
      $sql = "select count(*) as itens
              from
                     itens_receita
              where
                     receita_id_receita = '$verifica_id_receita'";
      $receita = mysqli_query($db, $sql);
      $res = mysqli_fetch_object($receita);
      $qtde_itens_banco = $res->itens;

      $pode_gravar='nok';
      
      if (($qtde_itens_banco == $total_itens) and ($total_itens==1))
      {
       $sql_ver_item = "select id_itens_receita
                        from
                               itens_receita
                        where
                               receita_id_receita = '$verifica_id_receita'
                               and material_id_material = '$vet_itens[0]'";
       $verifica_itens_receita = mysqli_query($db, $sql_ver_item);
       if(mysqli_num_rows($verifica_itens_receita)>0)
       {
        $pode_gravar = 'verificar';
        break;
       }
       else
       {
        $pode_gravar = 'ok';
       }
      }
      else
      {
       if ($qtde_itens_banco == $total_itens)
       {
        $itens_iguais = 0;
        for ($i=0;$i<$total_itens;$i++)
        {
           $sql_ver_item = "select id_itens_receita
                            from
                                   itens_receita
                            where
                                   receita_id_receita = '$verifica_id_receita'
                                   and material_id_material = '$vet_itens[$i]'";
           $verifica_itens_receita = mysqli_query($db, $sql_ver_item);
           if(mysqli_num_rows($verifica_itens_receita)>0)
           {
            $itens_iguais +=1;
           }
        }
        if ($itens_iguais != $total_itens)
        {
         $pode_gravar = 'ok';
        }
       }
       else
       {
        $pode_gravar = 'ok';
       }
      }
    }    // while
  }
  else
  {
   $pode_gravar = 'ok';
  }

  if ($pode_gravar=='ok')
  {
   echo 'receita_nao_existe';
  }
  else
  {
   if ($pode_gravar=='nok')
   {
    echo 'receita_existe';
   }
   else
   {
    echo 'receita_existe_verificar';
   }
  }
 ?>
