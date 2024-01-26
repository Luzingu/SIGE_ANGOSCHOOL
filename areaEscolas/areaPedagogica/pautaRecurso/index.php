<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pauta de Recurso", "pautaRecurso");
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

    #listaAlunos form input.valorCt{
        background-color: transparent;
        color: black;
        font-weight: 700;
    }
     #listaAlunos form .obs{
      font-weight: bolder;
     }
    #listaAlunos form input{
      font-size: 12pt !important;
      padding: 0px;
    }

     #listaAlunos form .nomeAluno{
      font-size: 15pt !important;
     }

     .nomeAluno{
      font-size: 15pt;
     }
     @media (max-width: 768px) {
        #referenciaDisciplina{
        font-size: 11pt;
        margin-top: 10px;
      }
     }

     #paraPaginacao ul li a{
      height: 40px;
      font-size: 12pt;
      padding: 5px;
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
        <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno text-center" style="padding:8px;">
        <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-print"></i> Pauta do Recurso
        </strong></h1>
      </nav>
      </div>
    </div>

<?php if($verificacaoAcesso->verificarAcesso("", ["pautaRecurso"], array(), "msg")){

    $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
    echo "<script>var idPAno='".$idPAno."'</script>";

    if(isset($_GET["luzingu"])){
      $luzingu = $_GET["luzingu"];
    }else{
      $array = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "tipoCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"]);
      if(valorArray($array, "tipoCurso")=="tecnico"){
        $luzingu = "10-".valorArray($array, "idPNomeCurso");
      }else{
        $luzingu = $manipulacaoDados->ultimaClasse(valorArray($array, "idPNomeCurso"))."-".valorArray($array, "idPNomeCurso");
      }

    }


    echo "<script>var luzingu='".$luzingu."'</script>";
    $classe=explode("-", $luzingu)[0];
    $idPNomeCurso=explode("-", $luzingu)[1];

    $sobreCurso = $manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPNomeCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
    echo "<script>var classe='".$classe."'</script>";
    echo "<script>var idPNomeCurso='".$idPNomeCurso."'</script>";
    echo "<script>var tipoCurso='".valorArray($sobreCurso, "tipoCurso")."'</script>";
    echo "<script>var campoAvaliar='".valorArray($sobreCurso, "campoAvaliar", "cursos")."'</script>";

    $alunos = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "grupo", "numeroInterno", "pautas.classePauta", "pautas.seFoiAoRecurso", "pautas.cf", "pautas.mf", "pautas.recurso", "reconfirmacoes.classeReconfirmacao", "reconfirmacoes.designacaoTurma", "pautas.idPautaDisciplina", "reconfirmacoes.observacaoF", "sexoAluno", "idPMatricula", "pautas.mf", "pautas.cf"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idPAno, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.seAlunoFoiAoRecurso"=>"A", "pautas.seFoiAoRecurso"=>"A", "reconfirmacoes.classeReconfirmacao"=>$classe, "pautas.classePauta"=>$classe, "pautas.idPautaCurso"=>$idPNomeCurso, "reconfirmacoes.idMatCurso"=>$idPNomeCurso], ["reconfirmacoes", "pautas"], "", [], ["nomeAluno"=>1]);
    $alunos = $manipulacaoDados->anexarTabela2($alunos, "nomedisciplinas", "pautas", "idPNomeDisciplina", "idPautaDisciplina");


    echo "<script>var pautas=".json_encode($alunos)."</script>";

  ?>

<div class="card">
  <div class="card-body">
          <div class="main-body">

    <div class="row">
      <div class="col-lg-2 col-md-2 lead">
        Ano:
        <select class="form-control lead" id="anosLectivos">
          <?php
            foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"]], ["anos_lectivos"], 2, [], ["numAno"=>-1]) as $ano){
              echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
            }
          ?>
        </select>
      </div>
      <div class="col-lg-3 col-md-3 lead">
        Classe:
        <select class="form-control lead" id="luzingu">
          <?php
          foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "duracao", "tipoCurso", "abrevCurso", "classes.identificador", "classes.abreviacao1", "classes.seComRecurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){


            foreach (listarItensObjecto($curso, "classes") as $l) {
              if(isset($l["seComRecurso"]) && $l["seComRecurso"] == "A")
              {
                echo "<option value='".$l["identificador"]."-".$curso["idPNomeCurso"]."'>".$l["abreviacao1"]." - ".$curso["abrevCurso"]."</option>";
              }
            }


          }

           ?>
        </select>
      </div>
      <div class="col-lg-7 col-md-7"><br>
        <a href="../../relatoriosPdf/mapaAlunosExameRecurso.php?classe=<?php echo $classe ?>&idPCurso=<?php echo $idPNomeCurso; ?>&idPAno=<?php echo $idPAno; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório</a>

      </div>
    </div>

    <div class="row" id="paraPaginacao" style="margin-top: -10px;" style="display: none;">
          <div class="col-lg-12 col-md-12 text-right">
            <button class="btn btn-success lead btnAlterarNotas"><i class="fa fa-check"></i> Alterar</button>
          </div>
      </div>


      <table id="example1" class="table table-striped table-bordered table-hover" >
          <thead class="corPrimary">
                <tr>
            <th class="lead text-center"><strong>Nº</strong></th>
            <th class="lead"><strong>Nome Completo</strong></th>
            <th class="lead text-center"><strong>Número Interno</strong></th>
            <th class="lead text-center"><strong>Disciplina</strong></th>
            <th class="lead text-center"><strong>Média</strong></th>
            <th class="lead text-center" style="width:130px;"><strong>Recurso</strong></th>
        </tr>

          </thead>
          <tbody id="tabela">
          </tbody>
      </table>

    <div class="row">
      <div class="col-lg-12 col-md-12 text-right">
        <button class="btn btn-success lead btnAlterarNotas"><i class="fa fa-check"></i> Alterar</button>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();
 ?>
