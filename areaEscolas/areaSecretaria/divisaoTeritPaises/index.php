<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Divisão Territoriais - Países", "divTerritorial");
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
                  <h1 class="navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> Divisões Territoriais - Países</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "divTerritorial", array(), "msg")){


          echo "<script>var listaPaises = ".$manipulacaoDados->selectJson("div_terit_paises", [], [], [], "", [], array("nomePais"=>1))."</script>";
           ?>
          
          <div class="card">

              <div class="card-body">
                <div class="row">
                   <div class="col-lg-12 col-md-12">
                      <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novoAnexo"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>
                </div>
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><strong></strong></th>
                          <th class="lead"><strong>Nome</strong></th>
                          <th class="lead"><strong>Continente</strong></th>
                          <th class="lead text-center"><strong>Preposição</strong></th>  
                          <th class="lead text-center"><strong>Preposição2</strong></th>  
                          <th class="lead text-center"><strong>Províncias</strong></th>                         
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



<div class="modal fade" id="formularioPais" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioPaisForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-school"></i> País</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      <label class="lead">Continente</label>
                      <select class="form-control lead" name="continentePais" id="continentePais" required="">
                        <option>África</option>
                        <option>América</option>
                        <option>Ásia</option>
                        <option>Europa</option>
                        <option>Oceania</option>
                      </select>
                    </div>
                    <div class="col-lg-8 col-md-8 lead">
                      <label class="lead">Nome</label> 
                      <input type="text" class="form-control lead vazio" id="nomePais" name="nomePais" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                      <label class="lead">Preposição</label>
                      <select class="form-control" id="preposicaoPais" name="preposicaoPais">
                        <option>em</option>
                        <option>no</option>
                        <option>na</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label class="lead">Preposição2</label>
                      <select class="form-control" id="preposicaoPais2" name="preposicaoPais2">
                        <option>de</option>
                        <option>do</option>
                        <option>da</option>
                      </select>
                    </div>
                  </div>

                  <input type="hidden" name="idPPais" id="idPPais" idChave="sim">
                  <input type="hidden" name="action" id="action">
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success lead submitter" id="Cadastar"><i class="fa fa-user-plus"></i> Cadastrar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
