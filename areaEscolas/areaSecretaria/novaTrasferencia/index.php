<?php session_start();   
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Nova Transferência", "novaTrasferencia");
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-mail-forward"></i> Transferências de Alunos</strong></h1>

              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "novaTrasferencia",array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = $luzingu[2];
          $classe = $luzingu[1];

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          

        echo "<script>var alunosReconfirmados=".$manipulacaoDados->selectJson("alunosmatriculados", ["nomeAluno", "idPMatricula", "numeroInterno", "reconfirmacoes.designacaoTurma", "reconfirmacoes.nomeTurma", "fotoAluno", "sexoAluno", "reconfirmacoes.classeReconfirmacao", "escola.idMatCurso"], ["reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$classe, "escola.estadoAluno"=>"A", "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1), $manipulacaoDados->matchMaeAlunos($manipulacaoDados->idAnoActual, $idCurso, $classe))."</script>";
          ?>

      <div class="card">
        <div class="card-body">
          <div class="row">

            <div class="col-lg-3 col-md-3 lead">
              Classe
                <select class="form-control lead" id="luzingu">
                  <?php 
                    if(isset($_SESSION['classesPorCurso'])){
                      echo $_SESSION['classesPorCurso'];
                    }else{
                      $_SESSION['classesPorCurso']= retornarClassesPorCurso($manipulacaoDados, "A", "nao", "nao", "sim");
                    }
                  ?>                  
                </select>
            </div>
            <div class="col-md-9 col-lg-9 col-sm-12 col-xs-12"><br/>
               <label class="lead">
                    Total de Alunos: <span class="numTAlunos quantidadeTotal">0</span>
                </label>&nbsp;&nbsp;&nbsp;
                 <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
            </div>
          </div>

          <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                  <tr>
                      <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                      <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa-user-tie"></i> Turma</strong></th>
                      <th class="lead text-center"></th>
                  </tr>
              </thead>
              <tbody id="tabJaReconfirmados">

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


<div class="modal fade" id="novaTrasferencia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formNovaTransferencia" method="POST">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-mail-forward"></i>  Transferência</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 lead">
                          <label>Nome do Aluno:</label>
                            <input style="font-weight: bolder; background-color: white; !important;" type="text" readonly class="nomeAluno form-control lead vazio">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 lead">
                          <label>Número Interno:</label>
                            <input style="font-weight: bolder; background-color: white; !important;" type="text" readonly class="numeroConta form-control lead vazio">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 lead">
                          <label class="lead"><input type="checkbox" name="seTransferenciaLocal" id="seTransferenciaLocal"> Transfere-se numa escola cadastrada no AngoSchool.</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 lead">
                          <label>Transfere-se para:</label>
                          <select class="form-control lead" id="nomeEscolaOption" name="nomeEscolaOption">
                            <?php 

                          foreach ($manipulacaoDados->selectArray("escolas", ["idPEscola", "nomeEscola"], ["estadoEscola"=>"A", "idPEscola"=>['$nin'=>array((int)$_SESSION['idEscolaLogada'], 4)]]) as $escola) { 

                            if(count($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso"], ["cursos.idCursoEscola"=>$escola["idPEscola"], "idPNomeCurso"=>$idCurso]))>0){

                              echo "<option value='".$escola["idPEscola"]."'>".$escola["nomeEscola"]."</option>";
                            }                          
                          }
                              
                            ?>
                          </select>
                            <input type="text" class="form-control lead vazio paraOutrasEscolas" name="nomeEscolaCaixa" id="nomeEscolaCaixa"s>
                        </div>
                    </div>

                    <div class="row hidden">
                    <div class="lead col-lg-2 col-md-2 text-right lab"><label for="pais">País:</label></div>
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <select id="pais" name="pais" class="form-control lead nomePaisBI">
                          <?php 
                            foreach($manipulacaoDados->selectArray("div_terit_paises", [], [], [], "", [], ["nomePais"=>1]) as $a){
                              echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                            }
                           ?> 
                        </select>
                      </div>
                    </div>

                    <div class="row paraOutrasEscolas">
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 lead">
                          <label>Província:</label>
                             <select class="form-control lead vazio" name="nomeProvincia" id="nomeProvincia"></select>
                        </div>
                        <div class="col-lg-4 col-md-4 lead">
                          <label>Municipio:</label>
                             <select class="form-control lead vazio" name="nomeMunicipio" id="nomeMunicipio"></select>
                        </div>
                        <div class="col-lg-4 col-md-4 lead">
                          <label>Comuna:</label>
                             <select class="form-control lead vazio" name="nomeComuna" id="nomeComuna"></select>
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-12 col-md-12 lead">

                      <fieldset style="border: solid rgba(0, 0, 0, 0.2) 1px; border-radius: 20px; padding: 10px; padding-top: 0px;">
                        <legend>Documentos Anexados</legend>
                        <textarea class="form-control lead" placeholder="Separar os documentos anexados deve estar separados entre ponto e vírgula (;)" style="max-width: 100%; min-width: 100%; height: 120px; min-height: 120px;" required="" name="documentosAnexos" id="documentosAnexos"></textarea>
                      </fieldset>
                      </div>
                    </div>
                    <input type="hidden" name="idPMatricula" id="idPMatricula">
                     <input type="hidden" name="idMatCurso" id="idMatCurso">
                    <input type="hidden" name="turma" id="turma">
                    <input type="hidden" name="action" value="transferirAluno">
                    <input type="hidden" name="classe" id="classe">
                </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button type="submit" id="Cadastrar" class="btn btn-success lead btn-lg"><i class="fa fa-check"></i> Concluir</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
