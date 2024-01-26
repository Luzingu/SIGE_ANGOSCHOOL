<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    
    $manipulacaoDados = new manipulacaoDados("Actualizar Pautas", "actualizarPautas");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->retornarAnosEmJavascript();
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .sopelaNgayi{
      line-height: 50px;
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
                                <strong class="caret"></strong>
                            </a>
          <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-redo"></i> <strong>Actualizar Pautas</strong></h1>
      </nav>
    </div>
    </div>
    <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("", ["actualizarPautas"], array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);

          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:""; 

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";

          echo "<script>var listaAlunos =".json_encode($manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma, $manipulacaoDados->idAnoActual, array(), ["idPMatricula", "nomeAluno", "numeroInterno", "biAluno", "sexoAluno", "pautas.idPPauta", "fotoAluno", "pautas.classePauta", "pautas.idPautaCurso"]))."</script>";
           ?> 

      <div class="card">
          <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-3 lead">
                  Turma
                    <select class="form-control lead" id="luzingu">
                      <?php optTurmas($manipulacaoDados); ?>
                    </select>
                </div>
                <div class="col-lg-8 col-md-8"><br>
                  <button id="actualizarPautas" class="btn btn-primary lead"><i class="fa fa-redo"></i> Actualizar</button>
                </div>
            </div>

            <table id="example1" class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                      <tr>
                  <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> TOTAL</strong></th>
                  <th class="lead text-center"><input type="checkbox" id="actualizarTodos"></th>
                  <th class="lead"><strong>Nome Completo</strong></th>
                  <th class="lead text-center"><strong>N.º Interno</strong></th>                      
                  <th class="lead text-center"><strong>N.º BI</strong></th>                      
                  <th class="lead text-center"><strong>Sexo</strong></th>
              </tr>

                </thead>
                <tbody id="tabela">
                </tbody>
            </table>
        </div>
      </div>
            
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>