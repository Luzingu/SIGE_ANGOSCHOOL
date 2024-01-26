<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Ficheiros Arquivados", "ficheirosArquivados");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts(); 
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
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-folder-open"></i> Arquivo</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("",["ficheirosArquivados"], array(), "msg")){
          $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->selectUmElemento("anolectivo LEFT JOIN ano_escola ON idPAno=idFAno", "idPAno", "idAnoEscola=:idAnoEscola", [$_SESSION["idEscolaLogada"]], "numAno DESC");

          echo "<script>var idPAno='".$idPAno."'</script>";
        ?>
          
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-xs-12 col-sm-12 lead">
                  Ano:
                  <select class="form-control lead" id="idPAno">
                      <?php 
                        foreach($manipulacaoDados->selectArray("anolectivo LEFT JOIN ano_escola ON idPAno=idFAno", "*", "idAnoEscola=:idAnoEscola", [$_SESSION["idEscolaLogada"]], "numAno DESC") as $ano){                      
                          echo "<option value='".$ano->idPAno."'>".retornarAnoLectivo($ano->numAno)."</option>";
                        }
                       ?>
                  </select>
                </div>
                <div class="col-lg-3 col-md-3 lead">
                    Turma:
                     <select class="form-control" id="luzingu">   
                      <?php 
                        foreach ($manipulacaoDados->selectArray("listaturmas LEFT JOIN nomecursos ON idListaCurso=idPNomeCurso", "*", "idListaEscola=:idListaEscola AND idListaAno=:idListaAno AND nomeTurma IS NOT NULL AND classe IS NOT NULL", [$_SESSION["idEscolaLogada"], $manipulacaoDados->idAnoActual], "nomeCurso ASC, classe ASC, nomeTurma ASC") as $tur) {
                          
                          echo "<option value='".$tur->nomeTurma."-".$tur->classe."-".$tur->idPNomeCurso."'>".$tur->abrevCurso." - ".$tur->classe.".ª - ".$tur->nomeTurma."</option>";
                         
                        } ?>                 
                    </select>
                </div>
                <div class="col-lg-2 col-md-2 lead">
                    Referência:
                    <select class="form-control" id="trimestreReferencia">
                    <option value="I">Iº Trimestre</option>
                    <option value="II">IIº Trimestre</option>
                    <option value="III">IIIº Trimestre</option>
                    <option value="IV">Período Final</option>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <hr>
                  <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="listaTurmas"><i class="fa fa-print"></i> Lista</a>&nbsp;&nbsp;&nbsp;
                <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="relacaoNominal"><i class="fa fa-print"></i> Relação Nominal</a>&nbsp;&nbsp;&nbsp;
                <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="cartoesEstudantes"><i class="fa fa-id-badge"></i> Cartões de Estudante</a>
                 &nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="exemplarMiniPauta"><i class="fa fa-print"></i> Exemplar de Mini-Pauta</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaControlFaltas"><i class="fa fa-print"></i> Mapa de Control de Faltas</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaControlFaltas2"><i class="fa fa-print"></i> Mapa de Control de Faltas2</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaAvaliacaoAlunos"><i class="fa fa-print"></i> Mapa de Avaliação dos Alunos</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="horarioTurma"><i class="fa fa-print"></i> Horário</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="boletins"><i class="fa fa-print"></i> Boletins</a>
                 <hr>

                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="pautaGeral"><i class="fa fa-print"></i> Pauta Geral</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="aproveitamentoGeral"><i class="fa fa-print"></i> Aproveitamento Geral</a>&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="resumoNotas"><i class="fa fa-print"></i> Resumo de Notas</a>
                 <hr>
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaAproveitamentoGeral"><i class="fa fa-print"></i> Mapa de Aproveitamento Geral</a>&nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaAproveitamentoCurso"><i class="fa fa-print"></i> Mapa de Aproveitamento Geral do Curso</a>&nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaAproveitamentoPorDisciplina"><i class="fa fa-print"></i> Mapa de Aproveitamento por Disciplinas</a>&nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaAproveitamentoPorDisciplinaCurso"><i class="fa fa-print"></i> Mapa de Aproveitamento por Disciplinas no Curso</a>&nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="alunosTransitaramDeficiencia"><i class="fa fa-print"></i> Alunos Que Transitararm com Deficiência</a>&nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="alunosSubmetidosAoRecurso"><i class="fa fa-print"></i> Alunos Submetidos ao Recurso</a>
                 <hr>
                 &nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="resumoMatriculas"><i class="fa fa-print"></i> Resumo de Matriculas</a>
                 &nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaFrequencias"><i class="fa fa-print"></i> Mapa de Frequências</a>
                 &nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="mapaFrequenciasPorTurma"><i class="fa fa-print"></i> Mapa de Frequências por Turma</a>
                 &nbsp;&nbsp;&nbsp;&nbsp;
                 <a href="#" class="lead visualizadorRelatorio btn btn-primary" referencia="estisticaDeAlunosRepetentes"><i class="fa fa-print"></i> Estatística de Alunos Repetentes</a>
                </div>
              </div>
                
            </div>
          </div><br/>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formConfirmarSenhaAdministrador(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>