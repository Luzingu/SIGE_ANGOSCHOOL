<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Cargos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->designacaoArea="Layout e Acessos";
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
    $layouts->aside(0);
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Cargos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, "", array(), "msg")){

         echo "<script>var listaCargos = ".$manipulacaoDados->selectJson("cargos", [], [], [], "", [], array("designacaoCargo"=>1))."</script>";
           ?>

            <div class="card">
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-2 col-md-2">
                      <button type="button" id="btnNovoCargo" class="lead btn btn-primary"><i class="fa fa-plus-circle"></i> Novo Cargo</button>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                        <th class="lead"><strong>Designação</strong></th>
                        <th class="lead"><strong>Instituição</strong></th>
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



<div class="modal fade" id="formularioCargos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioCargosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-circle"></i> Cargos das Escolas</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-8 col-md-8 lead">
                      <label>Designação do Cargo</label>
                      <input type="text" class="form-control fa-border vazio" id="designacaoCargo"  required name="designacaoCargo" autocomplete="off">
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Instituição</label>
                      <select class="form-control" id="instituicao" name="instituicao">
                        <option value="escola">Escola</option>
                        <option value="DM">Direcção Munic.</option>
                        <option value="DP">Direcção Prov.</option>
                        <option value="administrador">Administrador</option>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="idPCargo" id="idPCargo">
                  <input type="hidden" name="action" id="action">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Concluir</button>
                    </div>
                  </div>
              </div>
            </div>
          </form>
      </div>
