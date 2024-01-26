<?php session_start();  
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Relatório de Pagamentos de Propinas", "alunosJaEfectuaramPagamentos77");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
          #formularioPagamento .modal-dialog{
            width: 60%;
            margin-left: -30%;
        }

        @media (max-width: 768px) {
            #formularioPagamento .modal-dialog{
                width: 94%;
                margin-left: 3%;
            }
             #formularioPagamento .modal-dialog .lab{
                text-align: left;
             }
        }
      </style>
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
                  <h1 class=" navbar-brand" style="color: white;"><strong><i class="fa fa-info-circle"></i> Control de Pagamentos</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "alunosJaEfectuaramPagamentos77", array(), "msg")){

          $mesPorDefeito = ($manipulacaoDados->mes-1);
          if($mesPorDefeito==0){
            $mesPorDefeito=12;
          }else if($mesPorDefeito==8){
            $mesPorDefeito=9;
          }

          $mes = isset($_GET["mes"])?$_GET["mes"]:$mesPorDefeito;
          $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
          $idPNomeCurso =  isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"]);
          echo "<script>var mes =".$mes."</script>";
          echo "<script>var idPAno=".$idPAno."</script>";
          echo "<script>var idPNomeCurso='".$idPNomeCurso."'</script>";

          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["pagamentos.idHistoricoAno", "pagamentos.idHistoricoEscola", "pagamentos.idTipoEmolumento", "pagamentos.referenciaPagamento", "nomeAluno", "numeroInterno", "reconfirmacoes.nomeTurma", "reconfirmacoes.designacaoTurma", "reconfirmacoes.classeReconfirmacao", "fotoAluno"], ["reconfirmacoes.idReconfAno"=>$idPAno, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idMatCurso"=>$idPNomeCurso, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada']], ["reconfirmacoes"], "", [], array("nomeAluno"=>1));
          $alunosDevedores = array();
          foreach ($array as $aluno) {
            if(count(listarItensObjecto($aluno, "pagamentos", ["idHistoricoAno=".$idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1", "referenciaPagamento=".$mes]))>0){
              $alunosDevedores[]=$aluno;
            }
          }
          echo "<script>var listaAlunos=".json_encode($alunosDevedores)."</script>";
          

          ?>
      <div class="card">
        <div class="card-body"> 
          <div class="row">
            <div class="col-lg-2 col-md-2 lead">
            Ano Lectivo
              <select class="form-control " id="anosLectivos">
                <?php 
                  foreach($manipulacaoDados->anosLectivos as $ano){                     
                    echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                  }
                 ?>
              </select>
            </div>

            <div class="col-lg-2 col-md-2 lead">
                Curso
              <select class="form-control " id="idPNomeCurso">
                <?php 
                  foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "abrevCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"], "", [], ["ordenacao"=>1]) as $curso){
                    echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["abrevCurso"]."</option>";
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-2 col-md-2 lead">
              Mês
                <select class="form-control " name="mesAtraso" id="mesAtraso">
                  <?php 

                foreach($manipulacaoDados->mesesAnoLectivo as $i){ ?>
                  <option class="" value="<?php echo $i; ?>"><?php echo nomeMes($i); ?></option>
               <?php } ?>
                </select>
            </div>
          <div class="col-md-2 col-lg-2 "><br>
            <label class="">Total: <span id="totContas" class="quantidadeTotal">0</span></label>&nbsp;&nbsp;&nbsp;
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12 col-md-12  text-right"><br>
            <a href="../../relatoriosPdf/relatoriosFinanceiros/listaPagPropinasDoMes.php?idPAno=<?php echo $idPAno ?>&mes=<?php echo $mes; ?>&idPNomeCurso=<?php echo $idPNomeCurso; ?>" class="btn btn-primary "><i class="fa fa-print"></i> Lista</a>&nbsp;&nbsp;&nbsp;
 
            <a href="../../relatoriosPdf/relatoriosFinanceiros/listaGeralDePagPropinas.php?idPAno=<?php echo $idPAno ?>&mes=<?php echo $mes; ?>&idPNomeCurso=<?php echo $idPNomeCurso; ?>" class="btn btn-primary "><i class="fa fa-print"></i> Lista Geral</a>&nbsp;&nbsp;&nbsp;

            <a href="../../relatoriosPdf/relatoriosFinanceiros/listaPagPropinasPorTurma.php?idPAno=<?php echo $idPAno ?>&mes=<?php echo $mes; ?>&idPNomeCurso=<?php echo $idPNomeCurso; ?>" class="btn btn-primary "><i class="fa fa-print"></i> Lista por Turma</a>
          </div>
        </div>

        <table id="example1" class="table table-striped table-bordered table-hover" >
          <thead class="corPrimary">
              <tr>
                  <th class=" text-center"><strong>N.º</strong></th>
                  <th class=""><strong>Nome do Aluno</strong></th>
                  <th class=" font-weight-bolder text-center"><strong>N.º Interno</strong></th>
                   <th class=" text-center"><strong>Classe</th>
                   <th class=" text-center"><strong>Turma</th>

              </tr>
          </thead>
          <tbody>
              
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