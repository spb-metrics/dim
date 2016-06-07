<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2007             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM - Dispensa��o Individualizada de Medicamentos         |
// | Arquivo ............: Classe_Livro.php                                          |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Classe do Livro de Registro (.pdf)                        |
// | Data de Cria��o ....: 09/02/2007 - 17:50                                        |
// | �ltima Atualiza��o .: 12/02/2007 - 10:30                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

require("../../fpdf152/fpdf.php");

class PDF extends FPDF
{
  /*var $data;
  var $mat_at;
  var $mat_an;
  var $desc;*/
  var $widths;
  var $aligns;

  function PDF($or = "P")   //construtor: Chama a classe FPDF
  {
    $this->FPDF($or);
  }

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

  function Row($data)
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
      $this->MultiCell($w,5,$data[$i],0,$a);
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
    }
    return $nl;
  }
}
?>
