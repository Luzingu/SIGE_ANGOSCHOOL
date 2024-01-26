<?php session_start(); 
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pautas Arquivadas", "pautasArquivadas");
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
  <style type="text/css">
    #listaAlunos form{
      border-bottom:solid rgba(0, 0, 0, 0.2) 2px;
      padding-top: 10px;
      padding-bottom: 20px;
    }

    .caixaResultado{
      background-color: rgba(0,0,0,0.2) !important;
    }

    #listaAlunos form input.valorCt{
        background-color: transparent;
        color: black;
        font-weight: 700;
    }
     #listaAlunos form input{
      font-size: 12pt !important;
      padding: 0px;
    }

    #listaAlunos form input.observacaoF{
      font-weight: 700;
      background-color: transparent;
    }

    #divmapasEstaticosDeAproveitamentoDosAlunos .modal-dialog{
      width: 60%; 
      margin-left: -30%;
    }
    @media (max-width: 768px) {
          #divmapasEstaticosDeAproveitamentoDosAlunos .modal-dialog, .modal .modal-dialog{
              width: 94%;
              margin-left: 3%;

          }
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
           <div class="row" >
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-table"></i><strong id="pGeral"> Pautas Arquivadas</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  
        if($verificacaoAcesso->verificarAcesso("", ["pautasArquivadas"], array(), "msg")){
          
          $abigael = "";
          foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"], "anos_lectivos.estadoAnoL"=>array('$ne'=>"V")], ["anos_lectivos"], 1, [], ["numAno"=>-1]) as $ano){
            $abigael =$ano["idPAno"];
          }
          $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$abigael;
          
          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados, $idPAno); 
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);
          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";
          
          $periodo = retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma, $idPAno);
         $sobreCurso = $manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

          echo "<script>var idPAno='".$idPAno."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";
          echo "<script>var tipoCurso='".valorArray($sobreCurso, "tipoCurso")."'</script>";
          echo "<script>var campoAvaliar='".valorArray($sobreCurso, "campoAvaliar", "cursos")."'</script>";
          
          $disciplinas = $manipulacaoDados->disciplinas($idCurso, $classe, $periodo, "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "nomeDisciplina", "disciplinas.continuidadeDisciplina"], $idPAno);

          $camposAvaliacao=array();
          $manipulacaoDados->camposAvaliacao=array();
          foreach($disciplinas as $luzl){
            $camposAvaliacao[$luzl["idPNomeDisciplina"]] = $manipulacaoDados->camposAvaliacaoAlunos($idPAno, $idCurso, $classe, $periodo, $luzl["idPNomeDisciplina"]);
          }
          echo "<script>var camposAvaliacao=".json_encode($camposAvaliacao)."</script>";

          $condicaoAdicional = ["reconfirmacoes.idReconfAno"=>$idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "arquivo_pautas.classePauta"=>$classe, "arquivo_pautas.idPautaAno"=>$idPAno, "arquivo_pautas.idPautaEscola"=>$_SESSION['idEscolaLogada'], "arquivo_pautas.idPautaCurso"=>$idCurso];

          $campos =["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "sexoAluno", "reconfirmacoes.observacaoF", 'arquivo_pautas.idPPauta','arquivo_pautas.idPautaMatricula','arquivo_pautas.idPautaDisciplina','arquivo_pautas.obs','arquivo_pautas.seFoiAoRecurso','arquivo_pautas.classePauta','arquivo_pautas.semestrePauta','arquivo_pautas.idPautaCurso','arquivo_pautas.chavePauta','arquivo_pautas.idPautaAno','arquivo_pautas.idPautaEscola'];
          foreach($manipulacaoDados->camposAvaliacao as $campo){
            $campos[] = 'arquivo_pautas.'.trim($campo["identUnicaDb"]);
          }
          echo "<script>var listaAlunos =".json_encode($manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma, $idPAno, array(), $campos, ["reconfirmacoes", "arquivo_pautas"], $condicaoAdicional))."</script>";
        ?>

<div class="card">
  <div class="card-body">
      <div class="row">
        <div class="col-lg-2 col-md-2 lead">
          Ano:
          <select class="form-control lead" id="anosLectivos">
            <?php 
              foreach($manipulacaoDados->selectArray("anolectivo", [], ["idPAno"=>array('$nin'=>[$manipulacaoDados->idAnoActual, 1]), "anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada']], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){                      
                echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
              }
            ?>
          </select>
        </div>
        <div class="col-lg-2 col-md-2 lead">
          Turma:
           <select class="form-control" id="luzingu">   
            <?php 
              foreach ($manipulacaoDados->turmasEscola(array(), array(), $idPAno) as $tur) {
                  echo "<option value='".$tur["nomeTurma"]."-".$tur["classe"]."-".$tur["idPNomeCurso"]."'>".$tur["abrevCurso"]." - ".classeExtensa($manipulacaoDados, $tur["idPNomeCurso"], $tur["classe"])." - ".$tur["designacaoTurma"]."</option>";
              } ?>                 
          </select>
        </div>
        <div class="col-lg-4 col-md-4 lead">
          Disciplina:
          <select class="form-control" id="listaDisciplinas">
          <?php 
            foreach ($disciplinas as $disciplina) {

                if(valorArray($sobreCurso, "tipoCurso")=="tecnico"){
                  $attr=$disciplina["disciplinas"]["continuidadeDisciplina"];
                }else{
                  $attr = $disciplina["disciplinas"]["tipoDisciplina"];
                }
                echo "<option value='".$disciplina["idPNomeDisciplina"]."' continuidadeDisciplina='".$disciplina["disciplinas"]["continuidadeDisciplina"]."'>".$disciplina["nomeDisciplina"]." (".$attr.")"."</option>";
            } 
          ?>       
        </select>
      </div>
      <div class="col-lg-2 col-md-2 lead">
          Referência:
          <select class="form-control" id="epocaReferencia">
          <option value="IV">Período Final</option>
          <option value="I">Iº Trimestre</option>
          <option value="II">IIº Trimestre</option>
          <option value="III">IIIº Trimestre</option>
        </select>
      </div>  
      <div class="col-lg-2 col-md-2 lead">
          Data:
          <input type="date" class="form-control" id="dataVisualizacao" value="<?php echo $manipulacaoDados->dataSistema; ?>">
      </div>  
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
          <a href="#" class="lead btn-warning btn" id="visualizarPublicar"><i class="fa fa-file-pdf"></i> Visualizar</a>&nbsp;&nbsp;&nbsp;
          <a class="btn-primary btn" href="#" id="visualizarMiniPauta"><i class="fa fa-print"></i> Mini-Pauta</a>
          &nbsp;&nbsp;&nbsp;<a class="btn-primary btn" href="../../relatoriosPdf/mapaAlunosQueTransitaram.php?classe=<?php echo $classe ?>&idPCurso=<?php echo $idCurso; ?>&idPAno=<?php echo $idPAno; ?>"><i class="fa fa-print"></i> Alunos que transitaram</a>&nbsp;&nbsp;&nbsp;
          <a class="btn-primary btn visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoGeralPorCurso"><i class="fa fa-print"></i> Mapa de Aproveitamento Geral do <?php echo $classe>=10?"Curso":"Ciclo";?></a>&nbsp;&nbsp;&nbsp;
        <a class="btn-primary btn visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoGeralEscola.php"><i class="fa fa-print"></i>Mapa de Aproveitamento Geral da Escola</a>

        </div>  
    </div>

    <div class="row">

      <div class="col-lg-8 col-md-8 visible-mg visible-lg">
      </div>
      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="pesqUsario">
        <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <input type="search" class="form-control lead pesquisaEntidade" tipoEntidade="alunos" placeholder="Pesquisar Aluno..." list="listaOpcoes">
            
        </div>   
      </div>
    </div>

    <div class="row" id="cabecalhoTable">
      <div class="col-lg-12 col-md-12 lead">Total: <span class="quantidadeTotal" id="numTotAlunos">0</span>
        &nbsp;&nbsp;&nbsp; Femininos: <span class="quantidadeTotal" id="numTotMasculino">0</span> &nbsp;&nbsp; Aprovados: <span class="quantidadeTotal" id="numTotAprovado">0</span></div>
    </div>

    <div id="listaAlunos" class="fotFoto">
        
    </div>

     <div class="row" id="paraPaginacao" style="margin-top: -10px;">
          <div class="col-md-10 col-lg-10 coluna">
            <div class="form-group paginacao">
                  
            </div>
          </div>
        </div>
        </div>
        </div><br>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
 
<div class="modal fade" id="publicarPautas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="publicarPautasForm" method="post">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-eye"></i> Visualização</h4>
              </div>

              <div class="modal-body">
                <h2 class="text-success font-weight-bolder" id="referenciaVisualizacao">PAUTA</h2>

                  <div class="row">
                    <div class="col-lg-2 col-md-2 lead">
                      Folha
                      <select class="form-control lead" id="tamanhoFolha">                          
                        <option class="lead">A3</option>        
                        <option class="lead">A2</option>
                        <option class="lead">A1</option>
                        <option class="lead">A0</option>
                        <option class="lead">A4</option>
                      </select>
                    </div>
                      <div class="col-lg-4 col-md-4 lead">
                      Modelo de Pauta:
                        <select class="form-control lead" id="tipoPauta">
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      Resultado
                        <select class="form-control lead" id="resultPauta">
                          <option value="definitivo">Definitivos</option>
                          <option value="naoDefinitivo">Não Definitivos</option>
                        </select>
                    </div>
                  </div>
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-left">
                      <button type="button" class="btn btn-primary lead visualizarPauta" tipo="YXM">
                        <i class="fa fa-eye"></i> Visualizar
                      </button>                      
                    </div>                     
                  </div>             
              </div>
          </form>
      </div>
  </div>

  <div class="modal fade" id="divMapasEstatisticos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: none;">
        <form class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-table"></i> Mapas Estatísticos</h4>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoGeralPorCurso"><i class="fa fa-print"></i> Mapa de Aproveitamento Geral do <?php echo $classe>=10?"Curso":"Ciclo";?></a>

                  </div> 
                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoGeralEscola.php"><i class="fa fa-print"></i> Mapa de Aproveitamento Geral da Escola</a></div>
                </div>

                <div class="row">
                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoDisciplinaPorCurso"><i class="fa fa-print"></i> Mapa de Aproveitamento do <?php echo $classe>=10?"Curso":"Ciclo";?> da Disciplina</a></div>

                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoDisciplinaEscola.php"><i class="fa fa-print"></i> Mapa de Aproveitamento da Escola da Disciplina</a></div>
                </div>

                <!--<div class="row">
                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoPrDisciplinasNoCurso.php"><i class="fa fa-print"></i> Mapa de Aproveitamento do <?php if($classe>=10){
                    echo "Curso";
                  }else{
                    echo "Ciclo";
                  }?> por Disciplinas</a></div>

                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAproveitamentoPrDisciplinasNaEscola.php"><i class="fa fa-print"></i> Mapa de Aproveitamento da Escola por Disciplinas</a></div>
                </div>!-->

                <div class="row">
                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAlunosExameRecurso.php"><i class="fa fa-print"></i> Alunos Submetido a Recurso</a></div>

                  <div class="col-md-6 col-sm-12"><a href="#" class="lead visualizadorMapa" caminho="mapasEstaticosDeAproveitamentoDosAlunos/mapaAlunosQueTransitaram.php"><i class="fa fa-print"></i> Alunos Que Transitararm com Deficiência</a></div>
                </div>

                </div>
              </div>
              
            </div>
          </form>
    </div>