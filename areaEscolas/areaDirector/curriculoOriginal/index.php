<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Curriculo", "curriculoOriginal");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Curriculo</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("",["curriculoOriginal"], array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $curriculo = "";

          $array = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "sePorSemestre", "cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3", "cursos.curriculoEscola"], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
          
          $adminCurriculo="yes";
          if(valorArray($array, "curriculo1") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo1";
          else if(valorArray($array, "curriculo2") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo2";
          else if(valorArray($array, "curriculo3") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo2";
          else
          {
            $curriculo = valorArray($array, "tipoCurriculo", "cursos"); 
            $adminCurriculo="not";
          }

          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          
          echo "<script>var disciplinas =".json_encode($manipulacaoDados->disciplinas($idCurso, $classe, $periodo))."</script>";
          ?>
            
          <h4 class="text-primary font-weight-bolder text-right">Modelo: <?php echo strtoupper($curriculo); ?></h4>
          <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-3 col-md-3 lead">
                      Classe:
                      <select class="form-control lead" id="luzingu">
                        <?php 
                          if(isset($_SESSION['classesPorCursoPeriodo'])){
                            echo $_SESSION['classesPorCursoPeriodo'];
                          }else{
                            $_SESSION['classesPorCursoPeriodo']=retornarClassesPorCurso($manipulacaoDados, "A");
                          }
                        ?>                         
                      </select>
                  </div>
                  <div class="col-lg-2 col-md-2 lead">
                    Ano:
                    <select class="form-control lead" id="anosLectivos">
                      <?php 
                        foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada']], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){                      
                          echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                        }
                        echo "<option value=''>Todos Anos</option>";
                      ?>
                    </select>
                  </div>
              </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>Ordem</strong></th>
                        <th class="lead font-weight-bolder"><strong>Nome da Disciplina</strong></th>
                        <th class="lead"><strong>Tipo de Disciplina</strong></th>
                        <th class="lead"><strong>Semestre</strong></th>
                        <th class="lead"><strong>Continuidade</strong></th>
                        <th class="lead text-center"><strong>Anos Lectivos</strong></th>
                      </tr>
                  </thead>
                  <tbody id="tabela">
                      
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->formTrocarSenha(); ?>