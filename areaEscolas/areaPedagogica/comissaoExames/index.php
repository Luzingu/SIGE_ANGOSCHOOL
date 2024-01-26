<?php session_start();  
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Comissão de Exames", "comissaoExames");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
      <div class="row" >
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-users"></i> Comissão de Exames</strong></h1>
           
          
        </nav>
      </div>
    </div>
      <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "comissaoExames", array(), "msg")){


          $idPNomeDisciplina = isset($_GET["idPNomeDisciplina"])?$_GET["idPNomeDisciplina"]:$manipulacaoDados->selectUmElemento("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION["idEscolaLogada"], "disciplinas.classeDisciplina"=>['$in'=>array(6, 9, 12)]], ["disciplinas"]);
          echo "<script>var idPNomeDisciplina='".$idPNomeDisciplina."'</script>";
         ?>
                  
        <div class="row">
            <div class="col-lg-6 col-md-6 lead">
              Disciplina
                <select class="form-control lead" id="idPNomeDisciplina">
                  <?php 
                  foreach ($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION["idEscolaLogada"], "disciplinas.classeDisciplina"=>['$in'=>array(6, 9, 12)]], ["disciplinas"]) as $disciplina) {
                  
                    echo "<option value='".$disciplina["_id"]."'>".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$disciplina["_id"]])."</option>";
                  } ?>
                </select>
            </div>
            <div class="col-lg-6 col-md-6"><br>
             <button type="button" class="btn btn-success lead" id="btnAlterar">
               <i class="fa fa-check"></i> Alterar
             </button>
           </div>         
            
        </div>
        
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead class="corPrimary">
                <tr class="corPrimary">
                  <th class="lead font-weight-bolder"><strong>Turma</strong></th>
                  <th class="lead"><strong>Presidente da Comissão</strong></th>
                  <th class="lead text-center"><strong>Estado</strong><br>
                    <div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="totoCheckBox" class="altEstado"><span class="lever"></span></label></div>
                  </th>
                </tr>
            </thead>
            <tbody id="tabDivisao">
                  <?php
                  $entidades = $manipulacaoDados->entidades(["idPEntidade", "nomeEntidade"], "docente");

                  $array = $manipulacaoDados->selectArray("divisaoprofessores", ["idPresidenteComissaoExame", "estadoComissaoExame", "designacaoTurmaDiv", "classe", "abrevCurso", "idPDivisao"], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$manipulacaoDados->idAnoActual, "idPNomeDisciplina"=>$idPNomeDisciplina, "classe"=>['$in'=>array(6, 9, 12)]]);

                  foreach($array as $a){

                    $lDia="";
                    if($a["classe"]>=10){
                      $lDia = $a["abrevCurso"]." - ";
                    }
                    $lDia .= $a["classe"].".ª - ".$a["designacaoTurmaDiv"];
                    echo "<tr id='".$a["idPDivisao"]."'><td class='lead'>".$lDia."</td><td class='lead'><select class='idPresidenteComissaoExame form-control lead' style='font-weight:bolder; font-size:14pt;'>
                    <option value=''>Seleccionar</option>";
                    echo listaDeFuncionarios($entidades, $a["idPresidenteComissaoExame"]);
                    echo "</select></td>";

                    $checked="";
                    if($a["estadoComissaoExame"]=="V"){
                      $checked="checked";
                    }
                    echo '<td class="text-center"><div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-recurso" '.$checked.' class="altEstado"><span class="lever"></span></label></div></td></tr>';
                  }
                   ?>
            </tbody>
        </table>
      </div><br>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); 

  function listaDeFuncionarios($entidades, $idPresidenteComissaoExame){

    $retorno="";
    foreach($entidades as $a){
      $selected="";
      if($a["idPEntidade"]==$idPresidenteComissaoExame){
          $selected="selected";
      }
      $retorno .="<option ".$selected." value='".$a["idPEntidade"]."'>".$a["nomeEntidade"]."</option>";
    }
    return $retorno;
  }


?>