<?php session_start(); 
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Comissão de Exames", "comissaoExamesPorTurma");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript(); 
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;   
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #tabHorario{
      border: solid rgba(0,0,0,0.2) 2px;
    }
    #tabHorario .luzingu{
      background-color: rgba(0,0,0,0.2);
    }
    #tabHorario tr td{
      text-align: center;
      width:90px !important;
      font-size: 11pt !important;
      font-weight: 700;
    }
    .tabelaHorario tr td select{
      width: 100%;
      border: none;
      background-color: transparent;
      outline: none;
      font-size: 12pt;
    }
  </style>
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

        <?php  if($verificacaoAcesso->verificarAcesso("", "comissaoExamesPorTurma", array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);

          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>"; 
          if($_SESSION["idEscolaLogada"]==25){
            echo "<script>var classeModoDocencia=4</script>";
          }else{
             echo "<script>var classeModoDocencia=6</script>";
          }

          $periodo = retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma, $manipulacaoDados->idAnoActual);
          $semestreActivo = retornarSemestreActivo($manipulacaoDados, $idCurso, $classe);

          $listaProfessores = $manipulacaoDados->entidades(["idPEntidade", "nomeEntidade"], "docente");

          echo "<script>var listaProfessores=".json_encode($listaProfessores)."</script>";

          $divisaoProfessor = $manipulacaoDados->selectCondClasseCurso("array", "divisaoprofessores", ["idPDivisao", "idPNomeDisciplina", "idPresidenteComissaoExame", "estadoComissaoExame", "nomeDisciplina"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "nomeTurmaDiv"=>$turma, "semestre"=>$semestreActivo], $classe, ["idPNomeCurso"=>$idCurso]);
          echo "<script>var divisaoProfessor=".json_encode($divisaoProfessor)."</script>";
         ?>
                  
        <div class="row">
            <div class="col-lg-3 col-md-3 lead">
                Turma:
                <select class="form-control lead" id="luzingu">
                  <?php 
                  foreach($manipulacaoDados->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$manipulacaoDados->idAnoActual, "classe"=>array('$in'=>[6,9,12,10,11])], [], "", [], ["nomeCurso"=>1, "classe"=>1, "nomeTurma"=>1]) as $tur){

                    if($tur["classe"]==12 || $tur["classe"]==6 || $tur["classe"]==9 || ($tur["classe"]!=12 && $tur["sePorSemestre"]=="sim")){

                      echo "<option value='".$tur["nomeTurma"]."-".$tur["classe"]."-".$tur["idPNomeCurso"]."'>".$tur["abrevCurso"]." - ".classeExtensa($manipulacaoDados, $tur["idPNomeCurso"], $tur["classe"])." - ".$tur["designacaoTurma"]."</option>";
                    }
                  }


                   ?>
                </select>
            </div>
            <div class="col-lg-8 col-md-8"><br>
              <button type="button" class="btn btn-success lead btnAlterar">
                <i class="fa fa-check"></i> Alterar
              </button>
            </div>
            
        </div>

      <div class="row" id="divisaoProfessor" >
        <div class="col-lg-offset-1 col-md-offset-1 col-lg-10 col-md-10"> 
          
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                  <tr class="corPrimary">
                      <th class="lead font-weight-bolder" id="nomeDisciplinaTh"><strong><i class="fa fa-book-open"></i> Disciplina</strong></th>
                      <th class="lead"><strong><i class="fa fa-user-tie"></i> Professor</strong>
                        <select class="form-control" id="paraTodosProf" name="paraTodosProf">
                          <?php 
                          echo "<option value='-1'>Seleccionar</option>";
                          foreach($listaProfessores as $prof){
                            echo "<option value='".$prof["idPEntidade"]."'>".$prof["nomeEntidade"]."</option>";
                          }

                           ?>
                        </select>
                      </th>
                      <th class="lead text-center"><strong>Estado</strong><br>
                        <div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="totoCheckBox" class="altEstado"><span class="lever"></span></label></div>
                      </th>
                      <th class="lead"></th>
                  </tr>
                </thead>
                <tbody id="tabDivisao">
                                    
                </tbody>
            </table>
          </div>

          <div class="row">
            <div class="col-lg-12 col-md-12">
              <button type="button" class="btn btn-success lead btnAlterar">
                <i class="fa fa-check"></i> Alterar
              </button>
            </div>
          </div><br>
        </div> 
      </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>