<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Control de Comissão", "controlComissaoMatricula");
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

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-users"></i> Control da Comissão de Matricula</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", ["controlComissaoMatricula"], array(), "msg")){

         $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
        echo "<script>var idPAno='".$idPAno."'</script>";

        $array = $manipulacaoDados->selectArray("AfonsoLuzingu", [], ["luzinguEscola"=>$_SESSION['idEscolaLogada'], "luzinguAno"=>$idPAno, "idReconfAno"=>$idPAno]);
        $array = $manipulacaoDados->anexarTabela($array, "entidadesprimaria", "idPEntidade", "idReconfProfessor");
        $array = $manipulacaoDados->anexarTabela($array, "nomecursos", "idPNomeCurso", "idMatCurso");

        echo "<script>var alunosReconfirmados=".json_encode($array)."</script>";
          ?>

         
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-2 lead">
              Ano:
              <select class="form-control lead" id="anosLectivos">
                  <?php 
                    foreach($manipulacaoDados->anosLectivos as $ano){                      
                        echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                      } 
                   ?>
              </select>
            </div>         
            <div class="col-lg-3 col-md-3 lead">
                Referência:
                <select class="form-control lead" id="luzingu">
                  <?php 
                  foreach($manipulacaoDados->selectDistinct("AfonsoLuzingu", "idReconfProfessor", ["luzinguEscola"=>$_SESSION['idEscolaLogada'], "luzinguAno"=>$idPAno, "idReconfAno"=>$idPAno]) as $entidade){
                    
                    echo "<optgroup label='".$manipulacaoDados->selectUmElemento("entidadesprimaria", "nomeEntidade", ["idPEntidade"=>$entidade["_id"]])."'>";


                    foreach($manipulacaoDados->selectDistinct("AfonsoLuzingu", "dataReconf", ["luzinguEscola"=>$_SESSION['idEscolaLogada'], "luzinguAno"=>$idPAno, "idReconfAno"=>$idPAno, "idReconfProfessor"=>$entidade]) as $d){

                        echo "<option value='".$entidade["_id"]."_".$d["_id"]."'>".dataExtensa($d["_id"])."</option>";
                    }
                    echo "<option value='".$entidade["_id"]."_todo'>Resumo</option>";
                    echo "</optgroup>";
                  }
                  echo "<optgroup label='Resumo'>";
                  foreach($manipulacaoDados->selectDistinct("AfonsoLuzingu", "dataReconf", ["luzinguEscola"=>$_SESSION['idEscolaLogada'], "luzinguAno"=>$idPAno, "idReconfAno"=>$idPAno]) as $d){
                        echo "<option value='todo_".$d["_id"]."'>".dataExtensa($d["_id"])."</option>";
                  }
                  echo "</optgroup>";

                   ?>
                                            
                </select>
            </div>
          </div>
                    <div class="row">
                      <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12"><br>
                         <label class="lead">
                              Total: <span class="numTAlunos quantidadeTotal">0</span>
                          </label>&nbsp;&nbsp;&nbsp;
                           <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
                      </div>
                    </div>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead class="corPrimary">
                                <tr>
                                    <th class="lead text-center">N.º</th>
                                    <th class="lead"><strong>Nome Completo</strong></th>
                                    <th class="lead text-center"><strong>Número Interno</strong></th>
                                    <th class="lead"><strong>Inscrito pelo</strong></th>

                                    <th class="lead text-center"><strong>Classe</strong></th>
                                    <th class="lead text-center"><strong>Data</strong></th>
                                </tr>
                            </thead>
                            <tbody id="tabJaReconfirmados">

                            </tbody>
                        </table>
                    </div>
                  </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
