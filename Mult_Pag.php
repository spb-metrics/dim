<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/* ----------------------------------------------------------------------------- 
 Corrigido o comando SQL (LIMIT) para postgres 7.4
 Por: Gilson T. F. em 21/06/2006 
 ----------------------------------------------------------------------------- */
/*
A classe navbar de Copyright Joao Prado Maia (jpm@phpbrasil.com) e tradução de
Thomas Gonzalez Miranda (thomasgm@hotmail.com) baixada do site www.phpbrasil.com
em 06/05/2002 foi modificada para melhor entendimento do seu funcionamento e
aperfeiçoada deste que apareceram alguns "bugs", sendo transformada como classe
Mult_Pag (Multiplas paginas).
As informações acima foram retiradas da versão 1.3 da classe navbar do arquivo
navbar.zip.
Adaptação realizada por Marco A. D. Freitas (madf@splicenet.com.br) entre
06 e 09/05/2002.

Construi esta pequena classe para navegação dinâmica de links. Observe
por favor a simplicidade deste código. Este código é livre em
toda maneira que você puder imaginar. Se você o usar em seu
próprio script, por favor deixo os créditos como estão. Também,
envie-me um e-mail se você o fizer, isto me deixa feliz :-)

Abaixo está um exemplo de como utilizar esta classe:
=====================================================

// conexao ao BD
$conexao = mysqli_connect("host", "user", "senha");
mysqli_select_db("nome_BD");

// definicoes de variaveis
$max_links = 10; // máximo de links à serem exibidos
$max_res = 8; // máximo de resultados à serem exibidos por tela ou pagina
$mult_pag = new Mult_Pag(); // cria um novo objeto navbar
$mult_pag->num_pesq_pag = $max_res; // define o número de pesquisas (detalhada ou não) por página
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

// pega todos os links e define que 'Próxima' e 'Anterior' serão exibidos como texto plano
$todos_links = $mult_pag->Construir_Links("todos", "sim");
echo "<P>Esta é a lista de todos os links paginados</P>\n";
for ($n = 0; $n < count($todos_links); $n++) {
  echo $todos_links[$n] . "&nbsp;&nbsp;";
}

// função que limita a quantidade de links no rodape
$links_limitados = $mult_pag->Mostrar_Parte($todos_links, $coluna, $max_links);
echo "<P>Esta é a lista dos links limitados</P>\n";
for ($n = 0; $n < count($links_limitados); $n++) {
  echo $links_limitados[$n] . "&nbsp;&nbsp;";
}
*/

// classe que multiplica paginas
class Mult_Pag {
  // Valores padrão para a navegação dos links
  var $num_pesq_pag;
  var $str_anterior = "Anterior";
  var $str_proxima = "Próxima";
  // Variáveis usadas internamente
  var $nome_arq;
  var $total_reg;
  var $pagina;
  
  /*
     Metodo construtor. Isto é somente usado para setar
     o número atual de colunas e outros métodos que
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

     O próximo método roda o que é necessário para as queries.
     É preciso rodá-lo para que ele pegue o total
     de colunas retornadas, e em segundo para pegar o total de
     links limitados.

         $sql parâmetro:
           . o parâmetro atual da query que será executada

         $conexao parâmetro:
           . a ligação da conexão do banco de dados

         $tipo parâmetro:
           . "mysqli" - usa funções php mysqli
           . "pgsql" - usa funções pgsql php
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
     Este método cria uma string que irá ser adicionada à
     url dos links de navegação. Isto é especialmente importante
     para criar links dinâmicos, então se você quiser adicionar
     opções adicionais à estas queries, a classe de navegação
     irá adicionar automaticamente aos links de navegação
     dinâmicos.
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
     Este método cria uma ligação de todos os links da barra de
     navegação. Isto é útil, pois é totalmente independete do layout
     ou design da página. Este método retorna a ligação dos links
     chamados no script php, sendo assim, você pode criar links de
     navegação com o conteúdo atual da página.

         $opcao parâmetro:
          . "todos" - retorna todos os links de navegação
          . "numeracao" - retorna apenas páginas com links numerados
          . "strings" - retornar somente os links 'Próxima' e/ou 'Anterior'

         $mostra_string parâmetro:
          . "nao" - mostra 'Próxima' ou 'Anterior' apenas quando for necessários
          . "sim" - mostra 'Próxima' ou 'Anterior' de qualqur maneira
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
     Este método é uma extensão do método Construir_Links() para
     que possa ser ajustado o limite 'n' de número de links na página.
     Isto é muito útil para grandes bancos de dados que desejam não
     ocupar todo o espaço da tela para mostrar toda a lista de links
     paginados.

         $array parâmetro:
          . retorna o array de Construir_Links()

         $atual parâmetro:
          . a variável da 'pagina' atual das páginas paginadas. ex: pagina=1

         $tamanho_desejado parâmetro:
          . o número desejado de links à serem exibidos
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
      echo "<IMG SRC='$URL/imagens/i.p.first.gif' BORDER='0' title='Ir para a primeira página'>";
    }
    else{
      echo "<a href='$URL$aplicacao?pagina=0&pagina_a_exibir=1$extra_vars'>
              <IMG SRC='$URL/imagens/i.p.first.gif' BORDER='0' title='Ir para a primeira página'>
            </a>";
    }

  }
  
  function pagina_anterior($URL, $aplicacao, $pagina_exibicao){
    $todos_links = $this->Construir_Links("todos", "sim");
    $extra_vars = $this->Construir_Url();
    if($pagina_exibicao==1){
      $pagina_anterior=1;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a página anterior'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($pagina_anterior-1) . "&pagina_a_exibir=$pagina_anterior$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a página anterior'>
              </a>";
      }
    }
    else{
      $pagina_anterior = $pagina_exibicao - 1;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a página anterior'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($pagina_anterior-1) . "&pagina_a_exibir=$pagina_anterior$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.previous.gif' BORDER='0' title='Ir para a página anterior'>
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
        echo "<IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a próxima página'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a próxima página'>
              </a>";
      }
    }
    else{
      $proxima_pagina=$pagina_exibicao+1;
      if(count($todos_links)<=1){
        echo "<IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a próxima página'>";
      }
      else{
        echo "<a href='$URL$aplicacao?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina$extra_vars'>
                <IMG SRC='$URL/imagens/i.p.next.gif' BORDER='0' title='Ir para a próxima página'>
              </a>";
      }
    }
  }
  
  function ultima_pagina($URL, $aplicacao, $total_paginas){
    $todos_links = $this->Construir_Links("todos", "sim");
    $extra_vars = $this->Construir_Url();
    if(count($todos_links)<=1){
      echo "<IMG SRC='$URL/imagens/i.p.last.gif' BORDER='0' title='Ir para a última página'>";
    }
    else{
      $proxima_pagina=$total_paginas;
      echo "<a href='$URL$aplicacao?pagina=" . ($proxima_pagina-1) . "&pagina_a_exibir=$proxima_pagina$extra_vars'>
              <IMG SRC='$URL/imagens/i.p.last.gif' BORDER='0' title='Ir para a última página'>
            </a>";
    }
  }
}

?>
