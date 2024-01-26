<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Cursos", "cursos");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-reader"></i> Cursos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["cursos"], array(), "msg")){

          echo "<script>var listaNomeCursos=".$manipulacaoDados->selectJson("nomecursos", [], ["nomeCurso"=>array('$ne'=>null)], [], "", [], ["nomeCurso"=>1])."</script>";

          echo "<script>var listaCursos=".$manipulacaoDados->selectJson("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"], "", [], ["nomeCurso"=>1])."</script>";

          
           ?>
          
          <div class="card">
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-12 col-md-12">
                      <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novoCurso"><i class="fa fa-plus"></i> Adicionar</button>&nbsp;&nbsp;&nbsp;

                       <label class="lead">Total: <span id="numTCursos" class="quantidadeTotal"></span></label>
                    </div>
                </div>
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><strong>N.º</strong></th>
                          <th class="lead"><strong>Nome do Curso</strong></th>
                          <th class="lead"><strong>Semestre</strong></th>
                          <th class="lead"><strong>Penalização</strong></th>
                          <th class="lead text-center"><strong>Estado</strong></th>

                          <th class="lead text-center"></th>
                      </tr>

                    </thead>
                    <tbody id="tabela">
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



<div class="modal fade" id="formularioCursos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioCursosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-reader"></i> Cursos</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-9 col-md-3 col-lg-offset-3 col-md-offset-3 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">
                      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 lead">
                        Nome do Curso
                        <select class="form-control fa-border" id="idPNomeCurso" required name="idPNomeCurso"></select>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                        Estado
                        <select class="form-control lead" name="estadoCurso" id="estadoCurso">
                          <option value="A">Activo</option>
                          <option value="I">Inactivo</option>
                        </select>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
                        Mod L. E.:
                        <select class="form-control lead" id="modLinguaEst" name="modLinguaEst">
                          <option value="opcional">Opcional</option>
                          <option value="naoOpcional">Não Opcional</option>
                          <option value="lingEsp">L. de Especialidade</option>                          
                        </select>
                      </div>

                      <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 lead">
                        Coordenador:
                        <select class="form-control lead" name="nomeCoordenador" id="nomeCoordenador">
                          <option value="-1" class="lead">Seleccionador</option>
                          <?php foreach ($manipulacaoDados->entidades(["idPEntidade", "nomeEntidade"], "docente") as $prof) {

                            echo "<option value='".$prof["idPEntidade"]."'>".$prof["nomeEntidade"]."</option>";
                          } ?>
                        </select>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4">
                      <label class="lead">Campo Avaliar</label>
                      <select class="form-control lead" id="campoAvaliar" name="campoAvaliar" required>
                        <option value="cfd">Classif. Fin. da Discipl.</option>
                        <option value="mfd">Média. Fin. da Discipl.</option>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-4">
                      <label class="lead">Semestre Activo</label>
                      <select class="form-control lead" id="semestreActivo" name="semestreActivo" required></select>
                    </div>
                    <div class="col-lg-4 col-md-4">
                      <label class="lead">Modo de Penalização</label>
                      <select class="form-control lead" id="modoPenalizacao" name="modoPenalizacao" required>
                        <option value="repetirTodas">Rep. Todas</option>
                        <option value="apenasNegativas">Apenas Negativas</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4">
                      <label class="lead">Para Negativas</label>
                      <select class="form-control lead" id="paraDiscComNegativas" name="paraDiscComNegativas" required>
                        <option value="cadeira">Cadeira de atraso</option>
                        <option value="">Nenhum tratamento</option>
                      </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-3">
                      <label class="lead">Currículo</label>
                      <select class="form-control lead" id="tipoCurriculo" name="tipoCurriculo" required>
                        <option value="curriculo1">Curriculo 1</option>
                        <option value="curriculo2">Curriculo 2</option>
                        <option value="curriculo3">Curriculo 3</option>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="action" id="action">
              </div>


              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Salvar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
