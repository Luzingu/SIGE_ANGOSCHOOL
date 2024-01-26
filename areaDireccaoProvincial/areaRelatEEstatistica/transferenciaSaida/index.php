<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Transferências Efectuadas");
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
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->headerUsuario();
    $layouts->areaEstatERelat();
    $usuariosPermitidos[] = "aRelEstatistica"; 

    $privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"Pública";
    if($privacidade!="Pública" && $privacidade!="Privada"){
      $privacidade="Pública";
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

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-mail-forward"></i> (<?php echo $privacidade; ?>) Transferências - Saídas</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "efectTransf", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){

          $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->selectUmElemento("anolectivo", "idPAno", "estado=:estado", ["V"], "numAno ASC");
        echo "<script>var idPAno='".$idPAno."'</script>";
        echo "<script>var privacidade='".$privacidade."'</script>";

        $alunosTransferidos=array();
        foreach($manipulacaoDados->selectArray("transferencia_alunos LEFT JOIN alunosmatriculados ON idPMatricula=idTransfMatricula LEFT JOIN escolas ON idPEscola=idTransfEscolaOrigem", "*", "provincia=:provincia AND idTransfAno=:idTransfAno AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), $idPAno, $privacidade]) as $a){

          if($a->idTransfEscolaDestino==NULL || $a->idTransfEscolaDestino==""){
            $nomeEscolaDestino = $a->nomeEscolaDestino;
          }else{
            $nomeEscolaDestino = $manipulacaoDados->selectUmElemento("escolas", "nomeEscola", "idPEscola=:idPEscola", [$a->idTransfEscolaDestino]);
          }
          

          $alunosTransferidos[] = array("nomeAluno"=>$a->nomeAluno, "numeroInterno"=>$a->numeroInterno, "nomeEscolaOrigem"=>$a->nomeEscola, "nomeEscolaDestino"=>$nomeEscolaDestino);

        }
        echo "<script>var alunosTransferidos=".json_encode($alunosTransferidos)."</script>";
         
           

       
          ?>

      <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-xs-12 col-sm-12 lead">
                  Ano:
                  <select class="form-control lead" id="anosLectivos">
                  <?php 
                    foreach($manipulacaoDados->selectArray("anolectivo", "*", "numAno>='2021'", [], "numAno DESC") as $ano){                      
                      echo "<option value='".$ano->idPAno."'>".$ano->numAno."</option>";
                    }
                   ?>
              </select>
                </div>
                <div class="col-md-10 col-lg-10 col-sm-12 col-xs-12"><br/>
                   <label class="lead">
                        Total: <span class="numTAlunos quantidadeTotal">0</span>
                    </label>
                </div>
            </div>

            <div class="table-responsive">
                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                        <tr>
                            <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                            <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                            <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                            <th class="lead"><strong><i class="fa fa-school"></i> Origem</strong></th>
                            <th class="lead"><strong><i class="fa fa-school"></i> Destino</strong></th>
                        </tr>
                    </thead>
                    <tbody id="tabTransferencia">

                    </tbody>
                </table>
            </div>
        </div>
      </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formConfirmarSenhaAdministrador(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>