<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function somadata($pData, $pDias)//formato BR
    {
     if(ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $pData, $vetData))
     {
      $fDia = $vetData[1];
      $fMes = $vetData[2];
      $fAno = $vetData[3];

      for($x = 0; $x <= $pDias; $x++)
      {
       if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12)
       {
        $fMaxDia = 31;
       }
       elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11)
       {
        $fMaxDia = 30;
       }
       else
       {
        if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0)
        {
         $fMaxDia = 29;
        }
        elseif($fMes == 2)
        {
         $fMaxDia = 28;
        }
       }
       $fDia++;
       if($fDia > $fMaxDia)
       {
        if($fMes == 12)
        {
         $fAno++;
         $fMes = 1;
         $fDia = 1;
        }
        else
        {
         $fMes++;
         $fDia = 1;
        }
       }
      }
      if(strlen($fDia) == 1)
       $fDia = "0" . $fDia;
      if(strlen($fMes) == 1)
       $fMes = "0" . $fMes;
      return "$fDia/$fMes/$fAno";
     }
    }

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $param_material   = $_GET[material];
  $data_emissao = $_GET[data];

  $vet_linha = split('[|]',$material);
 //  echo "vetlinha ".$vet_linha[1];

   for($cont=0;$cont<count($vet_linha);$cont++)
   {
        $vet_dados[$cont] = split('[,]',$vet_linha[$cont]);
   }

 for ($cont=count($vet_dados)-1;$cont>=0;$cont--)
 {
  $material=$vet_dados[$cont][0];
  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU
  $sql_validade_receita="select dias_limite_disp, id_material, descricao, flg_autorizacao_disp
                         from
                                material
                         where
                                id_material = $material";
  $resultado = mysqli_query($db, $sql_validade_receita);
  if (mysqli_num_rows($resultado)!=0)
  {
   $dadosmaterial = mysqli_fetch_object($resultado);
   $material = $dadosmaterial->descricao;
   $dias_limite_disp = $dadosmaterial->dias_limite_disp;
   if ($dias_limite_disp!=0 and $dias_limite_disp!='')
   {
    $data_limite_restricao = somadata($data_emissao, (int)$dias_limite_disp-1);
    $data_limite_restricao = substr($data_limite_restricao,-4)."-".substr($data_limite_restricao,3,2)."-".substr($data_limite_restricao,0,2);
    if ((date('Y-m-d',strtotime($data_limite_restricao)) < date('Y-m-d')))
    {
     $flag="validade_expirou";//=".$dadosmaterial->descricao;
     break;
    }
    else
    {
     $flag= "validade_no_prazo";
    }

   }
   else
   {
    $flag= "validade_no_prazo";
   }
  }
 }
 echo $flag;
?>
