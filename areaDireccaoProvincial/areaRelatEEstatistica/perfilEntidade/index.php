<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Sobre Funcionários");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #citacaoProfessor{
      font-style: italic;
      color: white;
      font-weight: 800;
      color: orange;
    }
    .valor{
      font-weight: 800;
    }
    .informPerfil{
      font-weight: 1000;
    }
    .cargoProfessor{
      color: white;
      font-weight: 700;
      font-size: 18px;  
    }
    .nomeUsuarioCorente{
      font-weight: 800;
      color:white;
    }
    #imageProfessor{
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
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->headerUsuario();
    $layouts->areaEstatERelat();
    $usuariosPermitidos[] = "aRelEstatistica";
    $idPProfessor = isset($_GET["aWRQUHJvZmVzc29y"])?$_GET["aWRQUHJvZmVzc29y"]:"";
    $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:""; 

    if($valorPesquisado!=""){
        $valorPesquisado = explode("-", $valorPesquisado);

        if(trim($valorPesquisado[0])==""){
          $valorPesquisado[0]="--";
        }

        if(!isset($valorPesquisado[1])){
          $valorPesquisado[1]="--";
        }
        $valorPesquisado[1] = trim($valorPesquisado[1]);
        $valorPesquisado[0] = trim($valorPesquisado[0]);
 
        $idPProfessor=$manipulacaoDados->selectUmElemento("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idPEntidade=idFEntidade LEFT JOIN escolas ON idEntidadeEscola=idPEscola", "idPEntidade", "provincia=:provincia AND estadoActividadeEntidade=:estadoActividadeEntidade AND (nomeEntidade like '%".$valorPesquisado[0]."%' OR nomeEntidade like '%".$valorPesquisado[1]."%' OR numeroInternoEntidade like '%".$valorPesquisado[0]."%' OR numeroInternoEntidade like '%".$valorPesquisado[1]."%') AND idPEscola not in (4, 7) AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "A", "Pública"], "nomeEntidade ASC");
    }


    $array = $manipulacaoDados->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idPEntidade=idFEntidade LEFT JOIN escolas ON idEntidadeEscola=idPEscola", "*", "idPEntidade=:idPEntidade AND provincia=:provincia AND estadoActividadeEntidade=:estadoActividadeEntidade AND idPEscola not in (4, 7) AND privacidadeEscola=:privacidadeEscola", [$idPProfessor, valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "A", "Pública"]);

    echo "<script>var idPProfessor='".valorArray($array, "idPEntidade")."'</script>";
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
                  <h4 class="nomeUsuarioCorente text-center"><?php echo valorArray($array, "nomeEntidade"); ?></h4>
                  <div class="follow-ava text-center">
                    <img src="<?php echo $caminhoRetornar.'fotoUsuarios/'.valorArray($array, 'fotoEntidade'); ?>" class="medio imagemUsuarioCorrente" id="imageProfessor">
                  </div>
                  <h6 class="cargoProfessor text-center">
                    <?php echo valorArray($array, "funcaoEnt"); ?>
                  </h6>

                </div>
                <div class="col-lg-3 col-sm-3 follow-info">
                  <p class="text-justify citacaoUsuarioCorente" id="citacaoProfessor"><?php echo valorArray($array, "citacaoFavoritaEntidade"); ?></p>
                  <h6 class="outrasInformacoes">
                    <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($array, "numeroTelefoneEntidade");?></strong></span><br/><br/>

                    <span class="lead"><strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($array, "emailEntidade");?></strong></span><br/>
                    <?php 
                        $dataSaida = strtotime($manipulacaoDados->dataSistema.$manipulacaoDados->tempoSistema." - 600 seconds");

                    if(count($manipulacaoDados->selectArray("entidadesonline", "*", "idOnlineEnt=:idOnlineEnt AND estadoExpulsao=:estadoExpulsao AND dataSaida=:dataSaida AND horaSaida>:horaSaida", [$idPProfessor, "A", date("Y-m-d", $dataSaida), date("H:i:s", $dataSaida)]))>0){ ?>

                        <span class="lead text-success"><strong><i class="fa fa-rss"></i></strong></span><br/>

                    <?php  } ?>
                    </h6>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 follow-info weather-category" style="padding-top: 0px; border: none !important; color: white;">
                  <div class="">
                    <?php foreach($manipulacaoDados->selectArray("entidade_escola LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "idFEntidade=:idFEntidade", [$idPProfessor], "idPEscola ASC LIMIT 5") as $a){ ?>
        
                      <h2 class="lead"><i class="fa fa-map-marker-alt"></i><strong> <?php echo $a->nomeEscola." (".$a->estadoActividadeEntidade.") - ".$a->nivelSistemaEntidade; ?></strong></h2>
                      <?php  } ?>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <form class="row" id="pesquisarAluno">
          <?php $valor = valorArray($array, 'nomeEntidade')." - ".valorArray($array, 'numeroInternoEntidade');
            if(trim($valor)=="-"){
              $valor="";
            }
           ?>
          <div class="col-lg-10 col-md-10" id="pesqUsario">
                <input type="search" class="form-control lead"  placeholder="Pesquisar Professor..." required list="listaOpcoes" id="valorPesquisado" value="<?php echo $valor; ?>" autocomplete="off"  tipoEntidade="professores" >   
              </div>
          <div class="col-lg-2 col-md-2">
            <button type="submit" class="form-control lead btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
          </div>
          <input type="hidden" name="action" value="pesquisarAluno">
        </form>

        <div class="row">
          <div class="col-lg-12 col-md-12">
            <?php 
              if(isset($_GET["valorPesquisado"])){
                   //Outros nomes como sugestão...
                $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";
                $valorPesquisado = explode("-", $valorPesquisado);

                if(trim($valorPesquisado[0])==""){
                  $valorPesquisado[0]="--";
                }

                if(!isset($valorPesquisado[1])){
                  $valorPesquisado[1]="--";
                }
                $valorPesquisado[1] = trim($valorPesquisado[1]);
                $valorPesquisado[0] = trim($valorPesquisado[0]);

                foreach($manipulacaoDados->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idPEntidade=idFEntidade LEFT JOIN escolas ON idEntidadeEscola=idPEscola", "*", "provincia=:provincia AND estadoActividadeEntidade=:estadoActividadeEntidade AND (nomeEntidade like '%".$valorPesquisado[0]."%' OR nomeEntidade like '%".$valorPesquisado[1]."%' OR numeroInternoEntidade like '%".$valorPesquisado[0]."%' OR numeroInternoEntidade like '%".$valorPesquisado[1]."%') AND idPEntidade!=:idPEntidade AND idPEscola not in (4, 7) AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "A", valorArray($array, "idPEntidade"), "Pública"], "nomeEntidade ASC LIMIT 10") as $a){

                    echo "<i class='fa fa-user-circle'></i> <a href='?aWRQUHJvZmVzc29y=".$a->idPEntidade."' class='lead'>".$a->nomeEntidade." (".$a->numeroInternoEntidade.")</a>&nbsp;&nbsp;";
                } 
              }

             ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="panel">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead">
                        <i class="fa fa-user"></i>
                       Perfil
                    </a>
                  </li>
                  <?php if(count($array)>0){ ?>
                    <li class="">
                      <a data-toggle="tab" href="#documentos" class="lead">
                          <i class="fa fa-print"></i>
                          Relatórios
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content"> 
                  <!-- profile -->
                   <div id="profile" class="tab-pane active">
                    <div class="panel" style="min-height: 200px;">
                      <div class="bio-graph-heading lead col-sm-12 col-xs-12 col-lg-12 col-md-12" id="acercaUsuarioCorente" style="margin-bottom: 30px;">
                        <?php echo valorArray($array, "acercaEntidade"); ?>
                      </div>

                         <div class="col-lg-6 col-md-6">
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Número de Agente:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="numeroAgente"><?php echo valorArray($array, "numeroAgenteEntidade"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Categoria:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="numeroAgente"><?php echo valorArray($array, "categoriaEntidade"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nome do Pai:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($array, "paiEntidade"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4 col-md-4 lead text-right lab etiqueta">Nome da Mãe:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="maeAluno"><?php echo valorArray($array, "maeEntidade"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Sexo:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="sexoAluno"><?php echo generoExtenso(valorArray($array, "generoEntidade")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nascido Aos:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="dataNascAluno"><?php echo dataExtensa(valorArray($array, "dataNascEntidade")); ?></div>
                            </div>
                          </div>

                          <div class="col-lg-6  col-md-6">
                            <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Municipio:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="municAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_municipios", "nomeMunicipio", "idPMunicipio=:idPMunicipio", [valorArray($array, "municNascEntidade")]); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Província:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="provAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_provincias", "nomeProvincia", "idPProvincia=:idPProvincia", [valorArray($array, "provNascEntidade")]); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">BI:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($array, "biEntidade"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Emitido aos:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo dataExtensa(valorArray($array, "dataEBIEntidade")); ?></div>
                          </div>
                            
                          </div>
                    </div>
                   </div>

                   <div id="documentos" class="tab-pane">
                    <div class="panel">
                      <div class="panel-body bio-graph-info" id="listaDocumentos" style="min-height: 200px;">
                            <div class="row">
                              <div class="col-lg-12 col-md-12">
                                <a href="#" class="lead btn-primary btn" id="guiaMarcha"><i class="fa fa-print" ></i> Guia de Marcha</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="lead btn btn-primary" id="declarao"><i class="fa fa-print"></i> Declaração</a>
                                &nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="lead btn-primary btn" id="declaraoVencimento"><i class="fa fa-print"></i> Declaração com Vencimento</a>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
              </div>
            </div>
          </div>
        </div>
      <?php } echo "</div>"; $includarHtmls->rodape(); ?>
        </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); ?>
<?php $includarHtmls->formTrocarSenha(); $janelaMensagens->funcoesDaJanelaJs(); ?>


<div class="modal fade" id="modalGuiaMarcha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formGuiaMarcha">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-print"></i> Guia de Marcha</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-4 col-md-4 lead">
                        Guina N.º:
                          <input type="number" name="" id="numeroGuiaMarcha" required="" min="0" class="form-control lead text-center">
                      </div>
                      <div class="col-lg-8 col-md-8 lead">
                        Destino:
                          <select id="pais" name="pais" class="form-control lead nomePaisBI" required>
                            <?php 
                            foreach($manipulacaoDados->selectArray("div_terit_paises", "*", "", "", "nomePais ASC") as $a){
                              echo "<option value='".$a->idPPais."'>".$a->nomePais."</option>";
                            }
                           ?>
                          </select>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-lg-4 col-md-4 lead">
                        Província
                        <select id="provincia" name="provincia" class="form-control lead" required></select>
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
                        Municipio
                        <select id="municipio" name="municipio" class="form-control municipio lead" required>                            
                        </select>                        
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
                        Comuna
                        <select id="comuna" name="comuna" class="form-control municipio lead" required>                            
                        </select>                        
                      </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-12 col-md-12 lead">
                      Motivo:
                          <textarea class="form-control lead" id="motivo" required="" style="min-width: 100%; max-width: 100%; min-height: 50px;"></textarea>                       
                      </div>
                  </div>
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-file"></i> Visualizar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>



      <div class="modal fade" id="modalDeclaraoTrabalho" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="declaraoTrabalhoForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-print"></i> Declaração</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-3 col-md-3 lead">
                        Declaração N.º:
                          <input type="number" name="" id="numeroDeclaracao" required="" min="0" class="form-control lead text-center">
                      </div>
                      <div class="col-lg-9 col-md-9 lead">
                        Motivo da Decl.:
                          <input type="text" id="motivoDeclaracao" autocomplete="on" required=""  class="form-control lead">
                      </div>
                  </div>
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-file"></i> Visualizar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
