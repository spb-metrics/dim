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
// | Arquivo ............: Class.Pdf.inc.php                                         |
// | Autor ..............: José Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// +---------------------------------------------------------------------------------+
// | Função .............: Classe base dos Relatórios (.pdf)                         |
// | Data de Criação ....: 11/01/2007                                                |
// | Última Atualização .: 15/01/2007 - 14:20                                        |
// | Versão .............: 1.0.0                                                     |
// +---------------------------------------------------------------------------------+

   require("fpdf.php");

   class PDF extends FPDF
   {
      var $nome;  //nome do relatorio
      var $unidade; // unidade a qual pertence o usuário
      var $cabecalho; //cabeçalho para as colunas
      var $aplic;

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
