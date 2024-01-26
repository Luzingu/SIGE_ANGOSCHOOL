<?php session_start();
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Relatório do Aluno", "relatorioAluno");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();     
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->listaClassesPorCurso();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>

  <?php $conexaoFolhas->folhasCss();?>

  <style type="text/css">

      .paraRelatorios div{
        line-height: 30px;
      }

    .etiqueta, .panel-body{
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

    #notasEmAtraso form, #notasSistema form{
      border-bottom:solid rgba(0, 0, 0, 0.2) 2px;
      padding-top: 10px;
      padding-bottom: 20px;
    }


</style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();

    $idPMatricula = isset($_GET["idPMatricula"])?$_GET["idPMatricula"]:"";
    $usuariosPermitidos[] = "Professores";

    $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";

    if($valorPesquisado!=""){
      $valorPesquisado = trim($valorPesquisado);
      if(seTudoMaiuscula($valorPesquisado)){
        $valorPesquisado = strtolower($valorPesquisado);
      }
      $condicoesPesquisa = [array("nomeAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("biAluno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInterno"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeAluno"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("nomeAluno"=>ucwords($valorPesquisado))];

       $pedro = $manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>['$in'=>array("A", "Y", "T")], '$or'=>$condicoesPesquisa], ["escola"], 20);
       $idPMatricula = valorArray($pedro, "idPMatricula");   
    } 
    if($idPMatricula!=""){
      $manipulacaoDados->papaJipe("", "", "", $idPMatricula);  
    }
    
    $vetor = $manipulacaoDados->selectArray("alunosmatriculados", [], ["idPMatricula"=>$idPMatricula, "escola.estadoAluno"=>['$in'=>array("A", "Y", "T")], "escola.idMatEscola"=>$_SESSION['idEscolaLogada']], ["escola"], 1);
    $idCursoMaster = isset($_GET["idCursoMaster"])?$_GET["idCursoMaster"]:valorArray($vetor, "idMatCurso", "escola");
    $seCursoProvisorio="nao";
    if($idCursoMaster!=valorArray($vetor, "idMatCurso", "escola")){
      $seCursoProvisorio="sim";
    }
    $vetor = $manipulacaoDados->sobreEscreverAluno($vetor, $idCursoMaster);
    $sobreCursoAluno = $manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>valorArray($vetor, "idMatCurso", "escola")]);

    $listaTodosAnosLectivos = $manipulacaoDados->selectArray("anolectivo", ["idPAno", "numAno"], ["idPAno"=>array('$ne'=>(int)$manipulacaoDados->idAnoActual)], [], "", [], ["numAno"=>-1]);
    
    echo "<script>var idCursoMaster ='".$idCursoMaster."'</script>";
    echo "<script>var tipoCurso ='".valorArray($sobreCursoAluno, "tipoCurso")."'</script>";
    echo "<script>var sePorSemestre ='".valorArray($sobreCursoAluno, "sePorSemestre")."'</script>";
    echo "<script>var duracaoCurso ='".valorArray($sobreCursoAluno, "duracao")."'</script>";
    echo "<script>var idCursoP ='".valorArray($vetor, "idMatCurso", "escola")."'</script>";
    echo "<script>var classeAlunoP ='".valorArray($vetor, "classeActualAluno", "escola")."'</script>";
    echo "<script>var anoActual='".$manipulacaoDados->numAnoActual."'</script>";
    echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";
    $idPMatricula = valorArray($vetor, "idPMatricula");
    echo "<script>var idPMatricula='".valorArray($vetor, "idPMatricula")."'</script>";

    echo "<script>var dadosAluno=".json_encode($vetor)."</script>";

    $sobreTurmaAluno = listarItensObjecto($vetor, "reconfirmacoes", ["idReconfAno=".$manipulacaoDados->idAnoActual, "idMatCurso=".valorArray($vetor, "idMatCurso", "escola"), "idReconfEscola=".$_SESSION['idEscolaLogada']]);     
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "relatorioAluno", array(), "msg")){
            
            if($_SESSION["idUsuarioLogado"]==35){
            }
            
           
         ?>


           <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-md-3 col-sm-6 text-center">
                  <h4 id="nomeAlunoPesquisado"><?php echo valorArray($vetor, "nomeAluno");?></h4>
                  <div class="follow-ava text-center">
                    <img src="<?php echo '../../../fotoUsuarios/'.valorArray($vetor, 'fotoAluno'); ?>" class="medio" id="imagemAluno">
                  </div>

                  <?php if($_SESSION['idUsuarioLogado']==35){ ?>
                    <h3 id="numeroInterno" style="color:white;"><?php echo valorArray($vetor, "numeroInterno")."<br/>(".valorArray($vetor, "idPMatricula")." / ".valorArray($vetor, "grupo").")"; ?></h3>
                  <?php } ?>
                </div>
                
                <div class="col-lg-3 col-md-3 col-sm-6 follow-info">
                  <p><i class="fa fa-phone"></i><strong id="numeroTelefone"> <?php echo valorArray($vetor, "telefoneAluno"); ?></strong></p>
                  <h6>
                    <span><strong id="emailAluno">@<?php echo valorArray($vetor, "emailAluno"); ?></strong></span>
                  </h6>

                  <h6>
                    <span><i class="fa fa-calendar"></i><strong id="dataMatricula"> <?php echo valorArray($vetor, "dataMatricula", "escola"); ?></strong></span>
                  </h6>
                  <?php 
                  $papaJipe = listarItensObjecto($vetor, "reconfirmacoes", ["idReconfAno!=".$manipulacaoDados->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada']]);
                  foreach($papaJipe as $turma){ ?>

                  <h6>
                    <span><strong id="dataMatricula"> <?php echo $manipulacaoDados->selectUmElemento("anolectivo", "numAno", ["idPAno"=>$turma["idReconfAno"]])."-".$manipulacaoDados->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$turma["idMatCurso"]])." - ".$turma["classeReconfirmacao"]." - ".$turma["designacaoTurma"] ?></strong></span>
                  </h6>
                <?php  }
                    if(count(listarItensObjecto($vetor, "reconfirmacoes", ["idReconfAno=".$manipulacaoDados->idAnoActual, "idReconfEscola=".$_SESSION['idEscolaLogada'], "estadoReconfirmacao=A", "idMatCurso=".valorArray($vetor, "idMatCurso", "escola")]))>0){
                        echo '<strong><i class="fa fa-check fa-2x text-success"></i></strong>';
                    }else{
                        echo '<strong><i class="fa fa-times fa-2x text-danger"></i></strong>';
                    }
                    if(valorArray($vetor, "estadoAluno", "escola")=="T"){
                      echo "<br><strong class='text-danger'>TRANSFERIDO</strong>";
                    }
                    if(valorArray($vetor, "seBolseiro", "escola")=="V"){
                      echo "<br><strong class='text-primary'><i class='fa fa-id-card'></i> BOLSEIRO</strong>";
                    }
                ?>
                  
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 follow-info weather-category">
                  <div class="text-center">
                    <strong class="text-center cursoActualAluno" id="cursoCima"><?php echo valorArray($sobreCursoAluno, "abrevCurso"); ?></strong>
                  </div>                  
                </div>


                <div class="col-lg-2 col-md-2 col-sm-12 follow-info weather-category">
                  <div class="text-center">
                    <strong class="text-center classeActualAluno" id="classeCima" style="font-size:15pt;"><?php

                    if(valorArray($vetor, "classeActualAluno", "escola")==120){
                      echo "FINALISTA<br/>".$manipulacaoDados->selectUmElemento("anolectivo", "numAno", ["idPAno"=>valorArray($vetor, "idMatFAno", "escola")]);
                    }else{
                      echo classeExtensa($manipulacaoDados, valorArray($vetor, "idMatCurso", "escola"), valorArray($vetor, "classeActualAluno", "escola"));
                    }
                    ?></strong>
                  </div>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12 follow-info weather-category">
                  <div class="text-center">
                    <strong class="text-center turmaAluno" id="turmaCima"><?php echo valorArray($sobreTurmaAluno, "designacaoTurma")." /".$manipulacaoDados->selectUmElemento("listaturmas", "numeroSalaTurma", ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$manipulacaoDados->idAnoActual, "classe"=>valorArray($vetor, "classeActualAluno", "escola"), "nomeTurma"=>valorArray($sobreTurmaAluno, "nomeTurma"), "idPNomeCurso"=>valorArray($vetor, "idMatCurso", "escola")]); ?></strong>  
                  </div>
                </div>


              </div>
            </div>
          </div>
        </div>

        <form class="row" id="pesquisarAluno">
          <?php
            $valorCaixa = valorArray($vetor, 'numeroInterno');
            if(trim($valorCaixa)=="-"){
              $valorCaixa="";
            }

           ?>

          <div class="col-lg-8 col-md-8" id="pesqUsario"><br>
            <input type="search" class="form-control" autocomplete="off" placeholder="Pesquisar Aluno..." required id="valorPesquisado" list="sugestoesNomesAlunos" value="<?php echo $valorCaixa; ?>">   
          </div>
          <datalist id="sugestoesNomesAlunos">
          </datalist>
          <div class="col-lg-2 col-md-2"><br>
            <button type="submit" class="btn lead btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
          </div>

          <div class="col-lg-2 col-md-2" id="pesqUsario">
            <label>Curso</label>
            <select class="form-control" id="idCursoMaster">
              <?php 
              foreach(valorArray($vetor, "idCursos", "escola") as $a){
                echo "<option value='".$a["idMatCurso"]."'>".$manipulacaoDados->selectUmElemento("nomecursos", "abrevCurso", ["idPNomeCurso"=>$a["idMatCurso"]])."</option>";
              }
               ?>
            </select>
          </div>
          <input type="hidden" name="action" value="pesquisarAluno">
        </form>
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <?php 
              if(isset($_GET["valorPesquisado"])){
                $pedro = $manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>['$in'=>array("A", "Y", "T")], "idPMatricula"=>array('$ne'=>$idPMatricula), '$or'=>$condicoesPesquisa], ["escola"], 10); 

              $contador=0;
               foreach($pedro as $pepe){
                  $contador++;
                  echo "<i class='fa fa-user-circle'></i> <a href='?idPMatricula=".$pepe["idPMatricula"]."' class='lead'>".$pepe["nomeAluno"]." (".$pepe["numeroInterno"].")</a>&nbsp;&nbsp;";
               
               }
             }

             ?>
          </div>
        </div>
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
                  <li class="">
                    <a data-toggle="tab" href="#pagamentos" class="lead">
                        <i class="fa fa-money-check"></i>
                      Pagamentos
                    </a>
                  </li>
              <?php if(valorArray($vetor, "idPMatricula")!=NULL && valorArray($vetor, "idPMatricula")!="" && $verificacaoAcesso->verificarAcesso("", ["relatorioAluno"], array())){ ?>
                  <li class="">
                    <a data-toggle="tab" href="#editarDados" class="lead">
                        <i class="fa fa-user-edit"></i>
                        Editar Dados
                    </a>
                  </li>
                  <li class="">
                    <a data-toggle="tab" href="#aprovAcademico" class="lead">
                        <i class="fa fa-table"></i>
                        Notas
                    </a>
                  </li>
                  <li class="">
                    <a data-toggle="tab" href="#editarNotasAtraso" class="lead">
                        <i class="fa fa-pen-alt"></i>
                        Notas em Atraso
                    </a>
                  </li>
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
                    <div class="panel">
                      <div class="panel-body bio-graph-info" style="min-height: 200px;">

                        <div class="col-lg-12 col-md-12">
                          <div class="row">
                          <?php 

                            foreach(listarItensObjecto($vetor, "avaliacao_anual", ["anotacoesParaAluno!=", "anotacoesParaAluno!=null"]) as $anot){

                              if(isset($anot["anotacoesParaAluno"])){
                                foreach(explode(";", $anot["anotacoesParaAluno"]) as $a){
                                  echo "<p style='font-size:19pt;' class='text-primary'><strong>(".$manipulacaoDados->selectUmElemento("escolas", "abrevNomeEscola", ["idPEscola"=>$anot["idAvalEscola"]]).")</strong> ".str_replace("|", "- ", $a)."</p>";
                                }
                              }
                            }
                          ?> 
                          </div>
                        </div>

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
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Encarregado:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="encarregadoAluno"><?php echo valorArray($vetor, "encarregadoEducacao"); ?></div>
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
                            <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Municipio:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="municAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_municipios", "nomeMunicipio", ["idPMunicipio"=>valorArray($vetor, "municNascAluno")]); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Província:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="provAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_provincias", "nomeProvincia", ["idPProvincia"=>valorArray($vetor, "provNascAluno")]); ?></div> 
                            </div>
                          </div>

                        <div class="col-lg-6  col-md-6">
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">BI:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($vetor, "biAluno"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Emitido aos:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo dataExtensa(valorArray($vetor, "dataEBIAluno")); ?></div>
                          </div>
                          
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Período:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php 
                            $per = valorArray($vetor, "periodoAluno", "escola");
                            if($per=="reg"){
                                $per="Regular";
                            }else{
                                $per="Pós-Laboral";
                            }
                            echo $per; ?></div>
                          </div>
                          
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Anexo:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo valorArray($vetor, "identidadeAnexo"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Deficiência:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEspecialidade"><?php echo valorArray($vetor, "deficienciaAluno"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">P. da Deficiência:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEstrangeira"><?php echo valorArray($vetor, "tipoDeficienciaAluno"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Disc. Especialidade:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEspecialidade"><?php echo $manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>valorArray($vetor, "idGestDisEspecialidade", "escola")]); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">L. Estrangeira:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEstrangeira"><?php echo $manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>valorArray($vetor, "idGestLinguaEspecialidade", "escola")]); ?></div>
                          </div>
                            
                        </div>

                      </div>
                    </div>
                   </div>

                   <div id="pagamentos" class="tab-pane" >
                    <div class="panel">
                      <div class="panel-body bio-graph-info">
                        <?php listarPagamentos($vetor, $idPMatricula, $manipulacaoDados->idAnoActual); ?>
                      </div>
                    </div>
                  </div>

                  <!-- edit-profile -->
                  <div id="editarDados" class="tab-pane" >
                    <div class="panel">
                      <form class="panel-body bio-graph-info" id="formularioDadosAlunos">
                            
                        <div class="row">
                            <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                        </div>

                        <div class="row">
                          <div class="col-lg-7 col-md-7">
                            <label for="nomeAluno" class="lead">Nome Completo</label>
                            <input type="text" name="nomeAluno" class="form-control fa-border somenteLetras vazio" id="nomeAluno" autocomplete="off" required maxlength="60">

                            <div class="nomeAluno discasPrenchimento lead"></div>
                          </div>
                          <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6 ">
                              <label for="sexoAluno" class="lead">Sexo</label>
                              <select class="form-control" id="sexoAluno" name="sexoAluno">
                                  <option value="M">Masculino</option>
                                  <option value="F">Feminino</option>
                              </select>
                          </div>
                          <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                              <label for="dataNascAluno" class="lead">Data de Nasc.</label>
                              <input type="date" name="dataNascAluno" class="form-control vazio" id="dataNascAluno" required title="Data de nascimento" placeholder="Data de Nascimento" >
                              <div class="dataNascAluno discasPrenchimento lead"></div>
                            </div>                      
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3">
                              <label for="pais" class="lead">País</label>
                                <select id="pais" name="pais" class="form-control" required>
                                  <?php 
                                    foreach($manipulacaoDados->selectArray("div_terit_paises", [], [], [], "", [], ["nomePais"=>1]) as $a){
                                      echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                                    }
                                   ?>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label for="provincia" class="lead">Província</label>
                              <select id="provincia" name="provincia" class="form-control" required></select>
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label for="municipio" class="lead">Municipio</label>
                                <select id="municipio" name="municipio" class="form-control municipio lead" required ></select>
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label for="comuna" class="lead">Comuna</label>
                                <select id="comuna" name="comuna" class="form-control comuna lead" required></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3">
                              <label for="tipoDocumento" class="lead labelBI">Documento de Identif.</label>
                              <select type="text" name="tipoDocumento" class="form-control vazio" id="tipoDocumento">
                                <option value="BI">Bilhete de Indentidade</option>
                                <option>Cédula</option>
                                <option>Passaporte</option>
                              </select>
                            </div>                          
                            <div class="col-lg-4 col-md-4">
                              <label for="numBI" class="lead labelBI">N.º de  Identificação</label>
                              <input type="text" name="numBI" class="form-control vazio" id="numBI" autocomplete="off" maxlength="15" >
                              <div class="numBI discasPrenchimento"></div>
                            </div>
                            <div class="col-lg-3 col-md-3">
                              <label for="localEmissao" class="lead labelBI">Local de Emissão</label>
                              <input type="text" name="localEmissao" class="form-control vazio" id="localEmissao" autocomplete="off" maxlength="15" >
                              <div class="numBI discasPrenchimento"></div>
                            </div>
                            <div class="col-lg-2 col-md-2">
                              <label for="dataEmissaoBI" class="lead">Emitido aos</label>
                                  <input type="date" name="dataEmissaoBI" class="form-control data" id="dataEmissaoBI" placeholder="Data de Emissão do BI">
                                  <div class="dataEmissaoBI discasPrenchimento lead"></div>
                            </div>
                                                    </div>

                        <div class="row">
                          <div class="col-lg-2 col-md-2">
                              <label for="dataCaducidadeBI" class="lead">Caduca aos</label>
                                  <input type="date" name="dataCaducidadeBI" class="form-control data" id="dataCaducidadeBI">
                            </div>
                            <div class="col-lg-4 col-md-4">
                            <label for="nomePai" class="lead">Nome do Pai</label>
                               <input type="text" name="nomePai" class="form-control vazio somenteLetras" id="nomePai" title="Nome do Pai" maxlength="60" >
                               <div class="nomePai discasPrenchimento lead" autocomplete="off"></div>
                          </div>                    
                          <div class="col-lg-3 col-md-3">
                            <label for="nomeMae" class="lead">Nome da Mãe</label>
                               <input type="text" name="nomeMae" class="form-control vazio somenteLetras" id="nomeMae" title="Nome da mãe" maxlength="60" >
                               <div class="nomeMae discasPrenchimento lead" autocomplete="off" style="margin-top: -15px;"></div>
                          </div>
                          <div class="col-lg-3 col-md-3">
                            <label for="nomeEncarregado" class="lead">Encarregado(a)</label>
                               <input type="text" name="nomeEncarregado" class="form-control vazio" id="nomeEncarregado" title="Número de telefone" autocomplete="off" maxlength="60">

                               <div class="nomeEncarregado discasPrenchimento lead"></div>
                          </div>
                          <div class="col-lg-2 col-md-2">
                            <label for="numTelefone" class="lead">Telefone</label>
                               <input type="text" name="numTelefone" class="form-control numeroDeTelefone vazio" id="numTelefone" title="Número de telefone" autocomplete="off" maxlength="12" >
                               <div class="numTelefone discasPrenchimento lead"></div>
                          </div>
                          <div class="col-lg-4 col-md-4">
                            <label for="nomeEncarregado" class="lead">E-mail</label>
                               <input type="email" name="emailAluno" class="form-control vazio" id="emailAluno" title="E-mail do Aluno" autocomplete="off" maxlength="60">
                          </div>
                          <div class="col-lg-2 col-md-2">
                              <label for="numTelefone" class="lead">Período</label>
                               <select class="form-control" name="periodoAluno" id="periodoAluno" required="">
                                 <?php 
                                  if(trim(valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas"))=="regPos"){
                                    echo "<option value='reg'>Regular</option>
                                    <option value='pos'>Pós-Laboral</option>";
                                  }else{
                                     echo "<option value='reg'>Regular</option>";
                                  }

                                 ?> 
                               </select>
                          </div>
                          <div class="col-lg-2 col-md-2">
                            <label for="turnoAluno" class="lead">Turno</label>
                            <select class="form-control lead" name="turnoAluno" id="turnoAluno" required="">  
                             </select>
                          </div>
                          <div class="col-lg-2 col-md-2">
                            <label for="nomeMae" class="lead">N.º de Processo</label>
                               <input type="text" name="numeroProcesso" class="form-control vazio" id="numeroProcesso" title="Número de Processo" maxlength="20" >
                          </div>
                      </div>

                      <div class="row">
                          <div class="col-lg-4 col-md-4">
                            <label for="idPCursoForm" class="lead">Curso (Opção)</label>
                            <select class="lead form-control" id="idPCursoForm" name="idPCurso">
                              <?php 
                                foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "areaFormacaoCurso", "nomeCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){

                                  echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                                }
                              
                               ?>  
                            </select>
                          </div>
                          <div class="col-lg-3 col-md-3">
                            <label for="classeAlunoForm" class="lead">Classe</label>
                             <select class="form-control" id="classeAlunoForm" name="classeAluno" required="">
                                <?php 
                                  echo "<optgroup id='listaClasses'></optgrup>";
                                  echo "<optgroup label='Finalista'>";
                                  foreach($listaTodosAnosLectivos as $ano){
                                    echo "<option value='FIN_".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                                  }
                                  echo "</optgrup>";
                                  
                                 ?>            
                              </select>
                          </div>
                          <div class="col-lg-2 col-md-2">
                              <label for="numTelefone" class="lead">Acesso ao Sist.</label>
                               <select class="form-control" name="acessoConta" id="acessoConta">
                                 <option value="A">Autorizado</option>
                                 <option value="I">Não Autorizado</option>
                               </select>
                          </div>
                          <div class="col-lg-3 col-md-3 lead">
                            <label for="idMatAnexo" class="lead">Anexo</label>
                            <select class="form-control" required="" id="idMatAnexo" name="idMatAnexo">
                              <?php 
                                foreach ($manipulacaoDados->selectArray("escolas", ["anexos.idPAnexo", "anexos.identidadeAnexo"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]) as $a) {

                                  echo "<option value='".$a["anexos"]["idPAnexo"]."'>".$a["anexos"]["identidadeAnexo"]."</option>";
                                }
                               ?>
                            </select>
                          </div>
                        </div>

                        <div class="row">

                          
                            <div class="lead col-md-4 col-lg-4">
                              <label for="discEspecialidade" class="lead">Disciplina (Opção)</label>
                                <select class="form-control" id="discEspecialidade" name="discEspecialidade">
                                  <?php echo "<option value=''>Seleccionar</option>";

                                  foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)]]) as $disciplina) { 
 
                                    echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                                  }

                                   ?>
                                </select>
                            </div> 
                            <div class="lead col-md-3 col-lg-3">
                              <label for="lingEspecialidade" class="lead">Língua (Opção)</label>
                                <select class="form-control" id="lingEspecialidade" name="lingEspecialidade">
                                  <?php 
                                    foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(20, 21)]]) as $disciplina) {
 
                                      echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                                    }
                                   ?>

                                </select>
                            </div>
                            <div class="lead col-md-2 col-lg-2">
                              <label for="tipoDeficiencia" class="lead">Estado</label>
                                <select id="estadoDeDesistenciaNaEscola" name="estadoDeDesistenciaNaEscola" class="form-control lead" required="">
                                    <option value="A">Activo</option>
                                    <option value="D">Desistente</option>
                                    <option value="N">Mat. Anulada</option>
                                    <option value="F">Excluido por Faltas</option>
                                </select>
                            </div>
                            <div class="lead col-md-3 col-lg-3">
                              <label for="deficiencia" class="lead">Deficiência</label>
                                <select class="form-control" id="deficiencia" name="deficiencia">
                                  <option value="">Nenhuma Deficiencia</option>
                                  <option>Física</option>
                                  <option>Mental</option>
                                  <option>Visual</option>
                                  <option>Auditiva</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                          <div class="lead col-md-3 col-lg-3">
                              <label for="tipoDeficiencia" class="lead">Patologia da Deficiência</label>
                                <select class="form-control" id="tipoDeficiencia" name="tipoDeficiencia">
                                </select>
                            </div>                          
                          <div class="col-lg-3 col-md-3">
                            <label class="lead" for="nomeEntidade">Foto:</label>
                            <input type="file" name="fotoAluno" value="" accept='.jpg, .png, .jpeg' class="form-control fa-border vazio" id="fotoAluno">
                          </div>
                          <div class="col-lg-4 col-md-4"><br>
                            <label><input type="checkbox" id="seBolseiro" name="seBolseiro"> Bolseiro</label>
                            <input type="hidden" id="beneficiosDaBolsa" name="beneficiosDaBolsa">
                          </div>
                        </div>
                        <fieldset style="border:solid black 1px; border-radius: 10px; padding: 10px;" class="row" id="dadosSobreBolsa" style="display: none;">
                          <legend>Benfícios da Bolsa</legend>
                            <?php 
                            foreach($manipulacaoDados->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>array('$nin'=>array(9, 8, 1))], [], "", [], ["idPTipoEmolumento"=>1]) as $a){

                              $array =  listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "emolumentos", ["idTipoEmolumento=".$a["idPTipoEmolumento"], "idCurso=".valorArray($vetor, "idMatCurso", "escola"), "classe=".valorArray($vetor, "classeActualAluno", "escola"), "valor>0"]);
                              if(count($array)>0){

                             ?>
                              <div class="col-lg-2 col-md-2">
                                <small><?php echo $a["designacaoEmolumento"]; ?></small>
                                <input type="number" value="<?php echo floatval(valorArray($array, "valor")); ?>" codigoEmolumento="<?php echo $a["codigo"] ?>" mes="" idPTipoEmolumento="<?php echo $a["idPTipoEmolumento"] ?>" posicao="0" class="form-control text-center" id="bolsa_<?php echo $a["idPTipoEmolumento"]; ?>" name="bolsa_<?php echo $a["idPTipoEmolumento"]; ?>" min="0">
                              </div>
                            <?php } } 

                            $posicao=0;
                            foreach($manipulacaoDados->mesesAnoLectivo as $mes){
                                $array =  listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "emolumentos", ["idTipoEmolumento=1", "idCurso=".valorArray($vetor, "idMatCurso", "escola"), "classe=".valorArray($vetor, "classeActualAluno", "escola"), "mes=".$mes, "valor>0"]);
                                if(count($array)>0){

                               ?>
                                <div class="col-lg-2 col-md-2">
                                  <small><?php echo nomeMes($mes); ?></small>
                                  <input type="number" mes="<?php echo $mes; ?>" value="<?php echo floatval(valorArray($array, "valor")); ?>" idPTipoEmolumento="1" class="form-control text-center" id="bolsa_<?php echo $a["idPTipoEmolumento"]; ?>" posicao="<?php echo $posicao; ?>" codigoEmolumento="propina" name="bolsa_<?php echo $a["idPTipoEmolumento"]; ?>" min="0">
                                </div>
                              <?php }
                              $posicao++;
                            }

                             ?>
                            
                        </fieldset>
                        <div class="row">
                          <div class="col-lg-12">
                            <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-user-edit"></i> Alterar</button>
                          </div>
                        </div>
                        <input type="hidden" name="seCursoProvisorio" value="<?php echo $seCursoProvisorio; ?>">

                          <input type="hidden" name="idPMatricula" value="<?php echo valorArray($vetor, "idPMatricula"); ?>">
                          <input type="hidden" name="action" value="editarMatricula">
                          <input type="hidden" name="areaEmExecucao" id="areaEmExecucao" value="<?php echo "matriculados"; ?>"> 
                      </form>
                    </div>
                  </div>
                  <div id="aprovAcademico" class="tab-pane" >
                    <div class="panel">
                      <div class="panel-body bio-graph-info">
                            <div class="table-responsive" id="antigasMatriculas" >
                              <table class="table table-striped table-bordered table-hover" >
                                  <thead class="corPrimary">
                                      <tr>
                                          <th class="lead"><strong> <i class="fa fa-book-open"></i> Disciplina</strong></th>
                                          <th class="lead text-center"><strong><i class="fa fa-pen-alt"></i> Média</strong></th>
                                          <th class="lead text-center"><strong><i class="fa fa-print"></i></th>
                                      </tr>
                                  </thead>
                                  <tbody id="informacoesAcademicas">

                                      <?php 
                                      $notas = listarItensObjecto($vetor, "pautas", ["idPautaCurso=".valorArray($vetor, "idMatCurso", "escola")]);

                                      foreach (distinct2($notas, "classePauta") as $classe) {

                                        echo "<tr class='corPrimary'><td colspan='3' class='text-center lead'><strong>".classeExtensa($manipulacaoDados, valorArray($vetor, "idMatCurso", "escola"), $classe)."</strong></td></tr>";
                                        foreach(array_filter($notas, function($mamale) use ($classe, $vetor){
                                          return $mamale["classePauta"]==$classe;
                                        }) as $nota){
                                          echo "<tr><td class='lead'>".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$nota["idPautaDisciplina"]])."</td>".nota(nelson($nota, "mf"), $nota["classePauta"], nelson($nota, "recurso"), nelson($nota, "exameEspecial"))."<td class='lead text-center'><a href='../../relatoriosPdf/declaracaoDisciplina.php?idPNomeDisciplina=".$nota["idPautaDisciplina"]."&idPMatricula=".$idPMatricula."'><i class='fa fa-print'></i> Certificado</a></td></tr>";
                                        }
                                      }
                                      ?>
                                  </tbody>
                                </table>
                            </div> 
                      </div>
                    </div>
                  </div>
                  
                  <div id="editarNotasAtraso" class="tab-pane">
                    <div class="panel">
                      <div class="panel-body bio-graph-info">
                        <div class="row">
                          <div class="col-lg-2 col-md-2 lead">
                              Notas da:
                              <select class="form-control" id="classeNotasAtraso">
                                <?php 
                                  echo "<option value=''>Seleccionar</option>";
                                  foreach(listarItensObjecto($sobreCursoAluno, "classes") as $classe){
                                    echo "<option value='".$classe["identificador"]."'>".$classe["designacao"]."</option>";
                                  }
                                  ?>
                              </select>
                          </div>
                           <div class="col-lg-2 col-md-2 lead">
                            Ano Lectivo
                            <select class="form-control" id="anoAnterior" name="anoAnterior">
                              <?php 

                              foreach($listaTodosAnosLectivos as $ano){

                                echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                              }

                               ?>
                            </select>
                          </div>
                          <div class="col-lg-8 col-md-8 text-right"><br>
                            <a href="#" class="lead btn btn-primary" id="carregarNotasAtraso"><i class="fa fa-redo"></i> Actualizar</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button class="btn btn-success lead btnAlterarNotasAtraso"><i class="fa fa-check"></i> Alterar</button>
                          </div>
                        </div><br>  
                        <div id="notasEmAtraso"></div>

                        <div class="col-lg-12 col-md-12 text-right">
                            <button class="btn btn-success lead btnAlterarNotasAtraso" id=""><i class="fa fa-check"></i> Alterar</button>
                          </div>

                      </div>
                    </div>
                  </div>


                  <div id="documentos" class="tab-pane">
                    <div class="panel">
                      <div class="panel-body bio-graph-info" id="listaDocumentos">
                            <div class="row">
                              <fieldset class="col-lg-12 col-md-12" style="border:solid rgba(0, 0, 0, 0.3) 1px; border-radius: 10px;" id="boletimDocumento">
                                  <legend style="width: 100px;"><strong>Boletim</strong></legend>
                                  <div class="row"> 
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <select class="form-control" id="anosLectivos">
                                      <?php foreach ($manipulacaoDados->anosLectivos as $ano){                      
                                        echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                                      } ?>  
                                        </select>
                                    </div>
                                    <?php if(valorArray($vetor, "sePorSemestre")!="sim"){ ?>
                                      <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <a href="#" class="boletim btn-primary btn" id="I"><i class="fa fa-print"></i> I Trimestre</a>
                                      </div>
                                      <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <a href="#" class="boletim btn-primary btn" id="II"><i class="fa fa-print"></i> II Trimestre</a>
                                      </div>
                                      <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                        <a href="#" class="boletim btn-primary btn" id="III"><i class="fa fa-print"></i> III Trimestre</a>
                                      </div>
                                    <?php } ?>
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-sm-6">
                                      <a href="#" class="boletim btn-primary btn" id="IV"><i class="fa fa-print"></i> Final</a>
                                    </div>
                                  </div>
                              </fieldset>
                            </div>                           
                            <div class="row paraRelatorios">
                              <div class="col-lg-3 col-md-3 col-sm-4 col-sm-6">
                                <a href="#" class="termoAproveitamento btn-primary btn" documento="120"><i class="fa fa-print"></i> Termo de Aproveitamento</a>
                              </div>
                              <div class="col-lg-3 col-md-3 col-sm-4 col-sm-6">
                                <a href="#" class="declaracaoSemNotas btn-primary btn"><i class="fa fa-print"></i> Declaração sem Notas</a>
                              </div>             
                              <?php   

                                foreach(listarItensObjecto($sobreCursoAluno, "classes") as $a){ ?>

                                  <div class="col-lg-3 col-md-3 col-sm-4 col-sm-6">
                                  <a href="#" documento="<?php echo $a["identificador"] ?>" class="declaracao btn btn-primary"><i class="fa fa-print"></i> Declaração (<?php echo $a["designacao"]; ?>)</a>
                                </div>
                              <?php } ?>
                             <div class="col-lg-3 col-md-3 col-sm-4 col-sm-6">
                                <a href="#" documento="120" class="declaracao btn btn-primary" certificado="definitivo"><i class="fa fa-print"></i> Certificado</a>
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

<form id="formValoresNotaSistema">
  <input type="hidden" id="action" name="action">
  <input type="hidden" id="notas" name="notas">
  <input type="hidden" id="idPCurso" name="idPCurso">
  <input type="hidden" id="tipoCurso" name="tipoCurso">
  <input type="hidden" id="classeAluno" name="classeAluno">
  <input type="hidden" id="classeNotas" name="classe">
  <input type="hidden" id="idPMatricula" name="idPMatricula">
  <input type="hidden" id="modeloPauta" name="modeloPauta">
  <input type="hidden" id="dadosAtraso" name="dadosAtraso">
</form>
<?php $conexaoFolhas->folhasJs(); ?>
<script type="text/javascript" src="manipuladorNotasAtraso2.js"></script>

<?php $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();

  function nota($mediaFinal, $classe, $recurso, $exameEspecial){
    $nota = $mediaFinal;
    if($recurso!=NULL){
      $nota=$recurso;
    }
    
    if($exameEspecial!=NULL){
      //$nota=$exameEspecial;
    }
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

  function boletim($b){
    if($b==1){
      return "do Iº Trimestre";
    }else if($b==2){
      return "do IIº Trimestre";
    }else if($b==3){
      return "do IIIº Trimestre";
    }else{
      return "Final";
    }
  }

  function listarPagamentos($vetor, $idPMatricula, $idAnoActual){ ?>
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

        foreach(listarItensObjecto($vetor, "pagamentos", ["idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idHistoricoAno=".$idAnoActual, "precoPago>0"]) as $a){

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

          echo "<tr class='lead'><td>".$referencia."</td><td class='text-center'>".$a["dataPagamento"]." ".$a["horaPagamento"]."</td><td class='text-center'>".number_format($a["precoPago"], 2, ",", ".")."</td></tr>";

        }



        ?>

        </tbody>
    </table>
  </div>
  

<?php }
 ?>
       
 

<div class="modal fade" id="formularioDados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioDadosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel">Dados</h4>
              </div>

              <div class="modal-body">                 
                  <div class="row" id="idEfeitoDeclaracao">
                    <div class="lead col-lg-12 col-md-12 lead">
                      Motivo
                      <input type="text" class="form-control" value="efeitos legais" id="efeitoDeclaracao">
                    </div>
                  </div> 
                  <div class="row">
                    <div class="col-lg-12 col-md-12">
                      <input type="checkbox" name="comAssinDirectProv" id="comAssinDirectProv"> <label for="comAssinDirectProv">Com Visto do Director Provincial</label>
                    </div>
                    <div class="lead col-lg-12 col-md-12">
                      <input type="text" class="form-control" name="nomeDirectorProvincial" id="nomeDirectorProvincial" placeholder="Nome do Director Provincial">
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-12 col-md-12">
                      <input type="checkbox" name="comAssinDirectMunicipal" id="comAssinDirectMunicipal"> <label for="comAssinDirectMunicipal">Com Visto do Director Municipal</label>
                    </div>
                    <div class="col-lg-12 col-md-12">
                      <input type="text" class="form-control" name="nomeDirectorMunicipal" id="nomeDirectorMunicipal" placeholder="Nome do Director Municipal">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 col-md-12">
                      <input type="checkbox" name="comQRCode" id="comQRCode"> <label for="comQRCode">Com QR Code</label>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                    Emissão
                      <select class="form-control" id="viaDocumento" name="viaDocumento">
                        <?php for($i=1; $i<=30; $i++){
                          echo '<option value="'.$i.'">'.$i.'.ª Via</option>';
                        } ?>
                      </select>
                    </div>
                  </div>
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-left">
                      <button type="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-eye"></i> Visualizar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>