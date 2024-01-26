<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Reconfirmados", "reconfirmados");
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "reconfirmados", array(), "msg")){

         $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
        echo "<script>var idPAno='".$idPAno."'</script>";

        $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:""; 

          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula", "reconfirmacoes.dataReconf", "escola.idMatAno", "nomeAluno", "numeroInterno", "escola.classeActualAluno", "reconfirmacoes.idReconfAno", "sexoAluno", "reconfirmacoes.designacaoTurma", "reconfirmacoes.idReconfProfessor", "fotoAluno"], ["reconfirmacoes.idReconfAno"=>$idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idMatCurso"=>$idCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1));

           $array = $manipulacaoDados->anexarTabela2($array, "entidadesprimaria", "reconfirmacoes", "idPEntidade", "idReconfProfessor");

          echo "<script>var alunosReconfirmados=".json_encode($array)."</script>";

          ?>

         
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-2 lead">
              Ano:
              <select class="form-control lead" id="anosLectivos">
                <?php 
                  foreach($manipulacaoDados->anosLectivos as $ano){                      
                    echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                  } 
                ?>
              </select>
            </div>         
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
            <div class="col-lg-7 col-md-7"><br>
              <a href="../../relatoriosPdf/listaGeralAlunosReconfirmados.php?idPAno=<?php echo $idPAno; ?>&idPCurso=<?php echo $idCurso ?>&classe=<?php echo $classe; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Lista Geral</a>&nbsp;&nbsp;&nbsp;&nbsp;
              <a href="../../relatoriosPdf/listaGeralBolseiros.php?idPAno=<?php echo $idPAno; ?>&idPCurso=<?php echo $idCurso ?>&classe=<?php echo $classe; ?>" class="btn btn-primary"><i class="fa fa-id-card"></i> Bolseiros</a>&nbsp;&nbsp;&nbsp;&nbsp;
              <?php 
                if($classe==13){ ?>
                  <a href="../../relatoriosPdf/listaPagamentosFinalistas.php?idPAno=<?php echo $idPAno; ?>&idPCurso=<?php echo $idCurso ?>" class="btn btn-primary"><i class="fa fa-print"></i> Lista Pagamentos</a>
              <?php } else if($classe==12){ ?>
                  <a href="../../relatoriosPdf/listaAlunosAdmitidosParaExame.php?idPAno=<?php echo $idPAno; ?>&idPCurso=<?php echo $idCurso ?>" class="btn btn-primary"><i class="fa fa-print"></i> Adminitido para Exame</a>
              <?php } ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12"><br>
               <label class="lead">
                    Total: <span class="numTAlunos quantidadeTotal">0</span>
                </label>&nbsp;&nbsp;&nbsp;
                 <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
            </div>
          </div>
          <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead text-center"></th>
                      <th class="lead"><strong>Nome Completo</strong></th>
                      <th class="lead text-center"><strong> Número Interno</strong></th>
                      <th class="lead text-center"><strong>Funcionário</strong></th>
                      <th class="lead"><strong> Data</strong></th>
                      <th class="lead"><strong> Turma</strong></th>

                      <th class="lead text-center"><strong> Recibo</strong></th>
                      <th class="lead text-center"></th>
                  </tr>
              </thead>
              <tbody id="tabJaReconfirmados">

              </tbody>
          </table>
      </div>
    </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formularioDaMatricula(); $includarHtmls->formTrocarSenha(); ?>
