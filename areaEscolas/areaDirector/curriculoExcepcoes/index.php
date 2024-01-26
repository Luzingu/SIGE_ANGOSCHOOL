<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Curriculo Excepcoes", "curriculoExcepcoes");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Excepcoes</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("",["curriculoExcepcoes"], array(), "msg")){

            $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);

            echo "<script>var luzingu='".$luzingu."'</script>";
            $luzingu = explode("-", $luzingu);

          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          $array = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "sePorSemestre", "cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3", "cursos.curriculoEscola"], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);
          
          $adminCurriculo="yes";
          if(valorArray($array, "curriculo1") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo1";
          else if(valorArray($array, "curriculo2") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo2";
          else if(valorArray($array, "curriculo3") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo2";
          else
            $curriculo = valorArray($array, "tipoCurriculo", "cursos");

          $sePorSemestre = valorArray($array, "sePorSemestre");

          $listaExcepcoes = $manipulacaoDados->selectArray("excepcoes_curriculares", [], ["idDiscEscola"=>$_SESSION['idEscolaLogada'], "classeDisciplina"=>$classe, "periodoDisciplina"=>$periodo, "idDiscCurso"=>$idCurso]);
          $listaExcepcoes = $manipulacaoDados->anexarTabela($listaExcepcoes, "nomedisciplinas", "idPNomeDisciplina", "idPNomeDisciplina");

          echo "<script>var listaExcepcoes =".json_encode($listaExcepcoes)."</script>";

          echo "<script>var listaNomeDisciplinas=".$manipulacaoDados->selectJson("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>$curriculo, "disciplinas.classeDisciplina"=>$classe, "disciplinas.periodoDisciplina"=>$periodo, "disciplinas.idDiscCurso"=>$idCurso], ["disciplinas"], "", [], ["disciplinas.ordenacao"=>1])."</script>";
          ?>
          
          <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-3 col-md-3 lead">
                      Classe:
                      <select class="form-control lead" id="luzingu">
                        <?php 
                          if(isset($_SESSION['classesPorCursoPeriodo'])){
                            echo $_SESSION['classesPorCursoPeriodo'];
                          }else{
                            $_SESSION['classesPorCursoPeriodo']=retornarClassesPorCurso($manipulacaoDados, "A");
                          }
                        ?>                         
                      </select>
                  </div>
                  <div class="col-lg-2 col-md-2 lead">
                    Ano:
                    <select class="form-control lead" id="anosLectivos">
                        <?php 
                    echo "<option value=''>Todos Anos</option>";
                    foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada']], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){                      
                      echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                    }
                  ?>
                    </select>
                  </div>
                  <div class="col-lg-4 col-md-4"><br>
                    <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novaExcepcao"><i class="fa fa-plus-circle"></i> Nova</button>
                  </div>
              </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>N.º</strong></th>
                        <th class="lead font-weight-bolder"><strong>Nome da Disciplina</strong></th>
                        <th class="lead text-center"><strong>Anos Lectivos</strong></th>
                        <th class="lead text-center" style="min-width: 130px;"></th>
                      </tr>
                  </thead>
                  <tbody id="tabela">
                      
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->formTrocarSenha(); ?>


  <div class="modal fade" id="formularioExcepcoes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioExcepcoesForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-open"></i> Excepcoes</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-9 col-md-9 lead">
                        Nome da Disciplina
                        <select name="idPNomeDisciplina" class="form-control fa-border somenteLetras vazio lead" id="idPNomeDisciplina" required>

                        </select>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                        Semestre:
                        <select class="form-control lead" id="semestreDisciplina" name="semestreDisciplina">
                          <option value="I">I</option>
                          <?php if($sePorSemestre=="sim"){ ?>
                            <option value="II">II</option>
                          <?php } ?>
                        </select>
                      </div>
                  </div>

                  
                  <div class="row">
                    <fieldset style="border:solid rgba(0, 0, 0, 0.3) 1px; padding:4px;" id="paraAnosLectivos">
                      <legend style="margin-bottom:0px;"><strong>Anos Lectivos</strong></legend>
                      <?php 
                      foreach($manipulacaoDados->selectArray("anolectivo", ["idPAno", "numAno"], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"]], ["anos_lectivos"]) as $a){
                        echo "<label><input type='checkbox' id='".$a["idPAno"]."'> ".$a["numAno"]."</label>&nbsp;&nbsp;&nbsp;";
                      }

                       ?>
                    </fieldset>
                  </div>
                   <input type="hidden" name="periodoDisciplina" name="periodoDisciplina" value="<?php echo $periodo; ?>">
                  <input type="hidden" name="action" id="action">
                  <input type="hidden" name="classeDisciplinaOriginal" id="classeDisciplinaOriginal" value="<?php echo $classe; ?>">
                  <input type="hidden" name="idPCurso" id="idPCursoForm" value="<?php echo $idCurso ?>">
                  <input type="hidden" name="idPExcepcao" id="idPExcepcao">
                  <input type="hidden" name="anosLectivos" id="anosLectivos">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Salvar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>