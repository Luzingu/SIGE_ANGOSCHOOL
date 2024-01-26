<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Cursos");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-table"></i> Menus para Todas Escolas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, array(), array(), "msg")){

          echo "<script>var listaMenus = ".$manipulacaoDados->selectJson("menus", [], ["instituicao"=>"escola"], [], 15, [], array("idPMenu"=>-1))."</script>";

          $areas = $manipulacaoDados->selectArray("areas", [], [], [], "", [], array("ordemPorDefeito"=>1));
          echo "<script>var listaAreas=".json_encode($areas)."</script>";
           ?>

            <div class="card">
              <div class="card-body">

                <div class="row">
                  <div class="col-md-9 col-lg-9 text-right">
                    <button class="btn btn-success btn-lg" id="btnAlterar"><i class="fa fa-check-circle"></i> Alterar</button>
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <input type="search" class="form-control" id="pesqMenu" placeholder="Pesquisar...">
                  </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>ORDEM</strong></th>
                        <th class="lead"><strong>Nome</strong></th>
                        <th class="lead"><strong>Área por Defeito</strong></th>
                        <th class="lead"><strong>Área</strong></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table><br>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
<form id="formularioDados">
    <input type="hidden" name="action" id="action" value="luzinguLuame">
    <input type="hidden" name="dadosEnviar" id="dadosEnviar">
    <input type="hidden" name="idPEscola" id="idPEscola" value="<?php echo $idPEscola; ?>">
 </form>
