<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
     include_once $_SESSION["directorioPaterno"].'angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "CPainel - Adicionar Agentes");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Gestão do GPE";
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->headerUsuario();
    $layouts->areaGestaoGPE();
    $usuariosPermitidos[] = "aGestGPE";
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-plus"></i> Adicionar Agentes</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){

          $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC");

          if(count($manipulacaoDados->selectArray("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola AND idPEscola=:idPEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", $idPEscola], "nomeEscola ASC"))<=0){

            $idPEscola = $manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC");
          }
          echo "<script>var idPEscola='".$idPEscola."'</script>";

          ?>
          <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
            <label>Escola</label>
            <select class="form-control lead" id="idPEscola">
              <?php foreach($manipulacaoDados->selectArray("escolas", "*", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC") as $a){
                echo "<option value='".$a->idPEscola."'>".$a->nomeEscola."</option>";
              } ?>
            </select>
          </div>
          </div>
          <?php

             $includarHtmls->adicionarAgentes($idPEscola);
             
         } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>

