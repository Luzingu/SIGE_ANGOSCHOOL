<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Avaliação Final de Curso", "avalFinalCurso");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
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
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-user-md"></i> Avaliação Final do Curso</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  
        

        if($verificacaoAcesso->verificarAcesso("", "avalFinalCurso", array(), "msg")){

          if(isset($_GET["luzingu"])){
            $luzingu = $_GET["luzingu"];
          }else{
            $miro = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "duracao"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>['$in'=>array("tecnico", "pedagogico")], "cursos.estadoCurso"=>"A"], ["cursos"], 1, [], ["nomeCurso"=>1]);

            $luzingu = "reg-".$manipulacaoDados->ultimaClasse(valorArray($miro, "idPNomeCurso"))."-".valorArray($miro, "idPNomeCurso");
          }

          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = $luzingu[2];
          $classe = $luzingu[1];
          $periodo = $luzingu[0];

          $miro =$manipulacaoDados->selectArray("nomecursos", ["tipoCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>['$in'=>array("tecnico", "pedagogico")], "idPNomeCurso"=>$idCurso, "cursos.estadoCurso"=>"A"], ["cursos"], 1, [], ["nomeCurso"=>1]);

          $tipoCurso = valorArray($miro, "tipoCurso");

          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";          
          echo "<script>var tipoCurso='".$tipoCurso."'</script>";
          $classeOriginalAluno = $classe;

          $condicao["escola.idMatEscola"]=$_SESSION['idEscolaLogada'];
          $condicao["escola.idMatCurso"]=$idCurso;

          $expl = explode("_", $classe);

          if(count($expl)>1){
            $condicao["escola.idMatFAno"]=$expl[1];
          }else{
            $condicao["escola.classeActualAluno"]=$classe;
          }
          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "idPMatricula", "escola.notaExposicaoW", "escola.notaAvalTrabEscrito", "escola.notaEstagio", "escola.notaRelatorioEstagio", "escola.numeroActa", "escola.numeroFolha", "escola.dataDefesa", "escola.horaDefesa", "escola.membrosJuriDefesa", "grupo", "escola.temaTrabalho", "escola.casoPratico", "escola.dataConclusaoCurso", "escola.numeroLivroRegistro", "escola.numeroFolhaRegistro", "escola.numeroPauta", "escola.provAptidao", "escola.notaEstagio"], $condicao, ["escola"], "", [], ["nomeAluno"=>1]); 

          echo "<script>var listaAlunos=".json_encode($array)."</script>";



          ?>

        
 
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-3 col-md-3 lead">
                  Classe:
                  <select class="form-control lead" id="luzingu" name="luzingu">
                  <?php 

                    if(!(isset($_SESSION['subVariable']) && $_SESSION['subVariable']!="")){
                      $classes="";
                      $periodos[]="reg";
                      if($_SESSION["periodosEscolas"]=="regPos"){
                        $periodos[]="pos";
                      }

                      $listaCursos = $manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "abrevCurso", "duracao"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "tipoCurso"=>['$in'=>array("tecnico", "pedagogico")], "cursos.estadoCurso"=>"A"], ["cursos"], "", [], ["nomeCurso"=>1]);
                      $_SESSION['subVariable']=""; 
                      foreach ($periodos as $p) {
                        foreach ($listaCursos as $c) {
                          $ultimaClasse = $manipulacaoDados->ultimaClasse($c["idPNomeCurso"]);
                          $_SESSION['subVariable'] .="<option value='".$p."-".$ultimaClasse."-".$c["idPNomeCurso"]."'>".$c["abrevCurso"]." - ".classeExtensa($manipulacaoDados, $c["idPNomeCurso"], $ultimaClasse)." - ".periodoExtenso($p)."</option>";
                        }
                      }

                      $_SESSION['subVariable'] .="<optgroup label='Finalista'>";

                       foreach ($periodos as $p) {
                        foreach ($listaCursos as $c) {

                          foreach ($manipulacaoDados->selectDistinct("alunosmatriculados", "escola.idMatFAno", ["escola.idMatEscola"=>$_SESSION["idEscolaLogada"], "escola.periodoAluno"=>$p, "escola.idMatCurso"=>$c["idPNomeCurso"], "escola.idMatFAno"=>['$ne'=>""]], ["escola"]) as $ano) {
                      
                            $_SESSION['subVariable'] .="<option value='".$p."-FIN_".$ano["_id"]."-".$c["idPNomeCurso"]."'>".$c["abrevCurso"]." - ".$manipulacaoDados->selectUmElemento("anolectivo", "numAno", ["idPAno"=>$ano["_id"]])." - ".periodoExtenso($p)."</option>";
                          }
                        }
                      }
                      $_SESSION['subVariable'] .="</optgroup>";
                    }
                    echo $_SESSION['subVariable'];

                  ?>  
                  </select>
              </div>

            <div class="col-md-8 col-lg-8"><br><label class="lead">
                    Total de Alunos: <span class="quantidadeTotal" id="numTAlunos">0</span>
                </label>&nbsp;&nbsp;&nbsp;
                 <label class="lead">Femininos: <span class="quantidadeTotal " id="numTMasculinos">0</span></label>
            </div>
        </div>
          <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                <tr>
                    <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i></strong></th>
                    <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome do Aluno</strong></th>
                    <th class="lead text-center"><strong>PAP</strong></th>
                    <th class="lead text-center"><strong>NEC</strong></th>
                    <th class="lead text-center"><strong>Data de Conclusão</strong></th>
                    <th class="lead text-center"></th>
                    <th class="lead text-center"></th>
                </tr>
              </thead>
              <tbody id="tabela">

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



<div class="modal fade" id="formularioCadastro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="avalFinalCursoF">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-md"></i> Avaliação Final do Curso</h4>
              </div>

              <div class="modal-body">

                <div class="row">
                  <div class="col-lg-9 col-md-9 lead">
                    Nome do Aluno:
                    <input type="text" style="font-weight: bolder; background-color: white !important;" class="form-control lead" id="nomeAluno" readonly>
                  </div>
                  <div class="col-lg-3 col-md-3 lead text-center">
                    Expos. de Trab.(PAP)
                    <input type="number" min="0" max="10" step="0.01" name="notaExpoTrabalho" class="form-control text-center lead" id="notaExpoTrabalho">
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-3 lead text-center">
                    Trab. Escrito (PAP)
                    <input type="number" min="0" max="10" step="0.01" name="notaTrabEscrito" class="form-control text-center lead" id="notaTrabEscrito">
                  </div>
                  <div class="col-lg-3 col-md-3 lead text-center">
                    Relat. do Estágio
                    <input type="number" min="0" max="20" step="1" name="notaRelatorioEstagio" class="form-control text-center lead" id="notaRelatorioEstagio">
                  </div>
                  <div class="col-lg-3 col-md-3 lead text-center">
                    Nota do Estágio
                    <input type="number" min="0" max="20" step="1" name="notaEstagio" class="form-control text-center lead" id="notaEstagio">
                  </div>
                  <div class="col-lg-3 col-md-3 lead text-center">
                    N.º da Acta
                    <input type="text" name="numeroActa" class="form-control text-center lead" id="numeroActa">
                  </div>
                </div>
                <div class="row">
                  
                  <div class="col-lg-3 col-md-3 lead text-center">
                    N.º da Folha
                    <input type="number" name="numeroFolha" class="form-control text-center lead" id="numeroFolha">
                  </div> 
                  <div class="col-lg-3 col-md-3 lead text-center">
                    Data de Defesa
                    <input type="date" name="dataDefesa" class="form-control text-center lead" id="dataDefesa">
                  </div>
                  <div class="col-lg-3 col-md-3 lead text-center">
                    Hora
                    <input type="text" name="horaDefesa" class="form-control text-center lead" id="horaDefesa" placeholder="H:M:S">
                  </div>
                  <div class="col-md-3 col-md-3 lead text-center">
                    Data de Concl.
                    <input type="date" name="dataConclusao" class="form-control text-center lead" id="dataConclusao">
                  </div>                     
                </div>
                <div class="row">
                  <div class="col-md-12 lead">
                    Caso Prático
                    <input type="text" name="casoPratico" class="form-control lead" id="casoPratico">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 lead">
                    Tema do Trabalho
                    <textarea name="temaTrabalho" class="form-control lead" id="temaTrabalho" style="max-width: 100%; min-width: 100%; height:100px;"></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 lead">
                    Membros do Júri
                    <textarea name="membrosJuri" class="form-control lead" id="membrosJuri" placeholder="Separar entre ponto e vírgula (;)" style="height:60px;"></textarea>
                  </div>
                </div> 
                <input type="hidden" name="idPMatricula" id="idPMatricula"  value="">
                <input type="hidden" name="grupoAluno" id="grupoAluno"  value="">

                <input type="hidden" name="idPCurso" id="idPCurso"  value="<?php echo $idCurso; ?>">
                <input type="hidden" name="periodo" id="periodo"  value="<?php echo $periodo; ?>">
                 
                <input type="hidden" name="classe" id="classe"  value="<?php echo $classeOriginalAluno; ?>">
                <input type="hidden" name="action" value="manipularAvalFinalCurso">
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                       <button ype="submit" class="btn btn-success  lead btn-lg submitter" id="Cadastrar"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>