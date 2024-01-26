<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Campos de Avaliação", "camposAvaliacoes00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong>Campos de Avaliações
                  </strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "camposAvaliacoes00", array(), "msg")){

          $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola");
          echo "<script>var idPEscola='".$idPEscola."'</script>";

          if($idPEscola==4){
            $manipulacaoDados->conDb("teste");
          }          
          $dados  = ordenar($manipulacaoDados->selectArray("campos_avaliacao"), "ordenacao ASC");
          echo "<script>var campos_avaliacao = ".json_encode($dados)."</script>";
          $manipulacaoDados->conDb("escola");
           ?>
    
            <div class="card">              
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-2 col-md-2"><br>
                      <button type="button" name="" class="lead btn btn-primary" id="novaAvaliacao"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead"><strong>Ordem</strong></th>
                        <th class="lead"><strong>Identificação</strong></th>
                        <th class="lead"><strong>Designação</strong></th>
                        <th class="lead"><strong>Tipo de Campo</th>
                        <th class="lead text-center"></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
          </div><br>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioMatricula" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioAvaliacoesForm">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Avaliações</h4>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-lg-2 col-md-2 lead">
            <label>Ordem</label>
            <input type="number" required class="form-control text-center vazio" id="ordenacao" name="ordenacao">
          </div>
          <div class="col-lg-3 col-md-3 lead">
            <label>Identif.</label>
            <input type="text" required class="form-control text-center vazio" id="identUnicaDb" name="identUnicaDb">
          </div>
          <div class="col-lg-3 col-md-3 lead">
            <label>Designação1</label>
            <input type="text" required class="form-control text-center vazio" id="designacao1" name="designacao1">
          </div>
          <div class="col-lg-4 col-md-4 lead">
            <label>Designação2</label>
            <input type="text" required class="form-control text-center vazio" id="designacao2" name="designacao2">
          </div>
        </div>
        <div class="row">
          
          <div class="col-lg-4 col-md-4 lead">
            <label>Tipo</label>
            <select class="form-control" id="tipoCampo" name="tipoCampo">
              <option value="avaliacao">Avaliação</option>
              <option value="mediaTrim">Média Trimestral</option>
              <option value="mfd">MFD</option>
              <option value="exame">Exame</option>
              <option value="mediaFinal">Média Final</option>
              <option value="classificaDisciplina">Classif. Final Disciplina</option>
              <option value="recurso">Recurso</option>
            </select>
          </div>
          <div class="col-lg-2 col-md-2 lead">
            <label>MAX</label>
            <input type="number" class="form-control text-center" name="notaMaxima" id="notaMaxima">
          </div>
          <div class="col-lg-2 col-md-2 lead">
            <label>MED</label>
            <input type="number" class="form-control text-center" name="notaMedia" id="notaMedia">
          </div>
          <div class="col-lg-2 col-md-2 lead">
            <label>MIN</label>
            <input type="number" class="form-control text-center" name="notaMinima" id="notaMinima">
          </div>
          <div class="col-lg-2 col-md-2 lead">
            <label>Casas Dec.</label>
            <input type="number" min="0" class="form-control text-center" require name="numeroCasasDecimais" id="numeroCasasDecimais">
          </div>
        </div>
        <div class="row">
          <div class="col-lg-8 col-md-8">
            <label><input type="checkbox" name="seApenasLeitura" id="seApenasLeitura"> Apenas de Leitura</label>
          </div>
        </div>
        <input type="hidden" name="idCampoAvaliacao" id="idCampoAvaliacao">
        <input type="hidden" name="action" id="action">
      </div>
      <div class="modal-footer">
          <div class="row">
            <div class="col-lg-4 col-md-4 text-left">
              <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check-circle"></i> Salvar</button>
            </div>                    
          </div>                
      </div>
    </div>
  </form>
</div>
