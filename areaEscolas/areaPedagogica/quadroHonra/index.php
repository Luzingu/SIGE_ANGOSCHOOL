<?php session_start(); 
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Quadro de Honra", "quadroHonra");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
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

    $trimestreDefault=1;
    if($manipulacaoDados->mes>=3 && $manipulacaoDados->mes<=5){
      $trimestreDefault=2;
    }else if($manipulacaoDados->mes>5 && $manipulacaoDados->mes<=7){
      $trimestreDefault=4;
    }

    $trimestre= isset($_GET["trimestre"])?$_GET["trimestre"]:$trimestreDefault;
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                      </a>

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><i class="fa fa-star"></i> <strong><?php 
                  if($trimestre==1 || $trimestre==2 || $trimestre==3){
                    echo "QUADRO DE HONRA DO ".$trimestre.".º TRIMESTRE";
                  }else{
                    echo "QUADRO DE HONRA";
                  }
                  ?></strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  
        if($verificacaoAcesso->verificarAcesso("", ["quadroHonra"], array(), "msg")){

            $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
            echo "<script>var idPAno=".$idPAno."</script>";

            $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:"reg--";
            echo "<script>var luzingu='".$luzingu."'</script>";
            $luzingu = explode("-", $luzingu);

            
            $numero= isset($_GET["numero"])?$_GET["numero"]:50;

            $idCurso = isset($luzingu[2])?$luzingu[2]:"";
            $classe = isset($luzingu[1])?$luzingu[1]:"";

            $condicao=["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$idPAno];

            if(trim($idCurso)!=""){
              $condicao["escola.idMatCurso"]=$idCurso;
            }
            
            if(trim($classe)!=""){
              $condicao["reconfirmacoes.classeReconfirmacao"]=$classe;
            }
            $condicao["reconfirmacoes.mfT".$trimestre]=array('$gt'=>0);

          echo "<script>var trimestre='".$trimestre."'</script>";
          echo "<script>var numero='".$numero."'</script>";

          $quadroHonra = $manipulacaoDados->selectArray("alunosmatriculados", ["reconfirmacoes.classeTurma", "nomeAluno", "numeroInterno", "abrevCurso", "reconfirmacoes.designacaoTurma", "escola.idMatCurso", "fotoAluno", "dataNascAluno", "reconfirmacoes.classeReconfirmacao", "sexoAluno", "reconfirmacoes.mfT".$trimestre], $condicao, ["reconfirmacoes", "escola"], "", [], ["mfT".$trimestre=>-1, "dataNascAluno"=>-1], $manipulacaoDados->matchMaeAlunos($idPAno, $idCurso, $classe));

          $mamaPolina=array();
          $posicao=0;
          foreach ($quadroHonra as $quadro){
            $posicao++;
            $quadro->idPMatricula = $posicao;
            $mamaPolina[]=$quadro;
            if($posicao>=$numero){
              break;
            }
          }
          $quadroHonra = $manipulacaoDados->anexarTabela2($quadroHonra, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
        echo "<script>var quadroHonra=".json_encode($mamaPolina)."</script>";
          ?>
          <div class="card">
            <div class="card-body">
              <form class="row" method="POST" id="formPesquisar">
                  <div class="col-lg-2 col-md-2 lead">
                    Ano Lectivo
                    <select class="form-control lead" id="anosLectivos">
                      <?php 
                        foreach($manipulacaoDados->anosLectivos as $ano){                      
                          echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                        }
                      ?>
                    </select>
                  </div>

                         
              <div class="col-lg-3 col-md-3 lead">
                Classe:
                  <select class="form-control lead" id="luzingu">
                    <option value="reg--">Seleccionar</option>
                      <?php retornarClassesPorCurso($manipulacaoDados, "A", "nao"); ?>                          
                  </select>
              </div>
              <div class="col-lg-2 col-md-2 lead">
                Período:
                  <select class="form-control lead" id="trimestre">
                      <option value="1">Iº Trimestre</option>
                      <option value="2">IIº Trimestre</option>
                      <option value="3">IIIº Trimestre</option>
                      <option value="4">Todo Ano</option>                          
                  </select>
              </div>
              <div class="col-lg-2 col-md-2"><br>
                <input type="number" min="3" id="numero" placeholder="Número" value="<?php echo $numero; ?>" class="form-control lead text-center" required="">
              </div>
              <div class="col-lg-1 col-md-1"><br>
                <button type="submit" class="btn-primary btn lead"><i class="fa fa-search"></i> Pesquisar</button>
              </div>
                  
              </form> 
              <div class="row">
                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                     <label class="lead">Femininos: <span class="quantidadeTotal numTMasculinos">0</span></label>
                </div>
              </div>
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
