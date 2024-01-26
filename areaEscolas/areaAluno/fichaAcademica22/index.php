<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Ficha Académica", "fichaAcademica");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->designacaoArea ="Área do Aluno";
    $layouts->idPArea =1;
    $manipulacaoDados->retornarAnosEmJavascript();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .infoTurma h3{
      margin-bottom: -20px;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(1);
    $usuariosPermitidos[] = "Aluno";
  ?>

  <section id="main-content"> 
    <section class="wrapper" id="containers">
      <div class="row">
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-info-circle"></i> FICHA ACADÉMICA</h1>
          </nav>
        </div>
      </div>
    <div class="main-body">
    <?php if($verificacaoAcesso->verificarAcesso(1)){
           ?>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" >
              <tr style="font-weight:bolder;"><td class='lead corPrimary text-center'>Disciplina</td><td class='lead corPrimary text-center'>MF</td><td class='lead corPrimary text-center'>REC</td><td class='lead corPrimary text-center'>EXAME ESP.</td></tr>
              <?php 
                $classe=array();
                if(seEnsinoPrimario()){
                  $classes=[0,1,2,3,4,5,6];
                }
                if(seEnsinoBasico()){
                  $classes[]=7;$classes[]=8;$classes[]=9;
                }
                if(seEnsinoSecundario()){
                  $classes[]=10;$classes[]=11;$classes[]=12;$classes[]=13;
                }

                foreach($classes as $classe){
                  if($classe<valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola")){

                    echo "<tr><td class='lead text-center' colspan='4'><strong>".classeExtensa($manipulacaoDados, valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola"), $classe)."</strong></td></tr>";

                    $counter=0;

                    if($classe<10){
                      $notas = listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "pautas", ["classePauta=".$classe]);
                    }else{
                      $notas = listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "pautas", ["classePauta=".$classe, "idPautaCurso=".valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola")]);
                    }

                    foreach($notas as $nota){
                      $counter++;
                      echo "<tr><td class='lead'>".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$nota["idPautaDisciplina"]])."</td>".retornarNota(nelson($nota, "mf"), $nota["classePauta"]).retornarNota(nelson($nota, "notaRecurso"), $nota["classePauta"]).retornarNota(nelson($nota, "exameEspecial"), $nota["classePauta"])."</tr>";
                    }
                    
                  }
                }


               ?>
            </table></div>
          </div>
        </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();


function retornarNota ($nota, $classe){
      $notaMinima=10;
      if($classe<=6){
        $notaMinima=5;
      }
      if($nota=="" || $nota==null) {
          return "<td class='lead text-center' style='width:60px;'>==</td>";
      }else if($nota<$notaMinima){
          return "<td class='lead text-danger text-center' style='width:60px;'>".$nota."</td>";
      }else{
        return "<td class='lead text-center text-primary' style='width:60px;'>".$nota."</td>";
      }
  } 
?>