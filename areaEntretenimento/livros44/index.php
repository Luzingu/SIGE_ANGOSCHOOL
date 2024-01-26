<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEntretenimento/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("");
    $manipulacaoDados = new manipulacaoDados("Categorias");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->conDb("entretenimento", true);
    $layouts->idPArea=8;
    $layouts->designacaoArea="Entrenenimento";    
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book"></i> Livros</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
            <?php 
              $idPCategoria = isset($_GET['idPCategoria'])?$_GET['idPCategoria']:$manipulacaoDados->selectUmElemento("categoriaLivros", "idPCategoria", [], [], [], ["idPCategoria"=>1]);

              echo "<script>var idPCategoria='".$idPCategoria."'</script>";

              echo "<script>var livraria =".$manipulacaoDados->selectJson("livraria", [], ["idCategoria"=>$idPCategoria], [], "", [], ["tituloLivro"=>1])."</script>";


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
                          <th class="lead font-weight-bolder"><strong>Título</strong></th>
                          <th class="lead font-weight-bolder"><strong>Autores</strong></th>
                          <th class="lead font-weight-bolder"><strong>Sub Categoria</strong></th>
                          <th class="lead font-weight-bolder"><strong>Publicador</strong></th>
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

  <div class="modal fade" id="formularioCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioCategoriaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book"></i> Livros</h4>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    Título
                    <input type="text" class="form-control vazio" id="tituloLivro" name="tituloLivro" required>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    Autor(s)
                    <input type="text" class="form-control vazio" id="autoresLivro" name="autoresLivro" autocomplete="on">
                  </div>
                </div>
                <div class="row">
                    <div class="col-lg-7 col-md-7 lead">
                      Arquivo
                      <input type="file" class="form-control vazio" id="arquivo" name="arquivo" required>
                    </div>
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
                      Sub Categoria:
                      <select type="text" class="form-control lead" id="idSubCategoria" name="idSubCategoria">
                        <option value="">Nenhum</option>
                        <?php 
                          foreach($manipulacaoDados->selectArray("categoriaLivros", [], ["idPCategoria"=>$idPCategoria], ["subCategoria"], "", [], ["subCategoria.nomeSubCategoria"=>1]) as $a){
                            echo "<option value='".$a["subCategoria"]["idPSubCategoria"]."'>".$a["subCategoria"]["nomeSubCategoria"]."</option>";
                          }
                         ?>
                      </select>
                    </div> 
                </div>                
                <input type="hidden" name="action" id="action">
                <input type="hidden" name="idPCategoria" id="idPCategoria" value="<?php echo $idPCategoria; ?>">
                <input type="hidden" name="idPLivro" id="idPLivro">
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