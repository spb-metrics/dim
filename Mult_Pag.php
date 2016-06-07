<?php
/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/* ----------------------------------------------------------------------------- 
 Corrigido o comando SQL (LIMIT) para postgres 7.4
 Por: Gilson T. F. em 21/06/2006 
 ----------------------------------------------------------------------------- */
/*
A classe navbar de Copyright Joao Prado Maia (jpm@phpbrasil.com) e tradu��o de
Thomas Gonzalez Miranda (thomasgm@hotmail.com) baixada do site www.phpbrasil.com
em 06/05/2002 foi modificada para melhor entendimento do seu funcionamento e
aperfei�oada deste que apareceram alguns "bugs", sendo transformada como classe
Mult_Pag (Multiplas paginas).
As informa��es acima foram retiradas da vers�o 1.3 da classe navbar do arquivo
navbar.zip.
Adapta��o realizada por Marco A. D. Freitas (madf@splicenet.com.br) entre
06 e 09/05/2002.

Construi esta pequena classe para navega��o din�mica de links. Observe
por favor a simplicidade deste c�digo. Este c�digo � livre em
toda maneira que voc� puder imaginar. Se voc� o usar em seu
pr�prio script, por favor deixo os cr�ditos como est�o. Tamb�m,
envie-me um e-mail se voc� o fizer, isto me deixa feliz :-)

Abaixo est� um exemplo de como utilizar esta classe:
=====================================================

// conexao ao BD
$conexao = mysqli_connect("host", "user", "senha");
mysqli_select_db("nome_BD");

// definicoes de variaveis
$max_links = 10; // m�ximo de links � serem exibidos
$max_res = 8; // m�ximo de resultados � serem exibidos por tela ou pagina
$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
$mult_pag->num_pesq_pag = $max_res; // define o n�mero de pesquisas (detalhada ou n�o) por p�gina
// consulta a ser realizada, abaixo consta um exemplo:
$sql = "SELECT nome, email, comentario FROM mural order by codigo desc";

// metodo que realiza a pesquisa
$resultado = $mult_pag->executar($sql, $conexao, "otimizada", "mysqli");
$reg_pag = mysqli_num_rows($resultado); // total de registros por paginas ou telas

// visualizacao do conteudo
for ($n = 0; $n < $reg_pag; $n++) {
  $linha = mysqli_fetch_object($resultado); // retorna o resultado da pesquisa linha por linha em um array
  // relaciona o resultado com o seu devido campo da tabela, por exemplo:
  $email = $linha->email;
  $nome = $linha->nome;
  $comentario = $linha->comentario;

  echo "
<TABLE WIDTH=\"100%\">
<TR>
<TD WIDTH=\"25%\">$nome</TD>
</TR>
<TR>
<TD WIDTH=\"25%\">$email</TD>
</TR>
<TR>
<TD WIDTH=\"25%\">$comentario</TD>
</TR>
</TABLE>";
}

// pega todos os links e define que 'Pr�xima' e 'Anterior' ser�o exibidos como texto plano
$todos_links = $mult_pag->Construir_Links("todos", "sim");
echo "<P>Esta � a lista de todos os links paginados</P>\n";
for ($n = 0; $n < count($todos_links); $n++) {
  echo $todos_links[$n] . "&nbsp;&nbsp;";
}

// fun��o que limita a quantidade de links no rodape
$links_limitados = $mult_pag->Mostrar_Parte($todos_links, $coluna, $max_links);
echo "<P>Esta � a lista dos links limitados</P>\n";
for ($n = 0; $n < count($links_limitados); $n++) {
  echo $links_limitados[$n] . "&nbsp;&nbsp;";
}
*/

// classe que multiplica paginas
class Mult_Pag {
  // Valores padr�o para a navega��o dos links
  var $num_pesq_pag;
  var $str_anterior = "Anterior";
  var $str_proxima = "Pr�xima";
  // Vari�veis usadas internamente
  var $nome_arq;
  var $total_reg;
  var $pagina;
  
  /*
     Metodo construtor. Isto � somente usado para setar
     o n�mero atual de colunas e outros m�todos que
     podem ser re-usados mais tarde.
  */
 /* function Mult_Pag ()
  {
    global $pagina;
    $this->pagina = $pagina ? $pagina : 0;
  }*/
function Mult_Pag ($pagina)
  {
    $this->pagina = $pagina ? $pagina : 0;
  }

  /*

     O pr�ximo m�todo roda o que � necess�rio para as queries.
     � preciso rod�-lo para que ele pegue o total
     de colunas retornadas, e em segundo para pegar o total de
     links limitados.

         $sql par�metro:
           . o par�metro atual da query que ser� executada

         $conexao par�metro:
           . a liga��o da conex�o do banco de dados

         $tipo par�metro:
           . "mysqli" - usa fun��es php mysqli
           . "pgsql" - usa fun��es pgsql php
  */
  function Executar($sql, $conexao, $velocidade, $tipo)
  {
    // variavel para o inicio das pesquisas
    $inicio_pesq = $this->pagina * $this->num_pesq_pag;

    if ($velocidade == "otimizada") {
      $total_sql = preg_replace("/SELECT (.*?) FROM /sei", "'SELECT COUNT(*) FROM '", $sql);
    }
    else if($velocidade == "consulta_profissional"){
      $total_sql = preg_replace("/SELECT (.*?) FROM /sei", "'SELECT COUNT(distinct f.id_fabricante, m.codigo_material, m.descricao , e.lote, f.descricao, e.material_id_material) as pag FROM '", $sql);
  }
  else {
        if($velocidade=="consulta_estoque_unidade"){
          $total_sql = preg_replace("/SELECT (.*?) FROM /sei", "'SELECT COUNT(distinct uni.id_unidade, uni.nome, est.material_id_material, mat.descricao) FROM '", $sql);
        }
        if($velocidade=="consulta_estoque"){
          $total_sql = "select count(*) from (".$sql.") as teste";
        }
        else{
          $total_sql = preg_replace("/SELECT (.*?) FROM /sei", "'SELECT COUNT(distinct mat.codigo_material, mat.descricao, est.material_id_material) FROM '", $sql);
        }

    }
   //echo $total_sql;
    // tipo da pesquisa
    if ($tipo == "mysqli") {
      $resultado = mysqli_query($conexao, $total_sql);
      erro_sql("Pesquisa", $conexao, "");
      if(mysqli_num_rows($resultado) != 0 )
      {
        $qtde=mysqli_fetch_array($resultado);
        $this->total_reg = $qtde[0]; // total de registros da pesquisa inteira
        $sql .= " LIMIT $inicio_pesq, $this->num_pesq_pag";
      }

      $resultado = mysqli_query($conexao, $sql); // pesquisa com limites por pagina
      erro_sql("Pesquisa Limitada", $conexao, "");
    }
    else if ($tipo == "pgsql") {    
      $resultado = pg_exec($conexao, $total_sql);
      if ( pg_numrows( $resultado )  > 0 ) {
          // total de registros da pesquisa inteira
         $this->total_reg = pg_numrows( $resultado );//pg_Result($resultado, 0, 0);
      }
      $sql .= " LIMIT $this->num_pesq_pag OFFSET $inicio_pesq";
      $resultado = pg_Exec($conexao, $sql);// pesquisa com limites por pagina
    }
    return $resultado;
  }

  /*
     Este m�todo cria uma string que ir� ser adicionada �
     url dos links de navega��o. Isto � especialmente importante
     para criar links din�micos, ent�o se voc� quiser adicionar
     op��es adicionais � estas queries, a classe de navega��o
     ir� adicionar automaticamente aos links de navega��o
     din�micos.
  */
  function Construir_Url()
  {
    global $REQUEST_URI, $REQUEST_METHOD, $HTTP_GET_VARS, $HTTP_POST_VARS;

	$REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
	$REQUEST_URI = $_SERVER['REQUEST_URI'];
	$HTTP_GET_VARS = $_GET;
	$HTTP_POST_VARS = $_POST;	
    //if ($REQUEST_METHOD == "GET")    $cgi = $HTTP_GET_VARS;
    //else                             $cgi = $HTTP_POST_VARS;	
	
	if ($REQUEST_METHOD == "GET") {
		$cgi = $HTTP_GET_VARS;
    } else {
		$cgi = $HTTP_POST_VARS;
	}
	
    reset($cgi); // posiciona no inicio do array

    // separa a coluna com o seu respectivo valor
    while (list($chave, $valor) = each($cgi))
//      if ($chave != "pagina")
      if ($chave != "pagina_a_exibir" && $chave!="pagina" && $chave!="i" && $chave!="e" && $chave!="a" && $chave!="r")
        $query_string .= "&" . $chave . "=" . $valor;

    return $query_string;
  }

  /*
     Este m�todo cria uma liga��o de todos os links da barra de
     navega��o. Isto � �til, pois � totalmente independete do layout
     ou design da p�gina. Este m�todo retorna a liga��o dos links
     chamados no script php, sendo assim, voc� pode criar links de
     navega��o com o conte�do atual da p�gina.

         $opcao par�metro:
          . "todos" - retorna todos os links de navega��o
          . "numeracao" - retorna apenas p�ginas com links numerados
          . "strings" - retornar somente os links 'Pr�xima' e/ou 'Anterior'

         $mostra_string par�metro:
          . "nao" - mostra 'Pr�xima' ou 'Anterior' apenas quando for necess�rios
          . "sim" - mostra 'Pr�xima' ou 'Anterior' de qualqur maneira
  */
  function Construir_Links($opcao, $mostra_string)
  {
    $extra_vars = $this->Construir_Url();
    $arquivo = $this->nome_arq;
    $num_mult_pag = ceil($this->total_reg / $this->num_pesq_pag); // numero de multiplas paginas
    $indice = -1; // indice do array final

    for ($atual = 0; $atual < $num_mult_pag; $atual++) {
/*
      // escreve a string esquerda (Pagina Anterior)
      if ((($opcao == "todos") || ($opcao == "strings")) && ($atual == 0)) {
        if ($this->pagina != 0){
          $array[++$indice] = '<A HREF="' . $arquivo . '?pagina=' . ($this->pagina - 1) . $extra_vars . '">' . $this->str_anterior . '</A>';                 }
        elseif (($this->pagina == 0) && ($mostra_string == "sim"))
          $array[++$indice] = $this->str_anterior;
      }
*/
      // escreve a numeracao (1 2 3 ...)
      if (($opcao == "todos") || ($opcao == "numeracao")) {
	  //exit('this->pag = '.$this->pagina.'  -  atual='.$atual);
        if ($this->pagina == $atual)
          $array[++$indice] = ($atual > 0 ? ($atual + 1) : 1);
        else
//          $array[++$indice] = '<A HREF="' . $arquivo . '?pagina=' . $atual . $extra_vars . '">' . ($atual + 1) . '</A>';
          $array[++$indice] = '<A HREF="' . $arquivo . '?pagina=' . $atual . '&pagina_a_exibir=' . ($atual+1) . $extra_vars . '">' . ($atual + 1) . '</A>';
      }
/*
      // escreve a string direita (Proxima Pagina)
      if ((($opcao == "todos") || ($opcao == "strings")) && ($atual == ($num_mult_pag - 1))) {
        if ($this->pagina != ($num_mult_pag - 1))
          $array[++$indice] = '<A HREF="' . $arquivo . '?pagina=' . ($this->pagina + 1) . $extra_vars . '">' . $this->str_proxima . '</A>';
        elseif (($this->pagina == ($num_mult_pag - 1)) && ($mostra_string == "sim"))
          $array[++$indice] = $this->str_proxima;
      }
*/
    }
    return $array;
  }

  /*
     Este m�todo � uma extens�o do m�todo Construir_Links() para
     que possa ser ajustado o limite 'n' de n�mero de links na p�gina.
     Isto � muito �til para grandes bancos de dados que desejam n�o
     ocupar todo o espa�o da tela para mostrar toda a lista de links
     paginados.

         $array par�metro:
          . retorna o array de Construir_Links()

         $atual par�metro:
          . a vari�vel da 'pagina' atual das p�ginas paginadas. ex: pagina=1

         $tamanho_desejado par�metro:
          . o n�mero desejado de links � serem exibidos
  */
  function Mostrar_Parte($array, $atual, $tam_desejado)
  {
    $size = count($array);
    if (($size <= 2) || ($size < $tam_desejado)) {
      $temp = $array;
    }
    else {
      $temp = array();
      if (($atual + $tamanho_desejado) > $size) {
        $temp = array_slice($array, $size - $tam_desejado);
      } else {
        $temp = array_slice($array, $atual, $tam_desejado);
        if ($size >= $tamanho_desejado) {
          array_push($temp, $array[$size - 1]);
        }
      }
      if ($atual > 0) {
        array_unshift($temp, $array[0]);
      }
    }
    return $temp;
  }
  
  function primeria_pagina($URL, $aplicacao){
    $todos_links = $this->Construir_Links("todos", "sim");
    $extra_vars = $this->Construir_Url();
    if(count($todos_links)<=1){
      echo "<IMG SRC='$URL/imagens/i.p.first.gif' BORDER='0' title='Ir para a primeira p�gina'>";
    }
    else{
      echo "<a href='$URL$aplicacao?pagina=0&pagina_a_exibir=1$extra_vars'>
              <IMG SRC='$URL/imagens/i.p.first.gif' BORDER='0' title='Ir para a primeira p�gina'>
            </a>";
    }

  }
  
  function pagina_anterior($URL, $aplicacao, $pagina_exibicao){
    $todos_links = $this->Construir_Links("todos", "sim");
    $extra_vars = $this->Construir_Url();
    if($pagina_exibicao==1){
      $pagina_anterior=1;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a p�gina anterior'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($pagina_anterior-1) . "&pagina_a_exibir=$pagina_anterior$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a p�gina anterior'>
              </a>";
      }
    }
    else{
      $pagina_anterior = $pagina_exibicao - 1;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a p�gina anterior'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($pagina_anterior-1) . "&pagina_a_exibir=$pagina_anterior$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a p�gina anterior'>
              </a>";
      }
    }
  }
  
  function tamanho_links($max_links){
    $todos_links = $this->Construir_Links("todos", "sim");
    $tam=count($todos_links);
    if($tam==0){
      $tam++;
    }
    else{
      if($tam<=$max_links){
        $tam=count($todos_links);
      }
      else{
        $tam=$max_links;
      }
    }
    echo $tam;
  }
  
  function numeracao_paginas($max_links, $pagina_exibicao){
    $todos_links = $this->Construir_Links("todos", "sim");
    if(count($todos_links)==0){
      echo "[1]";
    }
    else{
      //verifica se eh os 10 primeiros numeros
      if($pagina_exibicao<=$max_links){
        if(count($todos_links)>$max_links){
          $qtde_links=$max_links;
        }
        else{
          $qtde_links=count($todos_links);
        }
        for ($n = 0; $n < $qtde_links; $n++) {
          if($n+1<$qtde_links){
            echo "[" .$todos_links[$n]. "]" . "&nbsp;&nbsp;";
          }
          else{
            echo "[" . $todos_links[$n] . "]";
          }
        }
        $_SESSION[PAGINA_BASE]=1;
      }
      else{
        //verifica se eh o ultimo numero
        if((int)$pagina_exibicao==count($todos_links)){
          $ultima_base=1;
          while($ultima_base<count($todos_links)-$max_links+1){
            $ultima_base+=$max_links;
          }
          for ($n = $ultima_base-1; $n < count($todos_links); $n++) {
            if($n+1<count($todos_links)){
              echo "[" .$todos_links[$n]. "]" . "&nbsp;&nbsp;";
            }
            else{
              echo "[" . $todos_links[$n] . "]";
            }
          }
          $_SESSION[PAGINA_BASE]=$ultima_base;
        }
        else{
          //botao anterior
          if((int)$pagina_exibicao<$_SESSION[PAGINA_BASE]){
            $_SESSION[PAGINA_BASE]-=$max_links;
            for ($n = $_SESSION[PAGINA_BASE]-1; $n < $_SESSION[PAGINA_BASE]+$max_links-1; $n++) {
              if($n+1<$_SESSION[PAGINA_BASE]+$max_links-1){
                echo "[" .$todos_links[$n]. "]" . "&nbsp;&nbsp;";
              }
              else{
                echo "[" . $todos_links[$n] . "]";
              }
            }
          }
          else{
            //botao proximo com numero de paginas incompleta
            if((int)$pagina_exibicao+$max_links-1>count($todos_links)){
              if(substr($pagina_exibicao, -1)=="1" || substr($pagina_exibicao, -1)==$max_links+1){
                $_SESSION[PAGINA_BASE]=(int)$pagina_exibicao;
              }
              for ($n = $_SESSION[PAGINA_BASE]-1; $n < count($todos_links); $n++) {
                if($n+1<count($todos_links)){
                  echo "[" .$todos_links[$n]. "]" . "&nbsp;&nbsp;";
                }
                else{
                  echo "[" . $todos_links[$n] . "]";
                }
              }
            }
            else{
              //botao proximo com numero de pagina completa
              if(substr($pagina_exibicao, -1)=="1" || substr($pagina_exibicao, -1)==$max_links+1){
                $_SESSION[PAGINA_BASE]=(int)$pagina_exibicao;
              }
              for ($n = $_SESSION[PAGINA_BASE]-1; $n < $_SESSION[PAGINA_BASE]+$max_links-1; $n++) {
                if($n+1<$_SESSION[PAGINA_BASE]+$max_links-1){
                  echo "[" .$todos_links[$n]. "]" . "&nbsp;&nbsp;";
                }
                else{
                  echo "[" . $todos_links[$n] . "]";
                }
              }
            }
          }
        }
      }
    }
  }
  
  function proxima_pagina($URL, $aplicacao, $pagina_exibicao, $total_paginas){
    $todos_links = $this->Construir_Links("todos", "sim");
    $extra_vars = $this->Construir_Url();
    if($pagina_exibicao==$total_paginas){
      $proxima_pagina=$total_paginas;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a pr�xima p�gina'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a pr�xima p�gina'>
              </a>";
      }
    }
    else{
      $proxima_pagina=$pagina_exibicao+1;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a pr�xima p�gina'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a pr�xima p�gina'>
              </a>";
      }
    }
  }
  
  function ultima_pagina($URL, $aplicacao, $total_paginas){
    $todos_links = $this->Construir_Links("todos", "sim");
    $extra_vars = $this->Construir_Url();
    if(count($todos_links)<=1){
      echo "<IMG SRC='$URL/imagens/i.p.last.gif' BORDER='0' title='Ir para a �ltima p�gina'>";
    }
    else{
      $proxima_pagina=$total_paginas;
      echo "<a href='$URL$aplicacao?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina$extra_vars'>
              <IMG SRC='$URL/imagens/i.p.last.gif' BORDER='0' title='Ir para a �ltima p�gina'>
            </a>";
    }
  }
}

?>
