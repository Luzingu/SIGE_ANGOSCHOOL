<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pag. do Sistema", "pagamentoSistemaPosPago00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-donate"></i> Pagamento do Sistema - <?php echo $manipulacaoDados->selectUmElemento("escolas", "abrevNomeEscola2", ["idPEscola"=>$idPEscola]) ?></strong></h1>                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "pagamentoSistemaPosPago00", array(), "msg")){
          
          $manipulacaoDados->valorContratatualDasEscolas($idPEscola);

          $sobreContrato =$manipulacaoDados->selectArray("escolas", [],["idPEscola"=>$idPEscola], ["contrato"]);

          echo "<script>var valorPagoPor15Dias ='".valorArray($sobreContrato, "valorPagoPor15Dias", "contrato")."'</script>";
          echo "<script>var pagamentos_escola=".$manipulacaoDados->selectJson("escolas", ["pagamentos.idPPagamento", "pagamentos.tempoTotalExtender", "pagamentos.argumentoRequerente", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.horaReqPagamento", "pagamentos.dataReqPagamento", "pagamentos.valorDescontado", "pagamentos.horaRespPagamento", "pagamentos.dataRespPagamento", "pagamentos.argumentoResposta", "idPEscola", "pagamentos.imgBolderon"], ["idPEscola"=>$idPEscola], ["pagamentos"], 100, [], array("pagamentos.dataReqPagamento"=>-1))."</script>";       
        ?>
        <div class="row">
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 lead">
            <label>Escola</label>
            <select class="form-control lead" id="idPEscola">
              <?php 

              $array = $manipulacaoDados->selectArray("escolas",["idPEscola", "nomeEscola"], ["contrato.tipoPagamento"=>"pos"], ["contrato"], "", [], array("nomeEscola"=>1));

              foreach($array as $a){
                echo "<option value='".$a["idPEscola"]."'>".$a["nomeEscola"]."</option>";
              } ?>
            </select>
          </div>
        </div>
        <div class="row">

            <div class="col-lg-4 col-md-4 lead">
              <p style="font-size: 24pt; font-weight: 500; text-align:center;">
              <strong id="valorDisponivel"><?php echo number_format((double)valorArray($sobreContrato, "valorPagoPor15Dias", "contrato")*2, 2, ",", "."); ?> (Kzs) <br/> - 30 dias</strong></p>
              
            </div>
            <div class="col-lg-3 col-md-3">
              <div class="text-danger" style="font-size: 12pt;">Data do Fim do Prazo:<br/><br/><strong id="valorDisponivel" style="font-size: 17pt;"><?php echo dataExtensa(valorArray($sobreContrato, "inicioPrazoPosPago", "contrato"))."<div class='text-center'>a</div>".dataExtensa(valorArray($sobreContrato, "fimPrazoPosPago", "contrato")); ?></strong></div>
            </div>

            <div class="col-lg-5 col-md-5 lead">
              <strong class="text-primary">LUZINGU LUAME LDA</strong><br>
              BANCO: <strong>SOL</strong><br>
              N.º DA CONTA: <strong>171709207 10 001</strong><br>
              IBAN: <strong>AO06.0044.0000.7170.9207.1018.5</strong><br>
              DIR. GERAL.: <strong>AFONSO LUZINGU</strong><br>
              TEL: <strong>921 785 681</strong>
            </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-md-12">
          <span class="lead" style="font-size:20pt;">Disponibilidade: <strong><?php echo number_format((double)valorArray($sobreContrato, "saldoParaPagamentoPosPago", "contrato"), 2, ",", "."); ?> (Kzs)</strong></span>
        </div>
      </div>
       


       <div class="table-responsive">
        <table class="table table-hover" id="movimentos">
            <thead class="corPrimary">
                <tr>
                    <th colspan="2" class="text-center"><strong>Requisição</strong></th>
                    <th rowspan="2" class="text-center"><strong>Valor<br/>(Kz)</strong></th>
                    <th rowspan="2" class="text-center"><strong>Desconto<br/>(Kz)</strong></th>
                    <th colspan="2" class="text-center"><strong>Resposta</strong></th>
                    <th rowspan="2" class="text-center"></th>

                    <th rowspan="2" class="text-center"><strong>Estado</strong></th>
                    <th rowspan="2" class="text-center"></th>
                </tr>
                <tr>
                  <th class="lead font-weight-bolder"><strong>Data</strong></th>
                    <th class="lead"><strong>Descrição</strong></th>
                  <th class="lead font-weight-bolder"><strong>Data</strong></th>
                    <th class="lead"><strong>Descrição</strong></th>
                </tr>
            </thead>
            <tbody>
                
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
<?php $conexaoFolhas->folhasJs();$janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioAdicionarSaldo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioAdicionarSaldoForm">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-donate"></i> Pagamentos</h4>
          </div>

          <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 lead">
                      Sobre o Pagamento:
                      <p class="lead text-primary" id="argumentoRequerente"></p>
                    </div>                      
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    <label>Responder</label>
                    <input type="text"  name="argumentoResposta" id="argumentoResposta" class="form-control" required="" placeholder="Por favor digite aqui uma resposta...">
                  </div>
                </div> 
                <div class="row">
                  <div class="col-lg-4 col-md-4 lead">
                    <label>Total Pagos (Kz)</label>
                    <input type="text" style="color:black; font-weight: bolder; font-size:14pt;" readonly="" name="valorTotalPago" id="valorTotalPago" class="form-control text-center" required="">
                  </div> 
                </div>                  
          </div>
          <input type="hidden" name="idPPagamento" id="idPPagamento">
          <input type="hidden" name="idPEscola" id="idPEscola" value="<?php echo $idPEscola; ?>">
          <input type="hidden"  name="action" id="action">

          <div class="modal-footer">
              <div class="row">
                <div class="col-lg-12 col-md-12 text-left ">
                  <button type="submit" action="aceitarPagamento" class="btn btn-success lead btn-lg"><i class="fa fa-check-circle"></i> Aceitar</button>&nbsp;&nbsp;
                  <button type="button" action="recusarPagamento" class="btn btn-danger lead btn-lg submitter"><i class="fa fa-times-circle"></i> Recusar</button>
                </div>                    
              </div>                
          </div>
        </div>
      </form>
  </div>
