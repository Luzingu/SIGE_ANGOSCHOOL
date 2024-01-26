<?php session_start();
  include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
  echo "<script>var caminhoRecuar='../'</script>";
  includar("../../");
  $manipulacaoDados = new manipulacaoDados("Layout e Acessos", "");
  $includarHtmls = new includarHtmls();
  $janelaMensagens = new janelaMensagens();
  $conexaoFolhas = new conexaoFolhas();
  $verificacaoAcesso = new verificacaoAcesso();
  $layouts = new layouts();
  $layouts->designacaoArea="Layout e Acessos";
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #informacoesArea p{
      color: black;
      font-size: 17pt;
      text-align: justify;
      margin-bottom: 20px;
    }
    .data{
      color: white;
      font-size: 14pt;
    }
    #tempoSistema{
      font-size: 26pt;
    }

    .cargoProfessor{
      color: white;
      font-weight: 700;
      font-size: 18px;
    }
    .nomeEscola{
      font-weight: 800;
      color:white;
    }
    #imageProfessor{
      width: 130px;
      height: 130px;
      max-height: 130px;
      max-width: 130px;
      min-width: 130px;
      min-height: 130px;
    }
    .border div{
      border: solid white 1px;
      height: 120px;
      padding:0px;
      padding: 0px;
      margin: 0px;
      padding-top: 30px;
      color: white;
      font-weight: bolder;
      font-size: 19px;
    }
    .outrasInformacoes{
      color: white;
    }
    @media (max-width: 768px) {
      #informacoesArea p{
         font-size: 14pt;
      }
       #tempoSistema{
        font-size: 20pt;
      }
     }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    $layouts->cabecalho();
    $layouts->aside(0);
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso(0, "", array(), "msg")){ ?>

          <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-lg-3 col-sm-3 text-center">
                  <h4 class="nomeEscola text-center"><?php echo valorArray($manipulacaoDados->sobreEscolaLogada, "nomeEscola"); ?></h4>
                  <div class="follow-ava text-center">
                    <img src='<?php echo "../../Ficheiros/Escola_".$_SESSION['idEscolaLogada']."/Icones/".valorArray($manipulacaoDados->sobreEscolaLogada, 'logoEscola'); ?>' class="medio imagemUsuarioCorrente" id="imageProfessor">
                  </div>
                </div>
                <div class="col-lg-3 col-sm-3 follow-info">
                  <p class="text-justify citacaoUsuarioCorente" id="citacaoProfessor"></p>
                  <h6 class="outrasInformacoes">
                    <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($manipulacaoDados->sobreEscolaLogada, "numeroTelefone") ?></strong></span>
                    <br/><br/>
                    <span class="lead"><strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($manipulacaoDados->sobreEscolaLogada, "email") ?></strong></span>
                </h6>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
                  <div class="text-center">
                    <strong class="data" id="dataHoje"><?php echo dataExtensa($manipulacaoDados->dataSistema); ?></strong><br/><br/>
                  <strong class="data"><?php echo diaSemana(date("w")); ?></strong>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 follow-info weather-category border" style="padding-top: 0px; border: none !important;">
                  <div class="text-center">
                    <strong class="text-center" id="tempoSistema"><?php echo $manipulacaoDados->tempoSistema; ?></strong>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="panel">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead"><i class="fa fa-info"></i></a>
                  </li>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content">
                  <!-- profile -->
                  <div id="profile" class="tab-pane active">
                    <div class="panel">
                      <div class="bio-graph-heading lead  col-sm-12 col-xs-12 col-lg-12 col-md-12" style="margin-bottom: 30px; font-weight: 800; color: white; font-size: 19pt;">
                          GESTÃO DAS ESCOLAS
                      </div>
                      <div class="panel-body bio-graph-info">
                        <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2" id="informacoesArea">
                          <p class="lead">Bem-vindo à <strong>Área de Gestão de Escolass.</strong></p>
                        <p class="lead">Esta área tem como administrador(a) principal, o excelentíssimo senhor <strong><?php echo $nomeProfessor; ?></strong>. Por tanto tudo que é publicado, visualizado e alterado é controlado por ele, na qualidade de ser o <strong>Coordenador do Projecto AngoSchool</strong> e coordenador da mesma área.</p>
                        </div>


                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } echo "</div>"; $includarHtmls->rodape(); ?>
        </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">
  window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaDirector/cursos/";
  }
</script>
