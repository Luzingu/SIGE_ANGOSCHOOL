<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Ficheiro SAFT", "ficheiroSaft");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
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
    $tipoFicheiro = isset($_GET["tipoFicheiro"])?$_GET["tipoFicheiro"]:"R";

    $designacaoFicheiro="";
    if($tipoFicheiro=="R"){
      $designacaoFicheiro="Recibos";
    }else{
      $tipoFicheiro="R";
      $designacaoFicheiro="Recibos";
    }
    echo "<script>var tipoFicheiro='".$tipoFicheiro."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["ficheiroSaft"], array(), "msg")){
          
          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          ?>

      <div class="card" style="margin-top:20px;">
        <div class="card-body" style="min-height: 300px;">
            <br>
            <h1 class="text-primary" style="text-transform: uppercase; font-weight:bolder; text-align: center;">FICHEIRO SAFT - <?php echo $designacaoFicheiro; ?></h1><br>

            <div class="row">
              <div class="col-lg-2 col-md-2 col-lg-offset-3 col-md-offset-3 lead">
                De:
                <input type="date" class="form-control" id="dataInicial" value="2023-09-01">
              </div>
              <div class="col-md-2 col-lg-2 lead">
                Até
                <input type="date" class="form-control" id="dataFinal" value="<?php echo $manipulacaoDados->dataSistema; ?>"> 
              </div>

              <div class="col-md-1 col-lg-1 lead"><br>
                <a href="#" class="btn btn-primary" id="gerarFicheiro"><i class="fa fa-print"></i> Gerar</a>
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