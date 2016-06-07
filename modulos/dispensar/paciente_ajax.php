<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  $configuracao = "../../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("N�o existe arquivo de configura��o!");
  }
  require ($configuracao);


  $paciente=$_POST[id_paciente];
  $unidade=$_SESSION[id_unidade_sistema];
  //$paciente='731307';
  //$unidade = '128';
  
  $sql = "select min(c.cartao_sus) as cartao,
                 p.nome as nome,
                 p.cpf as cpf, min(pt.num_prontuario) as pront,
                 p.nome_mae as nome_mae, p.data_nasc as data_nasc,
                 c.tipo_cartao as tipo_cartao
          from
                 paciente p left join cartao_sus c on p.id_paciente=c.paciente_id_paciente
                 left join prontuario pt on p.id_paciente=pt.paciente_id_paciente
                 and pt.unidade_id_unidade = '$unidade'
          where
                 p.id_paciente = '$paciente'
          group by
                 p.nome, p.nome_mae, p.data_nasc
          order by
                 c.tipo_cartao";

  $result=mysqli_query($db, $sql);
  erro_sql("Tabela Paciente", $db, "");

  $linhas=mysqli_num_rows($result);
  if($linhas>0){
    $xml="<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?\>\n";
    $xml= str_replace("\>", ">", $xml);
    $xml.="<paciente>\n";

    $paciente_info=mysqli_fetch_object($result);
    
    $nome = $paciente_info->nome;
    $mae  = $paciente_info->nome_mae;
    $nasc = $paciente_info->data_nasc;
    if ($paciente_info->cpf=='')
    {
     $cpf = '0';
    }
    else
    {
     $cpf = $paciente_info->cpf;
    }

    if ($paciente_info->cartao=='')
    {
     $cartao = '0';
    }
    else
    {
     $cartao = $paciente_info->cartao;
    }

    if ($paciente_info->pront=='')
    {
     $prontuario = '0';
    }
    else
    {
     $prontuario = $paciente_info->pront;
    }

    $xml.="<registro>\n";
    $xml.="<nome>" . $nome . "</nome>\n";
    $xml.="<mae>" . $mae . "</mae>\n";
    $xml.="<nasc>" . substr($nasc,8,2)."/".substr($nasc,5,2)."/".substr($nasc,0,4) . "</nasc>\n";
    $xml.="<cartao>" . $cartao . "</cartao>\n";
    $xml.="<cpf>" . $cpf . "</cpf>\n";
    $xml.="<prontuario>" . $prontuario . "</prontuario>\n";
    $xml.="</registro>\n";
    
    $xml.="</paciente>";

    Header("Content-type: application/xml; charset=iso-8859-1");
  }

  echo $xml;

?>
