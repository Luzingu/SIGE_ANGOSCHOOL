<?php session_start(); 
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("BACKUP DOS DADOS", "backupDosDados");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-save"></i> BACKUP DOS DADOS</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso("backup", array(), array(), "msg", "nao", (valorArray($manipulacaoDados->sobreUsuarioLogado, "BACKUP", "escola")=="V" && ($_SERVER['SERVER_NAME']=="angoschool.com" || $_SERVER['SERVER_NAME']=="angoschool.org")))){ ?>
          
        <div class="card">
          <div class="card-body">
            <h3 class="text-primary" style="text-transform:uppercase;">ÚLTIMO BAKUP: <strong><?php echo dataExtensa(valorArray($manipulacaoDados->sobreEscolaLogada, "dataBackup1")); ?></strong> - <?php echo valorArray($manipulacaoDados->sobreEscolaLogada, "horaBackup"); ?><br><br>USUÁRIO: <?php echo valorArray($manipulacaoDados->sobreEscolaLogada, "nomeUsuarioBackup"); ?></h3>

            <div class="row">
              <div class="col-lg-12 col-md-12 text-center"><br><br>
                <h3 class="text-danger">
                  <a href="https://angoschool.com/angoschool/solicitBackup?afonsoluzingu=<?php echo $_SESSION['idEscolaLogada']; ?>" id="btnBackup" style="font-size:20pt;" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Iniciar o Backup...</a>
                  <span class="text-success" id="iconeEspera" style="font-size:30pt; display: none;"><i class="fa fa-spinner fa-spin fa-5x fa-fw"></i></span>
                  <br><br><br><br>
              </div>
            </div>
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