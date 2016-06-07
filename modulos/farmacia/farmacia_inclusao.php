<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: farmacia_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao no estoque da farmacia
  //////////////////////////////////////////////////////////////////

  //CRIANDO NUMERO DE CONTROLE PARA EVITAR DUPLICIDADE NA GRAVAÇÃO
  session_regenerate_id();
  $idSessao = session_id();
  $numControle = date("Y-m-d H:i:s").$id_unidade_sistema.$idSessao;
  
  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";


    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if($_POST[flag]=="t" || $_POST[flag2]=="t"){
      $sql="select f.descricao
            from parametro as p,
                 fabricante as f
            where p.fabricante_id_fabricante=f.id_fabricante";
      $res=mysqli_query($db, $sql);
      erro_sql("Select parametro - fabricante", $db, "");
      if(mysqli_num_rows($res)>0){
        $fabr_padrao=mysqli_fetch_object($res);
        $descr_fabr_padrao=$fabr_padrao->descricao;
      }
    }
    
    if($_POST[flag]=="t"){
      if($_POST[id_login]==""){
        $_POST[id_login]=$_SESSION[id_usuario_sistema];
      }
      $lista_sig2m=$_SESSION["LISTA_SIG2M"];
      $data=date("Y-m-d H:i:s");

      //insercao de um registro por documento na tabela movto_geral
      $sql="insert into movto_geral ";
      $sql.="(tipo_movto_id_tipo_movto, usuario_id_usuario, unidade_id_unidade, num_documento, data_movto, data_incl, num_controle) ";
      $sql.="values ('1', '$_POST[id_login]', '$_SESSION[id_unidade_sistema]', '$_POST[numero]', '$data', '$data', '$numControle')";
      mysqli_query($db, $sql);
      erro_sql("Insert Movto Geral", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      $sql="select id_movto_geral from movto_geral ";
      $sql.="where tipo_movto_id_tipo_movto='1' and usuario_id_usuario='$_POST[id_login]' and ";
      $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and data_movto='$data' and data_incl='$data'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Id Movto Geral", $db, "");
      if(mysqli_num_rows($res)>0){
        $chave=mysqli_fetch_object($res);
      }

      //atualizando a base do dim
      $index=0;
      $info="";
      foreach($lista_sig2m as $linha){
        foreach($linha as $coluna){
          if($index<=(QTDE_COLUNA+3)){
            $info.=$coluna . "|";
          }
          if($index==(QTDE_COLUNA+3)){
            $valores=split("[|]", $info);
            //verificando se existe material cadastrado
            $sql="select id_material from material where codigo_material='$valores[0]' and status_2='A'";
            $result=mysqli_query($db, $sql);
            erro_sql("Select Material Cadastrado", $db, "");
            if(mysqli_num_rows($result)<=0){
              //verificando se existe grupo
              $sql="select id_grupo from grupo where descricao='$valores[4]' and status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Grupo Existe", $db, "");
              if(mysqli_num_rows($result)<=0){
                //insercao na tabela grupo
                $sql="insert into grupo (descricao, flg_tipo_obrigatorio, status_2, data_incl, usua_incl) ";
                $sql.="values ('" . strtoupper($valores[4]) . "', 'N', 'A', '$data', '$_POST[id_login]')";
                mysqli_query($db, $sql);
                erro_sql("Insert Grupo", $db, "");
                if(mysqli_errno($db)!="0"){
                  $atualizacao="erro";
                }
              }
              //obtem id do grupo inserido
              $sql="select id_grupo from grupo where descricao='$valores[4]' and status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Id Grupo", $db, "");
              if(mysqli_num_rows($result)>0){
                $grupo_info=mysqli_fetch_object($result);
                $cod_grupo=$grupo_info->id_grupo;
              }
              //verificando se existe subgrupo associado ao grupo
              $sql="select id_subgrupo from subgrupo where grupo_id_grupo='$cod_grupo' and descricao='$valores[5]' and status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Subgrupo Existe", $db, "");
              if(mysqli_num_rows($result)<=0){
                $sql="insert into subgrupo (grupo_id_grupo, descricao, status_2, data_incl, usua_incl) ";
                $sql.="values ('$cod_grupo', '" . strtoupper($valores[5]) . "', 'A', '$data', '$_POST[id_login]')";
                mysqli_query($db, $sql);
                erro_sql("Insert Subgrupo", $db, "");
                if(mysqli_errno($db)!="0"){
                  $atualizacao="erro";
                }
              }
              //obtem id do subgrupo associado ao grupo existente
              $sql="select id_subgrupo from subgrupo where grupo_id_grupo='$cod_grupo' and status_2='A' and descricao='$valores[5]'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Id Subgrupo", $db, "");
              if(mysqli_num_rows($result)>0){
                $sbgrupo_info=mysqli_fetch_object($result);
                $cod_sbgrupo=$sbgrupo_info->id_subgrupo;
              }
              //verificando se existe familia associada ao subgrupo
              $sql="select id_familia from familia where subgrupo_id_subgrupo='$cod_sbgrupo' and descricao='$valores[6]' and status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Família Existe", $db, "");
              if(mysqli_num_rows($result)<=0){
                $sql="insert into familia (subgrupo_id_subgrupo, descricao, status_2, data_incl, usua_incl) ";
                $sql.="values ('$cod_sbgrupo', '" . strtoupper($valores[6]) . "', 'A', '$data', '$_POST[id_login]')";
                mysqli_query($db, $sql);
                erro_sql("Insert Família", $db, "");
                if(mysqli_errno($db)!="0"){
                  $atualizacao="erro";
                }
              }
              //obtem id da familia associada ao subgrupo
              $sql="select id_familia from familia where subgrupo_id_subgrupo='$cod_sbgrupo' and descricao='$valores[6]' and status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Id Família", $db, "");
              if(mysqli_num_rows($result)>0){
                $familia_info=mysqli_fetch_object($result);
                $cod_familia=$familia_info->id_familia;
              }
              $sql="select id_unidade_material from unidade_material where unidade='$valores[7]'";
              $result=mysqli_query($db, $sql);
              erro_sql("Select Unidade Material", $db, "");
              if(mysqli_num_rows($result)>0){
                $unidade_info=mysqli_fetch_object($result);
                $cod_unidade=$unidade_info->id_unidade_material;
              }
              $sql="insert into material (unidade_material_id_unidade_material, grupo_id_grupo, subgrupo_id_subgrupo, tipo_material_id_tipo_material, familia_id_familia, lista_especial_id_lista_especial, codigo_material, descricao, flg_dispensavel, status_2, data_incl, usua_incl, flg_autorizacao_disp, dias_limite_disp) ";
              $sql.="values ('$cod_unidade', '$cod_grupo', '$cod_sbgrupo', Null, '$cod_familia', Null, '$valores[0]', '" . strtoupper($valores[9]) . "', 'S', 'A', '$data', '$_POST[id_login]', 'N', '')";
              mysqli_query($db, $sql);
              erro_sql("Insert Material", $db, "");
              if(mysqli_errno($db)!="0"){
                $atualizacao="erro";
              }
            }
            //obtem id do material associado ao grupo, subgrupo e familia
            $sql="select id_material from material where codigo_material='$valores[0]' and status_2='A'";
            $result=mysqli_query($db, $sql);
            erro_sql("Select Id Material", $db, "");
            if(mysqli_num_rows($result)>0){
              $material_info=mysqli_fetch_object($result);
              $id_mat=$material_info->id_material;
            }

            //fabricante nao informado no sig2m
            if(trim($valores[2])==""){
              $sql="select id_fabricante
                    from fabricante
                    where descricao='$descr_fabr_padrao' and
                          status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("select fabricante $descr_fabr_padrao", $db, "");
              if(mysqli_num_rows($result)>0){
                //fabricante n/a existe, obtem id do fabricante n/a
                $fabricante_info=mysqli_fetch_object($result);
                $cod_fabr=$fabricante_info->id_fabricante;
              }
            }
            //fabricante informado no sig2m
            else{
              //verificando se fabricante existe
              $sql="select id_fabricante
                    from fabricante
                    where descricao='" . trim($valores[2]). "' and
                          status_2='A'";
              $result=mysqli_query($db, $sql);
              erro_sql("select descricao fabricante", $db, "");
              if(mysqli_num_rows($result)>0){
                //fabricante existe, obtem id do fabricante
                $fabricante_info=mysqli_fetch_object($result);
                $cod_fabr=$fabricante_info->id_fabricante;
              }
              else{
                //fabricante nao existe, insere
                $sql="insert into fabricante
                      (descricao, status_2, data_incl, usua_incl)
                      values ('" . strtoupper(trim($valores[2])) . "', 'A', '$data',
                              '$_POST[id_login]')";
                $result=mysqli_query($db, $sql);
                erro_sql("insert descricao fabricante", $db, "");
                if(mysqli_errno($db)!="0"){
                  $atualizacao="erro";
                }
                else{
                  //obtem id do fabricante inserido
                  $sql="select id_fabricante
                        from fabricante
                        where descricao='" . trim($valores[2]). "' and
                              status_2='A'";
                  $result=mysqli_query($db, $sql);
                  erro_sql("select id fabricante", $db, "");
                  if(mysqli_num_rows($result)>0){
                    $fabricante_info=mysqli_fetch_object($result);
                    $cod_fabr=$fabricante_info->id_fabricante;
                  }
                }
              }
            }
            $cod_lote=$valores[1];

            //insercao de varios registros (varios medicamentos) na tabela itens_movto_geral
            //Verifica se eh insercao ou atualizacao
            $sql="select qtde from itens_movto_geral ";
            $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and material_id_material='$id_mat' and fabricante_id_fabricante='$cod_fabr' and lote='$cod_lote'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Itens Movto Geral", $db, "");
            if(mysqli_num_rows($res)>0){
              $qtde_consulta=mysqli_fetch_object($res);
              $qtde=(int)$valores[3]+(int)$qtde_consulta->qtde;
              $sql="update itens_movto_geral set qtde='$qtde' ";
              $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and material_id_material='$id_mat' and fabricante_id_fabricante='$cod_fabr' and lote='$cod_lote'";
            }
            else{
              $sql="insert into itens_movto_geral ";
              $sql.="(movto_geral_id_movto_geral, material_id_material, fabricante_id_fabricante, lote, validade, qtde) ";
              $sql.="values ('$chave->id_movto_geral', '$id_mat', '$cod_fabr', '" . strtoupper($cod_lote) . "', '$valores[8]', '$valores[3]')";
            }
            mysqli_query($db, $sql);
            erro_sql("Insert Itens Movto Geral", $db, "");
            if(mysqli_errno($db)!="0"){
              $atualizacao="erro";
            }
            //obtem a quantidade de material de uma unidade no estoque
            $sql="select quantidade from estoque ";
            $sql.="where fabricante_id_fabricante='$cod_fabr' and material_id_material='$id_mat' ";
            $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and lote='$cod_lote'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Qtde Material Unidade", $db, "");
            if(mysqli_num_rows($res)>0){
              $estoque_info=mysqli_fetch_object($res);
              $qtde_estoque_unidade=(int)$estoque_info->quantidade;
            }
            else{
              $qtde_estoque_unidade=0;
            }
            //obtem o saldo anterior de um material no estoque
            $sql="select quantidade from estoque where material_id_material='$id_mat' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Saldo Anterior Material", $db, "");
            $saldo_anterior=0;
            if(mysqli_num_rows($res)>0){
              while($qtde_estoque_material=mysqli_fetch_object($res)){
                $saldo_anterior+=(int)$qtde_estoque_material->quantidade;
              }
            }
            //verifica se eh uma insercao ou uma atualizacao no estoque
            $sql="select id_estoque from estoque ";
            $sql.="where fabricante_id_fabricante='$cod_fabr' and material_id_material='$id_mat' ";
            $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and lote='$cod_lote'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Estoque", $db, "");
            if(mysqli_num_rows($res)>0){
              $qtde=(int)$valores[3]+$qtde_estoque_unidade;
              $sql="update estoque ";
              $sql.="set quantidade='$qtde', data_alt='$data', usua_alt='$_POST[id_login]' ";
              $sql.="where fabricante_id_fabricante='$cod_fabr' and material_id_material='$id_mat' ";
              $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and lote='$cod_lote'";
            }
            else{
              //verificando se existe material/lote/fabricante bloqueado para alguma unidade
              $sql="select id_estoque from estoque where fabricante_id_fabricante='$cod_fabr' ";
              $sql.="and material_id_material='$id_mat' and lote='$cod_lote' and ";
              $sql.="flg_bloqueado='S'";
              $res=mysqli_query($db, $sql);
              erro_sql("Select Material/Lote/Fabricante Bloqueado", $db, "");
              //existe material/fabricante/lote bloqueado para alguma unidade
              if(mysqli_num_rows($res)>0){
                $sql="insert into estoque ";
                $sql.="(fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote, validade, quantidade, data_incl, usua_incl, flg_bloqueado) ";
                $sql.="values ('$cod_fabr', '$id_mat', '$_SESSION[id_unidade_sistema]' ,'" . strtoupper($cod_lote) . "', '$valores[8]', '$valores[3]', '$data', '$_POST[id_login]', 'S')";
              }
              //nao existe material/fabricante/lote bloqueado para alguma unidade
              else{
                $sql="insert into estoque ";
                $sql.="(fabricante_id_fabricante, material_id_material, unidade_id_unidade, lote, validade, quantidade, data_incl, usua_incl, flg_bloqueado) ";
                $sql.="values ('$cod_fabr', '$id_mat', '$_SESSION[id_unidade_sistema]' ,'" . strtoupper($cod_lote) . "', '$valores[8]', '$valores[3]', '$data', '$_POST[id_login]', '')";
              }
            }
            mysqli_query($db, $sql);
            erro_sql("Update/Insert Estoque", $db, "");
            if(mysqli_errno($db)!="0"){
              $atualizacao="erro";
            }
            //obtem o saldo atual de um material no estoque
            $sql="select quantidade from estoque where material_id_material='$id_mat' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Saldo Atual Material", $db, "");
            if(mysqli_num_rows($res)>0){
              $saldo_atual=0;
              while($qtde_estoque_material=mysqli_fetch_object($res)){
                $saldo_atual+=(int)$qtde_estoque_material->quantidade;
              }
            }

            //verificando se eh uma atualizacao ou insercao
            $sql="select qtde_entrada from movto_livro where movto_geral_id_movto_geral='$chave->id_movto_geral' ";
            $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$id_mat'";
            $res=mysqli_query($db, $sql);
            erro_sql("Select Movto Livro", $db, "");
            if(mysqli_num_rows($res)>0){
              //atualizando o movimento do livro
              $livro_info=mysqli_fetch_object($res);
              $qtde=(int)$livro_info->qtde_entrada+(int)$valores[3];
              $sql="update movto_livro set qtde_entrada='$qtde', saldo_atual='$saldo_atual'";
              $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and ";
              $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='$id_mat'";
            }
            else{
              //insercao movimento do livro
              $sql="select descricao from tipo_movto where id_tipo_movto='1'";
              $res=mysqli_query($db, $sql);
              erro_sql("Select Tipo Movto", $db, "");
              if(mysqli_num_rows($res)>0){
                $mov_info=mysqli_fetch_object($res);
              }
              $history=$mov_info->descricao . " Nº do BEC: " . $_POST[bec];
              $sql="insert into movto_livro ";
              $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material, tipo_movto_id_tipo_movto, saldo_anterior, qtde_entrada, saldo_atual, data_movto, historico) ";
              $sql.="values ('$chave->id_movto_geral', '$_SESSION[id_unidade_sistema]', '$id_mat', '1', '$saldo_anterior', '$valores[3]', '$saldo_atual', '$data', '" . strtoupper($history) . "')";
            }
            mysqli_query($db, $sql);
            erro_sql("Update/Insert Movto Livro", $db, "");
            if(mysqli_errno($db)!="0"){
              $atualizacao="erro";
            }
            $info="";
            $index=0;
          }
          else{
            $index++;
          }
        }
      }

      if($atualizacao==""){
        mysqli_commit($db);
        $sql="select integracao, setor_farmacia, cod_operacao from parametro";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Parâmetro - DIM", $db, "");
        if(mysqli_num_rows($res)>0){
        $consulta=mysqli_fetch_object($res);
        if (strtoupper($consulta->integracao)=='S')
        {
            require '../../siga/farmacia.php';
        }
      }
                        
        $data_certa=substr($data, 8, 2) . "/" . substr($data, 5, 2) . "/" . substr($data, 0, 4) . " " . substr($data, 11, 8);
        echo "<script> resposta=window.confirm('Operação efetuada com sucesso! Deseja imprimir?');
                if(resposta){
                  window.open('" . URL . "/modulos/impressao/impressao_farmacia.php?chave=$chave->id_movto_geral&numero=$_POST[numero]&data=$data_certa&aplicacao=$_SESSION[APLICACAO]&bec=$_POST[bec]');
                }
              </script>";
      }
      else{
        mysqli_rollback($db);
        echo "<script>
                window.alert('Não foi possível atualizar estoque da farmácia!');
              </script>";
      }
      
      
      $_POST[numero]="";
      $_POST[data]="";
      $_POST[bec]="";
      echo "<script>window.location='" . URL . "/modulos/farmacia/farmacia_inclusao.php';</script>";
      exit();
    }
    else{
      if($_POST[flag2]=="t"){
        if(file_exists("../../config/config_sig2m.inc.php")){
          require "../../config/config_sig2m.inc.php";
        }

        //acessando base do dim
        $sql="select integracao, setor_farmacia, cod_operacao from parametro";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Parâmetro - DIM", $db, "");
        if(mysqli_num_rows($res)>0){
          $consulta=mysqli_fetch_object($res);
        }
        //acessando base do sig2m
        $pos1=strpos($_POST[data], "/");
        $pos2=strrpos($_POST[data], "/");
        $data_consulta=substr($_POST[data], $pos2+1, strlen($_POST[data])) . "/" . substr($_POST[data], $pos1+1, 2) . "/" . substr($_POST[data], 0, 2);

        //verificando se o campo Nº BEC eh obrigatorio
        $sql="select cod_material from $base_local.expedicao ";
        $sql.="where lista='$_POST[numero]' and data='$data_consulta' and ";
        $sql.="cod_operacao='$consulta->cod_operacao' and sigla='$consulta->setor_farmacia'";
        $res=mysql_query($sql, $dbSIG2M);
        erro_sql("Select Nro BEC", "", $db);
        while($base_sig2m=mysql_fetch_object($res)){
          $sql="select m.codigo_material from material as m, lista_especial as le, livro as l ";
          $sql.="where m.lista_especial_id_lista_especial=le.id_lista_especial and ";
          $sql.="le.livro_id_livro=l.id_livro and m.codigo_material='$base_sig2m->cod_material'";
          //echo $sql . '<br>';
          $result=mysqli_query($db, $sql);
          erro_sql("Select Material - SIG2M", $db, "");
          if(mysqli_num_rows($result)>0){
            $obrigatorio_info="obrigatorio";
          }
        }
        if($obrigatorio_info!="" && $_POST[bec]==""){
          $_POST[flag2]="f";
          $_GET[b]="f";
        }
        else{
          $sql="select e.contador, e.cod_material, m.nome, e.cod_lote, e.qtde, g.nome as gnome, sbg.nome as sbgnome, f.nome as fnome, m.unidade, e.validade, e.fabricante ";
          $sql.="from $base_local.expedicao as e, $base_local.material as m, $base_local.grupo as g, $base_local.subgrupo as sbg, $base_local.familia as f ";
          $sql.="where g.cod_grupo=m.cod_grupo and sbg.cod_subgrupo=m.cod_subgrupo and f.cod_familia=m.cod_familia and e.cod_material=m.cod_material and lista='$_POST[numero]' and data='$data_consulta' and cod_operacao='$consulta->cod_operacao' and sigla='$consulta->setor_farmacia'";
          //echo $sql . '<br>';
          $result=mysql_query($sql, $dbSIG2M);
          erro_sql("Select Expedição - SIG2M", "", $db);
          if(mysql_num_rows($result)<=0){
            $_POST[flag2]="f";
            $_GET[v]="f";
          }
        }
      }
    }

    if($_GET[aplicacao]<>''){
      $_SESSION[cod_aplicacao]=$_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

    require "../../verifica_acesso.php";

?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var doc=x.numero;
        var date=x.data;

        if(doc.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          doc.focus();
          doc.select();
          return false;
        }
        if(date.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          date.focus();
          date.select();
          return false;
        }
        return true;
      }
      
      function obterDados(){
        if(validarCampos()==true){
          verificarDocumento();
        }
      }
      
      function verificarDocumento(){
        var x=document.form_inclusao;
        var numero=x.numero.value;
        var url = "../../xml/farmaciaDocumento.php?numero=" + numero + "&unidade=" + <?php echo $_SESSION[id_unidade_sistema];?>;
        requisicaoHTTP("GET", url, true);
      }

      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string
        info_res=info.substr(0, 3);
        if(info_res=="SAV"){
          x.flag2.value="t";
          x.submit();
        }
        if(info_res=="NAO"){
          var msg="Número de documento existente para essa unidade!";
          window.alert(msg);
        }
        if(info_res=="FAB"){
          var msg="Não existe fabricante configurado no DIM!";
          window.alert(msg);
        }
        var login_senha=info.split("@");
        if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
          window.alert("Login e/ou Senha Inválidos!");
          x.login.focus();
          return;
        }
        if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
          x.id_login.value=login_senha[1];
          salvarDados();
          return;
        }
      }
      
      function salvarDados(){
        var x=document.form_inclusao;
        x.salvar.disabled="true";
        x.flag.value="t";
        x.submit();
      }
      
      function mostraBEC(){
        var x=document.form_inclusao;
        x.bec.focus();
        x.salvar.disabled='true';
        window.alert('Favor Preencher campo Nº BEC!');
      }
      
      function mostraDocumento(){
        var x=document.form_inclusao;
        x.numero.focus();
        x.numero.select();
        x.salvar.disabled='true';
        window.alert('Não foi possível localizar o documento!')
      }
      
      function desabilitarBotao(){
        var x=document.form_inclusao;
        document.getElementById("tabela").style.display="none";
        x.salvar.disabled="true";
      }

      function habilitaBotaoSalvar(){
        var x=document.form_inclusao;
        if(Trim(x.login.value)=="" || Trim(x.senha.value)=="" || document.getElementById('tabela_aux').rows.length==1){
          x.salvar.disabled=true;
        }
        else{
          x.salvar.disabled=false;
        }
      }

      function desabilitaBotaoSalvar(){
        var x=document.form_inclusao;
        x.salvar.disabled=true;
      }

      function Trim(str){
        return str.replace(/^\s+|\s+$/g,"");
      }

      function salvarMovimento(){
        var x=document.form_inclusao;
        if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
          verificaLoginSenhaResponsavelDispensacao();
        }
        else{
          salvarDados();
        }
      }

      function verificaLoginSenhaResponsavelDispensacao(){
        var x=document.form_inclusao;
        var url = "../../xml_dispensacao/verificar_login_senha_responsavel_dispensacao.php?login="+x.login.value+"&senha="+x.senha.value;
        requisicaoHTTP("GET", url, true, '');
      }
      //-->
    </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td> <?php echo $caminho;?> </td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%'height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_inclusao" action="./farmacia_inclusao.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr>
                      <td colspan="5">
                        <table border="0" cellpadding="0" cellspacing="1" width="100%">
                          <tr class="titulo_tabela">
                            <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?> </td>
                          </tr>
                          <tr>
                            <td class="descricao_campo_tabela" valign="middle" width="20%">
                              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                              Num documento
                            </td>
                            <td class="campo_tabela" valign="middle" width="30%">
                              <input type="text" name="numero" size="30" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php if(isset($_POST[numero])){echo $_POST[numero];}?>" onchange="desabilitarBotao();">
                            </td>
                            <td class="descricao_campo_tabela" valign="middle" width="15%">
                              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                              Data
                            </td>
                            <td class="campo_tabela" valign="middle" width="100%">
                              <input type="text" name="data" size="30" style="width: 200px" onKeyPress="return mascara_data(event,this);" value="<?php if(isset($_POST[data])){echo $_POST[data];}?>" onblur="verificaData(this,this.value);" onchange="desabilitarBotao();">
                            </td>
                          </tr>
                          <tr>
                            <td class="descricao_campo_tabela" valign="middle" width="20%">
                              <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                              Nº BEC
                            </td>
                            <td class="campo_tabela" valign="middle" width="30%">
                              <input type="text" name="bec" size="30" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php if(isset($_POST[bec])){echo $_POST[bec];}?>" onchange="desabilitarBotao();">
                            </td>
                            <td class="campo_tabela" colspan="2" valign="middle" align="center" width="100%">
                            <?php
                              if($inclusao_perfil!=""){
                            ?>
                              <input type="button" style="font-size: 12px;" name="cadastrar" value="OK" onclick="obterDados();">
                            <?php
                              }
                              else{
                            ?>
                              <input type="button" style="font-size: 12px;" name="cadastrar" value="OK" disabled>
                            <?php
                              }
                            ?>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="5">
                        <table cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width='10%' align='center'> Linha </td>
                            <td width='10%' align='center'> Código </td>
                            <td width='38%' align='center'> Material </td>
                            <td width='16%' align='center'> Lote </td>
                            <td width='16%' align='center'> Fabricante </td>
                            <td width='20%' align='center'> Quantidade </td>
                          </tr>
                        </table>
                        <div id="tabela" style="display:'';">
                        <table id='tabela_aux' cellpadding='0' cellspacing='1' border='0' width='100%'>
<?php
                            $cor_linha = "#CCCCCC";
                            ///////////////////////////////////////
                            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
                            ///////////////////////////////////////

                            if($_POST[flag2]=="t"){
                              $_GET[msg]="";
                              while($documento_info=mysql_fetch_object($result)){
?>
                                <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED';" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'">
                                  <td width='10%' align='left'>
                                    <?php echo $documento_info->contador;?>
                                  </td>
                                  <td width='10%' align='left'>
                                    <?php echo $documento_info->cod_material;?>
                                  </td>
                                  <td <td width='38%' align='left'>
                                    <?php echo $documento_info->nome;?>
                                  </td>
                                  <td <td width='16%' align='left'>
                                    <?php echo $documento_info->cod_lote;?>
                                  </td>
                                  <td <td width='16%' align='left'>
                                  <?php
                                   if (trim($documento_info->fabricante) == '')
                                   {
                                    echo $descr_fabr_padrao;
                                   }
                                   else
                                   {
                                    echo $documento_info->fabricante;
                                   }
                                   ?>
                                  </td>
                                  <td <td width='20%' align='right'>
                                    <?php echo intval($documento_info->qtde);?>
                                  </td>
                                </tr>
<?php
                                $lista_sig2m[][]=$documento_info->cod_material;
                                $lista_sig2m[][]=$documento_info->cod_lote;
                                $lista_sig2m[][]=$documento_info->fabricante;
                                $lista_sig2m[][]=$documento_info->qtde;
                                $lista_sig2m[][]=$documento_info->gnome;
                                $lista_sig2m[][]=$documento_info->sbgnome;
                                $lista_sig2m[][]=$documento_info->fnome;
                                $lista_sig2m[][]=$documento_info->unidade;
                                $lista_sig2m[][]=$documento_info->validade;
                                $lista_sig2m[][]=$documento_info->nome;
                                ////////////////////////
                                //MUDANDO COR DA LINHA//
                                ////////////////////////
                                if($cor_linha=="#EEEEEE"){
                                  $cor_linha="#CCCCCC";
                                }
                                else{
                                  $cor_linha="#EEEEEE";
                                }
                              }
                              $_SESSION["LISTA_SIG2M"]=$lista_sig2m;
                            }
?>
                          <tr>
                            <td colspan="6" height="100%"></td>
                          </tr>
                        </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4">
                        <?php
                          if($mostrar_responsavel_dispensacao!="S"){
                            $mostrar_login_senha="none";
                          }
                          else{
                            $mostrar_login_senha="''";
                          }
                        ?>
                        <div id="mostrar_responsavel_dispensacao" style="display:<?php echo $mostrar_login_senha;?>">
                          <table>
                            <tr>
                              <td class="descricao_campo_tabela" width="30%">
                                Realizado por:
                              </td>
                              <td class="descricao_campo_tabela" width="10%">
                                Login:
                              </td>
                              <td>
                                <input type="text" name="login" onblur="habilitaBotaoSalvar();" onfocus="desabilitaBotaoSalvar();">
                                <input type="hidden" name="id_login" value="">
                              </td>
                              <td class="descricao_campo_tabela" width="10%">
                                Senha:
                              </td>
                              <td>
                                <input type="password" name="senha" onblur="habilitaBotaoSalvar(); document.form_inclusao.salvar.focus();" onfocus="desabilitaBotaoSalvar();">
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                      <td valign="middle" align="right" width="100%">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarMovimento();" disabled>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="5" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <?

                    ?>
                    <input type="hidden" name="flg_integracao" value="<?echo $consulta->integracao?>">
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="flag2" value="f">
                  </form>
                </table>
              </td>
            </tr>
          </table name='3'>
        </td>
      </tr>
    </table>
<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
?>
    <script language="javascript">
    <!--
      var x=document.form_inclusao;
      if(x.numero.value==""){
        x.numero.focus();
      }
      if("<?php echo $mostrar_responsavel_dispensacao;?>"==""){
        if(document.getElementById('tabela_aux').rows.length>1){
          x.salvar.disabled=false;
        }
      }
    //-->
    </script>
<?php

    if($_GET[b]=="f"){echo "<script>mostraBEC();</script>";}

    if($_GET[v]=='f'){echo "<script>mostraDocumento();</script>";}
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
