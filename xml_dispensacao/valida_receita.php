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


  $paciente   = $_GET[paciente];
  $prescritor = $_GET[prescritor];
  $data_emissao = $_GET[data];
  $itens  = $_GET[itens];
  $total_itens = $_GET[total_itens];
  $pode_gravar='ok';
  
  $vet_itens = split('[|]', $itens);

  $data_emissao = substr($data_emissao,-4)."-".substr($data_emissao,3,2)."-".substr($data_emissao,0,2);

  // EXECUTA A INSTRU��O SELECT PASSANDO O QUE O USUARIO DIGITOU

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
