<?php session_start();
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pagamento do Sistema", "pagamentoSistemaPosPago");
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

    $objectoEscola = $manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']]);
    $sobreContrato  = listarItensObjecto($objectoEscola, "contrato");
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-donate"></i> Pagamento do Sistema</strong></h1>                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["pagamentoSistemaPosPago"], array(), "msg", "sim") && valorArray($sobreContrato, "tipoPagamento")=="pos"){

          $manipulacaoDados->valorContratatualDasEscolas($_SESSION["idEscolaLogada"]);

          echo "<script>var valorPagoPor15Dias ='".valorArray($sobreContrato, "   valorPagoPor15Dias")."'</script>";
          
          echo "<script>var pagamentos_escola=".$manipulacaoDados->selectJson("escolas", ["pagamentos.idPPagamento", "pagamentos.tempoTotalExtender", "pagamentos.argumentoRequerente", "pagamentos.valorTotalPago", "pagamentos.estadoPagamento", "pagamentos.horaReqPagamento", "pagamentos.valorDescontado", "pagamentos.horaRespPagamento", "pagamentos.dataReqPagamento", "pagamentos.dataRespPagamento", "pagamentos.argumentoResposta", "pagamentos.imgBolderon", "idPEscola"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["pagamentos"], 100, [], array("pagamentos.dataReqPagamento"=>-1))."</script>";        
        ?>

        <div class="row">
            <div class="col-lg-3 col-md-3 lead">
              
              <p style="font-size: 24pt; font-weight: 500; text-align:center;">
              <strong id="valorDisponivel"><?php echo number_format((double)valorArray($sobreContrato, "   valorPagoPor15Dias")*2, 2, ",", "."); ?> (Kzs) <br/>/ Mês</strong></p>
              
            </div>
            <div class="col-lg-5 col-md-5">
              <div class="text-danger" style="font-size: 12pt;">Prazo de Uso Até:<br/><br/><strong id="valorDisponivel" style="font-size: 22pt;"><?php echo dataExtensa(valorArray($sobreContrato, "inicioPrazoPosPago"))."<br>a<br>".dataExtensa(valorArray($sobreContrato, "fimPrazoPosPago")) ?></strong></div>
            </div>

            <div class="col-lg-4 col-md-4 lead">
              <strong class="text-primary">CONTA: LUZINGU LUAME</strong><br>
              BANCO: <strong>SOL</strong><br>
              N.º: <strong>1717092071 001</strong><br>
              IBAN: <strong>0044 0000 7170 9207 1018 5</strong><br>
              Tel: <strong>924 015 164 / 926 930 664</strong>
            </div>
      </div>
      <div class="row">
        <div class="col-lg-12 col-md-12">
          <?php 
            if(valorArray($sobreContrato, "imgContrato")!=NULL && valorArray($sobreContrato, "imgContrato")!=""){
              echo '<a class="lead btn btn-primary" href="../../../Ficheiros/Escola_'.valorArray($sobreContrato, "idEscolaContrato").'/Icones/'.valorArray($sobreContrato, "imgContrato").'" style="margin-bottom:10px;"><i class="fa fa-print"></i> Visualizar Contrato</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }

           ?>
          <button class="btn btn-success lead" id="btnAdicionarSaldo"><i class="fa fa-plus-circle"></i> Adicionar Saldo</button>&nbsp;&nbsp;&nbsp;
          <span class="lead" style="font-size:20pt;">Disponibilidade: <strong><?php echo number_format(valorArray($sobreContrato, "saldoParaPagamentoPosPago"), 2, ",", "."); ?> (Kzs)</strong></span>
        </div>
      </div>
       


       <div class="table-responsive">
        <table class="table table-hover" id="movimentos">
            <thead class="corPrimary">
                <tr>
                    <th colspan="2" class="text-center"><strong>Requisição</strong></th>
                    <th rowspan="2" class="text-center"><strong>Valor<br/>(Kz)</strong></th>
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
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-plus-circle"></i> Adicionar Saldo</h4>
              </div>

              <div class="modal-body">
                    <div class="row">                        
                        <div class="col-lg-4 col-md-4 lead">
                          Total Pagar (Kz)
                          <input type="number" style="color:black; font-weight: bolder; font-size:15pt;" min="0" step="5" name="valorTotalPago" id="valorTotalPago" class="form-control text-center" required="">
                        </div>
                        <div class="col-lg-8 col-md-8 lead">
                          Comprovativo
                          <input type="file" required=""  name="imgBolderon" id="imgBolderon" class="form-control">
                        </div>                       
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 lead">
                          Sobre o Pagamento
                          <input type="text"  name="argumentoRequerente" id="argumentoRequerente" class="form-control" required="">
                        </div>
                    </div>                   
              </div>
              <input type="hidden" name="idPPagamento" id="idPPagamento">
              <input type="hidden"  name="action" id="action">

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-7 col-md-7 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg" id="Cadastar"><i class="fa fa-check"></i> Concluir Operação</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>