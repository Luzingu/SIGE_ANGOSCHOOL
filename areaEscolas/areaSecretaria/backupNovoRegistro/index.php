<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Backup de Novo Registro", "backupNovoRegistro");
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
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    $layouts->cabecalho();
    $layouts->aside();

    echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreEscolaLogada, "criterioEscolhaTurno")."'</script>";

  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-archive"></i> Backup - Novo Registro</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "backupNovoRegistro", array(), "msg")){

          $luzinguLuame =$manipulacaoDados->selectArray("alunosmatriculados", [], ["escola.idMatAno"=>$manipulacaoDados->idAnoActual, "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.idMatEntidade"=>$_SESSION['idUsuarioLogado'], "escola.estadoAluno"=>"Y", "escola.inscreveuSeAntes"=>"Y"], ["escola"], "", [], ["idPMatricula"=>-1]);

          $luzinguLuame = $manipulacaoDados->anexarTabela($luzinguLuame, "nomecursos", "idPNomeCurso", "idMatCurso");

           echo "<script>var listaAlunos = ".json_encode($luzinguLuame)."</script>";

         ?>
       <div class="card">
         <div class="card-body">`
            <div class="row">

              <div class="col-lg-4 col-md-4 col-lg-offset-8 col-md-offset-8" id="pesqUsario">
                <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    <input type="search" class="form-control lead pesquisaEntidade" tipoEntidade="alunos" placeholder="Pesquisar Aluno..." list="listaOpcoes">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                <button type="button" class="lead btn-success btn novoRegistroFormulario" id="novoAluno"><i class="fa fa-user-plus"></i> Novo Registro</button> &nbsp;&nbsp;&nbsp;
                      <label class="lead">Total: <span id="numTAlunos" class="quantidadeTotal"></span></label> &nbsp;&nbsp;&nbsp;
                      <label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>
              </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                          <tr>
                      <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
                      <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                      <th class="lead"><strong><i class="fa fa-book-reader"></i> Curso</strong></th>
                      <th class="lead"><strong><i class='fa fa-sun'></i> Período</strong></th>
                      <th class="lead text-center"></th>
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
        </div>
      </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();$includarHtmls->formularioDaMatricula("backup"); $includarHtmls->formTrocarSenha(); ?>
