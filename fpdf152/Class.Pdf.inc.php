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
// | Arquivo ............: Class.Pdf.inc.php                                         |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Fun��o .............: Classe base dos Relat�rios (.pdf)                         |
// | Data de Cria��o ....: 11/01/2007                                                |
// | �ltima Atualiza��o .: 15/01/2007 - 14:20                                        |
// | Vers�o .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

   require("fpdf.php");

   class PDF extends FPDF
   {
      var $nome;  //nome do relatorio
      var $unidade; // unidade a qual pertence o usu�rio
      var $cabecalho; //cabe�alho para as colunas
      var $aplic;

      function PDF($or = "P")   //construtor: Chama a classe FPDF
      {
         $this->FPDF($or);
      }
      
      function SetCabecalho($cab)  // define o cabe�alho
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
      
      function SetNomeAplic($nome_aplic)   // nomeia o Aplica��o
      {
         $this->aplic = $nome_aplic;
      }
      
      function Header()
      {
         $this->AliasNbPages();  // Define o n�mero total de paginas para a macro {nb}
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
         if( $this->cabecalho ) //Se tem Cabe�alho, Imprime
         {
            $this->SetFont("Arial","",10);
            $this->SetX(10);
            $this->Cell($this->GetStringWidth($this->cabecalho),5,$this->cabecalho,0,1);
         }
      }
      
      function Footer()   // Rodap�: imprime a hora de impress�o e copyright
      {
         $this->SetXY(-10,-8);
         $this->Line(10,$this->GetY()-2, $this->GetX(), $this->GetY()-2);
         $this->SetX(0);
         $this->SetTextColor(0,0,0);
         $this->SetFont("Arial","",8);
         $this->Cell(10,5,"",0,0);
         $this->Cell($this->GetStringWidth($this->aplic),5,$this->aplic,0,0,"L");
         $data = strftime("%d/%m/%Y  %T");
         //$this->Cell(70,5,"p�g. [".$this->PageNo()."] de [{nb}]",0,0,"C"); //  Imprime p�gina X/Total de P�ginas - Retrato
         $this->Cell(200,5,"p�g. [".$this->PageNo()."] de [{nb}]",0,0,"C"); //  Imprime p�gina X/Total de P�ginas - Paisagem
         $this->Cell(0,5,$data,0,0,"R");
      }
   }
?>
