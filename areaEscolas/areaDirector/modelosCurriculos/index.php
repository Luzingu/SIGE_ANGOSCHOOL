<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Modelos de Curriculos", "modelosCurriculos");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Modelos de Curriculos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php
          if($verificacaoAcesso->verificarAcesso("",["modelosCurriculos"], array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $curriculo = "";

          $array = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "sePorSemestre", "cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["idPNomeCurso"=>$idCurso, "cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"]);

          $adminCurriculo="yes";
          if(valorArray($array, "curriculo1") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo1";
          else if(valorArray($array, "curriculo2") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo2";
          else if(valorArray($array, "curriculo3") == $_SESSION["idEscolaLogada"])
            $curriculo = "curriculo2";
          else
          {
            $curriculo = valorArray($array, "tipoCurriculo", "cursos");
            $adminCurriculo="not";
          }

          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var curriculo='".$curriculo."'</script>";
          $sePorSemestre = valorArray($array, "sePorSemestre");

          echo "<script>var disciplinas =".$manipulacaoDados->selectJson("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>$curriculo, "disciplinas.classeDisciplina"=>$classe, "disciplinas.idDiscCurso"=>$idCurso], ["disciplinas"], "", [], ["disciplinas.ordenacao"=>1])."</script>";

          $condicaoDisciplina =array();
          if($idCurso!=3){
            $condicaoDisciplina =["idPNomeDisciplina"=>['$nin'=>array(22, 23)]];
          }
          echo "<script>var listaNomeDisciplinas=".$manipulacaoDados->selectJson("nomedisciplinas", [], $condicaoDisciplina, [], "", [], ["nomeDisciplina"=>1])."</script>";
          ?>

          <h4 class="text-primary font-weight-bolder text-right">Modelo: <?php echo strtoupper($curriculo); ?></h4>
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
                            $_SESSION['classesPorCursoPeriodo']=retornarClassesPorCurso($manipulacaoDados, "", "nao");
                          }
                        ?>
                      </select>
                  </div>
                  <div class="col-lg-2 col-md-2 lead">
                    Ano:
                    <select class="form-control lead" id="anosLectivos">
                        <?php
                    foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION['idEscolaLogada']], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){
                      echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                    }
                    echo "<option value=''>Todos Anos</option>";
                  ?>
                    </select>
                  </div>
                  <div class="col-lg-4 col-md-4"><br>
                    <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novaDisciplina"><i class="fa fa-plus"></i> Adicionar</button>
                  </div>
                <?php if(valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")==0 && $adminCurriculo == "yes"){ ?>
                  <div class="col-lg-2 col-md-2">
                    <label>Copiar</label>
                    <select class="form-control lead" id="idCurriculoCopiar">
                    <?php

                      foreach (["curriculo1", "curriculo2", "curriculo3"] as $c) {
                        if ($c != $curriculo)
                        echo "<option value='".$c."'>".strtoupper($c)."</option>";
                      }
                  ?>
                    </select>
                  </div>
                  <div class="col-lg-1 col-md-1 text-right"><br>
                    <button type="button" title="Copiar" class="btn lead btn-primary" id="copiarCuriculo"><i class="fa fa-spinner"></i></button>
                  </div>
                <?php } ?>
              </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>Ordem</strong></th>
                        <th class="lead font-weight-bolder"><strong>Nome da Disciplina</strong></th>
                        <th class="lead"><strong>Tipo de Disciplina</strong></th>
                        <th class="lead"><strong>Semestre</strong></th>
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


  <div class="modal fade" id="formularioDisciplinas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioDisciplinasForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-open"></i> Disciplinas</h4>
              </div>

              <div class="modal-body">
                 <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>



                  <div class="row">
                      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 lead">
                        Nome da Disciplina
                        <select name="idPNomeDisciplina" class="form-control fa-border somenteLetras vazio lead" id="idPNomeDisciplina" required>

                        </select>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                        Ordenação
                            <input type="number" min="0" required="" name="ordemDisciplina" class="form-control lead text-center vazio" id="ordemDisciplina">
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
                        Classe
                            <input type="text" name="classeDisciplina" class="form-control lead" title="<?php echo $classe; ?>" id="classeDisciplina" value="<?php echo classeExtensa($manipulacaoDados, $idCurso, $classe); ?>" readonly>
                      </div>
                      <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 lead">
                        Componente
                            <select class="form-control lead" id="tipoDisciplina" name="tipoDisciplina" required="">
                              <?php

                              $tipoCurso = $manipulacaoDados->selectUmElemento("nomecursos", "tipoCurso", ["idPNomeCurso"=>$idCurso]);
                              if($classe<=9){
                                echo '<option value="FG">Formação Geral</option>';
                              }else if($tipoCurso=="geral"){
                                echo '<option value="FG">Formação Geral</option>
                                <option value="FE">Formação Específica</option>
                                <option value="Op">Opção</option>';
                              }else if($tipoCurso=="pedagogico"){
                                echo ' <option value="FG">Formação Geral</option>
                                <option value="FP">Formação Profissional</option>
                                <option value="FE">Formação Específica</option>';
                              }else if($tipoCurso=="tecnico"){
                                echo '<option value="CSC">Componente Sócio Cultural</option>
                                <option value="CC">Componente Científica</option>
                                <option value="CTTP">Componente Técn., Tecnol. e Prát.</option>';
                              }

                              ?>

                            </select>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                        Semestre:
                        <select class="form-control lead" id="semestreDisciplina" name="semestreDisciplina">
                          <option value="I">I</option>
                          <?php if($sePorSemestre=="sim"){ ?>
                            <option value="II">II</option>
                          <?php } ?>
                        </select>
                      </div>
                      <?php if (valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade", "escola") ==0 )
                      { ?>
                      <div class="col-lg-9 col-md-9 lead"><br>
                        <label for="seAdicionarEmTodasEscolas"><input type="checkbox" name="seAdicionarEmTodasEscolas" id="seAdicionarEmTodasEscolas"> Adicionar em todoas Escolas</label>
                      </div>
                    <?php } ?>
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

                  <input type="hidden" name="action" id="action">
                  <input type="hidden" name="classeDisciplinaOriginal" id="classeDisciplinaOriginal" value="<?php echo $classe; ?>">
                  <input type="hidden" name="idPCurso" id="idPCursoForm" value="<?php echo $idCurso ?>">
                  <input type="hidden" name="curriculo" id="curriculo" value="<?php echo $curriculo; ?>">
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
