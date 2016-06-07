<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: consolidarInformacaoReceitasAtendidas.php
  //  Bancos...: dbmdim
  //  Data.....: 26/05/2009
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Crontab
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
/*  $ARQ_CONF="/home/hitoshi/public_html/dim4/branches/sm0004/codificacao/fontes/config/config.inc.php";*/
  $ARQ_CONF="/srv/www/saude-dim.ima.sp.gov.br/html/config/config.inc.php";
  $data_erro="2007-01-31";

  function excecao($db, $acao){
    if(mysqli_error($db)){
      throw new Exception($acao . ": ERRO " . mysqli_errno($db));
    }
  }
  
  function consolidacao($data_erro, $db, $operacao){
    //obtem os movimentos do dia, mes, ano especifico
    $sql="select tab.tipo_movto_id_tipo_movto as tipo_movto,
                 tab.unidade,
                 tab.unidade_pai,
                 date_format(tab.data_movto, '%d') as dia,
                 date_format(tab.data_movto, '%m') as mes,
                 date_format(tab.data_movto, '%Y') as ano,
                 count(tab.tipo_movto_id_tipo_movto) as total_mov
          from
          (
           select distinct mvg.tipo_movto_id_tipo_movto,
                           mvg.unidade_id_unidade as unidade,
                           uni.unidade_id_unidade as unidade_pai,
                           mvg.data_movto
           from movto_geral as mvg,
                itens_movto_geral as imvg,
                unidade as uni
           where mvg.id_movto_geral=imvg.movto_geral_id_movto_geral and
                 uni.id_unidade=mvg.unidade_id_unidade and
                 substring(mvg.data_movto, 1, 10)='$data_erro'
          ) as tab
          group by tab.tipo_movto_id_tipo_movto, tab.unidade
          union
          select '14' as tipo_movto,
                 tab.unidade,
                 tab.unidade_pai,
                 date_format(tab.data_incl, '%d') as dia,
                 date_format(tab.data_incl, '%m') as mes,
                 date_format(tab.data_incl, '%Y') as ano,
                 count(tab.id_receita) as total_mov
          from
          (
           select distinct rec.unidade_id_unidade as unidade,
                           uni.unidade_id_unidade as unidade_pai,
                           rec.data_incl,
                           rec.id_receita
           from receita as rec,
                itens_receita as irec,
                unidade as uni
           where rec.id_receita=irec.receita_id_receita and
                 uni.id_unidade=rec.unidade_id_unidade and
                 substring(rec.data_incl, 1, 10)='$data_erro' and
                 rec.id_receita and not exists (
                                                select 'true'
                                                from movto_geral as mvg,
                                                     itens_movto_geral as imvg
                                                where mvg.id_movto_geral=imvg.movto_geral_id_movto_geral and
                                                      substring(mvg.data_movto, 1, 10)='$data_erro' and
                                                      mvg.receita_id_receita=rec.id_receita
                                                )
          ) as tab
          group by tab.unidade";
    $result_movimento=mysqli_query($db, $sql);
    excecao($db, "SELECT - MOVIMENTO");
    //Caso exista movimento
    while($movimento=mysqli_fetch_object($result_movimento)){
      //insere os movimentos do dia, mes, ano especifico
      $sql="insert into
            movto_consolidado (id_tipo_movimento,
                               id_unidade,
                               unidade_id_unidade,
                               dia,
                               mes,
                               ano,
                               total_mov)
            values ($movimento->tipo_movto,
                    $movimento->unidade,
                    $movimento->unidade_pai,
                    $movimento->dia,
                    $movimento->mes,
                    $movimento->ano,
                    $movimento->total_mov)";
      mysqli_query($db, $sql);
      excecao($db, "INSERT - MOVIMENTO");
      mysqli_commit($db);
    }
    if($operacao=="UPDATE"){
      //atualiza o status_2 para ATUALIZADO
      $sql="update log_movto_consolidado
            set status_2='ATUALIZADO',
                observacao=null
            where data_log='$data_erro'";
    }
    else{
      $sql="insert into
            log_movto_consolidado (data_log,
                                   status_2)
            values ('$data_erro',
                    'ATUALIZADO')";
    }
    mysqli_query($db, $sql);
    excecao($db, "$operacao - ATUALIZADO");
    mysqli_commit($db);
  }

  if(!file_exists($ARQ_CONF)){
    exit("Não existe arquivo de configuração!");
  }
  require $ARQ_CONF;
  
  try{
    //verifica se a tabela esta vazia
    $sql="select count(*) as linha
          from log_movto_consolidado";
    $result=mysqli_query($db, $sql);
    excecao($db, "SELECT - TABELA VAZIA");
    $vazio=mysqli_fetch_object($result);
    //tabela vazia
    if($vazio->linha==0){
      consolidacao($data_erro, $db, "INSERT");
    }
    //tabela nao vazia
    else{
      //verifica se existe algum status_2=NAO ATUALIZADO
      $sql="select data_log,
                   date_format(data_log, '%d') as dia,
                   date_format(data_log, '%m') as mes,
                   date_format(data_log, '%Y') as ano
            from log_movto_consolidado
            where status_2='NAO ATUALIZADO' and
                  data_log<'" . date("Y-m-d") . "'";
      $result=mysqli_query($db, $sql);
      excecao($db, "SELECT - NAO ATUALIZADO");
      //Caso existe status_2=NAO ATUALIZADO
      while($data=mysqli_fetch_object($result)){
        $data_erro=$data->data_log;
        //apaga os registros do dia, mes, ano especifico
        $sql="delete
              from movto_consolidado
              where dia='$data->dia' and
                    mes='$data->mes' and
                    ano='$data->ano'";
        mysqli_query($db, $sql);
        excecao($db, "DELETE");
        mysqli_commit($db);
        consolidacao($data_erro, $db, "UPDATE");
      }

      //obtem a ultima data
      $sql="select max(date_add(data_log, interval 1 day)) as data_log
            from log_movto_consolidado as lmc
            where date_add(data_log, interval 1 day)<'" . date("Y-m-d") . "' and
                  not exists (select 'true'
                              from log_movto_consolidado
                              where data_log=date_add(lmc.data_log, interval 1 day)) ";
      $result=mysqli_query($db, $sql);
      excecao($db, "SELECT - ULTIMA DATA");
      $data=mysqli_fetch_object($result);
      $data_erro=$data->data_log;
      //ja existe movimento consolidado
      if($data_erro!=""){
        consolidacao($data_erro, $db, "INSERT");
      }
    }
  }
  catch(exception $e){
    //verifica se existe dia, mes, ano especifico
    $sql="select data_log
          from log_movto_consolidado
          where data_log='$data_erro'";
    $result=mysqli_query($db, $sql);
    //Caso nao exista dia, mes, ano especifico
    if(mysqli_num_rows($result)==0){
      //insere log de erro
      $sql="insert into
            log_movto_consolidado (data_log,
                                   observacao,
                                   status_2)
            values ('$data_erro',
                    '" . $e->getMessage() . "',
                    'NAO ATUALIZADO')";
    }
    else{
      //atualiza o status_2 para ATUALIZADO
      $sql="update log_movto_consolidado
            set observacao='" . $e->getMessage() . "'
            where data_log='$data_erro'";
    }
    mysqli_query($db, $sql);
    mysqli_commit($db);
  }
?>
