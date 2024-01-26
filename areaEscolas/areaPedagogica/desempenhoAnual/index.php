<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Desempenho Anual", "desempenhoAnual");
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
  <style type="text/css">
    table tr td, table tr th{
      font-size: 11pt !important;
    }
  </style>
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
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-user-md"></i> Desempenho dos Alunos</strong></h1>

               
              </nav>
            </div>
          </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", ["desempenhoAnual"], array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);

          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:""; 
          $manipulacaoDados->papaJipe($idCurso, $classe, $turma);
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var idPAno='".$manipulacaoDados->idAnoActual."'</script>";
          echo "<script>var turma='".$turma."'</script>";
          echo "<script>var dataSistema='".$manipulacaoDados->dataSistema."'</script>";

          echo "<script>var listaAlunos=".json_encode($manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma, $manipulacaoDados->idAnoActual, array(), ["nomeAluno", "numeroInterno", "fotoAluno", "reconfirmacoes.observacaoF", "avaliacao_anual.seAlunoFoiAoRecurso", "reconfirmacoes.estadoDesistencia", "reconfirmacoes.mfT1", "reconfirmacoes.mfT2", "reconfirmacoes.mfT3", "grupo", "idPMatricula", "reconfirmacoes.mfT4", "reconfirmacoes.idPReconf"]))."</script>"; 
          ?>

        
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-3 col-md-3 lead">
                Turma
                <select class="form-control lead" id="luzingu">
                  <?php optTurmas($manipulacaoDados); ?>
                </select>
              </div>
              <div class="col-lg-8 col-md-8"><br>
                <label class="lead">
                    Total: <span class="quantidadeTotal" id="numTAlunos">0</span>
                </label>
              </div>        
            </div>
            <table id="example1" class="table table-striped table-bordered table-hover" >
                <thead class="corPrimary">
                      <tr>
                  <th class="lead text-center"><strong>Nº</strong></th>
                  <th class="lead"><strong>Nome Completo</strong></th>
                  <th class="lead text-center"><strong>Número Interno</strong></th> 
                  <th class="lead text-center"><strong>MT1</strong></th>
                  <th class="lead text-center"><strong>MT2</strong></th>
                  <th class="lead text-center"><strong>MT3</strong></th>
                  <th class="lead text-center"><strong>MF</strong></th>
                  <th class="lead text-center"><strong>Estado</strong></th>
                  <th class="lead text-center"><strong>OBS F.</strong></th>
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

<div class="modal fade" id="formularioAvaliacao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioAvaliacaoF">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-md"></i> Estado do Aluno</h4>
              </div>

              <div class="modal-body">
                  <div class="row" id="discasPrenchimento">
                      <div class="col-lg-12 lead"></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-8 col-md-8 lead">
                        Nome do Aluno:
                      <input type="text" name="" class="form-control lead" id="nomeAluno" style="background-color: white; font-weight: bolder;" readonly>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                       Estado:
                      <select id="estadoAluno" name="estadoAluno" class="form-control lead" required="">
                          <option value="A">Activo</option>
                          <option value="D">Desistente</option>
                          <option value="N">Mat. Anulada</option>
                          <option value="F">Excluido por Faltas</option>
                      </select>
                    </div>
                  </div>

                  <input type="hidden" name="idPReconf" id="idPReconf"  value="">
                  <input type="hidden" name="idPMatricula" id="idPMatricula"  value="">
                  <input type="hidden" name="grupoAluno" id="grupoAluno"  value="">
                  <input type="hidden" name="action" value="manipularAvaliacaoAnual">
                  <input type="hidden" name="classe" id="classeF">
                  <input type="hidden" name="idPAno" id="idPAno" value="<?php echo $idPAno; ?>">
                  <input type="hidden" name="idPCurso" id="idPCursoF">
                  <input type="hidden" name="turma" id="turma" value="<?php echo $turma; ?>">           
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                       <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastrar"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>