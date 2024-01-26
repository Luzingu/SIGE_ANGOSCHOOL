<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Relatório de Aproveitamento Final", "relatorioAproveitamentoFinal");
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
    table tr td, table tr th{
      font-size: 11pt !important;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();

    $obs = isset($_GET["obs"])?$_GET["obs"]:"aprovado";
    $label="";
    if($obs!="aprovado" && $obs!="reprovado" && $obs!="desistente" && $obs!="exclFalta" && $obs!="matAnulada"){
      $obs="aprovado";
    }

    if($obs=="aprovado"){
      $label="Alunos Aprovados";
    }else if($obs=="reprovado"){
      $label="Alunos Reprovados";
    }else if($obs=="desistente"){
      $label="Alunos Desistentes";
    }else if($obs=="exclFalta"){
      $label="Alunos Excluídos por Faltas";
    }else if($obs=="matAnulada"){
      $label="Alunos que Anularam Matriculas";
    }
    echo "<script>var obs='".$obs."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-info-circle"></i> Relatório dos 
                    <?php echo $label; ?></strong></h1>

               
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", ["relatorioAproveitamentoFinal"], array(), "msg")){

            $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual; 
            echo "<script>var idPAno='".$idPAno."'</script>";

            $classe=1;
            $idCurso="";
            if(isset($_GET["idCurso"])){
              $idCurso = $_GET["idCurso"];
            }else{
              $idCurso = $manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"], "cursos.estadoCurso"=>"A"], ["cursos"]);           
            }
            echo "<script>var idCursoP='".$idCurso."'</script>";

            $condicaoAluno = ["reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idPAno, "reconfirmacoes.idMatCurso"=>$idCurso];

            if($obs=="aprovado"){
              $condicaoAluno["reconfirmacoes.observacaoF"]=['$in'=>array('A', 'TR')];
            }else if($obs=="reprovado"){
              $condicaoAluno["reconfirmacoes.observacaoF"]="NA";
            }else if($obs=="desistente"){
              $condicaoAluno["reconfirmacoes.observacaoF"]="D";
            }else if($obs=="exclFalta"){
              $condicaoAluno["reconfirmacoes.observacaoF"]="F";
            }else if($obs=="matAnulada"){
              $condicaoAluno["reconfirmacoes.observacaoF"]="N";
            }
            
            $alunos = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "reconfirmacoes.observacaoF", "nomeAluno", "sexoAluno", "numeroInterno", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma"], $condicaoAluno, ["reconfirmacoes"], "", [], ["nomeAluno"=>1], $manipulacaoDados->matchMaeAlunos($idPAno, $idCurso, $classe));

            $alunos = $manipulacaoDados->anexarTabela2($alunos, "nomecursos", "reconfirmacoes", "idPNomeCurso", "idMatCurso");
             echo "<script>var listaAlunos=".json_encode($alunos)."</script>";
          ?>

        
        <div class="card">
          <div class="card-body">
            <div class="row">
            <div class="col-lg-2 col-md-2 lead">
              Ano:
              <select class="form-control lead" id="anosLectivos">
                  <?php 
              foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada']], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){                      
                echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
              }
            ?>
              </select>
            </div>

          <div class="col-lg-3 col-md-3 lead">
              Curso:
              <select class="form-control lead" id="curso" name="curso">
                  <?php 
                  foreach ($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "nomeCurso", "areaFormacaoCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso) {

                      echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                  }
                 ?>
              </select> 
          </div>

          <div class="col-lg-7 col-md-7 lead"><br>
            <a href="../../relatoriosPdf/relatorioAproveitamentoFinal/index.php?idPAno=<?php echo $idPAno; ?>&idCurso=<?php echo $idCurso ?>&obs=<?php echo $obs; ?>" id="mapaDesempenhoFinal" class="lead btn btn-primary"><i class="fa fa-print"></i> Visualizar</a>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <label class="lead">Total: <span id="numTotalAlunos" class="quantidadeTotal">0</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
            <label class="lead">Femininos: <span class="quantidadeTotal" id="numTMasculinos">0</span></label>
            
          </div> 
          

    </div>
        
          <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                  <tr>
                      <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i></strong></th>
                      <th class="lead"><strong>Nome do Aluno</strong></th>
                      <th class="lead text-center"><strong>Número Interno</strong></th>
                      <th class="lead text-center"><strong>Classe Anterior</strong></th>
                      <th class="lead text-center"><strong>Turma Anterior</strong></th>
                  </tr>
              </thead>
              <tbody id="tabListaAlunos">

              </tbody>
          </table>
        </div>
        </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>