<?php session_start();       
  if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Editar Dados da Instituição");
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
    $layouts->headerUsuario();
    $layouts->areaGestaoGPE();
    $usuariosPermitidos[] = "aGestGPE";

    $codigoTurma = $manipulacaoDados->selectUmElemento('ano_escola', 'codigoTurma', 'idAnoEscola=:idAnoEscola AND idFAno=:idFAno', [$_SESSION["idEscolaLogada"], $manipulacaoDados->idAnoActual]);

    $dadosEscola = $manipulacaoDados->selectArray("escolas LEFT JOIN div_terit_provincias ON idPProvincia=provincia LEFT JOIN div_terit_municipios ON idPMunicipio=municipio", "*", "idPEscola=:idPEscola", [$_SESSION["idEscolaLogada"]]); 

    echo "<script>var dadosescola=".json_encode($dadosEscola)."</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
           <?php if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){ ?>
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
                <div class="col-lg-6 col-sm-6 follow-info">
                  <p class="text-justify citacaoUsuarioCorente" id="citacaoProfessor"></p>
                  <h6 class="outrasInformacoes" style="width:100%;">
                      <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone valor" id="thTelEscola"></strong></span><br/><br/>
                      <span class="lead"><strong class="numeroTelefone valor" id="thEmail"></strong></span>
                  </h6>
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
                  <?php if($verificacaoAcesso->verificarAcessoAlteracao(["aDirectoria"], "", "", "")){ ?>
                  <li class=""> 
                    <a data-toggle="tab" href="#edit-profile" class="lead">
                                          <i class="fa fa-user-edit"></i>
                                          Editar Perfil
                                      </a>
                  </li>
                  <?php } ?>
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
                        <h1 class="lead informPerfil"><b>Informações da Escola</b></h1>
                        <form class="form-horizontal" role="form" enctype="multipart-data" id="formularioPerfil">

                          <div class="form-group">
                           <div class="col-lg-6 col-md-6">
                              <label class="lead control-label">Nome da Escola:</label>
                              <input type="text" class="form-control lead" id="nomeEscola" name="nomeEscola" maxlength="80" readonly disabled>
                            </div>
                            <div class="col-lg-4 col-md-4">
                              <label class="lead control-label">Decreto da Criação:</label>
                              <input type="text" class="form-control lead" id="decretoCriacaoInstituicao" name="decretoCriacaoInstituicao" maxlength="80" >
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="control-label lead">Organismo:</label>
                              <input type="number" class="form-control lead text-center" id="codOrganismo" name="codOrganismo">
                            </div>
                          </div>

                          <div class="form-group">
                            
                            <div class="col-lg-4 col-md-4">
                              <label class="control-label lead">N.º de Telefone:</label>
                              <input type="number" class="form-control lead" id="numeroTelefone" name="numeroTelefone">
                            </div>
                            <div class="col-lg-6 col-md-6">
                              <label class="control-label lead">E-mail:</label>
                              <input type="email" class="form-control lead" id="valEmail" name="valEmail" maxlength="100">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Cabeç. das Tabs.</label>
                              <input type="color" title="Cor do cabeçalho" class="lead text-center" id="corCabecalhoTabelas" name="corCabecalhoTabelas">
                              <input type="color" title="Cor das letras" class="lead text-center" id="corLetrasCabecalhoTabelas" name="corLetrasCabecalhoTabelas">
                            </div>
                          </div>


                          <div class="form-group">
                            <div class="col-lg-3 col-md-3">
                                <label class="control-label lead">Logotipo:</label>
                               <input type="file" class="form-control lead" accept=".png, .jpg, .jpeg" id="logoEscola" name="logoEscola">
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label class="control-label lead">Assinatura do D. G.:</label>
                               <input type="file" class="form-control lead" accept=".png, .jpg, .jpeg" id="assinatura1" name="assinatura1">
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label class="control-label lead">Assinatura do D. P.:</label>
                              <input type="file" class="form-control lead" accept=".png, .jpg, .jpeg" id="assinatura2" name="assinatura2">
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label class="control-label lead">Assinatura do D.A:</label>
                              <input type="file" class="form-control lead" accept=".png, .jpg, .jpeg" id="assinatura3" name="assinatura3">
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-lg-12 control-label lead" style="text-align: left!important;">Sobre a Instituição:</label>
                            <div class="col-lg-12">
                              <textarea class="form-control lead" id="acercaEscola" name="acercaEscola" cols="30" rows="5"><?php echo valorArray($dadosEscola, "acercaEscola"); ?></textarea>
                            </div>
                          </div>                         

                          <input type="hidden" name="action" value="actualizarDefinicoesConta">

                          <div class="form-group">
                            <div class="col-lg-12">
                              <div class="col-lg-3 col-md-3"> 
                                  <button  type="submit" class="btn-success btn lead"><i class="fa fa-check"></i> Actualizar</button>                                    
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
