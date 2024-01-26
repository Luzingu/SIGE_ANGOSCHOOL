<?php 
    session_cache_expire(60);
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../");
    $manipulacaoDados = new manipulacaoDados("Control de Faltas - Professor", "controlFaltas33");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = 2;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .forTurmaProfessor{
      background-color: #428bca; !important;
      min-height: 140px;
      color: white;
      border: solid rgba(255, 255, 255, 0.3) 2px;
      border-radius: 5px;
      padding: 5px;
      margin-bottom: 20px;
    }

    .forTurmaProfessor .nomeClasse{
      font-weight: 300;

    }
    .nomeDisciplina{
      font-size: 16pt;
    }
    .periodoTurma{
      margin-top: 15pt;
      color: rgba(255, 255, 255, 0.6);
    }

    .forTurmaProfessor .nomeTurma{
      font-size: 26pt;
      font-weight: 700;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside (2);
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
      <div class="row" >
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-times"></i> Control de Faltas</strong></h1>
        </nav>
      </div>
    </div>
    <div class="main-body">
    <?php  if($verificacaoAcesso->verificarAcesso(2, array(), array(), "msg")){

      $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:$manipulacaoDados->ano;
      echo "<script>var anoCivil='".$anoCivil."'</script>";

      $controlPresenca =listarItensObjecto($manipulacaoDados->sobreUsuarioLogado, "controlPresenca", ["idEscola=".$_SESSION['idEscolaLogada']]);
    ?>
    <div class="row">
      <div class="col-lg-2 col-md-2">
        <label>Ano Lectivo</label>
        <select class="form-control lead" id="anoCivil">
          <?php 
            for($i=$manipulacaoDados->ano; $i>=2022; $i--){
              echo "<option>".$i."</option>";
            }

           ?>
        </select>
      </div>
      <div class="col-lg-10 col-md-10"><br>
        <a href="../../relatoriosPdf/relatoriosProfessores/mapaDeControlFaltasDoProfessor.php?anoCivil=<?php echo $anoCivil; ?>" class="btn lead btn-primary"><i class="fa fa-print"></i> Mapa</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-bordered table-hover" >
          <thead>
              <tr class="corPrimary">
                  <th class="lead text-center" rowspan="2"></th>
                  <?php for($i=1; $i<=12; $i++){ ?>
                    <th class="lead text-center" colspan="2"><strong><?php echo nomeMes2($i); ?></strong></th>
                  <?php } ?>
              </tr>
              <tr class="corPrimary">
                  <?php for($i=1; $i<=12; $i++){ ?>
                    <th class="lead text-center"><strong>P</strong></th>
                    <th class="lead text-center"><strong>F</strong></th>
                  <?php } ?>
              </tr>
              </thead>
          <tbody>
            <?php
            for($dia=1;$dia<=31;$dia++){ 
              echo "<tr><td class='text-center'>".completarNumero($dia)."</td>";
              for($mes=1; $mes<=12; $mes++){
                  
                  $data=$anoCivil."-".completarNumero($mes)."-".completarNumero($dia);
                  $semana = date("w", strtotime($data));

                  $faltas="";
                  $presencas="";

                  $contFaltas=0;
                  $contAusenciasSemImplicacao=0;

                  if($semana!=0){
                    foreach($controlPresenca as $presenca){
                      if($presenca["data"]==$data && $data<=$manipulacaoDados->dataSistema){
                        $faltas = isset($presenca["faltas"])?$presenca["faltas"]:"";
                        $presencas = isset($presenca["presencas"])?$presenca["presencas"]:"";
                        break;
                      }
                    }
                  }
                echo "<td class='text-center'><strong class='text-success'>".$presencas."</strong></td><td class='text-center'><strong class='text-danger'>".$faltas."</strong></td>";
              }
              echo "</tr>";
            }

             ?>
              
          </tbody>
      </table>
    </div>

      

       <?php } echo "</div><br/>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

