<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Transição Manual dos Alunos", "transicaoManual");
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

    #divMapasEstatisticos .modal-dialog{
      width: 60%;
      margin-left: -30%;
    }
    @media (max-width: 768px) {
          #divMapasEstatisticos .modal-dialog, .modal .modal-dialog{
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

    $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
    echo "<script>var luzingu='".$luzingu."'</script>";
    $luzingu = explode("-", $luzingu);
    $classe=isset($luzingu[1])?$luzingu[1]:"";
    $idCurso = isset($luzingu[2])?$luzingu[2]:"";
    $turma = isset($luzingu[0])?$luzingu[0]:"";
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
           <div class="row" >
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-redo"></i><strong id="pGeral"> Transição Manual</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php
        $idPAnoPassado = valorArray($manipulacaoDados->selectArray("anolectivo", ["idPAno", "numAno"], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"], "anos_lectivos.estadoAnoL"=>array('$ne'=>"V")], ["anos_lectivos"], 1, [], ["numAno"=>-1]), "idPAno");

        $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$idPAnoPassado;

        if($verificacaoAcesso->verificarAcesso("", "transicaoManual", array(), "msg")){


          $periodo = retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma, $idPAno);
          $sobreCurso = $manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

          $planoCurricular = $manipulacaoDados->disciplinas($idCurso, $classe, $periodo, "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "disciplinas.classeDisciplina", "disciplinas.semestreDisciplina", "disciplinas.semestreDisciplina", "disciplinas.continuidadeDisciplina", "disciplinas.tipoDisciplina", "nomeDisciplina"], "todas");
          if($idCurso=="" || $classe==""){
            $planoCurricular=array();
          }
          $camposAvaliacao=array();
          foreach($planoCurricular as $luzl){
            $camposAvaliacao[$luzl["idPNomeDisciplina"]] = $manipulacaoDados->camposAvaliacaoAlunos($idPAno, $idCurso, $classe, $periodo, $luzl["idPNomeDisciplina"]);
          }
          echo "<script>var camposAvaliacao=".json_encode($camposAvaliacao)."</script>";

          echo "<script>var planoCurricular=".json_encode($planoCurricular)."</script>";

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idPAno='".$idPAno."'</script>";
          echo "<script>var idAnoPassado='".$idPAnoPassado."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";
          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var tipoCurso='".valorArray($sobreCurso, "tipoCurso")."'</script>";
          echo "<script>var campoAvaliar='".valorArray($sobreCurso, "campoAvaliar", "cursos")."'</script>";
          echo "<script>var sePorSemestre='".valorArray($sobreCurso, "sePorSemestre")."'</script>";
          echo "<script>var idEscolaLogada='".$_SESSION["idEscolaLogada"]."'</script>";

          $alunos = $manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma, $idPAno, array(), ["idPMatricula", "nomeAluno", "numeroInterno", "reconfirmacoes.observacaoF", "sexoAluno", "grupo", "reconfirmacoes.classeReconfirmacao", "escola.classeActualAluno"], [], array());

          echo "<script>var listaAlunos =".json_encode($alunos)."</script>";
          $turmasEscola = $manipulacaoDados->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$idPAno], [], "", [], ["nomeCurso"=>1, "classe"=>1, "nomeTurma"=>1]);
        ?>

    <div class="card">
        <div class="card-body">
  <div class="row" id="dadosAlunos">
    <div class="col-lg-5 col-md-5">
      <div class="text-center"><img src="../../../fotoUsuarios/default.png" style="width: 110px; height: 110px; border-radius: 50%;"></div>
      <h2 class="text-center" id=""><strong class="vazio" id="nomeAluno"></strong></h2>
      <div class="col-lg-6 col-md-6">
        <h5>Nascido aos: <strong class="vazio" id="dataNascAluno">--</strong></h5>
        <h5>Idade: <strong class="vazio" id="idadeAluno">--</strong></h5>
      </div>
      <div class="col-lg-6 col-md-6">
        <h5>Sexo: <strong class="vazio" id="sexoAluno">--</strong></h5>
        <h5>Estado: <strong class="vazio" id="estadoActividadeAluno">--</strong></h5>
      </div>
    </div>
    <div class="col-lg-7 col-md-7">
        <div class="row">

          <div class="col-lg-4 col-md-4 lead">
              Turma:
               <select class="form-control" id="luzingu">
                <?php
                foreach ($turmasEscola as $tur) {

                    $lDia =$tur["abrevCurso"]." - ".classeExtensa($manipulacaoDados, $tur["idPNomeCurso"], $tur["classe"])." - ".$tur["designacaoTurma"];
                    echo "<option value='".$tur["nomeTurma"]."-".$tur["classe"]."-".$tur["idPNomeCurso"]."'>".$lDia."</option>";
                } ?>
              </select>
          </div>
          <div class="col-lg-8 col-md-8 lead">
              Alunos:
               <select class="form-control" id="selectAluno">
                <option value="">Seleccionar</option>
                <?php
                    $i=0;
                    foreach ($alunos as $aluno) {
                        $i++;
                        echo "<option value='".$aluno["idPMatricula"]."' posicao='".$i."' grupoAluno='".$aluno["grupo"]."'>".$aluno["nomeAluno"]."</option>";
                    }
                ?>
              </select>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-4"><br>
              <h2 class="text-center"><strong id="observacaoF"></strong></h2><br>
              <div class="col-lg-12 col-md-12 lead">
                <label>Estado do Aluno</label>
                <select class="form-control lead" id="estadoAluno">
                  <option value="A">Activo</option>
                  <option value="D">Desistente</option>
                  <option value="N">Mat. Anulada</option>
                  <option value="F">Excluido por Faltas</option>
                  <?php if($_SESSION['idEscolaLogada']==27){ ?>
                  <option value="A/TRANSF">Apto/Transferido</option>
                  <option value="NA/TRANSF">Não Apto/Transferido</option>
                  <?php } ?>
                  <option value="RI">Reprov. por Indisciplina</option>
                  <option value="RFN">Reprov. por Falta de Notas</option>
                </select>
              </div>
          </div>
          <div class="col-lg-8 col-md-8">
          <fieldset  style="border-radius: 10px; border: solid black 0.5px; padding: 5px; ">
              <legend style="width: 140px; margin-bottom: 0px;" class="lead">Deficiências<strong id="totalDeficiencia"></strong></legend>
              <div id="listaDificiencias1" class="col-lg-6 col-md-6"></div>
              <div id="listaDificiencias2" class="col-lg-6 col-md-6"></div>
          </fieldset>
          </div>
        </div>
        <div class="col-lg-12">
          <h4 class="text-danger" id="infoSobreAluno" style="line-height: 25px; font-weight:bolder;"></h4>
        </div>

    </div>
  </div>
    <div class="row">
      <div class="col-lg-2 col-md-2 lead">
        Ano:
        <select class="form-control lead" id="anosLectivos">
          <?php
            foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"]], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){

              echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
            }
          ?>
        </select>
      </div>
      <div class="col-lg-4 col-md-4 text-center"><br>
        <a href="#" id="recuarAluno" title="Recuar"><i class="fa fa-arrow-left fa-2x"></i></a>&nbsp;&nbsp;&nbsp;
        <a href="#" id="avancarAluno" title="Avançar"><i class="fa fa-arrow-right fa-2x"></i></a>
      </div>
      <div class="col-lg-6 col-md-6 text-right"><br>
        <?php if($idPAno==$idPAnoPassado){ ?>
        <button class="btn btn-primary lead" id="actualizarEstadoAluno"><i class="fa fa-refresh"></i> Actualizar</button>&nbsp;&nbsp;&nbsp;
        <?php } ?>
        <button class="btn btn-primary lead" id="btnRefresh"><i class="fa fa-spinner"></i> Refresh</button>&nbsp;&nbsp;&nbsp;

        <button class="btn btn-success lead btnAlterarNotas" style="display:none;" id="btn1"><i class="fa fa-check"></i> Alterar</button>
          &nbsp;&nbsp;&nbsp;
          <button class="btn btn-success lead" id="copiarDados"><i class="fa fa-copy"></i> Copiar</button>
      </div>
    </div>

    <div id="listaAlunos" class="fotFoto">
        <h1 class="text-center">Por favor Selecciones um aluno...</h1>
    </div>

     <div class="row">
      <div class="col-lg-2 col-md-2"><br>
            <button class="btn btn-success lead btnAlterarNotas" style="display:none;"><i class="fa fa-check"></i> Alterar</button><br><br>
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

<form id="formularioDados">
    <input type="hidden" name="action" id="action" value="alterarNotas">
    <input type="hidden" name="idPCurso" id="idPCurso">
    <input type="hidden" name="classe" id="classe">
    <input type="hidden" name="turma" id="turma">
    <input type="hidden" name="periodoTurma" id="periodoTurma">
    <input type="hidden" name="tipoCurso" id="tipoCurso">

    <input type="hidden" name="idAlunoSeleccionado" id="idAlunoSeleccionado">
    <input type="hidden" name="idPAno" id="idPAno">
    <input type="hidden" name="grupoAluno" id="grupoAluno">
    <input type="hidden" name="dados" id="dados">
 </form>
