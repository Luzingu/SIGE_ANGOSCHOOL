<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Menus");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-table"></i> Menus por Instituição</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, array(), array(), "msg")){

          $idPArea = isset($_GET["idPArea"])?$_GET["idPArea"]:"0";
          echo "<script>var idPArea='".$idPArea."'</script>";

           $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola", ["nomeEscola"=>array('$ne'=>null)]);
           $tipoInstituicao = $manipulacaoDados->selectUmElemento("escolas","tipoInstituicao", ["idPEscola"=>$idPEscola]);
           if($idPEscola==4){
            $manipulacaoDados->conDb("teste", true);
           }
          echo "<script>var idPEscola='".$idPEscola."'</script>";

          echo "<script>var listaMenus = ".$manipulacaoDados->selectJson("menus", [], ["instituicao"=>$tipoInstituicao], [], "", [], array("designacaoMenu"=>1))."</script>";

          $areas = $manipulacaoDados->selectArray("areas", [], ["instituicoes.idEscola"=>$idPEscola, "idPArea"=>array('$nin'=>[7,8])], ["instituicoes"], "", [], array("ordemPorDefeito"=>1));
          echo "<script>var listaAreas=".json_encode($areas)."</script>";
           ?>

            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-3 col-md-3 lead">
                    <label>Instituição</label>
                    <select class="form-control lead" id="idPEscola">
                      <?php
                      $manipulacaoDados->conDb("escola", true);
                      foreach($manipulacaoDados->selectArray("escolas", ["idPEscola", "abrevNomeEscola2"], ["nomeEscola"=>array('$ne'=>null)],[], "", [], array("nomeEscola"=>1)) as $a){
                        echo "<option value='".$a["idPEscola"]."'>".$a["abrevNomeEscola2"]."</option>";
                      } ?>
                    </select>
                  </div>
                  <div class="col-lg-3 col-md-3 lead">
                    <label>Áreas</label>
                    <select class="form-control lead" id="idPArea">
                      <option value=''>Sem Área</option>
                      <option value='0'>Todas Áreas</option>
                      <?php
                      foreach($areas as $a){
                        echo "<option value='".$a["idPArea"]."'>".$a["designacaoArea"]."</option>";
                      } ?>
                    </select>
                  </div>
                  <div class="col-lg-4 col-md-4 lead"><br>
                    <button class="btn btn-success btn-lg" id="btnAlterar"><i class="fa fa-check-circle"></i> Alterar</button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-9 col-lg-9 visible-md visible-lg">
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
