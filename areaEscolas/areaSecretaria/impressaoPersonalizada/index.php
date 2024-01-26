<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Impressão de Cartões Personalizada", "impressaoPersonalizada");
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
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-print"></i><strong> Impressão Personalizada</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  
        if($verificacaoAcesso->verificarAcesso("", "impressaoPersonalizada", array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          
          $luzingu = explode("-", $luzingu);
          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";

        ?>
  
  
    <div class="card">
        <div class="card-body">
      <div class="row">
         <div class="col-lg-3 col-md-3 lead">
            Turma:
             <select class="form-control" id="luzingu">   
              <?php optTurmas($manipulacaoDados); ?>                 
            </select>
        </div>
        <div class="col-lg-10 col-md-10"><br>
          <input type="checkbox" title="Seleccionar todos" style="height:30px; width: 30px;" id="visTodosCartoes" name="">
        </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-md-12"><br>
        <a href="#" class="visualizadorRelatorio1 btn btn-primary" caminho="cartoesAlunos/cartao1.php"><i class="fa fa-id-badge"></i> Cartões de Estudante</a>
         &nbsp;&nbsp;
        <a href="#" class="visualizadorRelatorio1 btn btn-primary" caminho="cartoesAlunos/cartao2.php"><i class="fa fa-id-badge"></i> Cartões de Pagamentos</a>
        &nbsp;&nbsp;

        <a caminho="boletins/index.php" trimestreApartir="I" class="btn-primary visualizadorRelatorio1 btn" id="boletins"><i class="fa fa-print"></i> Boletim(I)</a>
        &nbsp;&nbsp;
        <a caminho="boletins/index.php" trimestreApartir="II" class="btn-primary visualizadorRelatorio1 btn" id="boletins"><i class="fa fa-print"></i> Boletim(II)</a>
        &nbsp;&nbsp;
        <a caminho="boletins/index.php" class="btn-primary visualizadorRelatorio1 btn" id="boletins" trimestreApartir="III"><i class="fa fa-print"></i> Boletins (III)</a>
        &nbsp;&nbsp;
        <a caminho="boletins/index.php" class="btn-primary visualizadorRelatorio1 btn" id="boletins" trimestreApartir="IV"><i class="fa fa-print"></i> Boletins (F)</a>
      </div>
    </div>
    <br>
    <div class="row" id="listaAlunos1">
      <?php 
        foreach($manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma, $manipulacaoDados->idAnoActual, [], ["idPMatricula", "nomeAluno", "numeroInterno", "biAluno", "sexoAluno", "fotoAluno"]) as $a){ ?>

          <div class="col-lg-4 col-md-4"><label class="lead"><input type="checkbox" id="<?php echo $a->idPMatricula; ?>">&nbsp;<?php echo $a->nomeAluno; ?></label></div>

       <?php } ?>
        
    </div><br>
  </div>
  </div>
  </div>

    <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
