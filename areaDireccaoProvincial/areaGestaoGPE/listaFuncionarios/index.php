<?php session_start(); 
  if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }      
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Dados de Agentes");
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

       #formularioEntidade .modal-dialog{
          width: 70%; 
          margin-left: -35%;
        }
      @media (max-width: 768px) {
            #formularioEntidade .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
      }
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
    $layouts->headerUsuario();
    $layouts->areaGestaoGPE();
    $usuariosPermitidos = ["aGestGPE"];
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Agentes</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "dadosAgentes", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){
            
            
            echo "<script>var listaEntidades = ".$manipulacaoDados->selectJson("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND idEntidadeEscola=:idEntidadeEscola", ["A", $_SESSION["idEscolaLogada"]], "nomeEntidade ASC")."</script>";
         ?>

      <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-lg-12">
                    <button class="btn btn-success btn-lg" id="novoAgente"><i class="fa fa-plus-circle"></i> Novo Agente</button>&nbsp;&nbsp;&nbsp;
                    <select id="tamanhoFolha" class=" lead">
                      <option>A3</option>
                      <option>A2</option>
                      <option>A1</option>
                      <option>A0</option>
                      <option>A4</option>
                    </select>&nbsp;&nbsp;&nbsp;
                       <a href="#" id="pessoalDocente" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Lista de Pessoal Docente</a>
                               
                  </div>
                  <div class="col-md-12 col-lg-12"><br/>
                        <label class="lead">Total de Agentes: <span id="numTProfessores" class="quantidadeTotal"></span></label>     
                    </div>
                </div>
                <?php $includarHtmls->indexAgente(); ?>
            </div>
          </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs();  $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();  ?>