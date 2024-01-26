<?php session_start();  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Alunos com Deficiência", "alunosDeficiencia");
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
    $usuariosPermitidos = ["aDirectoria"];

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>

                      </a>

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><i class="fa fa-wheelchair"></i> <strong>Alunos Deficientes</strong></h1>
                 
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  
        if($verificacaoAcesso->verificarAcesso("", "alunosDeficiencia", array(), "msg")){
          
        $luzinguLuame = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno", "escola.idMatCurso", "escola.classeActualAluno", "deficienciaAluno", "tipoDeficienciaAluno"], ["escola.idMatEscola"=>$_SESSION["idEscolaLogada"], "escola.estadoAluno"=>"A", "deficienciaAluno"=>['$nin'=>array(null, "")]], ["escola"], "", [], ["nomeAluno"=>1], ["escola_".$_SESSION['idEscolaLogada']=>"sim"]);

        $luzinguLuame = $manipulacaoDados->anexarTabela2($luzinguLuame, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
        echo "<script>var alunosDeficientes=".json_encode($luzinguLuame)."</script>"; 
          ?> 

              

              <div class="card">

                <!-- /.card-header -->
                <div class="card-body">
                  
                  <table id="example1" class="table table-bordered table-striped">
                      <thead class="corPrimary">
                          <tr>
                              <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                              <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                              <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                              <th class="lead text-center"><strong><i class="fa fa-book-reader"></i> Curso</strong></th>
                <th class="lead text-center"><strong><i class='fa fa-graduation-cap'></i> Classe</strong></th><th class="lead"><strong> Deficiência</strong></th>
                <th class="lead"><strong> Patologia</strong></th>
                          </tr>
                      </thead>
                      <tbody id="alunosDeficientes">

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
