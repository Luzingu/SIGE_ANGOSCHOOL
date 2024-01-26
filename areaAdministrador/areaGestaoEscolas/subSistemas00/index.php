<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Sub Sistemas", "subSistemas00");
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
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-reader"></i> SubSistemas de Ensino</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "subSistemas00", array(), "msg")){

         ?>
          <?php echo "<script>var subsistemasDeEnsino = ".$manipulacaoDados->selectJson("subsistemasDeEnsino", [], ["designacaoSubistema"=>array('$ne'=>null)], [], "", [], array("ordem"=>1))."</script>";
           ?>
    
            <div class="card">              
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-2 col-md-2">
                      <button type="button" name="" class="lead btn btn-primary novoRegistroFormulario" id="novoDado"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>

                    <div class="col-md-5 col-lg-5">
                       <label class="lead">Total: <span id="numTCursos" class="quantidadeTotal"></span></label>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                    <tr>
                      <th class="lead text-center"><strong>Categoria</strong></th>
                      <th class="lead"><strong>Ordem</strong></th>
                      <th class="lead"><strong>Designação</strong></th>
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

<div class="modal fade" id="formularioSubSistema" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioSubSistemaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-reader"></i> SubSistemas de Ensino</h4>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-lg-3 col-md-3 lead">
                    Categoria
                    <select class="form-control" name="categroria" id="categroria">
                      <option value="EG">Ensino Geral</option>
                    </select>
                  </div>
                  <div class="col-lg-2 col-md-2 lead">
                    Ordem
                    <input type="number" name="ordem" id="ordem" class="form-control text-center vazio">
                  </div>
                  <div class="col-lg-7 col-md-7 lead">
                    Designação
                    <input type="text" name="designacaoSubistema" id="designacaoSubistema" class="form-control vazio">
                  </div>
                </div>
                <input type="hidden" name="idPSubsistema" id="idPSubsistema" idChave="sim">
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
