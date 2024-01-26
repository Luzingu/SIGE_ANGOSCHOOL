<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Menus");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->designacaoArea="Layout e Acessos";
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
    $layouts->aside(0);
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-table"></i> Menus</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(0, array(), array(), "msg")){


          $idPArea = isset($_GET["idPArea"])?$_GET["idPArea"]:0;
          echo "<script>var idPArea='".$idPArea."'</script>";

          if($idPArea!=0){
            $condicao =["idAreaPorDefeito"=>$idPArea];
          }else{
            $condicao=array();
          }
          echo "<script>var menusEscolas = ".$manipulacaoDados->selectJson("menus", [], $condicao, [], "", [], array("ordemPorDefeito"=>1))."</script>";
           ?>

            <div class="card">
              <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      <label>Área por Defeito</label>
                      <select class="form-control lead" id="idPArea">
                        <option value='0'>Todas Áreas</option>
                        <?php
                        foreach($manipulacaoDados->selectArray("areas", [], [], [], "", [], array("designacaoArea"=>-1)) as $a){

                          echo "<option value='".$a["idPArea"]."'>".$a["designacaoArea"]."</option>";
                        } ?>
                      </select>
                    </div>
                   <div class="col-lg-2 col-md-2"><br>
                      <button type="button" id="btnNovoMenu" class="lead btn btn-success"><i class="fa fa-plus-circle"></i> Novo Menu</button>
                    </div>
                </div>

                <div class="row">
                  <div class="col-md-9 col-lg-9 visible-md visible-lg">
                  </div>
                  <div class="col-lg-3 col-md-3">
                    <input type="search" class="form-control" id="pesqMenu" placeholder="Pesquisar...">
                  </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center">Á. Espec. - Á./Defeito</th>
                        <th class="lead text-center">Icone</th>
                        <th class="lead"><strong>Designação</strong></th>
                        <th class="lead"><strong>Identificação</strong></th>
                        <th class="lead"><strong>Instituição</strong></th>
                        <th class="lead text-center"><strong>Sub Menus</strong></th>
                        <th class="lead text-center"></th>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
          </div><br><br>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>



<div class="modal fade" id="formularioMenus" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioMenusForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-table"></i> Menus</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-2 col-md-2 lead">
                      <label>Ordem</label>
                      <input type="text" class="form-control fa-border vazio text-center" id="ordemPorDefeito"  required name="ordemPorDefeito" autocomplete="off">
                    </div>
                    <div class="col-lg-7 col-md-7 lead">
                      <label>Designação do Menu</label>
                      <input type="text" class="form-control fa-border vazio" id="designacaoMenu"  required name="designacaoMenu" autocomplete="off">
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      <label>Identif. do Menu</label>
                      <input type="text" class="form-control fa-border vazio" id="identificadorMenu"  required name="identificadorMenu" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      <label>É gratuito?</label>
                      <select class="form-control" name="eGratuito" id="eGratuito">
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      <label>Icone</label>
                      <input type="text" class="form-control fa-border vazio" id="icone"  required name="icone" autocomplete="off">
                    </div>
                    <div class="col-lg-6 col-md-6 lead">
                      <label>Link</label>
                      <input type="text" class="form-control fa-border vazio" id="linkMenu"  required name="linkMenu" autocomplete="off">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Instituição</label>
                      <select class="form-control" id="instituicao" name="instituicao">
                        <option value="escola">Escola</option>
                        <option value="DM">Direcção Munic.</option>
                        <option value="DP">Direcção Prov.</option>
                        <option value="administrador">Administrador</option>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Área por Defeito</label>
                      <select class="form-control" required id="idAreaPorDefeito" name="idAreaPorDefeito">
                        <?php
                          foreach($manipulacaoDados->selectArray("areas", ["idPArea", "designacaoArea"], [], [], "", [], array("ordenacao"=>1)) as $a){
                            echo "<option value='".$a["idPArea"]."'>".$a["designacaoArea"]."</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Área Específica</label>
                      <select class="form-control" id="idAreaEspecifica" name="idAreaEspecifica">
                        <?php
                          echo "<option value=''>Qualquer Área</option>";
                          foreach($manipulacaoDados->selectArray("areas", ["idPArea", "designacaoArea"], [], [], "", [], array("ordenacao"=>1)) as $a){
                            echo "<option value='".$a["idPArea"]."'>".$a["designacaoArea"]."</option>";
                          }
                        ?>
                      </select>
                    </div>

                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Somente Online?</label>
                      <select class="form-control" name="somenteOnline" id="somenteOnline">
                        <option value="nao">Não</option>
                        <option value="sim">Sim</option>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="idPMenu" id="idPMenu">
                  <input type="hidden" name="idPArea" id="idPArea" value="<?php echo $idPArea; ?>">
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
