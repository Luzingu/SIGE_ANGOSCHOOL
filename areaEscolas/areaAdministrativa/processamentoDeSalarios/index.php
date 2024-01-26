<?php session_start();       
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Processamento de Saída de Valores", "processamentoDeSalarios");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-money-check"></i> Pagamento de Salário</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["processamentoDeSalarios"], array(), "msg")){
          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          echo "<script>var listaFuncionarios=".$manipulacaoDados->selectJson("entidadesprimaria", ["idPEntidade", "nomeEntidade", "salarios.salarioLiquido", "salarios.idPSalario", "salarios.dataPagamento", "salarios.horaPagamento", "salarios.nomeFuncProc", "salarios.contaDebitada"], ["salarios.idEscola"=>$_SESSION['idEscolaLogada'], "salarios.anoCivil"=>$anoCivil, "salarios.mesPagamento"=>$mesPagamento], ["salarios"], "", [], ["nomeEntidade"=>-1])."</script>"; 
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
          <div class="col-md-4 col-lg-4 lead"><br>
            <button class="btn btn-success" id="novoProcessamento"><i class="fa fa-plus-circle"></i> Novo Pagamento</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../../relatoriosPdf/folhaDeSalario.php?anoCivil=<?php echo $anoCivil ?>&mesPagamento=<?php echo $mesPagamento; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Folha de Salário</a>
          </div>
        </div>
      <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                  <tr>
                     <th class="lead font-weight-bolder text-center"><strong>N.º</strong></th>
                    <th class="lead font-weight-bolder"><strong>Funcionário</strong></th>
                    <th class="lead font-weight-bolder text-center"><strong>Data</strong></th>
                    <th class="lead font-weight-bolder text-center"><strong>Salário Liquido</strong></th>
                    <th class="lead font-weight-bolder text-center"><strong>Processado pelo</strong></th>
                    <th class="lead font-weight-bolder text-center"><strong><i class="fa fa-print"></i></strong></th>
                    <th></th>
                  </tr>
              </thead>
              <tbody id="tabHistorico">
              </tbody>
          </table>
      </div>
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


 <div class="modal fade" id="formularioProcessamentoSalario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioProcessamentoSalarioForm" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-money-check"></i> Pagamento de Salários</h4>
              </div>
              <div class="modal-body">

                <div class="row">
                  <div class="col-lg-6 col-md-6">
                    <label>Funcionário</label>
                    <select id="funcionario" required class="form-control" name="funcionario">
                      
                    </select>
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <label>Salário (Base)</label>
                    <input type="text" style="background-color:white; font-weight:bolder;" readonly id="salarioBase" class="form-control text-center">
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <label>Pagamento/Tempo</label>
                    <input type="text" style="background-color:white; font-weight:bolder;" readonly id="pagamentoPorTempo" class="form-control text-center">
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-md-3">
                    <label>Carga Horária</label>
                    <input type="number" readonly min="0" required id="cargaHoraria" name="cargaHoraria" class="form-control text-center">
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <label>Leccionados</label>
                    <input type="number" readonly min="0" required id="tempoTotLeccionado" name="tempoTotLeccionado" class="form-control text-center">
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <label>Não Leccionados</label>
                    <input type="number" readonly min="0" required id="tempoTotNaoLeccionado" name="tempoTotNaoLeccionado" class="form-control text-center">
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <label>Subsídios</label>
                    <input type="number" min="0" step="0.01" required id="totalSubsidios" name="totalSubsidios" class="form-control text-center">
                  </div>  
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4">
                    <label>IRT</label>
                    <input type="number" min="0" step="0.01" id="IRT" name="IRT" class="form-control text-center">
                  </div>
                  <div class="col-lg-4 col-md-4">
                    <label>Segurança Social</label>
                    <input type="number" min="0" step="0.01" id="segurancaSocial" name="segurancaSocial" class="form-control text-center">
                  </div>
                  <div class="col-lg-4 col-md-4">
                    <label>Outros Descontos</label>
                    <input type="number" min="0" step="0.01" id="outrosDescontos" name="outrosDescontos" class="form-control text-center">
                  </div>
                </div>
                <div class="row">

                  <div class="col-lg-4 col-md-4">
                    <label>Salário Liquido</label>
                    <input type="text" readonly style="font-size:15pt; font-weight: bolder;" min="0" value="" step="0.01" id="salariorLiquido" class="form-control text-center text-primary">
                  </div>
                  <div class="col-md-5 col-lg-5">
                    <label>Conta Debitar</label>
                    <select id="contaUsar" name="contaUsar" class="form-control lead" required>
                      <?php 
                        foreach($manipulacaoDados->selectArray("contas_bancarias", ["idPContaFinanceira", "descricaoConta"], ["idContaEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                          echo "<option value='".$a["idPContaFinanceira"]."'>".$a["descricaoConta"]."</option>";
                        }
                       ?>
                    </select>
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <label>Data do Pag.</label>
                    <input type="date" value="<?php echo $manipulacaoDados->dataSistema; ?>" id="dataDePagamento" name="dataDePagamento" class="form-control text-center">
                  </div>
                </div>

                <input type="hidden" id="mesPagamento" name="mesPagamento" value="<?php echo $mesPagamento; ?>">
                <input type="hidden" id="anoCivil" name="anoCivil" value="<?php echo $anoCivil; ?>">
                <input type="hidden" id="action" name="action" value="processarSalario">
                <input type="hidden" id="idPFuncionario" name="idPFuncionario" value="">
                <input type="hidden" id="idPSalario" name="idPSalario">
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



    <div class="modal fade" id="formularioAnularFactura" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioAnularFacturaForm" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-money-check"></i> Motivo de Cancelamento</h4>
              </div>
              <div class="modal-body">

                <div class="row">
                  <div class="col-lg-12 col-md-12">
                    <textarea id="motivoCancelamento" required class="form-control" name="motivoCancelamento"></textarea>
                  </div>
                </div>
                <input type="hidden" id="mesPagamento" name="mesPagamento" value="<?php echo $mesPagamento; ?>">
                <input type="hidden" id="anoCivil" name="anoCivil" value="<?php echo $anoCivil; ?>">
                <input type="hidden" id="action" name="action" value="excluirProcessamento">
                <input type="hidden" id="idPFuncionario" name="idPFuncionario" value="">
                <input type="hidden" id="idPSalario" name="idPSalario">
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
