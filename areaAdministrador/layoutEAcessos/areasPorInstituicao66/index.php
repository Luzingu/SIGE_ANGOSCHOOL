<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Áreas por Instituição");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-pen-alt"></i> Áreas Por Instituição</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, "", array(), "msg")){

           $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola", ["nomeEscola"=>array('$ne'=>null)]);
           $tipoInstituicao = $manipulacaoDados->selectUmElemento("escolas","tipoInstituicao", ["idPEscola"=>$idPEscola]);
           if($idPEscola==4){
            $manipulacaoDados->conDb("teste", true);
           }
          echo "<script>var idPEscola='".$idPEscola."'</script>";

          echo "<script>var listaCargos = ".$manipulacaoDados->selectJson("cargos", [], ["instituicao"=>$tipoInstituicao, "idPCargo"=>array('$nin'=>[1,2,3,4])], [], "", [], array("designacaoCargo"=>1))."</script>";

          echo "<script>var listaAreas = ".$manipulacaoDados->selectJson("areas", [], ["instituicao"=>$tipoInstituicao], [], "", [], array("designacaoArea"=>1))."</script>";;

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
                  <div class="col-lg-7 col-md-7 lead"><br>
                    <button class="btn btn-success btn-lg" id="btnAlterar"><i class="fa fa-check-circle"></i> Alterar</button>
                  </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>N.º</strong></th>
                        <th class="lead"><strong>Nome</strong></th>
                        <th class="lead"><strong>Acessos</strong></th>
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
