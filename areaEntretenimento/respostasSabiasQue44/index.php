<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../");
    $manipulacaoDados = new manipulacaoDados("Respostas - Sabias que");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea=8;
    $layouts->designacaoArea="Entrenenimento";
    $manipulacaoDados->conDb("entretenimento", true);    
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #tabela tr td{
      font-size: 11pt;
    }
    #tabela tr td img{
      width: 100px;
      height: 100px;
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-question"></i> Respostas - Sabias que...</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
            <?php 
              echo "<script>var sabiasQue =".$manipulacaoDados->selectJson("sabias_que", [], [], [], "", [], ["idPSabiasQue"=>1])."</script>";
            ?>
          <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-4 col-md-4"><br>
                    <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novaCategoria"><i class="fa fa-plus-circle"></i> Adicionar</button>
                  </div>
              </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><strong>N.º</strong></th>
                          <th class="lead font-weight-bolder"><strong>Resposta</strong></th>
                          <th class="lead"><strong>Imagem</strong></th>
                          <th class="lead text-center" style="min-width: 130px;"></th>
                      </tr>
                  </thead>
                  <tbody id="tabela">
                      
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
        <?php echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->formTrocarSenha(); ?>

  <div class="modal fade" id="formularioSabiasQue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioSabiasQueForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-question"></i> </h4>
              </div>

              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 lead">
                      <label>Sabias que:</label>
                      <textarea type="text" class="form-control vazio lead" id="resposta" name="resposta" required style="max-width:100%; max-height:140px;" autocomplete="off"></textarea>
                    </div> 
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    <label>Arquivo</label>
                    <input type="file" class="form-control vazio" id="arquivo" name="arquivo">
                  </div>
                </div>
                <input type="hidden" name="action" id="action">
                <input type="hidden" name="idPSabiasQue" id="idPSabiasQue">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Concluir</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>