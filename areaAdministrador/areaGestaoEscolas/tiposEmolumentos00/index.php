<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Tipos de Emolumentos", "tiposEmolumentos00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-money-bill-alt"></i> Tipos de Emolumentos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "tiposEmolumentos00", array(), "msg")){

         ?>
          <?php echo "<script>var tipos_emolumentos = ".$manipulacaoDados->selectJson("tipos_emolumentos")."</script>";
           ?>
    
            <div class="card">              
              <div class="card-body">
                <div class="row">
                   <div class="col-lg-2 col-md-2">
                      <button type="button" name="" class="lead btn btn-primary" id="novoEmolumento"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>N.º</strong></th>
                        <th class="lead"><strong>Código</strong></th>
                        <th class="lead"><strong>Designação</strong></th>
                        <th class="lead"><strong>Tipo de Pag.</th>
                        <th class="lead text-center"></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
          </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>



<div class="modal fade" id="formularioTipoEmolumentos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioTipoEmolumentosForm">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-money-bill-alt"></i> Tipos de Emolumentos</h4>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-lg-3 col-md-3 lead">
            Código
            <input type="text" class="form-control fa-border vazio" id="codigo" required name="codigo">
          </div>
          <div class="col-lg-9 col-md-9 lead">
            Designação
            <input type="text" class="form-control fa-border vazio" id="designacaoEmolumento" required name="designacaoEmolumento">
          </div>
        </div>
        <div class="row">
          <div class="col-lg-5 col-md-5 lead">
            Tipo
            <select class="form-control" name="tipoPagamento" id="tipoPagamento">
              <option value="pagPorAno">Pag./Ano (Reconfirmação)</option>
              <option value="pagPorTrimestre">Pag./Trimestre (Boletim)</option>
              <option value="pagPorClasse">Pag/Classe (Declaração)</option>
              <option value="pagMensal">Paga/Mês (Propinas)</option>
              <option value="pagAberto">Pag. Aberto (Uniforme)</option>
            </select>
          </div>
        </div>

          <input type="hidden" name="idPTipoEmolumento" id="idPTipoEmolumento" idChave="sim">
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
