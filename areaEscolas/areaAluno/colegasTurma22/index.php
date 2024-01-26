<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Colegas de Turmas", "colegasTurma");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->designacaoArea ="Área do Aluno";
    $layouts->idPArea =1;
    $manipulacaoDados->retornarAnosEmJavascript();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .infoTurma h3{
      margin-bottom: -20px;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(1);
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-users"></i> COLEGAS</h1>
              </nav>
            </div>
          </div>
    <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso(1)){

            echo "<script>var listaAlunos = ".json_encode($manipulacaoDados->alunosPorTurma(intval(valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola")), intval(valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola")), valorArray($manipulacaoDados->sobreTurmaActualAluno, "nomeTurma"), "", "", ["idPMatricula", "nomeAluno", "numeroInterno", "sexoAluno", "telefoneAluno", "emailAluno"]))."</script>";


           ?>
      <div class="card">
        <div class="card-body">
            <table class="table table-striped table-bordered table-hover" id="example1">
              <thead>
                  <tr class="corPrimary">
                      <th class="lead text-center"><strong>N.º</strong></th>
                      <th class="lead"><strong>Nome Completo</strong></th>
                      <th class="lead text-center"><strong>Sexo</th>
                      <th class="lead text-center"><strong>N.º de Telefone</strong></th>
                      <th class="lead text-center"><strong>E-mail</strong></th>
                  </tr>
                  </thead>
              <tbody id="listaAlunos">
                  
              </tbody>
            </table>
        </div>
      </div><br>
            
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>