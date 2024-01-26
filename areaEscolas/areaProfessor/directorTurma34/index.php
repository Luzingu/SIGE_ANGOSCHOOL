<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados( "Director de Turma", "directorTurma34");
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
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-user-tie"></i><strong id="pGeral"> Director de Turma</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  

          if(isset($_GET["luzingu"]))
            $luzingu = $_GET["luzingu"];
          else{
            $array = $manipulacaoDados->selectArray("listaturmas", [], ["idListaAno"=>$manipulacaoDados->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idCoordenadorTurma"=>$_SESSION['idUsuarioLogado']], [], 1);
            $luzingu = valorArray($array, "nomeTurma")."-".valorArray($array, "classe")."-".valorArray($array, "idPNomeCurso");
          }
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);
          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";
          $periodo = retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma);
          $semestreActivo = retornarSemestreActivo($manipulacaoDados, $idCurso, $classe);

          $trimestreDefault="I";
          if($manipulacaoDados->mes>=3 && $manipulacaoDados->mes<=5){
            $trimestreDefault="II";
          }else if($manipulacaoDados->mes>5 && $manipulacaoDados->mes<=7){
            $trimestreDefault="IV";
          }
          echo "<script>var trimestreDefault='".$trimestreDefault."'</script>";

          if(count($manipulacaoDados->selectArray("listaturmas", ["nomeTurma"], ["idListaAno"=>$manipulacaoDados->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idCoordenadorTurma"=>$_SESSION['idUsuarioLogado'], "classe"=>$classe, "nomeTurma"=>$turma, "idPNomeCurso"=>$idCurso]))<=0){
            $classe="";
            $idCurso = "";
            $turma ="";
          }

          $sobreCurso = $manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";
          echo "<script>var tipoCurso='".valorArray($sobreCurso, "tipoCurso")."'</script>";
          echo "<script>var campoAvaliar='".valorArray($sobreCurso, "campoAvaliar", "cursos")."'</script>";
          echo "<script>var idAnoActual='".$manipulacaoDados->idAnoActual."'</script>";
          echo "<script>var idEscolaLogada='".$_SESSION['idEscolaLogada']."'</script>";

          $disciplinas = $manipulacaoDados->disciplinas($idCurso, $classe, $periodo, "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "nomeDisciplina", "disciplinas.tipoDisciplina"]);
          $camposAvaliacao=array();
          $manipulacaoDados->camposAvaliacao=array();
          foreach($disciplinas as $luzl){
            $camposAvaliacao[$luzl["idPNomeDisciplina"]] = $manipulacaoDados->camposAvaliacaoAlunos($manipulacaoDados->idAnoActual, $idCurso, $classe, $periodo, $luzl["idPNomeDisciplina"]);
          }
          echo "<script>var camposAvaliacao=".json_encode($camposAvaliacao)."</script>";

          $campos =["idPMatricula", "nomeAluno", "numeroInterno", "fotoAluno", "sexoAluno", "reconfirmacoes.observacaoF", 'pautas.idPPauta','pautas.idPautaMatricula','pautas.idPautaDisciplina','pautas.obs','reconfirmacoes.seAlunoFoiAoRecurso','pautas.classePauta','pautas.semestrePauta','pautas.idPautaCurso','pautas.chavePauta','pautas.idPautaAno','pautas.idPautaEscola'];
          foreach($manipulacaoDados->camposAvaliacao as $campo){
            $campos[] = 'pautas.'.trim($campo["identUnicaDb"]);
          }
          echo "<script>var listaAlunos =".json_encode($manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma, $manipulacaoDados->idAnoActual, array(), $campos, ["reconfirmacoes", "pautas"], ["reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "pautas.classePauta"=>$classe, "pautas.idPautaCurso"=>$idCurso]))."</script>";
        ?>
<div class="card">
  <div class="card-body">
      <div class="row">
         <div class="col-lg-3 col-md-3 lead">
            Turma:
             <select class="form-control" id="luzingu">
              <?php 
              foreach($manipulacaoDados->selectArray("listaturmas", [], ["idListaAno"=>$manipulacaoDados->idAnoActual, "idPEscola"=>$_SESSION['idEscolaLogada'], "idCoordenadorTurma"=>$_SESSION['idUsuarioLogado']]) as $tur){

                echo "<option value='".$tur["nomeTurma"]."-".$tur["classe"]."-".$tur["idPNomeCurso"]."'>".$tur["abrevCurso"]." - ".classeExtensa($manipulacaoDados, $tur["idPNomeCurso"], $tur["classe"])." - ".$tur["designacaoTurma"]."</option>";
              }
              ?>
            </select>
        </div>
        <div class="col-lg-4 col-md-4 lead">
          Disciplina:
          <select class="form-control" id="listaDisciplinas">
          <?php  
            foreach ($disciplinas as $disciplina) {
              if(valorArray($sobreCurso, "tipoCurso")=="tecnico"){
                $attr=valorArray($disciplina, "continuidadeDisciplina", "disciplinas");
              }else{
                $attr = $disciplina["disciplinas"]["tipoDisciplina"];
              }
              echo "<option value='".$disciplina["idPNomeDisciplina"]."' continuidadeDisciplina='".valorArray($disciplina, "continuidadeDisciplina", "disciplinas")."'>".$disciplina["nomeDisciplina"]." (".$attr.")"."</option>";
            }
          ?>       
        </select>
      </div>
      <div class="col-lg-2 col-md-2 lead">
          Referência:
          <select class="form-control" id="epocaReferencia">
          <option value="I">Iº Trimestre</option>
          <option value="II">IIº Trimestre</option>
          <option value="III">IIIº Trimestre</option>
          <option value="IV">Período Final</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12 col-md-12">
        <a href="#" class="btn btn-warning" id="imprimirlistaAlunos"><i class="fa fa-print"></i> Lista dos Alunos</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#" class="btn btn-warning" id="horarioTurma"><i class="fa fa-print"></i> Horário da Turma</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#" class="btn btn-primary imprimirPauta" tamanhoFolha="A3" tipoPauta="pautaGeral"><i class="fa fa-print"></i> Pauta Geral</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="#" class="btn btn-primary imprimirPauta" tamanhoFolha="A4" tipoPauta="resumo"><i class="fa fa-print"></i> Resumo</a>&nbsp;&nbsp;&nbsp;&nbsp;
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
        <?php  echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>