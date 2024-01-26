<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Control de Prazos", "controlPrazosEscolas");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-donate"></i> Control de Prazos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php
        if($verificacaoAcesso->verificarAcesso("", "controlPrazosEscolas", array(), "msg")){

            $listaEscolas = array();

            foreach ($manipulacaoDados->selectArray("escolas", ["nomeEscola", "numeroTelefone", "contrato.fimPrazoPosPago"], ["contrato.valorPagoPor15Dias"=>array('$gt'=>0), "idPEscola"=>array('$ne'=>4)], ["contrato"]) as $a) {

              $listaEscolas[] = array("nomeEscola"=>$a["nomeEscola"], "numeroTelefone"=>$a["numeroTelefone"], "fimPrazo"=>$a["contrato"]["fimPrazoPosPago"], "numeroDias"=>calcularDiferencaEntreDatas($a["contrato"]["fimPrazoPosPago"], $manipulacaoDados->dataSistema));
            }
            $listaEscolas = ordenar($listaEscolas,"numeroDias ASC");

            echo "<script>var listaEscolas = ".json_encode($listaEscolas)."</script>";
        ?>
        <div class="card">
          <div class="card-body">
            <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                <tr>
                  <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
                  <th class="lead"><strong>Escola</strong></th>
                  <th class="lead"><strong>N.º Tel.</strong></th>
                  <th class="lead text-center"><strong>Fim de Prazo</strong></th>
                  <th class="lead text-center"><strong>N.º Restante</strong></th>
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
