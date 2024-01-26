<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Nova Matricula Inscrição", "novaMatriculaInscricao");
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
    $usuariosPermitidos = ["aSecretaria"];

    $classesAcesso = valorArray(listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "classes_aceso", ["idEscola=".$_SESSION["idEscolaLogada"], "idPArea=1"]), "classes");

    $classesAcesso = explode(",", $classesAcesso);
    $idCursosPermitidos = array();
    foreach($classesAcesso as $a){
      $classe = explode("_", $a)[0];
      $idCurso = isset(explode("_", $a)[1])?explode("_", $a)[1]:"";
      $idCursosPermitidos[]=$idCurso;
    }

    if($verificacaoAcesso->verificarAcesso($manipulacaoDados->idPArea, array(), array(), "")){
      $idCursosPermitidos=array();
    }
    $condicaoCurso = ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']];
    if(count($idCursosPermitidos)>0){
      $condicaoCurso["idPNomeCurso"]=['$in'=>$idCursosPermitidos];
    }
   
    $idCursos =array();
    $manipulacaoDados->conDb("inscricao");
    foreach ($manipulacaoDados->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "estadoTransicaoCurso"=>"V"]) as $curso) {
      $idCursos[]=$curso["idGestCurso"];
    }
    $condicaoCurso["idPNomeCurso"]=['$in'=>$idCursos];
    
    $manipulacaoDados->conDb();
    $idCurso=isset($_GET["idCurso"])?$_GET["idCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", $condicaoCurso, ["cursos"]);

    $condicaoCurso2 = $condicaoCurso;
    $condicaoCurso2["idPNomeCurso"]=$idCurso;
    $idCurso = $manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", $condicaoCurso2, ["cursos"]);

    echo "<script>var idCurso='".$idCurso."'</script>";

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Matricula</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "novaMatriculaInscricao", array(), "msg")){


          echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";
          
            $manipulacaoDados->conDb("inscricao");
          echo "<script>var alunosQuePassaram=".json_encode($manipulacaoDados->selectArray("alunos", [], ["idAlunoAno"=>$manipulacaoDados->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$idCurso, "inscricao.estadoMatricula"=>"F", "inscricao.obsApuramento"=>"A"], ["inscricao"], "", [], ["nomeAluno"=>1]))."</script>";

           $manipulacaoDados->conDb();

          $array = $manipulacaoDados->selectArray("alunosmatriculados", [], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>"A", "escola.idMatEntidade"=>$_SESSION['idUsuarioLogado'], "escola.idMatCurso"=>$idCurso, "escola.idMatAno"=>$manipulacaoDados->idAnoActual, "escola.inscreveuSeAntes"=>"V"], ["escola"], 100, [], ["idPMatricula"=>-1]);
          $array = $manipulacaoDados->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");

           echo "<script>var listaAlunos = ".json_encode($array)."</script>";

         ?>    
    
    <div class="card">
      <div class="card-body">
        <div class="row">

          <div class="col-lg-3 col-md-3 col-xs-12 col-sm-12 lead">
            Curso
            <select class="form-control lead" id="curso" name="curso">
            <?php              
            foreach($manipulacaoDados->selectArray("nomecursos", [], $condicaoCurso, ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
            
              echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
            }
             ?>
            </select>
          </div>
          <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12"><br>
            <button type="button" class="btn lead btn-primary novoRegistroFormulario" id="btnNovaMatriculaInscricao"><i class="fa fa-user-plus"></i> Nova Matricula</button>
                  <label class="lead">Total de Alunos: <span id="numTAlunos" class="quantidadeTotal"></span></label> &nbsp;&nbsp;&nbsp;
                  <label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>
          </div>
        </div>

        <table id="example1" class="table table-striped table-bordered table-hover">
            <thead class="corPrimary">
                  <tr>
              <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
              <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
              <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
              <th class="lead text-center"><strong><i class="fa fa-book-reader"></i> Classe</strong></th>                      
              <th class="lead text-center"><strong><i class="fa fa-file"></i> Comprovativo</strong></th>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioMatricula" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioMatriculaF" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-circle"></i> Matricula</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>
                  <input type="hidden" name="idPAluno" id="idPAluno" class="vazio">
                  <input type="hidden" name="sexoAluno" id="sexoAluno2" class="vazio">
                  <input type="hidden" name="dataNascAluno" id="dataNascAluno2" class="vazio">
                  <input type="hidden" name="nomeAluno" id="nomeAluno2" class="vazio">

                  <input type="hidden" name="classeAluno" id="classeAlunoForm2" class="vazio">
                  <input type="hidden" name="idPCurso" id="idPCursoForm2" class="vazio">
                  <input type="hidden" name="acessoConta" id="acessoConta2" class="vazio">
                  <input type="hidden" name="periodoAluno" id="periodoAluno2" class="vazio">

                  <div class="row">
                    
                    <div class="lead col-md-2 col-lg-2">
                      <label>RPM</label>
                      <input type="text" autocomplete="off" name="rpm" class="form-control vazio" id="rpm" title="Referência de Pagamento de Matricula" maxlength="6">
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 idParaNomeAluno">
                      <label for="nomeAluno" class="lead">Nome Completo</label>
                      
                      <select class="form-control vazio lead" id="nomeAluno" name="nomeAluno" required maxlength="60">                      
                      </select> 
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 ">
                        <label for="sexoAluno" class="lead">Sexo</label>
                        <select class="form-control lead" id="sexoAluno" name="sexoAluno">
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </div>                      
                  </div>  

                  <div class="row">
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="dataNascAluno" class="lead">D. de Nascimento</label>
                        <input type="date" class="form-control vazio" id="dataNascAluno" required disabled="" title="Data de nascimento" placeholder="Data de Nascimento" >
                        <div class="dataNascAluno discasPrenchimento lead"></div>
                      </div>

                <?php $includarHtmls->parte2FormularioMatricula("novaMatriculaInscricao"); ?>
