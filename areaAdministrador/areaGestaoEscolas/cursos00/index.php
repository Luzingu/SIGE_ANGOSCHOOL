<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Cursos", "cursos00");
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
        <?php  if($verificacaoAcesso->verificarAcesso("", "cursos00", array(), "msg")){

         ?>
          <?php echo "<script>var listaCursos = ".$manipulacaoDados->selectJson("nomecursos", [], ["nomeCurso"=>array('$ne'=>null)], [], "", [], array("ordem"=>1))."</script>";
           ?>

            <div class="card">
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-2 col-md-2">
                      <button type="button" name="" class="lead btn btn-primary novoRegistroFormulario" id="novoCurso"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>

                    <div class="col-md-5 col-lg-5">
                       <label class="lead">Total: <span id="numTCursos" class="quantidadeTotal"></span></label>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"></th>
                        <th class="lead"><strong>Nome</strong></th>
                        <th class="lead"><strong>Abreviação</strong></th>
                        <th class="lead"><strong>1.º Curriculo</strong></th>
                        <th class="lead"><strong>2.º Curriculo</strong></th>
                        <th class="lead"><strong>3.º Curriculo</strong></th>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>



<div class="modal fade" id="formularioCursos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioCursosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-reader"></i> Cursos</h4>
              </div>

              <div class="modal-body">

                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      SubSistema:
                      <select class="form-control lead" required id="idSubSistema" name="idSubSistema">
                        <?php
                        foreach($manipulacaoDados->selectArray("subsistemasDeEnsino", [], ["designacaoSubistema"=>array('$ne'=>null)], [], "", [], array("ordem"=>1)) as $sub){
                          echo "<option value='".$sub["idPSubsistema"]."'>".$sub["categroria"]." (".$sub["designacaoSubistema"].")</option>";
                        }

                         ?>
                      </select>
                    </div>
                    <div class="col-lg-8 col-md-8 lead">
                      Nome do Curso:
                      <input type="text" class="form-control fa-border somenteLetras vazio" id="nomeCurso" title="Nome do Curso" required maxlength="60" name="nomeCurso">
                      <div class="nomeCurso discasPrenchimento lead"></div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      Abreviação
                      <input type="text" class="form-control fa-border somenteLetras vazio" id="abrevCurso" title="Abreviação do Curso" required maxlength="10" name="abrevCurso">
                      <div class="abrevCurso discasPrenchimento lead"></div>
                    </div>
                    <div class="col-lg-6 col-md-6 lead">
                      Área de Formação
                      <input type="text" class="form-control fa-border somenteLetras vazio" id="areaFormacao" title="Área de Formação" required maxlength="60" name="areaFormacao">
                      <div class="areaFormacao discasPrenchimento lead"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      Por Semestre?
                      <select class="form-control lead" id="sePorSemestre" name="sePorSemestre">
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-2 col-md-2 lead">
                      Ordem
                      <input type="number" class="form-control text-center fa-border vazio" id="ordem" name="ordem">
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      Tipo:
                      <select class="form-control lead" id="tipoCurso" name="tipoCurso">
                        <option value="infantil">Infantil</option>
                        <option value="primaria">Primaria</option>
                        <option value="basica">Básica</option>
                        <option value="geral">Geral</option>
                        <option value="tecnico">Técnico</option>
                        <option value="pedagogico">Pedagógico</option>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      Especialidade:
                      <select class="form-control lead" id="especialidadeCurso" name="especialidadeCurso">
                        <option value="infantil">Infantil</option>
                        <option value="primaria">Primaria</option>
                        <option value="basica">Básica</option>
                        <option value="geral">Geral</option>
                        <option value="pedagogico">Pedagógico</option>
                        <option value="saude">Saúde</option>
                        <option value="politecnica">Politécnica</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      1.ª Classe
                      <input type="text" required class="form-control text-center fa-border vazio" id="primeiraClasse" name="primeiraClasse">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      Última Classe
                      <input type="text" required class="form-control text-center fa-border vazio" id="ultimaClasse" name="ultimaClasse">
                    </div>
                    <div class="col-lg-5 col-md-5 lead">
                      Curriculo1
                      <select class="form-control" require name="curriculo1" id="curriculo1">
                        <?php
                          echo "<option value='0'>Todas Escolas</option>";
                          foreach ($manipulacaoDados->selectArray("escolas", ["idPEscola", "abrevNomeEscola2"], [], [], "", [], ["nomeEscola"=>1]) as $a)
                          {
                            echo "<option value='".$a["idPEscola"]."'>".$a["abrevNomeEscola2"]."</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      Curriculo2
                      <select class="form-control" name="curriculo2" id="curriculo2">
                        <?php
                          echo "<option value=''>Nenhuma</option>
                          <option value='0'>Todas Escolas</option>";
                          foreach ($manipulacaoDados->selectArray("escolas", ["idPEscola", "abrevNomeEscola2"],[], [], "", [], ["nomeEscola"=>1]) as $a)
                          {
                            echo "<option value='".$a["idPEscola"]."'>".$a["abrevNomeEscola2"]."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-5 col-md-5 lead">
                      Curriculo1
                      <select class="form-control" name="curriculo3" id="curriculo3">
                        <?php
                          echo "<option value=''>Nenhuma</option>
                          <option value='0'>Todas Escolas</option>";
                          foreach ($manipulacaoDados->selectArray("escolas", ["idPEscola", "abrevNomeEscola2"], [], [], "", [], ["nomeEscola"=>1]) as $a)
                          {
                            echo "<option value='".$a["idPEscola"]."'>".$a["abrevNomeEscola2"]."</option>";
                          }
                        ?>
                      </select>
                    </div>

                  </div>

                  <input type="hidden" name="idPCurso" id="idPCurso" idChave="sim">
                  <input type="hidden" name="action" id="action">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Concluir</button>
                    </div>
                  </div>
              </div>
            </div>
          </form>
      </div>
