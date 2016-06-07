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
   
  $configuracao = "../../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
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
          erro_sql("Tabela Cart�o Sus", $db,"");

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
                 echo "O n�mero do Cart�o SUS existente n�o est� cadastrado no SIGA. Favor orientar o paciente a atualizar as informa��es no SIGA e imprimir o Cart�o SUS";
           }

          else echo "Paciente sem Cart�o SUS. Favor orientar o paciente a fazer o Cart�o SUS";
     }
  }
?>
