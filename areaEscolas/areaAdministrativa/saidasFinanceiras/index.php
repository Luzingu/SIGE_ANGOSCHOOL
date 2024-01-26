<?php session_start();       
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Saídas Financeiras", "saidasFinanceiras");
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
    span#valorDisponivel{
        background-color: darkblue;
        border-radius: 20px;
        padding: 2px;
        color: white;
        padding-left: 10px;
        padding-right: 10px;
        font-weight: bolder;
        border: solid rgba(0, 0, 0, 0.6) 2px;
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-money-check"></i> Liquidações Financeiras</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["saidasFinanceiras"], array(), "msg")){

          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          echo "<script>var listaSaidaValores=".$manipulacaoDados->selectJson("facturas", [], ["idFacturaEscola"=>$_SESSION['idEscolaLogada'], "tipoFactura"=>"RP", "dataEmissao"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-")], [], "", [], ["idPFactura"=>-1])."</script>";
          ?>
    
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-2 col-md-2 lead">
            Ano:
            <select class="form-control lead" id="anosLectivos">
              <?php 
              for($i=explode("-", $manipulacaoDados->dataSistema)[0]; $i>=2023; $i--){
                echo "<option>".$i."</option>";
              } 
              ?>
            </select>
          </div>
          <div class="col-md-2 col-lg-2 lead">
            Mês
            <select class="form-control lead" id="mesPagamento">
              <?php 
              foreach($manipulacaoDados->mesesAnoLectivo as $m){
                echo "<option value='".completarNumero($m)."'>".nomeMes($m)."</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-3 col-lg-3 lead"><br>
            <button class="btn btn-success lead" id="novaSaida"><i class="fa fa-plus-circle"></i> Nova Saida</button>
          </div>
          <div class="col-lg-5 col-md-5 text-right"><br>
            <a href="../../relatoriosPdf/relatoriosFinanceiros/relatorioDiarioDeSaidaValores.php?dataHistorico=<?php echo $manipulacaoDados->dataSistema; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório Diário</a>&nbsp;&nbsp;&nbsp;
            <a href="../../relatoriosPdf/relatoriosFinanceiros/relatorioMensalDeSaidaValores.php?mesPagamento=<?php echo $mesPagamento; ?>&anoCivil=<?php echo $anoCivil; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório Mensal</a>
          </div>
        </div>

            <div class="table-responsive">
              <table id="example1" class="table table-bordered table-striped">
                <thead class="corPrimary">
                  <tr>
                    <th class="lead font-weight-bolder text-center"><strong>N.º</strong></th>
                    <th class="lead "><strong>Data</strong></th>
                    <th class="lead"><strong>Funcionário</strong></th>
                    <th class="lead"><strong>Empresa</strong></th>
                    <th class="lead text-center"><strong>Valor</strong></th>
                    <th class="lead text-center"><strong>Estado</strong></th>
                    <th class="lead text-center"></th>
                    <th class="lead text-center"></th>
                  </tr>
                </thead>
                <tbody id="tabHistorico">
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs();  $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();
 ?>


 <div class="modal fade" id="formularioSaidaValores" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioSaidaValoresForm" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-money-check"></i> Saída de Valores</h4>
              </div>
              <div class="modal-body">

                <div class="row paraCamposGerais">
                  <div class="col-lg-12 col-md-12 lead">
                    <label>Empresa</label>
                    <select class="form-control" required id="idPParceira" name="idPParceira">
                      <option value="">Seleccionar</option>
                      <?php 
                      foreach(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "empresa_parceiras") as $empresa){
                        echo '<option value="'.$empresa["idPParceira"].'">'.$empresa["nomeEmpresa"].' ('.$empresa["nifEmpresa"].')</option>';
                      }
                       ?>
                      <option value="0">Desconhecida</option>
                    </select>
                  </div>
                </div>
                <div class="row paraCamposGerais">
                  <div class="col-lg-9 col-md-9 lead">
                  <label>Serviço</label>
                    <select class="form-control" required id="idPItem" name="idPItem">
                      <option value="">Seleccionar</option>
                      <?php 
                      foreach(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "itens_financeiros") as $empresa){
                        echo '<option value="'.$empresa["idPItem"].'" valorItem="'.$empresa["valorItem"].'">'.$empresa["designacaoItem"].'</option>';
                      }
                       ?>
                    </select>
                  </div>
                  <div class="col-md-3 col-lg-3 lead">
                    <label>Valor Unitário</label>
                    <input type="number" min="0" required class="form-control lead text-center vazio" id="valorUnitario" name="valorUnitario">
                  </div>
                </div>

                <div class="row paraCamposGerais">

                  <div class="col-md-3 col-lg-3 lead">
                    <label>Quantidade</label>
                    <input type="number" required min="1" class="form-control text-center lead" id="quantidade" name="quantidade">
                  </div>

                  <div class="col-md-4 col-lg-4 lead">
                    <label>Valor Total</label>
                    <input type="text" style="background-color: transparent !important; font-weight: bolder;" readonly class="form-control text-center lead" id="valorTotal" name="valorTotal">
                  </div>

                  <div class="col-md-5 col-lg-5 lead">
                    <label>Conta</label>
                    <select id="contaUsar" name="contaUsar" class="form-control lead" required>
                      <option value="">Seleccionar</option>
                      <option value="geral_Caixa">Geral - Caixa</option>
                      <option value="geral_Banco">Geral - Banco</option>
                      <option value="propinas_Caixa">Propinas - Caixa</option>
                      <option value="propinas_Banco">Propinas - Banco</option>
                    </select>
                  </div>
                </div>
                <div class="row paraCamposGerais">
                  <div class="col-lg-12 col-md-12 text-center">
                    <p class="text-right" style="font-size: 20pt;"><span id="valorDisponivel">0</span></p>
                  </div>
                </div>

                <div class="row paraCamposAnular">
                  <div class="col-lg-12 col-md-12">
                    <label>Motivo</label>
                    <textarea class="form-control lead" name="motivoCancelamento" id="motivoCancelamento"></textarea>
                  </div>
                </div>

                <input type="hidden" id="mesPagamento" name="mesPagamento" value="<?php echo $mesPagamento; ?>">
                <input type="hidden" id="anoCivil" name="anoCivil" value="<?php echo $anoCivil; ?>">
                <input type="hidden" id="idPFactura" name="idPFactura">
                <input type="hidden" id="action" name="action" value="processarSalario">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-md-12 col-lg-12 text-left">
                      <button type="submit" class="btn btn-primary btn lead submitter" id="Cadastar"><i class="fa fa-check"></i> Concluir </button>
                    </div>                   
                  </div>                
              </div>
          </form> 
      </div>
    </div>
