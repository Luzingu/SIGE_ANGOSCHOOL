<?php session_start();

     include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Galeria de Fotos", "galeriaFotos11");
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
  <style type="text/css">
    #fotos img{
      max-width: 100%;
      max-height: 200px;
      height: 200px;
      border-radius: 10px;
      width: 100%;
    }
    #fotos .divFoto{
      height: 350px;
      max-height: 350px;
      
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
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-camera"></i> Galeria de Fotos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "galeriaFotos11", array(), "msg")){                                
            echo "<script>var galeriaFotos =".$manipulacaoDados->selectJson("escolas", ["fotos.idPGaleria", "fotos.fotoGaleria", "fotos.legendaFoto", "fotos.imgPrincipal", "idPEscola"],["idPEscola"=>$_SESSION['idEscolaLogada']], ["fotos"])."</script>";
          ?>
          <div class="row">
              <div class="col-lg-2 col-md-2 col-xs-6 col-sm-6">
                <button type="button" class="lead btn btn-primary" id="novaFoto"><i class="fa fa-plus"></i> Nova Foto</button>
              </div>
          </div>
          <div class="row" id="fotos"></div>    
  


        
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs();  $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioNovaFoto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioNovaFotoForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-camera"></i> Adicionar Foto</h4>
              </div>

              <div class="modal-body">
                 <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">
                      <div class="col-lg-5 col-md-5 lead">
                        Imagem:
                        <input type="file" class="form-control lead" name="imagem" id="foto" required>
                      </div>
                      <div class="col-lg-7 col-md-7 lead">
                        Legenda:
                        <input type="text" class="form-control lead" name="legendaFoto" id="legendaFoto" maxlength="50" required>
                      </div>
                  </div>
                  <input type="hidden" name="idPFoto" id="idPFoto">
                  <input type="hidden" name="action" id="action">
              </div>
              
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-plus"></i> Adicionar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
