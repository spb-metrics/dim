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
  //  Arquivo..: material_inclusao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de inclusao de material
  //////////////////////////////////////////////////////////////////

  //////////////////////////////////////////////////
  //TESTANDO EXISTÊNCIA DE ARQUIVO DE CONFIGURAÇÃO//
  //////////////////////////////////////////////////
  if (file_exists("../../config/config.inc.php")){
    require "../../config/config.inc.php";
  
    ////////////////////////////
    //VERIFICAÇÃO DE SEGURANÇA//
    ////////////////////////////

    if($_SESSION[id_usuario_sistema]==''){
      header("Location: ". URL."/start.php");
      exit();
    }


    if($_POST[flag]=="t"){
      if($_POST[unidade]==""){
        $_POST[unidade]="null";
      }
      if($_POST[grupo]==""){
        $_POST[grupo]="null";
      }
      else{
        $grupo_info=split("[|]", $_POST[grupo]);
        $_POST[grupo]=$grupo_info[0];
      }
      if($_POST[subgrupo]==""){
        $_POST[subgrupo]="null";
      }
      if($_POST[tipo]==""){
        $_POST[tipo]="null";
      }
      if($_POST[familia]==""){
        $_POST[familia]="null";
      }
      if($_POST[lista]==""){
        $_POST[lista]="null";
      }
      $data=date("Y-m-d H:i:s");
      $sql="insert into material (unidade_material_id_unidade_material, grupo_id_grupo, subgrupo_id_subgrupo, tipo_material_id_tipo_material, familia_id_familia, lista_especial_id_lista_especial, codigo_material, descricao, flg_dispensavel, status_2, data_incl, usua_incl, flg_autorizacao_disp, dias_limite_disp) ";
      $sql.="values ($_POST[unidade], $_POST[grupo], $_POST[subgrupo], $_POST[tipo], $_POST[familia], $_POST[lista], '$_POST[codigo]', '" . strtoupper($_POST[descricao]) . "', '$_POST[dispensavel]', 'A', '$data', '$_SESSION[id_usuario_sistema]', '$_POST[autorizacao]', '$_POST[prazo]')";
      mysqli_query($db, $sql);
      erro_sql("Insert Material", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      if(mysqli_errno($db)=="0"){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/material/material_inicial.php?i=t");
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/material/material_inicial.php?i=f");
      }
      exit();
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/materialCombo.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      function trataDados(){
          var x=document.form_inclusao;
	      var info = ajax.responseText;  // obtém a resposta como string
          info=info.substr(0, 3);
          
          if (info=='ok')
          {
            x.submit();
          }
          else if (info=='er|')
          {
           alert ('erro');
          }
          
	      if(info=="SAV"){
            x.flag.value="t";
            var flg_disp= '';
            var flg_integracao = '';
            for( i = 0; i < x.dispensavel.length; i++ )
            {
               if(x.dispensavel[i].checked == true )
               flg_disp = x.dispensavel[i].value;
            }
            flg_integracao= x.flg_integracao.value;
            if(flg_integracao=='S')
            {
               salvarSiga();
              // x.submit();
            }
            else
            {
               x.submit();
            }
        }
        else
          {
            if(info=="NAO"){
              var msg="Material já cadastrado!\n";
              window.alert(msg);
              x.codigo.focus();
              x.codigo.select();
            }
            
            var texto=info.substr(0, 2);
            if (texto=="er")
               alert(info);
         }
      }

      function salvarSiga(){
        var x=document.form_inclusao;
        var codigo=x.codigo.value;
        var descricao=x.descricao.value;
        descricao = descricao.replace(" ","|");

        var url = "../../siga/salvarMedicamentoSiga.php?codigo_material="+codigo+"&descricao="+descricao+"&operacao=I";
        requisicaoHTTP("GET", url, true);
      }
      
      function verificarCodigo(){
        var x=document.form_inclusao;
        var codigo=x.codigo.value;
        var url = "../../xml/materialCodigo.php?codigo=" + codigo;
        requisicaoHTTP("GET", url, true);
      }

      function salvarDados(){
        var i=0;
        var descricao=x.descricao.value;
        while(i < descricao.length)
        {
            descricao = descricao.replace("  "," ");
            i = i + 1;
        }
        x.descricao.value = descricao;
        
        if(validarCampos()==true){
          verificarCodigo();
        }
      }
      
      function removerFamilia(){
        var x=document.getElementById("familia");
        var tam=x.length;
        for(var i=tam; i>=0; i--){
          x.remove(i);
        }
        var elOptNew=document.createElement('option');
        elOptNew.text="Selecione uma Família";
        elOptNew.value="";
        elOptNew.id="opcao_familia";
        var elSel=document.getElementById('familia');
        try{
          elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
        }
        catch(ex){
          elSel.add(elOptNew); // IE only
        }
      }

      function removerSubgrupo(){
        var x=document.getElementById("subgrupo");
        var tam=x.length;
        for(var i=tam; i>=0; i--){
          x.remove(i);
        }
        var elOptNew=document.createElement('option');
        elOptNew.text="Selecione um Sub-Grupo";
        elOptNew.value="";
        elOptNew.id="opcao_subgrupo";
        var elSel=document.getElementById('subgrupo');
        try{
          elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
        }
        catch(ex){
          elSel.add(elOptNew); // IE only
        }
      }
      
      function carregarCombo(comb_pai, comb, op_comb, file, flg_group, flg_subgroup, flg_obrig){
        var x=document.getElementById(flg_obrig);
        var y=document.form_inclusao;
        if(flg_group=="t" && y.grupo.selectedIndex==0){
          x.value="";
          removerSubgrupo();
          removerFamilia();
        }
        if(flg_group=="t" && y.grupo.selectedIndex!=0){
          removerFamilia();
          carregarCombos(comb_pai, comb, op_comb, file, flg_obrig);
        }
        if(flg_subgroup=="t" && y.subgrupo.selectedIndex==0){
          removerFamilia();
        }
        if(flg_subgroup=="t" && y.subgrupo.selectedIndex!=0){
          carregarCombos(comb_pai, comb, op_comb, file, flg_obrig);
        }
      }
      
      ///////////////////////////////////////////
      //Validacao de campo obrigatorio:        //
      ///////////////////////////////////////////
      function validarCampos(){
        var x=document.form_inclusao;
        var cod=x.codigo;
        var descr=x.descricao;
        var unid=x.unidade;
        var flg=x.flag;
        var disp=x.dispensavel;
        var group=x.grupo;
        var subgroup=x.subgrupo;
        var family=x.familia;
        var tip=x.tipo;
        var flg2=x.flag_obrigatorio;
        
        if(cod.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          cod.focus();
          cod.select();
          return false;
        }
        if(descr.value==""){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          descr.focus();
          descr.select();
          return false;
        }
        if(unid.selectedIndex==0){
          window.alert("Favor Preencher os Campos Obrigatórios!");
          unid.focus();
          return false;
        }
        if(disp[0].checked && (group.selectedIndex==0 || subgroup.selectedIndex==0 || family.selectedIndex==0)){
          window.alert("Favor Preencher os Campos Grupo, Sub-Grupo e Família!");
          if(group.selectedIndex==0){
            group.focus();
            return false;
          }
          if(subgroup.selectedIndex==0){
            subgroup.focus();
            return false;
          }
          if(family.selectedIndex==0){
            family.focus();
            return false;
          }
        }
        if(tip.selectedIndex==0 && flg2.value=="S"){
          window.alert("Favor Preencher o Campo Tipo!");
          tip.focus();
          return false;
        }
        return true;
      }
      //-->
    </script>
    <table width="100%" height="100%" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="left">
          <table width="100%" class="caminho_tela" border="0" cellpadding="0" cellspacing="0">
            <tr><td><?php echo $caminho;?></td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <td height="100%" align="center" valign="top">
          <table name='3' cellpadding='0' cellspacing='0' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_inclusao" action="./material_inclusao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Incluir </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" onKeyPress="return isNumberKey(event);">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="75%">
                        <input type="text" name="descricao" maxlength="60" style="width: 500px">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Unidade Dispensada
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <select name="unidade" size="1" style="width: 200px">
                          <option value=""> Selecione uma Unidade </option>
                          <?php
                            $sql="select id_unidade_material, unidade from unidade_material order by unidade";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Unidade Dispensada", $db, "");
                            while($unidade_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $unidade_info->id_unidade_material;?>"> <?php echo $unidade_info->unidade;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Dispensável
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="radio" value="S" name="dispensavel" checked> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" name="dispensavel"> Não
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Período Dispensável
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="prazo" maxlength="5" style="width: 200px" onKeyPress="return isNumberKey(event);">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Necessita Autorização?
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="radio" value="S" name="autorizacao"> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" name="autorizacao" checked> Não
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Lista
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="lista" size="1" style="width: 200px">
                          <option value=""> Selecione uma Lista </option>
                          <?php
                            $sql="select id_lista_especial, lista from lista_especial where status_2='A' order by lista";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Lista", $db, "");
                            while($lista_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $lista_info->id_lista_especial;?>"> <?php echo $lista_info->lista;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Grupo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="grupo" id="grupo" size="1" style="width: 200px" onchange="carregarCombo('grupo', 'subgrupo', 'opcao_subgrupo', '../../xml/materialCombo.php', 't', 'f', 'flag_obrigatorio');">
                          <option value=""> Selecione um Grupo </option>
                          <?php
                            $sql="select id_grupo, flg_tipo_obrigatorio, descricao from grupo where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                              $valor=$grupo_info->id_grupo . "|" . $grupo_info->flg_tipo_obrigatorio;
                          ?>
                              <option value="<?php echo $valor;?>"> <?php echo $grupo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Subgrupo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="subgrupo" id="subgrupo" size="1" style="width: 200px" onchange="carregarCombo('subgrupo', 'familia', 'opcao_familia', '../../xml/materialCombo.php', 'f', 't', '');">
                          <option value="" id="opcao_subgrupo"> Selecione um Sub-Grupo </option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Família
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="familia" id="familia" size="1" style="width: 200px">
                          <option value="" id="opcao_familia"> Selecione uma Família </option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Tipo
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <select name="tipo" size="1" style="width: 200px">
                          <option value=""> Selecione um Tipo </option>
                          <?php
                            $sql="select id_tipo_material, descricao from tipo_material where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Tipo", $db, "");
                            while($tipo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $tipo_info->id_tipo_material;?>"> <?php echo $tipo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="35">
                      <td colspan="4"valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/material/material_inicial.php'">
                        <input type="button" name="salvar" style="font-size: 12px;" value="Salvar >>" onclick="salvarDados();">
                      </td>
                    </tr>
                    <tr class="campo_botao_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'> Campos Obrigatórios
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'> Campos Não Obrigatórios
                      </td>
                    </tr>
                    <? $sql="select integracao from parametro";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Parametro", $db, "");
                            if($param_integracao=mysqli_fetch_object($res)){
                                 $integracao = strtoupper($param_integracao->integracao);
                            }
                     ?>
                    <input type="hidden" name="flg_integracao" value="<?php echo $integracao;?>">
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="flag_obrigatorio" id="flag_obrigatorio">
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
      if(x.codigo.value==""){
        x.codigo.focus();
      }
      else{
        if(x.descricao.value==""){
          x.descricao.focus();
        }
        else{
          if(x.grupo.selectedIndex==0){
            x.grupo.focus();
          }
          else{
            if(x.subgrupo.selectedIndex==0){
              x.subgrupo.focus();
            }
            else{
              if(x.familia.selectedIndex==0){
                x.familia.focus();
              }
            }
          }
        }
      }
    //-->
    </script>

<?php
  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  }
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
