<?php session_start(); 

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Relatório do Aluno", "relatorioAlunoInscricao");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    inicializadorDaFuncaoGestaInscricao($manipulacaoDados);
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">


    .etiqueta{
      color: black;
      font-weight: 400;
    }
    .valor{
      color: black;
      font-weight: 600;
    }

    .follow-info{
      color: white;
    }

    .weather-category{
      color: white;
      padding: 2px;
    }
    .weather-category div{
      padding-top: 20px;
      border:solid white 2px;
      margin: 0px;
      min-height: 90px;
    }

    #classeCima, #numeroCima, #cursoCima, #turmaCima{
      font-size: 20pt;
    }

    #nomeAlunoPesquisado{
      font-weight: 700;
      margin-top: -5px;
      color: white;
    }
    #numeroInternoAluno{
      margin-top: 5px;
      font-weight: 300;
      margin-left: 30px;
      color: white;
    }
    #imagemAluno{
      width: 120px;
      height: 120px;
    }
    .table-responsive{
      color: rgba(0, 0, 0, 0.8) !important;
    }
</style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
    $usuariosPermitidos = ["aDirectoria", "aPedagogica", "aAdministrativa"];

    $manipulacaoDados->conDb("inscricao");

    $vetor = array();
    if(isset($_GET["idPAluno"])){

      $vetor = $manipulacaoDados->selectArray("alunos", [], ["idPAluno"=>$_GET["idPAluno"], "idAlunoAno"=>$manipulacaoDados->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']]); 
       
    }else if(isset($_GET["valorPesquisado"])){
      $valorPesquisado = $_GET["valorPesquisado"];

      $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("codigoAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeAluno"=>ucwords($valorPesquisado))];

       $vetor = $manipulacaoDados->selectArray("alunos", [], ['$or'=>$condicoesPesquisa, "idAlunoAno"=>$manipulacaoDados->idAnoActual, "idAlunoEscola"=>$_SESSION['idEscolaLogada']]);
    }
    
    

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso($manipulacaoDados->idPArea, "relatorioAlunoInscricao", array(), "msg")){

           
         ?>


           <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-md-3 col-sm-6 text-center">
                  <h4 id="nomeAlunoPesquisado"><?php echo valorArray($vetor, "nomeAluno"); ?></h4>
                  <div class="follow-ava text-center">
                    <img src="<?php echo '../../../fotoUsuarios/default.png' ?>" class="medio" id="imagemAluno">
                  </div><br>
                  <h4 id="numeroInterno" style="color:white;"><?php echo valorArray($vetor, "codigoAluno"); ?></h4>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-6 follow-info">
                  <p><i class="fa fa-phone"></i><strong id="numeroTelefone"> <?php echo valorArray($vetor, "telefoneAluno"); ?></strong></p>
                  <h6>
                    <span><strong id="emailAluno">@<?php echo valorArray($vetor, "emailAluno"); ?></strong></span>
                  </h6>

                  <h6>
                    <span><i class="fa fa-calendar"></i><strong id="dataMatricula"> <?php echo converterData(valorArray($vetor, "dataInscricao")); ?></strong></span>
                  </h6>
                  <h6>
                    <span><i class="fa fa-user-tie"></i><strong id="dataMatricula"> <?php
                  $manipulacaoDados->conDb();

                     echo $manipulacaoDados->selectUmElemento("entidadesprimaria", "nomeEntidade", ["idPEntidade"=>valorArray($vetor, "idAlunoEntidade")]); ?></strong></span>
                  </h6>
                  
                </div>

                <div class="col-lg-5 col-md-5 col-sm-12 follow-info weather-category">
                  <ul>
                    <?php 
                    $manipulacaoDados->conDb("inscricao");

                    foreach (listarItensObjecto($vetor, "inscricao", []) as $curso) { ?>

                      <li class="lead"><i class="fa fa-pen"></i> <?php 
                    $manipulacaoDados->conDb();
                      echo $manipulacaoDados->selectUmElemento("nomecursos", "nomeCurso", ["idPNomeCurso"=>$curso["idInscricaoCurso"]]);
                      if(nelson($curso, "obsApuramento")=="A"){
                        echo " (<span class='text-success'>A</span>)";
                      }else if(nelson($curso, "obsApuramento")=="P"){
                        echo " (<span class='text-primary'>--</span>)";
                      }else{
                        echo " (<span class='text-danger'>Ex</span>)";
                      }

                       ?></li><br>
                   <?php } ?>
                  </ul>                  
                </div>
                <div class="col-lg-1 col-md-1 col-sm-12 follow-info weather-category">
                  <br><br>
                  <?php 
                    $manipulacaoDados->conDb("inscricao");

                    if(count(listarItensObjecto($vetor, "inscricao", ["obsApuramento=A"]))>0){
                      echo '<h3 class="text-center text-success"><strong>Apurado(a)</strong></h3>';
                    }else{
                      echo '<h3 class="text-center text-danger"><strong>Excluido(a)</strong></h3>';
                    }
                    

                  ?>                  
                </div>


              </div>
            </div>
          </div>
        </div>

        <form class="row" id="pesquisarAluno">
          <div class="col-lg-10 col-md-10" id="pesqUsario">
                <input type="search" class="form-control lead"  placeholder="Pesquisar Aluno..." required id="valorPesquisado" autocomplete="off">   
              </div>
          <div class="col-lg-2 col-md-2">
            <button type="submit" class="form-control lead btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
          </div>
          <input type="hidden" name="action" value="pesquisarAluno">
        </form>

        <div class="row">
          <div class="col-lg-12" >
            <section class="panel" style="min-height: 500px !important;">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead">
                        <i class="fa fa-info-circle"></i>
                       Informações Gerais
                    </a>
                  </li>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content">
                  <!-- profile -->
                  <div id="profile" class="tab-pane active">
                    <div class="panel">
                      <div class="panel-body bio-graph-info" style="min-height: 200px;">
                         <?php if(seOficialEscolar()){ ?>
                         <div class="col-lg-6 col-md-6">
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nome do Pai:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($vetor, "paiAluno"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4 col-md-4 lead text-right lab etiqueta">Nome da Mãe:</div>
                              <div class="col-lg-8  col-md89 lead valor" id="maeAluno"><?php echo valorArray($vetor, "maeAluno"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Sexo:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="sexoAluno"><?php echo generoExtenso(valorArray($vetor, "sexoAluno")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nascido Aos:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="dataNascAluno"><?php echo dataExtensa(valorArray($vetor, "dataNascAluno")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Idade:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="idadeAluno"><?php echo calcularIdade(explode("-", $manipulacaoDados->dataSistema)[0], valorArray($vetor, "dataNascAluno"))." Anos"; ?></div>
                            </div>
                            
                          </div>

                          <div class="col-lg-6  col-md-6">
                              <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Municipio:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="municAluno"><?php 
                                $manipulacaoDados->conDb(); 
                                echo $manipulacaoDados->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($vetor, "municNascAluno")]); ?></div>
                              </div>

                              <div class="row">
                                <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Província:</div>
                                <div class="col-lg-8  col-md-8 lead valor" id="provAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_provincias", "nomeProvincia", ["idPProvincia"=>valorArray($vetor, "provNascAluno")]); ?></div>
                              </div>
                              <div class="row">
                                <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">BI:</div>
                                <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($vetor, "biAluno"); ?></div>
                              </div>

                              <div class="row">
                                <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Emitido aos:</div>
                                <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo dataExtensa(valorArray($vetor, "dataEBIAluno")); ?></div>
                              </div>
                            
                          </div>
                        <?php } ?>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();

 ?>