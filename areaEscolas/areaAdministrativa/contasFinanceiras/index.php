<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pagamentos", "contasFinanceiras");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-print"></i> Contas Financeiras</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["contasFinanceiras"], array(), "msg")){
          
          echo "<script>var listaContaBancaria = ".$manipulacaoDados->selectJson("contas_bancarias", [], ["idContaEscola"=>$_SESSION['idEscolaLogada']])."</script>";

          ?>

      <div class="card">
        <div class="card-body">
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <button class="btn btn-success lead" id="btnNovaConta"><i class="fa fa-plus-circle"></i> Nova Conta</button>
              </div>
            </div>
            
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center font-weight-bolder"><strong>N.º</strong></th>
                        <th class="lead font-weight-bolder"><strong>Descrição</strong></th>
                        <th class="lead text-center"><strong>Banco</strong></th>
                        <th class="lead text-center"><strong>Categoria</strong></th>
                        <th class="lead text-center"><strong>Hierarquia</strong></th> 
                        <th class="lead text-center"><strong>Saldo</strong></th> 
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


<div class="modal fade" id="formularioContaBancaria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioContaBancariaForm">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Contas</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12 col-md-12 lead">
                <label>Descrição da Conta</label>
                <input type="text" maxlength="100" required class="form-control vazio" id="descricaoConta" name="descricaoConta">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-4 col-md-4">
                <label>Banco</label>
                <select class="form-control" name="bancoConta" id="bancoConta">
                  <option value="">Nenhum</option>
                  <option>BPC</option>
                  <option>BAI</option>
                  <option>BIC</option>
                  <option>SOL</option>
                  <option>BFA</option>
                </select>
              </div>
              <div class="col-lg-8 col-md-8">
                <label>N.º da Conta</label>
                <input type="text" id="numeroConta" name="numeroConta" class="form-control lead vazio">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <label>IBAN</label>
                <input type="text" id="ibanConta" name="ibanConta" class="form-control lead vazio">
              </div>
              <div class="col-lg-6 col-md-6">
                <label>Categoria e Tipo de Conta</label>
                <select id="categoriaTipoConta" required name="categoriaTipoConta" class="form-control">
                  <option value="GR">(GR) Conta de 1.º grau da Contabilidade geral</option>
                  <option value="GA">(GA) Conta agregadora ou integradora da contabilidade geral</option>
                  <option value="GM">(GM) Conta de Movimento da contabilidade Geral</option>
                  <option value="AR">(AR) Conta de 1.º grau da contabilidade analítica</option>
                  <option value="AA">(AA) Conta agregadora ou integradoraa da contabilidade analítica</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-3">
                <label>Hierarquia</label>
                <input type="number" min="0" class="form-control text-center" id="hierarquia" name="hierarquia">
              </div>
            </div>
            <input type="hidden" id="action" name="action">
            <input type="hidden" id="idPContaFinanceira" name="idPContaFinanceira">
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