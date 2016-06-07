<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $configuracao = "../config/config.inc.php";
  if (!file_exists($configuracao))
  {
    exit("Não existe arquivo de configuração!");
  }
  require ($configuracao);

  $material=$_GET[material];

  // EXECUTA A INSTRUÇÃO SELECT PASSANDO O QUE O USUARIO DIGITOU

    $sql_estoque ="SELECT
                         id_estoque, lote, fabricante_id_fabricante, validade,
                         quantidade, flg_bloqueado, descricao
                   FROM
                       (
                         select
                               e.id_estoque, e.lote, e.fabricante_id_fabricante, e.validade,
                               e.quantidade, e.flg_bloqueado, f.descricao
                         from
                               estoque e,
                               fabricante f
                         where
                               material_id_material = $material
                               and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                               and e.quantidade > 0
                               and e.validade >= now()
                               and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                               and e.fabricante_id_fabricante = f.id_fabricante
                         order by
                               validade,
                               lote
                       ) as a

                   UNION ALL

                   SELECT
                         id_estoque, lote, fabricante_id_fabricante, validade,
                         quantidade, flg_bloqueado, descricao
                   FROM
                       (
                        select
                              e.id_estoque, e.lote, e.fabricante_id_fabricante, e.validade,
                              e.quantidade, e.flg_bloqueado, f.descricao
                        from
                              estoque e,
                              fabricante f
                        where
                              material_id_material = $material
                              and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                              and e.quantidade > 0
                              and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                              and e.validade < now()
                              and e.fabricante_id_fabricante = f.id_fabricante
                        order by
                              validade,
                              lote
                       ) as b

                   UNION ALL

                   SELECT
                         id_estoque, lote, fabricante_id_fabricante, validade,
                         quantidade, flg_bloqueado, descricao
                   FROM
                       (
                        select
                              e.id_estoque, e.lote, e.fabricante_id_fabricante, e.validade,
                              e.quantidade, e.flg_bloqueado, f.descricao
                        from
                              estoque e,
                              fabricante f
                        where
                              material_id_material = $material
                              and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                              and e.quantidade > 0
                              and e.flg_bloqueado = 'S'
                              and e.validade < now()
                              and e.fabricante_id_fabricante = f.id_fabricante
                        order by
                              validade,
                              lote
                       ) as c

                   UNION ALL

                   SELECT
                         id_estoque, lote, fabricante_id_fabricante, validade,
                         quantidade, flg_bloqueado, descricao
                   FROM
                       (
                        select
                              e.id_estoque, e.lote, e.fabricante_id_fabricante, e.validade,
                              e.quantidade, e.flg_bloqueado, f.descricao
                        from
                              estoque e,
                              fabricante f
                        where
                              material_id_material = $material
                              and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                              and e.quantidade > 0
                              and e.flg_bloqueado = 'S'
                              and e.validade > now()
                              and e.fabricante_id_fabricante = f.id_fabricante
                        order by
                              validade,
                              lote
                       ) as d";
    $resultado=mysqli_query($db, $sql_estoque);

  //VERIFICA A QUANTIDADE DE REGISTROS RETORNADOS
  $linhas=mysqli_num_rows($resultado);

   //EXECUTA UM LOOP PARA MOSTRAR OS LOTES
   // DENTRO DO DIV 'PAGINA'
   echo "<table id='tabela1' bgcolor='#D8DDE3' width='100%' cellpadding='0' cellspacing='1' border='0'>";
   echo " <tr bgcolor='#D8DDE3' class='coluna_tabela'>";
   echo "     <td width='10%' align='center'>";
   echo "      Lote";
   echo "     </td>";
   echo "     <td width='27%' align='center'>";
   echo "      Fabricante";
   echo "     </td>";
   echo "     <td width='10%' align='center'>";
   echo "      Validade";
   echo "     </td>";
   echo "     <td width='8%' align='center'>";
   echo "      Estoque";
   echo "     </td>";
   echo "     <td width='8%' align='center'>";
   echo "      Qtde. a Dispensar";
   echo "     </td>";
   echo "    </tr>";
  if($linhas>0)
  {
   while($pegar=mysqli_fetch_array($resultado))
   {
    echo "<tr class='linha_tabela'>";
     echo "<td bgcolor='#FFFFFF' align='left'><input name='lote' id='"."lot".$pegar[id_estoque]."' type='hidden' size='5' value='".$pegar[lote]."'>".$pegar[lote]."</td>";
     echo "<td bgcolor='#FFFFFF' align='left'><input name='id_fabricante' id='"."idf".$pegar[id_estoque]."' type='hidden' size='5' value='".$pegar[fabricante_id_fabricante]."'><input name='fabricante' id='"."fab".$pegar[id_estoque]."' type='hidden' size='5' value='".$pegar[descricao]."'>".$pegar[descricao]."</td>";
     echo "<td bgcolor='#FFFFFF' align='center'><input name='validade' id='"."val".$pegar[id_estoque]."' type='hidden' size='5' value='".substr($pegar[validade],-2)."/".substr($pegar[validade],5,2)."/".substr($pegar[validade],0,4)."'>".substr($pegar[validade],-2)."/".substr($pegar[validade],5,2)."/".substr($pegar[validade],0,4)."</td>";
     echo "<td bgcolor='#FFFFFF' align='right'><input name='estoque' id='"."etq".$pegar[id_estoque]."' type='hidden' size='5' value='".intval($pegar[quantidade])."'>".intval($pegar[quantidade])."</td>";
     if (($pegar[validade] >= date("Y-m-d")) && ($pegar[flg_bloqueado]== '' || $pegar[flg_bloqueado]== 'N'))
     {
        echo "<td bgcolor='#FFFFFF' align='center'><input name='dispensar' id='"."disp".$pegar[id_estoque]."' type='text' size='5' value='' onKeyPress='return numbers(event);'></td>";
     }
     else if (($pegar[validade] < date("Y-m-d")) && ($pegar[flg_bloqueado]== 'S'))
     {
       echo "<td bgcolor='#FFFFFF' align='center'><img src='".URL."/imagens/bolinhas/ball_vermelha.gif' border='0' title='Lote Vencido e Bloqueado'></td>";
     }
     else if (($pegar[validade] < date("Y-m-d")) && ($pegar[flg_bloqueado]== ''|| $pegar[flg_bloqueado]== 'N'))
     {
       echo "<td bgcolor='#FFFFFF' align='center'><img src='".URL."/imagens/bolinhas/ball_vermelha.gif' border='0' title='Lote Vencido'></td>";
     }
     else if (($pegar[validade] >= date("Y-m-d")) && ($pegar[flg_bloqueado]== 'S'))
     {
       echo "<td bgcolor='#FFFFFF' align='center'><img src='".URL."/imagens/bolinhas/ball_vermelha.gif' border='0' title='Lote Bloqueado'></td>";
     }
    echo "</tr>";
   }
   echo "</table>";
  }
?>
