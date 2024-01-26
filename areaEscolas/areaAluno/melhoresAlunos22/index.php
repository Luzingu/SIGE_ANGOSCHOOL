<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Quadro de Honra", "melhoresAlunos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea =1;
    $layouts->designacaoArea ="Área do Aluno";
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
    $layouts->aside(1);

    if(isset($_GET["trimestre"])){
        $trimestre= $_GET["trimestre"];
    }else{
        $trimestre="1";
    }
    if($trimestre<1 || $trimestre>4){
      $trimestre=1;
    }
    $numero = isset($_GET["numero"])?$_GET["numero"]:50;

  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-user-md"></i> QUADRO DE HONRA <?php if($trimestre==1){
                        echo " - I.º TRIMESTRE";
                      }else if($trimestre==2){
                        echo " - II.º TRIMESTRE";
                      }else if($trimestre==3){
                        echo " - III.º TRIMESTRE";
                      }else{
                        echo " - ".$manipulacaoDados->numAnoActual;
                      } ?></strong></h1>

               
              </nav>
            </div>
          </div>
        <div class="main-body">
        <?php  
        if($verificacaoAcesso->verificarAcesso(1)){

          echo "<script>var trimestre='".$trimestre."'</script>";
          echo "<script>var numero='".$numero."'</script>";
          echo "<script>var numeroInterno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "numeroInterno")."'</script>";
          
          echo "<script>var numero='".$numero."'</script>";
          echo "<script>var idPAno='".$manipulacaoDados->idAnoActual."'</script>";

          $quadroHonra = $manipulacaoDados->selectArray("alunosmatriculados", ["reconfirmacoes.classeReconfirmacao", "nomeAluno", "numeroInterno", "idPMatricula", "reconfirmacoes.designacaoTurma", "escola.idMatCurso", "fotoAluno", "dataNascAluno", "sexoAluno", "reconfirmacoes.mfT".$trimestre], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada']], ["reconfirmacoes", "escola"], "", [], ["mfT".$trimestre=>-1, "dataNascAluno"=>-1], $manipulacaoDados->matchMaeAlunos($manipulacaoDados->idAnoActual));

          $posicao=0;
          $mamaPolina=array();
          foreach ($quadroHonra as $quadro){
            $posicao++;
            $mamaPolina[]=$quadro;
            $quadro->idPMatricula = $posicao;
            
            if($posicao>=50){
              break;
            }
          }
          $mamaPolina = $manipulacaoDados->anexarTabela2($mamaPolina, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");

        echo "<script>var quadroHonra=".json_encode($mamaPolina)."</script>";
          ?>
                    <div class="card">
                      <div class="card-body">
                        <form class="row" method="POST" id="formPesquisar">
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6 lead text-center"><strong>Top</strong>
                          <input type="number" min="3" id="numero" placeholder="Número" value="<?php echo $numero; ?>" class="form-control lead text-center" required="">
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-6"><br>
                          <button type="submit" class="btn-primary btn lead"><i class="fa fa-search"></i> Pesquisar</button>
                        </div>
                        <div class="col-md-8 col-lg-8 col-sm-12 col-xs-12"><br>
                               <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
                          </div>
                            
                        </form> 

                        <table id="example1" class="table table-striped table-bordered table-hover" >
                          <thead class="corPrimary">
                                <tr>
                                    <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                                    <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                                    <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>                                    
                                    <th class="lead text-center"><strong>Curso</strong></th>
                                    <th class="lead text-center"><strong>Classe</strong></th>
                                    <th class="lead text-center"><strong>Turma</strong></th>
                                    <th class="lead text-center"><strong>Média</strong></th>
                                    <th class="lead text-center"><strong>Idade</strong></th>
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
