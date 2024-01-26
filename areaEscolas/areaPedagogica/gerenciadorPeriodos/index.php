<?php session_start(); 

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Gerenciador de Períodos", "gerenciadorPeriodos");
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-clock"></i> Gerenciador de Períodos</strong></h1>                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "gerenciadorPeriodos", array(), "msg")){

          $array = $manipulacaoDados->selectArray("escolas", ["gerencPerido.idPGerPeriodo", "gerencPerido.horaEntrada", "gerencPerido.periodoGerenciador", "gerencPerido.duracaoPorTempo", "gerencPerido.intevaloDepoisDoTempo", "gerencPerido.duracaoIntervalo", "gerencPerido.numeroTempos", "gerencPerido.numeroDias", "gerencPerido.idCoordernadorPeriodo", "gerencPerido.idGerPerEscola", "gerencPerido.idGerPerAno", "gerencPerido.chaveGerPerido"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "gerencPerido.idGerPerAno"=>$manipulacaoDados->idAnoActual], ["gerencPerido"]);
          $array = $manipulacaoDados->anexarTabela2($array, "entidadesprimaria", "gerencPerido", "idPEntidade", "idCoordernadorPeriodo");

          echo "<script>var gerenciadorPeriodo =".json_encode($array)."</script>";
          
  ?>

        <div class="row">
          <div class="col-lg-10 col-md-10 lead"><a href="#" class="lead btn-primary btn" id="actualizar"><i class="fa fa-refresh"></i></a></span></div>          
        </div>
        
       <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead class="corPrimary">
                <tr>
                    <th class="lead bolder"><strong>Período</strong></th>
                    <th class="lead bolder text-center"><strong>Entrada</strong></th>
                    <th class="lead bolder text-center"><strong>Carga Semanal</strong></th>                    
                    <th class="lead bolder text-center"><strong>Duração/Tempo</strong></th>
                    <th class="lead bolder text-center"><strong>Intervalo</strong></th>
                    <th class="lead bolder"><strong>Coordenador</strong></th>
                    <th class="lead bolder"></th>
                </tr>
            </thead>
            <tbody id="tabGerenciador">
                
            </tbody>
        </table>
      </div> 

      <div class="row" id="paraPaginacao" style="margin-top: -30px;">
        <div class="col-md-12 col-lg-12 coluna">
            <div class="form-group paginacao">
                  
            </div>
          </div>
        </div>      

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioPeriodos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioPeriodosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-clock"></i> Gerenciador de Períodos</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <label class="lead" for="periodoGerenciador">Período</label>
                        <input type="text" name="periodoGerenciador" id="periodoGerenciador" class="form-control lead" disabled="">
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label class="lead" for="horaEntrada">Hora da Entrada</label>
                        <input type="text" name="horaEntrada" id="horaEntrada" class="form-control text-center lead" min="0">
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label class="lead" for="duracaoPorTempo">Duração/Tempo:</label>
                        <input type="number" name="duracaoPorTempo" id="duracaoPorTempo" class="form-control text-center lead" min="0" placeholder="Minutos" required="">
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label class="lead" for="duracaoIntervalo">Aulas até:</label>
                        <select class="form-control lead" name="numeroDias" id="numeroDias">
                          <option value="2">Terça-Feira</option>
                          <option value="3">Quarta-Feira</option>
                          <option value="4">Quinta-Feira</option>
                          <option value="5">Sexta-Feira</option>
                          <option value="6">Sábado</option>
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label class="lead" for="duracaoIntervalo">Nº de Tempos</label>
                        <select class="form-control lead" name="numeroTempos" id="numeroTempos">
                          <option value="3">3 Tempos</option>
                          <option value="4">4 Tempos</option>
                          <option value="5">5 Tempos</option>
                          <option value="6">6 Tempos</option>
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label class="lead" for="intevaloDepoisDoTempo">Intervalo Após:</label>
                        <select type="number" name="intevaloDepoisDoTempo" id="intevaloDepoisDoTempo" class="form-control lead" min="0" required="">
                          <option value="2">2º Tempo</option>
                          <option value="3">3º Tempo</option>
                          <option value="4">4º Tempo</option>
                        </select>
                      </div>
                      
                      
                  </div>

                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label class="lead" for="duracaoIntervalo">Duração do Intervalo:</label>
                        <input type="number" name="duracaoIntervalo" id="duracaoIntervalo" placeholder="Minutos" class="form-control text-center lead" min="0" required="">
                      </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                      <label class="lead">Coodernador do Turno:</label>
                      <select class="form-control lead" name="idCoordernadorPeriodo" id="idCoordernadorPeriodo">
                        <option value=''>Nenhum Professor</option>
                        <?php
                          foreach ($manipulacaoDados->entidades(["idPEntidade", "nomeEntidade"], "docente") as $prof) {

                            echo "<option value='".$prof["idPEntidade"]."'>".$prof["nomeEntidade"]."</option>";
                          }

                         ?>
                      </select>
                    </div>
                  </div>

                  <input type="hidden" name="idPGerPeriodo" id="idPGerPeriodo" idChave="sim">
                  <input type="hidden" name="action" id="action" value="alterarGerenciarPeriodo">
                  <input type="hidden" name="idPAno" id="idPAno" value="<?php echo $idPAno; ?>">
              </div>


              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success btn lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
