<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Fornecedores", "fornecedores");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-print"></i> Fornecedores</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["fornecedores"], array(), "msg")){
          
          echo "<script>var listaFornecedores = ".$manipulacaoDados->selectJson("escolas", ["fornecedores.NIF", "fornecedores.idPFornecedor", "fornecedores.nomeEmpresa", "fornecedores.enderecoEmpresa", "fornecedores.cidadeEmpresa", "fornecedores.codigoConta", "fornecedores.paisEmpresa"],["idPEscola"=>$_SESSION['idEscolaLogada']],["fornecedores"], "", [], ["idPFornecedor"=>1])."</script>";

          ?>

      <div class="card">
        <div class="card-body">
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <button class="btn btn-success" id="btnNova"><i class="fa fa-plus-circle"></i> Novo Fornecedor</button>
              </div>
            </div>
            
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center font-weight-bolder"><strong>N.º</strong></th>
                        <th class="lead font-weight-bolder"><strong>NIF</strong></th>
                        <th class="lead text-center"><strong>Nome da Empresa</strong></th>
                        <th class="lead text-center"><strong>Endereço</strong></th>
                        <th class="lead"><strong></strong></th>
                      </tr>
                  </thead>
                  <tbody id="tabDados">
                      
                  </tbody>
                </table>
        </div>
      </div><br>     

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>


<div class="modal fade" id="formulario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioForm">
      <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Fornecedores</h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-4 col-md-4 lead">
                <label>NIF</label>
                <input type="text" required class="form-control vazio" id="NIF" name="NIF">
              </div>
              <div class="col-lg-8 col-md-8 lead">
                <label>Nome da Empresa</label>
                <input type="text" required class="form-control vazio" id="nomeEmpresa" name="nomeEmpresa">
              </div>
            </div>
            <div class="row">
              <div class="col-lg-7 col-md-7 lead">
                <label>Endereço</label>
                <input type="text" required class="form-control vazio" id="enderecoEmpresa" name="enderecoEmpresa">
              </div>
              <div class="col-lg-5 col-md-5 lead">
                <label>Cidade</label>
                <input type="text" required class="form-control vazio" id="cidadeEmpresa" name="cidadeEmpresa">
              </div>              
            </div>
            <div class="row">
              <div class="col-lg-4 col-md-4 lead">
                <label>País</label>
                <select class="form-control" id="paisEmpresa" name="paisEmpresa">
                  <option value="AO">Angola</option>
                </select>
              </div>
            </div>
            <input type="hidden" id="action" name="action">
            <input type="hidden" id="idPFornecedor" name="idPFornecedor">
          </div>
          <div class="modal-footer">
              <div class="row">
                <div class="col-lg-12 col-md-12 text-right">
                  <button type="submit" id="Cadastrar" class="btn btn-success lead btn-lg"><i class="fa fa-check"></i> Concluir</button>
                </div>                    
              </div>                
          </div>
        </div>
      </form>
  </div>