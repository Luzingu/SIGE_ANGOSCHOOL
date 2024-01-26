<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Reconfirmados");
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
    $usuariosPermitidos = ["aRelEstatistica"];

    $privacidade = isset($_GET["privacidade"])?$_GET["privacidade"]:"Pública";
    if($privacidade!="Pública" && $privacidade!="Privada"){
      $privacidade="Pública";
    }
    echo "<script>var privacidade='".$privacidade."'</script>";


  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand" style="color: white; font-weight: bolder;">(<?php echo $privacidade; ?>) Transferências - Entradas</h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){

         $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->selectUmElemento("anolectivo", "idPAno", "estado=:estado", ["V"], "numAno ASC");
        echo "<script>var idPAno='".$idPAno."'</script>";

        echo "<script>var alunosReconfirmados=".$manipulacaoDados->selectJson("alunosreconfirmados LEFT JOIN alunosmatriculados on idReconfMatricula=idPMatricula LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso LEFT JOIN entidadesprimaria ON idPEntidade=idReconfProfessor LEFT JOIN escolas ON idPEscola=idReconfEscola", "*", "idReconfAno=:idReconfAno AND provincia=:provincia AND idMatEscola=idReconfEscola AND estadoAluno in ('A', 'Y') AND tipoEntrada=:tipoEntrada", [$idPAno, valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "porTransferencia"], "nomeAluno ASC")."</script>";

          ?>

         
    <div class="card">
      <div class="card-body">
        <div class="row">
            <div class="col-lg-2 col-md-2 lead">
              Ano:
              <select class="form-control lead" id="anosLectivos">
                  <?php 
                    foreach($manipulacaoDados->selectArray("anolectivo", "*", "numAno>='2021'", [], "numAno DESC") as $ano){                      
                      echo "<option value='".$ano->idPAno."'>".$ano->numAno."</option>";
                    }
                   ?>
              </select>
            </div>

          </div>
          <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12"><br>
               <label class="lead">
                    Total de Alunos: <span class="numTAlunos quantidadeTotal">0</span>
                </label>&nbsp;&nbsp;&nbsp;
                 <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
            </div>
          </div>
          <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                      <th class="lead"><strong>Nome Completo</strong></th>
                      <th class="lead text-center"><strong>Número Interno</strong></th>

                      <th class="lead text-center"><strong>Escola</strong></th>
                  </tr>
              </thead>
              <tbody id="tabJaReconfirmados">

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
