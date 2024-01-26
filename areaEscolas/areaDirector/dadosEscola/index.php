<?php session_start();   

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Editar Dados da Escola", "dadosEscola");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
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

    $array = $manipulacaoDados->selectArray("anolectivo", [], ["idPAno"=>$manipulacaoDados->idAnoActual, "anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada']], ["anos_lectivos"]);
    $codigoTurma = valorArray($array, "codigoTurma", "anos_lectivos");

    $dadosEscola = $manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION["idEscolaLogada"]]); 
    $dadosEscola = $manipulacaoDados->anexarTabela($dadosEscola, "div_terit_provincias", "idPProvincia", "provincia");
    $dadosEscola = $manipulacaoDados->anexarTabela($dadosEscola, "div_terit_provincias", "div_terit_municipios", "municipio");

    echo "<script>var dadosescola=".json_encode($dadosEscola)."</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
           <?php if($verificacaoAcesso->verificarAcesso("", ["dadosEscola"], array(), "msg")){ ?>
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
                              <label class="lead control-label">N.º de Salas</label>
                              <input type="number" class="form-control lead text-center" id="numeroSalas" name="numeroSalas" min="1">
                            </div>
                          </div>

                          <div class="form-group">
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Cód. de Turma:</label>
                              <input type="text" class="form-control lead text-center" id="codigoTurma" name="codigoTurma" value="<?php echo $codigoTurma; ?>">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="control-label lead">Organismo:</label>
                              <input type="number" class="form-control lead text-center" id="codOrganismo" name="codOrganismo">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="control-label lead">N.º de Telefone:</label>
                              <input type="number" class="form-control lead" id="numeroTelefone" name="numeroTelefone">
                            </div>
                            <div class="col-lg-4 col-md-4">
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
                            <div class="col-lg-12 col-md-12">
                              <label class="lead control-label">Dias dos Feriados</label>
                              <input type="text" name="diasDosFeriados" id="diasDosFeriados" class="form-control lead" placeholder="dd/mm/aaaa; dd/mm/aaa; dd/mm/aaa">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-lg-12 col-md-12">
                              <label class="lead control-label">Dias das Actividades</label>
                              <input type="text" name="diasDasActividades" id="diasDasActividades" class="form-control lead" placeholder="dd/mm/aaaa; dd/mm/aaa; dd/mm/aaa">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-lg-9 col-md-9">
                              <label class="lead control-label">Nome Comercial</label>
                              <input type="text" name="nomeComercial" id="nomeComercial" class="form-control lead">
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label class="lead control-label">NIF</label>
                              <input type="text" name="nifEscola" id="nifEscola" class="form-control lead">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-lg-3 col-md-3">
                              <label class="lead control-label">Série da Factura</label>
                              <select class="form-control" required id="serieFactura" name="serieFactura">
                                <option>MNG</option>
                                <option>MAN</option>
                              </select>
                            </div>
                            <div class="col-lg-5 col-md-5">
                              <label class="lead control-label">Endreço</label>
                              <input type="text" name="enderecoEscola" id="enderecoEscola" class="form-control lead">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Comprovativo</label>
                              <select name="comprovativo" id="comprovativo" class="form-control lead">
                                <option>A4</option>
                                <option>A5</option>
                                <option>A6</option>
                              </select>
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
                          <fieldset class="form-group" style="border:solid rgba(0, 0, 0, 0.3) 1px; padding:4px;">
                            <legend><strong>Cartão de Estudante</strong></legend>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Altura (px)</label>
                              <input type="number" class="form-control lead text-center" id="alturaCartEstudante" name="alturaCartEstudante">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Tamanho(px)</label>
                              <input type="number" class="form-control lead text-center" id="tamanhoCartEstudante" name="tamanhoCartEstudante">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">1.ª Cor</label>
                              <input type="color" class="form-control lead text-center" id="corCart1" name="corCart1">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">2.ª Cor</label>
                              <input type="color" class="form-control lead text-center" id="corCart2" name="corCart2">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Bordas</label>
                              <input type="color" class="form-control lead text-center" id="corBordasCart" name="corBordasCart">
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label class="lead control-label">Letras</label>
                              <input type="color" class="form-control lead text-center" id="corLetrasCart" name="corLetrasCart">
                            </div>
                          </fieldset>

                          <fieldset class="form-group" style="border:solid rgba(0, 0, 0, 0.3) 1px; padding:4px;">
                            <legend><strong>Cabeçalho</strong></legend>
                            
                              <div class="col-lg-3 col-md-3">
                                <label class="lead control-label">Insignia Usar</label>
                                <select class="form-control" id="insigniaUsar" name="insigniaUsar" required>
                                  <option value="republica">República</option>
                                  <option value="escola">Escola</option>
                                </select>
                              </div>
                              <div class="col-lg-12 col-md-12">
                                <label class="lead control-label">Texto</label>
                                <textarea class="form-control" name="cabecalhoPrincipal" id="cabecalhoPrincipal" required></textarea>
                              </div>
                          </fieldset>

                          <div class="form-group">
                            <div class="col-lg-12 col-md-12 lead">
                              <label class="lead">Rodapé</label>
                              <input type="text" required class="form-control" id="rodapePrincipal" name="rodapePrincipal">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-lg-6 col-md-6 lead">
                              <fieldset style="border:solid black 1px; padding:10px; border-radius: 10px; border:solid rgba(0, 0, 0, 0.5) 1px;">
                                <legend style="margin-bottom:-10px;"><strong>1.º Assinante</strong></legend>
                                <div class="row">
                                  <div class="col-lg-6 col-md-6">
                                  <label>Designação</label>
                                  <input type="text" class="form-control" id="designacaoAssinate1" name="designacaoAssinate1">
                                </div>
                                <div class="col-lg-6 col-md-6">
                                  <label>Nome</label>
                                  <input type="text" class="form-control" id="nomeAssinate1" name="nomeAssinate1">
                                </div>
                                </div>
                              </fieldset>
                            </div>
                            <div class="col-lg-6 col-md-6 lead">
                              <fieldset style="border:solid black 1px; padding:10px; border-radius: 10px; border:solid rgba(0, 0, 0, 0.5) 1px;">
                                <legend style="margin-bottom:-10px;"><strong>2.º Assinante</strong></legend>
                                <div class="row">
                                  <div class="col-lg-6 col-md-6">
                                  <label>Designação</label>
                                  <input type="text" class="form-control" id="designacaoAssinate2" name="designacaoAssinate2">
                                </div>
                                <div class="col-lg-6 col-md-6">
                                  <label>Nome</label>
                                  <input type="text" class="form-control" id="nomeAssinate2" name="nomeAssinate2">
                                </div>
                                </div>
                              </fieldset>
                            </div>
                          </div>
                                
                          <input type="hidden" name="action" value="actualizarDefinicoesConta">

                          <div class="form-group">
                            <div class="col-lg-12">
                              <div class="col-lg-3 col-md-3"> 
                                  <button  type="submit" class="btn-primary  btn lead"><i class="fa fa-check"></i> Actualizar</button>                                    
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
