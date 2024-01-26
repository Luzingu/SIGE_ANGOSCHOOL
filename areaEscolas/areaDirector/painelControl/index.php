<?php session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Painel de Control", "painelControl");
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
           <div class="row" >
      <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

          <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                <b class="caret"></b>
                            </a>
          <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-cog"></i> Painel de Control</strong></h1>
      </nav>

    </div>
    <div class="main-body">
        <?php
        if($verificacaoAcesso->verificarAcesso("", ["painelControl"], array(), "msg")){

              $vetor[] = "inscricao";
              $vetor[] = "exibirAssinaturas";
              $vetor[] = "verfComparticipacoes";
              $vetor[] = "divTurmas";
              $vetor[] = "editDadosProfessores";
              $vetor[] = "permitirAlterarNotas";
              $vetor[] = "altTransicaoAlunos";
              $vetor[] = "inscriverCadeirantes";
              $vetor[] = "marcaAgua";

               for($i=0; $i<=count($vetor)-1; $i++){

                  $manipulacaoDados->inserirObjecto("escolas", "estadoperiodico", "idPEstado", "idEstadoEscola, objecto, estado, chaveEstado", [$_SESSION["idEscolaLogada"], $vetor[$i], "F", $_SESSION["idEscolaLogada"]."-".$vetor[$i]], ["idPEscola"=>$_SESSION['idEscolaLogada']]);

               }
            echo "<script>var estadoPeriodico=".json_encode($manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["estadoperiodico"]))."</script>";

          ?>
          <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="tab-content" style="padding-top: 15px;">
                <div class="tab-pane fade in active" id="dirigentes">
                      <div class="col-lg-12 col-md-12">


                      <div class="col-lg-5 col-md-5">

                           <div class="panel panel-info">
                              <div class="panel-heading lead">
                                 Estado de Alteração
                              </div>
                              <div class="panel-body">

                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Inscrição:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="inscricao" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>

                                <!--<div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Matriculas:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="matricula" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Reconfirmação:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="reconfirmacao" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>!-->
                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Edit/Dados/Profs:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="editDadosProfessores" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Alt. Notas/Sistema:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="permitirAlterarNotas" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Marca de Água:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="marcaAgua" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>


                              </div>
                          </div>
                        </div>

                        <div class="col-lg-4 col-md-4">

                           <div class="panel panel-info">
                              <div class="panel-heading lead">
                                 Estado de Alteração
                              </div>

                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Assinaturas:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="exibirAssinaturas" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Ver. Compart:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="verfComparticipacoes" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Div. Turmas:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="divTurmas" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Alt Trans. Alunos:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="altTransicaoAlunos" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right">Insc. Cadeirantes:</div>
                                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="switch">
                                      <label class="lead">
                                          OFF<input type='checkbox' style="margin-left: -15px;" id="inscriverCadeirantes" class="altEstado">
                                          <span class="lever"></span>ON
                                      </label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          </div>
                        </div>



                </div>
            </div>
        </div>
    </div>

    <?php
    $transicoesClasses = listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "trans_classes", ["idTransClAno=".$manipulacaoDados->idAnoActual]);

    foreach($manipulacaoDados->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"],["cursos"], "", [], ["nomeCurso"=>1]) as $curso){
            ?>

                    <div class="col-lg-4 col-md-4">
                     <div class="panel panel-info">
                        <div class="panel-heading lead">
                           <strong><?php echo $curso["abrevCurso"]; ?></strong>
                        </div>
                        <div class="panel-body">
                          <?php foreach(listarItensObjecto($curso, "classes") as $classe) {
                              $prop="";
                              foreach ($transicoesClasses as $transicao) {

                                  if(nelson($transicao, "idTransClCurso")==$curso["idPNomeCurso"] && $transicao["classeTrans"]==$classe["identificador"]){
                                    $prop="checked";
                                  }
                              }

                            ?>


                           <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 lead text-right"><?php echo $classe["designacao"]; ?></div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                              <div class="switch">
                                <label class="lead">
                                    OFF<input type='checkbox' style="margin-left: -15px;" classe="<?php echo $classe["identificador"]; ?>" idPNomeCurso="<?php echo $curso["idPNomeCurso"]; ?>" class="tranistarAno" <?php echo $prop; ?>>
                                    <span class="lever"></span>ON
                                </label>
                              </div>
                            </div>
                          </div>
                         <?php } ?>

                        </div>
                    </div>
                  </div>
          <?php } ?>

          <div class="row">
            <div class="col-lg-3 col-md-3col-sm-12">
              <a href="#" class="lead" style="padding: 10px; border-radius: 10px; text-decoration: none; outline:0; background-color:  #428bca; color: white;" id="btnAdicionarAnoLectivo"><i class="fa fa-plus"></i> Novo Ano Lectivo</a>
            </div>
            </div><br/><br>



        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="confirmarSenhaDirector" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false" style="display: none;">
        <form class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-key"></i> Confirmar Senha do Director</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="password" name="" class="form-control fa-border caixaSenha somenteLetras vazio" id="txtConfirmarSenharDirector" value="" required placeholder="Senha do Director">
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-7 col-xs-7">
                      <button type="submit" class="btn btn-primary col-lg-12 lead btn-lg"><i class="fa fa-check"></i> Confirmar</button>
                    </div>
                  </div>
              </div>
            </div>
          </form>
    </div>
