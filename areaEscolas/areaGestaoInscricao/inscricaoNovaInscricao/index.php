<?php session_start();  
    include_once $_SERVER["DOCUMENT_ROOT"].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER["DOCUMENT_ROOT"].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Nova Inscrição", "inscricaoNovaInscricao"); 
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

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-plus"></i> Inscrição</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
       <?php  if($verificacaoAcesso->verificarAcesso("", ["inscricaoNovaInscricao"], array(), "msg")){  

        $classesAcesso = valorArray(listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "classes_aceso", ["idClAcEscola=".$_SESSION['idEscolaLogada'], "tipoAcesso=secretaria"]), "classes");

        $classesAcesso = explode(",", $classesAcesso);
        $idCursosPermitidos = array();
        foreach($classesAcesso as $a){
          $classe = explode("_", $a)[0];
          $idCurso = isset(explode("_", $a)[1])?explode("_", $a)[1]:"";
          $idCursosPermitidos[]=intval($idCurso);
        }

        if($verificacaoAcesso->verificarAcesso(["aDirectoria"], [''], "semMensagem")){
          $idCursosPermitidos=array();
        }
        $condicaoCurso = ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"];
        if(count($idCursosPermitidos)>0){
          $condicaoCurso["idPNomeCurso"]=['$in'=>$idCursosPermitidos];
        }

        $idPCurso = isset($_GET["idPCurso"])?$_GET["idPCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", $condicaoCurso, ["cursos"]);
        echo "<script>var idPCurso='".$idPCurso."'</script>";

        $manipulacaoDados->conDb("inscricao");

        $gestorVagas = $manipulacaoDados->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "idGestCurso"=>$idPCurso]);

        echo "<script>var criterioEscolhaPeriodo ='".valorArray($gestorVagas, "criterioEscolhaPeriodo")."'</script>";

        echo "<script>var periodosCurso ='".valorArray($gestorVagas, "periodosCurso")."'</script>";

        echo "<script>var alunosInscritos = ".$manipulacaoDados->selectJson("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$manipulacaoDados->idAnoActual, "inscricao.idInscricaoProfessor"=>$_SESSION['idUsuarioLogado'], "inscricao.idInscricaoCurso"=>$idPCurso, "inscricao.idInscricaoAno"=>$manipulacaoDados->idAnoActual], ["inscricao"], "", [], ["nomeAluno"=>1])."</script>";
         ?>
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4 col-md-4 lead">
                Curso:
                <select class="form-control" id="idPCurso" name="idPCurso">
                   <?php 
                  $manipulacaoDados->conDb();
                  $arrayCursos=array();

                   foreach($manipulacaoDados->selectArray("nomecursos", [], $condicaoCurso, ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
                    $arrayCursos[]=$curso;
                    echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                   } ?>
                </select>
            </div>
              <div class="col-lg-3 col-md-3"><br>
                <button type="button" class="btn lead btn-success novoRegistroFormulario" id="novoProfessor"><i class="fa fa-user-plus"></i> Nova Inscrição</button>
              </div>
              <div class="col-md-5 col-lg-5"><br>
                      <label class="lead">Total: <span id="numTAlunos" class="quantidadeTotal"></span></label> &nbsp;&nbsp;&nbsp;
                      <label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>
              </div> 
            </div>
            <table id="example1" class="table table-striped table-bordered table-hover">
                <thead class="corPrimary"> 
                  <tr>
                    <th class="lead text-center"><strong>Nº</strong></th>
                    <th class="lead text-center"><strong>Nome Completo</strong></th>
                    <th class="lead text-center"><strong>Sexo</strong></th>
                    <th class="lead text-center"><strong>Data</strong></th>
                    <th class="lead text-center"><strong>Bilhete de Identidade</strong></th>
                    <th class="lead text-center"><strong>Nº de Telefone</strong></th>
                    <th class="lead text-center"></th>
                    <th class="lead text-center"></th>
                    <th></th>
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
<?php 
$conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formularioDeCadastro("inscricaoNovaInscricao", $idPCurso); $includarHtmls->formularioInscTrocCurso("inscricaoNovaInscricao", $arrayCursos);  $includarHtmls->formTrocarSenha(); ?>

    
