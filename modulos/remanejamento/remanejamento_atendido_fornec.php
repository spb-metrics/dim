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
  //  Arquivo..: remanejamento_atendido_fornec.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de atendimento do módulo de remanejamento - fornecimento
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

    $mostrar_responsavel_dispensacao=$_GET[responsavel];

    if($_POST[flag]=="t"){
      if($_POST[id_login]==""){
        $_POST[id_login]=$_SESSION[id_usuario_sistema];
      }
	  
	  $lista_materiais = $_POST['lista_materiais'];
	  
      $valor=split("[|]", substr($lista_materiais, 0, (strlen($lista_materiais)-1)));
      for($i=0; $i<count($valor); $i++){
        $valores[]=split("[,]", substr($valor[$i], 0, (strlen($valor[$i])-1)));
      }

      //obtem nome da unidade solicitante
      $sql="select u.nome from solicita_remanej as sol, unidade as u ";
      $sql.="where sol.id_unid_solicitante=u.id_unidade and ";
      $sql.="sol.id_solicita_remanej='$_POST[codigo_atual]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Unidade Solicitante", $db, "");
      if(mysqli_num_rows($res)>0){
        $unidade_solicitante=mysqli_fetch_object($res);
      }
      //obtem data do sistema
      $data=date("Y-m-d H:i:s");
      //insercao de um registro por remanejamento na tabela movto_geral
      $sql="insert into movto_geral ";
      $sql.="(tipo_movto_id_tipo_movto, usuario_id_usuario, unidade_id_unidade, data_movto, data_incl, num_controle) ";
      $sql.="values ('4', '$_POST[id_login]', '$_SESSION[id_unidade_sistema]', '$data', '$data', '$numControle')";
      mysqli_query($db, $sql);
      erro_sql("Insert Movto Geral", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      $sql="select id_movto_geral from movto_geral ";
      $sql.="where tipo_movto_id_tipo_movto='4' and usuario_id_usuario='$_POST[id_login]' and ";
      $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and data_movto='$data' and data_incl='$data'";
      $res=mysqli_query($db, $sql);
      erro_sql("Select Id Movto Geral", $db, "");
      if(mysqli_num_rows($res)>0){
        $chave=mysqli_fetch_object($res);
      }
      for($i=0; $i<count($valores); $i++){
        //obtendo id na tabela item_solicita_remanej referente ao material escolhido
        $sql="select id_item_solicita_remanej from item_solicita_remanej ";
        $sql.="where id_solicita_remanej='$_POST[codigo_atual]' and ";
        $sql.="material_id_material='" . $valores[$i][0] . "'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Item Solicita Remanej", $db, "");
        if(mysqli_num_rows($res)>0){
          $id_solic=mysqli_fetch_object($res);
        }
        //insercao na tabela itens_movto_geral
        if($valores[$i][4]!="" && $valores[$i][4]!="0"){
          $sql="insert into itens_movto_geral ";
          $sql.="(movto_geral_id_movto_geral, material_id_material, fabricante_id_fabricante, lote, validade, qtde, item_solicita_remanej) ";
          $sql.="values ('$chave->id_movto_geral', '" . $valores[$i][0] . "', '" . $valores[$i][1] . "', '" . strtoupper($valores[$i][2]) . "', '" . $valores[$i][3] . "', '" . $valores[$i][4] . "', '$id_solic->id_item_solicita_remanej')";
          mysqli_query($db, $sql);
          erro_sql("Insert Itens Movo Geral", $db, "");
          if(mysqli_errno($db)!="0"){
            $atualizacao="erro";
          }
        }
        //obtem a quantidade de material de uma unidade no estoque
        $sql="select quantidade from estoque where unidade_id_unidade='$_SESSION[id_unidade_sistema]' ";
        $sql.="and material_id_material='" . $valores[$i][0] . "' and fabricante_id_fabricante='" . $valores[$i][1] . "' ";
        $sql.="and lote='" . $valores[$i][2] . "' and flg_bloqueado=''";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Qtde Material Unidade", $db, "");
        if(mysqli_num_rows($res)>0){
          $qtde_estoque=mysqli_fetch_object($res);
        }
        //obtem o saldo anterior de um material no estoque
        $sql="select quantidade from estoque where material_id_material='" . $valores[$i][0] . "' and ";
        $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Saldo Anterior Material", $db, "");
        $saldo_anterior=0;
        if(mysqli_num_rows($res)>0){
          while($qtde_estoque_material=mysqli_fetch_object($res)){
            $saldo_anterior+=(int)$qtde_estoque_material->quantidade;
          }
        }
        //atualizacao da tabela estoque
        $qtde=(int)$qtde_estoque->quantidade-(int)$valores[$i][4];
        $sql="update estoque set quantidade='$qtde' ";
        $sql.="where unidade_id_unidade='$_SESSION[id_unidade_sistema]' and ";
        $sql.="material_id_material='" . $valores[$i][0] . "' and fabricante_id_fabricante='" . $valores[$i][1] . "' ";
        $sql.="and lote='" . $valores[$i][2] . "'";
        mysqli_query($db, $sql);
        erro_sql("Update Estoque", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
        //obtem o saldo atual de um material no estoque
        $sql="select quantidade from estoque where material_id_material='" . $valores[$i][0] . "' and unidade_id_unidade='$_SESSION[id_unidade_sistema]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Saldo Atual Material", $db, "");
        if(mysqli_num_rows($res)>0){
          $saldo_atual=0;
          while($qtde_estoque_material=mysqli_fetch_object($res)){
            $saldo_atual+=(int)$qtde_estoque_material->quantidade;
          }
        }
        //verificando se eh uma atualizacao ou insercao
        $sql="select qtde_saida from movto_livro where movto_geral_id_movto_geral='$chave->id_movto_geral' ";
        $sql.="and unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='" . $valores[$i][0] . "'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Movto Livro", $db, "");
        if(mysqli_num_rows($res)>0){
          //atualizando o movimento do livro
          $livro_info=mysqli_fetch_object($res);
          $qtde=(int)$livro_info->qtde_saida+(int)$valores[$i][4];
          $sql="update movto_livro set qtde_saida='$qtde', saldo_atual='$saldo_atual'";
          $sql.="where movto_geral_id_movto_geral='$chave->id_movto_geral' and ";
          $sql.="unidade_id_unidade='$_SESSION[id_unidade_sistema]' and material_id_material='" . $valores[$i][0] . "'";
        }
        else{
          //insercao movimento do livro
          $sql="select descricao from tipo_movto where id_tipo_movto='4'";
          $res=mysqli_query($db, $sql);
          erro_sql("Select Tipo Movto", $db, "");
          if(mysqli_num_rows($res)>0){
            $mov_info=mysqli_fetch_object($res);
          }
          $history=$mov_info->descricao . " a partir da solicitação " . $_POST[codigo_atual] . " da unidade " . $unidade_solicitante->nome;
          $sql="insert into movto_livro ";
          $sql.="(movto_geral_id_movto_geral, unidade_id_unidade, material_id_material, tipo_movto_id_tipo_movto, saldo_anterior, qtde_saida, saldo_atual, data_movto, historico) ";
          $sql.="values ('$chave->id_movto_geral', '$_SESSION[id_unidade_sistema]', '" . $valores[$i][0] . "', '4', '$saldo_anterior', '" . $valores[$i][4] . "', '$saldo_atual', '$data', '" . strtoupper($history) . "')";
        }
        mysqli_query($db, $sql);
        erro_sql("Update/Insert Movto Livro", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
        //atualizando a coluna qtde_atendida na tabela item_solicita_remanej
        $sql="select qtde_atendida from item_solicita_remanej ";
        $sql.="where id_solicita_remanej='$_POST[codigo_atual]' and ";
        $sql.="material_id_material='" . $valores[$i][0] . "'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Item Solicita Remanej", $db, "");
        if(mysqli_num_rows($res)>0){
          $quantidade_atendida=mysqli_fetch_object($res);
        }
        $qtde=(int)$quantidade_atendida->qtde_atendida+(int)$valores[$i][4];
        $sql="update item_solicita_remanej set qtde_atendida='$qtde' ";
        $sql.="where id_solicita_remanej='$_POST[codigo_atual]' and ";
        $sql.="material_id_material='" . $valores[$i][0] . "'";
        mysqli_query($db, $sql);
        erro_sql("Update Item Solicita Remanej", $db, "");
        if(mysqli_errno($db)!="0"){
          $atualizacao="erro";
        }
      }
      //atualiza a coluna status_2 para reservado na tabela solicita_remanej
      $sql="update solicita_remanej set status_2='RESERVADA' ";
      $sql.="where id_solicita_remanej='$_POST[codigo_atual]'";
      mysqli_query($db, $sql);
      erro_sql("Update Solicita Remanej", $db, "");
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      if($atualizacao==""){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial_fornec.php?a=t&codigo=$_POST[codigo_atual]&unidade_solicitante=$unidade_solicitante->nome&unidade_solicitada=$_SESSION[id_unidade_sistema]&chave=$chave->id_movto_geral&aplicacao=$_SESSION[APLICACAO]");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial_fornec.php?a=f&aplicacao=$_SESSION[APLICACAO]");
      }
      exit();
    }

    if($_POST[nao]=="t"){
      //atualiza a coluna status_2 na tabela solicita_remanej para nao atendida
      $sql="update solicita_remanej set status_2='NÃO ATENDIDA' ";
      $sql.="where id_solicita_remanej='$_POST[codigo_atual]'";
      mysqli_query($db, $sql);
      erro_sql("Update Solicita Remanej - Não Atendida", $db, "");
      $atualizacao="";
      if(mysqli_errno($db)!="0"){
        $atualizacao="erro";
      }
      if($atualizacao==""){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial_fornec.php?n=f&aplicacao=$_SESSION[APLICACAO]");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/remanejamento/remanejamento_inicial_fornec.php?a=f&aplicacao=$_SESSION[APLICACAO]");
      }
      exit();
    }

    if($_GET[codigo]!=""){
      //obtem numero da solicitacao, unidade solicitante, unidade solicitada
      $sql="select sol.id_solicita_remanej, u.nome, u.id_unidade, uni.id_unidade as idunidade, sol.status_2 ";
      $sql.="from solicita_remanej as sol, unidade as u, unidade as uni ";
      $sql.="where sol.id_unid_solicitante=u.id_unidade and sol.id_unid_solicitada=uni.id_unidade ";
      $sql.="and id_solicita_remanej='$_GET[codigo]'";
      $res=mysqli_query($db, $sql);
      erro_sql("Selct Solicitação 2", $db, "");
      if(mysqli_num_rows($res)>0){
        $solicitacao=mysqli_fetch_object($res);
      }
      //obtem os materias solicitados
      $sql_itens="select m.id_material, m.codigo_material, m.descricao, it.qtde_solicita ";
      $sql_itens.="from item_solicita_remanej as it, material as m ";
      $sql_itens.="where it.material_id_material=m.id_material and id_solicita_remanej='$_GET[codigo]'";
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript" type="text/javascript" src="../../scripts/remanejamentoLote.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      function trataDados(){
        var x=document.form_atendido;
	    var info = ajax.responseText;  // obtém a resposta como string
        var login_senha=info.split("@");
        if(info.substr(0, 7)=="estoque"){
          x.flag.value='t';
          x.submit();
        }
        if(info.substr(0, 7)!="estoque" && login_senha[0]!="sim_login_senha_responsavel_dispensacao" && login_senha[0]!="nao_login_senha_responsavel_dispensacao"){
          var msg="Quantidade em estoque insuficiente\nMaterial - Lote - Fabricante\n" + info;
          window.alert(msg);
          x.salvar.disabled="";
        }
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

      function verificarEstoque(){
        var y=document.form_atendido;
        var itens=y.lista_materiais.value;
        //var url = "../../xml/mestoqueEstoque.php?unidade=" + <?php echo $_SESSION[id_unidade_sistema];?> + "&itens=" + itens;
        var url = "../../xml/remanejamentoEstoque.php?unidade=" + <?php echo $_SESSION[id_unidade_sistema];?> + "&itens=" + itens;
        requisicaoHTTP("GET", url, true);
      }

      function removerLinha(lnh, lnh_aux, pos){
        var tab=document.getElementById("tabela");
        tab.deleteRow(document.getElementById(lnh).rowIndex);
        var tab_aux=document.getElementById("tabela_aux")
        tab_aux.deleteRow(document.getElementById(lnh_aux).rowIndex);

        var total_linhas=tab.rows.length;
        for(var i=pos; i<total_linhas; i++){
          var j=tab_aux.rows[i].cells[5].innerHTML;

          tab.rows[i].deleteCell(5);
          var cel5=tab.rows[i].insertCell(5);
          cel5.align="center";
          var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
          var urlRemover="javascript:removerLinha('lnh" + j + "', 'lnh_aux" + j + "', '" + i + "')";
          cel5.innerHTML=linkRemover.link(urlRemover);
        }
        if(total_linhas<=1){
          document.getElementById("salvar").disabled="true";
        }
      }

      function naoAtender(){
        var x=document.form_atendido;
        x.atender.disabled="true";
        x.nao.value="t";
        x.submit();
      }
      
      function salvarDados(){
        var x=document.form_atendido;
        x.salvar.disabled="true";
        var itens=document.getElementById("tabela_aux");
        var total_linhas=itens.rows.length;
        var lista=new Array(total_linhas);
        for(var i=1; i<lista.length; i++){
          lista[i]=new Array(5);
        }
        var info="";
        for(var i=1; i<lista.length; i++){
          for(var j=0; j<lista[i].length; j++){
            info=info + itens.rows[i].cells[j].innerHTML + ",";
          }
          info=info + "|";
        }

        document.getElementById("lista_materiais").value=info;
        verificarEstoque();
      }

      function inserirLinhas(){
        if(validarCampos()){
          var x=document.form_atendido;

          var tb=document.getElementById("tb_mat");
          var tot_lin=tb.rows.length;
          var itens=document.getElementById("tabela");
          var total_linhas=itens.rows.length;
          var cont=total_linhas;
          for(var i=1; i<tot_lin; i++){
            var j=i-1;
            var doc=document.getElementById(j);
            if(doc.value!=""){
              var tab=itens.insertRow(cont);
              tab.id="lnh" + cont;
              tab.className="campo_tabela";

              //codigo material
              var cel0=tab.insertCell(0);
              cel0.align="left";
              cel0.innerHTML=tb.rows[i].cells[0].innerHTML;

              //fabricante
              var cel1=tab.insertCell(1);
              cel1.align="left";
              cel1.innerHTML=tb.rows[i].cells[1].innerHTML;

              //lote
              var cel2=tab.insertCell(2);
              cel2.align="left";
              cel2.innerHTML=tb.rows[i].cells[2].innerHTML;

              //validade
              var cel3=tab.insertCell(3);
              cel3.align="center";
              cel3.innerHTML=tb.rows[i].cells[3].innerHTML;

              //estoque
              var cel4=tab.insertCell(4);
              cel4.align="right";
              cel4.innerHTML=doc.value;

              var cel5=tab.insertCell(5);
              cel5.align="center";
              var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
              var urlRemover="javascript:removerLinha('lnh" + cont + "', 'lnh_aux" + cont + "', '" + cont + "')";
              cel5.innerHTML=linkRemover.link(urlRemover);
              
              cont++;
            }
          }

          var tb_aux=document.getElementById("tb_mat_aux");
          var tot_lin_aux=tb_aux.rows.length;
          var itens_aux=document.getElementById("tabela_aux");
          var total_linhas_aux=itens_aux.rows.length;
          var cont_aux=total_linhas_aux;
          for(var i=1; i<tot_lin_aux; i++){
            var j=i-1;
            var doc_aux=document.getElementById(j);
            if(doc_aux.value!=""){
              var tab_aux=itens_aux.insertRow(cont_aux);
              tab_aux.id="lnh_aux" + cont_aux;
              tab_aux.className="campo_tabela";

              //codigo material
              var cel0=tab_aux.insertCell(0);
              cel0.align="left";
              cel0.innerHTML=tb_aux.rows[i].cells[0].innerHTML;

              //fabricante
              var cel1=tab_aux.insertCell(1);
              cel1.align="left";
              cel1.innerHTML=tb_aux.rows[i].cells[1].innerHTML;

              //lote
              var cel2=tab_aux.insertCell(2);
              cel2.align="left";
              cel2.innerHTML=tb_aux.rows[i].cells[2].innerHTML;

              //validade
              var cel3=tab_aux.insertCell(3);
              cel3.align="center";
              cel3.innerHTML=tb_aux.rows[i].cells[3].innerHTML;

              //estoque
              var cel4=tab_aux.insertCell(4);
              cel4.align="right";
              cel4.innerHTML=doc_aux.value;

              var cel5=tab_aux.insertCell(5);
              cel5.align="right";
              cel5.innerHTML=cont_aux;

              cont_aux++;
            }
          }
          removerLinhas();
          x.adicionar.disabled="true";
          if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
            x.salvar.disabled=true;
          }
          else{
            x.salvar.disabled="";
          }
          x.atender.disabled="true";
        }
      }
      
      function removerLinhas(){
        var tab=document.getElementById("tb_mat");
        var total_linhas=tab.rows.length;
        for(var i=0; i<total_linhas; i++){
          var lnh="linha" + i;
          var linha=document.getElementById(lnh);
          if(linha){
            tab.deleteRow(linha.rowIndex);
          }
        }
        var tab_aux=document.getElementById("tb_mat_aux");
        var total_linhas_aux=tab_aux.rows.length;
        for(var i=0; i<total_linhas_aux; i++){
          var lnh="linha_aux" + i;
          var linha=document.getElementById(lnh);
          if(linha){
            tab_aux.deleteRow(linha.rowIndex);
          }
        }
      }

      function buscarLotes(id, qtde){
        var achou=false;
        var itens=document.getElementById("tabela_aux");
        var total_linhas=itens.rows.length;
        for(var k=1;k<total_linhas; k++){
          var cod=itens.rows[k].cells[0].innerHTML;
          if(cod==id){
            achou=true;
          }
        }
        if(achou==true){
          window.alert("Material já existe na lista!");
        }
        else{
          removerLinhas();
          var x=document.form_atendido;
          x.codigo.value=id;
          x.quantidade.value=qtde;
          carregarLotes("../../xml/remanejamentoLote.php", id, "<?php echo $_SESSION[id_unidade_sistema]?>", "tb_mat", "tb_mat_aux", "adicionar");
        }
      }
      
      function validarCampos(){
        var formul=document.form_atendido;
        var saida="false";
        for(var i=0; i<formul.elements.length; i++){
          var x=document.getElementById(i);
          if(x){
            if(x.value!="" && x.value!="0"){
              saida="true";
            }
          }
        }
        if(saida=="false"){
          window.alert("Selecionar pelo menos um lote!");
          return false;
        }
        return true;
      }
      function validarQtde(pos){
        var formul=document.form_atendido;
        var solic=formul.quantidade;
        var total=0;
        var posicao=parseInt(pos, 10)+1;
        var tab=document.getElementById("tb_mat");
        var qtde=tab.rows[posicao].cells[4].innerHTML;
        var estoq=parseInt(qtde, 10);
        for(var i=0; i<formul.elements.length; i++){
          var x=document.getElementById(i);
          if(x && x.value!=""){
            total+=parseInt(x.value, 10);
          }
        }
        for(var i=0; i<formul.elements.length; i++){
          var ident_aux="aux";
          var x=document.getElementById(i);
          if(x){
            ident_aux+=x.id;
            if(x.value!=""){
              if(x.id==parseInt(pos,10)){
                if(parseInt(x.value, 10)==0){
                  window.alert("Quantidade igual a Zero!");
                  document.getElementById(ident_aux).value="";
                  x.value="";
                  x.focus();
                  return false;
                }
                if(parseInt(x.value, 10)>parseInt(solic.value, 10)){
                  window.alert("Quantidade informada maior que a quantidade solicitada!");
                  x.value="";
                  document.getElementById(ident_aux).value="";
                  x.focus();
                  return false;
                }
                if(parseInt(x.value, 10)>parseInt(estoq, 10)){
                  window.alert("Quantidade em estoque insuficiente!");
                  document.getElementById(ident_aux).value="";
                  x.value="";
                  x.focus();
                  return false;
                }
                if(total>parseInt(solic.value, 10)){
                  window.alert("Quantidade total informada maior que a quantidade solicitada!");
                  document.getElementById(ident_aux).value="";
                  x.value="";
                  x.focus();
                  return false;
                }
                document.getElementById(ident_aux).value=x.value;
              }
            }
            else{
              document.getElementById(ident_aux).value="";
            }
          }
        }
        return true;
      }

      function habilitaBotaoSalvar(){
        var x=document.form_atendido;
        if(Trim(x.login.value)=="" || Trim(x.senha.value)=="" || document.getElementById('tabela_aux').rows.length==1){
          x.salvar.disabled=true;
        }
        else{
          x.salvar.disabled=false;
        }
      }

      function desabilitaBotaoSalvar(){
        var x=document.form_atendido;
        x.salvar.disabled=true;
      }

      function Trim(str){
        return str.replace(/^\s+|\s+$/g,"");
      }

      function salvarMovimento(){
        var x=document.form_atendido;
        if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
          verificaLoginSenhaResponsavelDispensacao();
        }
        else{
          salvarDados();
        }
      }

      function verificaLoginSenhaResponsavelDispensacao(){
        var x=document.form_atendido;
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
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="100%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                  <form name="form_atendido" action="./remanejamento_atendido_fornec.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?>: Atender </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Nº da Solicitação
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="numero" size="30" style="width: 200px" disabled value="<?php echo $solicitacao->id_solicita_remanej;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitante
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="unidade_solicitante" size="30" disabled style="width: 200px" value="<?php echo $solicitacao->nome;?>">
                      </td>
                    </tr>
                    <tr>
                      <?php
                        $sql="select id_unidade, nome from unidade where id_unidade!='$solicitacao->id_unidade' order by nome";
                        $res=mysqli_query($db, $sql);
                        erro_sql("Select Unidade Solicitada", $db, "");
                      ?>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Solicitada
                      </td>
                      <td class="campo_tabela" valign="middle" width="30%">
                        <select name="unidade_solicitada" size="1" style="width: 200px" disabled>
                        <option> Selecione uma Unidade </option>
                        <?php
                          while($unidade_solic=mysqli_fetch_object($res)){
                        ?>
                            <option value="<?php echo $unidade_solic->id_unidade;?>" <?php if($unidade_solic->id_unidade==$solicitacao->idunidade){echo "selected";}?>> <?php echo $unidade_solic->nome;?> </option>
                        <?php
                          }
                        ?>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="15%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Status
                      </td>
                      <td class="campo_tabela" valign="middle" width="100%">
                        <input type="text" name="status" size="30" style="width: 200px" disabled value="<?php echo $solicitacao->status_2;?>">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tb_mat_solic" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="titulo_tabela">
                            <td colspan="3" valign="middle" align="center" width="100%"> Material Solicitado </td>
                          </tr>
                          <tr class="coluna_tabela">
                            <td width="10%" align="center"> Código </td>
                            <td width="70%" align="center"> Material </td>
                            <td width="20%" align="center"> Quantidade Solicitada </td>
                          </tr>
                          <?php
                            $cor_linha = "#CCCCCC";
                            ///////////////////////////////////////
                            //INICIO DAS DEFINIÇÕES DE CADA LINHA//
                            ///////////////////////////////////////

                            $res=mysqli_query($db, $sql_itens);
                            erro_sql("Select Lista", $db, "");
                            while($mat_solicitado=mysqli_fetch_object($res)){
                          ?>
                              <tr class="linha_tabela" bgcolor='<?php echo $cor_linha;?>' onMouseOver="this.bgColor='#D4DFED'" onMouseOut="this.bgColor='<?php echo $cor_linha;?>'" onclick="buscarLotes('<?php echo $mat_solicitado->id_material;?>', '<?php echo $mat_solicitado->qtde_solicita;?>');">
                                <td align="left"> <?php echo $mat_solicitado->codigo_material;?> </td>
                                <td align="left"> <?php echo $mat_solicitado->descricao;?> </td>
                                <td align="right"> <?php echo $mat_solicitado->qtde_solicita;?> </td>
                              </tr>
                          <?php
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
                          ?>
                        </table>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" name="atender" style="font-size: 12px;" value="Não Atender >>" onclick="naoAtender();">
                      </td>
                    </tr>
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%"> Materiais </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tb_mat" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="15%" align="center"> Código </td>
                            <td width="20%" align="center"> Fabricante </td>
                            <td width="15%" align="center"> Lote </td>
                            <td width="15%" align="center"> Validade </td>
                            <td width="15%" align="center"> Estoque </td>
                            <td width="20%" align="center"> Quantidade Atendida </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <div style="display:none">
                          <table id="tb_mat_aux" cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr class="coluna_tabela">
                              <td width="15%" align="center"> ID Material </td>
                              <td width="20%" align="center"> ID Fabricante </td>
                              <td width="15%" align="center"> Lote </td>
                              <td width="15%" align="center"> Validade </td>
                              <td width="15%" align="center"> Estoque </td>
                              <td width="20%" align="center"> Quantidade Atendida </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" name="adicionar" id="adicionar" style="font-size: 12px;" value="Adicionar >>" onclick="inserirLinhas();" disabled>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tabela" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width="20%" align="center"> Código </td>
                            <td width="20%" align="center"> Fabricante </td>
                            <td width="20%" align="center"> Lote </td>
                            <td width="15%" align="center"> Validade </td>
                            <td width="20%" align="center"> Quantidade Atendida </td>
                            <td width="5%" align="center"></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <div style="display:none">
                          <table id="tabela_aux" cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr class="coluna_tabela">
                              <td width="20%" align="center"> ID Material </td>
                              <td width="20%" align="center"> ID Fabricante </td>
                              <td width="20%" align="center"> Lote </td>
                              <td width="15%" align="center"> Validade </td>
                              <td width="20%" align="center"> Quantidade Atendida </td>
                              <td width="5%" align="center"> Linha </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" width="100%" height="100%"></td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="3">
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
                                <input type="password" name="senha" onblur="habilitaBotaoSalvar(); document.form_atendido.salvar.focus();" onfocus="desabilitaBotaoSalvar();">
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                      <td valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/remanejamento/remanejamento_inicial_fornec.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>'">
                        <input type="button" name="salvar" id="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarMovimento();" disabled>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="codigo">
                    <input type="hidden" name="quantidade" id="quantidade">
                    <input type="hidden" name="codigo_atual" value="<?php echo $_GET[codigo];?>">
                    <input type="hidden" name="nao" value="f">
                    <input type="hidden" name="lista_materiais" id="lista_materiais">
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
  }
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else{
    include_once "../../config/erro_config.php";
  }
?>
