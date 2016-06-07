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
   
  $configuracao = "../../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require $configuracao;

  $id_paciente =  $_GET["id_paciente"];
  echo $id_paciente."|";
  
  $cart_siga ='';
  $possui_cartao ='';
  
  $sql_parametro = "select mostrar_msg
                    from
                           unidade
                    where
                           id_unidade = $_SESSION[id_unidade_sistema]";
  $obj_parametro = mysqli_query($db, $sql_parametro);
  erro_sql("Tabela Unidade", $db,"");
  
  if($p_unidade = mysqli_fetch_array($obj_parametro))
  {
     $ver_siga = $p_unidade['mostrar_msg'];

     if (strtoupper($ver_siga)=='S')
     {
          $sql_cartao = "select cartao_siga
                         from
                                cartao_sus
                         where
                                paciente_id_paciente = $id_paciente";
          $obj_cartao = mysqli_query($db, $sql_cartao);
          erro_sql("Tabela Cartão Sus", $db,"");

          if (mysqli_num_rows($obj_cartao) > 0)
           {
             while($p_cartao=mysqli_fetch_array($obj_cartao))
              {
                 $cart_siga = $p_cartao['cartao_siga'];

                 if(strtoupper($cart_siga)=='S')
                 {
                    $possui_cartao='V';
                    echo exit;
                 }

              }
              if ($possui_cartao!='V')
                 echo "O número do Cartão SUS existente não está cadastrado no SIGA. Favor orientar o paciente a atualizar as informações no SIGA e imprimir o Cartão SUS";
           }

          else echo "Paciente sem Cartão SUS. Favor orientar o paciente a fazer o Cartão SUS";
     }
  }
?>
