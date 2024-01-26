<?php 
    session_cache_expire(60);
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../");
    $manipulacaoDados = new manipulacaoDados("Turmas do Professor - Professor", "turmasProfessor");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = 2;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .forTurmaProfessor{
      background-color: #428bca; !important;
      min-height: 140px;
      color: white;
      border: solid rgba(255, 255, 255, 0.3) 2px;
      border-radius: 5px;
      padding: 5px;
      margin-bottom: 20px;
    }

    .forTurmaProfessor .nomeClasse{
      font-weight: 300;

    }
    .nomeDisciplina{
      font-size: 16pt;
    }
    .periodoTurma{
      margin-top: 15pt;
      color: rgba(255, 255, 255, 0.6);
    }

    .forTurmaProfessor .nomeTurma{
      font-size: 26pt;
      font-weight: 700;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside (2);
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
      <div class="row" >
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-dungeon"></i> Turmas do Professor</strong></h1>
        </nav>
      </div>
    </div>
    <div class="main-body">
    <?php  if($verificacaoAcesso->verificarAcesso(2, array(), array(), "msg")){

      foreach ($manipulacaoDados->selectArray("divisaoprofessores", ["classe", "sePorSemestre", "designacaoTurmaDiv", "nomeDisciplina", "abrevCurso", "idPNomeCurso"], ["idDivAno"=>$manipulacaoDados->idAnoActual, "idPEntidade"=>$_SESSION['idUsuarioLogado'], "idPEscola"=>$_SESSION['idEscolaLogada']], [], "", [], ["nomeTurmaDiv"=>1]) as $divisao){

          $sobreCurso=$sobreCurso = $divisao["abrevCurso"]." - ";  

          echo "<a class='col-lg-3 col-md-3 col-sm-12 col-xs-12 divTurma' href='#' style='text-decoration:none; outline:0;'><div class='forTurmaProfessor'><div class='lead nomeClasse text-center'>".$sobreCurso.classeExtensa($manipulacaoDados, $divisao["idPNomeCurso"] $divisao["classe"])."</div><div class='lead nomeTurma text-center'>".$divisao["designacaoTurmaDiv"]."</div><div class='lead text-center nomeDisciplina'>".$divisao["nomeDisciplina"]."</div></div></a>";
        }
 

        } echo "</div><br/>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

