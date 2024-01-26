<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Avaliações por Disciplina", "avaliacoesPorDisciplina");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-info-circle"></i> Avaliações por Disciplina</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "avaliacoesPorDisciplina", array(), "msg")){

          $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->selectUmElemento("anolectivo", "idPAno", ["anos_lectivos.idAnoEscola"=>1, "anos_lectivos.estadoAnoL"=>"V"], ["anos_lectivos"]); 

          $idPNomeCurso = isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:"";
          echo "<script>var idPNomeCurso='".$idPNomeCurso."'</script>";
          echo "<script>var idPAno='".$idPAno."'</script>";

          $array = $manipulacaoDados->selectArray("nomecursos", ["cursos.tipoCurriculo", "curriculo1", "curriculo2", "curriculo3"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "idPNomeCurso"=>$idPNomeCurso], ["cursos"], 1);
          $idEscola = "";
          if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo1")
            $idEscola = valorArray ($array, "curriculo1");
          else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo2")
            $idEscola = valorArray ($array, "curriculo2");
          else if (valorArray($array, "tipoCurriculo", "cursos") == "curriculo3")
            $idEscola = valorArray ($array, "curriculo3");
          
          echo "<script>var listaDisciplinas =".$manipulacaoDados->selectJson("nomedisciplinas", [], ["disciplinas.idDiscCurriculo"=>valorArray($array, "tipoCurriculo", "cursos"), "disciplinas.idDiscCurso"=>$idPNomeCurso], ["disciplinas"], "", [], ["disciplinas.periodoDisciplina"=>1, "disciplinas.classeDisciplina"=>1])."</script>";
           ?>
            <h4 class="text-primary font-weight-bolder text-right">Ref.: <?php echo $manipulacaoDados->selectUmElemento("escolas", "nomeEscola", ["idPEscola"=>$idEscola]) ?></h4>
            <div class="card">              
              <div class="card-body">
                <div class="row">
                  <div class="col-lg-2 col-md-2 lead">
                    <label>Ano:</label>
                    <select class="form-control lead" id="anosLectivos">
                      <?php 
                        foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>1], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){                      
                          echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                        } 
                      ?>
                    </select>
                  </div>
                  <div class="col-lg-3 col-md-3 lead">
                    <label>Curso</label>
                    <select class="form-control lead" id="idPNomeCurso">
                      <?php 
                        foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "abrevCurso"], ["cursos.idCursoEscola"=>$_SESSION["idEscolaLogada"]], ["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
                          echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["abrevCurso"]."</option>";
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead"><strong>Nome da Disciplina</strong></th>
                        <th class="lead"><strong>Período</strong></th>
                        <th class="lead"><strong>Classe</th>
                        <th class="lead"><strong>Tipo</th>
                        <th class="lead"><strong>Continuidade</th>
                        <th class="lead"><strong>Avaliações</th>
                        <th class="lead text-center"></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
          </div><br>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioAvaliacoes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioAvaliacoesForm">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Avaliações</h4>
      </div>

      <div class="modal-body">
        <h3 class="text-danger"><strong>Aplicar para</strong></h3>
        <div class="row">

          <div class="col-lg-3 col-md-3 lead">
            <label>Escola</label>
            <select class="form-control" id="escolaAfectar" name="escolaAfectar">
              <option value="1">Apenas esta</option>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 lead">
            <label>Curso</label>
            <select class="form-control" id="cursoAfectar" name="cursoAfectar">
              <option value="1">Apenas este</option>
              <option value="2">Para este tipo de Curso</option>
              <option value="0">Todos</option>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 lead">
            <label>Classe</label>
            <select class="form-control" id="classeAfectar" name="classeAfectar">
              <option value="1">Apenas esta</option>
            </select>
          </div>
          <div class="col-lg-3 col-md-3 lead">
            <label>Períodos</label>
            <select class="form-control" id="periodoAfectar" name="periodoAfectar">
              <option value="0">Todos</option>
              <option value="1">Apenas este</option>
            </select>
          </div>   
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-4 lead">
            <label>Disciplina</label>
            <select class="form-control" id="disciplinasAfectar" name="disciplinasAfectar">
              <option value="1">Apendas esta</option>
              <option value="0">Todas</option>
            </select>
          </div>
        </div>
        <?php 
          $array = $manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPNomeCurso]);

          foreach(ordenar(listarItensObjecto($array, "periodos"), "ordem ASC") as $periodo){ ?>
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <h4 class="text-primary" style="text-transform:uppercase;"><strong><?php echo $periodo["designacao"]; ?></strong></h4>
                <?php 
                foreach(ordenar($manipulacaoDados->selectArray("campos_avaliacao"), "ordenacao ASC") as $campo){
                  echo "<label><input type='checkbox' id='".$periodo["identificador"]."-".$campo["idCampoAvaliacao"]."' idExtenso='".$campo["designacao1"]."'> ".$campo["designacao1"]."</label>&nbsp;&nbsp;&nbsp;";
                }

                 ?>
              </div>
            </div>
         <?php } ?>

        <input type="hidden" id="idPDisciplina" name="idPDisciplina">
        <input type="hidden" id="idPNomeDisciplina" name="idPNomeDisciplina">
        <input type="hidden" id="conjuntoDados" name="conjuntoDados">
        <input type="hidden" id="conjuntoDadosExt" name="conjuntoDadosExt">
        <input type="hidden" id="idPAno" name="idPAno" value="<?php echo $idPAno; ?>">
        <input type="hidden" id="idPNomeCurso" name="idPNomeCurso" value="<?php echo $idPNomeCurso; ?>">
        <input type="hidden" name="action" id="action" value="actualizarAvaliacoes">
      </div>
      <div class="modal-footer">
          <div class="row">
            <div class="col-lg-4 col-md-4 text-left">
              <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Salvar</button>
            </div>                    
          </div>                
      </div>
    </div>
  </form>
</div>
