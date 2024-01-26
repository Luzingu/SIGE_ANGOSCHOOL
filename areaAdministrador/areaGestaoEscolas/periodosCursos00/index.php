<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Períodos", "periodosCursos00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-reader"></i> Períodos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "classes00", array(), "msg")){
          
          $idPNomeCurso = isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso");
          echo "<script>var idPNomeCurso='".$idPNomeCurso."'</script>";

          $array = listarItensObjecto($manipulacaoDados->selectArray("nomecursos", [], ["idPNomeCurso"=>$idPNomeCurso]), "periodos");
          echo "<script>var listaPeriodos = ".json_encode(ordenar($array, "ordem ASC"))."</script>";
           ?>
    
            <div class="card">              
              <div class="card-body">
                <div class="row">

                  <div class="col-lg-3 col-md-3 lead">
                    <label>Curso</label>
                    <select class="form-control" name="idPNomeCurso" id="idPNomeCurso">
                    <?php 
                      foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "nomeCurso"], [], [],"", [], ["ordem"=>1]) as $a){
                        echo "<option value='".$a["idPNomeCurso"]."'>".$a["nomeCurso"]."</option>";
                      }
                    ?>
                    </select>
                  </div>
                   <div class="col-lg-9 col-md-9"><br>
                      <button type="button" name="" class="lead btn btn-primary novoRegistroFormulario" id="novoPeriodo"><i class="fa fa-plus"></i> Adicionar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                       <label class="lead">Total: <span id="numTCursos" class="quantidadeTotal"></span></label>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"></th>
                        <th class="lead"><strong>Identificador</strong></th>
                        <th class="lead"><strong>Designação</strong></th>
                        <th class="lead"><strong>1.ª Abrev</strong></th>
                        <th class="lead text-center"><strong>2.ª Abrev</strong></th>
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



<div class="modal fade" id="formularioPeriodos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioPeriodosForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-reader"></i> Períodos</h4>
              </div>

              <div class="modal-body">

                  <div class="row">
                    <div class="col-lg-2 col-md-2">
                      <label>Ordem</label>
                      <input type="number" class="form-control text-center vazio" required name="ordem" id="ordem">
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label>Identificador</label>
                      <input type="text" class="form-control vazio text-center" required name="identificador" id="identificador">
                    </div>
                    <div class="col-lg-4 col-md-4">
                      <label>Designação</label>
                      <input type="text" class="form-control vazio" required id="designacao" name="designacao">
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label>1.ª Abrev</label>
                      <input type="text" class="form-control vazio" required id="abreviacao1" name="abreviacao1">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                      <label>2.ª Abrev</label>
                      <input type="text" class="form-control vazio" required id="abreviacao2" name="abreviacao2">
                    </div>
                  </div>

                  <input type="hidden" name="idPPeriodo" id="idPPeriodo">
                  <input type="hidden" name="idPNomeCurso" id="idPNomeCurso" value="<?php echo $idPNomeCurso; ?>">
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
