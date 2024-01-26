<?php session_start(); 
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Definições de Conselho de Notas", "definicoesConselhoNotas");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-cog"></i> Conselho de Notas - Definições</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["definicoesConselhoNotas"], array(), "msg")){

            echo "<script>var definicoesConselhoNotas = ".$manipulacaoDados->selectJson("definicoesConselhoNotas", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idPAno"=>$manipulacaoDados->idAnoActual])."</script>";
           ?>
          <div class="card">
            <div class="card-body">
              <div class="row">
                <form class="col-lg-6 col-md-6 col-lg-offset-3 col-md-offset-3" style="border-radius: 10px; padding-bottom: 20px;" id="definicoesConselhoNotas"><br>
                  <div class="row">
                    <div class="col-lg-6 col-md-6">
                      <label>N.º Negativas por Deliberar</label>
                      <select class="form-control lead" id="negativasPorDeliberar" name="negativasPorDeliberar">
                        <option value="20">Qualquer</option>
                        <option value="1">No máximo 1 negativa</option>
                        <option value="2">No máximo 2 negativas</option>
                        <option value="3">No máximo 3 negativas</option>
                        <option value="4">No máximo 4 negativas</option>
                        <option value="5">No máximo 5 negativas</option>
                        <option value="6">No máximo 6 negativas</option>
                        <option value="7">No máximo 7 negativas</option>
                      </select>
                    </div>
                    <div class="col-lg-6 col-md-6">
                      <label>Nota mínima por Deliberar</label>
                      <select class="form-control lead" id="notaMinimaPorDeliberar" name="notaMinimaPorDeliberar">
                        <option value="0">Qualquer</option>
                        <option value="0.1">No mínimo 2/20 (1/10)</option>
                        <option value="0.2">No mínimo 4/20 (2/10)</option>
                        <option value="0.25">No mínimo 5/20 (2,5/10)</option>
                        <option value="0.3">No mínimo 6/20 (3/10)</option>
                        <option value="0.35">No mínimo 7/20 (3,5/10)</option>
                        <option value="0.4">No mínimo 8/20 (4/10)</option>
                        <option value="0.45">No mínimo 9/20 (4,5/10)</option>
                      </select>
                    </div>
                  </div>
                  
                  <fieldset style="border:solid rgba(0, 0, 0, 1.0) 1px; padding:3px; font-size: 18pt;">
                    <legend>Notas por alterar</legend>
                    <label><input type="checkbox" style="width:20px; height: 20px;" name="mac" id="mac"> MAC</label>&nbsp;&nbsp;&nbsp;
                    <label><input type="checkbox" style="width:20px; height: 20px;" name="trimestre1" id="trimestre1"> I trimestre</label>&nbsp;&nbsp;&nbsp;
                    <label><input type="checkbox" style="width:20px; height: 20px;" name="trimestre2" id="trimestre2"> II trimestre</label>&nbsp;&nbsp;&nbsp;
                    <label><input type="checkbox" style="width:20px; height: 20px;" name="trimestre3" id="trimestre3"> III trimestre</label>&nbsp;&nbsp;&nbsp;
                    <label><input type="checkbox" style="width:20px; height: 20px;" name="exame" id="exame"> Exame</label>
                    <input type="hidden" name="action" id="action" value="manipularDefinicoes">
                  </fieldset><br>

                  <fieldset style="border:solid rgba(0, 0, 0, 1.0) 1px; padding:3px;">
                    <legend>Expressões a usar</legend>
                  
                      <div class="col-lg-4 col-md-4">
                        <label>Aprovado</label>
                        <select class="form-control" required id="exprParaAprovado" name="exprParaAprovado">
                          <option>APTO</option>
                          <option>APROVADO</option>
                          <option>TRANSITA</option>
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label>Aprovado com Def.</label>
                        <select class="form-control" id="exprParaAprovadoComDef" name="exprParaAprovadoComDef" required>
                          <option>TRANSITA</option>
                          <option>TRANSITA/DEF</option>
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label>Recurso</label>
                        <select class="form-control" id="exprParaAprovadoComRecurso" name="exprParaAprovadoComRecurso" required>
                          <option>RECURSO</option>
                        </select>
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label>Não Aprovado</label>
                        <select class="form-control" id="exprParaNaoAprovado" name="exprParaNaoAprovado" required>
                          <option>N. APTO</option>
                          <option>N. APROVADO</option>
                          <option>N. TRANSITA</option>
                        </select>
                      </div>
                    
                  </fieldset>
                  <div class="row">
                    <div class="col-lg-4 col-md-4">
                      <button type="submit" class="btn btn-lg btn-success"><i class="fa fa-check-circle"></i> Salvar</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>