<?php session_start();
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }       
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Direcções Municipais");
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
    $layouts->headerUsuario();
    $layouts->areaGestaoGPE();
    $usuariosPermitidos[] = "aGestGPE";
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> Direcções Municipais</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos)){

          echo "<script>var provincia='".valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia")."'</script>";
          echo "<script>var municipio='".valorArray($manipulacaoDados->sobreUsuarioLogado, "municipio")."'</script>";
          echo "<script>var comuna='".valorArray($manipulacaoDados->sobreUsuarioLogado, "comuna")."'</script>";

          echo "<script>var listaEscolas =".$manipulacaoDados->selectJson("escolas LEFT JOIN div_terit_comunas ON idPComuna=comuna LEFT JOIN div_terit_municipios ON idPMunicipio=municipio", "*", "tipoInstituicao=:tipoInstituicao AND provincia=:provincia", ["DM", valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia")], "nomeEscola ASC")."</script>";

         ?>
    
      
            <div class="card">              
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                          <button ype="button" class="btn lead btn-success novoRegistroFormulario" id="novoProfessor"><i class="fa fa-user-plus"></i> Adicionar</button> &nbsp;&nbsp;&nbsp;
                    <label class="lead">Total: <span id="numTEscolas" class="quantidadeTotal"></span></label>&nbsp;&nbsp;&nbsp;
                    <label class="lead">Activos: <span id="numTActivo" class="quantidadeTotal"></span></label>
                  </div>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                        <tr>
                            <th class="lead text-center">Nº</th>
                            <th class="lead font-weight-bolder "><strong>Nome da Escola</strong></th>
                            <th class="lead text-center"><strong>Localização</strong></th>
                            <th class="lead visible-md visible-lg"><strong><i>@</i> Endereco</strong></th>
                            <th class="lead font-weight-bolder text-center"><strong>Estado</strong></th>
                            <th class="lead text-center" style="min-width: 100px;"></th>
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
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-school"></i> Direcções Municipais</h4>
              </div>


              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">
                      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 lead">
                        Nome da Instituição:
                        <input type="text" class="form-control vazio lead" id="nomeEscola" title="Nome da Escola" required  name="nomeEscola">
                        <div class="nomeEscola discasPrenchimento lead"></div>
                      </div>
                      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 lead">
                          Título/Cabeçalho
                        <input type="text" class="form-control vazio lead" id="tituloEscola" title="Nome da Escola" required  name="tituloEscola">
                        <div class="tituloEscola discasPrenchimento lead"></div>
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 lead">
                        Abreviação (10):
                        <input type="text" class="form-control vazio lead" id="abrevNomeEscola" title="Abreviação da Escola" required  name="abrevNomeEscola" maxlength="10">
                        <div class="abrevNomeEscola discasPrenchimento lead"></div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 lead">
                        2.ª Abreviação (30):
                        <input type="text" class="form-control vazio lead" id="abrevNomeEscola2" title="Abreviação da Escola" required  name="abrevNomeEscola2" maxlength="30">
                        <div class="abrevNomeEscola2 discasPrenchimento lead"></div>
                    </div>
                  </div>
                  
                  <div class="row">
                      
                      <div class="col-lg-3 col-md-3 lead">
                        Tipo:
                        <select class="form-control lead" name="tipoInstituicao" id="tipoInstituicao">
                            <option value="DM">Direcção Municipal</option>
                        </select>
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Endereço:
                        <input type="text" name="enderecoEscola" id="enderecoEscola" class="form-control lead vazio" placeholder="luzl.com" required>
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Privacidade:
                        <select class="form-control lead" name="privacidade" id="privacidade">
                            <option value="Pública">Pública</option>                      
                          </select>
                      </div>

                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 lead">
                        Estado:
                        <select class="form-control lead" id="estadoEscola" name="estadoEscola" required>
                            <option class="lead" value="A">Activo</option>
                            <option class="lead" value="I">Inactivo</option>
                        </select>
                      </div>
                  </div>
                  
                  <div class="row">
                      <div class="col-lg-3 col-md-3 lead">
                        Localização:
                        <select id="pais" disabled name="pais" class="form-control lead" required>
                          <?php 
                            foreach($manipulacaoDados->selectArray("div_terit_paises", "*", "", "", "nomePais ASC") as $a){
                              echo "<option value='".$a->idPPais."'>".$a->nomePais."</option>";
                            }
                          ?>
                        </select>

                      </div>
                       <div class="col-lg-3 col-md-3 lead">
                        Província:
                        <select id="provincia" disabled required name="provincia" class="form-control lead">
                      </select>
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Municipio:
                       <select id="municipio" required name="municipio" class="form-control municipio lead">                            
                      </select> 
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Comuna:
                        <select id="comuna" required name="comuna" class="form-control comuna lead"></select> 
                      </div>
                  </div>

              </div>             

              <input type="hidden" name="idPEscola" idChave="sim">
               <input type="hidden" name="action">

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Cadastrar</button>
                    </div>                    
                  </div>                
              </div>
          </form>
      </div>
  </div>