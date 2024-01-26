<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pagamentos", "resumoPagamentos");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-money-bill"></i> Resumo de Pagamentos de Outros Emolumentos</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["resumoPagamentos"], array(), "msg")){

          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "numeroInterno", "idPMatricula", "grupo", "fotoAluno", "pagamentos.idHistoricoEscola", "pagamentos.dataPagamento", "pagamentos.horaPagamento", "pagamentos.nomeFuncionario", "pagamentos.idTipoEmolumento", "pagamentos.designacaoEmolumento", "pagamentos.referenciaPagamento", "pagamentos.idPHistoricoConta", "pagamentos.precoPago", "pagamentos.estadoPagamento"], ["pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$manipulacaoDados->idAnoActual, "pagamentos.dataPagamento"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-"), "pagamentos.idTipoEmolumento"=>array('$ne'=>1)], ["pagamentos"], "", [], ["pagamentos.dataPagamento"=>1, "pagamentos.horaPagamento"=>1]);

            echo "<script>var listaPagamentos=".json_encode($array)."</script>";
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
              <div class="col-md-7 col-lg-7"><br/>
               <label class="lead">Total Pago (Kz): <span id="totValores" class="quantidadeTotal">0</span></label>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12 col-lg-12">
              <a href="../../relatoriosPdf/relatoriosFinanceiros/relatPagDiarioOutrosEmolumentos.php?dataHistorico=<?php echo $manipulacaoDados->dataSistema; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório Diário</a>
              &nbsp;&nbsp;&nbsp;
              <a href="../../relatoriosPdf/relatoriosFinanceiros/relatPagMensalOutrosEmolumentos.php?mesPagamento=<?php echo $mesPagamento; ?>&anoCivil=<?php echo $anoCivil; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório Mensal</a>
            </div>
          </div>        
            <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead font-weight-bolder"><strong>Nome do Aluno</strong></th>
                      <th class="lead "><strong>Referência</strong></th>  
                      <th class="lead"><strong>Funcionário</strong></th>
                      <th class="lead text-center"><strong>Data</strong></th>
                      <th class="lead text-center"><strong>Valor</strong></th>
                      <th class="lead text-center"></th>
                      <th class="lead text-center"></th>
                       <th class="lead text-center"></th>
                  </tr>
              </thead>
              <tbody id="tabDados">
                  
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


<div class="modal fade" id="detalhePagamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
          <form class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Detalhes do Pagamento</h4>
                </div>

                <div class="modal-body">
                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                      
                      <table class="table table-hover" id="tabContas">
                          <tr>
                              <td class="lead text-right">Nome do Aluno:</td>
                              <td class="lead" id="nomeAluno"></td>
                          </tr>
                          <tr>
                              <td class="lead text-right">Número Interno:</td>
                              <td class="lead" id="numeroConta"></td>
                          </tr>

                          <tr>
                              <td class="lead text-right">Preço (Kz):</td>
                              <td class="lead" id="valLiquido"></td>
                          </tr>
                          <tr>
                              <td class="lead text-right">Multa (Kz):</td>
                              <td class="lead" id="valMulta"></td>
                          </tr>
                           <tr>
                              <td class="lead text-right">Valor Pago (Kz):</td>
                              <td class="lead" id="valPago"></td>
                          </tr>

                          <tr>
                              <td class="lead text-right">Data:</td>
                              <td class="lead" id="dataPagamento"></td>
                          </tr>

                          <tr>
                              <td class="lead text-right">Professor:</td>
                              <td class="lead" id="profPagamento"></td>
                          </tr>
                          <tr>
                              <td class="lead text-right">Referência:</td>
                              <td class="lead" id="refPagamento"></td>
                          </tr>

                          <tr>
                              <td class="lead text-right"></td>
                              <td class="lead" id="observPagamento"></td>
                          </tr>

                      </table>
                    
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-3 col-xs-6 col-sm-6">
                        <a href="#" class="btn btn-primary lead text-center" id="visualizarRecibo"><i class="fa fa-print" ></i> Recibo</a>
                  </div>
                  </div>                     
                </div>              
            </div>
          </form>
      </div>