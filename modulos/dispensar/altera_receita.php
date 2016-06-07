<?php
/* 
	Copyright 2011 Inform·tica de MunicÌpios Associados
	Este arquivo È parte do programa DIM
	O DIM È um software livre; vocÍ pode redistribuÌ-lo e/ou modific·-lo dentro dos termos da LicenÁa P˙blica Geral GNU como publicada pela FundaÁ„o do Software Livre (FSF); na vers„o 2 da LicenÁa.
	Este programa È distribuÌdo na esperanÁa que possa ser  ˙til, mas SEM NENHUMA GARANTIA; sem uma garantia implÌcita de ADEQUA«√O a qualquer  MERCADO ou APLICA«√O EM PARTICULAR. Veja a LicenÁa P˙blica Geral GNU/GPL em portuguÍs para maiores detalhes.
	VocÍ deve ter recebido uma cÛpia da LicenÁa P˙blica Geral GNU, sob o tÌtulo "LICENCA.txt", junto com este programa, se n„o, acesse o Portal do Software P˙blico Brasileiro no endereÁo www.softwarepublico.gov.br ou escreva para a FundaÁ„o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  
  $_SESSION[APLICACAO]=$_GET[aplicacao];

  

 

  //////////////////////////////////////////////////
  //TESTANDO EXIST NCIA DE ARQUIVO DE CONFIGURA«√O//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICA«√O DE SEGURAN«A//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }

    session_regenerate_id();
    $teste = session_id();
    $num_controle = date("Y-m-d H:i:s").$id_unidade_sistema.$teste;
//    echo '*'.$num_controle;

function somadata($pData, $pDias)//formato BR
{
  if(ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $pData, $vetData))
  {
    $fDia = $vetData[1];
    $fMes = $vetData[2];
    $fAno = $vetData[3];

    for($x = 0; $x <= $pDias; $x++)
    {
      if($fMes == 1 || $fMes == 3 || $fMes == 5 || $fMes == 7 || $fMes == 8 || $fMes == 10 || $fMes == 12)
      {
        $fMaxDia = 31;
      }
      elseif($fMes == 4 || $fMes == 6 || $fMes == 9 || $fMes == 11)
      {
        $fMaxDia = 30;
      }
      else
      {
        if($fMes == 2 && $fAno % 4 == 0 && $fAno % 100 != 0)
        {
          $fMaxDia = 29;
        }
        elseif($fMes == 2)
        {
          $fMaxDia = 28;
        }
      }
      $fDia++;
      if($fDia > $fMaxDia)
      {
        if($fMes == 12)
        {
          $fAno++;
          $fMes = 1;
          $fDia = 1;
        }
        else
        {
          $fMes++;
          $fDia = 1;
        }
      }
    }
    if(strlen($fDia) == 1)
      $fDia = "0" . $fDia;
    if(strlen($fMes) == 1)
      $fMes = "0" . $fMes;
    return "$fDia/$fMes/$fAno";
  }
}

    if (($_POST[ano]!= "") and  ($_POST[numero]!= "") and ($_POST[unidade]!= ""))
    {
     $ano = $_POST[ano];
     $unidade = $_POST[unidade];
     $numero = $_POST[numero];

     $sql = "select id_receita, status_2, profissional_id_profissional,
                    data_emissao, subgrupo_origem_id_subgrupo_origem, cidade_id_cidade,
                    paciente_id_paciente
             from
                    receita
             where
                    ano = '$ano'
                    and numero = '$numero'
                    and unidade_id_unidade = '$unidade'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));

     $id_receita = $dados_receita->id_receita;

     $status_receita = $dados_receita->status_2;
     
     $prescritor = $dados_receita->profissional_id_profissional;

     $sql = "select
                   inscricao, nome
             from
                   profissional
             where
                   id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $inscricao = $dados_prescritor->inscricao;
     $nomeprescritor = $dados_prescritor->nome;

     $data_emissao = $dados_receita->data_emissao;
     $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);

     $origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
     $sql = "select
                   descricao
             from
                   subgrupo_origem
             where
                   id_subgrupo_origem = '$origem'";
     $dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeorigem = $dados_origem->descricao;

     $cidadereceita = $dados_receita->cidade_id_cidade;
     $sql = "select c.nome, e.uf
             from
                    cidade c, estado e
             where
                    c.id_cidade = '$cidadereceita'
                    and e.id_estado = c.estado_id_estado";
     $dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidadereceita = $dados_cidadereceita->nome."/".$dados_cidadereceita->uf;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select nome
             from
                    paciente
             where
                    id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $nome            = $dados_paciente->nome;

    }
    
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA P¡GINA//
    ////////////////////////////////////
    require DIR."/header.php";

    //permiss„o
    require "../../verifica_acesso.php";

    //caminho
    if ($_GET[aplicacao] <> '')
    {
     $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";

?>
<script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src= "../../scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src= "../../scripts/frame.js"></script>
<script language="JavaScript" type="text/JavaScript">
<!--
  function Trim(str){
    return str.replace(/^\s+|\s+$/g,"");
  }
  function habilitaBotaoSalvar(){
    var x=document.form_alteracao;
    if(Trim(x.login.value)=="" || Trim(x.senha.value)==""){
      x.salvar.disabled=true;
    }
    else{
      x.salvar.disabled=false;
      x.salvar.focus();
    }
  }
  function desabilitaBotaoSalvar(){
    var x=document.form_alteracao;
    x.salvar.disabled=true;
  }
  function verificaLoginSenhaResponsavelDispensacao(){
    var x=document.form_alteracao;
    var url = "../../xml_dispensacao/verificar_login_senha_responsavel_dispensacao.php?login="+x.login.value+"&senha="+x.senha.value;
    requisicaoHTTP("GET", url, true, '');
  }
  function salvarReceita(){
    var x=document.form_alteracao;
    if(x.flag_mostrar_responsavel_dispensacao.value=="S"){
      verificaLoginSenhaResponsavelDispensacao();
    }
    else{
      verificar_campos();
    }
  }
//-->
var d = new Date();
var ID = d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

function validarNotificacao(campo){
    var numero = campo.value;
    var comprimento = numero.length;
    var aux = numero.charAt(0);
    var aux2 ='';
    var cont=0;

    var tam = numero.length;
    for (var i=0; i<tam; i++)
    {
       numero = numero.replace(" ", "");
    }
    campo.value = numero;
  
  
    var caracteres = ",.;/<>:?~^]}¥`[{=+-_)\\\\(*&®%$#@!'|‡ËÏÚ˘‚ÍÓÙ˚‰ÎÔˆ¸·ÈÌÛ˙„ı¿»Ã“Ÿ¬ Œ‘€ƒÀœ÷‹¡…Õ”⁄√’Á« ";
    caracteres = caracteres + '"';

    for (i = 0;i<caracteres.length;i++)
    {
        if(numero.indexOf(caracteres.charAt(i)) != -1)
        {
            var strerror = caracteres.substring(i,i+1);
            window.alert("VocÍ digitou um caracter inv·lido!");
            globalvar = campo;
            setTimeout("globalvar.focus()",250);
            globalvar.select();
            return false
        }
    }


    for (var i=1;i<comprimento;i++)
    {
        aux2 = numero.charAt(i);

        if (aux==aux2)
        {
           cont++;
        }
        aux = numero.charAt(i);
    }
    comprimento--;

    if (comprimento > 0)
    {
      if (cont == comprimento)
      {
       alert("Digite um n˙mero de noficaÁ„o v·lido!");
       globalvar = campo;
       setTimeout("globalvar.focus()",250);
       globalvar.select();
      }
    }
    else{
      if (campo.value =='0')
      {
       alert("Digite um n˙mero de noficaÁ„o v·lido!");
       globalvar = campo;
       setTimeout("globalvar.focus()",250);
       globalvar.select();
      }
    }
 }
 
function buscar_lote_altera(id_material)
{
    var url = "../../xml_dispensacao/buscar_lote_altera.php?material="+id_material;
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraLotes
    });
}

function mostraLotes(resposta)
{
  var resp = resposta.responseText;
  var separador= resp.indexOf("|");
  var id_material=resp.substr(0, separador);

  tabela = resp.substr(separador+1);
  var div = document.getElementById(id_material);
  div.innerHTML = tabela;

  if (div.style.display == 'none')
      div.style.display = 'inline';
  else
   div.style.display = 'none';
}

function popup_autorizador(id_itens_receita, material)
{
    var texto = material;
    var pos = texto.indexOf(",");
    var mat_par = texto.substr(pos+1);
    var id_itens = id_itens_receita;
    var url = "autorizador_alteracao.php?id_itens_receita="+id_itens+"&material="+mat_par;
	var height = 115;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog(url, dialogArguments, "dialogWidth=450px;dialogHeight=115px;dialogTop=350px;dialogLeft=280px;scroll=no;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameAutorizador(_R.id, id_itens_receita, material);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open(url, ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=no,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}

function SetNameAutorizador(id, id_itens_receita, material)
{
    var texto = "AUT_"+id_itens_receita+","+material;
    var x = document.getElementById(texto);
    x.value = id;

    var div = document.getElementById(material);
    
    if(id !='')
    {
       div.style.display = 'inline';
       var texto = "imgAutorizacao_"+material;
       var x = document.getElementById(texto);
       x.heigth = 0;
       x.width = 0;
    }
}

   function verificar_campos(){
      var ident = '';
      var item_id = '';
      var lote_id = '';
      var str = '';
      var valor_compara_ant = 0;
      var valor_compara = 0;
      var erro = 0 ;
      var vet_soma = new Array();
      var vet_dispensado = new Array();
      var vet_aut = new Array(20);
      var vet_lotes = new Array();

      //validar valor dispensado para o material
      document.form_alteracao.salvar.disabled = true;

      for (var i=0;i<document.form_alteracao.elements.length;i++){
         var k = 0;
         var x = document.form_alteracao.elements[i];

         if (x.name == 'lista_itens_receita[]'){
            var valores = x.value;
            var vet_valores = valores.split(",");
            var itens_rec = parseInt(vet_valores[0],10);
            var medic = parseInt(vet_valores[1],10);
            var prescritor = parseInt(vet_valores[2],10);
            var anterior = parseInt(vet_valores[3],10);
            var auxiliar = 'item'+itens_rec+','+medic;
            var dispensar ='';

            if(document.getElementById(auxiliar)){
               dispensar = document.getElementById(auxiliar).value;
            }

            if (((dispensar!='')&&(dispensar!=0))&&((prescritor-anterior)<(dispensar))){
               alert('Quantidade a dispensar inv·lida');
               document.getElementById(auxiliar).focus();
               document.form_alteracao.salvar.disabled = false;
               return false;
            }

            if (document.getElementById(medic)){
               if ((document.getElementById(medic).value!=0 )&& (document.getElementById(medic).value!="")){
                  if ((prescritor-anterior)!= 0){
                     vet_soma.push(prescritor - anterior);
                  }else{
                     vet_soma.push(0);
                  }
                  k+=1;
               }
            }
         }
      }

      for (var j=0;j<document.form_alteracao.elements.length;j++){
         var k = 0;
         var aux='F';
         var x = document.form_alteracao.elements[j];

         if (x.name == 'item[]'){
            var nome_item = x.id;
            var pos1 = nome_item.indexOf(",");
            var item_material =nome_item.substr(pos1+1);

            if (x.value != ''){
               for (var i=0;i<document.form_alteracao.elements.length;i++){
                   var k = 0;
                   var x = document.form_alteracao.elements[i];
                   var texto;
                   if (x.name == 'rec_controlada[]'){
                      texto = x.id;
                      var pos = texto.indexOf("_");
                      var item_controlado = texto.substr(pos+1);

                      if (item_controlado == item_material){
                         if ((document.getElementById(texto.substr(3)).value != 0)
                            && (document.getElementById(texto.substr(3)).value != "")){
                            if (x.value == ""){
                               alert ('Favor preencher campo obrigatÛrio!');
                               globalvar = x;
                               setTimeout("globalvar.focus()",250);
                               globalvar.focus();
                               document.form_alteracao.salvar.disabled = false;
                               return false
                            }
                         }
                      }
                   }
               }
            }
         }
      }

      for (var i=0;i<document.form_alteracao.elements.length;i++){
         var k = 0;
         var x = document.form_alteracao.elements[i];
         var texto;
         if (x.name == 'id_aut[]'){
            if (x.value == ""){
               texto = x.id;
               item_id = texto.substr(4);
               teste = 'item'+item_id;
               if ((document.getElementById(teste).value!='0')
                  && (document.getElementById(teste).value!='')){
                  alert('Material / Medicamento sem autorizaÁ„o');
                  document.form_alteracao.salvar.disabled = false;
                  return false
               }
            }
         }
      }

      var tam_vet=0;
      var texto1="";
      for (var i=0;i<document.form_alteracao.elements.length;i++){
         var k = 0;
         var x = document.form_alteracao.elements[i];
         if (x.name == 'lista_estoque[]'){
            var identificador = x.value;
            var vet1 = identificador.split(",");

            texto1 =  texto1 + vet1[2] + ",";
            tam_vet+=1;
         }
      }

      var vet_texto1 = texto1.split(",");

      var texto2 = "";
      for (var i=0;i<document.form_alteracao.elements.length;i++){
         var k = 0;
         var x = document.form_alteracao.elements[i];
         if (x.name == 'valor[]'){
            texto2 = texto2 + x.value + ",";
         }
      }

      var vet_texto2 = texto2.split(",");

      for (var i=0;i<tam_vet;i++){
         if (parseInt(vet_texto1[i],10) < parseInt(vet_texto2[i],10)){
            alert ("Quantidade a dispensar por lote È maior que a quantidade existente no lote!");
            document.form_alteracao.salvar.disabled = false;
            return
         }
      }

      //colhendo os valores a dispensar para cada item
      for (var i=0;i<document.form_alteracao.elements.length;i++){
         var x = document.form_alteracao.elements[i];
         if (x.name == 'item[]'){
            if (x.value == ''){
               vet_dispensado.push(0);
            }else{
               vet_dispensado.push(x.value);
            }
         }
      }

      for (var i=0;i<=vet_dispensado.length;i++){
         if (parseInt(vet_dispensado[i],10) > parseInt(vet_soma[i],10)){
            alert ("Valor a ser dispensado È maior que o valor prescrito!");
            document.form_alteracao.salvar.disabled = false;
            return false;
         }
      }

      var soma_itens = 0;
      var v_itens = new Array();
      var v_soma_itens = new Array();
      var ind_itens = 0;
      for (var i=0;i<document.form_alteracao.elements.length;i++){
         var x = document.form_alteracao.elements[i];
         if (x.name == 'item[]'){
            mat_item = x.id;
            mat_item = mat_item.substring(mat_item.indexOf(',')+1);
            if (x.value==''){
               v_itens[ind_itens] = 0;
            }else{
               v_itens[ind_itens] = parseInt(x.value);
            }
            v_soma_itens[ind_itens] = 0;

            for (var p=0;p<document.form_alteracao.elements.length;p++){
               var d = document.form_alteracao.elements[p];
               if (d.name == 'valor[]'){
                  mat_valor = d.id;
                  mat_valor = mat_valor.substring(mat_valor.indexOf(',')+1,mat_valor.indexOf('_'));
                  if (mat_valor == mat_item){
                     if (d.value==''){
                        v_soma_itens[ind_itens] += 0;
                     }else{
                        v_soma_itens[ind_itens] += parseInt(d.value);
                     }
                  }
               }
            }
            ind_itens ++;
         }
      }
      for (var s=0;s<v_itens.length;s++){
          if (parseInt(v_itens[s]) != (parseInt(v_soma_itens[s]))){
             alert('Soma das quantidades dos lotes deve ser igual a quantidade a dispensar!');
             document.form_alteracao.salvar.disabled = false;
             return false;
          }
      }
      salvar_completar();
   } //function
   
function precisa_autorizador()
{
    var url = "../../xml_dispensacao/precisa_autorizador.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true);
}

function trataDados()
{
     var retornoajax=ajax.responseText;  // obtÈm a resposta como string
     var pos= retornoajax.indexOf("-");
     var inicio_material= retornoajax.indexOf("*");
     var pos_material= retornoajax.indexOf("|");
     var e="Erro ";
     var id_movto_geral = retornoajax.substr(pos+1);

     //se no retorno ajax existir a string 'ERRO' - erro na inclus„o da receita
     var er=retornoajax.indexOf(e);

     if (er!=-1)
       {
        alert (retornoajax.substr(er));
        var id_material = retornoajax.substring(inicio_material+1,pos_material);
        document.form_alteracao.salvar.disabled = false;
        buscar_lote_altera(id_material);
       }
     else
     {

      var login_senha=retornoajax.split("@");
      if(login_senha[0]=="sim_login_senha_responsavel_dispensacao"){
        document.form_alteracao.id_login.value=login_senha[1];
        id_movto_geral='';
        verificar_campos();
      }
      
      if(login_senha[0]=="nao_login_senha_responsavel_dispensacao"){
        document.form_alteracao.id_login.value=login_senha[1];
        id_movto_geral = '';
        window.alert("Login e/ou Senha para DispensaÁ„o Inv·lidos!");
        document.form_alteracao.login.focus();
        return;
      }
      
     if (retornoajax=='duplicacao_usuario')
     {
           alert("Receita j· alterada na data de hoje! ");
           document.form_alteracao.salvar.disabled = false;
           document.form_alteracao.dados_salvar.value='';
           return;
     }

     if (retornoajax=='duplicacao_browser')
     {
           alert("Houve tentativa de reincidÍncia ao completar a receita. \n Verifique se esta operaÁ„o foi realizada com sucesso!");
           document.form_alteracao.salvar.disabled = false;
           document.form_alteracao.dados_salvar.value='';
           return

     }
     else if (id_movto_geral!='')
      {
        document.form_alteracao.id_movto_geral.value = id_movto_geral;
        document.form_alteracao.salvar.disabled = false;

        for (var i=0;i<document.form_alteracao.elements.length;i++)
        {
           var x = document.form_alteracao.elements[i];
           var aux_id = x.id;
           var sub_aux_id = aux_id.substr(0,aux_id.indexOf("_"));

           if (sub_aux_id == 'divlotes')
           {
             var desc = x.value;
             document.getElementById(desc).style.display = 'none';
             var texto = 'imagem_lote_'+desc;
             var elemento = document.getElementById(texto);
             elemento.heigth = 0;
             elemento.width = 0;
           }
        }
        imprimir_recibo();
        document.form_alteracao.salvar.disabled = true;

        document.form_alteracao.login.value="";
        document.form_alteracao.senha.value="";
        document.getElementById("mostrar_responsavel_dispensacao").style.display="none";
        
      }
     }
}


function montar_tabela()
 {
        var id_receita  = document.getElementById('id_receita').value;
        var id_paciente = document.getElementById('id_paciente').value;

        var aux_ano  = document.getElementById('aux_ano').value;
        var aux_unid = document.getElementById('aux_unidade').value;
        var aux_num  = document.getElementById('aux_num').value;
        var num_doc = aux_ano+"-"+aux_unid+"-"+aux_num;

        var separador = '';
        var aux_estoque = '';
        var campo_estoque = '';
        var num_estoque = '';
        var num_material = '';
        var id_estoque = '';
        var qtde = '';
        var aux_qtde ='';
        var val_qtde = '';
        var dispens = '';
        var aux_disp ='';
        var disp_qtd = '';

        var id_receita  = document.getElementById('id_receita').value;
        var id_paciente = document.getElementById('id_paciente').value;

        var aux_ano  = document.getElementById('aux_ano').value;
        var aux_unid = document.getElementById('aux_unidade').value;
        var aux_num  = document.getElementById('aux_num').value;
        var num_doc = aux_ano+"-"+aux_unid+"-"+aux_num;

        var tam_valores=0;
        for (var i=0;i<document.form_alteracao.elements.length;i++)
        {
         var x = document.form_alteracao.elements[i];
         var texto = x.name;
         if (texto.substring(0,11) == 'lista_itens')
         {
          tam_valores++;
         }
        }

        var tam_valor_est=0;
        for (var i=0;i<document.form_alteracao.elements.length;i++)
        {
         var x = document.form_alteracao.elements[i];

         if (x.name == 'valor[]')
         {
          tam_valor_est++;
         }
        }

        var vetItens = new Array(tam_valores);
        var vetLotes = new Array(tam_valor_est);
        var valores = new Array(tam_valores);
        var valor_est = new Array(tam_valor_est);

        var pos = 0;
        var posicao = 0;
        var conta = 0;
        var aux = 0;

        for (var w=0;w<document.form_alteracao.elements.length;w++)
        {
          var verifica = document.form_alteracao.elements[w];
          var itens = verifica.id;
          var aux_id_material = itens.substring(itens.indexOf(','), itens.length);
          var aux_id_itens = itens.substring(11, itens.indexOf(',')); //id_itens_receita

          var sub_itens = itens.substring(0,11);
          var valor = itens.substring(0,3);
          var aux_qtde = 'item'+aux_id_itens;


          if(sub_itens=='lista_itens')
          {
              var val_itens = document.getElementById(itens).value;
              aux_qtde = aux_qtde+aux_id_material;

              var achou = false;
              for (var contador=0;contador<document.form_alteracao.elements.length;contador++)
              {
                 var elem_aux = document.form_alteracao.elements[contador];
                 if (aux_qtde==elem_aux.id)
                 {
                    achou=true;
                 }
              }
              if (achou)
              {
                 vetItens[pos]=val_itens+','+document.getElementById(aux_qtde).value;
              }
              else vetItens[pos]=val_itens+',0';
              pos++;
          }

          if (valor == 'est')
          {
           var aux_id_material = itens.substring(itens.indexOf(',')+1, itens.length); //id_material

           if  ((aux== 0) ||(aux_id_material == aux))
           {
              var aux_id_estoque = itens.substring(3,itens.indexOf(',')); //id_esotque
              var aux_qtde_lote  = 'val'+aux_id_estoque+','+aux_id_material+'_'+conta;
              var val_est = document.getElementById(itens).value;

              for (var contador=0;contador<document.form_alteracao.elements.length;contador++)
              {
                var elem_aux = document.form_alteracao.elements[contador];
                if (aux_qtde_lote == elem_aux.id)
                {
                  vetLotes[posicao] = val_est+','+document.getElementById(aux_qtde_lote).value;
                  break;
                }
              }
              conta++;
           }
           else
           {
               conta = 0;
               var aux_id_estoque = itens.substring(3,itens.indexOf(',')); //id_esotque
               var aux_qtde_lote  = 'val'+aux_id_estoque+','+aux_id_material+'_'+conta;
               var val_est = document.getElementById(itens).value;

               for (var contador=0;contador<document.form_alteracao.elements.length;contador++)
               {
                  var elem_aux = document.form_alteracao.elements[contador];
                  if (aux_qtde_lote == elem_aux.id)
                  {
                     vetLotes[posicao] = val_est+','+document.getElementById(aux_qtde_lote).value;
                     break;
                  }
              }
              conta++;
           }
           aux = aux_id_material;
           posicao++;
         }
        }

        //verificar qtde de itens <> 0
        for(pos=0;pos<vetItens.length;pos++)
        {
          valores[pos] = vetItens[pos].split(',');
          var aux_aut_usuario = 'AUT_'+valores[pos][0]+','+valores[pos][1];

          for (var i=0;i<document.form_alteracao.elements.length;i++)
          {
           var k = 0;

           var x = document.form_alteracao.elements[i];
           var qtde_total = 'item'+valores[pos][0]+','+valores[pos][1];

           if (x.id == qtde_total)
           {
               valores[pos][7] = document.getElementById(qtde_total).value;
           }
          }
          valores[pos][8] = document.getElementById(aux_aut_usuario).value;
               //0-id_itens_receita
               //1-id_material
               //2-prescrita
               //3-anterior
               //4-flg autorizacao
               //5-dias limite
               //6-rc_controle
               //7-qtde a dispensar
               //8-autorizador
        }

        //alert (vetLotes.length);

        for(posicao=0;posicao<vetLotes.length;posicao++)
        {
            valor_est[posicao] = vetLotes[posicao].split(',');
               //0-id_material
               //1-id_estoque
               //2-quantidade em estoque
               //3-fabricante
               //4-lote
               //5-validade
               //6-qtde a dispensar por lote
        }

        // --------- montando a tabela -----
        for(v=0;v<valores.length;v++)
        {
          for(u=0;u<valor_est.length;u++)
          {
              if(valores[v][1]==valor_est[u][0])
              {
                if ((valor_est[u][6] != '') && (valor_est[u][6] != '0'))
                {
                 var h_pos = document.getElementById('hidden_lista').rows.length;
                 var tab = document.getElementById("hidden_lista").insertRow(h_pos);
                 tab.id = "linha"+h_pos;
                 tab.className = "descricao_campo_tabela";

                 var a = tab.insertCell(0);   //id_receita
                 var b = tab.insertCell(1);    //id_paciente
                 var c = tab.insertCell(2);    //num_doc
                 var d = tab.insertCell(3);    //id_itens_receita
                 var e = tab.insertCell(4);    //id_estoque
                 var f = tab.insertCell(5);    //id_material
                 var g = tab.insertCell(6);    //rec_controlada
                 var h = tab.insertCell(7);    //qtde_prescrita
                 var i = tab.insertCell(8);    //qtde_anterior
                 var j = tab.insertCell(9);   //flg_autorizacao
                 var k = tab.insertCell(10);   //qtd_total
                 var l = tab.insertCell(11);   //qtd_lote
                 var m = tab.insertCell(12);   //autorizador

                 a.innerHTML = id_receita;
                 b.innerHTML = id_paciente;
                 c.innerHTML = num_doc;
                 d.innerHTML = valores[v][0];
                 e.innerHTML = valor_est[u][1];
                 f.innerHTML = valores[v][1];
                 g.innerHTML = valores[v][6];     //rec_controlada
                 h.innerHTML = valores[v][2];
                 i.innerHTML = valores[v][3];
                 j.innerHTML = valores[v][4];    //flg
                 k.innerHTML = valores[v][7];
                 l.innerHTML = valor_est[u][6];
                 m.innerHTML = valores[v][8];    //autorizador
                 
                if (g.innerHTML=="")
                {
                 for (var i=0;i<document.form_alteracao.elements.length;i++)
                 {
                     var x = document.form_alteracao.elements[i];
                     var rec_controlada = 'NR_'+valores[v][1];

                     var valor_rec_cont='';
                     if (x.id == rec_controlada)
                     {
                        valor_rec_cont= document.getElementById(rec_controlada).value;
                        break;
                     }
                 }
                     g.innerHTML = valor_rec_cont;    //rec_controlada
                 }

                }
              }
           }
        }

  }

  function mostraResposta(resposta)
  {
     var div = document.getElementById("hidden_lista_dispensados");
     div.innerHTML = resposta.responseText;
  }


function acertar_dados_salvar()
{
   var h_pos = document.getElementById('hidden_lista').rows.length;
   if (h_pos > 1)
   {
       for(var i=h_pos-1;i>0 ;i--)
       {
          document.getElementById("hidden_lista").deleteRow(i);
       }
   }

   montar_tabela();
   var itens=document.getElementById("hidden_lista");
   var total_linhas=itens.rows.length;
   var lista=new Array(total_linhas);
  for(var i=1; i<lista.length; i++)
  {
    lista[i]=new Array(12);
  }

  var info='';
  for(var i=1; i<lista.length; i++)
  {
   for(var j=0; j<=lista[i].length; j++)
   {
           info+=itens.rows[i].cells[j].innerHTML + ',';
   }
   info = info.substr(0,(info.length)-1);
   info=info + '|';
  }
  info = info.substr(0,(info.length)-1);

  document.getElementById('dados_salvar').value=info;

}

function salvar_completar()
{

  acertar_dados_salvar();
  var dados_salvar = document.getElementById('dados_salvar').value;
  var nome = document.getElementById('nome').value;
  var qtos_itens =0;
  var num_controle = document.getElementById('num_controle').value;
  

  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var x = document.form_alteracao.elements[i];
   if (x.name == 'lista_itens_receita[]')
   {
      var valores = x.value;
      var vet_valores = valores.split(",");
      var itens_rec = parseInt(vet_valores[0],10);
      var medic = parseInt(vet_valores[1],10);
      var auxiliar = 'item'+itens_rec+','+medic;
//  alert(document.getElementById(auxiliar).value);
      if (document.getElementById(auxiliar))
      {
          if ((document.getElementById(auxiliar).value!=0) && (document.getElementById(auxiliar).value!=''))
          {
              qtos_itens++;
          }
      }
   }
 }
  if (qtos_itens==0)
  {
      alert("Nenhum material / medicamento foi dispensado.");
      document.form_alteracao.salvar.disabled = false;
      document.form_alteracao.dados_salvar.value='';
      return;
  }
  else
  {
   var id_login=document.form_alteracao.id_login.value;
   var url = "../../xml_dispensacao/salva_altera_receita.php?dados_salvar="+dados_salvar+"&nome="+nome+"&itens="+qtos_itens+"&num_controle="+num_controle+"&id_login="+id_login;
   requisicaoHTTP("GET", url, true);
  }
//  alert(url);
}

function imprimir_recibo()
  {
   var url;

   url = "<?= URL?>/modulos/consulta/recibo_receita_imp.php?id_receita=<?=$id_receita?>&id_movto_geral="+document.form_alteracao.id_movto_geral.value;
   if (confirm("Deseja imprimir recibo?")){
       window.open(url,target="_blank");
       /*"toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=0,width=850,height=500,scrollbars=1,top=100,left=100"*/
    }
  }
</script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0" height="100%" >
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>

      <tr>
        <td align="left" valign="top">
          <form name="form_alteracao" enctype="application/x-www-form-urlencoded">
          <table width="100%" cellpadding="0" cellspacing="1" height="50%" border='0'>
            <tr>
                <td colspan=6>
                <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr class="titulo_tabela">
                      <td colspan="6" valign="middle" align="center" width="100%" height="21"> Receita </td>
                  </tr>
                </table>
                </td>
            </tr>

            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Ano
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="hidden" id="num_controle" name="num_controle" value="<?php echo $num_controle;?>">
               <input type="hidden" id="id_receita" name="id_receita" value="<?php echo $id_receita;?>">
               <input type="hidden" name="id_movto_geral" id="id_movto_geral">
              <input type="hidden" name="aux_ano" id="aux_ano" value="<?php echo $ano;?>">
              <input type="text" name="ano" size="10"  maxlength="4" value="<?php echo $ano;?>" disabled>
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Unidade
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="hidden" id="aux_unidade" name="aux_unidade" value="<?php echo $unidade;?>">
               <input type="text" name="codigo_unidade" size="10" maxlength="10" value="<?php echo $unidade;?>" disabled>
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>N˙mero
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="hidden" id="aux_num" name="aux_num" value="<?php echo $numero;?>">
               <input type="text" name="numero" size="5" maxlength="10" value="<?php echo $numero;?>" disabled>
              </td>
            </tr>
            
            <tr>
             <td class="descricao_campo_tabela" valign="middle">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Data Emiss„o
             </td>
             <td class="campo_tabela" valign="middle">
               <input type="text" name="data_emissao" size="15" value="<?php echo $data_emissao;?>" disabled>
             </td>
             <td colspan="1" class="descricao_campo_tabela" valign="middle">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Origem
             </td>
             <td colspan="3" class="campo_tabela" valign="middle">
               <input type="text" name="nomeorigem" size="40" value="<?php echo $nomeorigem;?>" disabled>
             </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Cidade
             </td>
             <td colspan="5" class="campo_tabela" valign="middle">
               <input type="text" name="nomecidadereceita" size="40" value="<?php echo $nomecidadereceita;?>" disabled>
             </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Paciente
             </td>
             <td colspan="5" class="campo_tabela" valign="middle">
              <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $id_paciente;?>">
              <input type="text" name="nome" id="nome" size="70"  maxlength="70" value="<?php echo $nome;?>" disabled>
             </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Prescritor
             </td>
             <td colspan="5" class="campo_tabela" valign="middle">
              <input type="text" name="nomeprescritor" size="70"  maxlength="70" value="<?php echo $nomeprescritor;?>" disabled>

             </td>
            </tr>

			<tr>
				<td colspan="6">
                    <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">
                        <tr class="titulo_tabela" height='20'>
                            <td colspan='10' valign="middle" align="center" width="100%"> Materiais / Medicamentos Dispensados </td>
                        </tr>

                        <tr class="coluna_tabela">
                            <td  colspan='3' align='center' width='30%'>
                            Material / Medicamento
                            </td>
                            <td align='center' width='5%'>
                            Ult. Disp.
                            </td>
                            <td align='center' width='8%'>
                            N. NotificaÁ„o
                            </td>
                            <td align='center' width='8%'>
                            Qtde. Prescrita
                            </td>
                            <td align='center' width='8%'>
                            Tempo Tratamento
                            </td>
                            <td align='center' width='8%'>
                            Qtde. Disp. Anterior
                            </td>
                            <td align='center' width='8%'>
                            Qtde. a Dispensar
                            </td>
                            <td align='center' width='3%'>
                            </td>
						</tr>
                       <?php
                          $sql = "select ir.id_itens_receita,
                                         ir.material_id_material,
                                         ir.receita_id_receita,
                                         ir.qtde_prescrita,
                                         ir.tempo_tratamento,
                                         ir.qtde_disp_anterior,
                                         ir.qtde_disp_mes,
                                         ir.data_ult_disp,
                                         ir.num_receita_controlada,
										 ir.status,
                                         ma.descricao, ma.flg_autorizacao_disp, ma.dias_limite_disp
                                  from
                                         itens_receita ir,
                                         material ma
                                  where
                                         ir.receita_id_receita = '$id_receita'
                                         and ir.material_id_material = ma.id_material
                                  order by
                                         descricao";
                         //echo "*".$sql;
                         $item = mysqli_query($db, $sql);
                        // echo $sql;
                        // echo exit;
                         while ($dados_item = mysqli_fetch_object($item))
                         {
                          $id_material      = $dados_item->material_id_material;
                          $data_ult_disp    = $dados_item->data_ult_disp;
                          
                          if ($data_ult_disp!="0000-00-00 00:00:00")
                          {
                           $data_ult_disp    = substr($data_ult_disp,8,2)."/".substr($data_ult_disp,5,2)."/".substr($data_ult_disp,0,4);
                          }
                          else
                          {
                           $data_ult_disp    = "--";
                          }
                          $id_itens_receita = $dados_item->id_itens_receita;

                          $nec_autorizacao =  $dados_item->flg_autorizacao_disp;
                          
                          $nr_controlada    = $dados_item->num_receita_controlada;
                          
                          $prescrita        = intval($dados_item->qtde_prescrita);
                          
                          $tempo            = intval($dados_item->tempo_tratamento);
                          
                          $anterior         = intval($dados_item->qtde_disp_anterior)+intval($dados_item->qtde_disp_mes);

                          if ($tempo==0 or $tempo=='')
                          {?>
                           <script language="javascript" >
                              alert ('Receita incluÌda com Tempo de Tratamento inv·lido!') ;
                           </script>
                          <?
                            //$dispensar = 0;
                            exit;
                          }
                          else
                          {

                          //********************
                          $dispensar        = intval(($prescrita/$tempo)*30);
                          //********************
                          }
                          $dias_limite      = $dados_item->dias_limite_disp;

                          if ($dispensar<=$prescrita)
                          {
                               $dispensar = $dispensar;
                          }
                          else
                          {
                               $dispensar = $prescrita;
                          }
                          
                          $saldo = ($prescrita  - $anterior);
                          
                          if (($saldo/2)<$dispensar)
                          {
                           $dispensar = $saldo;
                          }
                          else
                          {
                           $dispensar = $dispensar;
                          }

                          if ($dias_limite_disp!=0 and $dias_limite_disp!="")
                          {
                           $data_limite_restricao = somadata($data_emissao, (int)$dias_limite_disp-1);

                           $data_limite_restricao = substr($data_limite_restricao,-4)."-".substr($data_limite_restricao,3,2)."-".substr($data_limite_restricao,0,2);

                           if ((date('Y-m-d',strtotime($data_limite_restricao)) < date('Y-m-d')))
                           {
                            $data_vencida = "S";
                           }
                           else
                           {
                            $data_vencida = "N";
                           }
                          }
                          $sql = "select e.id_estoque,
                                         e.fabricante_id_fabricante,
                                         e.material_id_material,
                                         e.unidade_id_unidade,
                                         e.lote,
                                         e.validade,
                                         e.quantidade,
                                         e.flg_bloqueado,
                                         e.motivo_bloqueio,
                                         f.descricao
                                  from
                                         estoque e,
                                         fabricante f
                                  where
                                         e.material_id_material = '$dados_item->material_id_material'
                                         and e.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                                         and e.quantidade > 0
                                         and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                                         and e.validade >'".date("Y-m-d")."'
                                         and e.fabricante_id_fabricante = f.id_fabricante
                                  order by
                                         e.validade asc";
                             //echo $sql;
                             //echo exit;
                            $lote = mysqli_query($db, $sql);
                            if ((mysqli_num_rows($lote)>0))
                            {
                             $tem_estoque = "S";
                            }
                            else
                            {
                             $tem_estoque = "N";
                            }
                         ?>
                         <tr height='20'class="linha_tabela" >
                            <?php
                            if (($nec_autorizacao=='S' or $data_vencida=='S') and ($tem_estoque=='S') and ($prescrita > $anterior))
                            {?>
                             <td bgcolor="#D8DDE3" colspan='3' align="left">
                              <input type="hidden" size="20" name="id_aut[]" id="<?php echo 'AUT_'.$dados_item->id_itens_receita.','.$dados_item->material_id_material;?>" >
                              <input type="hidden" size="10" id="<?php echo 'lista_itens'.$id_itens_receita.','.$id_material;?>" name="lista_itens_receita[]" value="<?php echo $id_itens_receita;?>,<?php echo $id_material;?>,<?php echo $prescrita;?>,<?php echo $anterior;?>,<?php echo $nec_autorizacao;?>,<?php echo $dias_limite;?>,<?php echo $nr_controlada;?>">
                              <A HREF=JavaScript:window.popup_autorizador('<?php echo $dados_item->id_itens_receita;?>','<?php echo $dados_item->material_id_material;?>');><IMG SRC="<?php echo URL. '/imagens/mini_cadeado_red2.gif'; ?>" id="<?php echo 'imgAutorizacao_'.$dados_item->material_id_material;?>" border="0" title="Precisa de AutorizaÁ„o"></A>
                              <?php echo $dados_item->descricao;?>
                             </td>
                            <?php
                            }
                            else
                            {?>
                            <td bgcolor="#D8DDE3" colspan='3' align="left">
                            <input type="hidden" size="20" name="id_aut[]" id="<?php echo 'AUT_'.$dados_item->id_itens_receita.','.$dados_item->material_id_material;?>" value="0">
                            <input type="hidden" size="10" id="<?php echo 'lista_itens'.$id_itens_receita.','.$id_material;?>" name="lista_itens_receita[]" value="<?php echo $id_itens_receita;?>,<?php echo $id_material;?>,<?php echo $prescrita;?>,<?php echo $anterior;?>,<?php echo $nec_autorizacao;?>,<?php echo $dias_limite;?>,<?php echo $nr_controlada;?>"><?php echo $dados_item->descricao;?>
                            </td>
                            <?php
                            }
                            ?>
                            <td bgcolor="#D8DDE3" align="center"><?php echo $data_ult_disp;?></td>
                        <?php
                         if ($nr_controlada!="")
                         {?>
                          <td bgcolor="#D8DDE3" align="center"><?php echo $nr_controlada;?></td>
                         <?}
                         else
                         {
                          $sql="select l.id_lista_especial,
                                       l.livro_id_livro,
                                       l.lista,
                                       l.descricao,
                                       l.status_2,
                                       l.date_incl,
                                       l.usua_incl,
                                       l.date_alt,
                                       l.usua_alt,
                                       l.flg_receita_controlada,
                                       l.flg_medicamento_controlado,
                                       m.id_material,
                                       m.unidade_material_id_unidade_material,
                                       m.grupo_id_grupo,
                                       m.subgrupo_id_subgrupo,
                                       m.tipo_material_id_tipo_material,
                                       m.familia_id_familia,
                                       m.lista_especial_id_lista_especial,
                                       m.codigo_material,
                                       m.descricao,
                                       m.flg_dispensavel,
                                       m.dias_limite_disp,
                                       m.status_2,
                                       m.flg_autorizacao_disp
                                from
                                       lista_especial l,
                                       material m
                                where
                                       m.id_material = '$id_material'
                                       and l.flg_receita_controlada like 'S'
                                       and m.lista_especial_id_lista_especial = l.id_lista_especial";
                          $sqllista = mysqli_query($db, $sql);
                          if (mysqli_num_rows($sqllista)==0)
                          {?>
                           <td bgcolor="#D8DDE3" align="center">--</td>
                          <?}
                          else
                          {
                           if ($prescrita == $anterior)
                           {?>
                            <td bgcolor="#D8DDE3" align="center"><?php echo $nr_controlada;?></td>
                           <?}
                           else
                           {
                            $sql = "select e.id_estoque,
                                           e.fabricante_id_fabricante,
                                           e.material_id_material,
                                           e.unidade_id_unidade,
                                           e.lote,
                                           e.validade,
                                           e.quantidade,
                                           e.flg_bloqueado,
                                           e.motivo_bloqueio,
                                           f.descricao
                                    from
                                           estoque e, fabricante f
                                    where
                                         e.material_id_material = '$dados_item->material_id_material'
                                         and e.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                                         and e.quantidade > 0
                                         and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                                         and e.validade >'".date("Y-m-d")."'
                                         and e.fabricante_id_fabricante = f.id_fabricante
                                    order by
                                         e.validade, e.lote asc ";
                             //echo $sql;
                             //echo exit;
                            $lote = mysqli_query($db, $sql);
                            if ((mysqli_num_rows($lote)>0))
                            {?>
                             <td bgcolor="#D8DDE3" align="center"><input type="text" size="10" maxlength="20" name="rec_controlada[]" id="<?php echo 'NR_'.$dados_item->material_id_material;?>" onBlur="validarNotificacao(<?php echo 'NR_'.$dados_item->material_id_material;?>)"></td>
                            <?}
                            else
                            {?>
                             <td bgcolor="#D8DDE3" align="center"></td>
                            <?}
                            }
                          }
                         }
                          ?>
                            <td bgcolor="#D8DDE3" align="right"><?php echo $prescrita;?></td>
                            <td bgcolor="#D8DDE3" align="right"><?php echo $tempo;?></td>
                            <td bgcolor="#D8DDE3" align="right"><?php echo $anterior;?></td>
                        <?php
                        $status_item = $dados_item->status;
						//para cada medicamento verifica se ainda posso dispensar
                        if ($prescrita == $anterior or $status_item == "FINALIZADO" )
                        {?>
                            <td bgcolor="#D8DDE3" align="right">0</td>
    						<td bgcolor="#D8DDE3" align="center"> </td>
                         </tr>
                      <?}
                        else
                        {
                         $sql ="SELECT tipo, id_estoque, lote, fabricante_id_fabricante,
                                       validade, quantidade, flg_bloqueado, descricao
                                FROM
                                    (select 'estoque_ok' as tipo, e.id_estoque, e.lote,
                                            e.fabricante_id_fabricante, e.validade,
                                            e.quantidade, e.flg_bloqueado, f.descricao
                                     from
                                            estoque e,
                                            fabricante f
                                     where
                                            material_id_material = $dados_item->material_id_material
                                            and e.fabricante_id_fabricante = f.id_fabricante
                                            and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                                            and e.quantidade > 0
                                            and e.validade >= now()
                                            and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                                     order by
                                            validade,
                                            lote
                                    ) as a
                                UNION ALL
                                SELECT tipo, id_estoque, lote, fabricante_id_fabricante,
                                       validade, quantidade, flg_bloqueado, descricao
                                FROM
                                    (select 'vencido' as tipo, e.id_estoque, e.lote,
                                            e.fabricante_id_fabricante, e.validade,
                                            e.quantidade, e.flg_bloqueado, f.descricao
                                     from
                                            estoque e,
                                            fabricante f
                                     where
                                            material_id_material = $dados_item->material_id_material
                                            and e.fabricante_id_fabricante = f.id_fabricante
                                            and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                                            and e.quantidade > 0
                                            and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                                            and e.validade < now()
                                     order by
                                            validade,
                                            lote
                                    ) as b
                                UNION ALL
                                SELECT tipo, id_estoque, lote, fabricante_id_fabricante,
                                       validade, quantidade, flg_bloqueado, descricao
                                FROM
                                    (select 'vencido_bloqueado' as tipo, e.id_estoque, e.lote,
                                            e.fabricante_id_fabricante, e.validade,
                                            e.quantidade, e.flg_bloqueado, f.descricao
                                     from
                                            estoque e,
                                            fabricante f
                                     where
                                            material_id_material = $dados_item->material_id_material
                                            and e.fabricante_id_fabricante = f.id_fabricante
                        and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                        and e.quantidade > 0
                        and e.flg_bloqueado = 'S'
                        and e.validade < now()
                        order by validade, lote
                        ) as c

                        union all

                        SELECT tipo, id_estoque, lote, fabricante_id_fabricante, validade, quantidade, flg_bloqueado, descricao FROM
                        (
                        select 'bloqueado' as tipo, e.id_estoque, e.lote, e.fabricante_id_fabricante, e.validade, e.quantidade, e.flg_bloqueado, f.descricao from estoque e, fabricante f
                        where material_id_material = $dados_item->material_id_material
                        and e.fabricante_id_fabricante = f.id_fabricante
                        and e.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                        and e.quantidade > 0
                        and e.flg_bloqueado = 'S'
                        and e.validade > now()
                        order by validade, lote
                        ) as d";

                         //echo $sql;
                         //echo exit;
                         $lote = mysqli_query($db, $sql);
                         $lote_aux = mysqli_query($db, $sql);
                         if ((mysqli_num_rows($lote)>0))
                         {
                         // verificar se existe algum lote com tipo = 'estoque_ok'
                            $tipo_estoque=0;
                            while ($dados_aux = mysqli_fetch_object($lote_aux))
                            {
                             if ($dados_aux->tipo == 'estoque_ok')
                             {
                              $tipo_estoque++;
                             }
                            }
                            if ($tipo_estoque<>0)
                            {
                         ?>
                            <td bgcolor="#D8DDE3" align="center"><input type="text" size="5" name="item[]" id="item<?php echo $dados_item->id_itens_receita.','.$dados_item->material_id_material;?>" value="<?php echo intval($dispensar);?>" onKeyPress='return numbers(event);'"></td>
                         <?
                            }
                            else
                            {
                         ?>
                            <td bgcolor="#D8DDE3" align="center"> <input type="text" size="5" name="item[]" id="item<?php echo $dados_item->id_itens_receita.','.$dados_item->material_id_material;?>" value="<?php echo intval($dispensar);?>" onKeyPress='return numbers(event);'"></td>
                         <?
                            }
                            if($nec_autorizacao=='S')
                            {
                         ?>
                              <td bgcolor="#D8DDE3" align="center" width='5%'><IMG SRC="<?php echo URL. '/imagens/folder_store.gif'; ?>" name="imagem_lote_<?php echo $dados_item->material_id_material;?>" id="imagem_lote_<?php echo $dados_item->material_id_material;?>" BORDER="0" TITLE="Exibir InformaÁıes de Lotes"></td>
                         <?
                            }
                            else
                            {
                         ?>
                             <td bgcolor="#D8DDE3" align="center" width='5%'><a href="JavaScript:showFrame('<?php echo $dados_item->material_id_material;?>');"> <IMG SRC="<?php echo URL. '/imagens/folder_store.gif'; ?>" name="imagem_lote_<?php echo $dados_item->material_id_material;?>" id="imagem_lote_<?php echo $dados_item->material_id_material;?>" BORDER="0" TITLE="Exibir InformaÁıes de Lotes"></a></td>
                         <? }

                         ?>
                            
                        </tr>
                        <tr>
                           <td colspan="10">
                           <input type="hidden" size="20" id="divlotes_<?php echo $dados_item->material_id_material;?>" value="<?php echo $dados_item->material_id_material;?>">
                              <div id="<?=$dados_item->material_id_material;?>" style="display:none;">
                                <table id="tabela1" bgcolor='#D8DDE3' width="100%" cellpadding="0" cellspacing="1" border="0">
                                 <tr class="coluna_tabela">
                                    <td align='center' width='10%'>
                                       Lote
                                    </td>
                                    <td align='center' width='46%'>
                                       Fabricante
                                    </td>
                                    <td align='center' width='14%'>
                                       Validade
                                    </td>
                                    <td align='center' width='10%'>
                                       Estoque
                                    </td>
                                    <td align='center' width='14%'>
                                       Qtde. a Dispensar
                                    </td>
                                 </tr>
                                 <?php
                                 $cont = 0;
                                 while ($dados_lote = mysqli_fetch_object($lote))
                                 {?>
                                 <tr class="linha_tabela" >
                                    <td bgcolor="#FFFFFF" align="left"><?php echo $dados_lote->lote;?></td>
                                    <td bgcolor="#FFFFFF" align="left"><?php echo $dados_lote->descricao;?></td>
                                    <td bgcolor="#FFFFFF" align="center"><?php echo substr($dados_lote->validade,8,2)."/".substr($dados_lote->validade,5,2)."/".substr($dados_lote->validade,0,4);?></td>
                                    <td bgcolor="#FFFFFF" align="right"><?php echo intval($dados_lote->quantidade);?></td>
                                    <?
                                    if (($dados_lote->validade >= date("Y-m-d")) && ($dados_lote->flg_bloqueado == '' || $dados_lote->flg_bloqueado== 'N'))
                                    {
                                    ?>
                                     <td bgcolor="#FFFFFF" align="center"><input type="text" size="5" name="valor[]" id="val<?php echo $dados_lote->id_estoque;?>,<?php echo $dados_item->material_id_material.'_'.$cont;?>" onKeyPress='return numbers(event);'>
                                     <input type="hidden" name="id_estoque" id="id_estoque<?php echo $dados_lote->id_estoque;?>" value="<?php echo $dados_lote->id_estoque;?>">
                                     <input type="hidden" size="10" id="est<?php echo $dados_lote->id_estoque;?>,<?php echo $dados_item->material_id_material;?>" name="lista_estoque[]" value="<?php echo $dados_item->material_id_material;?>,<?php echo $dados_lote->id_estoque;?>,<?php echo intval($dados_lote->quantidade);?>,<?php echo $dados_lote->fabricante_id_fabricante;?>,<?php echo $dados_lote->lote;?>,<?php echo $dados_lote->validade;?>">
                                     </td>
                                    <?
                                    }
                                    else if (($dados_lote->validade < date("Y-m-d")) && ($dados_lote->flg_bloqueado == 'S'))
                                    {
                                    ?>
                                     <td bgcolor='#FFFFFF' align='center'><IMG SRC="<?php echo URL. '/imagens/bolinhas/ball_vermelha.gif'; ?>" border='0' title='Lote Vencido e Bloqueado'></td>
                                    <?
                                    }
                                    else if (($dados_lote->validade < date("Y-m-d")) && ($dados_lote->flg_bloqueado == ''|| $dados_lote->flg_bloqueado == 'N'))
                                    {
                                    ?>
                                     <td bgcolor='#FFFFFF' align='center'><IMG SRC="<?php echo URL. '/imagens/bolinhas/ball_vermelha.gif'; ?>" border='0' title='Lote Vencido'></td>
                                    <?
                                    }
                                    else if (($dados_lote->validade >= date("Y-m-d")) && ($dados_lote->flg_bloqueado == 'S'))
                                    {
                                    ?>
                                     <td bgcolor='#FFFFFF' align='center'><IMG SRC="<?php echo URL. '/imagens/bolinhas/ball_vermelha.gif'; ?>" border='0' title='Lote Bloqueado'></td>
                                    <?
                                    }
                                    ?>
                                 </tr>
                                <?
                                  $cont++;
                                 }
                                ?>

                               </table>
                              </div>
                           </td>
                        </tr>
                        <?
                        }
                        else
                        {?>
                            <td bgcolor="#D8DDE3" align="center">--</td>
    						<td bgcolor="#D8DDE3" align="center"><IMG SRC="<?php echo URL. '/imagens/bolinhas/ball_amarela.gif'; ?>" BORDER="0" TITLE="sem quantidade em estoque"></td>
                         </tr>
                        <?}
                        }
                        }
                        ?>
					</table>
				</td>
			</tr>
            <tr>
            	<td colspan="6">
                     <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="10%" >
                       <tr>
                         <td width="83%" bgcolor="#D8DDE3">
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
                                  Dispensado por:
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
                                  <input type="password" name="senha" onblur="habilitaBotaoSalvar();" onfocus="desabilitaBotaoSalvar();">
                                </td>
                               </tr>
                             </table>
                           </div>
                         </td>
                         <td  align="right" bgcolor="#D8DDE3">
                            <input type="hidden" id="dados_salvar" name="dados_salvar">
                            <input style="font-size: 10px;" type="button" name="voltar" value="<< Voltar" onClick="javascript:history.go(-1)">
                            <?php
                            //echo $alteracao_perfil;
                            //echo exit;

                            if($alteracao_perfil!="")
                            {
                             if ($status_receita == "FINALIZADA")
                             {?>
                              <input style="font-size: 10px;" type="button" name="salvar" value="Salvar >>" onClick="verificar_campos()" disabled>
                             <!-- teste glaison
                              <input style="font-size: 10px;" type="button" name="Nova Receita" value="Salvar >>" onClick="verificar_campos()" disabled> -->
                             <?}
                             else
                             {
                               if($mostrar_responsavel_dispensacao=="S"){
                                 $desabilitado="disabled";
                               }
                               else{
                                 $desabilitado="";
                               }
                             ?>
                               <input style="font-size: 10px;" type="button" name="salvar" value="Salvar >>" onClick="salvarReceita();" <?php echo $desabilitado;?>>
                               <!-- teste
                               <input style="font-size: 10px;" type="button" name="Nova Receita" value="<< Nova Receita >>" onClick="verificar_campos()" disabled>  -->
                             <?}
                            }
                            else
                            {?>

                            <input style="font-size: 10px;" type="button" name="Nova Receita" value="Nova Receita >>" onClick="verificar_campos()" disabled>
                             <!-- teste

                              <input style="font-size: 10px;" type="button" name="salvar" value="Salvar >>" onClick="verificar_campos()" disabled> -->
                            <?}?>
                         </td>
                       </tr>
                     </table name='3'>
                     <input type="hidden" id="flag_mostrar_responsavel_dispensacao" name="flag_mostrar_responsavel_dispensacao" value="<?php echo $mostrar_responsavel_dispensacao;?>">
                        <!-- div escondida -->
                                 <div id="hidden_lista_dispensados" style="display:none;">
                                   <table id='hidden_lista' bgcolor='#D0D0D0' width='100%' cellpadding='0' cellspacing='1' border='0'>
                                          <tr bgcolor='#6B6C8F' class='coluna_tabela'>
                                            <td width='10%' align='center'>id_receita</td>
                                            <td width='10%' align='center'>id_paciente</td>
                                            <td width='10%' align='center'>num_doc</td>
                                            <td width='10%' align='center'>id_itens_receita</td>
                                            <td width='30%' align='center'>id_estoque</td>
                                            <td width='30%' align='center'>id_material</td>
                                            <td width='30%' align='center'>rec_controlada</td>
                                            <td width='30%' align='center'>qtde_prescrita</td>
                                            <td width='30%' align='center'>qtde_anterior</td>
                                            <td width='15%' align='center'>flg_autorizacao</td>
                                            <td width='15%' align='center'>qtd_total</td>
                                            <td width='15%' align='center'>qtd_lote</td>
                                            <td width='15%' align='center'>autorizador</td>
                                          </tr>
                                   </table>
                              </div>

                      <!-- div escondida -->
                 </td>
            </tr>
          </table>
          </form>
        </td>
      </tr>
    </table>
  <script>

  var existe_item ='f';
  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var x = document.form_alteracao.elements[i];
   if (x.name == 'item[]')
   {
     existe_item = 't';
     break;
   }
  }

  if(existe_item =='f')
  {
     document.form_alteracao.salvar.disabled= true;
  }

  </script>
<?php
    ////////////////////
    //RODAP… DA P¡GINA//
    ////////////////////
	
	$_SESSION ['id_paciente']= $id_paciente;  

    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE N√O ENCONTRAR ARQUIVO DE CONFIGURA«√O//
  ////////////////////////////////////////////

  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
