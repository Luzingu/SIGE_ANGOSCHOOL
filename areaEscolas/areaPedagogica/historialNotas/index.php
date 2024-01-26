<?php session_start();
    include_once '../../funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Historial de Notas", "historialNotas");
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
    $layouts->aside();
    echo "<script>var idUsuarioLogado=".$_SESSION['idUsuarioLogado']."</script>";
    $trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:"I";
    if(!($trimestre=="I" || $trimestre=="II" || $trimestre=="III" || $trimestre=="IV")){
      $trimestre="I";
    }
    echo "<script>var trimestre='".$trimestre."'</script>";
    $alteracoes_notas = array();
  ?>
  <section id="main-content"> 
        <section class="wrapper" id="containers">
           <div class="row" >
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-history"></i>
                  <?php 
                  echo "HISTORIAL DE NOTAS ";
                  if($trimestre=="I" || $trimestre=="II" || $trimestre=="III"){
                    echo "DO ".$trimestre."º TRIMESTRE";
                  }else{
                    echo "FINAL";
                  } ?></strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "historialNotas", array(), "msg")){
          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:turnaInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";

          $idPNomeDisciplina = isset($_GET["idPNomeDisciplina"])?$_GET["idPNomeDisciplina"]:"";
          echo "<script>var idPNomeDisciplina='".$idPNomeDisciplina."'</script>";

          $luzingu = explode("-", $luzingu);
          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";

          $periodo = retornarPeriodoTurma($manipulacaoDados, $idCurso, $classe, $turma);
          $semestreActivo = retornarSemestreActivo($manipulacaoDados, $idCurso, $classe);
          $alunos = $manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma);
          $tipoCurso = $manipulacaoDados->selectUmElemento("nomecursos", "tipoCurso", ["idPNomeCurso"=>$idCurso]);

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";

          $condicao["reconfirmacoes.idReconfEscola"]=$_SESSION['idEscolaLogada'];
          $condicao["escola.idMatEscola"]=$_SESSION['idEscolaLogada'];
          $condicao["reconfirmacoes.nomeTurma"]=$turma;
          $condicao["reconfirmacoes.classeReconfirmacao"]=$classe;
          $condicao["alteracoes_notas.idHistDisciplina"]=$idPNomeDisciplina;
          $condicao["alteracoes_notas.idHistEscola"]=$_SESSION['idEscolaLogada'];
          $condicao["alteracoes_notas.idHistAno"]=$manipulacaoDados->idAnoActual;
          $condicao["alteracoes_notas.trimestre"]=$trimestre;

          $condicao["reconfirmacoes.idReconfAno"]=$manipulacaoDados->idAnoActual;

          if($classe>=10){
            $condicao["escola.idMatCurso"]=$idCurso;
          }
          $alteracoes_notas = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "fotoAluno", "numeroInterno", "alteracoes_notas.nota1I", "alteracoes_notas.nota2I", "alteracoes_notas.nota3I", "alteracoes_notas.nota1F", "alteracoes_notas.nota2F", "alteracoes_notas.nota3F", "alteracoes_notas.idHistAlterador", "alteracoes_notas.dataAlteracao", "alteracoes_notas.horaAlteracao"], $condicao, ["escola", "reconfirmacoes", "alteracoes_notas"], "", [], ["nomeAluno"=>1]);
          $alteracoes_notas = $manipulacaoDados->anexarTabela2($alteracoes_notas, "entidadesprimaria", "alteracoes_notas", "idPEntidade", "idHistAlterador");

          echo "<script>var alteracoes_notas=".json_encode($alteracoes_notas)."</script>";
         ?>

    <div class="card">
        <div class="card-body"> 
        <div class="row">
          <div class="col-lg-2 col-md-2 lead">
            Turma:
             <select class="form-control" id="luzingu">   
              <?php 
                foreach ($manipulacaoDados->turmasEscola() as $tur) {
                  
                    $lDia="";
                    if($tur["classe"]>=10){
                      $lDia = $tur["abrevCurso"]." - ";
                    }
                    $lDia .= classeExtensa($tur["classe"], $tur["sePorSemestre"], "sim")." - ".$tur["designacaoTurma"];
                    echo "<option value='".$tur["nomeTurma"]."-".$tur["classe"]."-".$tur["idPNomeCurso"]."'>".$lDia."</option>";
                 
                } ?>                 
            </select>
          </div>
          <div class="col-lg-5 col-md-5 lead" id="pesqUsario">
            Disciplinas
              <select id="idPNomeDisciplina" class="form-control">
                <option value="">Seleccionar</option>
               <?php 
                foreach ($manipulacaoDados->disciplinas($idCurso, $classe, $periodo, "") as $disciplina) {

                    if($tipoCurso=="tecnico"){
                      $attr=$disciplina["disciplinas"]["continuidadeDisciplina"];
                    }else{
                      $attr = $disciplina["disciplinas"]["tipoDisciplina"];
                    }
                    echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]." (".$attr.")"."</option>";
                }
               ?>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-8 visible-md visible-lg col-md-8"></div>
          <div class="col-lg-4 col-md-4" id="pesqUsario"><br>
            <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="search" class="form-control lead pesquisaEntidade" tipoEntidade="alunos" placeholder="Pesquisar Aluno..." list="listaOpcoes">            
          </div>   
          </div> 
        </div>
      <?php echo "<script>var alteracoes_notas=".json_encode($alteracoes_notas)."</script>"; ?>
       <table id="example1" class="table table-striped table-bordered table-hover" >
          <thead class="corPrimary">
              <tr>
                <td class="lead" rowspan="2"><strong>Nome do Auno</strong></td><td class="lead" rowspan="2"><strong>Alterado pelo</strong></td><td class="lead text-center" rowspan="2" style="width: 200px;"><strong>Data da Alteração</strong></td>

                <?php if($trimestre=="IV"){ ?>
                    <td colspan="2" class="lead text-center"><strong>MEC</strong></td>
                <?php } else{ ?>
                    <td colspan="2" class="lead text-center"><strong>MAC</strong></td><td colspan="2" class="lead text-center"><strong>NPP</strong></td><td colspan="2" class="lead text-center"><strong>NPT</strong></td>
                <?php } ?>
              </tr>
              <tr>
                <?php if($trimestre=="IV"){ ?>
                  <td class="lead text-center"><strong>V. I.</strong>
                  </td>
                  <td class="lead text-center"><strong>V. F.</strong>
                  </td>
                <?php }else{ ?>
                     <td class="lead text-center"><strong>V. I.</strong>
                </td>
                <td class="lead text-center"><strong>V. F.</strong>
                </td>

                <td class="lead text-center"><strong>V. I.</strong>
                </td>
                <td class="lead text-center"><strong>V. F.</strong>
                </td>
                 <td class="lead text-center"><strong>V. I.</strong>
                </td>
                <td class="lead text-center"><strong>V. F.</strong>
                </td>
                <?php } ?>
                
               
              </tr>
            </thead>
            <tbody id="histNotas">
                
            </tbody>
          </table>
      </div>
    </div>
    </div>
    </div><br>
        <?php } echo "</div><br/><br/>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>