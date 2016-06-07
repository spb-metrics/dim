<?php
/* 
	Copyright 2011 Informática de Municípios Associados
	Este arquivo é parte do programa DIM
	O DIM é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão 2 da Licença.
	Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
	Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA.
*/

session_start();




  $sql = "select caminho_logo_empresa, caminho_logo_dim, pagina_default, versao
          from
                 parametro";
      //echo $sql;
  $p = mysqli_query($db, $sql);
  while($res=mysqli_fetch_array($p))
  {
   //pegar caminho dos logos
   $logo_empresa = $res['caminho_logo_empresa'];
   $logo_dim = $res['caminho_logo_dim'];
   
   //pegar nome da página default
   if ($res['pagina_default']=='')
   {
    $exe1 = '';
   }
   else
   {
    $exe1 = $res[pagina_default];
   }
   //pegar versão do software
   if ($res['versao']=='')
   {
    $versao = '';
   }
   else
   {
    $versao = $res['versao'];
   }
  }
//  echo "XX".$exe;
//////////
//HEADER//
//////////

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

function lista($perfil, $idpai, $db){
         $sql = "select  distinct
                    m.id_item_menu,
                    m.item_menu_id_item_menu,
                    m.descricao,
                    m.aplicacao_id_aplicacao,
                    a.executavel
                  from
                  	item_menu m,
                    aplicacao a,
                    perfil_has_aplicacao pa
                  where
                  	m.item_menu_id_item_menu = '$idpai'
                   	and a.status_2 = 'A'
                   	and m.status_2 = 'A'
                    and pa.perfil_id_perfil =  '$perfil'
                    and m.aplicacao_id_aplicacao = a.id_aplicacao
                    and a.id_aplicacao = pa.aplicacao_id_aplicacao
                  order by ordem";
    //echo $sql.'<br>';
    $z = mysqli_query($db, $sql);

    if(mysqli_num_rows($z)!=0){
       echo "<ul>";
        while($res=mysqli_fetch_array($z)){
          if ($res['executavel']!='')
          {
           $exe = $res['executavel'];
          }
          else
          {
           $exe= $exe1;
          }
          
          echo "<li><a href='".URL. $exe."?aplicacao=".$res['aplicacao_id_aplicacao']."' style='border-bottom:1px solid black'>".$res['descricao']."</a></li>";
          lista($perfil, $res["id_item_menu"], $db);
        }
       echo "</ul>";
    }
}
/////////////////////////////////
//VERIFICANDO SE SESSÃO EXPIROU//
/////////////////////////////////
if($_SESSION[id_usuario_sistema]!='')
{
   $tamanho_tabela = 850;

   //descobrindo o numero de niveis
   $sql = "select distinct nivel from item_menu where nivel is not null";
   $a = mysqli_query($db, $sql);
   $num_niveis = mysqli_num_rows($a);

  // echo $num_niveis.'<br>';

   $sql= "select
    	im.id_item_menu,
     	im.descricao,
     	ap.executavel
      from
      	perfil_has_aplicacao pa,
       	item_menu im,
        aplicacao ap
      where
      	pa.aplicacao_id_aplicacao = ap.id_aplicacao
       	and ap.id_aplicacao = im.aplicacao_id_aplicacao
        and pa.perfil_id_perfil = '$_SESSION[id_perfil_sistema]'
        and im.status_2 = 'A'
        and im.item_menu_id_item_menu = 0
        and im.bloqueado = 'N'
      order by ordem";
   $x = mysqli_query($db, $sql);
   //echo $sql.'<br>';
   $user_agent = $_SERVER["HTTP_USER_AGENT"]; //identifica browser
   $flg = explode("/",$user_agent);

/////////////////////////////
//DEFININDO MENU A EXECUTAR//
/////////////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
  <TITLE><?php echo TIT;?></TITLE>
  <?
  //echo "<script> alert('".URL."'); </script>"; 
  
  ?>
  
  <link href="<?php echo CSS;?>" rel="stylesheet" type="text/css">

  <script type="text/javascript" src="<?php echo URL;?>/scripts/scripts.js"></script>

<?php
  if ($flg[2]==''){$flg_b=0;}else{$flg_b=8;} //seta a variavel de acordo com o browser ie=3 mozilla=9;
	$num_cols = mysqli_num_rows($x)+1;
	$tamanho_coluna = floor ((850/$num_cols)-$flg_b); //calcula o tamanho da coluna do menu
  
	$tam_sair =  $tamanho_coluna +(850 -($num_cols * ($tamanho_coluna+6)+6 ) - ($num_cols + 1));
	//$tam_sair =  $tamanho_coluna  - ($num_cols );
?>

<script>
if(navigator.appName.indexOf('Internet Explorer')>0){
  alert ('Pagina Bloqueada! Utilize o Navegador FireFox para acesso ao Sistema!');
      window.open('./atualize_navegador.php', '_self');
     }else{
     }
</script>

 <style type="text/css">

.treemenustyle ul{
margin: 0;
padding: 0;
list-style-type: none;
}

/*Menu superior*/
.treemenustyle ul li{
position: relative;
display: inline;
float: left;
background-color: #E9E9E9 ; /*overall menu background color*/
font-family: verdana, arial, sans_serif;
font-size: 11px;
}

/*Menu superior */
.treemenustyle ul li a{
display: block;
width: <?php echo $tamanho_coluna."px";?>;
padding: 5px 1px 5px 5px;   /*top left button right*/
border: 1px solid black;
border-right-width:0px;
text-decoration: none;
color: black;
}

#sair {
display: block;
width: <?php echo $tam_sair."px";?>;
padding: 5px 1px 5px 5px;   /*top left button right*/
border: 1px solid black;
border-right-width:1px;
text-decoration: none;
color: black;
}


/*1st sub level menu*/
.treemenustyle ul li ul{
left: 0;
position: absolute;
top: 1em; /* Qualquer necessidade de mudança, como verdadeiro valor fixado pelo script */
display: block;
visibility: hidden;
}

/*Sub menu nível lista itens (undo estilo de nível superior Lista Itens)*/
.treemenustyle ul li ul li{
display: list-item;
/*mudei aki float: none;*/
}

/*Todos posterior sub menu níveis compensado após 1 º nível sub menu */
.treemenustyle ul li ul li ul{
left: <?php echo $tamanho_coluna."px";?>; /*120px;*/ /* Qualquer necessidade de mudança, como verdadeiro valor fixado pelo script */
top: 0;
}


/* Sub level menu links style */
.treemenustyle ul li ul li a{
display: block;
width: <?php echo $tamanho_coluna."px";?>;/*120px;*/ /*width of sub menu levels*/
color: black;
text-decoration: none;
padding: 5px 1px 5px 5px;
border: 1px solid black;
border-bottom-width:0px;
}

.treemenustyle ul li a:hover{
background-color: #D0D0D0;
color: black;
}


* html p#iepara{ /*Para um número (se houver) que segue imediatamente suckertree menu, adicione 1em top espaçamento entre os dois no IE*/
padding-top: 1em;
}

/* Holly Hack for IE \*/
* html .treemenustyle ul li { float: left; height: 1%; }
* html .treemenustyle ul li a { height: 1%; }
/* End */

////// FORMAT MENU  ///////////////////////////////////////////////////////////////////
orientation          = "horizontal" // Orientation of menu.  (horizontal, vertical)
cellPadding          = 4            // Cell Padding
cellBorder           = 1            // Include table border (for no border, enter 0)
verticalOffset       = 0            // Vertical offset of Sub Menu. (if set to 0, default offset will be used)
horizontalOffset     = 1            // Horizontal offset of Sub Menu. (if set to 0, default offset will be used)
subMenuDelay         = 1            // Time sub menu stays visible for (in seconds)

// Main Menu Items
borderColor          = "#000000"    // Border Colour
menuBackground       = "#E9E9E9"    // Cell Background Colour
menuHoverBackground  = "#C0C0C0"    // Cell Background Colour on mouse rollover
fontFace             = "arial"     // Font Face
fontColour           = "#333333"    // Font Colour
fontHoverColour      = "#333333"    // Font Colour on mouse rollover
fontSize             = "12px"       // Font Size
fontDecoration       = "none"       // Style of the link text (none, underline, overline, line-through)
fontWeight           = "normal"     // Font Weight (normal, bold)

// Sub Menu Items
sborderColor         = "#808080"    // Border Colour
smenuBackground      = "#E9E9E9"    // Cell Background Colour
smenuHoverBackground = "#C0C0C0"    // Cell Background Colour on mouse rolloverr
sfontFace            = "arial"     // Font Face
sfontColour          = "#333333"    // Font Colour
sfontHoverColour     = "#333333"    // Font Colour on mouse rollover
sfontSize            = "12px"       // Font Size
sfontDecoration      = "none"       // Style of the link text (none, underline, overline, line-through)
sfontWeight          = "normal"     // Font Weight (normal, bold)


</style>

<script type="text/javascript">
var menuids=["treemenu1"] //Digite id (s) de SuckerTree UL menus, separados por vírgulas

function buildsubmenus_horizontal()
{
 for (var i=0; i<menuids.length; i++)
 {
  var ultags=document.getElementById(menuids[i]).getElementsByTagName("ul")
    for (var t=0; t<ultags.length; t++)
    {
		if (ultags[t].parentNode.parentNode.id==menuids[i])
        { //Se este é um primeiro nível submenu
			ultags[t].style.top=ultags[t].parentNode.offsetHeight+"px" //Dinamicamente posição primeiro nível submenus a ser altura do menu principal item
			ultags[t].parentNode.getElementsByTagName("a")[0].className="mainfoldericon"
		}
		else
        { //Então se esse é um nível sub menu (ul)
		  ultags[t].style.left=ultags[t-1].getElementsByTagName("a")[0].offsetWidth+"px" //Posição menu à direita do item do menu que é activado
    	  ultags[t].parentNode.getElementsByTagName("a")[0].className="subfoldericon"
		}
        ultags[t].parentNode.onmouseover=function(){
        this.getElementsByTagName("ul")[0].style.visibility="visible"
    }
    ultags[t].parentNode.onmouseout=function(){
    this.getElementsByTagName("ul")[0].style.visibility="hidden"
  }
    }
  }
}

if (window.addEventListener)
window.addEventListener("load", buildsubmenus_horizontal, false)
else if (window.attachEvent)
window.attachEvent("onload", buildsubmenus_horizontal)

</script>

</HEAD>
<BODY>

      <div id="carregador_pai" class="carregador_pai" style="display:none">
        <div id="carregador" class="carregador">
          <div align="center">Aguarde! Processando ...</div>
          <div id="carregador_fundo" class="carregador_fundo">
            <div id="barra_progresso" class="barra_progresso"></div>
          </div>
        </div>
      </div>

      <table width="<?php echo $tamanho_tabela;?>" style="height:3%;" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td rowspan="2" align="center" width="15%">
          <? if ($logo_empresa!='')
          { ?>
            <img src="<? echo URL.$logo_empresa;?>" alt="Imagem">
          <? }?>
          </td>

          <td rowspan="2" valign="middle" align="center" width="70%">
            <font class="cabecalho">
              Unidade : <?php echo $_SESSION[nome_unidade_sistema];?>
            </font><br><br>
            <font class="cabecalho">
              Usuário : <?php echo $_SESSION[nome_usuario_sistema];?>
            </font>
          </td>

          <td rowspan="2" align="center" width="15%">
          <? if ($logo_dim!='')
          { ?>
            <img src="<? echo URL.$logo_dim;?>" alt="Imagem">
          <?}?>
          </td>
        </tr>

      </table>
      <!-- <table width="< ?php echo $tamanho_tabela;?>" style="height:80%;" border="1" align="center" cellpadding="1" cellspacing="1">-->
      <table  style="height:80%;" border="0" align="center" cellpadding="1" cellspacing="1">
        <tr>
          <td align="center" valign="bottom" width="100%">
           <div class="treemenustyle">
            <ul id="treemenu1">
             <?php
             while($menu_info= mysqli_fetch_object($x))
             {
              if ($menu_info->executavel!='')
              {
               $exe= $menu_info->executavel;
              }
              else
              {
               $exe= $exe1;
              }

               ?>
              <li ><a href="<?php echo URL. $exe;?>" ><?php echo $menu_info->descricao;?></a>
              <?php
              $sql = "select  distinct
                    m.id_item_menu,
                    m.item_menu_id_item_menu,
                    m.descricao,
                    m.aplicacao_id_aplicacao,
                    a.executavel
                  from
                  	item_menu m,
                    aplicacao a,
                    perfil_has_aplicacao pa
                  where
                  	m.item_menu_id_item_menu = '$menu_info->id_item_menu'
                   	and a.status_2 = 'A'
                   	and m.status_2 = 'A'
                    and pa.perfil_id_perfil =  '$_SESSION[id_perfil_sistema]'
                    and m.aplicacao_id_aplicacao = a.id_aplicacao
                    and a.id_aplicacao = pa.aplicacao_id_aplicacao
                  order by ordem";
                  //echo $sql;
                  $y = mysqli_query($db, $sql);
                  if (mysqli_num_rows($y)!=0)
                   {?>
                    <ul>
                    <?
                    while($menu_info1= mysqli_fetch_object($y))
                      {
                       if ($menu_info1->executavel!='')
                       {
                        $exe = $menu_info1->executavel;
                       }
                       else
                       {
                        $exe= $exe1;
                       }


                      ?>
                       <li><a href="<?php echo URL. $exe.'?aplicacao='.$menu_info1->aplicacao_id_aplicacao ;?>" style="border-bottom:1px solid black" ><?php echo $menu_info1->descricao;?></a>
                       <?php
                       lista($_SESSION[id_perfil_sistema], $menu_info1->id_item_menu, $db)
                        //echo $sql.'<br>';
                       ?>
                    </li>
                   <?}?>
                  </ul>
                 <?}?>
              </li>
           <?}?>
             <li><a id="sair"  href="<?php echo URL;?>/desconectar.php?>"  >Sair</a>
             </li>
            </ul>
           </div>
          </td>

       </tr>

        <tr>
          <td colspan="4" height="100%">

<?
}
/////////////////////////////
//SE A SESSÃO ESTÁ EXPIRADA//
/////////////////////////////
else
{
  $_SESSION["EXPIRADO"] = "sim";
  $_SESSION["MSG_LOGIN"] = "Sessão expirada ou acesso indevido. Por favor, efetue o login novamente";
  header("Location: ". URL."/desconectar.php");
}
?>
