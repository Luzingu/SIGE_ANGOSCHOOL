<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados( "Matriculados", "matriculados");
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Alunos Registrados</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "matriculados", array(), "msg")){ 

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:""; 

          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";

          $condicao =["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatCurso"=>$idCurso, "escola.periodoAluno"=>$periodo];

          $expl = explode("_", $classe);
          if(count($expl)>1){
            $idAnoF = $expl[1];
            $condicao["escola.idMatFAno"]=$idAnoF;
            $condicao["escola.idMatCurso"] =$idCurso;
          }else{
            $condicao["escola.estadoAluno"]="A";
            $condicao["escola.classeActualAluno"]=$classe;
          }

          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "telefoneAluno", "estadoAcessoAluno", "sexoAluno"], $condicao, ["escola"], "", [], ["nomeAluno"=>1]);

          echo "<script>var listaAlunos = ".json_encode($array)."</script>";
          
          
         ?>

    
    
            <div class="card">
              <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                        Classe:
                        <select class="form-control lead" id="luzingu">
                          <?php 
                          if(isset($_SESSION['classesPorCursoPeriodoFinalista'])){
                            echo $_SESSION['classesPorCursoPeriodoFinalista'];
                          }else{
                            $_SESSION['classesPorCursoPeriodoFinalista']=retornarClassesPorCurso($manipulacaoDados, "", "sim", "sim", "sim");
                          }
                        ?>                           
                        </select>
                    </div>
                    <div class="col-lg-8 col-md-8"><br>
                      <label class="lead">Total: <span id="numTAlunos" class="quantidadeTotal"></span></label>
                          &nbsp;&nbsp;&nbsp;<label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label> 
                    </div>
                </div>
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                          <tr>
                      <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
                      <th class="lead"><strong>Nome Completo</strong></th>
                      <th class="lead text-center"><strong>Número Interno</strong></th> 
                      <th class="lead text-center"><strong>Telefone</strong></th>
                      <th class="lead text-center"><strong>Acesso</strong></th>                    
                      
                      <th class="lead text-center"></th>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formularioDaMatricula("matricula"); $includarHtmls->formTrocarSenha(); ?>
