<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Sub Menus - Layout e Acessos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->designacaoArea ="Layout e Acessos";
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
    $idPMenu = isset($_GET["idPMenu"])?$_GET["idPMenu"]:"";
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-table"></i> <?php echo $manipulacaoDados->selectUmElemento("menus", "designacaoMenu", ["idPMenu"=>$idPMenu]); ?></strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, array(), array(), "msg")){

          echo "<script>var subMenusEscolas = ".$manipulacaoDados->selectJson("menus", [], ["idPMenu"=>$idPMenu], ["subMenus"])."</script>";
           ?>

            <div class="card">
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-2 col-md-2">
                      <button type="button" id="btnNovoMenu" class="lead btn btn-primary"><i class="fa fa-plus-circle"></i> Novo Sub Menu</button>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                        <th class="lead"><strong>Designação</strong></th>
                        <th class="lead"><strong>Identificador</strong></th>
                        <th class="lead"><strong>Link</strong></th>
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



<div class="modal fade" id="formularioSubMenus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioSubMenusForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-table"></i> Sub Menus das Escolas</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-8 col-md-8 lead">
                      <label>Designação do Sub-Menu</label>
                      <input type="text" class="form-control fa-border vazio" id="designacaoSubMenu"  required name="designacaoSubMenu" autocomplete="off">
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Identificador</label>
                      <input type="text" class="form-control fa-border vazio" id="identificadorSubMenu"  required name="identificadorSubMenu" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 lead">
                      <label>Link</label>
                      <input type="text" class="form-control fa-border vazio" id="linkSubMenu"  required name="linkSubMenu" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Somente Online?</label>
                      <select class="form-control" name="somenteOnline" id="somenteOnline">
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="idPMenu" id="idPMenu" value="<?php echo $idPMenu; ?>">
                  <input type="hidden" name="idPSubMenu" id="idPSubMenu">
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
