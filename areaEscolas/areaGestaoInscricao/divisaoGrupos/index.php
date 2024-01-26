<?php session_start();       
     
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");

    $manipulacaoDados = new manipulacaoDados("Divisão de Grupos", "divisaoGrupos");
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

    //Exibir apenas cursos onde criterio de teste é exame de aptidão...
    $condicaoCurso = ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"];

    $manipulacaoDados->conDb("inscricao");
    $idCursosPermitidos = array();
    foreach ($manipulacaoDados->selectDistinct("gestorvagas", "idGestCurso", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "criterioTeste"=>"exameAptidao"]) as $idCurso) {
        $idCursosPermitidos[]=$idCurso["_id"];
    }
    $condicaoCurso["idPNomeCurso"]=['$in'=>$idCursosPermitidos];
    

    $manipulacaoDados->conDb();
    $idCurso=isset($_GET["idCurso"])?$_GET["idCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", $condicaoCurso, ["cursos"]);

    $manipulacaoDados->conDb("inscricao");

    $idCurso = $manipulacaoDados->selectUmElemento("gestorvagas", "idGestCurso", ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual, "criterioTeste"=>"exameAptidao", "idGestCurso"=>$idCurso]);

    echo "<script>var idCurso='".$idCurso."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

    <div class="row" >
      <div class="col-lg-12 col-md-12">
      <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

          <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                <strong class="caret"></strong>
                            </a>
          <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-dungeon"></i> <strong>Divisão de Grupos</strong></h1>
    </nav>
    </div>
    </div>
    <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("", ["divisaoGrupos"], array(), "msg")){



          echo "<script>var grupos = ".$manipulacaoDados->selectJson("lista_grupos", [], ["idListaAno"=>$manipulacaoDados->idAnoActual, "idListaCurso"=>$idCurso, "idListaEscola"=>$_SESSION["idEscolaLogada"]], [], "", [], array("numeroGrupo"=>1))."</script>"; 

          echo "<script>var listaAlunos =".$manipulacaoDados->selectJson("alunos", [], ["idAlunoAno"=>$manipulacaoDados->idAnoActual, "idAlunoEscola"=>$_SESSION["idEscolaLogada"], "grupo.idGrupoCurso"=>$idCurso], ["grupo"], "", [], ["nomeAluno"=>1])."</script>";

         echo "<script>var numeroInscritos=".count($manipulacaoDados->selectArray("alunos", ["idPAluno"], ["idAlunoAno"=>$manipulacaoDados->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada'], "inscricao.idInscricaoCurso"=>$idCurso], ["inscricao"], "", [], ["nomeAluno"=>1]))."</script>"; 


           ?>
      <form id="formDivisaoTurmas">     
      <div class="row">
      
      <div class="col-lg-4 col-md-4 lead">
        Curso:
          <select class="form-control lead" id="curso" name="curso">
            <?php 
              $manipulacaoDados->conDb();
              foreach ($manipulacaoDados->selectArray("nomecursos", [], $condicaoCurso, ["cursos"], "", [], ["nomeCurso"=>1]) as $curso) {
                echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
              }
            ?>
          </select>
      </div>
        <div class="col-lg-2 col-md-2 lead">
          Grupos:
          <select class="form-control lead text-center" id="grupo">
          </select>
        </div>
        <div class="col-lg-2 col-md-2 lead">
          Total:
          <input type="number" id="numeroGrupos" class="form-control text-center lead" min="1" max="26">
        </div>
      <div class="col-lg-4 col-md-4 lead"><br>
          <button type="submit" class="btn lead btn-primary font-weight-bolder" id="btnDividirTurma"><i class="fa fa-check-double"></i> Dividir</button>
          &nbsp;&nbsp;&nbsp;&nbsp;
          Inscritos: <span id="numInscritos" class="lead quantidadeTotal"></span>
        </div>
    </div>

    </form>  
          <div class="row">
            <div class="col-lg-12 col-md-19 col-sm-12 col-xs-12" id="pesqUsario">
              <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <span class="input-group-addon"><i class="fa fa-search"></i></span>
                  <input type="search" class="form-control lead" id="pesquisaAluno" tipoEntidade="alunos" placeholder="Pesquisar Aluno...">
                  
              </div>       
            </div>
          </div>
          <div class="row">

    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12">
              <a href="#" class="lead btn btn-primary visualizadorLista" referencia='listaGrupos.php'><i class="fa fa-print"></i> Lista</a>&nbsp;&nbsp;&nbsp;&nbsp; 
              <a href="#" class="lead btn btn-primary visualizadorLista" referencia='listaGrupos2.php'><i class="fa fa-print"></i> Lista2</a>&nbsp;&nbsp;&nbsp;&nbsp; 
              <a href="#" class="lead btn btn-primary visualizadorLista" referencia='listaGrupos4.php'><i class="fa fa-print"></i> Lista4</a>&nbsp;&nbsp;&nbsp;&nbsp; 
              <a href="#" class="lead btn btn-primary visualizadorLista" referencia='fichasExame.php'><i class="fa fa-print"></i> Fichas de Exame</a>&nbsp;&nbsp;&nbsp;&nbsp;<label class="lead">Total de Alunos: <span id="numTotalAlunos" class="quantidadeTotal">0</span></label>
            <label class="lead">Femininos: <span class="quantidadeTotal" id="numTMasculinos">0</span></label>
      </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead>
                <tr class="corPrimary">
                    <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i></strong></th>
                    <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome do Aluno</strong></th>
                    
                    <th class="lead text-center"><strong><i class="fa fa fa-restroom"></i></strong></th>
                    <th class="lead text-center"><strong><i class="fa fa-phone"></i> Nº de Telefone</strong></th>
                    <th class="lead text-center"><strong><i class='fa <i class="fa fa-hiking'></i> Idade</strong></th>
                    <th class="lead text-center"></th>
                </tr>
                </thead>
            <tbody id="listaAlunos">
                
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


  <div class="modal fade" id="trocarGrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="trocarGrupoForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-dungeon"></i> Trocar Grupo</h4>
              </div>

              <div class="modal-body">
                  <div class="row">

                      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 lead">
                        Nome do Aluno:
                        <input type="text" class="form-control" id="nomeAluno" readonly>
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Grupo
                        <select class="form-control lead" id="grupoTrocar" >

                        </select>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                      <button type="submit" class="btn btn-primary form-control lead btn-lg" id="Cadastar"><i class="fa fa-check"></i> Trocar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>