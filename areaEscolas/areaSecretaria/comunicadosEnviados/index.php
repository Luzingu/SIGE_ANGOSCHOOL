<?php session_start();
     include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Comunicados Enviados", "comunicadosEnviados");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-envelope"></i> Comunicados</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "comunicadosEnviados", array(), "msg")){
          $data = isset($_GET["data"])?$_GET["data"]:$manipulacaoDados->selectUmElemento("comunicados", "data", ["idPEscola"=>$_SESSION['idEscolaLogada']]);
          echo "<script>var data='".$data."'</script>";

          $comunicados = $manipulacaoDados->selectJson("comunicados", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "data"=>$data]);
          echo "<script>var listacomunicados=".$comunicados."</script>";
         ?>

          <div class="card"> 
            <div class="card-body">
              <div class="row">
                <div class="col-lg-3 col-md-3">
                  <label>Data</label>
                  <select class="form-control lead" id="data">
                    <?php foreach($manipulacaoDados->selectDistinct("comunicados", "data", ["idPEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                      echo "<option value='".$a["_id"]."'>".converterData($a["_id"])."</option>";
                    } ?>
                  </select>
                </div>
                <div class="col-lg-9 col-md-9"><br>
                  <h4 id="numeroMensagens" style="font-weight: bolder;"></h4>
                </div>
                
              </div>

              <table id="example1" class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                  <tr>
                    <th class="lead text-center"><strong>Nº</strong></th>
                    <th class="lead"><strong>Destinatário</strong></th>
                    <th class="lead text-center"><strong>Telefone</strong></th>
                    <th class="lead text-center"><strong>Autor</strong></th>
                    <th class="lead text-center"><strong>Hora</strong></th>
                    <th class="lead text-center"><strong>Mensagem</strong></th>
                  </tr>
                </thead>
                <tbody id="tabela">
                </tbody>
              </table><br><br>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>