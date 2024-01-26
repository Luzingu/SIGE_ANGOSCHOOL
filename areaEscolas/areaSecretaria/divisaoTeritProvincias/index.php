<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Divisão Territoriais - Províncias", "divTerritorial");
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
                  <h1 class="navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> Divisões Territoriais - Províncias</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["divTerritorial"], array(), "msg")){

          $idPPais = isset($_GET['idPPais'])?$_GET['idPPais']:"";

          if($idPPais==""){
            echo "<script>window.location ='../divisaoTeritPaises/index.php'</script>";
          }
          echo "<script>var idPPais='".$idPPais."'</script>";

          echo "<script>var listaProvincias = ".$manipulacaoDados->selectJson("div_terit_provincias", [], ["idProvPais"=>$idPPais], [], "", [], ["nomeProvincia"=>1])."</script>";
        ?>
          <h1 style="margin-top:-10px;"><strong class="text-primary"><?php echo $manipulacaoDados->selectUmElemento("div_terit_paises", "nomePais", ["idPPais"=>$idPPais]); ?></strong></h1>
          <div class="card">

              <div class="card-body">
                <div class="row">
                   <div class="col-lg-12 col-md-12"><br>
                      <button type="button" name="" class="btn lead btn-success novoRegistroFormulario" id="novoAnexo"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>
                </div>
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                          <th class="lead text-center"><strong></strong></th>
                          <th class="lead"><strong>Nome</strong></th>
                          <th class="lead text-center"><strong>Preposição</strong></th>    
                          <th class="lead text-center"><strong>Preposição2</strong></th>                         
                          <th class="lead text-center"><strong>Municipios</strong></th>  
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formConfirmarSenhaAdministrador(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>



<div class="modal fade" id="formularioProvincia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioProvinciaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-school"></i> Província</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-9 col-md-9 lead">
                      <label class="lead">Nome</label> 
                      <input type="text" class="form-control lead vazio" id="nomeProvincia" name="nomeProvincia" autocomplete="off">
                    </div>

                    <div class="col-lg-3 col-md-3">
                      <label class="lead">Preposição</label>
                      <select class="form-control" id="preposicaoProvincia" name="preposicaoProvincia">
                        <option>em</option>
                        <option>no</option>
                        <option>na</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                      <label class="lead">Preposição2</label>
                      <select class="form-control" id="preposicaoProvincia2" name="preposicaoProvincia2">
                        <option>de</option>
                        <option>do</option>
                        <option>da</option>
                      </select>
                    </div>
                  </div>

                  <input type="hidden" name="idPProvincia" id="idPProvincia" idChave="sim">
                  <input type="hidden" name="idProvPais" id="idProvPais" value="<?php echo $idPPais; ?>">
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
