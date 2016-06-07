<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  session_regenerate_id();
  $session_id=session_id();
  $chave_unica=date("Y-m-d H:i:s") . $_SESSION[id_unidade_sistema] . $session_id;

  /////////////////////////////////////////////////////////////////
  //  Sistema..: DIM
  //  Arquivo..: mestoque_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de movimento de estoque
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if(file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";

    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }

    if(isset($_GET[aplicacao])){
      $_SESSION[APLICACAO]=$_GET[aplicacao];
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    if($_GET[aplicacao] <> ''){
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

    require "../../verifica_acesso.php";
    
    $sql="select num_max_material from parametro";
    $res=mysqli_query($db, $sql);
    erro_sql("Select parametro", $db, "");
    if(mysqli_num_rows($res)>0){
      $qtde_max=mysqli_fetch_object($res);
      $max_itens=$qtde_max->num_max_material;
      if($max_itens==""){
        $max_itens="vazio";
      }
    }
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="javascript" type="text/javascript" src="../../scripts/mestoqueLote.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      var d=new Date();
      var ID=d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

      function popup_medicamento(){
        var height=350;
	    var width=450;
	    var left=(screen.availWidth-width)/2;
	    var top=(screen.availHeight-height)/2;

        var x=document.form_inclusao;
        var id_movto=x.numero.value;
	    if(window.showModalDialog){
 		  var dialogArguments=new Object();
		  var _R=window.showModalDialog("pesquisa_material.php?id_operacao=" + id_movto, dialogArguments, "dialogWidth=450px;dialogHeight=350px;dialogLeft=290px;dialogTop=250px;scroll=yes;status=no;");
		  if("undefined"!=typeof(_R)){
			SetNameMedicamento(_R.strArgs);
  		  }
	    }
	    //NS
	    else{
		  var left=(screen.width-width)/2;
		  var top=(screen.height-height)/2;
 		  var winHandle=window.open("pesquisa_material.php?id_operacao=" + id_movto, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		  winHandle.focus();
	    }
      }

      function SetNameMedicamento(argumentos){
        var valores=argumentos.split('|');

        var x=document.form_inclusao;
        if(valores[0]=="entrada"){
          x.codigo.value=valores[1];
          x.descricao.value=valores[2];
        }
        if(valores[0]=="saida"  || valores[0]=="perda" || valores[0]=="lote"){
          x.codigo.value=valores[1];
          x.descricao.value=valores[2];
          x.flg_lote.value="t";
          x.descricao.focus();
        }
      }

      function verificarEstoque(){
        var y=document.form_inclusao;
        var itens=y.lista_materiais.value;
        var numero=y.numero.value;
        var motivo=y.motivo.value;
        var chave=y.chave.value;
        var id_login=y.id_login.value;
        var url = "../../xml/mestoqueEstoque.php?itens=" + itens + "&numero=" + numero + "&motivo=" + motivo + "&chave=" + chave + "&id_login=" + id_login;
        var palavra=/<?php echo SIMBOLO;?>/gi;
        url=url.replace(palavra, "CERQUILHA");
        requisicaoHTTP("GET", url, true);
      }

      function trataDados(){
        var x=document.form_inclusao;
	    var info = ajax.responseText;  // obtém a resposta como string

        if(info.substr(0, 8)=="validade"){
          if(info.substr(9, 2)=="NO"){
             var validade=info.substr(11, 10);
             valid=validade.substr(8, 2) + "-" + validade.substr(5, 2) + "-" + validade.substr(0, 4);
             window.alert ("Esse lote já está cadastrado com à validade: "+valid+ " !!!");
             x.validade.focus();
             x.validade.select();
           }
           else if(info.substr(9, 2)=="OK"){
                  inserirLinha();
           }
        }
        else{
          x.codigo.value=info;
          var valor=info.split("|");
          var login_senha=info.split("@");
          if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
            window.alert("Login e/ou Senha Inválidos!");
            x.login.focus();
            return;
          }
          if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
            x.id_login.value=login_senha[1];
            btSalvar();
            return;
          }
          if(valor[0]=="estoque"){
            if(valor[1]=="NO"){
              window.alert(valor[2]);
              x.salvar.disabled="";
            }
            else{
              var resposta=window.confirm("Movimento " + valor[2] + " gerado com sucesso! Deseja imprimir?");
              if(resposta){
                var link="<?php echo URL;?>/modulos/impressao/impressao_mestoque.php?chave=" + valor[2];
                window.open(link);
              }
              window.location="<?php echo URL;?>/modulos/mestoque/mestoque_inclusao.php?aplicacao=<?php echo $_SESSION[APLICACAO];?>";
            }
          }
          if(info!="" && !isNaN(info)){
            x.flg_lote.value="t";
            buscarLote();
          }
          if(info.substr(0, 7)!="estoque" && info!="" && isNaN(info)){
            var msg="Quantidade em estoque insuficiente\nMaterial - Lote - Fabricante\n" + info;
            window.alert(msg);
            x.salvar.disabled="";
          }
          if(info==""){
            removerLote();
            window.alert("Material não encontrado!");
            x.descricao.focus();
            x.descricao.select();
          }
        }
      }
      function verificarMedicamento(){
        var y=document.form_inclusao;
        var descricao=y.descricao.value;
		
		//alert(descricao);
        var id_movto=y.numero.value;
        var url = "../../xml/mestoqueVerificarMedicamento.php?descricao=" + descricao + "&id_movto=" + id_movto + "&id_unidade=" + <?php echo $_SESSION[id_unidade_sistema];?> + "&aplicacao=mestoque";
        requisicaoHTTP("GET", url, true);
      }

      var cont=1;
      function limparVariaveis(){
        var x=document.form_inclusao;
        x.numero.options[0].selected=true;
        x.motivo.value="";
        x.lista_materiais.value="";
        x.aux_lista_materiais.value="";
        x.submit();
      }
      
      function inserirLinha(){
        var x=document.form_inclusao;
        var tipo=x.numero.value;
        var achou=false;

        var itens=document.getElementById("tabela");
        var total_linhas=itens.rows.length;

        var itens_aux=document.getElementById("tabela_aux");
        var total_linhas_aux=itens_aux.rows.length;

        if("<?php echo $max_itens;?>"!="vazio"){
          if(total_linhas><?php echo $max_itens;?>){
            window.alert("Este material não será adicionado.\nO limite máximo para cada documento é de <?php echo $max_itens;?> materiais.\nClique no botão salvar para efetuar a operação com os <?php echo $max_itens;?> materiais adicionados anteriormente.");
            return false;
          }
        }

        if(tipo=="10"){
          if(validarCamposEntrada()==true){
            for(var i=1; i<total_linhas_aux; i++){
              if(itens_aux.rows[i].cells[0].innerHTML==x.codigo.value &&
                 itens_aux.rows[i].cells[1].innerHTML==x.fabricante.options[x.fabricante.selectedIndex].value &&
                 itens_aux.rows[i].cells[2].innerHTML==x.lote_entrada.value){
                achou=true;
              }
            }

            if(achou==true){
              window.alert("Material já adicionado!");
              document.form_inclusao.descricao.focus();
              return false;
            }
            else{
              var tab=itens.insertRow(total_linhas);
              tab.id="linha" + cont;
              tab.className="campo_tabela";

              //material
              var cel0=tab.insertCell(0);
              cel0.align="left";
              cel0.innerHTML=x.descricao.value;

              //fabricante
              var cel1=tab.insertCell(1);
              cel1.align="left";
              var simbolo=/&/gi;
              var palavra=x.fabricante.options[x.fabricante.selectedIndex].text;
              palavra=palavra.replace(simbolo, "&amp;");
              cel1.innerHTML=palavra;

              //lote
              var cel2=tab.insertCell(2);
              cel2.align="left";
              cel2.innerHTML=x.lote_entrada.value;

              //validade
              var cel3=tab.insertCell(3);
              cel3.align="center";
              cel3.innerHTML=x.validade.value;

              //quantidade
              var cel4=tab.insertCell(4);
              cel4.align="right";
              cel4.innerHTML=parseInt(x.quantidade.value, 10);

              var cel5=tab.insertCell(5);
              cel5.align="center";
              var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
              var urlAlterar="javascript:alterarLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "', '" + tipo + "')";
              cel5.innerHTML=linkAlterar.link(urlAlterar);

              var cel6=tab.insertCell(6);
              cel6.align="center";
              var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
              var urlRemover="javascript:removerLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "', '" +  tipo + "')";
              cel6.innerHTML=linkRemover.link(urlRemover);

              //tabela auxiliar
              var tab_aux=itens_aux.insertRow(total_linhas_aux);
              tab_aux.id="linha_aux" + cont;
              tab_aux.className="campo_tabela";

              //id material
              var cel0=tab_aux.insertCell(0);
              cel0.align="left";
              cel0.innerHTML=x.codigo.value;

              //id fabricante
              var cel1=tab_aux.insertCell(1);
              cel1.align="left";
              cel1.innerHTML=x.fabricante.options[x.fabricante.selectedIndex].value;

              //lote
              var cel2=tab_aux.insertCell(2);
              cel2.align="left";
              cel2.innerHTML=x.lote_entrada.value;

              //validade
              var cel3=tab_aux.insertCell(3);
              cel3.align="center";
              var str=x.validade.value.substr(6, 4) + "-" + x.validade.value.substr(3, 2) + "-" + x.validade.value.substr(0, 2);
              cel3.innerHTML=str;

              //quantidade
              var cel4=tab_aux.insertCell(4);
              cel4.align="right";
              cel4.innerHTML=parseInt(x.quantidade.value, 10);

              //indice fabricante
              var cel5=tab_aux.insertCell(5);
              cel5.align="right";
              cel5.innerHTML=x.fabricante.selectedIndex;

              var cel6=tab_aux.insertCell(6);
              cel6.align="right";
              cel6.innerHTML=cont;

              cont++;

              limpaCampos(tipo);
            }
          }
        }
        else{
          //tipo operacao != inventario
          if(validarCamposSaida()==true){
            var valores=x.lote_saida.value.split("|");
            var indexLote=x.lote_saida.selectedIndex;
            var lot=x.lote_saida.options[x.lote_saida.selectedIndex].text;

            for(var i=1; i<total_linhas_aux; i++){
              if(itens_aux.rows[i].cells[0].innerHTML==x.codigo.value &&
                 itens_aux.rows[i].cells[1].innerHTML==valores[1] &&
                 itens_aux.rows[i].cells[2].innerHTML==valores[0]){
                achou=true;
              }
            }

            if(achou==true){
              window.alert("Material já adicionado!");
              document.form_inclusao.descricao.focus();
              return false;
            }
            else{
              var tab=itens.insertRow(total_linhas);
              tab.id="linha" + cont;
              tab.className="campo_tabela";

              //material
              var cel0=tab.insertCell(0);
              cel0.align="left";
              cel0.innerHTML=x.descricao.value;

              //fabricante
              var valoresTexto=lot.split("---");
              var valor=valoresTexto[2].split(":");
              var cel1=tab.insertCell(1);
              cel1.align="left";
              cel1.innerHTML=valor[1];

              //lote
              var cel2=tab.insertCell(2);
              cel2.align="left";
              cel2.innerHTML=valores[0];

              //validade
              valoresTexto=lot.split("---");
              valor=valoresTexto[1].split(":");
              var cel3=tab.insertCell(3);
              cel3.align="center";
              cel3.innerHTML=valor[1];

              //quantidade
              var cel4=tab.insertCell(4);
              cel4.align="right";
              cel4.innerHTML=parseInt(x.quantidade.value, 10);

              var cel5=tab.insertCell(5);
              cel5.align="center";
              var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
              var urlAlterar="javascript:alterarLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "', '" + tipo + "')";
              cel5.innerHTML=linkAlterar.link(urlAlterar);

              var cel6=tab.insertCell(6);
              cel6.align="center";
              var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
              var urlRemover="javascript:removerLinha('linha" + cont + "', 'linha_aux" + cont + "', '" + total_linhas + "', '" + tipo + "')";
              cel6.innerHTML=linkRemover.link(urlRemover);

              //tabela auxiliar
              var tab_aux=itens_aux.insertRow(total_linhas_aux);
              tab_aux.id="linha_aux" + cont;
              tab_aux.className="campo_tabela";

              //id material
              var cel0=tab_aux.insertCell(0);
              cel0.align="left";
              cel0.innerHTML=x.codigo.value;

              //id fabricante
              var cel1=tab_aux.insertCell(1);
              cel1.align="left";
              cel1.innerHTML=valores[1];

              //lote
              var cel2=tab_aux.insertCell(2);
              cel2.align="left";
              cel2.innerHTML=valores[0];

              //validade
              var cel3=tab_aux.insertCell(3);
              cel3.align="center";
              cel3.innerHTML=valores[2];

              //quantidade
              var cel4=tab_aux.insertCell(4);
              cel4.align="right";
              cel4.innerHTML=parseInt(x.quantidade.value, 10);

              //indice lote
              var cel5=tab_aux.insertCell(5);
              cel5.align="right";
              cel5.innerHTML=indexLote;

              var cel6=tab_aux.insertCell(6);
              cel6.align="right";
              cel6.innerHTML=cont;

              cont++;

              limpaCampos(tipo);
            }
          }
        }
        if("<?php echo $mostrar_responsavel_dispensacao;?>"=="S"){
          document.getElementById("salvar").disabled=true;
        }
        else{
          document.getElementById("salvar").disabled="";
        }
        return true;
      }

      function limpaCampos(tipo){
        var x=document.form_inclusao;
        
        if(tipo=="10"){
          x.descricao.value="";
          x.codigo.value="";
          x.fabricante.options[0].selected=true;
          x.lote_entrada.value="";
          x.validade.value="";
          x.quantidade.value="";
        }
        else{
          x.descricao.value="";
          x.codigo.value="";
          x.lote_saida.options[0].selected=true;
          x.quantidade.value="";
          removerLote();
        }
      }

      function removerLinha(lnh, lnh_aux, pos, tipo){
        var tab=document.getElementById("tabela");
        tab.deleteRow(document.getElementById(lnh).rowIndex);
        var tab_aux=document.getElementById("tabela_aux")
        tab_aux.deleteRow(document.getElementById(lnh_aux).rowIndex);

        var total_linhas=tab.rows.length;
        for(var i=pos; i<total_linhas; i++){
          var j=tab_aux.rows[i].cells[6].innerHTML;
          tab.rows[i].deleteCell(5);
          var cel5=tab.rows[i].insertCell(5);
          cel5.align="center";
          var linkAlterar="<img src='<?php echo URL;?>/imagens/b_edit.gif' border='0' title='Alterar'>";
          var urlAlterar="javascript:alterarLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "', '" + tipo + "')";
          cel5.innerHTML=linkAlterar.link(urlAlterar);

          tab.rows[i].deleteCell(6);
          var cel6=tab.rows[i].insertCell(6);
          cel6.align="center";
          var linkRemover="<img src='<?php echo URL;?>/imagens/trash.gif' border='0' title='Excluir'>";
          var urlRemover="javascript:removerLinha('linha" + j + "', 'linha_aux" + j + "', '" + i + "', '" + tipo + "')";
          cel6.innerHTML=linkRemover.link(urlRemover);
        }
        if(total_linhas<=1){
          document.getElementById("salvar").disabled="true";
        }
        document.form_inclusao.descricao.focus();
      }

      function alterarLinha(lnh, lnh_aux, pos, tipo){
        var itens=document.getElementById("tabela");
        var itens_aux=document.getElementById("tabela_aux");
        var x=document.form_inclusao;

        if(tipo=="10"){
          x.descricao.value=itens.rows[pos].cells[0].innerHTML;
          x.codigo.value=itens_aux.rows[pos].cells[0].innerHTML;
          x.fabricante.options[itens_aux.rows[pos].cells[5].innerHTML].selected=true;
          x.lote_entrada.value=itens.rows[pos].cells[2].innerHTML;
          x.validade.value=itens.rows[pos].cells[3].innerHTML;
          x.quantidade.value=itens.rows[pos].cells[4].innerHTML;
        }
        else{
          x.flg_lote.value="t";
          x.descricao.value=itens.rows[pos].cells[0].innerHTML;
          x.codigo.value=itens_aux.rows[pos].cells[0].innerHTML;
          //falta arranja um jeito de atualiza o campo lote
          x.quantidade.value=itens.rows[pos].cells[4].innerHTML;
          var CodLotFabr=itens_aux.rows[pos].cells[2].innerHTML;
          CodLotFabr+="|" + itens_aux.rows[pos].cells[1].innerHTML;
          CodLotFabr+="|" + itens_aux.rows[pos].cells[3].innerHTML;
          x.indice_lote.value=CodLotFabr;
        }
        removerLinha(lnh, lnh_aux, pos, tipo);
      }

      function salvarDados(tipo){
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

      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      
      //verifica se não existe lote cadastrado - pois a validade deve ser a mesma
      function verificaValidade(){
        var x=document.form_inclusao;
        var material=x.codigo.value;
        var fabr=x.fabricante.value;
        var lot=x.lote_entrada.value;
        var valid=x.validade.value.substr(6, 4) + "-" + x.validade.value.substr(3, 2) + "-" + x.validade.value.substr(0, 2);
        var und=x.unidade_atual.value;
        var url = "../../xml/verificarValidade.php?mat="+material+"&fabr="+fabr+"&lot="+lot+"&valid="+valid+"&und="+und;
        var palavra=/<?php echo SIMBOLO;?>/gi;
        url=url.replace(palavra, "CERQUILHA");
        requisicaoHTTP("GET", url, true);
      }
      
      function validarCamposSaida(){
        var x=document.form_inclusao;
        var doc=x.numero;
        var mot=x.motivo;
        var mat=x.codigo;
        var descr=x.descricao;
        var lot=x.lote_saida;
        var qtde=x.quantidade;

        if(doc.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          doc.focus();
          return false;
        }
        if(mot.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          mot.focus();
          mot.select();
          return false;
        }
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        if(mat.value==""){
          window.alert("Material Não Cadastrado!");
          descr.focus();
          descr.select();
          return false;
        }
        if(lot.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          lot.focus();
          return false;
        }
        if(qtde.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          qtde.focus();
          qtde.select();
          return false;
        }
        if(parseInt(qtde.value, 10)==0){
          window.alert("Quantidade Igual a Zero!");
          qtde.focus();
          qtde.select();
          return false;
        }
        var valores=lot.options[lot.selectedIndex].text;
        var valoresTexto=valores.split("---");
        var valor=valoresTexto[3].split(":");
        if(parseInt(qtde.value, 10)>parseInt(valor[1], 10)){
          window.alert("Quantidade em estoque insuficiente!");
          qtde.focus();
          qtde.select();
          return false;
        }

        return true;
      }
      function validarCamposEntrada(){
        var x=document.form_inclusao;
        var doc=x.numero;
        var mot=x.motivo;
        var mat=x.codigo;
        var descr=x.descricao;
        var fabr=x.fabricante;
        var lot=x.lote_entrada;
        var valid=x.validade;
        var qtde=x.quantidade;

        if(doc.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          doc.focus();
          return false;
        }
        if(mot.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          mot.focus();
          mot.select();
          return false;
        }
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        if(mat.value==""){
          window.alert("Material Não Cadastrado!");
          descr.focus();
          descr.select();
          return false;
        }
        if(fabr.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          fabr.focus();
          return false;
        }
        if(lot.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          lot.focus();
          lot.select();
          return false;
        }
        if(valid.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          valid.focus();
          valid.select();
          return false;
        }
        if(qtde.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          qtde.focus();
          qtde.select();
          return false;
        }
        if(parseInt(qtde.value, 10)==0){
          window.alert("Quantidade Igual a Zero!");
          qtde.focus();
          qtde.select();
          return false;
        }
        if(!validarData(x.validade)){
          alert ("A data fornecida foi preenchida incorretamente.");
          x.validade.focus();
          x.validade.select();
          return false;
        }
        return true;
      }
      
      function removerLote(){
        var x=document.getElementById("lote_saida");
        for(var i=x.length-1; i>0; i--){
          x.options[i].selected=true;
          x.remove(i);
        }
        document.getElementById("opcao_lote").innerHTML="Selecione um Lote";
      }
      
      function desabilitarCampo(){
        var x=document.form_inclusao;
        x.motivo.value="";
        x.descricao.value="";
        x.codigo.value="";
        x.quantidade.value="";
        x.salvar.disabled="true";
        if(x.numero.selectedIndex==0){
          removerTabelas();
          removerLote();
          x.lote_saida.selectedIndex=0;
          x.motivo.disabled="true";
          document.getElementById("tela_material").style.display="none";
          x.descricao.disabled="true";
          x.lote_saida.disabled="true";
          x.quantidade.disabled="true";
          x.adicionar.disabled="true";
        }
        else{
          removerTabelas();
          removerLote();
          document.getElementById("tela_material").style.display="";
          x.motivo.disabled="";
          if(x.numero.value=="10"){
            x.fabricante.selectedIndex=0;
            x.lote_entrada.value="";
            x.validade.value="";
            x.descricao.disabled="";
            x.fabricante.disabled="";
            x.lote_entrada.disabled="";
            x.validade.disabled="";
          }
          else{
            x.lote_saida.selectedIndex=0;
            x.descricao.disabled="";
            x.lote_saida.disabled="";
          }
          x.quantidade.disabled="";
          x.adicionar.disabled="";
        }
        if(x.numero.value=="10"){
          document.getElementById("tabela_entrada").style.display="";
          document.getElementById("tabela_saida").style.display="none";
        }
        else{
          document.getElementById("tabela_entrada").style.display="none";
          document.getElementById("tabela_saida").style.display="";
        }
      }
      
      function removerTabelas(){
        var tab=document.getElementById("tabela");
        var tab_aux=document.getElementById("tabela_aux")
        for(var i=1; i<cont; i++){
          var lnh="linha" + i;
          var lnh_aux="linha_aux" + i;
          var x=document.getElementById(lnh);
          var y=document.getElementById(lnh_aux);
          if(x){
            tab.deleteRow(document.getElementById(lnh).rowIndex);
          }
          if(y){
            tab_aux.deleteRow(document.getElementById(lnh_aux).rowIndex);
          }
        }
        document.form_inclusao.salvar.disabled="true";
      }

      function btSalvar(){
        var x=document.form_inclusao;
        var operacao=x.numero.value;
        x.salvar.disabled="true";
        salvarDados(operacao);
      }

      function buscarLote(){
        carregarLote('indice_lote', 'flg_lote', 'lote_saida', 'opcao_lote', '../../xml/mestoqueLote.php', 'codigo', 'numero', 'unidade_atual', 'mestoque');
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
          btSalvar();
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
                <table width="100%" cellpadding="0" cellspacing="0" border="0" height="100%">
                  <form name="form_inclusao" action="./mestoque_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela">
                      <td colspan="4" valign="middle" align="center" width="100%" height="21"> <?php echo $nome_aplicacao;?> </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Tipo de Movimento
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="80%">
                        <select name="numero" id="numero" size="1" style="width: 200px" <?php if($inclusao_perfil==""){echo "disabled='true'";}else{echo "enabled='true'";}?>" onchange="desabilitarCampo();">
                          <option value="0"> Selecione uma Descrição </option>
                          <?php
                            $sql="select id_tipo_movto, descricao from tipo_movto where flg_movto='s' and status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Tipo Movimento", $db, "");
                            while($numero_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $numero_info->id_tipo_movto;?>"> <?php echo $numero_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Motivo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="80%">
                        <textarea name="motivo" row="2" cols="31" style="width: 500px"></textarea>
                      </td>
                    </tr>
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> Material </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" valign="middle" width="67%">
                        <input type="text" name="descricao" id="descricao" size="30" style="width: 560px" onchange="verificarMedicamento();" onfocus="buscarLote();">
                        <div id="acDiv"></div>
                      </td>
                      <td colspan="2" class="campo_tabela" valign="middle" width="13%">
                        <div id="tela_material" style="display:none;">&nbsp;<a onclick="popup_medicamento();"><img src="<?php echo URL;?>/imagens/b_search.png" border="0" title="Pesquisar"></a></div>
                      </td>
                    </tr>
                    <input type="hidden" name="codigo" id="codigo" size="30">
                    <tr>
                      <td colspan="4" class="descricao_campo_tabela">
                        <div id="tabela_saida" style="display:'';">
                          <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                            <tr>
                              <td class="descricao_campo_tabela" valign="middle" width="20%">
                                <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                                Lote
                              </td>
                              <td class="campo_tabela" valign="middle" width="80%" colspan="3">
                                <select name="lote_saida" id="lote_saida" size="1" style="width: 500px">
                                  <option id="opcao_lote" value="0"> Selecione um Lote </option>
                                </select>
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" class="descricao_campo_tabela">
                        <div id="tabela_entrada" style="display:none;">
                          <table cellpadding='0' cellspacing='0' border='0' width='100%'>
                            <tr>
                              <td class="descricao_campo_tabela" valign="middle" width="20%">
                                <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                                Fabricante
                              </td>
                              <td class="campo_tabela" colspan="3" valign="middle" width="80%">
                                <select name="fabricante" id="fabricante" size="1" style="width: 200px">
                                  <option value="0"> Selecione um Fabricante </option>
                                  <?php
                                    $sql="select id_fabricante, descricao from fabricante where status_2='A' order by descricao ";
                                    $res=mysqli_query($db, $sql);
                                    erro_sql("Select Fabricante", $db, "");
                                    while($fabricante_info=mysqli_fetch_object($res)){
                                  ?>
                                      <option value="<?php echo $fabricante_info->id_fabricante;?>"> <?php echo $fabricante_info->descricao;?> </option>
                                  <?php
                                    }
                                  ?>
                                </select>
                              </td>
                            </tr>
                            <tr>
                              <td class="descricao_campo_tabela" valign="middle" width="20%">
                                <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                                Lote
                              </td>
                              <td class="campo_tabela" valign="middle" width="30%">
                                <input type="text" name="lote_entrada" id="lote_entrada" maxlength="30" style="width: 200px" onKeyPress="return validarLote(event);">
                              </td>
                              <td class="descricao_campo_tabela" valign="middle" width="15%">
                                <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                                Validade
                              </td>
                              <td class="campo_tabela" valign="middle" width="35%">
                                <input type="text" name="validade" size="8"  maxlength="10" onKeyPress="return mascara_data(event,this);">
                              </td>
                            </tr>
                          </table>
                        </div>
                       </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Quantidade
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="80%">
                        <input type="text" name="quantidade" maxlength="12" style="width: 200px" onKeyPress="return isNumberKey(event);" onblur="return verificarNumero(this);">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" name="adicionar" style="font-size: 12px;" value="Adicionar >>" onclick="if(document.form_inclusao.numero.value==10){verificaValidade();}else{inserirLinha(); }">
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <table id="tabela" cellpadding='0' cellspacing='1' border='0' width='100%'>
                          <tr class="coluna_tabela">
                            <td width='41%' align='center'> Material </td>
                            <td width='13%' align='center'> Fabricante </td>
                            <td width='13%' align='center'> Lote </td>
                            <td width='10%' align='center'> Validade </td>
                            <td width='13%' align='center'> Quantidade </td>
                            <td width='5%' align='center'></td>
                            <td width='5%' align='center'></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4">
                        <div style="display:none">
                          <table id="tabela_aux" cellpadding='0' cellspacing='1' border='0' width='100%'>
                            <tr class="coluna_tabela">
                              <td width='41%' align='center'> ID Material </td>
                              <td width='13%' align='center'> ID Fabricante </td>
                              <td width='13%' align='center'> Lote </td>
                              <td width='10%' align='center'> Validade </td>
                              <td width='13%' align='center'> Quantidade </td>
                              <td width='5%' align='center'> Index Fabricante </td>
                              <td width='5%' align='center'> Linha </td>
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
                                <input type="password" name="senha" onblur="habilitaBotaoSalvar(); document.form_inclusao.salvar.focus();" onfocus="desabilitaBotaoSalvar();">
                              </td>
                            </tr>
                          </table>
                        </div>
                      </td>
                      <td valign="middle" align="right" width="100%">
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

                    <input type="hidden" name="lista_materiais" id="lista_materiais">
                    <input type="hidden" name="indice_lote" id="indice_lote" value="f">
                    <input type="hidden" name="unidade_atual" id="unidade_atual" value="<?php echo $_SESSION[id_unidade_sistema];?>">
                    <input type="hidden" name="flg_lote" id="flg_lote" value="f">
                    <input type="hidden" name="chave" value="<?php echo $chave_unica;?>">
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

    <style type="text/css">
    <!--
      /* Definição dos estilos do DIV */
      /* CSS for the DIV */
      #acDiv{ border: 1px solid #9F9F9F; background-color:#F3F3F3; padding: 3px; font-size:10px; font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; display:none; position:absolute; z-index:999;}
      #acDiv UL{ list-style:none; margin: 0; padding: 0; }
      #acDiv UL LI{ display:block;}
      #acDiv A{ color:#000000; text-decoration:none; }
      #acDiv A:hover{ color:#000000; }
      #acDiv LI.selected{ background-color:#7d95ae; color:#000000; }
    //-->
    </style>

    <script language="javascript" type="text/javascript" src="../../scripts/mestoque.js"></script>
    <script language="javascript">
    <!--
      //Instanciar objeto AutoComplete
      var AC = new dmsAutoComplete('descricao','acDiv', "numero", "unidade_atual", "mestoque");

      AC.ajaxTarget = '../../xml/mestoqueMedicamento.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
      AC.chooseFunc = function(id,label){
        var x=document.form_inclusao;
        x.codigo.value = id;
        var texto=x.numero.value;
        if(x.codigo.value!=""){
          x.flg_lote.value="t";
          buscarLote();
          if(texto=="10"){
            x.fabricante.focus();
          }
          else{
            x.lote_saida.focus();
          }
        }
      }

      var x=document.form_inclusao;
      if(x.numero.selectedIndex==0){
        desabilitarCampo();
      }

      x.numero.focus();
    //-->
    </script>

<?php
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else{
    include_once "../../config/erro_config.php";
  }
?>
