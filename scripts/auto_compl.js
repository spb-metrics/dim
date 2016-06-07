/* 
	Copyright 2011 Inform�tica de Munic�pios Associados
	Este arquivo � parte do programa DIM
	O DIM � um software livre; voc� pode redistribu�-lo e/ou modific�-lo dentro dos termos da Licen�a P�blica Geral GNU como publicada pela Funda��o do Software Livre (FSF); na vers�o 2 da Licen�a.
	Este programa � distribu�do na esperan�a que possa ser  �til, mas SEM NENHUMA GARANTIA; sem uma garantia impl�cita de ADEQUA��O a qualquer  MERCADO ou APLICA��O EM PARTICULAR. Veja a Licen�a P�blica Geral GNU/GPL em portugu�s para maiores detalhes.
	Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU, sob o t�tulo "LICENCA.txt", junto com este programa, se n�o, acesse o Portal do Software P�blico Brasileiro no endere�o www.softwarepublico.gov.br ou escreva para a Funda��o do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// +---------------------------------------------------------------------------------+
// | IMA - Inform�tica de Munic�pios Associados S/A - Copyright (c) 2006             |
// +---------------------------------------------------------------------------------+
// | Sistema ............: DIM                                                       |
// | Arquivo ............: auto_compl.js                                             |
// | Autor ..............: Jos� Renato C. P. Barbosa <jrenato.barbosa@ima.sp.gov.br> |
// | Data de Cria��o ....: 20/11/2006                                                |
// | �ltima Atualiza��o .: 07/03/2007 - 13:03                                        |
// | Fun��o .............: Fun��es para autocompletar campos text                    |
// +---------------------------------------------------------------------------------+

// func�o que autocompleta campo texto, crinado uma lista

function checkList(obj,what)
{
var k = event.keyCode;
var T = findPosY(obj);
var L = findPosX(obj);
var hld = document.getElementById('listHolder');

  if(!hld)
  {
    var hld = document.createElement('DIV');

    hld.id = 'listHolder';
    document.body.appendChild(hld);
  }
  hld.style.top = (T + obj.offsetHeight);
  hld.style.left = L;
  hld.style.display = 'none';
  var txt = obj.value;
  if((txt) && (txt.length >= 3))
  {
    var str = '<select class="list" onclick="'
              +'setOption(\''+obj.id+'\',this.options[this.selectedIndex].value)"'
              +'onkeyup="if(event.keyCode==13){'
              +'setOption(\''+obj.id+'\',this.options[this.selectedIndex].value)};'
              +'if(event.keyCode==27){'
              +'document.getElementById(\''+obj.id+'\').focus();'
              +'document.getElementById(\'listHolder\').style.display=\'none\';}"'
              +' id="selector" size="5">';
    var match = false;

    for(a=0;a<what.length;a++)
    {
      if(txt.toLowerCase() == what[a][1].toLowerCase().substring(0,txt.length))
      {
        match = true;
        str += ('<option value="'+what[a][0].replace(/\'/gi,'�')+'">'+what[a][1]+'</option>')
      }
    }
    str += '</select>'
    if(match)
    {
      hld.innerHTML = str
      hld.style.display = 'block'
      var sel = document.getElementById('selector')
      if(k == '40')
      {
        sel.focus()
      }
      if(k == '13')
      {
        document.getElementById('listHolder').style.display='none'
        //boxAction(obj.value)
       }
    }
  }
}

function setOption(obj,val)
{
  var obj = document.getElementById(obj)
  obj.value = val
  obj.focus()
  document.getElementById('listHolder').style.display = 'none'
}

/********************************************************************/

function checkList02(obj01,obj02,what, e)
{
var k = e.keyCode;
var T = findPosY(obj01);
var L = findPosX(obj01);
var hld = document.getElementById('listHolder');

  if(!hld)
  {
    var hld = document.createElement('DIV');

    hld.id = 'listHolder';
    document.body.appendChild(hld);
  }
  hld.style.top = (T + obj01.offsetHeight);
  hld.style.left = L;
  hld.style.display = 'none';
  var txt = obj01.value;

  if((txt) && (txt.length >= 3))
  {
    var str = '<select class="list" onclick="'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)"'
              +' onkeyup="if(event.keyCode==13){'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)};'
              +'if(event.keyCode==27){'
              +'document.getElementById(\''+obj01.id+'\').focus();'
              +'document.getElementById(\'listHolder\').style.display=\'none\';}"'
              +' id="selector" size="5">';
    var match = false;

    for(a=0;a<what.length;a++)
    {
      if(txt.toLowerCase() == what[a][1].toLowerCase().substring(0,txt.length))
      {
        match = true;
        str += ('<option value="'+what[a][0].replace(/\'/gi,'�')+'">'+what[a][1]+'</option>')
      }
    }
    str += '</select>'
    if(match)
    {
      hld.innerHTML = str
      hld.style.display = 'block'
      var sel = document.getElementById('selector')
      if(k == '40')
      {
        sel.focus()
      }
      if(k == '13')
      {
        document.getElementById('listHolder').style.display='none'
        //boxAction(obj.value)
      }
    }
  }
}

function setOption02(obj01,obj02,val01,val02)
{
  var obj01 = document.getElementById(obj01);
  var obj02 = document.getElementById(obj02);
  obj01.value = val01;
  obj02.value = val02;
  obj01.focus();
  document.getElementById('listHolder').style.display = 'none';
}

/********************************************************************/
//material,id_medicamento,codigo_material,id_unidade_medicamento,unidade

function checkList03(obj01,obj02,what)
{
var k = event.keyCode;
var T = findPosY(obj01);
var L = findPosX(obj01);
var hld = document.getElementById('listHolder');

  if(!hld)
  {
    var hld = document.createElement('DIV');

    hld.id = 'listHolder';
    document.body.appendChild(hld);
  }
  hld.style.top = (T + obj01.offsetHeight);
  hld.style.left = L;
  hld.style.display = 'none';
  var txt = obj01.value;

  if((txt) && (txt.length >= 3))
  {
    var str = '<select class="list" onClick="'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)"'
              +' onKeyUp="if(event.keyCode==13){'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)};'
              +'if(event.keyCode==27){'
              +'document.getElementById(\''+obj01.id+'\').focus();'
              +'document.getElementById(\'listHolder\').style.display=\'none\';}"'
              +' onBlur="document.form_inclusao.submit();"'
              +' id="selector" size="5">';
    var match = false;

    for(a=0;a<what.length;a++)
    {
      if(txt.toLowerCase() == what[a][1].toLowerCase().substring(0,txt.length))
      {
        match = true;
        str += ('<option value="'+what[a][0].replace(/\'/gi,'�')+'">'+what[a][1]+'</option>')
      }
    }
    str += '</select>'
    if(match)
    {
      hld.innerHTML = str
      hld.style.display = 'block'
      var sel = document.getElementById('selector')
      if(k == '40')
      {
        sel.focus()
      }
      if(k == '13')
      {
        document.getElementById('listHolder').style.display='none'
        //boxAction(obj.value)
      }
    }
  }
  return true;
}

/********************************************************************/
//material,id_medicamento,codigo_material,id_unidade_medicamento,unidade

function checkList04(obj01,obj02,what)
{
var k = event.keyCode;
var T = findPosY(obj01);
var L = findPosX(obj01);
var hld = document.getElementById('listHolder');

  if(!hld)
  {
    var hld = document.createElement('DIV');

    hld.id = 'listHolder';
    document.body.appendChild(hld);
  }
  hld.style.top = (T + obj01.offsetHeight);
  hld.style.left = L;
  hld.style.display = 'none';
  var txt = obj01.value;

  if((txt) && (txt.length >= 3))
  {
    var str = '<select class="list" onClick="'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)"'
              +' onKeyUp="if(event.keyCode==13){'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)};'
              +'if(event.keyCode==27){'
              +'document.getElementById(\''+obj01.id+'\').focus();'
              +'document.getElementById(\'listHolder\').style.display=\'none\';}"'
              +' onBlur="document.form_argumentos.submit();"'
              +' id="selector" size="5">';
    var match = false;

    for(a=0;a<what.length;a++)
    {
      if(txt.toLowerCase() == what[a][1].toLowerCase().substring(0,txt.length))
      {
        match = true;
        str += ('<option value="'+what[a][0].replace(/\'/gi,'�')+'">'+what[a][1]+'</option>')
      }
    }
    str += '</select>'
    if(match)
    {
      hld.innerHTML = str
      hld.style.display = 'block'
      var sel = document.getElementById('selector')
      if(k == '40')
      {
        sel.focus()
      }
      if(k == '13')
      {
        document.getElementById('listHolder').style.display='none'
        //boxAction(obj.value)
      }
    }
  }
  return true;
}

/********************************************************************/

function checkListDispensacao(obj01,obj02,what)
{
var k = event.keyCode;
var T = findPosY(obj01);
var L = findPosX(obj01);
var hld = document.getElementById('listHolder');

  if(!hld)
  {
    var hld = document.createElement('DIV');

    hld.id = 'listHolder';
    document.body.appendChild(hld);
  }
  hld.style.top = (T + obj01.offsetHeight);
  hld.style.left = L;
  hld.style.display = 'none';
  var txt = obj01.value;

  if((txt) && (txt.length >= 3))
  {
    var str = '<select class="list" onClick="'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)"'
              +' onKeyUp="if(event.keyCode==13){'
              +'setOption02(\''+obj01.id+'\',\''+obj02.id+'\',this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)};'
              +'if(event.keyCode==27){'
              +'document.getElementById(\''+obj01.id+'\').focus();'
              +'document.getElementById(\'listHolder\').style.display=\'none\';}"'
              +' onBlur="document.form_inclusao.flg_autorizador.value=\'0\'; document.form_inclusao.flg_precisa_autorizador.value=\'\'; document.form_inclusao.submit();"'
              +' id="selector" size="5">';
    var match = false;

    for(a=0;a<what.length;a++)
    {
      if(txt.toLowerCase() == what[a][1].toLowerCase().substring(0,txt.length))
      {
        match = true;
        str += ('<option value="'+what[a][0].replace(/\'/gi,'�')+'">'+what[a][1]+'</option>')
      }
    }
    str += '</select>'
    if(match)
    {
      hld.innerHTML = str
      hld.style.display = 'block'
      var sel = document.getElementById('selector')
      if(k == '40')
      {
        sel.focus()
      }
      if(k == '13')
      {
        document.getElementById('listHolder').style.display='none'
        //boxAction(obj.value)
      }
    }
  }
  return true;
}

/********************************************************************/

function findPosX(obj)
{
  var curleft = 1;
  if(obj.offsetParent)
  {
    while(obj.offsetParent)
    {
      curleft += obj.offsetLeft;
      obj = obj.offsetParent;
    }
  }
  else if(obj.x)
    curleft += obj.x;
  return curleft;
}

function findPosY(obj)
{
  var curtop = 2;

  if(obj.offsetParent)
  {
    while(obj.offsetParent)
    {
      curtop += obj.offsetTop;
      obj = obj.offsetParent;
    }
  }
  else if(obj.y)
    curtop += obj.y;
  return curtop;
}

function boxAction(val)
{
  alert(val)
}

/* ###################################################### */

// func�o que autocompleta no pr�prio edit
function autocomplete(n,ac_array)
{
  if (n.value == "")
    return 0;
  if (event.keyCode == 8 && n.backspace)
  {
    n.value = n.value.substr(0,n.value.length-1);
    n.backspace = false;
  }

  var r = n.createTextRange();
  tmp = n.value;
  if (tmp == "")
    return 0;
  for (z=0;z<ac_array.length;z++)
  {
    tmp2 = ac_array[z];
    count = 0;
    for (i = 0;i<tmp.length;i++)
    {
      if (tmp2.charAt(i) == tmp.charAt(i))
      {
        count++
      }
    }
    if (count == tmp.length)
    {
      diff = tmp2.length - tmp.length;
      if (diff <= 0)
        break;
      kap = "";
      for (i=0;i<tmp2.length;i++)
      {
        if (i >= tmp.length)
          kap += tmp2.charAt(i);
      }
      n.backspace = true;
      r.text += kap;
      r.findText(kap,diff*-2);
      r.select();
      return 0;
    }
  }
  n.backspace = false;
  return 0;
}

