<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Sub - Categorias");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Sub Categorias</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
            <?php 
              $idPCategoria = isset($_GET['idPCategoria'])?$_GET['idPCategoria']:$manipulacaoDados->selectUmElemento("categoriaLivros", "idPCategoria", [], [], [], ["idPCategoria"=>1]);

              echo "<script>var idPCategoria='".$idPCategoria."'</script>";
              echo "<script>var listaSubCategorias =".$manipulacaoDados->selectJson("categoriaLivros", [], ["idPCategoria"=>$idPCategoria], ["subCategoria"], "", [], ["subCategoria.nomeSubCategoria"=>1])."</script>";


             ?>
          <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-4 col-md-4 lead">
                    <label>Categoria</label>
                    <select class="form-control lead" id="idPCategoria">
                      <?php 
                      foreach($manipulacaoDados->selectArray("categoriaLivros", [], [], [], "", [], ["nomeCategoria"=>1]) as $a){
                        echo "<option value='".$a["idPCategoria"]."'>".$a["nomeCategoria"]."</option>";
                      }

                       ?>
                    </select>
                  </div>
                  <div class="col-lg-4 col-md-4"><br>
                    <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novaCategoria"><i class="fa fa-plus-circle"></i> Adicionar</button>
                  </div>
              </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><strong>N.º</strong></th>
                          <th class="lead font-weight-bolder"><strong>Categoria</strong></th>
                          <th class="lead font-weight-bolder"><strong>Sub Categoria</strong></th>
                          <th class="lead"><strong>Autor</strong></th>
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

  <div class="modal fade" id="formularioSubCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioSubCategoriaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-open"></i> Sub Categoria</h4>
              </div>

              <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 lead">
                      Sub Categoria:
                      <input type="text" class="form-control lead vazio" id="subCategoriaLivro" name="subCategoriaLivro" autocomplete="off">
                    </div> 
                </div>                
                <input type="hidden" name="action" id="action">
                <input type="hidden" name="idPSubCategoria" id="idPSubCategoria">
                <input type="hidden" name="idPCategoria" id="idPCategoria" value="<?php echo $idPCategoria; ?>">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-user-plus"></i> Cadastrar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>