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
    $trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:"I";
    if($trimestre!="I" && $trimestre!="II" && $trimestre!="III" && $trimestre!="IV"){
        $trimestre="I";
    }
    echo "<script>var trimestre='".$trimestre."'</script>";

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><i class="fa fa-star"></i> <strong>Top 10 dos Melhores Alunos (<?php 
                    if($trimestre!="IV"){
                      echo $trimestre." Trimestre";
                    }
                   ?>)</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  
        if($verificacaoAcesso->verificarAcesso($usuariosPermitidos)){

          $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->selectUmElemento("anolectivo", "idPAno", "estado=:estado", ["V"], "numAno ASC");
          echo "<script>var idPAno='".$idPAno."'</script>";

          $label="mfT4";
          if($trimestre=="I"){
            $label="mfT1";
          }else if($trimestre=="II"){
            $label="mfT2";
          }else if($trimestre=="III"){
            $label="mfT3";
          }

          $quadroHonra = $manipulacaoDados->selectArray("alunosreconfirmados LEFT JOIN alunosmatriculados on idReconfMatricula=idPMatricula LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso LEFT JOIN avaliacaoanualaluno ON idAvalMatricula=idReconfMatricula LEFT JOIN turmas ON idTurmaMatricula=idReconfMatricula LEFT JOIN escolas ON idPEscola=idMatEscola", "*", "idReconfAno=:idReconfAno AND idTurmaEscola=idMatEscola AND idTurmaEscola=idReconfEscola AND idTurmaAno=idReconfAno AND idAvalAno=idReconfAno AND classeReconfirmacao!=13 AND ".$label." IS NOT NULL AND provincia=:provincia AND idPEscola not in (4, 7)", [$idPAno, valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia")], $label." DESC, dataNascAluno DESC LIMIT 10");

          $posicao=0;
          foreach ($quadroHonra as $quadro){
            $posicao++;
            $quadro->idPMatricula = ($posicao);
          }

        echo "<script>var quadroHonra=".json_encode($quadroHonra)."</script>";
          ?>
          <div class="card">
            <div class="card-body">
              <form class="row" method="POST" id="formPesquisar">
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
                <div class="col-md-10 col-lg-10"><br>
                     <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
                </div>                 
              </form> 
              <table id="example1" class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                          <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                          <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>                                    
                          <th class="lead text-center"><strong>Curso</strong></th>
                          <th class="lead text-center"><strong>Classe</strong></th>
                          <th class="lead text-center"><strong>Turma</strong></th>
                          <th class="lead text-center"><strong>Média</strong></th>
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
