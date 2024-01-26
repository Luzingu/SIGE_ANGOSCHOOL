<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Meses Atraso Sistema", "mesesAtrasoSistema");
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
  <style type="text/css">
        
        #detalhePagamento form table tr td{
          padding: 3px;
          font-size: 14pt;

        }
        #detalhePagamento form table tr td:nth-child(2){
          font-weight: bolder;
          padding-left: 10px;
        }
        #detalhePagamento form table tr td:nth-child(1){
          padding-right: 10px;
          text-align: right;
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-donate"></i> Meses em Atraso do Sistema</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "mesesPagoSistema", array(), "msg")){

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          $sobreContrato = $manipulacaoDados->selectArray("escolas", ["contrato.inicioPrazoPosPago", "contrato.mesesConsecutivosParaBloquear", "contrato.valorPagoPor15Dias"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["contrato"]);


          $dataDivida1=valorArray($sobreContrato, "inicioPrazoPosPago", "contrato");
          $dataDivida2=valorArray($sobreContrato, "inicioPrazoPosPago", "contrato");


          $mesesEmAtraso=array();
          for($i=1; $i<=20; $i++){

            if($i!=1){
              $dataDivida1 = $manipulacaoDados->adicionarDiasData(30, $dataDivida1);
            }
            $dataDivida2 = $manipulacaoDados->adicionarDiasData(30, $dataDivida2);

            if($dataDivida2<=$manipulacaoDados->dataSistema || ($i==valorArray($sobreContrato, "mesesConsecutivosParaBloquear", "contrato") && $dataDivida1<=$manipulacaoDados->dataSistema )){

              $mesesEmAtraso[]=array("dataDivida1"=>$dataDivida1, "dataDivida2"=>$dataDivida2, "valorPagoMensal"=>floatval(valorArray($sobreContrato, "valorPagoPor15Dias", "contrato"))*2);
            }else{
              break;
            }
          }
          echo "<script>var mesesEmAtraso=".json_encode($mesesEmAtraso)."</script>";
          ?>

      <div class="card">
        <div class="card-body">
            <div class="row">
              <div class="col-md-10 col-lg-10"><br/>
               <label class="lead">Total (Kz): <span id="totValores" class="quantidadeTotal">0</span></label>
              </div>
            </div>

            <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                    <th class="lead font-weight-bolder text-center"><strong>N.º</strong></th>
                    <th class="lead text-center"><strong>Período</strong></th>
                    <th class="lead text-center"><strong>Valor</strong></th>
                  </tr>
              </thead>
              <tbody id="tabDados">
                  
              </tbody>
            </table>
        </div>
      </div><br>     

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>