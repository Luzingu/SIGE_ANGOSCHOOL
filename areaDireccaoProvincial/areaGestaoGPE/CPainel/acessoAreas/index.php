<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
     include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "CPainel - Acessao a Áreas");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Gestão do GPE";
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #tabela tr td{
      font-size: 12pt;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    $layouts->headerUsuario();
    $layouts->areaGestaoGPE();
    $usuariosPermitidos = ["aGestGPE"];
  ?>
  <section id="main-content"> 
        <section class="wrapper" id="containers">
           <div class="row" >
      <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

          <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                <b class="caret"></b>
                            </a>
          <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-cog"></i> Acesso a Áreas</strong></h1>
      </nav>
      
    </div>
    <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "admContrAcessoArea")){

          $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC");

          if(count($manipulacaoDados->selectArray("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola AND idPEscola=:idPEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", $idPEscola], "nomeEscola ASC"))<=0){

            $idPEscola = $manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC");
          }

          $sobreEscola = $manipulacaoDados->selectArray("escolas", "*", "idPEscola=:idPEscola", [$idPEscola]);
          echo "<script>var idPEscola='".$idPEscola."'</script>";

          if(valorArray($sobreEscola, "tipoInstituicao")=="escola"){
            $vetor[] = "aPedagogica";
            $vetor[] = "aDirectoria";
            $vetor[] = "aAdministrativa";
            $vetor[] = "aComissaoPais";
            $vetor[] = "aPromotoria";

            $cargos[] ="Administrativo"; $cargos[] ="Pedagógico"; $cargos[] ="Director"; $cargos[] ="Chefe_da_Secretaria"; $cargos[] ="Secretário_Pedagógico"; $cargos[] ="Secretário_Administrativo"; $cargos[] ="Acessório_Pedagógico"; $cargos[] ="Acessório_Administrativo"; $cargos[] ="Promotor"; $cargos[] ="Promotor_Adjunto"; $cargos[] ="Coordenador_de_Pais";
          }else{
            $vetor[] = "aGestGPE";
            $vetor[] = "aRelEstatistica";
            $cargos[] ="DP"; $cargos[] ="CDI"; $cargos[] ="CDEEGCT"; $cargos[] ="CDARH"; $cargos[] ="CDEPE"; $cargos[] ="CDASE";
          }

          foreach ($vetor as $area) {
              $manipulacaoDados->inserir("acessoareas", "idAcessoEscola, nomeArea, chaveArea", [$idPEscola, $area, $area."-".$idPEscola]);
          } 

            echo "<script>var acessoAreas =".$manipulacaoDados->selectJson("acessoareas", "*", "idAcessoEscola=:idAcessoEscola", [$idPEscola])."</script>";
          ?>

          <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
            <label>Escola</label>
            <select class="form-control lead" id="idPEscola">
              <?php foreach($manipulacaoDados->selectArray("escolas", "*", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC") as $a){
                echo "<option value='".$a->idPEscola."'>".$a->nomeEscola."</option>";
              } ?>
            </select>
          </div>
          </div>

          <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>                         
                          <th class="lead"><strong>Área</strong></th>
                          <th class="lead"><strong>Acesso a Visualização</strong></th>
                          <th class="lead"><strong>Acesso a Alteração<strong></th>
                          <th class="lead text-center"></th>
                      </tr>

                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>

         <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha();
 ?>


<div class="modal fade" id="alterarAcesso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioAletrarAcesso">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-book-open"></i> <span id="tituloModal">Alterar Acessos</span></h4>
              </div>

              <div class="modal-body">
                <fieldset style="border:solid rgba(0,0,0,0.3) 1px; border-radius: 10px; padding: 20px;" id="alteracao">
                  <legend style="margin-bottom: -20px; width: 130px;"><strong>Alteração</strong></legend>
                  <?php foreach ($cargos as $cargo) { ?>
                    <input type="checkbox" name="<?php echo $cargo; ?>" id="alt<?php echo $cargo; ?>"> <label for="alt<?php echo $cargo; ?>" class="lead"><?php echo $cargo; ?></label>&nbsp;&nbsp;&nbsp;
                   <?php } ?>
                </fieldset>

                <fieldset style="border:solid rgba(0,0,0,0.3) 1px; border-radius: 10px; padding: 20px;" id="visualizacao">
                  <legend style="margin-bottom: -20px; width: 130px;"><strong>Visualização</strong></legend>
                  <?php foreach ($cargos as $cargo) { ?>
                    <input type="checkbox" name="<?php echo $cargo; ?>" id="vis<?php echo $cargo; ?>"> <label for="vis<?php echo $cargo; ?>" class="lead"><?php echo $cargo; ?></label>&nbsp;&nbsp;&nbsp;
                   <?php } ?>
                </fieldset>
                

              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button type="submit" class="btn btn-primary lead btn-md submitter" id="alterar"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>