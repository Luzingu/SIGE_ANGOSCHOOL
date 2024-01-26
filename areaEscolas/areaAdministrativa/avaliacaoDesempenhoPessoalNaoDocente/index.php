<?php session_start();

   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Avaliação de Desempenho do Professor", "avaliacaoDesempenhoPessoalNaoDocente");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->retornarAnosEmJavascript();
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #listaProfessor form{
      border-bottom:solid rgba(0, 0, 0, 0.2) 2px;
      padding-top: 10px;
      padding-bottom: 20px;
    }
    #paraPaginacao ul li a{
      height: 40px;
      font-size: 15pt;
      padding: 5px;
      padding-right: 10px;
      padding-left: 10px;
      font-weight: bolder;
    }
  </style>
</head>

<body>
  <?php
    $usuariosPermitidos = ["aAdministrativa"];
    $janelaMensagens->processar ();
    $layouts->cabecalho();
    $layouts->aside();
    $trimestre=isset($_GET["trimestre"])?$_GET["trimestre"]:"I";
    if(!($trimestre=="I" || $trimestre=="II" || $trimestre=="III" || $trimestre=="IV")){
      $trimestre="I";
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
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-user-md"></i> Avaliação de Desempenho de Pessoal Nao Docente</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php

        if($verificacaoAcesso->verificarAcesso("", "avaliacaoDesempenhoPessoalNaoDocente", array(), "msg")){

            $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
            echo "<script>var idPAno=".$idPAno."</script>";

            echo "<script>var avaliacaoProfessor=".json_encode($manipulacaoDados->selectArray("entidadesprimaria", [], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"naoDocente", "aval_desemp.idAvalEntAno"=>$idPAno, "aval_desemp.idAvalEntEscola"=>$_SESSION['idEscolaLogada']], ["escola", "aval_desemp"], "", [], ["nomeEntidade"=>1]))."</script>";

            echo "<script>var comissAvalDesempPessoalNaoDocente=".$manipulacaoDados->selectJson("comissAvalDesempPessoalNaoDocente", [], ["idEscola"=>$_SESSION['idEscolaLogada'], "idAno"=>$idPAno])."</script>";

            $entidades = $manipulacaoDados->entidades(["idPEntidade", "nomeEntidade"]);


          ?>

        <div class="row">
          <div class="col-lg-2 col-md-2 lead">
              Ano Lectivo:
              <select class="form-control" id="anosLectivos">
                <?php
                  foreach($manipulacaoDados->selectArray("anolectivo", [], ["anos_lectivos.idAnoEscola"=>$_SESSION["idEscolaLogada"], "idPAno"=>['$nin'=>array(1, 842)]], ["anos_lectivos"], "", [], ["numAno"=>-1]) as $ano){
                    echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                  }
                ?>
              </select>
            </div>

        <div class="col-md-2 col-lg-2"><br>
          <a href="#" class="lead btn-primary btn" id="actualizar"><i class="fa fa-refresh fa-1x"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
          <a href="../../relatoriosPdf/mapaAvaliacaoDesempenhoProfessores.php?idPAno=<?php echo $idPAno ?>&tipoPessoal=naoDocente" class='lead btn-primary btn'><i class="fa fa-print"></i> Mapa</a>&nbsp;&nbsp;&nbsp;
        </div>
        <div class="col-md-5 col-lg-5">
          <strong>CAP</strong>: Capacidade de análise Profissional, <strong>CLT</strong>: Conhecimentos ligados ao trabalho <br> <strong>SP</strong>: Sigilo Profissional, <strong>RIP</strong>: Relacionamento InterPessoal, <strong>PA</strong>: Pontualidade e Assiduidade
        </div>
        <div class="col-lg-3 col-md-3 text-right"><br>
          <button class="btn btn-success lead btnAlterarNotas" style="display: none;"><i class="fa fa-check"></i> Alterar</button>
        </div>
      </div>

      <form id="formDados">
        <fieldset style="border:solid black 2px; padding-left: 15px; padding-right:15px; border-radius:10px;">
          <legend style="width:400px;"><strong>COMISSÃO DE AVALIAÇÃO</strong></legend>
          <div class="row" style="margin-top:-10px;">
            <div class="col-lg-4 col-md-4">
              <label class="lead">Coordenador</label>
              <select id="coordenador" class="form-control lead">
                <?php
                  echo "<option value=''>Seleccionar</option>";
                  foreach($entidades as $entidade){
                    echo "<option value='".$entidade["idPEntidade"]."'>".$entidade["nomeEntidade"]."</option>";
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-4 col-md-4">
              <label class="lead">Coordenador Adjunto</label>
              <select id="coordenadorAdjunto" class="form-control lead">
                <?php
                  echo "<option value=''>Seleccionar</option>";
                  foreach($entidades as $entidade){
                    echo "<option value='".$entidade["idPEntidade"]."'>".$entidade["nomeEntidade"]."</option>";
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-4 col-md-4">
              <label class="lead">1.º Vogal</label>
              <select id="vogal1" class="form-control lead">
                <?php
                  echo "<option value=''>Seleccionar</option>";
                  foreach($entidades as $entidade){
                    echo "<option value='".$entidade["idPEntidade"]."'>".$entidade["nomeEntidade"]."</option>";
                  }
                 ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-4">
              <label class="lead">2.º Vogal</label>
              <select id="vogal2" class="form-control lead">
                <?php
                  echo "<option value=''>Seleccionar</option>";
                  foreach($entidades as $entidade){
                    echo "<option value='".$entidade["idPEntidade"]."'>".$entidade["nomeEntidade"]."</option>";
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-4 col-md-4">
              <label class="lead">3.º Vogal</label>
              <select id="vogal3" class="form-control lead">
                <?php
                  echo "<option value=''>Seleccionar</option>";
                  foreach($entidades as $entidade){
                    echo "<option value='".$entidade["idPEntidade"]."'>".$entidade["nomeEntidade"]."</option>";
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-4 col-md-4">
              <label class="lead">Secretário</label>
              <select id="secretario" class="form-control lead">
                <?php
                  echo "<option value=''>Seleccionar</option>";
                  foreach($entidades as $entidade){
                    echo "<option value='".$entidade["idPEntidade"]."'>".$entidade["nomeEntidade"]."</option>";
                  }
                 ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3 col-md-3">
              <label class="lead">De</label>
              <input type="date" id="dataInicial" class="form-control lead" name="">
            </div>
            <div class="col-lg-3 col-md-3">
              <label class="lead">À</label>
              <input type="date" id="dataFinal" class="form-control lead" name="">
            </div>
          </div>
          <input type="hidden" id="idPAno" name="idPAno" value="<?php echo $idPAno; ?>">
          <input type="hidden" id="dadosEnviar" name="dadosEnviar">
          <input type="hidden" name="action" id="action" value="manipularAvaliacaoDesempenho">
        </fieldset>
      </form>

        <div class="row">
            <div class="col-lg-12 col-md-12" id="pesqUsario"><br>
              <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                  <span class="input-group-addon"><i class="fa fa-search"></i></span>
                  <input type="search" class="form-control lead pesquisaEntidade" tipoEntidade="professores" placeholder="Pesquisar..." list="listaOpcoes">
              </div>
            </div>
        </div>
        <div id="listaProfessor" class="fotFoto">

        </div>

      <div class="row" id="paraPaginacao" style="margin-top: -10px;" style="display: none;"><br>
        <div class="col-lg-12 col-md-12 col-sm-4 col-xs-4 text-right">
          <div class="form-group paginacao">

          </div>
          <button class="btn btn-success lead btnAlterarNotas" style="display: none;"><i class="fa fa-check"></i> Alterar</button>
        </div><br>
      </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
