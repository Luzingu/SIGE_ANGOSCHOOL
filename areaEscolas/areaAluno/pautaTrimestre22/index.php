<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pauta do Trimestre", "pautaTrimestre");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea =1;
    $layouts->designacaoArea ="Área do Aluno";
    $manipulacaoDados->retornarAnosEmJavascript();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .tabelaNotas tr td{

    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(1);

    $trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:1;
    if($trimestre<=0 || $trimestre>4){
      $trimestre=1;
    }
    $etiquetaTrimestre="I";
    if($trimestre==2){
      $etiquetaTrimestre="II";
    }else if($trimestre==3){
      $etiquetaTrimestre="III";
    }else if($trimestre==4){
      $etiquetaTrimestre="IV";
    }else{
      $etiquetaTrimestre="I";
    }
  ?>

  <section id="main-content"> 
    <section class="wrapper" id="containers">
      <div class="row">
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-table"></i> PAUTA <?php 
                if($trimestre==4){
                    echo "FINAL";
                }else{
                  echo "DO ".$etiquetaTrimestre." TRIMESTRE";
                }
               ?></h1>
          </nav>
        </div>
      </div>
      <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(1)){

          $manipulacaoDados->papaJipe("", "", "", $_SESSION['idUsuarioLogado']);
          

         ?>

          
        <div class="card">
          <div class="card-body">
           <?php $estadoPagamento="nao";
            $i=0;
            if(valorArray($manipulacaoDados->sobreTurmaActualAluno, "trimestrePublicado")<$trimestre){

              echo '<div class="row"><div class="text-center col-lg-12"><img style="width:200px; height:250px;" src="../../../icones/cadeado.png">
                <h2 class="text-danger">A pauta ';
                  if($trimestre==4){
                    echo 'Final';
                  }else{
                    echo "do ".$etiquetaTrimestre.' trimestre';
                  }
                echo ' ainda não foi publicada.</h2>
              </div></div></div><br/>';
            }else{

                if(valorArray($manipulacaoDados->sobreTurmaActualAluno, "mesApartirPublicado")<=0){
                    $estadoPagamento="sim";
                }else{

                    if(count(listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "pagamentos", ["idTipoEmolumento=1", "idHistoricoMatricula=".$_SESSION['idUsuarioLogado'], "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idHistoricoAno=".$manipulacaoDados->idAnoActual, "referenciaPagamento=".valorArray($manipulacaoDados->sobreTurmaActualAluno, "mesApartirPublicado")]))>0 ){
                        $estadoPagamento="sim";
                    }else if($manipulacaoDados->preco("propina", valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola"), valorArray($manipulacaoDados->sobreTurmaActualAluno, "mesApartirPublicado"), $manipulacaoDados->sobreUsuarioLogado)<=0) {
                        $estadoPagamento="sim";
                    }else{
                      $estadoPagamento="nao";
                    }
                }
                if($estadoPagamento=="nao"){
                    echo '<div class="row"><div class="col-lg-12"><br><div class="text-center"><img style="width:200px; height:250px;" src="../../../icones/cadeado.png">
                    <h2 class="text-danger">Não podes visualizar esta pauta, porque ainda não concluiu o pagamento do mês de '.nomeMes(valorArray($manipulacaoDados->sobreTurmaActualAluno, "mesApartirPublicado")).'</h2>
                  </div></div></div>';
                }else{
                  publicar($manipulacaoDados, $etiquetaTrimestre, $trimestre);
                }
            }
         ?>
        </div>
        </div>


        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();
  
    function publicar ($manipulacaoDados, $etiquetaTrimestre, $trimestre){
 
      $camposAvaliacoes = $manipulacaoDados->cabecalhoBoletim ($manipulacaoDados->idAnoActual, valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola"), valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), $etiquetaTrimestre);

        $_SESSION['estadoPublicacao']="sim";
        $_SESSION['etiquetaTrimestre']=$etiquetaTrimestre;
        $_SESSION['trimestre']=$trimestre;

        $mediaMac=0;
       $mediaNpp=0;
       $mediaNpt=0;
       $mediaMt=0;
       $contador=0;

        echo '
        <div class="row">
        <div class="col-lg-12 col-md-12">
        <p style="margin-top:0px;" class="lead text-right"><a class="btn btn-primary" href="../../relatoriosPdf/relatorioParaAlunos/boletimAproveitamento.php" class="btn btn-primary"><i class="fa fa-print"></i> Boletim</a></p>';

        $sobreAvaliacaoAnual = listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "reconfirmacoes", ["idReconfAno=".$manipulacaoDados->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada']]);

        if($_SESSION['trimestre']==4){
          echo '<h1 class="text-center"><strong>'.observacaoFinal(valorArray($sobreAvaliacaoAnual, "observacaoF"), valorArray($sobreAvaliacaoAnual, "seAlunoFoiAoRecurso")).'</strong></h1>';
        }

        echo '<div class="table-responsive">
        <table class="table table-striped table-bordered table-hover tabelaNotas">
          <thead class="corPrimary"><tr><td class="lead text-center"><strong>DISCIPLINA</strong></td>';

        foreach($camposAvaliacoes as $campo){
          echo '<td class="lead text-center"><strong>'.$campo["designacao2"].'</strong></td>';
        }
        echo '</tr></thead>';

        $curriculoClasse = $manipulacaoDados->disciplinas (valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola"), valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), valorArray($manipulacaoDados->sobreUsuarioLogado, "periodoAluno", "escola"), "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "nomeDisciplina"], $manipulacaoDados->idAnoActual);
        
        $array_putas = listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "pautas", ["classePauta=".valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola"), "idPautaCurso=".valorArray($manipulacaoDados->sobreUsuarioLogado, "idMatCurso", "escola")]);

        foreach ($curriculoClasse as $disc) { 
          echo '<tr><td class="lead">'.nelson($disc, "nomeDisciplina").'</td>';
          foreach($array_putas as $p)
          {
            if ($p["idPautaDisciplina"] == $disc["idPNomeDisciplina"])
            {
              foreach($camposAvaliacoes as $campo){
                echo retornarNota(valorArray($p, $campo["identUnicaDb"]), $campo["notaMedia"], $campo["cd"]);
              }
            }
          }
          echo "</tr>";
        }
        echo  '</table></div></div></div><br/>';    
    }


  function retornarNota ($nota, $notaMedia){
      
      if($nota=="" || $nota==null) {
          return "<td class='lead text-center'>==</td>";
      }else if($nota<$notaMedia){
          return "<td class='lead text-danger text-center'>".$nota."</td>";
      }else{
        return "<td class='lead text-center text-primary'>".$nota."</td>";
      }
  }

  function bomMau($a){
    if($a<5){
      return "<td><span class='text-danger'>Mau</span></td>";
    }else if($a<7){
      return "<td><span class='text-primary'>Suficiente</span></td>";
    }else if($a<8.5){
      return "<td><span class='text-success'>Bom</span></td>";
    }else{
      return "<td><span class='text-primary'>M. Bom</span></td>";
    }
  }

  function observacaoFinal($obs, $seAlunoFoiAoRecurso){
    if($obs=="A"){
      return "<span class='text-success'>APTO(A)</span>";
    }else if($obs=="TR"){
      return "<span class='text-success'>TRANSITA</span>";
    }else if($obs=="D"){
      return "<span class='text-danger'>DESISTENTE</span>";
    }else if($obs=="N"){
      return "<span class='text-danger'>ANULADA</span>";
    }else if($obs=="EF"){
      return "<span class='text-danger'>EX. FALTA</span>";
    }else if($obs=="D"){
      return "<span class='text-danger'>REP. INDISC.</span>";
    }else if($obs=="F"){
      return "<span class='text-danger'>REP. FALTAS.</span>";
    }else{
      if($seAlunoFoiAoRecurso=="A"){
        return "<span class='text-primary'>RECURSO</span>";
      }else{
        return "<span class='text-danger'>N. APTO(A)</span>";
      }
    }
  }
?>
