<?php session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");

    $manipulacaoDados = new manipulacaoDados("Exame de Inscrição", "exameInscricao");
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
  <style type="text/css">
    table tr td{
      vertical-align: middle;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();

    //Exibir apenas cursos onde criterio de teste é exame de aptidão...
    $idCursosPermitidos=array();
    $manipulacaoDados->conDb("inscricao");
    foreach ($manipulacaoDados->selectDistinct("gestorvagas", "idGestCurso", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "criterioTeste"=>"exameAptidao"]) as $idCurso) {
      $idCursosPermitidos[]=$idCurso["_id"];
    }

      //Apresentar apenas os cursos na qual o professor foi selecionado...
    $idCursosAcesso=array();
    $manipulacaoDados->conDb();
    $classesAcesso = valorArray(listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "classes_aceso", ["idClAcEscola=".$_SESSION['idEscolaLogada'], "tipoAcesso=secretaria"]), "classes");

    $classesAcesso = explode(",", $classesAcesso);
    foreach($classesAcesso as $a){
      $classe = explode("_", $a)[0];
      $idCurso = isset(explode("_", $a)[1])?explode("_", $a)[1]:"";
      if($classe==10){
        $idCursosAcesso[]=intval($idCurso);
      }
    }

    if($verificacaoAcesso->verificarAcesso("", ["exameInscricao"], array(), "")){
      $idCursosAcesso="";
    }

    $condicaoCurso = ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>['$in'=>$idCursosPermitidos], "cursos.estadoCurso"=>"A"];
    if(is_array($idCursosAcesso)){
      $condicaoCurso["idPNomeCurso"]=['$in'=>$idCursosAcesso];
    }

    $idCurso= isset($_GET["idCurso"])?$_GET["idCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", $condicaoCurso, ["cursos"]);

    $condicaoCurso2 = $condicaoCurso;
    $condicaoCurso2["idPNomeCurso"]=$idCurso;
    $idCurso = $manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", $condicaoCurso2, ["cursos"]);

    $manipulacaoDados->conDb("inscricao");
    $gestor = $manipulacaoDados->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "criterioTeste"=>"exameAptidao", "idGestCurso"=>$idCurso]);

    $idCurso = valorArray($gestor, "idGestCurso");
    echo "<script>var numeroProvas ='".valorArray($gestor, "numeroProvas")."';
      var nomeProvas = new Array();
      nomeProvas[1]='".valorArray($gestor, "nomeProva1")."';
      nomeProvas[2]='".valorArray($gestor, "nomeProva2")."';
      nomeProvas[3]='".valorArray($gestor, "nomeProva3")."'
    </script>";
    $manipulacaoDados->conDb();
  ?> 

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-pen"></i> Exames de Aptidão</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "exameInscricao", [10, $idCurso], "msg")){ 

          $manipulacaoDados->conDb("inscricao");


          echo "<script>var tipoAutenticacao ='".$manipulacaoDados->selectUmElemento("gestorvagas", "tipoAutenticacao", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "idGestCurso"=>$idCurso])."' </script>";


         echo "<script>var alunos =".json_encode($manipulacaoDados->selectArray("alunos", [], ["idAlunoEscola"=>$_SESSION['idEscolaLogada'], "idAlunoAno"=>$manipulacaoDados->idAnoActual, "inscricao.idInscricaoCurso"=>$idCurso], ["inscricao"], "", [], ["nomeAluno"=>1]))."</script>";

            echo "<script>var idPCurso='".$idCurso."'</script>";
         ?>
    <div class="row">
        <div class="col-lg-4 col-md-4 lead">
          Curso:
            <select class="form-control lead" id="curso" name="curso">
                <?php                
                $manipulacaoDados->conDb();
                foreach($manipulacaoDados->selectArray("nomecursos", [], $condicaoCurso, ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
                echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
               } 
                 ?> 
            </select>

        </div>        
    </div>  
  <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="pesqUsario">
        <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="search" class="form-control lead pesquisaAluno" tipoEntidade="alunos" placeholder="Pesquisar Aluno pelo código" list="listaOpcoes">
            
        </div>   
      </div>
    </div>

          <div class="row">
              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
               <label class="lead">Total de Alunos: <span id="numTAlunos" class="quantidadeTotal"></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
               <label class="lead">Femininos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>
              </div>
          </div>
          <div id="listaAlunos">
            
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