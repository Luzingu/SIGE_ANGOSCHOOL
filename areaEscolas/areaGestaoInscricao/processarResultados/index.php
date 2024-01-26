<?php session_start();    
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");

    $manipulacaoDados = new manipulacaoDados("Processar Resultados", "processarResultados");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    inicializadorDaFuncaoGestaInscricao($manipulacaoDados);
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
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-hammer"></i> Processamento de Resultados</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["processarResultados"], array(), "msg")){ 

          $manipulacaoDados->conDb("inscricao");
          echo "<script>var gestorvagas=".$manipulacaoDados->selectJson("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual])."</script>";

         ?>    
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center" rowspan="2" style="vertical-align: middle;"><strong>Curso</strong></th>
                          <th class="lead text-center"><strong>Nº de Vagas</strong></th>
                          <th class="lead text-center"><strong>Critério</strong></th>
                          <th class="lead text-center"><strong>Estado</strong></th>
                          <th class="lead text-center"><strong><i class="fa fa-refresh"></i> Processar</strong></th>
                          <th class="lead text-center"><strong><i class="fa fa-print"></i> Relatórios</strong></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
            <div class="row" id="paraPaginacao" style="margin-top: -30px;">
                <div class="col-md-12 col-lg-12 coluna">
                    <div class="form-group paginacao">
                          
                    </div>
                </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>