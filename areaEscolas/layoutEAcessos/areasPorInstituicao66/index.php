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
    $layouts->designacaoArea="Layout e Acesos";
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-table"></i> Áreas Por Instituição</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, "", array(), "msg")){

          echo "<script>var idPEscola='".$_SESSION['idEscolaLogada']."'</script>";

          echo "<script>var listaCargos = ".$manipulacaoDados->selectJson("cargos", [], ["instituicao"=>valorArray($manipulacaoDados->sobreEscolaLogada, "tipoInstituicao"), "idPCargo"=>array('$nin'=>[1,2,3,4])], [], "", [], array("designacaoCargo"=>1))."</script>";

          echo "<script>var listaAreas = ".$manipulacaoDados->selectJson("areas", [], ["instituicao"=>valorArray($manipulacaoDados->sobreEscolaLogada, "tipoInstituicao"), "idPArea"=>['$nin'=>[13, 14]] ], [], "", [], array("designacaoArea"=>1))."</script>";;

           ?>

            <div class="card">
              <div class="card-body">
                <ol style="font-size: 13pt;">
                <?php
                  foreach($manipulacaoDados->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "escola.nivelSistemaEntidade"],["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.nivelSistemaEntidade"=>array('$nin'=>array(1, 2, 4, 3))], ["escola"]) as $a){
                    echo "<li><strong>".$manipulacaoDados->selectUmElemento("cargos", "designacaoCargo", ["idPCargo"=>$a["escola"]["nivelSistemaEntidade"]])."</strong> - ".$a["nomeEntidade"]."</li>";
                  }
                ?>
                </ol>

                <div class="row">
                  <div class="col-lg-12 col-md-12 lead text-right"><br>
                    <button class="btn btn-success btn-lg" id="btnAlterar"><i class="fa fa-check-circle"></i> Alterar</button>
                  </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>Área</strong></th>
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
