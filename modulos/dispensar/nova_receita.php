<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();
  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php"))
  {
    require "../../config/config.inc.php";
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////
    if($_SESSION[id_usuario_sistema]=='')
    {
      header("Location: ". URL."/start.php");
    }
    require DIR."/header.php";
    
    if(($_GET[num_inscricao]!="") and ($_GET[dispensacao] == "ok") and ($_GET[id_paciente] != ""))
    {
      header("Location: ". URL."/modulos/dispensar/nova_receita.php?dispensacao=$_GET[dispensacao]&id_paciente=$_GET[id_paciente]&id_prescritor=$_GET[id_prescritor]");
    }

    if($_GET[id_paciente]!="")
    {
     //busca dados do paciente
     $id_paciente = $_GET[id_paciente];
     
     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));
     
     $cartao_sus      = $dados_paciente->cartao_sus;
     $cartao_sus_prov = $dados_paciente->cartao_sus_prov;
     $nome            = $dados_paciente->nome;
     $data_nasc       = $dados_paciente->data_nasc;
     $data_nasc       = substr($data_nasc,-2)."/".substr($data_nasc,5,2)."/".substr($data_nasc,0,4);
     $sexo            = $dados_paciente->sexo;
     if ($sexo=='F')
     {
      $sexo = "FEMININO";
     }
     else
     {
      if ($sexo=='M')
      {
       $sexo = "MASCULINO";
      }
      else
      {
       $sexo='';
      }
     }
     
     $sql = "select id_cidade, concat(cid.nome,'/',est.uf) as nome
          from cidade cid, estado est, parametro par
          where cid.estado_id_estado = est.id_estado
          and cid.id_cidade = par.cidade_id_cidade";

     $dados_cidade_receita = mysqli_fetch_object(mysqli_query($db, $sql));
     $cidade_receita = $dados_cidade_receita->nome;
     $id_cidade_receita = $dados_cidade_receita->id_cidade;

    } //$_GET[id_paciente]!=""
    
    if($_POST[id_paciente]!="")
    {
     //busca dados do paciente
     $id_paciente = $_POST[id_paciente];
     
     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $cartao_sus      = $dados_paciente->cartao_sus;
     $cartao_sus_prov = $dados_paciente->cartao_sus_prov;
     $nome            = $dados_paciente->nome;
     $data_nasc       = $dados_paciente->data_nasc;
     $data_nasc       = substr($data_nasc,-2)."/".substr($data_nasc,5,2)."/".substr($data_nasc,0,4);
     $sexo            = $dados_paciente->sexo;
     if ($sexo=='F')
     {
      $sexo = "FEMININO";
     }
     else
     {
      if ($sexo=='M')
      {
       $sexo = "MASCULINO";
      }
      else
      {
       $sexo='';
      }
     }
    }//$_POST[id_paciente]!=""

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////

    //permissão de acesso para dispensar (nova receita)
    require "../../verifica_acesso.php";
    
    $sql = "select * from aplicacao where executavel = '/modulos/profissional/profissional_inicial.php' and status_2 = 'A'";
    //echo $sql;
    //echo exit;

    $res = mysqli_query($db, $sql);
    $res_profissional = mysqli_fetch_object($res);
    $id_aplicacao_profissional = $res_profissional->id_aplicacao;

    $sql = "select * from perfil_has_aplicacao where perfil_id_perfil = '$_SESSION[id_perfil_sistema]' and aplicacao_id_aplicacao = '$id_aplicacao_profissional'";
    //echo $sql;
    //echo exit;
    $acesso = mysqli_fetch_object(mysqli_query($db, $sql));

    $inclusao_perfil_profissional  = $acesso->inclusao;
    $alteracao_perfil_profissional = $acesso->alteracao;
    $exclusao_perfil_profissional  = $acesso->exclusao;
    $consulta_perfil_profissional  = $acesso->consulta;

    //caminho
    if ($_GET[aplicacao] <> '')
    {
      $_SESSION[cod_aplicacao] = $_GET[aplicacao];
    }
    require DIR."/buscar_aplic.php";
    

?>
<script>
function esconde_botao()
{
    to1 = document.getElementById('novareceita');
    to1.style.display = "none";
    to2 = document.getElementById('completarreceita');
    to2.style.display = "none";
}

function mostra_botao()
{
    to1 = document.getElementById('novareceita');
    to1.style.display = "block";
    to2 = document.getElementById('completarreceita');
    to2.style.display = "block";
}

var d = new Date();
var ID = d.getDate()+""+d.getMonth() + 1+""+d.getFullYear()+""+d.getHours()+""+d.getMinutes()+""+d.getSeconds();

function popup_cidade()
{
	var height = 350;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_cidade_dispensacao.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetName(_R.id, _R.strName);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_cidade_dispensacao.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}

function SetName(id, strName)
{
	document.form_inclusao.id_cidade_receita.value = id;
	document.form_inclusao.cidade_receita.value = strName;
}

function popup_autorizador()
{
	var height = 115;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("autorizador.php", dialogArguments, "dialogWidth=450px;dialogHeight=115px;scroll=no;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameAutorizador(_R.id);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("autorizador.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=no,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}

function SetNameAutorizador(id)
{
	document.form_inclusao.flg_autorizador.value = id;
}

function popup_prescritor()
{
	var height = 350;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_prescritor.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNamePrescritor(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_prescritor.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}

function SetNamePrescritor(argumentos)
{
    var valores = argumentos.split('|');

    document.form_inclusao.id_prescritor.value = valores[0];
    document.form_inclusao.id_tipo_prescritor.value = valores[1];
    document.form_inclusao.inscricao.value = valores[2];

    var codigo = valores[0]+'|'+valores[1]+'|'+valores[2];
    var descricao = valores[3];

    var sel = document.getElementById("prescritor");
    sel.options.length = 1;

    sel.options[1] = new Option(descricao, codigo);
    sel.selectedIndex = 1;
}

function popup_medicamento()
{
	var height = 350;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;

    limpar_receita_controlada();
    limpar_estoque();
    document.form_inclusao.medicamento01.value = '';
	document.form_inclusao.medicamento.value = '';
	document.form_inclusao.unidade.value = '';
	
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("pesquisa_material.php", dialogArguments, "dialogWidth=450px;dialogHeight=350px;scroll=yes;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameMedicamento(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("pesquisa_material.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=yes,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}

function SetNameMedicamento(argumentos)
{
    var valores = argumentos.split('|');

    document.form_inclusao.flg_material.value = '1';
    document.form_inclusao.medicamento.value = valores[0];
    document.form_inclusao.medicamento01.value = valores[1];
    document.form_inclusao.unidade.value = valores[2];
}

function popup_novo_prescritor()
{
	var height = 250;
	var width = 750;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog("profissional_inclusao_popup.php", dialogArguments, "dialogWidth=750px;dialogHeight=250px;scroll=no;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetName_NovoPrescritor(_R.strArgs);
		}
	}
	else	//NS
	{
		var left = (screen.width-width)/2;
		var top = (screen.height-height)/2;
 		var winHandle = window.open("profissional_inclusao_popup.php", ID, "modal,toolbar=false,location=false,directories=false,status=false,menubar=false,scrollbars=no,resizable=no,left="+left+",top="+top+",width="+width+",height="+height);
		winHandle.focus();
	}
	//return false;
}

function SetName_NovoPrescritor(argumentos)
{
    //alert (argumentos);
    var valores = argumentos.split('|');

    document.form_inclusao.id_prescritor.value = valores[0];
    document.form_inclusao.id_tipo_prescritor.value = valores[1];
    document.form_inclusao.inscricao.value = valores[2];

    var codigo = valores[0]+'|'+valores[1]+'|'+valores[2];
    var descricao = valores[3];

    var sel = document.getElementById("prescritor");
    sel.options.length = 1;

    sel.options[1] = new Option(descricao, codigo);
    sel.selectedIndex = 1;
}

function TrimJS(){

String = document.form_inclusao.medicamento01.value;
Resultado = String

//Retira os espaços do inicio
//Enquanto o primeiro caracter for igual à "Espaço"
//1 caracter do inicio é removido

var i
i = 0

if (Resultado.charCodeAt(2-1) == '32'){
}

while (Resultado.charCodeAt(0) == '32'){
   Resultado = String.substring(i,String.length);
  i++;}

//Pega a string já formatada e agora retira os espaços do final
//mesmo esquema, enquanto o ultimo caracter for um espaço,
//ele retira 1 caracter do final...

while(Resultado.charCodeAt(Resultado.length-1) == "32"){
   Resultado = Resultado.substring(0,Resultado.length-1);
  }

document.form_inclusao.medicamento01.value = Resultado;

String = ""

}

function imprimir_recibo()
{
   var url;

   url = "<?= URL?>/modulos/consulta/recibo_receita_imp.php?id_receita="+document.form_inclusao.id_receita.value;
   if (confirm("Deseja imprimir recibo?")){
       window.open(url,target="_blank");
       /*"toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=0,width=850,height=500,scrollbars=1,top=100,left=100"*/
    }
}

  function desabilitar_lixeira()
  {
    var linha = document.getElementById("lista_dispensados");
    total_linhas = document.getElementById("lista_dispensados").rows.length;
    for (i=1; i<total_linhas; i++)
    {
      linha.rows[i].cells[6].innerHTML = "";
    }

  }

  var cont = 0;
  function insere_linhas()
  {
      var med = document.form_inclusao.medicamento01.value;

      var achou_medicamento = false;

      document.getElementById("cabec_lista_dispensados").style.display = 'inline';
      //document.getElementById("hidden_lista_dispensados").style.display = 'inline';

      var itens = document.getElementById("lista_dispensados");
      total_linhas = document.getElementById("lista_dispensados").rows.length;

      //document.getElementById("hidden_lista_dispensados").style.display = 'inline';
      var h_itens = document.getElementById("hidden_lista");
      h_total_linhas = document.getElementById("hidden_lista").rows.length;

      for (i=1; i<total_linhas; i++)
      {
        if (itens.rows[i].cells[0].innerHTML == med)
        {
          achou_medicamento = true;
          break;
        }
      }

      if (!achou_medicamento)
      {
        var existe_estoque = false;
        var vai_dispensar = false;

        //verificar se existe estoque para o medicamento selecionado
        for (var i=0;i<document.form_inclusao.elements.length;i++)
        {
         var x = document.form_inclusao.elements[i];
         if (x.name == 'dispensar')
         {
          existe_estoque = true;
          break;
         }
        }

        if (existe_estoque)
        {
         //verificar se vai ser dispensada alguma qtde do medicamento
         for (var i=0;i<document.form_inclusao.elements.length;i++)
         {
          var x = document.form_inclusao.elements[i];
          if (x.name == 'dispensar')
          {
           if ((x.value != '')&&(x.value != '0'))
           {
            vai_dispensar = true;
            break
           }
          }
         }
        }

        if (vai_dispensar)
        {
         for (var i=0;i<document.form_inclusao.elements.length;i++)
         {
          var x = document.form_inclusao.elements[i];
          if (x.name == 'dispensar')
          {
           if ((x.value != '')&&(x.value != '0'))
           {

            var texto = x.id;
            var id_estoque = texto.substr(4,texto.length);

            var id_medicamento = document.form_inclusao.medicamento.value;

            var lote = document.getElementById('lot'+ id_estoque);
            var fabricante = document.getElementById('fab'+ id_estoque);
            var id_fabricante = document.getElementById('idf'+ id_estoque);
            var validade = document.getElementById('val'+ id_estoque);
            var qtde_lote = document.getElementById('disp'+ id_estoque);

            pos = document.getElementById('lista_dispensados').rows.length;
            var tab = document.getElementById("lista_dispensados").insertRow(pos);
            tab.id = "linha"+cont;
            tab.className = "descricao_campo_tabela";

            h_pos = document.getElementById('hidden_lista').rows.length;
            var h_tab = document.getElementById("hidden_lista").insertRow(h_pos);
            h_tab.id = "linha"+cont;
            h_tab.className = "descricao_campo_tabela";

            var a = tab.insertCell(0);   //medicamento
            var b = tab.insertCell(1);   //lote
            var c = tab.insertCell(2);   //fabricante
            var d = tab.insertCell(3);   //validade
            var e = tab.insertCell(4);   //qtde_dispensar
            var f = tab.insertCell(5);   //bolinha
            var g = tab.insertCell(6);   //lixo

            a.align = "left";
            b.align = "center";
            c.align = "left";
            d.align = "center";
            e.align = "right";
            f.align = "center";
            g.align = "center";

            a.innerHTML = document.form_inclusao.medicamento01.value;
            b.innerHTML = lote.value;

            var simbolo=/&/gi;
            var palavra=fabricante.value;
            palavra=palavra.replace(simbolo, '&amp;');

            //c.innerHTML = fabricante.value;
            c.innerHTML = palavra;
            d.innerHTML = validade.value;
            e.innerHTML = qtde_lote.value;

            var Site = "<img name='imagem_lixo' src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
            var url = "JavaScript:removeLinhas('linha"+cont+"')";

            g.innerHTML = Site.link(url);

            var h_a = h_tab.insertCell(0); //id_medicamento
            var h_b = h_tab.insertCell(1); //id_estoque
            var h_c = h_tab.insertCell(2); //lote
            var h_d = h_tab.insertCell(3); //id_fabricante
            var h_e = h_tab.insertCell(4); //validade
            var h_f = h_tab.insertCell(5); //qtde_lote
            var h_g = h_tab.insertCell(6); //qtde_prescrita
            var h_h = h_tab.insertCell(7); //tempo_tratamento
            var h_i = h_tab.insertCell(8); //qtde_anterior
            var h_j = h_tab.insertCell(9); //qtde_dispensada
            var h_k = h_tab.insertCell(10); //rec_controlada
            var h_l = h_tab.insertCell(11); //id_autorizador

            h_a.innerHTML = id_medicamento;

            h_b.innerHTML = id_estoque;
            h_c.innerHTML = lote.value;
            h_d.innerHTML = id_fabricante.value;
            h_e.innerHTML = validade.value;
            h_f.innerHTML = qtde_lote.value;
            h_g.innerHTML = document.form_inclusao.qtde_prescrita.value;
            h_h.innerHTML = document.form_inclusao.tempo_tratamento.value;
            h_i.innerHTML = document.form_inclusao.anterior.value;
            h_j.innerHTML = document.form_inclusao.qtde_dispensar.value;
            h_k.innerHTML = document.form_inclusao.rec_controlada.value;
            h_l.innerHTML = document.form_inclusao.flg_autorizador.value;

            cont++;


           }//x.value != '' && x.value != '0'

          }//name == 'dispensar'
         }//for
        }//vai_dispensar

        else

        { //não vai dispensar / não existe estoque

          pos = document.getElementById('lista_dispensados').rows.length;
          var tab = document.getElementById("lista_dispensados").insertRow(pos);
          tab.id = "linha"+cont;
          tab.className = "descricao_campo_tabela";

          h_pos = document.getElementById('hidden_lista').rows.length;
          var h_tab = document.getElementById("hidden_lista").insertRow(h_pos);
          h_tab.id = "linha"+cont;
          h_tab.className = "descricao_campo_tabela";

          var a = tab.insertCell(0);   //medicamento
          var b = tab.insertCell(1);   //lote
          var c = tab.insertCell(2);   //fabricante
          var d = tab.insertCell(3);   //validade
          var e = tab.insertCell(4);   //qtde_dispensar
          var f = tab.insertCell(5);   //bolinha
          var g = tab.insertCell(6);   //lixo

          a.align = "left";
          b.align = "center";
          c.align = "left";
          d.align = "center";
          e.align = "right";
          f.align = "center";
          g.align = "center";

          var h_a = h_tab.insertCell(0) ; //id_medicamento
          var h_b = h_tab.insertCell(1) ; //id_estoque
          var h_c = h_tab.insertCell(2) ; //lote
          var h_d = h_tab.insertCell(3) ; //id_fabricante
          var h_e = h_tab.insertCell(4) ; //validade
          var h_f = h_tab.insertCell(5) ; //qtde_lote
          var h_g = h_tab.insertCell(6) ; //qtde_prescrita
          var h_h = h_tab.insertCell(7) ; //tempo_tratamento
          var h_i = h_tab.insertCell(8) ; //qtde_anterior
          var h_j = h_tab.insertCell(9) ; //qtde_dispensada
          var h_k = h_tab.insertCell(10) ; //rec_controlada
          var h_l = h_tab.insertCell(11) ; //id_autorizador

          var id_medicamento = document.form_inclusao.medicamento.value;

          a.innerHTML = document.form_inclusao.medicamento01.value;
          b.innerHTML = '--';
          c.innerHTML = '--';
          d.innerHTML = '--';
          e.innerHTML = '0';

          var bolinha = "<img src='<?php echo URL;?>/imagens/bolinhas/ball_amarela.gif' border='0' alt='Sem estoque'>";

          f.innerHTML = bolinha;

          var Site = "<img name='imagem_lixo' src='<?php echo URL;?>/imagens/trash.gif' width='16' height='16' border='0' alt='Remover Registro'>";
          var url = "JavaScript:removeLinhas('linha"+cont+"')";

          g.innerHTML = Site.link(url);

          h_a.innerHTML = id_medicamento ;
          h_b.innerHTML = '0';
          h_c.innerHTML = '0';
          h_d.innerHTML = '0';
          h_e.innerHTML = '0';
          h_f.innerHTML = '0';
          h_g.innerHTML = document.form_inclusao.qtde_prescrita.value ;
          h_h.innerHTML = document.form_inclusao.tempo_tratamento.value ;
          h_i.innerHTML = document.form_inclusao.anterior.value ;
          h_j.innerHTML = '0' ;
          h_k.innerHTML = document.form_inclusao.rec_controlada.value ;
          h_l.innerHTML = document.form_inclusao.flg_autorizador.value ;

          cont++;

        }

        document.form_inclusao.salvar.disabled = false;
        limpar_campos();

      }
      else    //achou medicamento
      {
        alert('Medicamento já adicionado!')
        limpar_campos();
      }
  }

function removeLinhas(lnh)
{
  document.getElementById("lista_dispensados").deleteRow(document.getElementById(lnh).rowIndex);
  document.getElementById("hidden_lista").deleteRow(document.getElementById(lnh).rowIndex);
  if (document.getElementById('hidden_lista').rows.length==1)
  {
   document.getElementById("cabec_lista_dispensados").style.display = 'none';
   document.form_inclusao.salvar.disabled = true;
   document.form_inclusao.medicamento01.focus();
  }
}

function salvar_receita()
{
 if (document.form_inclusao.data_emissao.value == '')
 {
  alert ('Favor preencher os campos obrigatórios!');
  document.form_inclusao.data_emissao.focus();
  document.form_inclusao.data_emissao.select();
  return false;
 }

 if (document.form_inclusao.origem_receita.value == '')
 {
  alert ('Favor preencher os campos obrigatórios!');
  document.form_inclusao.origem_receita.focus();
  document.form_inclusao.origem_receita.select();
  return false;
 }

 if (document.form_inclusao.id_prescritor.value == '')
 {
  alert ('Favor preencher os campos obrigatórios!');
  document.form_inclusao.inscricao.focus();
  return false;
 }

 if (document.getElementById('hidden_lista').rows.length==1)
 {
  alert ('Pelo menos um medicamento deve ser adicionado a receita!');
  document.form_inclusao.medicamento01.focus();
  return false;
 }

 var data = document.form_inclusao.data_emissao.value;
 var erro= false;
 var dia, mes, ano;

 if (data.indexOf(" ")+data.indexOf(".")+data.indexOf("-")+data.indexOf("+")==-4)
 {
  dia = data.substring(0,2);
  if (dia.charAt(1)=="/")
  {
   data = "0" + data;
   dia  = dia.charAt(0);
  }
  if (!isNaN(dia) && dia>=1 && dia<=31)
   if (data.charAt(2)=="/")
   {
    mes = data.substring(3,5);
    if (mes.charAt(1)=="/")
    {
     data = data.substring(0,3) + "0" + data.substring(3,data.length);
     mes  = mes.charAt(0);
     erro = true;
    }
    if (!isNaN(mes) && mes>=1 && mes<=12)
     if (data.charAt(5)=="/")
     {
      ano = data.substring(6,data.length);
	  if (!isNaN(ano) && ano.length!=3)
	   erro = true;
	 }
   }
 }
 if (!erro)
 {
  alert ("A data fornecida foi preenchida incorretamente.");
  document.form_inclusao.data_emissao.focus();
  document.form_inclusao.data_emissao.select();
  return false;
 }
 else
 {
   ano = parseInt(ano,10);
   if (ano<4)
   {
    ano += 2000;
   }
   else
   {
    if (ano<100)
    {
     ano += 1900;
     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11))
     {
      alert ("Este mês não possui mais de 30 dias.");
      erro = true;
     }
	}
    else
    {
     if (dia > 30 && (mes==4 || mes==6 || mes==9 || mes==11))
     {
      alert ("Este mês não possui mais de 30 dias.");
      erro = false;
     }
     if (mes==2)
     {
      if (dia>29)
      {
       alert ("Fevereiro não pode conter mais de 29 dias.");
       erro = false;
      }
      else
       if (dia==29 && ano%4!=0)
       {
        alert ("Este não é um ano bissexto.");
        erro = false;
       }
     }
     if (!erro)
     {
      document.form_inclusao.data_emissao.focus();
      document.form_inclusao.data_emissao.select();
      return false;
     }
     else
     {
      document.form_inclusao.salvar.disabled = true;
      acertar_dados_salvar();
      valida_receita();
     }
    }
   }
  }
}

function acertar_dados_salvar()
{
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
   for(var j=0; j<lista[i].length; j++)
   {
           info+=itens.rows[i].cells[j].innerHTML + ',';
   }
   info = info.substr(0,(info.length)-1);
   info=info + '|';
  }
  info = info.substr(0,(info.length)-1);

  document.getElementById('itens_receita').value=info;

}

function limpar_campos()
{
 limpar_receita_controlada();
 limpar_estoque();

 document.form_inclusao.medicamento01.value='';
 document.form_inclusao.medicamento.value='';
 document.form_inclusao.unidade.value='';
 document.form_inclusao.flg_autorizador.value = '';

 document.form_inclusao.qtde_prescrita.value='';
 document.form_inclusao.tempo_tratamento.value='';
 document.form_inclusao.qtde_dispensar.value='';
 document.form_inclusao.anterior.value='0';

}

function monta_lista()
{
  insere_linhas();
}

function header_medicamentos_dispensados()
{
    var url = "../../xml_dispensacao/header_medicamentos_dispensados.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraHeader_medicamentos_dispensados
    });
}

function medicamentos_dispensados()
{
    var url = "../../xml_dispensacao/medicamentos_dispensados.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraMedicamentos_dispensados
    });
}

function mostraHeader_medicamentos_dispensados(cabec)
{
    var div = document.getElementById("cabec");
    div.innerHTML = cabec.responseText;
}

function mostraMedicamentos_dispensados(lista)
{
    var div = document.getElementById("lista");
    div.innerHTML = lista.responseText;

}

function receita_controlada()
{
    var url = "../../xml_dispensacao/receita_controlada.php?material="+document.form_inclusao.medicamento.value;
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraNRControlada
    });
}

function mostraNRControlada(controlada)
{
    var div = document.getElementById("controlada");
    div.innerHTML = controlada.responseText;
    if (document.form_inclusao.rec_controlada.type=='hidden')
    {
     if (document.form_inclusao.medicamento01.value=='')
     {
      document.form_inclusao.medicamento01.focus();
     }
     else
     {
      if (document.form_inclusao.medicamento01.value=='')
      {
       document.form_inclusao.medicamento01.focus();
      }
      else
      {
       document.form_inclusao.qtde_prescrita.focus();
      }
     }
    }
    else
    {
     document.form_inclusao.rec_controlada.focus();
    }
}

function limpar_receita_controlada()
{
    var url = "../../xml_dispensacao/limpar_receita_controlada.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraNRControlada
    });
}

function limpar_estoque()
{
    var url = "../../xml_dispensacao/limpar_estoque.php";
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraResposta
    });
}

function buscar_estoque()
{
    var url = "../../xml_dispensacao/buscar_estoque.php?material="+document.form_inclusao.medicamento.value;
    var pars = "";
    var myAjax = new Ajax.Request(url,{
        method: 'post',
        parameters: pars,
        onComplete: mostraResposta
    });
}

function mostraResposta(resposta)
{
    var div = document.getElementById("resposta");
    div.innerHTML = resposta.responseText;
}

function valida_receita()
{
    var tab=document.getElementById("hidden_lista");
    var total_itens=tab.rows.length-1;
    var itens = '';
    for (i=1; i<=total_itens; i++)
    {
      itens = itens + tab.rows[i].cells[0].innerHTML + '|';
    }
    itens = itens.substr(0,itens.length-1);

    var url = "../../xml_dispensacao/valida_receita.php?paciente="+document.form_inclusao.id_paciente.value
              +"&prescritor="+document.form_inclusao.id_prescritor.value
              +"&data="+document.form_inclusao.data_emissao.value
              +"&itens="+itens
              +"&total_itens="+total_itens;
    requisicaoHTTP("GET", url, true);
}

function procura_medicamento_nome()
{
    var url = "../../xml_dispensacao/procura_medicamento_nome.php?descricao="+document.form_inclusao.medicamento01.value;
    requisicaoHTTP("GET", url, true);
}

function valida_prescritor_medicamento()
{
    var url = "../../xml_dispensacao/valida_prescritor_medicamento.php?material="+document.form_inclusao.medicamento.value
              +"&tipo_prescritor="+document.form_inclusao.id_tipo_prescritor.value;
    requisicaoHTTP("GET", url, true);
}

function validade_receita()
{
    var url = "../../xml_dispensacao/validade_receita.php?material="+document.form_inclusao.medicamento.value
            +"&data="+document.form_inclusao.data_emissao.value;
    requisicaoHTTP("GET", url, true);
}

function autorizacao_receita_vencida()
{
    var url = "../../xml_dispensacao/autorizacao_receita_vencida.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true);
}

function precisa_autorizador()
{
    var url = "../../xml_dispensacao/precisa_autorizador.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true);
}

function precisa_autorizador_receita_vencida()
{
    var url = "../../xml_dispensacao/precisa_autorizador_receita_vencida.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true);
}

function proc_salvar_receita()
{
 var f = document.form_inclusao;
 var ano = f.ano_tela.value;
 var unidade = f.unidade_tela.value;
 var data_emissao = f.data_emissao.value;
 var origem = f.origem_receita.value;
 var cidade = f.id_cidade_receita.value;
 var paciente = f.id_paciente.value;
 var prescritor = f.id_prescritor.value;
 var itens_receita = f.itens_receita.value;

//  alert(itens_receita);
//  var texto = itens_receita;
//  alert(texto.length);

 var url = "../../xml_dispensacao/proc_salvar_receita.php?ano="+ano
           + "&unidade="+unidade
           + "&data_emissao="+ data_emissao
           + "&origem="+ origem
           + "&cidade="+ cidade
           + "&prescritor="+ prescritor
           + "&paciente="+ paciente
           + "&itens_receita="+ itens_receita;

 requisicaoHTTP("GET", url, true);
}

function trataDados()
{
	var info = ajax.responseText;  // obtém a resposta como string
    //alert (info);

    var variavel=info;
    v="RIS";
    e="Erro ";
    x=variavel.indexOf(v);
    er=variavel.indexOf(e);
    //alert(x);

   if ((x==-1)&&(er==-1))
   {
	if (info == 'nao_prescritor') //retorno de valida_prescritor_medicamento.php
	{
     limpar_receita_controlada();
     limpar_estoque();

	 alert('Material não pode ser dispensado por esse prescritor');
	 document.form_inclusao.medicamento01.value = '';
	 document.form_inclusao.medicamento.value = '';
	 document.form_inclusao.unidade.value = '';
	 document.form_inclusao.medicamento01.focus();
    }

    if (info == 'sim_prescritor')  //retorno de valida_prescritor_medicamento.php
    {
         validade_receita();
    }

    if (info == 'validade_expirou')
    {
	 if (!confirm('Receita com prazo de validade vencida. Deseja dispensar medicamento?'))
	 {
      limpar_receita_controlada();
      limpar_estoque();

	  document.form_inclusao.medicamento01.value = '';
	  document.form_inclusao.medicamento.value = '';
	  document.form_inclusao.unidade.value = '';
	  document.form_inclusao.medicamento01.focus();
     }
     else
     {
      autorizacao_receita_vencida();
     }
    }

    if (info == 'validade_no_prazo')
    {
        precisa_autorizador();
    }

    if (info == 'sem_estoque')
    {
        limpar_receita_controlada();
        limpar_estoque();

        alert('Receita com prazo de validade vencida e sem quantidade em estoque.')
	    document.form_inclusao.medicamento01.value = '';
	    document.form_inclusao.medicamento.value = '';
	    document.form_inclusao.unidade.value = '';
	    document.form_inclusao.medicamento01.focus();
    }

    if (info == 'com_estoque')
    {
        precisa_autorizador_receita_vencida();
    }

    if (info == 'sim_autorizador_sem_msg')
    {
     popup_autorizador ();
     //modalWinAutorizador('nova_receita', 0);
     if (document.form_inclusao.flg_autorizador.value!='')
     {
      receita_controlada();
      buscar_estoque();
     }
     else
     {
      limpar_receita_controlada();
      limpar_estoque();
      document.form_inclusao.medicamento01.value = '';
 	  document.form_inclusao.medicamento.value = '';
 	  document.form_inclusao.unidade.value = '';
	  document.form_inclusao.flg_autorizador.value = '';
	  document.form_inclusao.medicamento01.focus();
     }
    }

    if (info == 'nao_autorizador')
    {
         receita_controlada();
         buscar_estoque();
    }

    if (info == 'sim_autorizador')
    {
         if (confirm('Medicamento precisa ser autorizado. Deseja dispensar o medicamento?'))
         {
          popup_autorizador();
          //modalWinAutorizador('nova_receita',0);
          if (document.form_inclusao.flg_autorizador.value!='')
          {
           receita_controlada();
           buscar_estoque();
          }
          else
          {
           limpar_receita_controlada();
           limpar_estoque();
           document.form_inclusao.medicamento01.value = '';
 	       document.form_inclusao.medicamento.value = '';
 	       document.form_inclusao.unidade.value = '';
	       document.form_inclusao.flg_autorizador.value = '';
	       document.form_inclusao.medicamento01.focus();
          }
         }
         else
         {
          limpar_receita_controlada();
          limpar_estoque();
          document.form_inclusao.medicamento01.value = '';
	      document.form_inclusao.medicamento.value = '';
	      document.form_inclusao.unidade.value = '';
	      document.form_inclusao.flg_autorizador.value = '';
 	      document.form_inclusao.medicamento01.focus();
         }
    }

    if (info == 'receita_nao_existe')
    {
     proc_salvar_receita();
    }

    if (info == 'receita_existe_verificar')
    {
     if (confirm('Receita já foi incluída! Deseja incluir mesmo assim?'))
     {
      proc_salvar_receita();
     }
     else
     {
      document.form_inclusao.salvar.disabled = false;
     }
    }

    if (info == 'receita_existe')
    {
     alert('Receita já foi incluída!');

     document.form_inclusao.salvar.disabled = false;
    }

    if (info.substring(0,3) == 'med')
    {
     if (info.substring(3) == 'nao')
     {
       alert('Medicamento Inválido!');

       document.form_inclusao.medicamento01.value = ''
       document.form_inclusao.medicamento.value = '';
       document.form_inclusao.unidade.value = '';
       document.form_inclusao.qtde_prescrita.value = '';
       document.form_inclusao.tempo_tratamento.value = '';
       document.form_inclusao.anterior.value = '';
       document.form_inclusao.qtde_dispensar.value = '';
       document.form_inclusao.flg_autorizador.value = '';

       limpar_receita_controlada();
       limpar_estoque();

       document.form_inclusao.medicamento01.focus();
       document.form_inclusao.medicamento01.select();

     }
     else
     {
      var codigos = info.substring(3);
      var vet = new Array();
      vet = codigos.split('|');

      document.form_inclusao.medicamento.value = vet[0];
      document.form_inclusao.unidade.value = vet[1];

      document.form_inclusao.flg_autorizador.value = '';
      valida_prescritor_medicamento();
     }
    }

   }
   else
   {
    if ((x!=-1)&&(er==-1))
    {
     var retornoajax=info;
     var1="-";
     x=retornoajax.indexOf(var1);
     var2="|";
     y=retornoajax.indexOf(var2);
     //alert(x);

     //alert (retornoajax.substring(x+1,y));

     var pos_ris = retornoajax.indexOf("RIS-");
     var pos_ris = pos_ris + 4;
     //alert (pos_ris);

     alert('Operação concluída com sucesso! \n Receita número:'+ retornoajax.substring(pos_ris,y));

     //document.form_inclusao.numero_receita.value = retornoajax.substring(x+1,y);
     document.form_inclusao.numero_receita.value = retornoajax.substring(pos_ris,y);

     document.form_inclusao.id_receita.value = retornoajax.substring(y+1);
     document.form_inclusao.data_emissao.disabled = true;
     document.form_inclusao.origem_receita.disabled = true;
     document.form_inclusao.inscricao.disabled = true;
     document.form_inclusao.prescritor.disabled = true;
     document.form_inclusao.medicamento01.disabled = true;
     document.form_inclusao.qtde_prescrita.disabled = true;
     document.form_inclusao.tempo_tratamento.disabled = true;
     document.form_inclusao.qtde_dispensar.disabled = true;
     document.form_inclusao.imagem_cidade.style.visibility = 'hidden';
     document.form_inclusao.imagem_prescritor.style.visibility = 'hidden';
     document.form_inclusao.imagem_medicamento.style.visibility = 'hidden';
     desabilitar_lixeira();
     //document.form_inclusao.imagem_lixo.style.visibility = 'hidden';
     document.form_inclusao.novo.disabled = true;

     document.form_inclusao.adiciona.style.visibility = 'hidden';
     document.form_inclusao.salvar.style.visibility = 'hidden';

     mostra_botao();

     imprimir_recibo();
    }
    else
    {
     if (er!=-1)
     {
      alert (info);
      document.form_inclusao.salvar.disabled = false;
     }
    }
   }

}

function preenche_campos()
{
// alert(document.form_inclusao.prescritor.value);
 if (document.form_inclusao.prescritor.value!=0)
 {
  var str = document.form_inclusao.prescritor.value;
  var vet = new Array();
  vet = str.split('|');

  document.form_inclusao.id_prescritor.value = vet[0];
  document.form_inclusao.id_tipo_prescritor.value = vet[1];
 }
}

function buscar_inscricao()
{
 if (document.form_inclusao.prescritor.value!=0)
 {
  var str = document.form_inclusao.prescritor.value;
  var vet = new Array();
  vet = str.split('|');
  document.form_inclusao.id_prescritor.value = vet[0];
  document.form_inclusao.id_tipo_prescritor.value = vet[1];
  document.form_inclusao.inscricao.value = vet[2];

 }
 else
 {
  document.form_inclusao.prescritor.value = "";
  document.form_inclusao.inscricao.value = "";
  document.form_inclusao.id_prescritor.value = "";
  document.form_inclusao.id_tipo_prescritor.value = "";
  carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
 }
}

function retirar_brancos()
{
 if (document.form_inclusao.rec_controlada.type=="text")
 {
  var texto = document.form_inclusao.rec_controlada.value;
  var tam = texto.length;
  for (var i=0; i<tam; i++)
  {
   texto = texto.replace(" ", "");
  }
  document.form_inclusao.rec_controlada.value = texto;
  return
 }
}

function adicionar_medicamentos()
{
   var soma=0;
   var existe=false;

   //verificando se medicamento foi selecionado
   if ((document.form_inclusao.medicamento01.value == '') || (document.form_inclusao.medicamento.value == ''))
   {
      alert ("Medicamento Inválido!");

      document.form_inclusao.medicamento01.value = ''
      document.form_inclusao.medicamento.value = '';
      document.form_inclusao.unidade.value = '';
      document.form_inclusao.qtde_prescrita.value = '';
      document.form_inclusao.tempo_tratamento.value = '';
      document.form_inclusao.anterior.value = '';
      document.form_inclusao.qtde_dispensar.value = '';
      document.form_inclusao.flg_autorizador.value = '';

      limpar_receita_controlada();
      limpar_estoque();

      document.form_inclusao.medicamento01.focus();
      document.form_inclusao.medicamento01.select();
      return false;
   }

   //verificando se qtde_prescrita está preenchida
   if ((parseInt(document.form_inclusao.qtde_prescrita.value,10) == 0) || (document.form_inclusao.qtde_prescrita.value == ''))
   {
      alert ('Quantidade Prescrita deve ser informada!');
      document.form_inclusao.qtde_prescrita.focus();
      document.form_inclusao.qtde_prescrita.select();
      return false;
   }

   //verificando se tempo_tratamento está preenchido
   if ((parseInt(document.form_inclusao.tempo_tratamento.value,10) == 0) || (document.form_inclusao.tempo_tratamento.value == ''))
   {
      alert ('Tempo de Tratamento deve ser informado!');
      document.form_inclusao.tempo_tratamento.focus();
      document.form_inclusao.tempo_tratamento.select();
      return false;
   }

   //verificando se qtde_dispensar é menor ou igual que a qtde_prescrita
   if (parseInt(document.form_inclusao.qtde_dispensar.value,10) > parseInt(document.form_inclusao.qtde_prescrita.value,10))
   {
      alert ('Quantidade a dispensar é maior que a quantidade prescrita!');
      document.form_inclusao.qtde_dispensar.focus();
      document.form_inclusao.qtde_dispensar.select();
      return false;
   }

   //caso exista rec_controlada, verifica se está preenchida se o medicamento for dispensado
   if ((document.form_inclusao.rec_controlada.type=='text')
        && (parseInt(document.form_inclusao.qtde_dispensar.value,10)!=0)
        && ((document.form_inclusao.rec_controlada.value == '') || (document.form_inclusao.rec_controlada.value == '0')))
   {
      alert ('Número da Notificação deve ser informado!');
      document.form_inclusao.rec_controlada.focus();
      document.form_inclusao.rec_controlada.select();
      return false;
   }

   //verificando se existe estoque para o medicamento selecionado
   existe_estoque = false;
   for (var i=0;i<document.form_inclusao.elements.length;i++)
   {
     var x = document.form_inclusao.elements[i];
     if (x.name == 'estoque')
     {
      existe_estoque = true;
     }
   }

   //verificando se há lote vencido ou bloqueado
   estoque_blq_venc = false;
   for (var i=0;i<document.form_inclusao.elements.length;i++)
   {
     var x = document.form_inclusao.elements[i];
     if (x.name == 'dispensar')
     {
      estoque_blq_venc = true;
     }
   }

   //existindo estoque, verifica se soma das quantidade a dispensar dos estoques é igual a quantidade a dispensar
   if ((existe_estoque)  && (estoque_blq_venc))
   {
    var soma_estoque=0;
    for (var i=0;i<document.form_inclusao.elements.length;i++)
    {
     var x = document.form_inclusao.elements[i];
     if (x.name == 'dispensar')
     {
      if (x.value=='')
      {
       soma_estoque = soma_estoque + 0;
      }
      else
      {
       soma_estoque = soma_estoque + parseInt(x.value, 10)
      }
     }
    }

    if (soma_estoque > parseInt(document.form_inclusao.qtde_dispensar.value,10))
    {
     alert ('A soma dos lotes é maior que a quantidade escolhida para dispensar!');
     return false;
    }

    if (soma_estoque < parseInt(document.form_inclusao.qtde_dispensar.value,10))
    {
     alert ('A soma dos lotes é menor que a quantidade escolhida para dispensar!');
     return false;
    }
   }


   //verificar se quantidade existente em estoque <= quantidade a dispensar
   if (existe_estoque)
   {
     var tam_vet=0;
     var estoque='';

     for (var i=0;i<document.form_inclusao.elements.length;i++)
     {
      var x = document.form_inclusao.elements[i];
      if (x.name == 'estoque')
      {
       estoque = estoque + x.value + '|';
       tam_vet = tam_vet + 1;
      }
     }
     estoque = estoque.substr(0,estoque.length-1);
     var vet_estoque = estoque.split("|");

     var dispensar='';
     for (var i=0;i<document.form_inclusao.elements.length;i++)
     {
      var x = document.form_inclusao.elements[i];
      if (x.name == 'dispensar')
      {
       if (x.value=='')
       {
        dispensar = dispensar + '0|';
       }
       else
       {
        dispensar = dispensar + x.value + '|';
       }
      }
     }
     dispensar = dispensar.substr(0,dispensar.length-1);
     var vet_dispensar = dispensar.split("|");

     for (var i=0;i<tam_vet;i++)
     {
       if (parseInt(vet_estoque[i],10) < parseInt(vet_dispensar[i],10))
       {
        alert ("Quantidade a dispensar por lote é maior que a quantidade existente no lote!");
        return false;
       }
     }
   }
   return true;
}

function calcular_qtde_dispensar()
{

  var qtde_prescrita = document.form_inclusao.qtde_prescrita.value;
  var tempo_tratamento = document.form_inclusao.tempo_tratamento.value;
  var qtde_dispensar = 0;
  var saldo = 0;

  if ((qtde_prescrita!=0) || (tempo_tratamento!=0))
  {
    qtde_dispensar = ((qtde_prescrita/tempo_tratamento)*30);

    if (qtde_prescrita<qtde_dispensar)
    {
     document.form_inclusao.qtde_dispensar.value = Math.round(qtde_prescrita);
    }
    else
    {
     if ((qtde_dispensar > 0) && (qtde_dispensar < 1))
     {
      qtde_dispensar = 1;
     }
     document.form_inclusao.qtde_dispensar.value = Math.round(qtde_dispensar);
    }
    document.form_inclusao.qtde_dispensar.focus();
    document.form_inclusao.qtde_dispensar.select();
    return;
  }
}
</script>

<script language="JavaScript" type="text/javascript" src = "../../scripts/scripts.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/combo_dispensacao.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/prototype.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/prescritor_material.js"></script>
<script language="javascript" type="text/javascript" src = "../../scripts/auto_completar_dispensacao.js"></script>

    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>

      <tr>
        <td align="left">
          <body onload="esconde_botao();" onunload="if(winHandle && !winHandle.closed) winHandle.close();">
          <form name="form_inclusao">
          <input type="hidden" name="id_paciente" value="<?php echo $id_paciente;?>">
          <table width="100%" border="0" cellpadding="0" cellspacing="1">
            <tr class="titulo_tabela" height="21">
             <td colspan="6" valign="middle" align="center" width="100%"> Receita </td>
            </tr>

            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Ano
              </td>
              <input type="hidden" name="ano" size="10"  maxlength="4" value="<?php echo date('Y');?>">
              <td class="campo_tabela" valign="middle" width="15%">
              <input type="text" name="ano" size="10"  maxlength="4" value="<?php echo date('Y');?>" disabled>
              <input type="hidden" name="ano_tela" size="10"  maxlength="4" value="<?php echo date('Y');?>">
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Unidade
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="text" name="codigo_unidade" size="10" maxlength="10" value="<?php echo $_SESSION[id_unidade_sistema];?>" disabled>
               <input type="hidden" name="unidade_tela" size="10" maxlength="10" value="<?php echo $_SESSION[id_unidade_sistema];?>" disabled>
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Número
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="text" name="numero_receita" size="10"  maxlength="10" disabled>
               <input type="hidden" name="id_receita">
              </td>
            </tr>

            <tr>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Data Emissão
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
                  <input type="text" name="data_emissao" size="10"  maxlength="10" value="<?php if(isset($data_emissao)){echo $data_emissao;}else{ echo date('d/m/Y');} ?>" onblur="verificaData(this,this.value);" onKeyPress="return mascara_data(event,this);">
              </td>
              <td class="descricao_campo_tabela" valign="middle" width="15%">
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Origem
              </td>
              <td colspan="3" class="campo_tabela" valign="middle" width="20%">
                  <select size="1" name="origem_receita" style="width:200px;" >
                   <option value="">Selecione uma origem</option>
                  <?php
                      $sql = "select * from subgrupo_origem where status_2 = 'A' order by descricao";
                      $origem = mysqli_query($db, $sql);
                      while ($dadosorigem = mysqli_fetch_object($origem))
                      {
                      if ($origem_receita=="")
                      {
                      //selecionar como default local
                        if (trim($dadosorigem->descricao) == "LOCAL")
                        {
                        ?>
                          <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>" selected><?php echo $dadosorigem->descricao;?></option>
                        <?
                        }
                        else
                        {
                        ?>
                          <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>"><?php echo $dadosorigem->descricao;?></option>
                        <?
                        }
                      }
                      else
                      {
                       if($dadosorigem->id_subgrupo_origem == $origem_receita)
                       {
                       ?>
                         <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>" selected><?php echo $dadosorigem->descricao;?></option>
                     <?}
                       else
                       {?>
                         <option value="<?php echo $dadosorigem->id_subgrupo_origem;?>"><?php echo $dadosorigem->descricao;?></option>
                    <? }
                      }
                      }
                       ?>
                  </select>
              </td>
            </tr>
                     
            <tr>
                <td class="descricao_campo_tabela" valign="middle" width="10%">
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Cidade
                </td>
                <?
                $sql = "select c.id_cidade, concat(c.nome,'/',e.uf) as nome
                     from cidade c, estado e, parametro p
                     where p.cidade_id_cidade = c.id_cidade
                     and c.estado_id_estado = e.id_estado";

                 $dados_cidade_receita = mysqli_fetch_object(mysqli_query($db, $sql));
                 $cidade_receita = $dados_cidade_receita->nome;
                 $id_cidade_receita = $dados_cidade_receita->id_cidade;
                 ?>
                <td colspan="5" class="campo_tabela" valign="middle" width="35%">
                 <input type="text" size="30" name="cidade_receita" value="<?php echo $cidade_receita;?>" disabled>
                 <A HREF=JavaScript:window.popup_cidade();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_cidade" border="0" title="Pesquisar"></a>
                 <input type="hidden" name="id_cidade_receita" value="<?php echo $id_cidade_receita;?>">
                </td>
            </tr>

            <tr>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Paciente
             </td>
             <td colspan="5" class="campo_tabela" valign="middle" width="25%">
              <input type="text" name="nome" size="70"  maxlength="70" value="<?php echo $nome;?>" disabled>
             </td>
            </tr>

            <tr>
				<td colspan="6">
                    <table width="100%" cellpadding="0" cellspacing="1" border="0" height="100%">

             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>No. Inscrição
             </td>
             <?php
              if ($_GET[id_prescritor]!="")
              {
               $sql = " select p.id_profissional, p.tipo_prescritor_id_tipo_prescritor, p.inscricao, p.nome, e.uf
                     from profissional p, estado e
                     where p.id_profissional = $_GET[id_prescritor]
                     and p.estado_id_estado = e.id_estado
                     order by p.nome " ;
               $insc_aux = mysqli_query($db, $sql);
               $dados_insc = mysqli_fetch_object($insc_aux);
               
               $codigo = $dados_insc->codigo;
               $nome = $dados_insc->nome;
               //echo $codigo;
               //echo exit;
               $id_prescritor = $dados_insc->id_profissional;
               $id_tipo_prescritor = $dados_insc->tipo_prescritor_id_tipo_prescritor;
               $inscricao = $dados_insc->inscricao;
              }
             ?>
             <td class="campo_tabela" valign="middle" width="15%">
              <input type="text" name="inscricao" size="10"  maxlength="10" value="<?php if(isset($inscricao)){echo $inscricao;}?>"
                     onChange="if (this.value!='')
                               {
                                document.form_inclusao.id_prescritor.value='';
                                document.form_inclusao.id_tipo_prescritor.value='';
                                document.form_inclusao.prescritor.value='';
                                carregarCombo(this.value, '../../xml_dispensacao/prescritor_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
                               }
                               else
                               {
                                document.form_inclusao.id_prescritor.value='';
                                document.form_inclusao.id_tipo_prescritor.value='';
                                document.form_inclusao.prescritor.value='';
                                carregarCombo(<?php echo $_SESSION[id_unidade_sistema];?>, '../../xml_dispensacao/prescritor_unidade_ajax.php', 'lista_profissional', 'opcao_prescritor', 'prescritor');
                               }"
                     onBlur="if (this.value!=''){document.form_inclusao.prescritor.focus();}">

             </td>
             <td class="descricao_campo_tabela" valign="middle" width="15%">
              <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Prescritor
             </td>
             <td class="campo_tabela" valign="middle" width="45%" colspan="2">
              <select size="1" id="prescritor" name="prescritor" style="width:350px;" onChange="buscar_inscricao();" onBlur="preenche_campos();">
                <option id="opcao_prescritor" value="0">Selecione um prescritor</option>
                <?php
                   $sql = " select concat(concat(p.id_profissional,'|',p.tipo_prescritor_id_tipo_prescritor),'|', p.inscricao) as codigo, concat(p.nome,'/',e.uf) as nome
                     from unidade_has_profissional u, profissional p, estado e
                     where u.unidade_id_unidade = $_SESSION[id_unidade_sistema]
                     and p.status_2 = 'A'
                     and u.profissional_id_profissional = p.id_profissional
                     and p.estado_id_estado = e.id_estado
                     order by p.nome " ;

                   $result=mysqli_query($db, $sql);
                   erro_sql("Tabela tipo movimento", $db,"");
                   while($movimento_info=mysqli_fetch_object($result)){
                     echo "<option id='opcao_prescritor' value='$movimento_info->codigo'>$movimento_info->nome</option>";
                   }
                ?>
              </select>
              <a href=JavaScript:window.popup_prescritor();>
              <img name="imagem_prescritor" src="<?php echo URL;?>/imagens/i_002.gif" border="0" title="Pesquisar"></a>
             <input type="hidden" size="20" name="id_prescritor" value="<?php if(isset($id_prescritor)){echo $id_prescritor;}?>">
             <input type="hidden" name="id_tipo_prescritor" value="<?php if(isset($id_tipo_prescritor)){echo $id_tipo_prescritor;}?>">
             </td>
             </table>
             </td>
            <tr>
             <td colspan=7 class="descricao_campo_tabela" align="right">
             <?php
              if($inclusao_perfil_profissional!="")
              {?>
               <input style="font-size: 10px;" type="button" name="novo" value="Novo Prescritor" onclick='popup_novo_prescritor();'>
              <?}
              else
              {?>
               <input style="font-size: 10px;" type="button" name="novo" value="Novo Prescritor" onClick="window.location='<?php echo URL;?>/modulos/profissional/profissional_inclusao.php?dispensacao=ok&id_paciente=<?=$id_paciente?>'" disabled>
              <?}?>
             </td>
            </tr>
            <tr>
             <td  colspan=7 class="descricao_campo_tabela">
              <div id="cabec_lista_dispensados" style="display:none;">
                   <table id='lista_dispensados' bgcolor='#D0D0D0' width='100%' cellpadding='0' cellspacing='1' border='0'>
                          <tr class='titulo_tabela' height='21'>
                              <td colspan='8' valign='middle' align='center' width='100%'> Medicamentos Dispensados </td>
                          </tr>
                          <tr bgcolor='#6B6C8F' class='coluna_tabela'>
                              <td width='39%' align='center'>Medicamento</td>
                              <td width='8%' align='center'>Lote</td>
                              <td width='25%' align='center'>Fabricante</td>
                              <td width='10%' align='center'>Validade</td>
                              <td width='12%' align='center'>Qtde. Disp.</td>
                              <td width='3%' align='center'></td>
                              <td width='3%' align='center'></td>
                          </tr>
                   </table>
              </div>
              <div id="hidden_lista_dispensados" style="display:none;">
                   <table id='hidden_lista' bgcolor='#D0D0D0' width='100%' cellpadding='0' cellspacing='1' border='0'>
                          <tr bgcolor='#6B6C8F' class='coluna_tabela'>
                              <td width='5%' align='center'>id_medicamento</td>
                              <td width='5%' align='center'>id_estoque</td>
                              <td width='5%' align='center'>lote</td>
                              <td width='5%' align='center'>id_fabricante</td>
                              <td width='5%' align='center'>validade</td>
                              <td width='5%' align='center'>qtde_lote</td>
                              <td width='5%' align='center'>qtde_prescrita</td>
                              <td width='5%' align='center'>tempo_tratamento</td>
                              <td width='5%' align='center'>qtde_anterior</td>
                              <td width='5%' align='center'>qtde_dispensada</td>
                              <td width='5%' align='center'>rec_controlada</td>
                              <td width='5%' align='center'>id_autorizador</td>
                          </tr>
                   </table>
              </div>
             </td>
            </tr>
            <tr>
             <td  colspan=7 class="descricao_campo_tabela">
              <div id="vetor"></div>
              <div id="lista"></div>
             </td>
            </tr>
             <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr class="titulo_tabela" height="21">
                    <td colspan="8" valign="middle" align="center" width="100%"> Incluir </td>
                </tr>
                <tr>
                 <td class="descricao_campo_tabela" valign="middle" width="15%">
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Medicamento
                 </td>
                 <td class="campo_tabela" valign="middle" width="60%">
                  <input type="hidden" name="flg_material">
                  <input type="hidden" name="flg_autorizador">
                  <input type="hidden" name="medicamento" id="medicamento">
                  <input type="textBox" name="medicamento01" id="medicamento01" style="width: 400px" style="text-transform:uppercase"
                         onChange="document.form_inclusao.flg_autorizador.value = ''; document.form_inclusao.unidade.value = ''; document.form_inclusao.medicamento.value = ''; TrimJS(); procura_medicamento_nome();"
                         onFocus="if(document.form_inclusao.flg_material.value=='1'){document.form_inclusao.flg_material.value='0';valida_prescritor_medicamento();}"
                         onBlur="document.form_inclusao.flg_autorizador.value = ''; document.form_inclusao.unidade.value = ''; document.form_inclusao.medicamento.value = ''; TrimJS(); procura_medicamento_nome();"
                         value="<?php echo $medicamento01;?>">
                  <div id="acDiv"></div>
                  <A HREF=JavaScript:window.popup_medicamento();><IMG src="<?php echo URL;?>/imagens/b_search.png" name="imagem_medicamento" border="0" title="Pesquisar"></a>
                  <input type="text" name="unidade" id="unidade" size="3" disabled>
                 </td>
                 <td  class="campo_tabela" valign="middle" width="25%">
                  <div id="controlada"></div>
                 </td>
                 </tr>
             <tr>
             <table width="100%" border="0" cellpadding="0" cellspacing="1">
              <tr>
                 <td colspan="2" class="descricao_campo_tabela" valign="middle" >
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Qtde. Prescrita
                 </td>
                 <td class="campo_tabela" valign="middle" >
                  <input type="text" name="qtde_prescrita" size="10"  maxlength="10" value="<?php echo $qtde_prescrita;?>" onKeyPress="return isNumberKey(event);">
                 </td>
                 <td colspan="2" class="descricao_campo_tabela" valign="middle" >
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Tempo de Tratamento
                 </td>
                 <td class="campo_tabela" valign="middle" >
                  <input type="text" name="tempo_tratamento" id="numero" value="<?php echo $tempo_tratamento;?>" maxlength="10" onKeyPress="return isNumberKey(event)" onblur="calcular_qtde_dispensar()"> Dias
                 </td>
              </tr>
              <tr>
                 <td colspan="2" class="descricao_campo_tabela" valign="middle" >
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>Qtde. Dispensada Anterior
                 </td>
                 <td class="campo_tabela" valign="middle" >
                  <input type="text" name="anterior" size="10"  maxlength="10" value="0" disabled>
                 </td>
                 <td colspan="2" class="descricao_campo_tabela" valign="middle" >
                  <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Qtde. a Dispensar
                 </td>
                 <td class="campo_tabela" valign="middle" >
                  <input type='text' name='qtde_dispensar' value='<?php echo intval($qtde_dispensar);?>' maxlength='10' onKeyPress='return isNumberKey(event);'>
                 </td>
              </tr>
                 <input type="hidden" size="20" name="itens_receita">
             </table>
             
            </tr>
            <tr>
                <td height="100%" align="center" valign="top">
                <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
                <tr>
                    <td colspan='6'>
                    <div id = "resposta">
                        <table id="tabela1" bgcolor='#D0D0D0' width="100%" cellpadding="0" cellspacing="1" border="0">
                        <tr bgcolor='#6B6C8F' class="coluna_tabela">
                         <td width='10%' align='center'>
                          Lote
                         </td>
                         <td width='30%' align='center'>
                          Fabricante
                         </td>
                         <td width='10%' align='center'>
                          Validade
                         </td>
                         <td width='10%' align='center'>
                          Estoque
                         </td>
                         <td width='15%' align='center'>
                          Qtde. a Dispensar
                         </td>
                        </tr>
                    </div>
                </table>
              </td>
            </tr>

            <tr>
              <td colspan="4" align="right" bgcolor="#D8DDE3" width='88%'>
                <input style="font-size: 10px;" type="button" name="adiciona" id="adiciona" value="Adiciona" onClick="if (adicionar_medicamentos()){monta_lista();}">
              </td>
              <td colspan="2" align="right" bgcolor="#D8DDE3" width='12%'>
                <input style="font-size: 10px;" type="button" name="salvar" id="salvar" value="Salvar >>" onClick="salvar_receita()" disabled>
              </td>
            </tr>

            <tr>
             <td colspan="6" class="descricao_campo_tabela">
              <table align="center" border="0" cellpadding="0" cellspacing="0">
               <tr valign="top" class="descricao_campo_tabela"  height="21">
                <td align="center" bgcolor="#D8DDE3" width='50%'>
                 <input style="font-size: 10px;" type="button" name="novareceita" id="novareceita" value="Nova Receita" style="display: none;" onClick="window.location='<?php echo URL;?>/modulos/dispensar/inicial.php?aplicacao=<?php echo $_SESSION[cod_aplicacao];?>'">
                </td>
                <td align="center" bgcolor="#D8DDE3" width='50%'>
                 <input style="font-size: 10px;" type="button" name="completarreceita" id="completarreceita" value="Completar Receita" style="display: none;" onClick="window.location='<?php echo URL;?>/modulos/dispensar/busca_altera_receita.php?aplicacao=<?php echo $_SESSION[cod_aplicacao];?>'">
                </td>
               </tr>
              </table>
              </td>
            </tr>

    		<tr>
			  <td colspan="6" class="descricao_campo_tabela">
				<table align="center" border="0" cellpadding="0" cellspacing="0">
				       <tr valign="top" class="descricao_campo_tabela"  height="21">
						<td><img src="<? echo URL."/imagens/obrigat.gif";?>" border="0"> Campos Obrigatórios</td>
						<td>&nbsp&nbsp&nbsp</td>
                        <td><img src="<? echo URL."/imagens/obrigat_1.gif";?>" border="0"> Campos não Obrigatórios</td>
					   </tr>
				</table>
              </td>
			</tr>

          </table>
         </tr>
         </form>
         </body>
        </table>

        </td>
      </tr>
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
    
    <script>
      <!--
      //Instanciar objeto AutoComplete Medicamento
      var ACM = new dmsAutoComplete('medicamento01','acDiv','medicamento','unidade');

      ACM.ajaxTarget = '../../xml_dispensacao/medicamento_dispensacao.php';
      //Definir função de retorno
      //Esta função será executada ao se escolher a palavra
       ACM.chooseFunc = function(id,label)
       {
         var array_material = id.split("|");
         
         var nome_med = document.form_inclusao.medicamento01.value;
         
         if (array_material[0]=='0')
         {
           //retira espaços em branco do nome do medicamento digitado em tela
          TrimJS();
          procura_medicamento_nome();
         }
         else
         {
          document.form_inclusao.medicamento.value = array_material[0];
          document.form_inclusao.unidade.value = array_material[1];
          document.form_inclusao.flg_autorizador.value = '';
          valida_prescritor_medicamento();
         }
       }
       
       
  //-->
  </script>

<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////

    require DIR."/footer.php";

  //************************
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
<script>
        if (document.form_inclusao.inscricao.value=="")
        {
         document.form_inclusao.inscricao.focus();
        }
</script>
