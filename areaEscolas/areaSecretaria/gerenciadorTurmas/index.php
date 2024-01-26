<?php session_start();
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Gerenciador de Turmas", "gerenciadorTurmas");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
     $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
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
          <div class="row" >
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <strong class="caret"></strong>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-dungeon"></i> <strong>Gerenciador de Turmas</strong></h1>
              
            </nav>
          </div>
        </div>
          <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "gerenciadorTurmas", array(), "msg")){ 

            $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
            echo "<script>var idPAno='".$idPAno."'</script>";
            $disable="";
            if($idPAno!=$manipulacaoDados->idAnoActual){
              $disable="disabled";
            }

            $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
            echo "<script>var luzingu='".$luzingu."'</script>";
            $luzingu = explode("-", $luzingu);
            $idCurso = isset($luzingu[2])?$luzingu[2]:"";
            $classe = isset($luzingu[1])?$luzingu[1]:"";
            $periodo = isset($luzingu[0])?$luzingu[0]:""; 

            echo "<script>var periodo='".$periodo."'</script>";
            echo "<script>var classeP='".$classe."'</script>";
            echo "<script>var idCursoP='".$idCurso."'</script>";

            $array = $manipulacaoDados->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idListaAno"=>$idPAno, "classe"=>$classe, "idPNomeCurso"=>$idCurso]);
            $array = $manipulacaoDados->anexarTabela($array, "entidadesprimaria", "idPEntidade", "idCoordenadorTurma");

            echo "<script>var gerenciadorTurmas = ".json_encode($array)."</script>";


            $disciplinasOpcao=array();
            foreach($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9, 20, 21)] ]) as $a){
              $disciplinasOpcao[]=array("idPNomeDisciplina"=>$a["_id"], "nomeDisciplina"=>$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$a["_id"]]));
            }
            echo "<script>var nomedisciplinas = ".json_encode($disciplinasOpcao)."</script>";
          ?> 

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
            <div class="col-lg-2 col-md-2 lead">
              Classe:
              <select class="form-control lead" id="luzingu">
                <?php 
                  if(isset($_SESSION['classesPorCurso'])){
                    echo $_SESSION['classesPorCurso'];
                  }else{
                    $_SESSION['classesPorCurso']=retornarClassesPorCurso($manipulacaoDados, "", "nao");
                  }
                  ?>             
              </select>
            </div>

            <div class="col-md-6 col-lg-6"><br>
              <?php if(seEnsinoSecundario() || seEnsinoBasico()){ ?>
              <a href="../../relatoriosPdf/listaTurmas.php?idPAno=<?php echo $idPAno; ?>" class="btn btn-primary lead"><i class="fa fa-print"></i> Lista de Turmas</a>
              <?php } ?>&nbsp;&nbsp;&nbsp;
             <label class="lead">Número de Turmas: <span id="numTTurmas" class="quantidadeTotal">0</span></label>
          </div>              
        </div>

        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead class="corPrimary">
                <tr>
                    <th class="lead font-weight-bolder text-center"><strong>Turma</strong></th>
                    <th class="lead text-center"><strong>Sala Nº</strong></th>
                    <th class="lead"><strong>Regime</strong></th>
                    <th class="lead"><strong>Período</strong></th>
                    <th class="lead paraCoordenador"><strong>Director</strong></th>
                    <th class="lead paraCoordenador"><strong>Opção</strong></th>
                    <th class="lead text-center"></th>
                </tr>
            </thead>
            <tbody id="tabTurmas">

              
            </tbody>
        </table>
      </div><br>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>



 <div class="modal fade" id="formularioGerTurmas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioGerTurmasF">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-dungeon"></i> Gerenciador de Turma</h4>
        </div>

        <div class="modal-body">
            <div class="row" id="discasPrenchimento">
                <div class="col-lg-12 lead"></div>
            </div>

            <div class="row">
                <div class="col-lg-2 col-md-2 lead">
                  <label class="lead">Turma:</label>
                    <input type="text" name="nomeTurma1" disabled class="form-control text-center" id="nomeTurma1" class="nomeTurma1 text-center">
                </div>
                <div class="col-lg-4 col-md-4 lead">
                  <label class="lead">Designação:</label>
                    <input type="text" class="form-control text-center" id="designacaoTurma" name="designacaoTurma" autocomplete="off" required <?php echo $disable; ?>>
                </div>
                <div class="col-lg-2 col-md-2 lead">
                  <label class="lead">Sala N.º:</label>
                    <input type="number" class="form-control text-center" id="numeroTurma" min="0" name="numeroTurma" required <?php echo $disable; ?>>
                </div>
                <div class="col-lg-4 col-md-4 lead">
                  <label class="lead">Período:</label>
                      <select id="periodoT" name="periodoT" class="form-control lead" <?php echo $disable; ?>>
                      </select>
                </div>
                
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 lead">
                <label class="lead">Director(a) da Turma:</label>
                  <select id="listaProfessor" class="form-control lead" name="listaProfessor" <?php echo $disable; ?>>
                    <option value="-1" class="lead">Seleccionar</option>
                  <?php foreach($manipulacaoDados->entidades() as $a){
                    echo "<option value='".$a["idPEntidade"]."'>".$a["nomeEntidade"]."</option>";
                  } ?> 

                  </select>
              </div>
              <div class="col-lg-5 col-md-5 lead">
                  <label class="lead">N.º de Pauta (Livro):</label>
                    <input type="text" class="form-control text-center" id="numeroPauta" name="numeroPauta" required>
                </div>              
            </div>

            <input type="hidden" name="idPCurso" id="idPCurso" value="<?php echo $idCurso ?>">
            <input type="hidden" name="classe" id="classe" value="<?php echo $classe; ?>">
            <input type="hidden" name="idPAno" id="idPAno" value="<?php echo $idPAno; ?>">
            <input type="hidden" name="idPListaTurma" id="idPListaTurma">
            <input type="hidden" name="action" value="manipularGerenciadorTurma">

        </div>
        <div class="modal-footer">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 text-left">
                <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Alterar</button>

              </div>                    
            </div>                
        </div>
      </div>
    </form>
</div>
