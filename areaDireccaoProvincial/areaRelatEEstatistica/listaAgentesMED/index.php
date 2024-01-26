<?php session_start();
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }       
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Escolas");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Relatório e Estatística";
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
    $layouts->areaEstatERelat();
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
                  <h1 class=" navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> <?php echo "Agentes do Ministério da Educação"; ?></strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos)){

          $listaAgentesMED = array();

          foreach($manipulacaoDados->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "DISTINCT idPEntidade", "estadoActividadeEntidade=:estadoActividadeEntidade AND provincia=:provincia AND idPEscola not in (4,7) AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND (naturezaVinc not in ('Colaborador') OR naturezaVinc IS NULL)", ["A", valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", "A"], "nomeEntidade ASC") as $a){

            $listaAgentesMED = array_merge($listaAgentesMED, $manipulacaoDados->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND provincia=:provincia AND idPEscola not in (4,7) AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND (naturezaVinc not in ('Colaborador') OR naturezaVinc IS NULL) AND idPEntidade=:idPEntidade", ["A", valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", "A", $a->idPEntidade], "nomeEntidade ASC LIMIT 1"));
          }
          echo "<script>var listaAgentes = ".json_encode($listaAgentesMED)."</script>";



         ?>
    
      
            <div class="card">              
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3 col-lg-3">
                    <label class="">Categoria da Instituição</label>
                    <select class="form-control " id="categoriaEscola">
                      <option value="">Todas</option>
                      <option value="primaria">Ensino Primário</option>
                      <option value="basica">I Ciclo</option>
                      <option value="media">II Ciclo</option>
                      <option value="primBasico">Complexo (Primária e I Ciclo)</option>
                      <option value="basicoMedio">Complexo (I e II Ciclo)</option>
                      <option value="complexo">Complexo</option>
                      <option value="DP">GPE</option>
                      <option value="DM">DME</option>
                    </select>
                  </div>
                  <div class="col-md-8 col-lg-8"><br>
                    <label class="">Total: <span id="numTEscolas" class="quantidadeTotal"></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label class="">Feminino: <span id="sexoFeminino" class="quantidadeTotal"></span></label>
                  </div>
                </div>

                <table id="example1" class="table table-bordered table-striped fotFoto" >
                  <thead class="corPrimary">
                        <tr>
                            <th class=" text-center">Nº</th>
                            <th class=" font-weight-bolder "><strong>Nome Completo</strong></th>
                            <th class=" text-center"><strong>N.º Agente</strong></th>
                            <th class=" font-weight-bolder"><strong>Categoria</strong></th>
                            <th class=" text-center"><strong>Instituição</strong></th>
                            <th class=" text-center"><strong>Função</strong></th>
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