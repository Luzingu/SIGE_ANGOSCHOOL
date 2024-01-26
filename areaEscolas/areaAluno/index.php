<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar("../");
    $manipulacaoDados = new manipulacaoDados("Área do Aluno");
    
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->designacaoArea ="Área do Aluno";
    $layouts->idPArea =1;
    $manipulacaoDados->retornarAnosEmJavascript();
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
    $layouts->aside(1);
    
    $tipoEspecialidade="B";
    if(valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno")>=10){
      $tipoEspecialidade="M";
    }
    $manipulacaoDados->papaJipe("", "", "", $_SESSION['idUsuarioLogado']);
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso(1)){
         ?>
 

           <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-lg-4 col-sm-4 text-center">
                  <h3 id="nomeAlunoPesquisado"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "nomeAluno"); ?></h3>
                  <div class="follow-ava">
                    <img src="<?php echo '../../fotoUsuarios/'.valorArray($manipulacaoDados->sobreUsuarioLogado, 'fotoAluno'); ?>" class="medio" id="imagemAluno">
                  </div>
                  <h4 id="numeroInternoAluno"><strong><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "numeroInterno"); ?></strong></h4>
                </div>
                
                <div class="col-lg-2 col-md-2 col-sm-6 follow-info">
                  <p><i class="fa fa-phone"></i><strong id="numeroTelefone"> <?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "telefoneAluno"); ?></strong></p>
                  <h6>
                    <span><i class="fa fa-calendar"></i><strong id="dataMatricula"> <?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "dataMatricula", "escola"); ?></strong></span>
                    <?php 
                    if(valorArray($manipulacaoDados->sobreUsuarioLogado, "seBolseiro", "escola")=="V"){
                      echo "<br><br><strong class='text-primary'><i class='fa fa-id-card'></i> BOLSEIRO</strong>";
                    }

                     ?>
                  </h6>
                  <h6>
                    <span><strong id="emailAluno"> <?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "emailAluno"); ?></strong></span>
                  </h6>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 follow-info weather-category">
                  <div class="text-center">
                    <strong class="text-center cursoActualAluno" id="cursoCima"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "abrevCurso"); ?></strong>
                  </div>                  
                </div>


                <div class="col-lg-2 col-md-2 col-sm-12 follow-info weather-category">
                  <div class="text-center">
                    <strong class="text-center classeActualAluno" id="classeCima">
                      <?php 

                          if(valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno")==120){
                              echo "FINALISTA";
                          }else{
                            echo valorArray($manipulacaoDados->sobreUsuarioLogado, "classeActualAluno", "escola").".ª";
                          }?></strong>
                  </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 follow-info weather-category">
                  <div class="text-center">
                    <strong class="text-center turmaAluno" id="turmaCima"><?php echo valorArray($manipulacaoDados->sobreTurmaActualAluno, "designacaoTurma")." / ".completarNumero(valorArray($manipulacaoDados->sobreTurmaActualAluno, "numeroSalaTurma")); ?></strong> 
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
                                          <i class="fa fa-info-circle"></i>
                                         Informações Gerais
                                      </a>
                  </li>

                  <li class="">
                    <a data-toggle="tab" href="#pagamentos" class="lead">
                                          <i class="fa fa-donate"></i>
                                          Pagamentos
                                      </a>
                  </li>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content">
                  <!-- profile -->
                  <div id="profile" class="tab-pane active">
                    <div class="panel">
                      <div class="panel-body bio-graph-info">
                         <div class="col-lg-12 col-md-12">
                          <div class="row">
                          <?php 
                          
                          ?> 
                          </div>
                        </div>
                         <div class="col-lg-6 col-md-6">
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nome do Pai:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "paiAluno"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4 col-md-4 lead text-right lab etiqueta">Nome da Mãe:</div>
                              <div class="col-lg-8  col-md89 lead valor" id="maeAluno"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "maeAluno"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Encarregado:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="encarregadoAluno"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "encarregadoEducacao"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Sexo:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="sexoAluno"><?php echo generoExtenso(valorArray($manipulacaoDados->sobreUsuarioLogado, "sexoAluno")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nascido Aos:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="dataNascAluno"><?php echo dataExtensa(valorArray($manipulacaoDados->sobreUsuarioLogado, "dataNascAluno")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Idade:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="idadeAluno"><?php echo calcularIdade(explode("-", $manipulacaoDados->dataSistema)[0], valorArray($manipulacaoDados->sobreUsuarioLogado, "dataNascAluno"))." Anos"; ?></div>
                            </div>
                          </div>

                        <div class="col-lg-6  col-md-6">
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Municipio:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="municAluno"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "nomeMunicipio"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Província:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="provAluno"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "nomeProvincia"); ?></div>
                            </div>
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">BI:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($manipulacaoDados->sobreUsuarioLogado, "biAluno"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Emitido aos:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo dataExtensa(valorArray($manipulacaoDados->sobreUsuarioLogado, "dataEBIAluno")); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Disc. Especialidade:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEspecialidade"><?php echo $manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>valorArray($manipulacaoDados->sobreUsuarioLogado, "idGestDisEspecialidade", "escola")]); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">L. Estrangeira:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEstrangeira"><?php echo $manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>valorArray($manipulacaoDados->sobreUsuarioLogado, "idGestLinguaEspecialidade", "escola")]); ?></div>
                          </div>
                            
                          </div>
                      </div>
                    </div>
                   </div>                 
                  <!-- edit-profile -->
                  
                  <!-- edit-profile -->
                  <div id="pagamentos" class="tab-pane">
                    <div class="panel">
                      <div class="panel-body bio-graph-info">
                            <?php listarPagamentos($manipulacaoDados->sobreUsuarioLogado, $manipulacaoDados->idAnoActual); ?>
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

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
<script type="text/javascript">
  $(document).ready(function(){
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaDirector/cursos/";
  })   
</script>

 <?php function nota($nota, $classe){
    if($classe<=6){
      if($nota<5){
        return "<td class='lead text-danger text-center'>".$nota."</td>";
      }else{
        return "<td class='lead text-center'>".$nota."</td>";
      }
    }else{
      if($nota<10){
        return "<td class='lead text-danger text-center'>".$nota."</td>";
      }else{
        return "<td class='lead text-center'>".$nota."</td>";
      }
    }
  }


  function listarPagamentos($aluno, $idAnoActual){ ?>
    <div class="table-responsive" id="antigasMatriculas" >
      <table class="table table-striped table-bordered table-hover" >
        <thead class="corPrimary">
            <tr>
                <th class="lead"><strong>Referência</strong></th>
                <th class="lead text-center"><strong>Data</strong></th>
                <th class="lead text-center"><strong>Valor(Kz)</strong></th>
            </tr>
        </thead>
        <tbody id="informacoesAcademicas">
        <?php

        foreach(listarItensObjecto($aluno, "pagamentos", ["idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idHistoricoAno=".$idAnoActual, "precoPago>0"]) as $a){

          $referencia=nelson($a, "designacaoEmolumento");

          if(nelson($a, "codigoEmolumento")=="boletim"){
            if($a["referenciaPagamento"]=="IV"){
              $referencia ="Boletim Final";
            }else{
              $referencia ="Boletim do ".$a["referenciaPagamento"]." Trimestre";
            }
          }else if(nelson($a, "codigoEmolumento")=="declaracao"){
            $referencia =retornarNomeDocumento ($a["referenciaPagamento"]);
          }else if(nelson($a, "codigoEmolumento")=="propinas"){
            $referencia =nomeMes($a["referenciaPagamento"]);
          }
          echo "<tr class='lead'><td>".$referencia."</td><td class='text-center'>".$a["dataPagamento"]." ".$a["horaPagamento"]."</td><td class='text-center'>".number_format(floatval($a["precoPago"]), 2, ",", ".")."</td></tr>";

        }



        ?>

        </tbody>
    </table>
  </div>
  

<?php }


 ?>