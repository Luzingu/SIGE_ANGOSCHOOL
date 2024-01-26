<?php session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Escolas", "escolas00");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> Escolas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "escolas00", array(), "msg")){
            echo "<script>var listaEscolas =".json_encode($manipulacaoDados->selectArray("escolas", [], ["nomeEscola"=>array('$ne'=>null)], [], "", [], ["nomeEscola"=>1]))."</script>";

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
                            <th class="lead text-center"><strong>Períodos</strong></th>

                            <th class="lead font-weight-bolder"><strong>Estado</strong></th>
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

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<div class="modal fade" id="formularioEscola" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioEscolaForm" method="POST">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-school"></i> Escolas</h4>
              </div>


              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-7 col-md-7 lead">
                      Nome da Instituição:
                      <input type="text" class="form-control vazio lead" id="nomeEscola" title="Nome da Escola" required  name="nomeEscola">
                      <div class="nomeEscola discasPrenchimento lead"></div>
                    </div>
                    <div class="col-lg-5 col-md-5 lead">
                        Abreviação (10):
                        <input type="text" class="form-control vazio lead" id="abrevNomeEscola" title="Abreviação da Escola" required  name="abrevNomeEscola" maxlength="10">
                        <div class="abrevNomeEscola discasPrenchimento lead"></div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 lead">
                        2.ª Abreviação (30):
                        <input type="text" class="form-control vazio lead" id="abrevNomeEscola2" title="Abreviação da Escola" required  name="abrevNomeEscola2" maxlength="30">
                        <div class="abrevNomeEscola2 discasPrenchimento lead"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      Tipo:
                      <select class="form-control lead" name="tipoInstituicao" id="tipoInstituicao">
                          <option value="escola">Escola</option>
                          <option value="DM">Direcção Munic.</option>
                          <option value="DP">Direcção Prov.</option>
                          <option value="administrador">Administrador</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      Privacidade:
                      <select class="form-control lead" name="privacidade" id="privacidade">
                          <option value="Pública">Pública</option>
                          <option value="Privada">Privada</option>                        
                        </select>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      Períodos:
                      <select class="form-control lead" id="periodosEscolas" name="periodosEscolas" required>
                          <option class="lead" value="reg">Regular</option>
                          <option class="lead" value="regPos">Regular e Pós-Laboral</option>
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                      Escolha de Turnos:
                      <select class="form-control lead" id="criterioEscolhaTurno" name="criterioEscolhaTurno" required>
                          <option class="lead" value="automatico">Automático</option>
                          <option class="lead" value="opcional">Opcional</option>
                      </select>
                    </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Localização:
                        <select id="pais" name="pais" class="form-control lead" required>
                          <?php 
                            foreach($manipulacaoDados->selectArray("div_terit_paises", [], [], [], "", [],array("nomePais"=>1)) as $a){
                              echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                            }
                          ?>
                        </select>

                      </div>
                       <div class="col-lg-3 col-md-3 lead">
                        Província:
                        <select id="provincia" required name="provincia" class="form-control lead">
                      </select>
                      </div>
                  </div>

                  <div class="row">
                      
                      <div class="col-lg-3 col-md-3 lead">
                        Municipio:
                       <select id="municipio" required name="municipio" class="form-control municipio lead">                            
                      </select> 
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Comuna:
                        <select id="comuna" required name="comuna" class="form-control comuna lead"></select> 
                      </div>
                      <div class="col-lg-2 col-md-2 lead">
                        Estado:
                        <select class="form-control lead" id="estadoEscola" name="estadoEscola" required>
                            <option class="lead" value="A">Activo</option>
                            <option class="lead" value="I">Inactivo</option>
                        </select>
                      </div>
                  </div>

              </div>             

              <input type="hidden" name="idPEscola" idChave="sim">
               <input type="hidden" name="action">

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button ype="submit" class="btn btn-primary lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Cadastrar</button>
                    </div>                    
                  </div>                
              </div>
          </form>
      </div>
  </div>