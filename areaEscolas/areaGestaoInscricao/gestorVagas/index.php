<?php session_start(); 

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/areaGestaoInscricao/funcoesGestaoInscricao.php';

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Gestor de Vagas", "gestorVagas");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    inicializadorDaFuncaoGestaInscricao($manipulacaoDados);
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-reader"></i> Gestor de Vagas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "gestorVagas", array(), "msg")){

          $manipulacaoDados->conDb("inscricao");

          echo "<script>var gestorvagas=".json_encode($manipulacaoDados->selectArray("gestorvagas", [], ["idGestEscola"=>$_SESSION["idEscolaLogada"], "idGestAno"=>$manipulacaoDados->idAnoActual]))."</script>";

         ?>
          <div class="row">
            <div class="col-md-12 text-right">
              
              <a href="../../relatoriosPdf/relatoriosInscricao/estatisticaIscritos.php" class="btn btn-primary lead"><i class="fa fa-file"></i> Estatística</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <a href="#" id="actualizar"><i class="fa fa-refresh fa-2x"></i></a>
            </div>
          </div>
    
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center" rowspan="2" style="vertical-align: middle;"><strong>Curso</strong></th>
                          <th class="lead text-center" colspan="3"><strong>Vagas</strong></th>
                          <th class="lead text-center" rowspan="2" style="vertical-align: middle;"><strong>Critério</strong></th>
                          <th class="lead text-center" rowspan="2" style="vertical-align: middle;"><strong>Períodos</strong></th>
                          <th class="lead text-center" rowspan="2" style="vertical-align: middle;"><strong>Nº de Provas</strong></th>
                          <th class="lead text-center" rowspan="2" style="vertical-align: middle;"><strong>Estado</strong></th>
                          
                          <th class="lead text-center" rowspan="2"><strong></strong></th>
                      </tr>
                      <tr>                          
                          <th class="lead text-center"><strong>Regular<strong></th>
                          <th class="lead text-center"><strong>Pós-Laboral</strong></th>
                          <th class="lead text-center"><strong>Total</strong></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
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

<div class="modal fade" id="gestorvagas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="gestorvagasForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-dungeon"></i> Gestor de Vagas</h4>
              </div>

              <div class="modal-body">

                  <div class="row">
                    <div class="col-md-12"><label id="msgErro" class="text-danger lead"></label></div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead text-center">
                      <label class="lead" for="numeroVagasReg">Nº Vagas no Reg.:</label>
                      <input type="number" class="form-control lead text-center" id="numeroVagasReg" name="numeroVagasReg" min="0" required=""></div>

                    <?php 
                      $manipulacaoDados->conDb();
                    if(valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas")=="regPos"){ ?>
                      <div class="col-lg-4 col-md-4 lead">
                         <label class="lead" for="numeroVagasPosLab">Nº Vagas no Pós-Lab.:</label>
                        <input type="number" class="form-control lead text-center" id="numeroVagasPosLab" name="numeroVagasPosLab" required="" min="0">
                      </div>
                    <?php } ?>
                      <div class="col-lg-4 col-md-4 lead">
                        <label for="criterioTeste">Critério de Teste:</label>
                        <select class="form-control lead" id="criterioTeste" name="criterioTeste">
                            <option value="exameAptidao">Exame de Aptidão</option>
                            <option value="factor">Por Factores</option>
                            <option value="criterio">Por Critérios</option>
                        </select>                        
                      </div>
                    <input type="hidden" name="idPGestor" id="idPGestor">
                    <input type="hidden" name="action" id="action" value="alterarGestao">            
                  </div>
                  <div class="row">
                    <div class="col-md-6 col-lg-6">
                      <label class="lead" for="periodoCurso">Períodos:</label>
                      <select class="form-control lead" name="periodoCurso" id="periodoCurso">
                        <option value="reg">Somente Regular</option>
                        <?php if(valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas")=="regPos"){ ?>
                        <option value="pos">Somente Pós-Laboral</option>
                        <option value="regPos">Regular e Pós-Laboral</option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-6 col-lg-6">
                      <label class="lead" for="criterioEscolhaPeriodo" >Escolha do Período:</label>
                      <select class="form-control lead" name="criterioEscolhaPeriodo" id="criterioEscolhaPeriodo">
                        <option value="auto">Automático</option>
                        <option value="opcional">Opcional</option>
                        <option value="16">(>16) Pós-Laboral</option>
                        <option value="17">(>17) Pós-Laboral</option>
                        <option value="18">(>18) Pós-Laboral</option>
                        <option value="19">(>19) Pós-Laboral</option>
                        <option value="20">(>20) Pós-Laboral</option>
                        <option value="21">(>21) Pós-Laboral</option>
                        <option value="22">(>22) Pós-Laboral</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead text-center numeroProvas">
                      <label class="lead" for="numeroProvas">Nº de Provas:</label>
                      <select class="form-control lead text-center" id="numeroProvas" name="numeroProvas">
                        <option value="1">1 Prova</option>
                        <option value="2">2 Provas</option>
                        <option value="3">3 Provas</option>
                      </select></div>
                  </div> 

                  <div class="row">
                    <div class="col-md-12" >
                      <fieldset style="border-radius: 20px; border: solid rgba(0,0,0,0.2) 1px; padding: 15px;" id="precedencia1">
                        <legend style="width: 270px;"><strong>Procedência de Avaliação</strong></legend>
                        <div class="row" style="margin-top: -20px;">
                          <div class="col-md-4"><label class="lead">1)<br/>Exame de Apt.</label></div>
                        <div class="col-md-4 lead"><strong>2)</strong>
                          <select class="form-control" id="procFactor2" name="procFactor2">
                              <option value="dataNascAluno">Idade</option>
                              <option value="sexoAluno">Genero</option>
                              <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                              <option value="alunosEmRegime">Alunos em Regime</option>
                          </select>
                        </div>
                        <div class="col-md-4 lead"><strong>3)</strong>
                          <select class="form-control" id="procFactor3" name="procFactor3">
                              <option value="dataNascAluno">Idade</option>
                              <option value="sexoAluno">Genero</option>
                              <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                              <option value="alunosEmRegime">Alunos em Regime</option>
                          </select>
                        </div>
                        </div>

                        <div class="row">
                          <div class="col-md-4 lead"><strong>4)</strong>
                            <select class="form-control" id="procFactor4" name="procFactor4">
                                <option value="dataNascAluno">Idade</option>
                                <option value="sexoAluno">Genero</option>
                                <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                                <option value="alunosEmRegime">Alunos em Regime</option>
                            </select>
                          </div>
                          <div class="col-lg-4 col-md-4 lead">
                            <strong>Tipo de Autentic.</strong>
                            <select class="form-control" name="tipoAutenticacao" id="tipoAutenticacao">
                              <option value="codigo">Código do Aluno</option>
                              <option value="nome">Nome do Aluno</option>
                            </select>
                          </div>
                          <div class="col-lg-4 col-md-4 lead">
                            <label><input type="checkbox" id="seAvaliarApenasMF" name="seAvaliarApenasMF"> Avaliar apenas a média final</label>
                          </div>
                        </div>

                      </fieldset>

                       <fieldset style="border-radius: 20px; border: solid rgba(0,0,0,0.2) 1px; padding: 15px;" id="precedencia2">
                        <legend style="width: 140px;"><strong> Critérios</strong></legend>
                        <div class="row" style="margin-top: -20px;">
                          <div class="col-md-4 lead"><strong>1)</strong>
                            <select class="form-control" id="avalFactor1" name="avalFactor1">
                                <option value="dataNascAluno">Idade</option>
                                <option value="sexoAluno">Genero</option>
                                <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                                <option value="alunosEmRegime">Alunos em Regime</option>
                            </select>
                          </div>
                        <div class="col-md-4 lead"><strong>2)</strong>
                          <select class="form-control" id="avalFactor2" name="avalFactor2">
                              <option value="dataNascAluno">Idade</option>
                              <option value="sexoAluno">Genero</option>
                              <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                              <option value="alunosEmRegime">Alunos em Regime</option>
                          </select>
                        </div>
                        <div class="col-md-4 lead"><strong>3)</strong>
                          <select class="form-control" id="avalFactor3" name="avalFactor3">
                              <option value="dataNascAluno">Idade</option>
                              <option value="sexoAluno">Genero</option>
                              <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                              <option value="alunosEmRegime">Alunos em Regime</option>
                          </select>
                        </div>
                        </div>

                        <div class="row">
                          <div class="col-md-4 lead"><strong>4)</strong>
                            <select class="form-control" id="avalFactor4" name="avalFactor4">
                                <option value="dataNascAluno">Idade</option>
                                <option value="sexoAluno">Genero</option>
                                <option value="mediaDiscNuclear">Média das Disciplinas Nucleares</option>
                                <option value="alunosEmRegime">Alunos em Regime</option>
                            </select>
                          </div>
                        </div>

                      </fieldset>

                      <fieldset style="border-radius: 20px; border: solid rgba(0,0,0,0.2) 1px; padding: 15px;" id="precedencia3">
                        <legend style="width: 140px;"><strong>Factores</strong></legend>
                        <div class="row" style="margin-top: -20px;">
                          
                        <div class="col-md-5 lead text-center">
                          <strong>Idade (%)</strong>
                          <input type="number" class="form-control lead text-center" value="0" step="0.1" id="percIdade" name="percIdade"> 
                        </div>

                        <div class="col-md-7 lead text-center">
                          <strong>Média das Disciplinas Nucleares (%)</strong>
                          <input type="number" class="form-control lead text-center" value="0" step="0.1" min="0" max="100" id="perMedDiscNucleares" name="perMedDiscNucleares">
                        </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 lead text-center">
                            <strong>Genero (%)</strong>
                            <input type="number" class="form-control lead text-center" value="0" step="0.1" min="0" max="100" id="percGenero" name="percGenero">
                          </div>
                          <div class="col-md-6 lead text-center">
                            <strong>Alunos em Regime (%)</strong>
                            <input type="number" class="form-control lead text-center" value="0" step="0.1" min="0" max="100" id="percAlunosEmRegime" name="percAlunosEmRegime">
                          </div>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                  <!--<div class="row">
                    <div class="col-md-12 lead">A média das disciplinas nucleares deve ser maior que: <input type="number" name="notaMinDiscNucleares" id="notaMinDiscNucleares" style="width: 100px; height: 40px; text-align: center; border-radius: 10px; border-color: darkblue;" min="0" step="0.01" max="20"></div>
                  </div>!-->

              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-3 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg" id="Cadastar"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>