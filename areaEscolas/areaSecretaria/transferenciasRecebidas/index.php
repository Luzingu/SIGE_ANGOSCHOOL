<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Transferências Recebidas", "transferenciasRecebidas");
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
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
    $usuariosPermitidos[] = "aSecretaria";

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-mail-reply-all"></i> Transferências Recebidas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php   

        if($verificacaoAcesso->verificarAcesso("", "transferenciasRecebidas", array(), "msg")){

          $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
          echo "<script>var idPAno='".$idPAno."'</script>";

          $luzinguLuame = $manipulacaoDados->selectArray("alunosmatriculados", [], ["transferencia.idTransfEscolaDestino"=>$_SESSION['idEscolaLogada'], "transferencia.idTransfAno"=>$idPAno, "transferencia.estadoTransferencia"=>"V"], ["transferencia"],"", [], ["nomeAluno"=>1]);
 
          $luzinguLuame = $manipulacaoDados->anexarTabela2($luzinguLuame, "transferencia", "escolas", "idPEscola", "idTransfEscolaOrigem");

          echo "<script>var alunosTransferidos=".json_encode($luzinguLuame)."</script>";
          ?>

      <div class="card">
        <div class="card-body">
          <div class="row">
              <div class="col-lg-2 col-md-2 col-xs-12 col-sm-12 lead">
                Ano Lectivo:
                <select class="form-control" id="anosLectivos">
                <?php 
                  foreach($manipulacaoDados->anosLectivos as $ano){                      
                    echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                  }
                 ?>
                  </select>
              </div>
              <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12"><br/>
                 <label class="lead">
                      Total: <span class="numTAlunos quantidadeTotal">0</span>
                  </label>
              </div>
          </div>

          <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                  <tr>
                      <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                      <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                      <th class="lead"><strong><i class="fa fa-school"></i> Origem</strong></th>
                                           
                      <th class="lead text-center"><strong><i class="fa fa-calendar"></i> Data</strong></th>
                  </tr>
              </thead>
              <tbody id="tabTransferencia">

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