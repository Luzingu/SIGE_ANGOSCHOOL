<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Disciplinas", "disciplinas00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-open"></i> Disciplinas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("", "disciplinas00", array(), "msg")){


              echo "<script>var disciplinas =".$manipulacaoDados->selectJson("nomedisciplinas", [], ["idPNomeDisciplina"=>array('$ne'=>null)], [], "", [], array("nomeDisciplina"=>1))."</script>";
           ?>

  <div class="card">              
    <div class="card-body">
        <div class="row">
             <div class="col-lg-2 col-md-2">
                <button type="button" name="" class="btn lead btn-primary novoRegistroFormulario" id="novaDisciplina"><i class="fa fa-plus"></i> Adicionar</button>
              </div>

              <div class="col-md-5 col-lg-5">
                 <label class="lead">Total: <span id="numTDisciplinas" class="quantidadeTotal"></span></label>
              </div>
          </div> 
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="lead text-center"><strong>Nº</strong></th>
                    <th class="lead font-weight-bolder"><strong>Nome da Disciplina</strong></th>
                    <th class="lead font-weight-bolder"><strong>Abrev. da Disciplina1</strong></th>
                    <th class="lead font-weight-bolder"><strong>Abrev. da Disciplina2</strong></th>
                    <th class="lead"><strong>Nível</strong></th>                    
                    <th class="lead text-center" style="min-width: 130px;"></th>
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
                      <div class="col-lg-8 col-md-8 lead">
                        Nome da Disciplina:
                        <input type="text" name="nomeDisciplina" class="form-control fa-border somenteLetras vazio lead" id="nomeDisciplina" required>
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
                        Nível da Disciplina:
                        <select class="form-control lead" name="nivelDisciplina" id="nivelDisciplina">
                          <option value="primaria">Primária</option>
                          <option value="basica">Iº Ciclo</option>
                          <option value="media">IIº Ciclo</option>
                          <option value="primBasico">Primária e Iº Ciclo</option>
                          <option value="primMedio">Primária e IIº Ciclo</option>
                          <option value="basicoMedio">Iº e IIº Ciclo</option>
                          <option value="primMedio">Primária e IIº Ciclo</option>
                          <option value="complexo">Primária, Iº e IIº Ciclo</option>
                        </select>
                     </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-6 col-md-6 lead">
                        Abrev. da Disciplina1:
                        <input type="text" name="abreviacaoDisciplina1" class="form-control fa-border somenteLetras vazio lead" id="abreviacaoDisciplina1" maxlength="50">
                      </div>
                      <div class="col-lg-6 col-md-6 lead">
                        Abrev. da Disciplina2:
                        <input type="text" name="abreviacaoDisciplina2" class="form-control fa-border somenteLetras vazio lead" id="abreviacaoDisciplina2" maxlength="20">
                      </div>
                  </div>
                  <input type="hidden" name="idPDisciplina" idChave="sim">
                  <input type="hidden" name="action" id="action">

              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-user-plus"></i> Cadastrar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>