<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Nova Matricula", "novaMatricula");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->listaClassesPorCurso();
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Nova Matricula</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["novaMatricula"], array(), "msg")){   

          echo "<script>var dataMatricula=''</script>";
          echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";
          
          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "sexoAluno", "dataNascAluno", "biAluno", "dataEBIAluno", "paiAluno", "maeAluno", "encarregadoEducacao", "telefoneAluno", "emailAluno", "dataCaducidadeBI", "estadoAcessoAluno", "tipoDocumento", "localEmissao", "numeroProcesso", "deficienciaAluno", "numeroInterno", "deficienciaAluno", "tipoDeficienciaAluno", "paisNascAluno", "provNascAluno", "municNascAluno", "comunaNascAluno", "idPMatricula", "escola.estadoDeDesistenciaNaEscola","escola.idMatAnexo","escola.idGestLinguaEspecialidade","escola.idGestDisEspecialidade","escola.periodoAluno","escola.numeroProcesso", "escola.rpm","escola.idMatCurso","escola.classeActualAluno", "escola.turnoAluno", "reconfirmacoes.tipoEntrada"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatAno"=>$manipulacaoDados->idAnoActual, "escola.idMatEntidade"=>$_SESSION['idUsuarioLogado'], "escola.estadoAluno"=>"A", "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual], ["escola", "reconfirmacoes"], "", [], ["idPMatricula"=>-1]);
          $novoArray = $manipulacaoDados->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
          echo "<script>var listaAlunos = ".json_encode($array)."</script>";
         ?>
                  
        

    
     
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2 col-lg-2 col-sm-6 col-xs-6"><br>
                    <button type="button" class="btn btn-success btn-lg btn novoRegistroFormulario"><i class="fa fa-user-plus"></i> Matricular</button>                          
                  </div>
                  <div class="col-md-8 col-lg-8 col-sm-12 col-xs-12"><br>
                          <label class="lead">Total: <span id="numTAlunos" class="quantidadeTotal"></span></label> &nbsp;&nbsp;&nbsp;
                          <label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>
                  </div>
                </div>
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                          <tr>
                      <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
                      <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                      <th class="lead text-center"><strong><i class='fa fa-graduation-cap'></i> Classe</strong></th>
                      <th class="lead"><strong><i class='fa fa-sun'></i> Período</strong></th>                      
                      <th class="lead text-center"><strong><i class="fa fa-file"></i></strong></th>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); $includarHtmls->formularioDaMatricula("novaMatricula");?> 
