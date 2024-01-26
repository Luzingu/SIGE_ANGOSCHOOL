<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Contr. Mensal de Pagamentos", "controlMensalPagamentos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?> 
  
<!DOCTYPE html>
<html lang="pt">
<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
     #containers span#valorDisponivel{
        background-color: darkblue;
        border-radius: 20px;
        padding: 2px;
        color: white;
        padding-left: 10px;
        padding-right: 10px;
        font-weight: bolder;
        border: solid rgba(0, 0, 0, 0.6) 2px;
      }

      #valDeposito, #valDesconto, #vistoDirector, .visualizadorDocumento{
        padding: 5px;
        border-radius: 10px;
      }
      #movimentos, #movimentos tr td, #movimentos tr th{
        font-size: 11pt !important;
      }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
   $layouts->cabecalho();
    $layouts->aside();

    $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]: $manipulacaoDados->selectUmElemento("escolas", "idPEscola", ["pagamentos.estadoPagamento"=>"Y"]);
      echo "<script>var idPEscola='".$idPEscola."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-donate"></i> Control de Pagamentos</strong></h1>                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "controlMensalPagamentos", array(), "msg")){
          
          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";
          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          echo "<script>var pagamentos_escola=".$manipulacaoDados->selectJson("escolas", ["nomeEscola", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.horaReqPagamento", "contrato.valorPagoPor15Dias", "pagamentos.dataReqPagamento", "idPEscola", "pagamentos.imgBolderon"], ["pagamentos.dataReqPagamento"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-"), "pagamentos.estadoPagamento"=>"V"], ["pagamentos", "contrato"], 100, [], array("pagamentos.dataReqPagamento"=>-1))."</script>";    

 
        ?>
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-2 col-md-2 lead">
                  Ano:
                <select class="form-control lead" id="anoCivil">
                  <?php 
                  for($i=explode("-", $manipulacaoDados->dataSistema)[0]; $i>=2023; $i--){
                    echo "<option>".$i."</option>";
                  } 
                  ?>
                </select>
              </div>
              <div class="col-md-3 col-lg-3 lead">
                Mês
                <select class="form-control lead" id="mesPagamento">
                  <?php 
                  foreach($manipulacaoDados->mesesAnoLectivo as $m){
                    echo "<option value='".completarNumero($m)."'>".nomeMes($m)."</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-lg-7 col-md-7"><br>
                <span class="lead" style="font-size:20pt;">Total: <strong id="valorTotalPago"></strong></span>
              </div>

            </div>
      <table id="example1" class="table table-striped table-bordered table-hover" >
        <thead class="corPrimary">
          <tr>
            <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
            <th class="lead"><strong>Data</strong></th>
            <th class="lead"><strong>Escola</strong></th>
            <th class="lead text-center"><strong>Valor Pago/Mês</strong></th> 
            <th class="lead text-center"><strong>Total</strong></th>
            <th class="lead text-center"></th>
          </tr>
        </thead>
        <tbody id="tabela">
        </tbody>
      </table>
      </div> 
    </div>      

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs();$janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>