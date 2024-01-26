<?php session_start();    
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Resultado Final", "relatorioAlunoInscricao");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    inicializadorDaFuncaoGestaInscricao($manipulacaoDados);
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

    if(!isset($_GET["idPCurso"])){
        echo "<script>window.location ='../../areaGestaoInscricao/lancamentoResultados';</script>";
    }else{
        $idPCurso = $_GET["idPCurso"];
        $manipulacaoDados->conDb("inscricao");
        $gestor = $manipulacaoDados->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "idGestCurso"=>$idPCurso]);

        $manipulacaoDados->conDb();
    }

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <?php if(valorArray($gestor, "estadoTransicaoCurso")=="Y"){ ?>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-hammer"></i> Resultados Provisórios</strong></h1>
                  <?php } else if(valorArray($gestor, "estadoTransicaoCurso")=="V") { ?>
                    <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-hammer"></i> Resultados Definitivos</strong></h1>
                  <?php } ?>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["resultadoFinal"], array(), "msg")){ 

          echo "<script>var criterioTeste='".valorArray($gestor, "criterioTeste")."'</script>";
          echo "<script>var idPCurso='".valorArray($gestor, "idGestCurso")."'</script>";
          $manipulacaoDados->conDb("inscricao");

          echo "<script>var alunos =".json_encode($manipulacaoDados->selectArray("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$manipulacaoDados->idAnoActual, "inscricao.idInscricaoCurso"=>valorArray($gestor, "idGestCurso")], ["inscricao"], "", [], array("inscricao.posicaoApuramento"=>1)))."</script>";
          
          

         ?>
         <div class="row">
            <div class="col-lg-3 col-md-3">
              <select class="form-control lead" id="curso" name="curso">
                  <?php 
                  $manipulacaoDados->conDb();
                  foreach($manipulacaoDados->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){ 
                    echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                  }

                   ?> 
              </select>
          </div>
      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12" id="pesqUsario">
          <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="search" class="form-control lead pesquisaAluno" tipoEntidade="alunos" placeholder="Pesquisar Aluno pelo nome ou código...">
              
          </div>   
        </div>
    </div>
    <div class="row">
      <div class="col-md-12">
              <a href="../../relatoriosPdf/relatoriosInscricao/listaResultados.php?idPCurso=<?php echo $idPCurso; ?>" class="lead btn btn-primary"><i class="fa fa-print"></i> Visualizar</a> &nbsp;&nbsp;&nbsp; 
              <label class="lead">Total de Alunos: <span id="numTAlunos" class="quantidadeTotal"></span></label> &nbsp;&nbsp;&nbsp;
              <label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>&nbsp;&nbsp;&nbsp;
              <label class="lead">Aprovados: <span id="numTAprovados" class="quantidadeTotal"></span></label>
      </div>
    </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center" style="vertical-align: middle;"><strong><i class='fa fa-sort-numeric-down'></i></strong></th>
                          <th class="lead text-center" style="vertical-align: middle;"><strong>Nome do Aluno</strong></th>
                          <th class="lead text-center"><strong>Cógigo</strong></th>
                          <?php  if(valorArray($gestor, "criterioTeste")=="exameAptidao"){ ?>

                            <th class="lead text-center"><strong>N1</strong></th>
                            <th class="lead text-center"><strong>N2</strong></th>
                            <th class="lead text-center"><strong>N3</strong></th>
                            <th class="lead text-center"><strong>M</strong></th>
                            <th class="lead text-center"><strong>Sexo</strong></th>
                            <th class="lead text-center"><strong>Data de Nasc.</strong></th>
                          <?php  } else  if(valorArray($gestor, "criterioTeste")=="factor"){ ?>

                            <th class="lead text-center"><strong>M. Disc. Nucl.</strong></th>
                            <th class="lead text-center"><strong>Data de Nasc.</strong></th>
                            <th class="lead text-center"><strong>Sexo</strong></th>
                            <th class="lead text-center"><strong>%</strong></th>
                          <?php  } else { ?>
                            <th class="lead text-center"><strong>M. Disc. Nucl.</strong></th>
                            <th class="lead text-center"><strong>Data de Nasc.</strong></th>
                            <th class="lead text-center"><strong>Sexo</strong></th>
                          <?php } ?>
                          <th class="lead text-center"><strong>Período</strong></th>
                          <th class="lead text-center"><strong>OBS</strong></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
            <div class="row" id="paraPaginacao" style="margin-top: -30px;">
                <div class="col-md-12 col-lg-12 coluna">
                    <div class="form-group paginacao">
                          
                    </div>
                </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>