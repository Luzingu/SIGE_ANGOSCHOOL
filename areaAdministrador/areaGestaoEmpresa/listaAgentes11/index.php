<?php session_start();
     include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Lista dos Agentes", "listaAgentes11");
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

       #formularioEntidade .modal-dialog{
          width: 70%;
          margin-left: -35%;
        }
      @media (max-width: 768px) {
            #formularioEntidade .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
      }
      fieldset{
        border:solid rgba(0, 0, 0, 0.6) 2px;
        border-radius: 10px;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 10px;
      }
      fieldset legend{
        width: 150px;
        font-weight: bolder;
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Agentes</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "listaAgentes11", array(), "msg")){

            echo "<script>var listaEntidades = ".json_encode($manipulacaoDados->entidades(array()))."</script>";
         ?>

      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 col-lg-12">
              <button class="btn btn-success btn-lg" id="novoAgente"><i class="fa fa-plus-circle"></i> Novo Agente</button>&nbsp;&nbsp;&nbsp;
              <label class="lead">Total de Agentes: <span id="numTProfessores" class="quantidadeTotal"></span></label>
            </div>
          </div>
          <?php $includarHtmls->indexAgente(); ?>
        </div>
      </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs();  $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();  ?>
