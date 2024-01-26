<?php session_start();
    
    include_once $_SERVER["DOCUMENT_ROOT"].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Anos Lectivos", "anosLectivos00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Anos Lectivos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("", "anosLectivos00", array(), "msg")){


              echo "<script>var anolectivo =".$manipulacaoDados->selectJson("anolectivo", [], [], [], "", [], array("numAno"=>-1))."</script>";
           ?>

  <div class="card">              
    <div class="card-body">
        <div class="row">
             <div class="col-lg-2 col-md-2">
                <button type="button" name="" class="btn lead btn-primary" id="btnAdicionarAno"><i class="fa fa-plus-circle"></i> Novo Ano</button>
              </div>
          </div>
        <div class="row">
          <div class="col-lg-8 col-md-8"> 
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="lead text-center"><strong>ID</strong></th>
                        <th class="lead text-center"><strong>Ano Lectivo</strong></th>
                        <th class="lead font-weight-bolder"><strong>Estado</strong></th>                    
                        <th class="lead text-center" style="min-width: 130px;"></th>
                    </tr>
                </thead>
                <tbody id="tabela">
                    
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>


  <div class="modal fade" id="formularioAnoLectivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioAnoLectivoForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-reader"></i> Anos Lectivos</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
                      Designação:
                      <input type="text" name="numAno" class="form-control fa-border somenteLetras vazio lead" id="numAno" autocomplete="off" required>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                      Estado:
                      <select class="form-control lead" id="estado" name="estado">
                        <option value="V">Activo</option>
                        <option value="F">Inactivo</option>
                      </select>
                    </div>
                  </div>
                    
                  <input type="hidden" name="idPAno" id="idPAno" idChave="sim">
                  <input type="hidden" name="action" id="action">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>