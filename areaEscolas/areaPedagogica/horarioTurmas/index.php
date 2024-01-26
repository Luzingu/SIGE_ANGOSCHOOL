<?php session_start(); 
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Horário de Turmas", "horarioTurmas");
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
              <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-border-all"></i> Horário das Turmas</strong></h1>
        </nav>
      </div>
    </div>
    <div class="main-body">

        <?php  if($verificacaoAcesso->verificarAcesso("", ["horarioTurmas"],array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);

          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";
          echo "<script>var classeModoDocencia=4</script>";

          $periodo = retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma, $manipulacaoDados->idAnoActual);
          $semestreActivo = retornarSemestreActivo($manipulacaoDados, $idCurso, $classe);

          $horario = $manipulacaoDados->selectArray("horario", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "turma"=>$turma, "semestre"=>$semestreActivo, "idPNomeCurso"=>$idCurso]);
          
          echo "<script>var horario =".json_encode($horario)."</script>";
          echo "<script>var listaProfessores=".json_encode($manipulacaoDados->entidades(["idPEntidade", "nomeEntidade"], "docente"))."</script>";

          $divT = $manipulacaoDados->selectArray("divisaoprofessores", ["idPDivisao", "idPNomeDisciplina", "idPEntidade", "avaliacoesContinuas", "nomeDisciplina"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "nomeTurmaDiv"=>$turma, "semestre"=>$semestreActivo, "idPNomeCurso"=>$idCurso]);

          $disciplinas = $manipulacaoDados->disciplinas($idCurso, $classe, $periodo, "", array(), [51, 140], ["idPNomeDisciplina", "abreviacaoDisciplina2"]);

          $divisaoProfessor = array();
          foreach($divT as $d)
          {
            if (count(array_filter($disciplinas, function ($mamale) use ($d){
              return ($mamale["idPNomeDisciplina"]==$d["idPNomeDisciplina"]) ;
            })) > 0)
              $divisaoProfessor[] = $d;
          }

          echo "<script>var divisaoProfessor=".json_encode($divisaoProfessor)."</script>";

          $periodoT = $manipulacaoDados->selectUmElemento("listaturmas", "periodoT", ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "nomeTurma"=>$turma, "idPNomeCurso"=>$idCurso]);

          $gerenciador_periodo = listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "gerencPerido", ["idGerPerAno=".$manipulacaoDados->idAnoActual, "periodoGerenciador=".$periodoT]); 

          $optionDisciplinas="<option value=''>Tempo Livre</option>";
          foreach ($disciplinas as $disciplina) {
            $optionDisciplinas .= "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["abreviacaoDisciplina2"]."</option>";
          }

         ?>
                  
        <div class="row">
            <div class="col-lg-4 col-md-4 lead">
                Turma:
                <select class="form-control lead" id="luzingu">
                  <?php optTurmas($manipulacaoDados); ?>
                </select>
            </div>
            <div class="col-lg-8 col-md-8"><br>
              <a href="#" class="lead btn-primary btn" id="actualizar"><i class="fa fa-refresh fa-1x"></i> Actualizar</a>&nbsp;&nbsp;&nbsp;
              <a href="#" id="visualizarHorario" class="lead  btn-primary btn"><i class="fa fa-print"></i> Visualizar</a>
              &nbsp;&nbsp;&nbsp;
              <button type="button" class="btn btn-success lead btnAlterar">
                <i class="fa fa-check"></i> Alterar
              </button>
            </div>
            
        </div>

          <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover tabelaHorario" id="tabHorario" style="display: none;">
            <thead>
                <tr class="corPrimary">
                    <th class="lead text-center"><strong>Tempo</strong></th>

                    <?php for($dia=1; $dia<=valorArray($gerenciador_periodo, "numeroDias"); $dia++){

                        echo '<th class="lead text-center"><strong><i class="fa fa-sun"></i> '.diaSemana2($dia).'</strong></th>';
                      
                    } ?>

                </tr>
            </thead>
            <tbody>
              <?php 

                for($tempo=1; $tempo<=valorArray($gerenciador_periodo, "numeroTempos"); $tempo++){

                  echo '<tr><td class="lead text-center">'.$tempo.'º</td>';
                  for($dia=1; $dia<=valorArray($gerenciador_periodo, "numeroDias"); $dia++){
                    echo '<td class="lead valor text-center" ><select class="text-center" id="t'.$tempo.$dia.'" identificador="'.$tempo."-".$dia.'">'.$optionDisciplinas.'</select></td>';
                  }
                  echo '</tr>';

                  if($tempo==valorArray($gerenciador_periodo, "intevaloDepoisDoTempo")){
                      echo '<tr><td class="lead text-center luzingu" colspan="'.(1+(int)valorArray($gerenciador_periodo, "numeroDias")).'"><strong>Intervalo</strong></td></tr>';
                  }
                }
              ?>
            </tbody>
        </table>
      </div>

      <div class="row" id="divisaoProfessor" >
        <div class="col-lg-offset-1 col-md-offset-1 col-lg-10 col-md-10"> 
          
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                  <tr class="corPrimary">
                      <th class="lead font-weight-bolder" id="nomeDisciplinaTh"><strong><i class="fa fa-book-open"></i> Disciplina</strong></th>
                      <th class="lead"><strong><i class="fa fa-user-tie"></i> Professor</strong></th>
                      <th class="lead text-center"><strong>Av. Cont.</strong><br>
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