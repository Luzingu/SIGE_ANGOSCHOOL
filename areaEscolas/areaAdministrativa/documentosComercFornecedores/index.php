<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Documentos Comerciais a Fornecedores", "documentosComercFornecedores");
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
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-print"></i> Documentos Comerciais de Fornecedores</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["documentosComercFornecedores"], array(), "msg")){
          
          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          echo "<script>var listaPagamentos = ".$manipulacaoDados->selectJson("purchase_invoices", [],["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-")],[], "", [], ["idPDocumento"=>1])."</script>";

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
                <button class="btn btn-success" id="btnNova"><i class="fa fa-plus-circle"></i> Novo Documento</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a class="btn btn-primary" href="../../relatoriosPdf/mapaPagamentoFornecedores.php?anoCivil=<?php echo $anoCivil ?>&mesPagamento=<?php echo $mesPagamento; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório</a>
              </div>
            </div>
            
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center font-weight-bolder"><strong>N.º</strong></th>
                        <th class="lead font-weight-bolder"><strong>Data</strong></th>
                        <th class="lead text-center"><strong>Doc.</strong></th>
                        <th class="lead text-center"><strong>Fornecedor</strong></th>
                        <th class="lead text-center"><strong>Valor</strong></th>
                        <th class="lead text-center"><strong>IVA</strong></th>
                        <th class="lead text-center"><strong>Retenção</strong></th>
                        <th class="lead text-center"><strong>Total</strong></th>
                        <th class="lead text-center"><strong>Nota</strong></th>
                        <th class="lead"><strong></strong></th>
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


<div class="modal fade" id="formulario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioForm">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Docs. Comerciais de Fornecedores</h4>
          </div>
          <div class="modal-body">
            <div class="row">

              <div class="col-lg-3 col-md-3">
                <label>Referência</label>
                <input type="text" required class="form-control text-center vazio" id="referenciaDocumento" name="referenciaDocumento">
              </div>

              <div class="col-lg-4 col-md-4">
                <label>Data</label>
                <input type="date" required class="form-control vazio" id="dataDocCompra" name="dataDocCompra">
              </div>
              <div class="col-lg-5 col-md-5">
                <label>Tipo de Doc.</label>
                <select class="form-control" name="tipoDocumento" id="tipoDocumento">
                  <option value="FT">Fatura</option>
                  <option value="FR">Fatura/recibo</option>
                  <option value="GF">Factura genérica</option>
                  <option value="FG">Factura global</option>
                  <option value="AC">Aviso de cobrança</option>
                  <option value="AR">Aviso de cobrança/Recibo</option>
                  <option value="AF">Factura/Recibo</option>
                  <option value="TV">Talão de Venda</option>
                  <option value="NL">Nota de Liquidação</option>
                  <option value="NC">Nota de Crédito</option>
                </select>
              </div>
            </div>
            <div class="row">

              <div class="col-lg-9 col-md-9">
                <label>Fornecedor</label>
                <select class="form-control" required name="idFornecedor" id="idFornecedor">
                  <?php 
                  foreach(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "fornecedores") as $f){
                    echo "<option value='".$f["idPFornecedor"]."'>".$f["nomeEmpresa"]."</option>";
                  }
                   ?>
                </select>
              </div>
              <div class="col-lg-3 col-md-3">
                <label>Vaor Liquidado</label>
                <input type="number" min="0" step="0.01" class="form-control text-center vazio" id="valorLiquidado" name="valorLiquidado">
              </div>             
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-3">
                <label>IVA</label>
                <input type="number" min="0" step="0.01" class="form-control text-center vazio" id="IVA" name="IVA">
              </div>
              <div class="col-lg-4 col-md-4">
                <label>Total</label>
                <input type="number" readonly style="background-color: white;" min="0" class="form-control text-center vazio" id="totalLiquidado" name="totalLiquidado">
              </div>
              <div class="col-lg-5 col-md-5">
                <label>Conta Usada</label>
                <select class="form-control" id="contaUsar" name="contaUsar">
                  <?php 
                    foreach($manipulacaoDados->selectArray("contas_bancarias", ["idPContaFinanceira", "descricaoConta"], ["idContaEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                      echo "<option value='".$a["idPContaFinanceira"]."'>".$a["descricaoConta"]."</option>";
                    }
                    echo "<option value='0'>Desconhecida</option>";
                   ?>
                </select>
              </div>
            </div>

            <div class="row" id="paraIsencaoIVA">
              <div class="col-lg-3 col-md-3">
                <label>Cód. T. de Impo.</label>
                <input type="text" class="form-control text-center" value="IVA" id="codigoTipoImpostoRetido" readonly name="codigoTipoImpostoRetido">
              </div>
              <div class="col-lg-3 col-md-3">
                <label>Montante Retido</label>
                <input type="number" class="form-control text-center vazio" id="montanteImpostoRetido" name="montanteImpostoRetido">
              </div>
              <div class="col-lg-6 col-md-6">
                <label>Motivo da Retenção</label>
                <input type="text" class="form-control vazio" id="motivoDaRetencao" name="motivoDaRetencao">
              </div>
            </div>

            <input type="hidden" id="action" name="action">
            <input type="hidden" id="mesPagamento" name="mesPagamento" value="<?php echo $mesPagamento ?>">
            <input type="hidden" id="anoCivil" name="anoCivil" value="<?php echo $anoCivil ?>">
            <input type="hidden" id="idPDocumento" name="idPDocumento">
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-lg-12 col-md-12 text-right">
                  <button type="submit" id="Cadastrar" class="btn btn-success lead btn-lg"><i class="fa fa-check"></i> Concluir</button>
                </div>                    
              </div>                
          </div>
        </div>
      </form>
  </div>