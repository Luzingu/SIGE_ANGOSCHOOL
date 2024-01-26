<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Definições de Empresa", "sobreAEmpresa11");
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
  <style type="text/css">
    #citacaoProfessor{
      color: white;
      font-style: italic;
      color: white;
      font-weight: 800;
      color: red;
    }
    .informPerfil{
      font-weight: 1000;
    }
    #thNumeroInternoEscola{
      color: white;
      font-weight: 700;
      font-size: 18px;
    }
    .nomeEscola{
      font-weight: 800;
      color:white;
    }
    #logotipoEscola{
      width: 130px;
      height: 130px;
      max-height: 130px;
      max-width: 130px;
      min-width: 130px;
      min-height: 130px;
    }
    .border div{
      border: solid white 1px;
      height: 120px;
      padding:0px;
      padding: 0px;
      margin: 0px;
      padding-top: 50px;
      color: white;
      font-weight: bolder;
      font-size: 19px;
    }
    .outrasInformacoes{
      color: white;
    }
    div.valor{
      font-weight: 800;
    }
    div.valor, .control-label, .lab{
      color: black !important;

    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
   $layouts->cabecalho();
    $layouts->aside();

    $codigoTurma = "";

    $dadosEscola = $manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"]]);

    echo "<script>var dadosescola=".json_encode($dadosEscola)."</script>";
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="main-body">
           <?php if($verificacaoAcesso->verificarAcesso("", ["sobreAEmpresa11"], array(), "msg")){ ?>
          <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-lg-3 col-sm-3 text-center">
                  <h4 class="nomeEscola text-center valor" id="thNomeEscola"></h4>
                  <div class="follow-ava text-center">
                    <img src="" class="medio" id="logotipoEscola">
                  </div>
                   <h6 class="text-center valor" id="thNumeroInternoEscola"></h6>
                </div>
                <div class="col-lg-3 col-sm-3 follow-info">
                  <p class="text-justify citacaoUsuarioCorente" id="citacaoProfessor"></p>
                  <h6 class="outrasInformacoes">
                      <span class="lead"><i class="fa fa-map-marker-alt"></i> <strong id="thLocalizacaoEscola" class="valor"></strong></span> <br/><br/>
                      <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone valor" id="thTelEscola"></strong></span><br/><br/>
                      <span class="lead"><strong class="numeroTelefone valor" id="thEmail"></strong></span>
                  </h6>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
                  <div class="text-center">
                    <strong class="text-center valor" id="thNivelEscola">--</strong>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
                  <div class="text-center" >
                    <strong class="text-center valor" id="thNumeroEscola">--</strong>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <section class="panel">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead">
                        <i class="fa fa-user"></i>
                       Perfil
                    </a>
                  </li>

                  <li class="">
                    <a data-toggle="tab" href="#edit-profile" class="lead">
                        <i class="fa fa-user-edit"></i>
                        Editar Perfil
                    </a>
                  </li>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content">
                  <!-- profile -->
                  <div id="profile" class="tab-pane active">
                    <section class="panel">
                      <div class="bio-graph-heading lead col-sm-12 col-xs-12 col-lg-12 col-md-12" id="thAcercaEscola" style="margin-bottom: 30px;">Bem vindo ao <?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "nomeEscola"); ?></div>
                      <div class="panel-body bio-graph-info">
                          <div class="row">
                            <div class="col-lg-2 col-md-2 lead text-right lab">Nome da Escola:</div>
                            <div class="lead col-lg-3 col-md-3 valor" id="textNomeEscola"></div>
                          </div>
                          <div class="row">
                            <div class="col-lg-2 col-md-2 lead text-right lab">Nº:</div>
                            <div class="lead col-lg-3 col-md-3 valor" id="textNúmeroEscola"></div>
                          </div>
                          <div class="row">
                            <div class="col-lg-2 col-md-2 lead text-right lab">Número Interno:</div>
                            <div class="lead col-lg-3 col-md-3 valor" id="textNúmeroInterno"></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-2 col-md-2 lead text-right lab">Regime:</div>
                            <div class="lead col-lg-3 col-md-3 valor" id="textPrivacidade"></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-2 col-md-2 lead text-right lab">Ano da Fundação:</div>
                            <div class="lead col-lg-3 col-md-3 valor" id="textAnoFundado"></div>

                          </div>
                      </div>
                    </section>
                    <section>
                      <div class="row">
                      </div>
                    </section>
                  </div>
                  <!-- edit-profile -->
                  <div id="edit-profile" class="tab-pane">
                    <section class="panel">
                      <div class="panel-body bio-graph-info">
                        <h1 class="lead informPerfil"><strong>Informações da Empresa</strong></h1>
                        <form class="form-horizontal" role="form" enctype="multipart-data" id="formularioPerfil">

                          <div class="form-group">
                            <label class="col-lg-4 control-label lead">Nome da Empresa:</label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control lead" id="nomeEscola" name="nomeEscola" maxlength="80" readonly disabled>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-lg-4 control-label lead">Comuna/Distrito:</label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control lead" id="comunaEscola" name="comunaEscola" maxlength="80">
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-lg-4 control-label lead">E-mail:</label>
                            <div class="col-lg-6">
                              <input type="email" class="form-control lead" id="valEmail" name="valEmail" maxlength="100">
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-lg-4 control-label lead">Telefone:</label>
                            <div class="col-lg-6">
                              <input type="text" class="form-control lead" id="numeroTelefone" name="numeroTelefone" maxlength="100">
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-lg-4 control-label lead">Logotipo:</label>
                            <div class="col-lg-6">
                               <input type="file" class="form-control lead" accept=".png, .jpg, .jpeg" id="logoEscola" name="logoEscola">
                            </div>
                          </div>

                          <input type="hidden" name="action" value="actualizarDefinicoesConta">

                          <div class="form-group">
                            <div class="col-lg-12">
                              <div class="col-lg-3 col-md-3">
                                  <button  type="submit" class="btn-primary btn lead"><i class="fa fa-check"></i> Actualizar</button>
                                </div>
                            </div>
                          </div>
                        </form>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </section>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
        </div>
        </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
