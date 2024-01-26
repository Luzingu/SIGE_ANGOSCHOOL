<?php session_start(); 
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("DOWNLOAD ARQUIVOS", "downloadArquivos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();

    $idPArea = isset($_GET["idPArea"])?$_GET["idPArea"]:2;

    $areas = $manipulacaoDados->selectArray("areas", ["designacaoArea"], ["idPArea"=>$idPArea, "instituicoes.idEscola"=>$_SESSION['idEscolaLogada']], ["instituicoes"]);

    if($_SESSION['tipoUsuario']=="aluno"){
      echo "<script>window.location='areaAluno/index.php'</script>";
    }else if(count($areas)<=0 || $idPArea==7 || $idPArea==8 || $idPArea==1){
      $areas = $manipulacaoDados->selectArray("areas", ["designacaoArea", "idPArea"], ["idPArea"=>array('$nin'=>[1,2,7,8]), "instituicoes.idEscola"=>$_SESSION['idEscolaLogada']], ["instituicoes"], 1);
      $idPArea = valorArray($areas, "idPArea");
    }
    $layouts->idPArea = $idPArea;
    $layouts->designacaoArea=valorArray($areas, "designacaoArea");
    $manipulacaoDados->retornarAnosEmJavascript();
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-save"></i> DOWNLOAD DE FOTOS</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso("backup", array(), array(), "msg", "nao", (valorArray($manipulacaoDados->sobreUsuarioLogado, "BACKUP", "escola")=="V" && ($_SERVER['SERVER_NAME']=="angoschool.com" || $_SERVER['SERVER_NAME']=="angoschool.org")))){ ?>
          
        <div class="card">
          <div class="card-body">

            <form class="row" method="POST" action="https://angoschool.com/angoschool/downloadArquivos.php">
              <h4 class="text-center">Selecciona a data</h4>
              <div class="col-lg-3 col-md-3 visible-md visible-lg"></div>
              <div class="col-lg-2 col-md-2">
                <label>De</label>
                <input type="date" value="<?php echo $manipulacaoDados->adicionarDiasData(-7, $manipulacaoDados->dataSistema); ?>" class="form-control" id="dataInicial" name="dataInicial">
              </div>
              <div class="col-lg-2 col-md-2">
                <label>Até</label>
                <input type="date" value="<?php echo $manipulacaoDados->dataSistema; ?>" class="form-control" id="dataFinal" name="dataFinal">
              </div>
              <div class="col-lg-2 col-md-2"><br>
                <button type="submit" class="btn btn-success" name="btnSubmit"><i class="fa fa-send"></i> Fazer download</button>
              </div>
              <input type="hidden"  name="idUsuarioLogado" value="<?php echo $_SESSION['idUsuarioLogado']; ?>">
              <input type="hidden"  name="idEscolaLogada" value="<?php echo $_SESSION['idEscolaLogada']; ?>">
            </form>

          </div>
        </div>

    

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs();$janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">
  
  $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaDirector/backupDados/";    
  })   
</script>

<?php 
  if(isset($_POST["btnSubmit"])){

    echo "OK";
  }
  
 ?>