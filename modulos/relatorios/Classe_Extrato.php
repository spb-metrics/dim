<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// +---------------------------------------------------------------------------------+
// | IMA - Informática de Municípios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensação Individualizada de Medicamentos         |
// | Arquivo ............: Classe_Livro.php                                          |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Classe do Livro de Registro (.pdf)                        |
// | Data de Criação ....: 09/02/2007 - 17:50                                        |
// | Última Atualização .: 12/02/2007 - 10:30                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

require("../../fpdf152/fpdf.php");

class PDF extends FPDF
{
  var $widths;
  var $aligns;
  var $nome;  //nome do relatorio
  var $unidade; // unidade a qual pertence o usuário
  var $cabecalho; //cabeçalho para as colunas
  var $aplic;

  /*function SetData($data)
  {
    $this->data = $data;
  }

  function SetProd($mat_at, $mat_an, $desc)
  {
    $this->mat_at = $mat_at;
    $this->mat_an = $mat_an;
    $this->desc = $desc;
  }

  function AcceptPageBreak()
  {
     $this->Line(10,$this->GetY(), 287, $this->GetY());
     cabecalho_pagina($this->data);
     cabecalho_tabela();
     if ($this->mat_at == $this->mat_an)
     {
       $this->SetFont('Arial','B',12);
       cabecalho_troca($this->mat_at, $this->desc);
       $this->SetFont('Arial','',9);
     }
     $this->Ln(5.4);
     $this->Line(10,$this->GetY(), 287, $this->GetY());
  }   */

  function SetWidths($w)
  {
    //Set the array of column widths
    $this->widths = $w;
  }

  function SetAligns($a)
  {
    //Set the array of column alignments
    $this->aligns = $a;
  }


  
  function Row2($data)
  {
    //Calculate the height of the row
    $nb = 0;
    for($i=0; $i<count($data); $i++)
      $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));

    $h = 5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0; $i<count($data); $i++)
    {
      $w = $this->widths[$i];
      $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
      //Save the current position
      $x = $this->GetX();
      $y = $this->GetY();
      //Draw the border
      //$this->Rect($x,$y,$w,$h,'FD');
      $this->Line($x,$y,$x,$y+$h);
      $this->Line($x+$w,$y,$x+$w,$y+$h);
      //Print the text
      $this->MultiCell($w,5,$data[$i],'LR',$a);
      //$this->MultiCell($w,5,$data[$i],'LRB',$a);
      //Put the position to the right of the cell
      $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
  }

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
      $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw = &$this->CurrentFont['cw'];

    if($w == 0)
      $w = $this->w - $this->rMargin-$this->x;
      
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);

    if($nb>0 and $s[$nb-1]=="\n")
      $nb--;
      
    $sep=-1;  $i=0;   $j=0;   $l=0;   $nl=1;
    while($i<$nb)
    {
      $c = $s[$i];
      if($c == "\n")
      {
        $i++;   $sep=-1;   $j=$i;   $l=0;    $nl++;
        continue;
      }
      if($c == ' ')
        $sep=$i;
      $l += $cw[$c];
      if($l > $wmax)
      {
        if($sep == -1)
        {
          if($i == $j)
            $i++;
        }
        else
          $i=$sep+1;
        $sep=-1;    $j=$i;   $l=0;    $nl++;
      }
      else
       $i++;
      $this->Line(10,22,$this->GetX(),22);
    }
    return $nl;
  }
  


      function PDF($or = "P")   //construtor: Chama a classe FPDF
      {
         $this->FPDF($or);
      }

      function SetCabecalho($cab)  // define o cabeçalho
      {
         $this->cabecalho = $cab;
      }

      function SetName($nomerel)   // nomeia o relatorio
      {
         $this->nome = $nomerel;
      }

      function SetUnd($nome_und)   // nomeia o relatorio
      {
         $this->unidade = $nome_und;
      }

      function SetNomeAplic($nome_aplic)   // nomeia o Aplicação
      {
         $this->aplic = $nome_aplic;
      }

      function Header()
      {
         $this->AliasNbPages();  // Define o número total de paginas para a macro {nb}
         $this->Image("../../imagens/brasao_peqno.jpg",8,2,18,18); // importa uma imagem
         //$this->Image("../../imagens/DIM_logo_pequeno.jpg",178,2,22,18); // importa uma imagem - Retrato
         $this->Image("../../imagens/DIM_logo_pequeno.jpg",265,2,22,18); // importa uma imagem  - Paisagem

         $this->SetFont("Helvetica","B",10);
         $this->SetTextColor(165,0,0);
         $this->Cell(0,5,"Unidade: ".$this->unidade,0,1,"C");
         $this->Cell(0,5,$this->nome,0,1,"C");
         $this->SetFont("Arial","",10);
         $this->SetX(-10);
         $this->Line(10,22,$this->GetX(),22);   //Desenha uma linha
         if( $this->cabecalho ) //Se tem Cabeçalho, Imprime
         {
            $this->SetFont("Arial","",10);
            $this->SetX(10);
            $this->Cell($this->GetStringWidth($this->cabecalho),5,$this->cabecalho,0,1);
         }
      }

      function Footer()   // Rodapé: imprime a hora de impressão e copyright
      {
         $this->SetXY(-10,-8);
         $this->Line(10,$this->GetY()-2, $this->GetX(), $this->GetY()-2);
         $this->SetX(0);
         $this->SetTextColor(0,0,0);
         $this->SetFont("Arial","",8);
         $this->Cell(10,5,"",0,0);
         $this->Cell($this->GetStringWidth($this->aplic),5,$this->aplic,0,0,"L");
         $data = strftime("%d/%m/%Y  %T");
         //$this->Cell(70,5,"pág. [".$this->PageNo()."] de [{nb}]",0,0,"C"); //  Imprime página X/Total de Páginas - Retrato
         $this->Cell(200,5,"pág. [".$this->PageNo()."] de [{nb}]",0,0,"C"); //  Imprime página X/Total de Páginas - Paisagem
         $this->Cell(0,5,$data,0,0,"R");
      }

   }

?>
