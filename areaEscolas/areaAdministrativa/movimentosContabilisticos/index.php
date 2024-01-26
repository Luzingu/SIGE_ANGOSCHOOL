<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");

    $manipulacaoDados = new manipulacaoDados("Movimentos Contabilisticos", "movimentosContabilisticos");

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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-print"></i> Movimentos Contabilísticos</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["movimentosContabilisticos"], array(), "msg")){

          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";
          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";
          
          echo "<script>var listaMovimentos = ".$manipulacaoDados->selectJson("general_ledger_entries", [],["idDocEscola"=>$_SESSION['idEscolaLogada'], "dataEmissao"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-"), "sePagSalario"=>"I"],[], "", [], ["idPDocumento"=>1])."</script>";

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
                <button class="btn btn-success" id="btnNova"><i class="fa fa-plus-circle"></i> Novo Movimento</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a class="btn btn-primary" href="../../relatoriosPdf/movimentosContabilisticos.php?anoCivil=<?php echo $anoCivil ?>&mesPagamento=<?php echo $mesPagamento; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório</a>
              </div>
            </div>
            
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center font-weight-bolder"><strong>N.º</strong></th>
                        <th class="lead font-weight-bolder"><strong>Data</strong></th>
                        <th class="lead text-center"><strong>Descrição</strong></th>
                        <th class="lead text-center"><strong>Tipo</strong></th>
                        <th class="lead text-center"><strong>Conta</strong></th>
                        <th class="lead text-center"><strong>Valor</strong></th>
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
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Movimentos Contabilísticos</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <label>Descrição do movimento</label>
                <input type="text" class="form-control vazio" required id="descricaoMovimento" name="descricaoMovimento">
              </div>
              <div class="col-lg-3 col-md-3">
                <label>Nº do Arquivo</label>
                <input type="number" class="form-control vazio text-center" required id="numArquivoDocumento" name="numArquivoDocumento">
              </div>
              <div class="col-lg-3 col-md-3">
                <label>Tipo de Mvt</label>
                <select class="form-control" id="tipoMovimento" required name="tipoMovimento">
                  <option value="N">Normal</option>
                  <option value="R">Regularização do período de tributação</option>
                  <option value="A">Apuramento de resultados</option>
                  <option value="J">Movimento de ajustamento</option>
                </select>
              </div>
            </div>
            <div class="row">
              
              <div class="col-lg-3 col-md-3">
                <label>Data do Mvt Cont.</label>
                <input type="date" class="form-control vazio" id="dataMovimentoContabilistico" name="dataMovimentoContabilistico">
              </div>
              <div class="col-lg-2 col-md-2">
                <label>Ident. Diário</label>
                <input type="text" required class="form-control vazio text-center" id="identificadorDiario" name="identificadorDiario">
              </div>
              <div class="col-lg-7 col-md-7">
                <label>Descrição do Diário</label>
                <input type="text" required class="form-control vazio" id="descricaoDiario" name="descricaoDiario">
              </div>
            </div>

            
            <div class="row">
              <div class="col-lg-4 col-md-4">
                <label>Natureza</label>
                <select class="form-control" id="movimento" required name="movimento">
                  <option value="Credito">Crédito</option>
                  <option value="Debito">Débito</option>
                </select>
              </div>
              <div class="col-lg-4 col-md-4">
                <label>Conta</label>
                <select class="form-control" id="contaLinha" name="contaLinha">
                  <?php 
                    foreach($manipulacaoDados->selectArray("contas_bancarias", ["idPContaFinanceira", "descricaoConta"], ["idContaEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                      echo "<option value='".$a["idPContaFinanceira"]."'>".$a["descricaoConta"]."</option>";
                    }
                   ?>
                </select>
              </div>
              <div class="col-lg-4 col-md-4">
                <label>Valor</label>
                <input type="number" class="form-control text-center vazio" id="valorLinha" name="valorLinha">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-7 col-md-7">
                <label>Descrição da Linha</label>
                <input type="text" class="form-control vazio" id="descricaoLinhha" name="descricaoLinhha">
              </div>
            </div>
            
            <input type="hidden" id="action" name="action">
            <input type="hidden" id="idPDocumento" name="idPDocumento">
            <input type="hidden" id="mesPagamento" name="mesPagamento" value="<?php echo $mesPagamento ?>">
            <input type="hidden" id="anoCivil" name="anoCivil" value="<?php echo $anoCivil ?>">
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