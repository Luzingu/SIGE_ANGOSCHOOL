<?php 
    session_cache_expire(60);
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Resumo de Notas - Professor", "miniPautasProfessor33");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = 2;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->retornarAnosEmJavascript();
 ?>

 <!DOCTYPE html>
<html lang="pt">
<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #listaAlunos form{
      border-bottom:solid rgba(0, 0, 0, 0.2) 2px;
      padding-top: 10px;
      padding-bottom: 20px;
    }
    #listaAlunos form input.valorCt{
        background-color: transparent;
        color: black;
        font-weight: 700;
    }
    #tabela tr td, #tabela tr th{
      font-size: 12pt !important;
      vertical-align: middle;
    }


  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(2);
  ?>
  <section id="main-content"> 
        <section class="wrapper" id="containers">
           <div class="row" >
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-history"></i> MINI-PAUTAS</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso(2, array(), array(), "msg")){

          $listaDisciplinas = $array = $manipulacaoDados->selectArray("divisaoprofessores", [], ["idDivAno"=>$manipulacaoDados->idAnoActual, "idPEntidade"=>$_SESSION['idUsuarioLogado'], "idPEscola"=>$_SESSION['idEscolaLogada']], [], "", []);
          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:(valorArray($listaDisciplinas, "idPNomeCurso")."-".valorArray($listaDisciplinas, "classe")."-".valorArray($listaDisciplinas, "nomeTurmaDiv")."-".valorArray($listaDisciplinas, "idPNomeDisciplina"));
          
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);

          $idCurso = isset($luzingu[0])?$luzingu[0]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $turma = isset($luzingu[2])?$luzingu[2]:"";
          $idPNomeDisciplina = isset($luzingu[3])?$luzingu[3]:"";

          $semestre = retornarSemestreActivo($manipulacaoDados, $idCurso, $classe);
          echo "<script>var classe='".$classe."'</script>";

          echo "<script>var listaAlunos=".json_encode($manipulacaoDados->miniPautas($idCurso, $classe, $turma, array(), $idPNomeDisciplina, "pautas", $manipulacaoDados->idAnoActual, ["pautas.mtI", "pautas.mtII", "fotoAluno", "sexoAluno", "pautas.mtIII", "pautas.mfd", "nomeAluno", "numeroInterno"], $semestre))."</script>";
          ?>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-5 col-md-5 lead" id="pesqUsario">
            Disciplinas
              <select id="luzingu" name="referenciaDisciplina" class="form-control">
               <?php 
                foreach ($array as $divisao){

                 if((nelson($divisao, "sePorSemestre")=="sim" && $trimestre=="I") || nelson($divisao, "sePorSemestre")!="sim"){

                    $sobreCurso = nelson($divisao, "abrevCurso")."/";
                    echo "<option value='".(nelson($divisao, "idPNomeCurso")."-".$divisao["classe"]."-".$divisao["nomeTurmaDiv"]."-".$divisao["idPNomeDisciplina"])."'>".$sobreCurso.classeExtensa($manipulacaoDados, $divisao["idPNomeCurso"], $divisao["classe"])."/".$divisao["designacaoTurmaDiv"]." - ".$divisao["nomeDisciplina"]."</option>";
                  }
                }
               ?>
            </select>
          </div>
          <div class="col-lg-7 col-md-7" id="pesqUsario"><br>
            <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="search" class="form-control lead pesquisaEntidade" tipoEntidade="alunos" placeholder="Pesquisar Aluno..." list="listaOpcoes">            
            </div>   
          </div>
        </div>
        <div class="row">
          <div class="col-lg-4 col-md-4 lead">
            <h4>Total de Alunos: <strong id="numTotAlunos">0</strong></h4>
            <h4>Femininos: <strong id="numTotFeminino">0</strong></h4>
            <h4>Aprovados: <strong id="numTotAprovado">0</strong></h4>
          </div>
          <div class="col-lg-8 col-md">
            <a class="btn-primary btn" href="<?php echo "../../relatoriosPdf/pautas/miniPautas.php?turma=".$turma."&classe=".$classe."&idPCurso=".$idCurso."&trimestreApartir=IV&idPDisciplina=".$idPNomeDisciplina."&semetre=I";?>" id="visualizarMiniPauta"><i class="fa fa-print"></i> Mini-Pauta</a>
          </div>
        </div>
      <div class="table-responsive">
        <table id="example1" class="table table-striped table-bordered table-hover" >
          <thead class="corPrimary">
              <tr>
                <td class="lead text-center"><strong>N.º</strong></td>
                <td class="lead"><strong>Nome do Auno</strong></td>
                <td class="lead text-center"><strong>MT1</strong></td>
                <td class="lead text-center"><strong>MT2</strong></td>
                <td class="lead text-center"><strong>MT3</strong></td>
                <td class="lead text-center"><strong>MFD</strong></td>                
              </tr>
            </thead>
            <tbody id="histNotas">
                
            </tbody>
          </table>
        </div>
      </div>
    </div>
    </div>
    </div>
        <?php } echo "</div><br/><br/>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>