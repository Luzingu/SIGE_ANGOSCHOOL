<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Contrato de Escolas Pós-Pago", "contratoEscolasPosPago00");
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
  <style type="">

       #formularioEscola .modal-dialog{
          width: 60%; 
          margin-left: -30%;
        }
      @media (max-width: 768px) {
            #formularioEscola .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Contratos (Pós-Pago)</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["contratoEscolasPosPago00"], array(), "msg")){

            echo "<script>var listaEscolas =".json_encode($manipulacaoDados->selectArray("escolas", [], ["contrato.tipoPagamento"=>"pos", "idEstadoEscola"=>['$nin'=>[4, 7]]], ["contrato"], "", [], array("nomeEscola"=>1)))."</script>";
         ?>
    
      
            <div class="card">              
              <div class="card-body"> 

                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                        <tr>
                            <th class="lead font-weight-bolder "><strong>Nome da Escola</strong></th>
                            <th class="lead text-center"><strong>Pacote</strong></th>

                            <th class="lead font-weight-bolder text-center"><strong>Vigência</strong></th>
                            <th class="lead font-weight-bolder"><strong>Inicio<br/>Prazo</strong></th>
                            <th class="lead font-weight-bolder text-center"><strong>Fim<br>Prazo</strong></th>
                            <th class="lead text-center"><strong>Saldo</strong></th>
                            <th class="lead"><strong>Contrato</strong></th>
                            <th class="lead text-center" style="min-width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tabEscola">
                    </tbody>
                </table>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>
