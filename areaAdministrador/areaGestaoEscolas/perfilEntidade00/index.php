<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pesquisa de Funcionários", "perfilEntidade00");
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
  <style type="text/css">
    #citacaoProfessor{
      font-style: italic;
      color: white;
      font-weight: 800;
      color: orange;
    }
    .valor{
      font-weight: 800;
    }
    .informPerfil{
      font-weight: 1000;
    }
    .cargoProfessor{
      color: white;
      font-weight: 700;
      font-size: 18px;  
    }
    .nomeUsuarioCorente{
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
      padding-top: 50px;
      color: white;
      font-weight: bolder;
      font-size: 19px;
    }
    .outrasInformacoes{
      color: white;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();     
    $layouts->aside();
    
    $usuariosPermitidos[] = "Administradores";
    $idPProfessor = isset($_GET["aWRQUHJvZmVzc29y"])?$_GET["aWRQUHJvZmVzc29y"]:"";
    $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";

    if($valorPesquisado!=""){

      $condicoesPesquisa = [array("nomeEntidade"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("nomeEntidade"=>new \MongoDB\BSON\Regex(ucwords($valorPesquisado))), array("biEntidade"=>new \MongoDB\BSON\Regex($valorPesquisado)), array("numeroInternoEntidade"=>new \MongoDB\BSON\Regex($valorPesquisado))];

        $array = $manipulacaoDados->selectArray("entidadesprimaria", ["idPEntidade"], ['$or'=>$condicoesPesquisa], [], 1, [], ["nomeEntidade"=>1]);

        $idPProfessor = valorArray($array, "idPEntidade");
    }
  

    $array = $manipulacaoDados->selectArray("entidadesprimaria", [], ["idPEntidade"=>$idPProfessor]);
    $array = $manipulacaoDados->anexarTabela($array, "div_terit_provincias", "idPProvincia", "provNascEntidade");
    $array = $manipulacaoDados->anexarTabela($array, "div_terit_municipios", "idPMunicipio", "municNascEntidade");

    echo "<script>var idPProfessor='".valorArray($array, "idPEntidade")."'</script>";

    $cargoExtenso = valorArray($array, "nivelSistemaEntidade");
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
    <?php if($verificacaoAcesso->verificarAcesso("", "perfilEntidade00", array(), "msg")){ ?>
      
        <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-lg-3 col-sm-3 text-center">
                  <h4 class="nomeUsuarioCorente text-center"><?php echo valorArray($array, "nomeEntidade"); ?></h4>
                  <div class="follow-ava text-center">
                    <img src="<?php echo $caminhoRetornar.'fotoUsuarios/'.valorArray($array, 'fotoEntidade'); ?>" class="medio imagemUsuarioCorrente" id="imageProfessor">
                  </div>
                  <h6 class="cargoProfessor text-center">
                    <?php echo valorArray($array, "numeroInternoEntidade"); ?>
                  </h6>

                </div>
                <div class="col-lg-9 col-sm-9 follow-info">
                  <p class="text-justify citacaoUsuarioCorente" id="citacaoProfessor"><?php echo valorArray($array, "citacaoFavoritaEntidade"); ?></p>
                  <h6 class="outrasInformacoes">
                    <span class="lead"><i class="fa fa-phone"></i> <strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($array, "numeroTelefoneEntidade");?></strong></span><br/><br/>

                    <span class="lead"><strong class="numeroTelefone" id="telProfessor"><?php echo valorArray($array, "emailEntidade");?></strong></span>
                    <?php 
                        $dataSaida = strtotime($manipulacaoDados->dataSistema.$manipulacaoDados->tempoSistema." - 600 seconds");

                    if(count($manipulacaoDados->selectArray("entidadesonline", [], ["idUsuarioLogado"=>$idPProfessor, "estadoExpulsao"=>"A", "dataSaida"=>date("Y-m-d", $dataSaida), "horaSaida"=>array('$gt'=>date("H:i:s", $dataSaida))]))>0){ ?>

                        <span class="lead text-success"><strong><i class="fa fa-rss"></i></strong></span><br/>

                    <?php  } ?>
                </h6>
                </div>

              </div>
            </div>
          </div>
        </div>

        <form class="row" id="pesquisarAluno">
          <?php $valor =valorArray($array, 'numeroInternoEntidade');
           ?>
          <div class="col-lg-10 col-md-10" id="pesqUsario">
                <input type="search" class="form-control lead" value="<?php echo $valor; ?>"  placeholder="Pesquisar Professor..." required list="listaOpcoes" id="valorPesquisado" autocomplete="off"  tipoEntidade="professores" >   
              </div>
          <div class="col-lg-2 col-md-2">
            <button type="submit" class="form-control lead btn-primary"><i class="fa fa-search"></i> Pesquisar</button>
          </div>
          <input type="hidden" name="action" value="pesquisarAluno">
        </form>
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <?php 
              if(isset($_GET["valorPesquisado"])){               

                foreach($manipulacaoDados->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "numeroInternoEntidade"], ['$or'=>$condicoesPesquisa, "idPEntidade"=>array('$ne'=>$idPProfessor)], [], 10) as $a){
                    echo "<i class='fa fa-user-circle'></i> <a href='?aWRQUHJvZmVzc29y=".$a["idPEntidade"]."' class='lead'>".$a["nomeEntidade"]." (".$a["numeroInternoEntidade"].")</a>&nbsp;&nbsp;";
                } 
              }

             ?> 
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12">
            <div class="panel">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead">
                                          <i class="fa fa-user"></i>
                                         Perfil
                                      </a>
                  </li>
                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content"> 
                  <!-- profile -->
                   <div id="profile" class="tab-pane active">
                    <div class="panel" style="min-height: 200px;">
                      <div class="bio-graph-heading lead col-sm-12 col-xs-12 col-lg-12 col-md-12" id="acercaUsuarioCorente" style="margin-bottom: 30px;">
                        <?php echo valorArray($array, "acercaEntidade"); ?>
                      </div>
                         
                          <div class="col-lg-12  col-md-12">
                            <?php 
                            $escolas = listarItensObjecto($array, "escola", []);
                            $escolas = $manipulacaoDados->anexarTabela($escolas, "escolas", "idPEscola", "idEntidadeEscola");
                            foreach($escolas as $a){ ?>
        
                            <h2 class="lead"><i class="fa fa-map-marker-alt"></i><strong> <?php echo $a["nomeEscola"]." (".$a["estadoActividadeEntidade"].") - ".$a["nivelSistemaEntidade"]; ?></strong></h2>
                            <?php  } ?>
                            
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
<?php $conexaoFolhas->folhasJs(); ?>
<script type="text/javascript" src="script.js"></script>
<?php $includarHtmls->formTrocarSenha(); $janelaMensagens->funcoesDaJanelaJs(); ?>

<script type="text/javascript">
  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = caminhoRecuar+"areaGestaoEscolas/perfilEntidade/";
       $("#pesquisarAluno").submit(function(){
          window.location ="?valorPesquisado="+$("#valorPesquisado").val();
          return false;
       })
  }
</script>
