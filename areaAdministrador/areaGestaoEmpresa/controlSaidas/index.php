<?php session_start();

     include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Control de Saídas", "controlSaidas");
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
    #fotos img{
      max-width: 100%;
      max-height: 200px;
      height: 200px;
      border-radius: 10px;
      width: 100%;
    }
    #fotos .divFoto{
      height: 350px;
      max-height: 350px;
      
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
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-money"></i> Control de Saídas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "controlSaidas", array(), "msg")){

            $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
            echo "<script>var mesPagamento='".$mesPagamento."'</script>";

            $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
            echo "<script>var anoCivil='".$anoCivil."'</script>";

            echo "<script>var listaSaidas = ".$manipulacaoDados->selectJson("saidas_luzl", [], ["dataSaida"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-")])."</script>";
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
               <label class="lead">Total (Kz): <span id="totValores" class="quantidadeTotal">0</span></label>
              </div>
            </div>

          <div class="row">
            <div class="col-md-12 col-lg-12">
              <button type="button" class="btn btn-success" id="novaSaida"><i class="fa fa-plus-circle"></i> Nova Saída</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <a href="../../relatoriosPdf/relatorioSaidas.php?mesPagamento=<?php echo $mesPagamento; ?>&anoCivil=<?php echo $anoCivil; ?>" class="btn btn-primary"><i class="fa fa-print"></i> Relatório de Saídas</a>
            </div>
          </div>        
            <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead font-weight-bolder"><strong>Data</strong></th>
                      <th class="lead font-weight-bolder"><strong>Funcionário</strong></th>
                      <th class="lead "><strong>Descrição</strong></th>  
                      <th class="lead"><strong>Valor</strong></th>
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

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioSaida" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioNovaSaidaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-money"></i> Saídas</h4>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    Descricção:
                    <input type="text" class="form-control vazio lead" name="descricaoSaida" id="descricaoSaida" required>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 col-lg-4 lead">
                    Valor
                    <input type="number" required min="0" class="form-control text-center lead vazio" id="valor" name="valor">
                  </div>
                  <div class="col-lg-8 col-md-8 lead">
                    Factura
                    <input type="file" class="form-control" id="factura" name="factura">
                  </div>
                </div>
                <input type="hidden" name="anoCivil" id="anoCivil" value="<?php echo $anoCivil; ?>">
                <input type="hidden" name="mesPagamento" id="mesPagamento" value="<?php echo $mesPagamento; ?>">
                <input type="hidden" name="idPSaida" id="idPSaida">
                <input type="hidden" name="action" id="action">
              </div>
              
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Concluir</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
