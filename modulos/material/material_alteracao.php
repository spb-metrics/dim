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
  //  Arquivo..: material_alteracao.php
  //  Bancos...: dbtdim
  //  Data.....: 27/11/2006
  //  Analista.: Fabio Hitoshi Ide
  //  Função...: Tela de alteracao de material
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
      $sql="update material ";
      $sql.="set unidade_material_id_unidade_material=$_POST[unidade], grupo_id_grupo=$_POST[grupo], ";
      $sql.="subgrupo_id_subgrupo=$_POST[subgrupo], tipo_material_id_tipo_material=$_POST[tipo], ";
      $sql.="familia_id_familia=$_POST[familia], lista_especial_id_lista_especial=$_POST[lista], ";
      $sql.="codigo_material='$_POST[codigo_material_antigo]', descricao='" . trim(strtoupper($_POST[descricao])) . "', flg_dispensavel='$_POST[dispensavel]', ";
      $sql.="status_2='A', data_alt='$data', usua_alt='$_SESSION[id_usuario_sistema]', flg_autorizacao_disp='$_POST[autorizacao]', dias_limite_disp='$_POST[prazo]' ";
      $sql.="where id_material='$_POST[codigo_antigo]'";
      mysqli_query($db, $sql);
      erro_sql("Update Material", $db, "");

      /////////////////////////////////////
      //SE INCLUSÃO OCORREU SEM PROBLEMAS//
      /////////////////////////////////////
      $aux=$_POST[aux];
      if(mysqli_errno($db)=="0"){
        mysqli_commit($db);
        header("Location: ". URL."/modulos/material/material_inicial.php?a=t&".$aux);
      }
      else{
        mysqli_rollback($db);
        header("Location: ". URL."/modulos/material/material_inicial.php?a=f&".$aux);
      }
      exit();
    }
    else{
      if($_GET[codigo]==""){
        header("Location: ". URL."/modulos/material/material_inicial.php?".$aux);
        exit();
      }
      else{
        $sql="select codigo_material, descricao, flg_dispensavel, id_material,
              unidade_material_id_unidade_material,  dias_limite_disp,
              flg_autorizacao_disp, lista_especial_id_lista_especial,
              grupo_id_grupo, subgrupo_id_subgrupo, familia_id_familia,
              tipo_material_id_tipo_material ";
        $sql.="from material ";
        $sql.="where id_material='$_GET[codigo]'";
        $res=mysqli_query($db, $sql);
        erro_sql("Select Material Escolhido", $db, "");
        if(mysqli_num_rows($res)>0){
          $consulta=mysqli_fetch_object($res);
        }
      }
    }

    ////////////////////////////////////
    //BLOCO HTML DE MONTAGEM DA PÁGINA//
    ////////////////////////////////////
    require DIR."/header.php";
    require DIR."/buscar_aplic.php";
?>
    <script language="JavaScript" type="text/javascript" src="../../scripts/materialCombo.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/pacienteCartao.js"></script>
    <script language="JavaScript" type="text/javascript" src="../../scripts/scripts.js"></script>
    <script language="javascript">
      <!--
      
      function trataDados(){
         var info = ajax.responseText;  // obtém a resposta como string
         var texto=info.substr(0, 2);
         if (texto=="ok")
            x.submit();
         else alert(info);
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
        var y=document.form_alteracao;
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
        var x=document.form_alteracao;
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
      
      function salvarDados(){
        var x=document.form_alteracao;
        if(validarCampos()==true){
          x.flag.value="t";
          var descricao=x.descricao.value;
          var flg_integracao= x.flg_integracao.value;

          var i = 0;
          while(i < descricao.length)
          {
             descricao = descricao.replace("  "," ");
             i = i + 1;
          }
          x.descricao.value = descricao;
          if(flg_integracao=='S'){
            salvarSiga();
          }
          else x.submit();
        }
      }
      
      function salvarSiga(){
        var x=document.form_alteracao;
        var codigo=x.codigo_material_antigo.value;
        var descricao=x.descricao.value;
        descricao = descricao.replace(" ","|");
        var flg_dispensavel = x.radio_disp.value;
        var url = "../../siga/salvarMedicamentoSiga.php?codigo_material="+codigo+"&descricao="+descricao+"&flg_dispensavel="+flg_dispensavel+"&operacao=A";
        requisicaoHTTP("GET", url, true);
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
          <table name='3' cellpadding='0' cellspacing='1' border='0' width='100%' height="20%">
            <tr>
              <td colspan='8'>
                <table width="100%" cellpadding="0" cellspacing="1" border="0">
                  <form name="form_alteracao" action="./material_alteracao.php" method="POST" enctype="application/x-www-form-urlencoded">
                    <tr class="titulo_tabela" height="21">
                      <td colspan="4" valign="middle" align="center" width="100%"> <? echo $nome_aplicacao;?>: Alterar </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Código
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" name="codigo" maxlength="10" style="width: 200px" disabled value="<?php echo $consulta->codigo_material;?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="20%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Material
                      </td>
                      <td class="campo_tabela" colspan="3" valign="middle" width="100%">
                        <input type="text" id="descricao" name="descricao" maxlength="60" style="width: 500px" value="<?php echo $consulta->descricao;?>">
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
                            $sql="select * from unidade_material order by unidade";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Unidade Dispensada", $db, "");
                            while($unidade_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $unidade_info->id_unidade_material;?>" <?php if($consulta->unidade_material_id_unidade_material==$unidade_info->id_unidade_material){echo "selected";}?>> <?php echo $unidade_info->unidade;?> </option>
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
                        <input type="radio" value="S" name="dispensavel" id="dispensavel" onChange="document.form_alteracao.radio_disp.value='S'" <?php if($consulta->flg_dispensavel=="S"){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" name="dispensavel" id="dispensavel" onChange="document.form_alteracao.radio_disp.value='N'" <?php if($consulta->flg_dispensavel=="N"){echo "checked";}?>> Não
                        <input type="hidden" name="radio_disp" id="radio_disp" value="<?echo $consulta->flg_dispensavel ?>">
                        <input type="hidden" name="flg_disp" id="flg_disp" value="<?echo $consulta->flg_dispensavel ?>">
                      </td>
                    </tr>
                    <tr>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat_1.gif' BORDER='0'>
                        Período Dispensável
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="text" name="prazo" maxlength="5" style="width: 200px" onKeyPress="return isNumberKey(event);" value="<?php echo $consulta->dias_limite_disp;?>">
                      </td>
                      <td class="descricao_campo_tabela" valign="middle" width="25%">
                        <IMG SRC='<?php echo URL; ?>/imagens/obrigat.gif' BORDER='0'>
                        Necessita Autorização?
                      </td>
                      <td class="campo_tabela" valign="middle" width="25%">
                        <input type="radio" value="S" name="autorizacao" <?php if($consulta->flg_autorizacao_disp=="S"){echo "checked";}?>> Sim
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <input type="radio" value="N" name="autorizacao" <?php if($consulta->flg_autorizacao_disp!="S"){echo "checked";}?>> Não
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
                            $sql="select * from lista_especial where status_2='A' order by lista";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Lista", $db, "");
                            while($lista_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $lista_info->id_lista_especial;?>" <?php if($consulta->lista_especial_id_lista_especial==$lista_info->id_lista_especial){echo "selected";}?>> <?php echo $lista_info->lista;?> </option>
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
                            $sql="select * from grupo where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo", $db, "");
                            while($grupo_info=mysqli_fetch_object($res)){
                              $valor=$grupo_info->id_grupo . "|" . $grupo_info->flg_tipo_obrigatorio;
                          ?>
                              <option value="<?php echo $valor;?>" <?php if($consulta->grupo_id_grupo==$grupo_info->id_grupo){echo "selected";}?>> <?php echo $grupo_info->descricao;?> </option>
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
                          <?php
                            $sql="select * from subgrupo where grupo_id_grupo='$consulta->grupo_id_grupo' and status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Subgrupo Escolhido", $db, "");
                            while($sbgrupo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $sbgrupo_info->id_subgrupo;?>" <?php if($consulta->subgrupo_id_subgrupo==$sbgrupo_info->id_subgrupo){echo "selected";}?>> <?php echo $sbgrupo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
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
                          <?php
                            $sql="select * from familia where subgrupo_id_subgrupo='$consulta->subgrupo_id_subgrupo' and status_2='A'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Família Escolhida", $db, "");
                            while($familia_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $familia_info->id_familia;?>" <?php if($consulta->familia_id_familia==$familia_info->id_familia){echo "selected";}?>> <?php echo $familia_info->descricao;?> </option>
                          <?php
                            }
                          ?>
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
                            $sql="select * from grupo where id_grupo='$consulta->grupo_id_grupo'";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Grupo Obrigatório", $db, "");
                            if(mysqli_num_rows($res)>0){
                              $obrigatorio_info=mysqli_fetch_object($res);
                              $obrigatorio=$obrigatorio_info->flg_tipo_obrigatorio;
                            }

                            $sql="select * from tipo_material where status_2='A' order by descricao";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Tipo", $db, "");
                            while($tipo_info=mysqli_fetch_object($res)){
                          ?>
                              <option value="<?php echo $tipo_info->id_tipo_material;?>" <?php if($tipo_info->id_tipo_material==$consulta->tipo_material_id_tipo_material){echo "selected";}?>> <?php echo $tipo_info->descricao;?> </option>
                          <?php
                            }
                          ?>
                        </select>
                      </td>
                    </tr>
                   <tr class="campo_botao_tabela" height="35">
                      <td colspan="4" valign="middle" align="right" width="100%">
                        <input type="button" style="font-size: 12px;" name="voltar" value="<< Voltar" onclick="window.location='<?php echo URL;?>/modulos/material/material_inicial.php?pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&buscar=<?=$_GET[buscar]?>&indice=<?=$_GET[indice]?>&pesquisa=<?=$_GET['pesquisa']?>'">
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
                    <input type="hidden" name="codigo_material_antigo" value="<?php echo $consulta->codigo_material;?>">
                    <input type="hidden" name="codigo_antigo" value="<?php echo $_GET[codigo];?>">
                    <? $sql="select integracao from parametro";
                            $res=mysqli_query($db, $sql);
                            erro_sql("Select Parametro", $db, "");
                            if($param_integracao=mysqli_fetch_object($res)){
                                 $integracao = strtoupper($param_integracao->integracao);
                            }
                     ?>
                    <input type="hidden" name="flg_integracao" value="<?php echo $integracao;?>">
                    <input type="hidden" name="flag" value="f">
                    <input type="hidden" name="flag_obrigatorio" id="flag_obrigatorio" value="<?php echo $obrigatorio;?>">
                    <input type="hidden" id="aux" name="aux" value="pagina=<?=$_GET[pagina]?>&pagina_a_exibir=<?=$_GET[pagina_a_exibir]?>&indice=<?=$_GET[indice]?>&buscar=<?=$_GET[buscar]?>&pesquisa=<?=$_GET['pesquisa']?>">
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
      var x=document.form_alteracao;
      x.descricao.focus();
    //-->
    </script>

<?php
  }

  ////////////////////////////////////////////
  //SE NÃO ENCONTRAR ARQUIVO DE CONFIGURAÇÃO//
  ////////////////////////////////////////////
  else
  {
    include_once "../../config/erro_config.php";
  }
?>
