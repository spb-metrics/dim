<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: recibo_receita_pdf.php                                    |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Recibo da Receita (.pdf)                                  |
// | Data de Criação ....: 28/01/2007 - 16:00                                        |
// | Última Atualização .: 12/02/2007 - 17:50                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

$header = array('Lote','Validade','Quantidade Dispensada','');
$w = array(35,35,40,165);//,35,63); 275

function cabecalho()
{
  global $pdf, $nr_receita, $nome, $cartao_sus, $data_dispensasao,
         $nomeprescritor, $data_emissao, $dispensado;

  $pdf->AddPage();
  $pdf->Ln();

  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(0,5,"Número da Receita: ".$nr_receita,0,1,"C");
  $pdf->SetFont('Arial','',9);  $pdf->Ln();

  $pdf->Cell(30,5,"     Paciente:",0,0,"L");
  if ($nome == '')
    $pdf->Cell(95,5,"---",0,0,"L");
  else
    $pdf->Cell(95,5,$nome,0,0,"L");

  $pdf->Cell(32,5,"     Cartão SUS:",0,0,"L");
  if ($cartao_sus == '')
    $pdf->Cell(0,5,"---",0,1,"L");
  else
    $pdf->Cell(0,5,$cartao_sus,0,1,"L");

  $pdf->Cell(30,5,"     Prescritor:",0,0,"L");
  if ($nomeprescritor == '')
    $pdf->Cell(100,5,"---",0,0,"L");
  else
    $pdf->Cell(100,5,$nomeprescritor,0,0,"L");

  $pdf->Cell(32,5,"Data da prescrição:",0,0,"L");
  if ($data_emissao == '')
    $pdf->Cell(0,5,"---",0,1,"L");
  else
    $pdf->Cell(0,5,$data_emissao,0,1,"L");


}

function cabecalho_tabela($med_atual, $qtd_pre_atual, $qtd_dis_atual)
  {
  global $pdf, $header, $w;

  $pdf->Ln();

  $pdf->SetFont('','B');
  $pdf->Cell(40,5,"Material / Medicamento:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(140,5,$med_atual,0,0,"L");

  $pdf->SetFont('','B');
  //$pdf->Cell(25,5,"Qtde Prescrita:",0,0,"L");
  $pdf->Cell(30,5,"Qtde Prescrita:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(13,5,$qtd_pre_atual,0,0,"L");

  $pdf->SetFont('','B');
  //$pdf->Cell(30,5,"Qtde Dispensada:",0,0,"L");
  $pdf->Cell(35,5,"Qtde Dispensada:",0,0,"L");
  $pdf->SetFont('','');
  $pdf->Cell(13,5,$qtd_dis_atual,0,0,"L");

  $pdf->Ln(5);

  //Colors, line width and bold font
  $pdf->SetFillColor(255,255,255);  // cor do fundo do cabeçalho da tabela
  $pdf->SetTextColor(0);  // cor do texto

 $pdf->SetLineWidth(.3);
  $pdf->SetFont('','B');

  //Header
  for($i = 0; $i < count($header); $i++)
  $pdf->Cell($w[$i],5,$header[$i],'LTRB',0,'C',1);
  $pdf->Ln(5.4);

  //Font restoration
  $pdf->SetFont('');
}

if (file_exists("../../config/config.inc.php"))
{
  require "../../config/config.inc.php";

   $sql = "select rec.id_receita, rec.ano, rec.unidade_id_unidade, rec.numero, rec.data_emissao,
                   rec.data_ult_disp, pro.nome as prescritor, pac.nome as paciente, pac.id_paciente,
                   und.nome as nome_unidade_sistema, mov.id_movto_geral
            from receita rec
                 inner join profissional pro on rec.profissional_id_profissional = pro.id_profissional
                 inner join paciente pac on rec.paciente_id_paciente = pac.id_paciente
                 inner join unidade und on rec.unidade_id_unidade = und.id_unidade
                 left  join movto_geral mov on rec.id_receita = mov.receita_id_receita
            where rec.ano = $_GET[ano]
                  and rec.unidade_id_unidade =$_GET[unidade]
                  and rec.numero = $_GET[numero]";

$sql_query_cabecalho = mysqli_query($db, $sql);
    erro_sql("Receita", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query_cabecalho) > 0)
     {
      while ($dados_receita = mysqli_fetch_array($sql_query_cabecalho))
     {
      $id_receita = $dados_receita['id_receita'];
      $nr_receita = $dados_receita['ano']."-".$dados_receita['unidade_id_unidade']."-".$dados_receita['numero'];
      $nome = $dados_receita['paciente'];
      //obter algum cartao sus
      $sql="select * from cartao_sus where paciente_id_paciente='$dados_receita[id_paciente]'";
      $cartao_info=mysqli_query($db, $sql);
      erro_sql("Cartao SUS", $db, "");
      if(mysqli_num_rows($cartao_info)>0){
        $cartao_sus_info=mysqli_fetch_object($cartao_info);
        $cartao_sus=$cartao_sus_info->cartao_sus;
      }
      else{
        $cartao_sus="0";
      }
      $data_emissao = $dados_receita['data_emissao'];
      //$data_dispensasao = $dados_receita['data_ult_disp'];
      //$data_dispensasao = substr($data_dispensasao,8,2)."/".substr($data_dispensasao,5,2)."/".substr($data_dispensasao,0,4);
      $nomeprescritor = $dados_receita['prescritor'];
      $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);
      $unidade = $dados_receita['unidade_id_unidade'];
      $und_user = $dados_receita['nome_unidade_sistema'];
      $id_movto = $dados_receita['id_movto_geral'];
    }
 require "../../fpdf152/Class.Pdf.inc.php";
  DEFINE("FPDF_FONTPATH","font/");

    $pdf = new PDF('L','cm','A5'); //P: Portrait (Retrato) / L = Landscape (Paisagem)

    //$pdf->SetName($nome_rel);
    $pdf->SetName("Recibo da Receita");
    $pdf->SetUnd($und_user);
    //$pdf->SetNomeAplic($aplic);
    $pdf->SetNomeAplic("recibo_receita_pdf.php");
}
else
    {
      $msg_erro = "Não existe recibo para a receita pesquisada!";//.$campos_obr;
      ?>
        <script>
          alert('<?=$msg_erro?>');
          window.close();
       </script>
      <?
    }
  $pdf->Open();
  cabecalho();
  $pdf->Cell(array_sum($w),0,'','T');

   if ($id_movto <>'')
    {
     $sql = "select mat.codigo_material, mat.descricao, irc.data_ult_disp, mov.data_movto,
                 irc.qtde_disp_anterior, irc.qtde_disp_mes,irc.qtde_prescrita,
                 img.lote, img.validade, img.qtde, usr.nome as dispensador, usr.matricula, mov.id_movto_geral , usr2.nome as autorizador
            from itens_receita irc
                 inner join material mat on mat.id_material = irc.material_id_material
                 left join movto_geral mov on irc.receita_id_receita = mov.receita_id_receita
                 left join itens_movto_geral img on mov.id_movto_geral = img.movto_geral_id_movto_geral
                      and irc.material_id_material = img.material_id_material
                 left join usuario usr on mov.usuario_id_usuario = usr.id_usuario
                 left join usuario usr2 on usr2.id_usuario = img.usuario_autorizador
           where mat.status_2 = 'A'
                 and mat.flg_dispensavel = 'S'
                 and irc.receita_id_receita ='$id_receita'
                 order by mov.data_movto, usr.nome, mov.id_movto_geral, mat.descricao";

  /*   select mat.codigo_material, mat.descricao, irc.data_ult_disp, mov.data_movto,
                 irc.qtde_disp_anterior, irc.qtde_disp_mes,irc.qtde_prescrita,
                 img.lote, img.validade, img.qtde, usr.nome as dispensador, usr.matricula, mov.id_movto_geral , usr2.nome as autorizador
            from itens_receita irc
                 inner join material mat on mat.id_material = irc.material_id_material
                 left join movto_geral mov on irc.receita_id_receita = mov.receita_id_receita
                 left join itens_movto_geral img on mov.id_movto_geral = img.movto_geral_id_movto_geral
                      and irc.material_id_material = img.material_id_material
                 left join usuario usr on mov.usuario_id_usuario = usr.id_usuario
                 left join usuario usr2 on usr2.id_usuario = img.usuario_autorizador
           where mat.status_2 = 'A'
                 and mat.flg_dispensavel = 'S'
                 and irc.receita_id_receita ='$id_receita'
                 order by usr.nome, mov.id_movto_geral";*/
    }
    else
    {
     $sql = "select mat.codigo_material, mat.descricao, irc.qtde_prescrita, usr.nome as dispensador, usr.matricula
               from receita rec
                    inner join itens_receita irc on rec.id_receita = irc.receita_id_receita
                    inner join material mat on irc.material_id_material = mat.id_material
                    inner join usuario usr on rec.usua_incl = usr.id_usuario
              where rec.id_receita = $id_receita  order by mat.descricao";
      }

  //  echo $sql;
    $sql_query = mysqli_query($db, $sql);
    erro_sql("Receita", $db, "");
    echo mysqli_error($db);
    if (mysqli_num_rows($sql_query) > 0)
    {
      $fill = 0;
      $cont_linhas = 0;

     while ($dados_receita = mysqli_fetch_array($sql_query))
     {
      $id_receita = $dados_receita['id_receita'];
      $data_dispensasao = $dados_receita['data_movto'];
      $data_dispensasao = substr($data_dispensasao,8,2)."/".substr($data_dispensasao,5,2)."/".substr($data_dispensasao,0,4);
      $id_movto = $dados_receita['id_movto_geral'];
      $dispensado = $dados_receita['matricula']." - ".$dados_receita['dispensador'];
      $inclusor = $dados_receita['inclusor'];
      $med_atual = $dados_receita['codigo_material']." - ".$dados_receita['descricao'];
      $qtd_pre_atual = intval($dados_receita['qtde_prescrita']);
      $qtd_dsan_at = intval($dados_receita['qtde_disp_anterior']);
      $qtd_dis_atual = intval($dados_receita['qtde']);
      
      if (($id_movto_ant=='') or ($id_movto<>$id_movto_ant))
       {
        $id_movto_ant=$id_movto;
        $pdf->Ln(5);
        $pdf->SetFont('','B');
        $pdf->Cell(28,5,"Dispensado por:",0,0,"L");
        $pdf->SetFont('','');
        $pdf->Cell(102,5,$dispensado,0,0,"L");
        $pdf->SetFont('','B');
        $pdf->Cell(35,5,"Data da dispensação:",0,0,"L");
        $pdf->SetFont('','');
        $pdf->Cell(100,5,$data_dispensasao,0,1,"L");
        $cont_linhas = $cont_linhas + 2;
        }
 
     if ($cont_linhas > 20)
      {
       cabecalho();
       $pdf->Cell(array_sum($w),0,'','T');
       $cont_linhas = 0;
       $med_anterior = '';
       $pdf->Ln();
       if ($id_movto==$id_movto_ant)
         {
          $id_movto_ant=$id_movto;
          $pdf->Ln(5);
          $pdf->SetFont('','B');
          $pdf->Cell(28,5,"Dispensado por:",0,0,"L");
          $pdf->SetFont('','');
          $pdf->Cell(102,5,$dispensado,0,0,"L");
          $pdf->SetFont('','B');
          $pdf->Cell(35,5,"Data da dispensação:",0,0,"L");
          $pdf->SetFont('','');
          $pdf->Cell(100,5,$data_dispensasao,0,1,"L");
          }
        }

        cabecalho_tabela($med_atual, $qtd_pre_atual, $qtd_dis_atual);
        $fill = 0;
        $cont_linhas = $cont_linhas + 2;

        $validade = ((substr($dados_receita['validade'],8,2))."/".(substr($dados_receita['validade'],5,2))."/".(substr($dados_receita['validade'],0,4)));
        $pdf->Cell($w[0],5,$dados_receita['lote'],'LR',0,'L',$fill);
        $pdf->Cell($w[1],5,$validade,'LR',0,'C',$fill);
        $pdf->Cell($w[2],5,intval($dados_receita['qtde'])." ",'LR',0,'L',$fill);

      if ($dados_receita['autorizador'] <> '')
         $pdf->Cell($w[3],5," Autorizado por: ".$dados_receita['autorizador'],'LR',0,'L',$fill);
       else
          $pdf->Cell($w[3],5," ",'LR',0,'L',$fill);
          
        $pdf->Ln();
        $fill=!$fill;
        $cont_linhas = $cont_linhas + 1;
        $pdf->Cell(array_sum($w),0,'','T');
     }
   }

  $pdf->Output();
  $pdf->Close();
 }


?>
