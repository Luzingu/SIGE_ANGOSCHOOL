<?php session_start();       
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
     include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Pesquisa de Alunos");
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

  <style type="text/css">


    .etiqueta{
      color: black;
      font-weight: 400;
    }
    .valor{
      color: black;
      font-weight: 600;
    }

    .follow-info{
      color: white;
    }

    .weather-category{
      color: white;
      padding: 2px;
    }
    .weather-category div{
      padding-top: 20px;
      border:solid white 2px;
      margin: 0px;
      min-height: 90px;
    }

    #classeCima, #numeroCima, #cursoCima, #turmaCima{
      font-size: 20pt;
    }

    #nomeAlunoPesquisado{
      font-weight: 700;
      margin-top: -5px;
      color: white;
    }
    #numeroInternoAluno{
      margin-top: 5px;
      font-weight: 300;
      margin-left: 30px;
      color: white;
    }
    #imagemAluno{
      width: 120px;
      height: 120px;
    }
    .table-responsive{
      color: rgba(0, 0, 0, 0.8) !important;
    }
</style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->headerUsuario();
    $layouts->areaEstatERelat();

    $idPMatricula = isset($_GET["aWRQTWF0cmljdWxh"])?$_GET["aWRQTWF0cmljdWxh"]:"";
    $usuariosPermitidos[] = "aRelEstatistica";

    $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";

    if($valorPesquisado!=""){
        $valorPesquisado = explode("-", $valorPesquisado);

        if(trim($valorPesquisado[0])==""){
          $valorPesquisado[0]="--";
        }

        if(!isset($valorPesquisado[1])){
          $valorPesquisado[1]="--";
        }
        $valorPesquisado[1] = trim($valorPesquisado[1]);
        $valorPesquisado[0] = trim($valorPesquisado[0]);

        $idPMatricula = $manipulacaoDados->selectUmElemento("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN escolas ON idPEscola=idMatEscola", "idPMatricula", "nomeAluno like '%".$valorPesquisado[0]."%' OR nomeAluno like '%".$valorPesquisado[1]."%' OR numeroInterno like '%".$valorPesquisado[0]."%' OR numeroInterno like '%".$valorPesquisado[1]."%' AND nomeAluno IS NOT NULL AND provincia=:provincia AND idPEscola not in (4, 7)", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia")], "nomeAluno ASC");     
    }

    $vetor = $manipulacaoDados->selectArray("alunosmatriculados", "*", "idPMatricula=:idPMatricula AND nomeAluno IS NOT NULL", [$idPMatricula]);

    $idPMatricula = valorArray($vetor, "idPMatricula");

    echo "<script>var classe=".valorArray($vetor, "classeActualAluno")."</script>";
    echo "<script>var idPCurso=".valorArray($vetor, "idMatCurso")."</script>";

    $tipoEspecialidade="B";
    if(valorArray($vetor, "classeActualAluno")>=10){
        $tipoEspecialidade="M";
     }
    

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso($usuariosPermitidos)){
          echo "<script>var idPMatricula='".valorArray($vetor, "idPMatricula")."'</script>";
         ?>
        <div class="row">
          <!-- profile-widget -->
          <div class="col-lg-12">
            <div class="profile-widget profile-widget-info">
              <div class="panel-body">
                <div class="col-md-3 col-sm-6 text-center">
                  <h4 id="nomeAlunoPesquisado"><?php echo valorArray($vetor, "nomeAluno"); ?></h4>
                  <div class="follow-ava text-center">
                    <img src="<?php echo $caminhoRetornar.'fotoUsuarios/'.valorArray($vetor, 'fotoAluno'); ?>" class="medio" id="imagemAluno">
                  </div>
                  <h3 id="numeroInterno" style="color:white;"><?php echo valorArray($vetor, "numeroInterno"); ?></h3>
                </div>
                
                <div class="col-lg-5 col-md-5 col-sm-6 follow-info">
                   <?php foreach($manipulacaoDados->selectArray("aluno_escola LEFT JOIN escolas ON idPEscola=idMatEscola LEFT JOIN nomecursos ON idPNomeCurso=idMatCurso", "*", "idFMatricula=:idFMatricula", [$idPMatricula], "estadoAluno ASC") as $a){
                  $estadoAluno=$a->estadoAluno;
                  if($estadoAluno=="A"){
                    $estadoAluno="Activo";
                  }else{
                    $estadoAluno="Inactivo";
                  }
                  $classeActualAluno=$a->classeActualAluno;
                  if($classeActualAluno==60){
                    $classeActualAluno="TEC_PRIM";
                  }else if($classeActualAluno==90){
                    $classeActualAluno="TEC_BÁSICO";
                  }else if($classeActualAluno==120){
                    $classeActualAluno="TEC_MÉDIO";
                  }
                    ?>
                  
                  <span class="lead"><i class="fa fa-map-marker-alt"></i><strong> <?php echo $a->nomeEscola." - ".$a->abrevCurso."/".$classeActualAluno." (".$estadoAluno.")"; ?></strong></span> <br/><br/>
                  <?php  } ?>
                </div>

              </div>
            </div>
          </div>
        </div>

        <form class="row" id="pesquisarAluno">
          <?php
            $valorCaixa = valorArray($vetor, 'nomeAluno').' - '.valorArray($vetor, 'numeroInterno');
            if(trim($valorCaixa)=="-"){
              $valorCaixa="";
            }

           ?>
          <div class="col-lg-10 col-md-10" id="pesqUsario">
                <input type="search" class="form-control lead"  placeholder="Pesquisar Aluno..." required id="valorPesquisado" autocomplete="off" value="<?php echo $valorCaixa; ?>">   
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
                   //Outros nomes como sugestão...
                $valorPesquisado = isset($_GET["valorPesquisado"])?$_GET["valorPesquisado"]:"";
                $valorPesquisado = explode("-", $valorPesquisado);

                if(trim($valorPesquisado[0])==""){
                  $valorPesquisado[0]="--";
                }

                if(!isset($valorPesquisado[1])){
                  $valorPesquisado[1]="--";
                }
                $valorPesquisado[1] = trim($valorPesquisado[1]);
                $valorPesquisado[0] = trim($valorPesquisado[0]);

                foreach($manipulacaoDados->selectArray("alunosmatriculados LEFT JOIN aluno_escola ON idPMatricula=idFMatricula LEFT JOIN escolas ON idPEscola=idMatEscola", "*", "nomeAluno like '%".$valorPesquisado[0]."%' OR nomeAluno like '%".$valorPesquisado[1]."%' OR numeroInterno like '%".$valorPesquisado[0]."%' OR numeroInterno like '%".$valorPesquisado[1]."%' AND nomeAluno IS NOT NULL AND provincia=:provincia AND idPEscola not in (4, 7) AND idPMatricula!='".$idPMatricula."'", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia")], "nomeAluno ASC LIMIT 10") as $a){

                    echo "<i class='fa fa-user-circle'></i> <a href='?aWRQTWF0cmljdWxh=".$a->idPMatricula."' class='lead'>".$a->nomeAluno." (".$a->numeroInterno.")</a>&nbsp;&nbsp;";
                } 
              }

             ?>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12" >
            <section class="panel" style="min-height: 500px !important;">
              <header class="panel-heading tab-bg-info">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a data-toggle="tab" href="#profile" class="lead">
                        <i class="fa fa-info-circle"></i>
                       Informações Gerais
                    </a>
                  </li>

                </ul>
              </header>
              <div class="panel-body">
                <div class="tab-content">
                  <!-- profile -->
                  <div id="profile" class="tab-pane active">
                    <div class="panel">
                      <div class="panel-body bio-graph-info" style="min-height: 200px;">
                         <div class="col-lg-6 col-md-6">
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">N.º de Telefone:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($vetor, "telefoneAluno"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">E-mail:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($vetor, "emailAluno"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nome do Pai:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="paiAluno"><?php echo valorArray($vetor, "paiAluno"); ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4 col-md-4 lead text-right lab etiqueta">Nome da Mãe:</div>
                              <div class="col-lg-8  col-md89 lead valor" id="maeAluno"><?php echo valorArray($vetor, "maeAluno"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Encarregado:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="encarregadoAluno"><?php echo valorArray($vetor, "encarregadoEducacao"); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Sexo:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="sexoAluno"><?php echo generoExtenso(valorArray($vetor, "sexoAluno")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nascido Aos:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="dataNascAluno"><?php echo dataExtensa(valorArray($vetor, "dataNascAluno")); ?></div>
                            </div>

                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Idade:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="idadeAluno"><?php echo calcularIdade(explode("-", $manipulacaoDados->dataSistema)[0], valorArray($vetor, "dataNascAluno"))." Anos"; ?></div>
                            </div>
                            <div class="row">
                              <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Província:</div>
                              <div class="col-lg-8  col-md-8 lead valor" id="provAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_provincias", "nomeProvincia", "idPProvincia=:idPProvincia", [valorArray($vetor, "provNascAluno")]); ?></div>
                            </div>                            
                          </div>

                          <div class="col-lg-6  col-md-6">

                            <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Municipio:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="municAluno"><?php echo $manipulacaoDados->selectUmElemento("div_terit_municipios", "nomeMunicipio", "idPMunicipio=:idPMunicipio", [valorArray($vetor, "municNascAluno")]); ?></div>
                            </div>
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Nº Interno:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($vetor, "numeroInterno"); ?></div>
                          </div>
                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">BI:</div>
                            <div class="col-lg-8  col-md-8 lead valor" id="biAluuno"><?php echo valorArray($vetor, "biAluno"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Emitido aos:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="dataEmitidoBI"><?php echo dataExtensa(valorArray($vetor, "dataEBIAluno")); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">Deficiência:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEspecialidade"><?php echo valorArray($vetor, "deficienciaAluno"); ?></div>
                          </div>

                          <div class="row">
                            <div class="col-lg-4  col-md-4 lead text-right lab etiqueta">P. da Deficiência:</div>
                            <div class="col-lg-8 col-md-8 lead valor" id="linguaEstrangeira"><?php echo valorArray($vetor, "tipoDeficienciaAluno"); ?></div>
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">
  $(document).ready(function(){
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/aperfilAluno/";
    $("#pesquisarAluno").submit(function(){
      window.location ="?valorPesquisado="+$("#valorPesquisado").val();
      return false;
    });
  });
</script>
