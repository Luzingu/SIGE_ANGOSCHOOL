<?php session_start();
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }       
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Escolas");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Relatório e Estatística";
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="">

       #formularioEscola .modal-dialog{
          width: 60%; 
          margin-left: -30%;
        }
      @media (max-width: 768px) {
            #formularioEscola .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
      }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
   $layouts->headerUsuario();
    $layouts->areaEstatERelat();
    $usuariosPermitidos[] = "aRelEstatistica";
    $privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"Pública";
    if($privacidade!="Pública" && $privacidade!="Privada"){
      $privacidade="Pública";
    }
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> <?php 
                  if($privacidade=="Pública"){
                    echo "Escolas Públicas";
                  }else{
                    echo "Escolas Privadas";
                  }

                   ?></strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos)){

            echo "<script>var listaEscolas =".$manipulacaoDados->selectJson("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio LEFT JOIN div_terit_provincias ON idPProvincia=provincia", "*", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia AND idPEscola!=4 AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola", ["escola", valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), $privacidade, "A"], "nomeEscola ASC")."</script>";

         ?>
    
      
            <div class="card">              
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 col-lg-3">
                    <label class="lead">Categoria</label>
                    <select class="form-control lead" id="categoriaEscola">
                      <option value="">Todas</option>
                      <option value="primaria">Ensino Primário</option>
                      <option value="basica">I Ciclo</option>
                      <option value="media">II Ciclo</option>
                      <option value="primBasico">Complexo (Primária e I Ciclo)</option>
                      <option value="basicoMedio">Complexo (I e II Ciclo)</option>
                      <option value="complexo">Complexo</option>
                    </select>
                  </div>
                  <div class="col-md-8 col-lg-8"><br>
                    <label class="lead">Total: <span id="numTEscolas" class="quantidadeTotal"></span></label>&nbsp;&nbsp;&nbsp;
                    <a href="../../relatoriosPdf/listaEscolas.php?privacidade=<?php echo $privacidade; ?>" class="btn btn-primary lead"><i class="fa fa-print"></i> Lista</a>
                  </div>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                        <tr>
                            <th class="lead text-center">Nº</th>
                            <th class="lead font-weight-bolder "><strong>Nome da Escola</strong></th>
                            <th class="lead text-center"><strong>Nível</strong></th>
                            <th class="lead font-weight-bolder"><strong>Períodos</strong></th>
                            <th class="lead text-center"><strong>Municipio</strong></th>
                            <th class="lead text-center"><strong>Comuna</strong></th>
                        </tr>
                    </thead>
                    <tbody id="tabEscola">
                    </tbody>
                </table>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>