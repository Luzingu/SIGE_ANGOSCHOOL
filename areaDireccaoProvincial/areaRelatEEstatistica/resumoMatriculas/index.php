<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Reconfirmados");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Relatório e Estatística";
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
</head>
 
<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->headerUsuario();
    $layouts->areaEstatERelat();
    $usuariosPermitidos = ["aRelEstatistica"];

    $privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"Pública";
    if($privacidade!="Pública" && $privacidade!="Privada"){
      $privacidade="Pública";
    }
    echo "<script>var privacidade='".$privacidade."'</script>";


  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand" style="color: white; font-weight: bolder;">(<?php echo $privacidade; ?>) Matrículas</h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){

         $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->selectUmElemento("anolectivo", "idPAno", "estado=:estado", ["V"], "numAno ASC");
        echo "<script>var idPAno='".$idPAno."'</script>";

        $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), $privacidade, "A"], "nomeEscola ASC");

        $idPEscola = $manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola AND idPEscola=:idPEscola AND estadoEscola=:estadoEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), $privacidade, $idPEscola, "A"], "nomeEscola ASC");

        echo "<script>var idPEscola='".$idPEscola."'</script>";


        $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados, $idPEscola);
        echo "<script>var luzingu='".$luzingu."'</script>";
        $luzingu = explode("-", $luzingu);
        $idCurso = isset($luzingu[2])?$luzingu[2]:"";
        $classe = isset($luzingu[1])?$luzingu[1]:"";
        $periodo = isset($luzingu[0])?$luzingu[0]:""; 

        echo "<script>var periodo='".$periodo."'</script>";
        echo "<script>var classeP='".$classe."'</script>";
        echo "<script>var idCursoP='".$idCurso."'</script>";

        echo "<script>var alunosReconfirmados=".$manipulacaoDados->selectCondClasseCurso("json", "alunosreconfirmados LEFT JOIN alunosmatriculados on idReconfMatricula=idPMatricula LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso LEFT JOIN entidadesprimaria ON idPEntidade=idReconfProfessor LEFT JOIN turmas ON idTurmaMatricula=idPMatricula", "*", "idReconfAno=:idReconfAno AND idReconfEscola=:idReconfEscola AND idMatEscola=idReconfEscola AND estadoAluno in ('A', 'Y') AND classeReconfirmacao=:classeReconfirmacao AND periodoAluno=:periodoAluno AND idTurmaAno=idReconfAno AND classeReconfirmacao=classeTurma", [$idPAno, $idPEscola, $classe, $periodo, $idCurso], $classe, " AND idMatCurso=:idMatCurso", "nomeAluno ASC")."</script>";

          ?>

         
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-2 lead">
              Ano: 
              <select class="form-control lead" id="anosLectivos">
                  <?php 
                    foreach($manipulacaoDados->selectArray("anolectivo", "*", "numAno>='2021'", [], "numAno DESC") as $ano){                      
                      echo "<option value='".$ano->idPAno."'>".$ano->numAno."</option>";
                    }
                   ?>
              </select>
            </div>
            <div class="col-md-5 col-lg-5 lead">
              Instituição
              <select class="form-control lead" id="idPEscola">
                <?php foreach($manipulacaoDados->selectArray("escolas", "*", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), $privacidade, "A"], "nomeEscola ASC") as $a){
                  echo "<option value='".$a->idPEscola."'>".$a->nomeEscola."</option>";
                } ?>
              </select>
            </div>
            <div class="col-lg-3 col-md-3 lead">
                Classe:
                <select class="form-control lead" id="luzingu">
                  <?php retornarClassesPorCurso($manipulacaoDados, "A", "sim", "nao", "nao", $idPEscola); ?>                         
                </select>
            </div>

          </div>
          <div class="row">
            <div class="col-md-12 col-lg-12">
              <label class="lead">Total: <span id="numTotAlunos" class="quantidadeTotal numTAlunos">0</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
              <label class="lead">Femininos: <span id="numTotAlunosM" class="quantidadeTotal numTMasculinos">0</span></label>
              &nbsp;&nbsp;<a href="#" class="btn btn-primary abrirRelatorio" caminho="resumoMatriculas"><i class="fa fa-print"></i> Resumo das Matriculas</a>
               &nbsp;&nbsp;
              <?php if($classe>=10){ ?>
               <a href="#" class="btn btn-primary abrirRelatorio" caminho="mapaFrequencias.php"><i class="fa fa-print"></i> Mapa de Frequências</a>
              <?php } ?>
               <?php if($classe>=10){ ?>
               <!--&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="btn btn-primary abrirRelatorio" caminho="estatistica.php"><i class="fa fa-print"></i> Matrícula</a>!-->
              <?php } ?>
            </div>
          </div>
          <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                      <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>

                      <th class="lead text-center"><strong><i class="fa fa-dungeon"></i> Turma</strong></th>
                  </tr>
              </thead>
              <tbody id="tabJaReconfirmados">

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
