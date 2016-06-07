<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

  session_start();

  $_SESSION[APLICACAO]=$_GET[aplicacao];

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


    if($_POST[id_receita]!="")
    {
    
     //busca dados do paciente
     $id_receita = $_POST[id_receita];
     $sql = "select * from receita where id_receita = '$id_receita'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));

     $ano = $dados_receita->ano;
     $unidade = $dados_receita->unidade_id_unidade;
     $numero = $dados_receita->numero;
     $prescritor = $dados_receita->profissional_id_profissional;

     $status_receita = $dados_receita->status_2;

     $sql = "select * from profissional where id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $inscricao = $dados_prescritor->inscricao;
     $nomeprescritor = $dados_prescritor->nome;

     $data_emissao = $dados_receita->data_emissao;
     $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);

     $origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
     $sql = "select * from subgrupo_origem where id_subgrupo_origem = '$origem'";
     $dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeorigem = $dados_origem->descricao;

     $cidadereceita = $dados_receita->cidade_id_cidade;
     $sql = "select c.*, e.* from cidade c, estado e where c.id_cidade = '$cidadereceita'
          and e.id_estado = c.estado_id_estado";
     $dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidadereceita = $dados_cidadereceita->nome."/".$dados_cidadereceita->uf;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $nome            = $dados_paciente->nome;

    }

    if($_GET[id_receita]!="")
    {

     //busca dados do paciente
     $id_receita = $_GET[id_receita];
     $sql = "select * from receita where id_receita = '$id_receita'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));

     $ano = $dados_receita->ano;
     $unidade = $dados_receita->unidade_id_unidade;
     $numero = $dados_receita->numero;
     $prescritor = $dados_receita->profissional_id_profissional;

     $status_receita = $dados_receita->status_2;

     $sql = "select * from profissional where id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $inscricao = $dados_prescritor->inscricao;
     $nomeprescritor = $dados_prescritor->nome;

     $data_emissao = $dados_receita->data_emissao;
     $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);

     $origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
     $sql = "select * from subgrupo_origem where id_subgrupo_origem = '$origem'";
     $dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeorigem = $dados_origem->descricao;

     $cidadereceita = $dados_receita->cidade_id_cidade;
     $sql = "select c.*, e.* from cidade c, estado e where c.id_cidade = '$cidadereceita'
          and e.id_estado = c.estado_id_estado";
     $dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidadereceita = $dados_cidadereceita->nome."/".$dados_cidadereceita->uf;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $nome            = $dados_paciente->nome;

    }


    if (($_GET[ano]!= "") and  ($_GET[numero]!= "") and ($_GET[unidade]!= ""))
    {
     $ano = $_GET[ano];
     $unidade = $_GET[unidade];
     $numero = $_GET[numero];

     $sql = "select * from receita where ano = '$ano' and numero = '$numero' and unidade_id_unidade = '$unidade'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));

     $id_receita = $dados_receita->id_receita;

     $status_receita = $dados_receita->status_2;

     $prescritor = $dados_receita->profissional_id_profissional;

     $sql = "select * from profissional where id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $inscricao = $dados_prescritor->inscricao;
     $nomeprescritor = $dados_prescritor->nome;

     $data_emissao = $dados_receita->data_emissao;
     $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);

     $origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
     $sql = "select * from subgrupo_origem where id_subgrupo_origem = '$origem'";
     $dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeorigem = $dados_origem->descricao;

     $cidadereceita = $dados_receita->cidade_id_cidade;
     $sql = "select c.*, e.* from cidade c, estado e where c.id_cidade = '$cidadereceita'
          and e.id_estado = c.estado_id_estado";
     $dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidadereceita = $dados_cidadereceita->nome."/".$dados_cidadereceita->uf;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $nome            = $dados_paciente->nome;

    }

    if (($_POST[ano]!= "") and  ($_POST[numero]!= "") and ($_POST[unidade]!= ""))
    {
     $ano = $_POST[ano];
     $unidade = $_POST[unidade];
     $numero = $_POST[numero];

     $sql = "select * from receita where ano = '$ano' and numero = '$numero' and unidade_id_unidade = '$unidade'";
     $dados_receita = mysqli_fetch_object(mysqli_query($db, $sql));

     $id_receita = $dados_receita->id_receita;

     $status_receita = $dados_receita->status_2;
     
     $prescritor = $dados_receita->profissional_id_profissional;

     $sql = "select * from profissional where id_profissional = '$prescritor'";
     $dados_prescritor = mysqli_fetch_object(mysqli_query($db, $sql));
     $inscricao = $dados_prescritor->inscricao;
     $nomeprescritor = $dados_prescritor->nome;

     $data_emissao = $dados_receita->data_emissao;
     $data_emissao = substr($data_emissao,8,2)."/".substr($data_emissao,5,2)."/".substr($data_emissao,0,4);

     $origem = $dados_receita->subgrupo_origem_id_subgrupo_origem;
     $sql = "select * from subgrupo_origem where id_subgrupo_origem = '$origem'";
     $dados_origem = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomeorigem = $dados_origem->descricao;

     $cidadereceita = $dados_receita->cidade_id_cidade;
     $sql = "select c.*, e.* from cidade c, estado e where c.id_cidade = '$cidadereceita'
          and e.id_estado = c.estado_id_estado";
     $dados_cidadereceita = mysqli_fetch_object(mysqli_query($db, $sql));
     $nomecidadereceita = $dados_cidadereceita->nome."/".$dados_cidadereceita->uf;

     $id_paciente = $dados_receita->paciente_id_paciente;

     $sql = "select * from paciente where id_paciente = '$id_paciente'";
     $dados_paciente = mysqli_fetch_object(mysqli_query($db, $sql));

     $nome            = $dados_paciente->nome;

    }
    
    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";

    //permissão
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
<script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="../../scripts/frame.js"></script>
<script language="JavaScript" type="text/JavaScript">

function popup_autorizador(material)
{
    var texto = material;
    var pos = texto.indexOf(",");
    var mat_par = texto.substr(pos+1);
//    alert (material);
    var url = "autorizador_alteracao.php?material="+mat_par;
	var height = 115;
	var width = 450;
	var left = (screen.availWidth - width)/2;
	var top = (screen.availHeight - height)/2;
	if (window.showModalDialog)
	{
		var dialogArguments = new Object();
		var _R = window.showModalDialog(url, dialogArguments, "dialogWidth=450px;dialogHeight=115px;scroll=no;status=no;");
		if ("undefined" != typeof(_R))
		{
			SetNameAutorizador(_R.id, material);
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

function SetNameAutorizador(id, material)
{
    var texto = "AUT_"+material;
//    alert(texto);
    var x = document.getElementById(texto);
    x.value = id;

    var texto = "imgAutorizacao_"+material;
    var x = document.getElementById(texto);
    x.heigth = 0;
    x.width = 0;

}

function verificar_campos()
{
  var soma = 0;
  var ident = '';
  var item_id = '';
  var lote_id = '';
  var str = '';
  var valor_compara_ant = 0;
  var valor_compara = 0;
  var erro = 0 ;
  var vet_soma = new Array(20);
  var vet_dispensado = new Array(20);
  var vet_aut = new Array(20);

  //validar valor dispensado para o material

  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var k = 0;
   var x = document.form_alteracao.elements[i];
   if (x.name == 'lista_itens_receita[]')
   {
      var valores = x.value;
      var vet_valores = valores.split(",");

      var medic = parseInt(vet_valores[1],10);
      var prescritor = parseInt(vet_valores[2],10);
      var anterior = parseInt(vet_valores[3],10);

      if (document.getElementById(medic))
      {
        if ((document.getElementById(medic).value!=0 )&& (document.getElementById(medic).value!=""))
        {
         if ((prescritor-anterior)!= 0)
         {
          vet_soma[k] = prescritor - anterior;
         }
         else
         {
          vet_soma[k] = 0;
         }
         k+=1;
        }
      }
   }
  }

  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var k = 0;
   var x = document.form_alteracao.elements[i];
   var texto;
   if (x.name == 'rec_controlada[]')
   {
     texto = x.id;
     if (document.getElementById(texto.substr(3)))
     {
      if ((document.getElementById(texto.substr(3)).value != 0) && (document.getElementById(texto.substr(3)).value != ""))
      {
       if ((x.value == "") || (x.value == 0))
       {
        alert ('Favor preecher campo obrigatório');
        return false
       }
      }
     }
   }
  }

  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var k = 0;
   var x = document.form_alteracao.elements[i];
   var texto;
   if (x.name == 'id_aut[]')
   {
      if (x.value == "")
      {
        texto = x.id;
        item_id = texto.substr(4);
        teste = 'item'+item_id;
        if ((document.getElementById(teste).value!='0') && (document.getElementById(teste).value!=''))
        {
         alert('Medicamento sem autorização');
         return false
        }
      }
   }
  }

  var tam_vet=0;
  var texto1="";
  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var k = 0;
   var x = document.form_alteracao.elements[i];
   if (x.name == 'lista_estoque[]')
   {
     var identificador = x.value;
     var vet1 = identificador.split(",");

     texto1 =  texto1 + vet1[2] + ",";
     tam_vet+=1;
   }
  }

  var vet_texto1 = texto1.split(",");

  var texto2 = "";
  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var k = 0;
   var x = document.form_alteracao.elements[i];
   if (x.name == 'valor[]')
   {
     texto2 = texto2 + x.value + ",";
   }
  }

  var vet_texto2 = texto2.split(",");

  for (var i=0;i<tam_vet;i++)
  {
   if (parseInt(vet_texto1[i],10) < parseInt(vet_texto2[i],10))
   {
    alert ("Quantidade a dispensar por lote é maior que a quantidade existente no lote!");
    return
   }
  }

  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var k = 0;
   var x = document.form_alteracao.elements[i];
   if (x.name == 'item[]')
   {
      if (x.value == '')
      {
       vet_dispensado[k] = 0;
      }
      else
      {
       vet_dispensado[k] = x.value;
      }
      //alert (vet_dispensado[k]);
      k+=1;
   }
  }


  for (var i=0;i<=k;i++)
  {
   if (parseInt(vet_dispensado[i],10) > parseInt(vet_soma[i],10))
   {
    alert ('Valor a ser dispensado é maior que o valor prescrito!');
    return false;
   }
  }

  for (var i=0;i<document.form_alteracao.elements.length;i++)
  {
   var x = document.form_alteracao.elements[i];
   if (x.name == 'item[]')
   {
      if (item_id == '')
      {
       if (x.value=='')
       {
        valor_compara_ant = 0;
       }
       else
       {
        valor_compara_ant = x.value;
       }
       if (x.value=='')
       {
        valor_compara = 0;
       }
       else
       {
        valor_compara = x.value;
       }
      }

      item_id = x.id;  //item<$id_itens_receita e material
      item_id = item_id.substring(item_id.indexOf(',')-1,item_id.length);  //id_material
      if (item_id != ident)
      {
       valor_compara_ant = valor_compara;
      }
      if (x.value=='')
      {
       valor_compara = 0;
      }
      else
      {
       valor_compara = x.value;
      }

   }
   if (x.name == 'valor[]')
   {
    str = x.id;
    lote_id = str.substring(str.indexOf(',')+1,str.indexOf('_'));
    if (ident == '')
    {
     ident = lote_id;
    }
    if (ident == lote_id)
    {
     if ((x.value != '') || (x.value != 0))
     {
        soma = soma + parseInt(x.value,10);
     }
     else
     {
        soma = soma;
     }
    }
    else
    {
     if (soma == parseInt(valor_compara_ant,10))
     {
      ident = lote_id;
      if (x.value != '')
      {
         soma = parseInt(x.value,10);
      }
      else
      {
         soma = 0;
      }
     }
     else
     {
      erro = 1;
     }

    }

   }
  }
  if ((erro == 1))
  {
//   alert(soma);
//   alert(valor_compara_ant);
   alert ('Soma das quantidades dos lotes deve ser igual a quantidade a dispensar');
   return false;
  }
  else
  {
   if (soma != parseInt(valor_compara,10))
   {
    alert ('Soma das quantidades dos lotes deve ser igual a quantidade a dispensar');
    return false;
   }
   salvar_completar();
  // document.form_alteracao.submit();
  }

}

function precisa_autorizador()
{
    var url = "../../xml_dispensacao/precisa_autorizador.php?material="+document.form_inclusao.medicamento.value;
    requisicaoHTTP("GET", url, true);
}

function trataDados()
{
	var info = ajax.responseText;  // obtém a resposta como string

    if (info == 'nao_autorizador')
    {
    }

    if (info == 'sim_autorizador')
    {
    }
     var retornoajax=info;
     var pos= retornoajax.indexOf("-");
     var id_movto_geral = retornoajax.substr(pos+1);
     if (id_movto_geral!='')
      {
        document.form_alteracao.id_movto_geral.value = id_movto_geral;
        document.form_alteracao.salvar.disabled = true;

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
             var x = document.getElementById(texto);
             x.heigth = 0;
             x.width = 0;
           }
        }
        imprimir_recibo();
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

         if (x.name == 'lista_estoque[]')
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
        
        for (var i=0;i<document.form_alteracao.elements.length;i++)
        {
          var verifica = document.form_alteracao.elements[i];
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
                // alert(aux_qtde+ " = "+elem_aux.id);
                 if (aux_qtde==elem_aux.id)
                 {
                    achou=true;
                 }
              }
              if (achou)
              {
                 vetItens[pos]=val_itens+','+document.getElementById(aux_qtde).value;
         //        alert("if "+vetItens[pos]);
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
               vetLotes[posicao] = val_est+','+document.getElementById(aux_qtde_lote).value;
               conta++;
           }
           else
           {
               conta = 0;
               var aux_id_estoque = itens.substring(3,itens.indexOf(',')); //id_esotque
               var aux_qtde_lote  = 'val'+aux_id_estoque+','+aux_id_material+'_'+conta;
               var val_est = document.getElementById(itens).value;
               vetLotes[posicao] = val_est+','+document.getElementById(aux_qtde_lote).value;
               conta++;
           }
           aux = aux_id_material;
           posicao++;
         }
        }
        //alert(vetItens.length);

        for(pos=0;pos<vetItens.length;pos++)
        {            //alert(vetItens[pos]);
          valores[pos] = vetItens[pos].split(',');
          var aux_aut_usuario = 'AUT_'+valores[pos][0]+','+valores[pos][1];
          valores[pos][7] = document.getElementById(aux_aut_usuario).value;
         // alert(aux_aut_usuario+"  "+valores[pos][7]);
               //0-id_itens_receita
               //1-id_material
               //2-prescrita
               //3-anterior
               //4-flg autorizacao
               //5-dias limite
               //6-qtde a dispensar
               //7-usuario autorizador
        }

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
              //alert ('v ' + v + ' u ' + u);
              //alert ('item ' + valores[v][1]+ ' sub '+ valor_est[u][0]);
              if(valores[v][1]==valor_est[u][0])
              {
                var h_pos = document.getElementById('hidden_lista').rows.length;
               // alert (h_pos);
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
                var j = tab.insertCell(9);    //id_fabricante
                var k = tab.insertCell(10);   //lote
                var l = tab.insertCell(11);   //validade
                var m = tab.insertCell(12);   //qtde_estoque
                var n = tab.insertCell(13);   //flg_autorizacao
                var o = tab.insertCell(14);   //dias_limite
                var p = tab.insertCell(15);   //qtd_total
                var q = tab.insertCell(16);   //qtd_lote
                var r = tab.insertCell(17);   //autorizador

                a.innerHTML = id_receita;
                b.innerHTML = id_paciente;
                c.innerHTML = num_doc;
                d.innerHTML = valores[v][0];
                e.innerHTML = valor_est[u][1];
                f.innerHTML = valores[v][1];
                g.innerHTML = 0;                //rec_controlada
                h.innerHTML = valores[v][2];
                i.innerHTML = valores[v][3];
                j.innerHTML = valor_est[u][3];  //id_fabricante
                k.innerHTML = valor_est[u][4];  //lote
                l.innerHTML = valor_est[u][5];  //validade
                m.innerHTML = valor_est[u][2];  //qtde_estoque
                n.innerHTML = valores[v][4];    //flg
                o.innerHTML = valores[v][5];
                p.innerHTML = valores[v][6];
                q.innerHTML = valor_est[u][6];
                r.innerHTML = valores[v][7];    //autorizador
              }

           }
        }

  }



   function buscar_completar(id_estoque, val_qtde, dispens)
    {
        var id_receita  = document.getElementById('id_receita').value;
        var id_paciente = document.getElementById('id_paciente').value;
        //var id_receita  = document.getElementById('id_receita').value;

        var aux_ano  = document.getElementById('aux_ano').value;
        var aux_unid = document.getElementById('aux_unidade').value;
        var aux_num  = document.getElementById('aux_num').value;
        var num_doc = aux_ano+"-"+aux_unid+"-"+aux_num;

        var url = "../../xml_dispensacao/buscar_completar.php?id_receita="+id_receita+"&id_paciente="+id_paciente+
                                             "&num_doc="+num_doc+"&id_estoque="+id_estoque+"&qtde_lote="+val_qtde+
                                             "&total="+dispens;
  // alert(url);
        var pars = "";
        var myAjax = new Ajax.Request(url,{
            method: 'post',
            parameters: pars,
            onComplete: mostraResposta
        });

    }

  function mostraResposta(resposta)
  {
     var div = document.getElementById("hidden_lista_dispensados");
     div.innerHTML = resposta.responseText;
  }


function acertar_dados_salvar()
{
   montar_tabela();
   var itens=document.getElementById("hidden_lista");
   var total_linhas=itens.rows.length;
   var lista=new Array(total_linhas);
   //alert("linhas "+total_linhas);
  for(var i=1; i<lista.length; i++)
  {
    lista[i]=new Array(17);
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

  document.getElementById('dados_salvar').value=info;
}

function salvar_completar()
{
  acertar_dados_salvar();
  var dados_salvar = document.getElementById('dados_salvar').value;
  var nome = document.getElementById('nome').value;
  var url = "../../xml_dispensacao/salva_altera_receita.php?dados_salvar="+dados_salvar+"&nome="+nome;
  requisicaoHTTP("GET", url, true);
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
        <td align="left">
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
               <input type="hidden" name="id_receita" value="<?php echo $id_receita;?>">
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
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Número
              </td>
              <td class="campo_tabela" valign="middle" width="15%">
               <input type="hidden" id="aux_num" name="aux_num" value="<?php echo $numero;?>">
               <input type="text" name="numero" size="5" maxlength="10" value="<?php echo $numero;?>" disabled>
              </td>
            </tr>
            
            <tr>
             <td class="descricao_campo_tabela" valign="middle">
               <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>Data Emissão
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
                            <td colspan='10' valign="middle" align="center" width="100%"> Medicamentos Dispensados </td>
                        </tr>

                        <tr class="coluna_tabela">
                            <td  colspan='2' align='center' width='30%'>
                            Medicamento
                            </td>
                            <td align='center' width='5%'>
                            Ult. Disp.
                            </td>
                            <td align='center' width='8%'>
                            N. Notificação
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
                         $sql = "select ir.*, ma.descricao, ma.flg_autorizacao_disp, ma.dias_limite_disp
                              from itens_receita ir, material ma
                              where ir.receita_id_receita = '$id_receita'
                              and ir.material_id_material = ma.id_material
                              order by descricao";
                         $item = mysqli_query($db, $sql);
                         //echo $sql;
                         //echo exit;
                         while ($dados_item = mysqli_fetch_object($item))
                         {
                          $id_material      = $dados_item->material_id_material;
                          $data_ult_disp    = $dados_item->data_ult_disp;
                          
                          if ($data_ult_disp!="")
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
                          //$dispensar        = (($prescrita/$tempo)*30)-$anterior;
                          $dispensar        = intval(($prescrita/$tempo)*30);
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

                           //echo "Limite:".date('Y-m-d',strtotime($data_limite_restricao));
                           //echo " Hoje:".date('Y-m-d');

                           // echo exit;

                           if ((date('Y-m-d',strtotime($data_limite_restricao)) < date('Y-m-d')))
                           {
                            $data_vencida = "S";
                           }
                           else
                           {
                            $data_vencida = "N";
                           }
                          }

                          $sql = "select e.*, f.descricao from estoque e, fabricante f
                             where
                             e.material_id_material = '$dados_item->material_id_material'
                             and e.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                             and e.quantidade > 0
                             and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                             and e.validade >'".date("Y-m-d")."'
                             and e.fabricante_id_fabricante = f.id_fabricante
                             order by e.validade asc ";
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
                             <td bgcolor="#D8DDE3" colspan='2' align="left">
                              <input type="hidden" size="20" name="id_aut[]" id="<?php echo 'AUT_'.$id_itens_receita.','.$dados_item->material_id_material;?>" >
                              <input type="hidden" size="10" id="lista_itens<?php echo $id_itens_receita;?>,<?php echo $id_material;?>" name="lista_itens_receita[]" value="<?php echo $id_itens_receita;?>,<?php echo $id_material;?>,<?php echo $prescrita;?>,<?php echo $anterior;?>,<?php echo $nec_autorizacao;?>,<?php echo $dias_limite;?>">
                              <A HREF=JavaScript:window.popup_autorizador('<?php echo $id_itens_receita;?>,<?php echo $dados_item->material_id_material;?>');><IMG SRC="<?php echo URL. '/imagens/mini_cadeado_red2.gif'; ?>" id="<?php echo 'imgAutorizacao_'.$id_itens_receita.','.$dados_item->material_id_material;?>" border="0" title="Precisa de Autorização"></A>
                              <?php echo $dados_item->descricao;?>
                             </td>
                            <?php
                            }
                            else
                            {?>
                            <td bgcolor="#D8DDE3" colspan='2' align="left"><input type="hidden" size="20" name="id_aut[]" id="<?php echo 'AUT_'.$id_itens_receita.','.$dados_item->material_id_material;?>" value="0"><input type="hidden" size="10" id="lista_itens<?php echo $id_itens_receita;?>,<?php echo $id_material;?>" name="lista_itens_receita[]" value="<?php echo $id_itens_receita;?>,<?php echo $id_material;?>,<?php echo $prescrita;?>,<?php echo $anterior;?>,<?php echo $nec_autorizacao;?>,<?php echo $dias_limite;?>"><?php echo $dados_item->descricao;?></td>

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
                          $sql="select l.*, m.* from lista_especial l, material m
                               where m.lista_especial_id_lista_especial = l.id_lista_especial
                               and l.flg_receita_controlada like 'S'
                               and m.id_material = '$id_material'";
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
                            $sql = "select e.*, f.descricao from estoque e, fabricante f
                             where
                             e.material_id_material = '$dados_item->material_id_material'
                             and e.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                             and e.quantidade > 0
                             and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                             and e.validade >'".date("Y-m-d")."'
                             and e.fabricante_id_fabricante = f.id_fabricante
                             order by e.validade asc ";
                             //echo $sql;
                             //echo exit;
                            $lote = mysqli_query($db, $sql);
                            if ((mysqli_num_rows($lote)>0))
                            {?>
                             <td bgcolor="#D8DDE3" align="center"><input type="text" size="10" maxlength="20" name="rec_controlada[]" id="<?php echo 'NR_'.$dados_item->material_id_material;?>"></td>
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
                        //para cada medicamento verifica se ainda posso dispensar
                        if ($prescrita == $anterior)
                        {?>
                            <td bgcolor="#D8DDE3" align="right">0</td>
    						<td bgcolor="#D8DDE3" align="center"></td>
                         </tr>
                      <?}
                        else
                        {
                        //para cada medicamento os lotes disponiveis
                        $sql = "select e.*, f.descricao from estoque e, fabricante f
                             where
                             e.material_id_material = '$dados_item->material_id_material'
                             and e.unidade_id_unidade = '$_SESSION[id_unidade_sistema]'
                             and e.quantidade > 0
                             and (e.flg_bloqueado is null or e.flg_bloqueado = '')
                             and e.validade >'".date("Y-m-d")."'
                             and e.fabricante_id_fabricante = f.id_fabricante
                             order by e.validade asc ";
                             //echo $sql;
                             //echo exit;
                             $lote = mysqli_query($db, $sql);
                         if ((mysqli_num_rows($lote)>0))
                         {
                         ?>
                            <td bgcolor="#D8DDE3" align="center"><input type="text" size="5" name="item[]" id="item<?php echo $id_itens_receita;?>,<?php echo $dados_item->material_id_material;?>" value="<?php echo intval($dispensar);?>" onKeyPress="return isNumberKey(event);"></td>
    						<td bgcolor="#D8DDE3" align="center"><A href="javascript:showFrame('<?php echo $dados_item->descricao;?>');"><IMG SRC="<?php echo URL. '/imagens/folder_store.gif'; ?>" name="imagem_lote_<?php echo $dados_item->descricao;?>" BORDER="0" TITLE="Exibir Informações de Lotes"></A></td>
                        </tr>

                        <tr>
                           <td colspan="9">
                           <input type="hidden" size="20" id="divlotes_<?php echo $dados_item->descricao;?>" value="<?php echo $dados_item->descricao;?>">
                              <div id='<?php echo $dados_item->descricao;?>' style="display:'none';">
                               <table bgcolor='#808080' align="center" width="100%" border="0" cellpadding="0" cellspacing="1" >
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
                                    <td bgcolor="#FFFFFF" align="left"><input type="hidden" name="id_estoque" id="id_estoque<?php echo $dados_lote->id_estoque;?>" value="<?php echo $dados_lote->id_estoque;?>">
                                     <input type="hidden" size="10" id="est<?php echo $dados_lote->id_estoque;?>,<?php echo $dados_item->material_id_material;?>" name="lista_estoque[]" value="<?php echo $dados_item->material_id_material;?>,<?php echo $dados_lote->id_estoque;?>,<?php echo intval($dados_lote->quantidade);?>,<?php echo $dados_lote->fabricante_id_fabricante;?>,<?php echo $dados_lote->lote;?>,<?php echo $dados_lote->validade;?>"><?php echo $dados_lote->lote;?></td>
                                    <td bgcolor="#FFFFFF" align="left"><?php echo $dados_lote->descricao;?></td>
                                    <td bgcolor="#FFFFFF" align="center"><?php echo substr($dados_lote->validade,8,2)."/".substr($dados_lote->validade,5,2)."/".substr($dados_lote->validade,0,4);?></td>
                                    <td bgcolor="#FFFFFF" align="right"><?php echo intval($dados_lote->quantidade);?></td>
                                    <td bgcolor="#FFFFFF" align="center"><input type="text" size="5" name="valor[]" id="val<?php echo $dados_lote->id_estoque;?>,<?php echo $dados_item->material_id_material.'_'.$cont;?>" onKeyPress="return isNumberKey(event);"></td>
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
                             <?}
                             else
                             {?>
                               <input style="font-size: 10px;" type="button" name="salvar" value="Salvar >>" onClick="verificar_campos()">
                             <?}
                            }
                            else
                            {?>
                              <input style="font-size: 10px;" type="button" name="salvar" value="Salvar >>" onClick="verificar_campos()" disabled>
                            <?}?>
                         </td>
                       </tr>
                     </table name='3'>
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
                                            <td width='10%' align='center'>id_fabricante</td>
                                            <td width='10%' align='center'>lote</td>
                                            <td width='15%' align='center'>validade</td>
                                            <td width='15%' align='center'>qtd_estoque</td>
                                            <td width='15%' align='center'>flg_autorizacao</td>
                                            <td width='15%' align='center'>dias_limite</td>
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

<?php
    ////////////////////
    //RODAPÉ DA PÁGINA//
    ////////////////////
    require DIR."/footer.php";
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////

  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
