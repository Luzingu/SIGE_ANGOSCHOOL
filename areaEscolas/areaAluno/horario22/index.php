<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Horário da Turma", "horario");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea =1;
    $layouts->designacaoArea ="Área do Aluno";
    $manipulacaoDados->retornarAnosEmJavascript();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
      .infoTurma h3{
      margin-bottom: -20px;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(1);
  ?>

  <section id="main-content"> 
    <section class="wrapper" id="containers">
      <div class="row">
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-table"></i> HORÁRIO</h1>
          </nav>
        </div>
      </div>

    <div class="main-body">

        <?php  if($verificacaoAcesso->verificarAcesso(1)){


          echo "<script>var classeActualAluno=".valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola").";

          var turmaAluno='".valorArray($manipulacaoDados->sobreTurmaActualAluno, "nomeTurma")."'; var idCursoAluno=".valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola")."; </script>";

            $gerenciador_periodo = listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "gerencPerido", ["idGerPerAno=".$manipulacaoDados->idAnoActual, "periodoGerenciador=".valorArray($manipulacaoDados->sobreTurmaActualAluno, "periodoT")]);
         ?>
        
          <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="tabHorario">
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

                  echo '<tr><td class="lead text-center">'.$tempo.'.º</td>';
                  for($dia=1; $dia<=valorArray($gerenciador_periodo, "numeroDias"); $dia++){

                   $horario =  $manipulacaoDados->selectCondClasseCurso("array", "horario", [], ["classe"=>valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), "turma"=>valorArray($manipulacaoDados->sobreTurmaActualAluno, "nomeTurma", "turmas"), "idHorAno"=>$manipulacaoDados->idAnoActual, "idPEscola"=>$_SESSION["idEscolaLogada"], "tempo"=>$tempo, "dia"=>$dia], valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), ["idPNomeCurso"=>valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola")]);


                    echo '<td class="lead valor text-center" >'.valorArray($horario, "abreviacaoDisciplina2").'</td>';
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
      <?php if(valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola")>=4){ ?>
      <div class="row" id="divisaoProfessor">
        <div class="col-lg-offset-2 col-md-offset-2 col-lg-8 col-md-8"> 
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                    <tr>
                        <th class="lead"><strong><i class="fa fa-book-open"></i> Disciplina</strong></th>
                        <th class="lead"><strong><i class="fa fa-user-tie"></i> Professor</strong></th>
                    </tr>
                </thead>
                <tbody id="tabDivisaoProfessor">
                    <?php 
                    $divisaoProfessor = $manipulacaoDados->selectCondClasseCurso("array", "divisaoprofessores", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$manipulacaoDados->idAnoActual, "classe"=>valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), "nomeTurmaDiv"=>valorArray($manipulacaoDados->sobreTurmaActualAluno, "nomeTurma", "turmas")], valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), ["idPNomeCurso"=>valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola")]);


                    foreach($divisaoProfessor as $a){?>

                      <tr>
                        <td class="lead"><?php echo $a["nomeDisciplina"]; ?></td>
                        <td class="lead"><?php echo isset($a["nomeEntidade"])?$a["nomeEntidade"]:""; ?></td>
                    </tr>
                   <?php } ?>
                </tbody>
            </table>
          </div>
        </div> 
      </div>
    <?php } } echo "<br/><br/></div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
