<?php session_start();     
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Contrato Escolas", "contratoEscolas00");
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
  <style type="">

       #formularioEscola .modal-dialog{
          width: 60%; 
          margin-left: -30%;
        }
      @media (max-width: 768px) {
            #formularioEscola .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book"></i> Contratos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "contratoEscolas00", array(), "msg")){

            echo "<script>var listaEscolas =".json_encode($manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>['$nin'=>[4, 7]]], ["contrato"], "", [], array("nomeEscola"=>1)))."</script>";

         ?>
    
      
            <div class="card">              
              <div class="card-body">

                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                        <tr>
                            <th class="lead font-weight-bolder "><strong>Nome da Escola</strong></th>
                            <th class="lead text-center"><strong>Pacote</strong></th>

                            <th class="lead font-weight-bolder text-center"><strong>Vigência</strong></th>
                            <th class="lead font-weight-bolder"><strong>Valor<br/>Pago</strong></th>
                            <th class="lead text-center"><strong>Tipo de<br>Contrato</strong></th>
                            <th class="lead"><strong></strong></th>
                            <th class="lead text-center" style="min-width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tabEscola">
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

<div class="modal fade" id="formularioEscola" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioEscolaForm" method="POST">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-book"></i> Gerenciador de Contratos</h4>
              </div>


              <div class="modal-body">

                  <div class="row">
                      <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 lead">
                        <label>Nome da Instituição:</label>
                        <input type="text" class="form-control vazio" id="nomeEscola"  title="Nome da Escola" disabled="" required  name="nomeEscola">
                      </div>

                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                        <label>Inicio</label>
                        <input type="date" name="dataInicioContrato" id="dataInicioContrato" class="vazio lead form-control">
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      <label>Tipo de Contrato</label>
                      <select class="form-control" id="tipoPagamento" name="tipoPagamento" required>
                        <option value="pos">Pós-Pago</option>
                        <!--<option value="pre">Pré-Pago</option>!-->
                        <option value="nao">Nenhum Pagamento</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      <label>Modo de Pag.</label>
                      <select class="form-control" id="modoPagamento" name="modoPagamento" required>
                        <option value="porAluno">Por Aluno</option>
                        <option value="valorGlobal">Por um valor global</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead idValorPagoPor15Dias">
                      <label>Valor Pago/15 dias</label>
                      <input type="number" step="5" required="" id="valorPagoPor15Dias" name="valorPagoPor15Dias" class="form-control lead text-center">
                    </div>

                    <div class="col-lg-3 col-md-3 lead valorPorAluno">
                      <label>Valor por Aluno</label>
                      <input type="number" step="5" required="" id="valorPorAluno" name="valorPorAluno" class="form-control lead text-center">
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      <label>N.º Meses por Bloq.</label>
                      <select required="" id="mesesConsecutivosParaBloquear" name="mesesConsecutivosParaBloquear" class="form-control lead text-center">
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                        <option>6</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                        <label>Inicio do Prazo</label>
                        <input type="date" name="inicioPrazoPosPago" id="inicioPrazoPosPago" class="vazio lead form-control">
                    </div>  
                    <div class="col-lg-3 col-md-3 lead">
                        <label>Fim do Prazo</label>
                        <input type="date" name="fimPrazoPosPago" id="fimPrazoPosPago" class="vazio lead form-control">
                    </div>                 
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Contrato</label>
                      <input class="form-control lead" type="file" id="imgContrato" name="imgContrato">
                    </div>
                  </div>                 
              </div>             

              <input type="hidden" name="idPEscola" idChave="sim">
               <input type="hidden" name="action">

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Editar</button>
                    </div>                    
                  </div>                
              </div>
          </form>
      </div>
  </div>