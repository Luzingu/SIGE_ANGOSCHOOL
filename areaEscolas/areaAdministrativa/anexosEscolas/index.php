<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Anexo das Escolas", "anexosEscolas");
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
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();    
    $usuariosPermitidos = ["aAdministrativa"];
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> Anexos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["anexosEscolas"],array(), "msg")){


          echo "<script>var anexos = ".json_encode($manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]))."</script>";
           ?>
          
          <div class="card">

              <div class="card-body">
                <div class="row">
                   <div class="col-lg-12 col-md-12">
                      <button type="button" name="" class="btn lead btn-primary novoRegistroFormulario" id="novoAnexo"><i class="fa fa-plus"></i> Adicionar</button>&nbsp;&nbsp;
                      <label class="lead">Total: <span id="numTAnexos" class="quantidadeTotal"></span></label>
                    </div>
                </div>
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><strong>ORDEM</strong></th>
                          <th class="lead"><strong>Identificação do Anexo</strong></th>
                          <th class="lead text-center"></th>
                      </tr>

                    </thead>
                    <tbody id="tabela">
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



<div class="modal fade" id="formularioAnexos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioAnexosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-school"></i> Anexos</h4>
              </div>

              <div class="modal-body">

                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      Ordem
                      <input type="number" class="form-control text-center fa-border vazio" id="ordenacaoAnexo" required name="ordenacaoAnexo">
                    </div>
                    <div class="col-lg-9 col-md-9 lead">
                      Identificação do Anexo
                      <input type="text" class="form-control fa-border vazio" id="identidadeAnexo" required maxlength="60" name="identidadeAnexo">
                    </div>
                  </div>
                  <input type="hidden" name="idPAnexo" id="idPAnexo" idChave="sim">
                  <input type="hidden" name="action" id="action">
              </div>


              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-primary lead submitter" id="Cadastar"><i class="fa fa-user-plus"></i> Cadastrar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
