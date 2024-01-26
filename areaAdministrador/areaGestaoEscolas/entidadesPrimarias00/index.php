<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Entidades Primárias", "entidadesPrimarias00");
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

      fieldset{
        border:solid rgba(0, 0, 0, 0.6) 2px;
        border-radius: 10px;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 10px;
      }
      fieldset legend{
        width: 150px;
        font-weight: bolder;
      }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    $layouts->cabecalho();
    $layouts->aside();

    $cargo = isset($_GET["cargo"])?$_GET["cargo"]:"";

    $limite="";
    $ordenacao =array("nomeEntidade"=>1);
    if($cargo==100){
      $limite=100;
      $ordenacao =array("idPEntidade"=>-1);
    }
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" >

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Entidades Primárias</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "entidadesPrimarias00", array(), "msg")){

            echo "<script>var listaEntidades = ".$manipulacaoDados->selectJson("entidadesprimaria", ["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "ninjaF5", "generoEntidade", "fotoEntidade", "tituloNomeEntidade", "numeroAgenteEntidade", "dataNascEntidade", "estadoAcessoEntidade"], ["nomeEntidade"=>array('$ne'=>null)], [], $limite, [], $ordenacao)."</script>";

         ?>



           <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-2 col-lg-2">
                      <div>
                          <button ype="button" class="btn lead btn-success novoRegistroFormulario"><i class="fa fa-user-plus"></i> Adicionar</button>
                      </div>
                  </div>
                  <div class="col-md-10 col-lg-10">
                      <label class="lead">Total de Entidades: <span id="numTProfessores" class="quantidadeTotal"></span></label>&nbsp;&nbsp;
                       <label class="lead">Masculinos: <span id="numTMasculinos" class="quantidadeTotal"></span></label>
                  </div>
                </div>
                <table id="example1" class="table table-bordered table-striped">
                    <thead class="corPrimary">
                        <tr>
                            <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                            <th class="lead font-weight-bolder "><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                            <th class="lead text-center"><i class="fa fa-restroom"></i></th>
                            <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                            <th class="lead font-weight-bolder text-center" style="min-width: 100px;"><strong>Acesso</strong></th>
                            <th class="lead font-weight-bolder text-center" style="min-width: 100px;"><strong>Ninja</strong></th>
                            <th class="lead text-center" style="min-width: 100px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tabProfessores">
                    </tbody>
                </table>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs();  $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioEntidade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioEntidadeForm" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-circle"></i> Entidades Primárias</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">

                      <div class="col-lg-6 col-md-6 lead">
                        Nome Completo
                        <input type="text" name="nomeEntidade" class="form-control fa-border somenteLetras vazio" id="nomeEntidade" title="Nome da Entidade" autocomplete="off" required maxlength="60">

                        <div class="nomeEntidade discasPrenchimento lead"></div>
                      </div>

                      <div class="col-lg-6 col-md-6 lead">
                        Título da Entidade
                        <input type="text" name="tituloNomeEntidade" class="form-control fa-border somenteLetras vazio" id="tituloNomeEntidade" title="Título da Entidade" autocomplete="off" maxlength="60">

                        <div class="tituloNomeEntidade discasPrenchimento lead"></div>
                      </div>
                  </div>

                  <div class="row">
                     <div class="col-lg-3 col-md-3 lead">
                        Género:
                          <select class="form-control lead" id="sexoEntidade" name="sexoEntidade">
                              <option value="M">Masculino</option>
                              <option value="F">Feminino</option>
                          </select>
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
                        Data de Nascimento
                        <input type="date" name="dataNascEntidade" class="form-control vazio" id="dataNascEntidade" max="<?php echo $manipulacaoDados->dataSistema; ?>">
                        <div class="dataNascEntidade discasPrenchimento lead"></div>
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Acesso:
                          <select class="form-control lead" id="estadoAcesso" name="estadoAcesso">
                            <option value="A">Activo</option>
                            <option value="I">Inactivo</option>
                          </select>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12 col-md-12 lead"><label><input type="checkbox" name="ninjaF5" id="ninjaF5"> Ninja F5</label></div>
                  </div>

                  <input type="hidden" name="cargo" value="<?php echo $cargo; ?>">
                  <input type="hidden" name="idPEntidade" idChave="sim">
                  <input type="hidden" name="action">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button type="submit" class="btn btn-primary lead submitter" id="Cadastar"><i class="fa fa-user-edit"></i> </button>
                    </div>
                  </div>
              </div>
          </form>
      </div>
    </div>
