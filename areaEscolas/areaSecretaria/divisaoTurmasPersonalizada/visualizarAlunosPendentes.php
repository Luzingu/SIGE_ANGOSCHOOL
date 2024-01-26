<?php session_start();
    include_once $_SERVER["DOCUMENT_ROOT"].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    
    $manipulacaoDados = new manipulacaoDados("Visualizar alunos pendentes", "divisaoTurmasPersonalizada");
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
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

    <div class="row" >
      <div class="col-lg-12 col-md-12">
      <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

          <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                <strong class="caret"></strong>
                            </a>
          <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-times-circle"></i> <strong>Alunos com turmas pendentes</strong></h1>
      </nav>
    </div>
    </div>
    <div class="main-body">
      <?php if($verificacaoAcesso->verificarAcesso("", "divisaoTurmasPersonalizada", array(), "msg")){

        $idCurso = isset($_GET["idCurso"])?$_GET["idCurso"]:"";
        $classe = isset($_GET["classe"])?$_GET["classe"]:"";
        $periodo = isset($_GET["periodo"])?$_GET["periodo"]:"";
        echo "<script>var idCurso='".$idCurso."'</script>";
        echo "<script>var classe='".$classe."'</script>";
        echo "<script>var periodo='".$periodo."'</script>";
        echo "<script>var luzingu=''</script>";

        $informacaoGeral=$manipulacaoDados->selectUmElemento("nomecursos","abrevCurso", ["idPNomeCurso"=>$idCurso])." - ";
        $informacaoGeral .=$classe.".ª";
        if($periodo=="reg"){
          $informacaoGeral .=" - Regular";
        }else{
          $informacaoGeral .=" - Pós Laboral";
        }

        $alunosSemTurma = $manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "escola.idGestLinguaEspecialidade", "escola.idGestDisEspecialidade", "grupo"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "escola.classeActualAluno"=>$classe, "escola.periodoAluno"=>$periodo, "reconfirmacoes.nomeTurma"=>"", "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idCurso], ["escola", "reconfirmacoes"], "", [], ["nomeAluno"=>1], $manipulacaoDados->matchMaeAlunos($manipulacaoDados->idAnoActual, $idPCurso, $classe));

        $alunosSemTurma=$manipulacaoDados->anexarTabela2($alunosSemTurma, "nomedisciplinas", "escola", "idPNomeDisciplina", "idGestLinguaEspecialidade");
        
        echo "<script>var alunosSemTurma =".json_encode($alunosSemTurma)."</script>";

        $disciplinasOpcao=array();
        foreach($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9, 20, 21)] ]) as $a){
          $disciplinasOpcao[]=array("idPNomeDisciplina"=>$a["_id"], "nomeDisciplina"=>$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$a["_id"]]));
        }
        echo "<script>var disciplinasDeOpcao = ".json_encode($disciplinasOpcao)."</script>";
      ?>
      <h2 style="text-transform: uppercase; margin-top: -10px;"><strong><?php echo $informacaoGeral; ?></strong></h2>

        <div class="card">
            <div class="card-body">
              
              <table id="tabelaAlunosPendentes" class="table table-striped table-bordered table-hover" >
                  <thead class="corPrimary">
                    <tr>
                        <th class="lead text-center"><strong>Nº</strong></th>
                        <th class="lead"><strong>Nome do Aluno</strong></th>
                        <th class="lead text-center"><strong>Número Interno</strong></th>
                        <th class="lead text-center"><strong>Língua de Opç.</strong></th>
                        <th class="lead text-center"><strong>Disciplina de Opç.</strong></th>
                        <th class="lead text-center"></th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
          </div>
        </div><br/>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>


  <div class="modal fade" id="divAlterarOpcoes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="divAlterarOpcoesForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-check"></i> Opções</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-6 col-md-6 lead">
                      Língua de Opção:
                      <select class="form-control lead" id="lingEspecialidade" name="lingEspecialidade">
                        <?php 
                          echo "<option value=''>Seleccionar</option>";
                          foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"] ["idPNomeDisciplina"=>['$in'=>array(20, 21)]]) as $disciplina) {

                            echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                          } 
                         ?>
                      </select>
                    </div>
                    <div class="col-lg-6 col-md-6 lead">
                      Disciplina de Opção:
                      <select class="form-control lead" id="discEspecialidade" name="discEspecialidade">
                        <?php 
                          echo "<option value=''>Seleccionar</option>";
                          foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)]]) as $disciplina) {
 
                            echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                          } 

                         ?>
                      </select>
                    </div>
                  </div>
              </div>
              <input type="hidden" id="idPMatricula" name="idPMatricula" value="">
              <input type="hidden" id="grupo" name="grupo" value="">
              <input type="hidden" id="idPCurso" name="idPCurso" value="<?php echo $idCurso; ?>">
              <input type="hidden" id="classe" name="classe" value="<?php echo $classe; ?>">
              <input type="hidden" id="periodo" name="periodo" value="<?php echo $periodo; ?>">
              <input type="hidden" id="action" name="action" value="trocarDisciplinasOpcoes">

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-left">
                      <button type="submit" class="btn btn-success lead btn-lg" id="Cadastar"><i class="fa fa-check"></i> Alterar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
<script type="text/javascript">
  $(document).ready(function(){
    listarAlunos()
    DataTables("#tabelaAlunosPendentes", "sim")

    var repet1 = true;
    $("#tabelaAlunosPendentes").bind("mouseenter click", function(){
        repet1=true;
        $("#tabelaAlunosPendentes tr td a.editDados").click(function(){
            if(repet1==true){
                $("#divAlterarOpcoes #idPMatricula").val($(this).attr("idPMatricula"));
                $("#divAlterarOpcoes #grupo").val($(this).attr("grupo"));

                $("#divAlterarOpcoes #lingEspecialidade").val($(this).attr("idGestLinguaEspecialidade"))
                $("#divAlterarOpcoes #discEspecialidade").val($(this).attr("idGestDisEspecialidade"))
                $("#divAlterarOpcoes").modal("show")
            }
        });
    });
    $("#divAlterarOpcoesForm").submit(function(){
      trocarDisciplinasOpcoes();
      return false;
    })


    function  listarAlunos(){
      var htmlLinhas="";
      var i=0;
      alunosSemTurma.forEach(function(dado){
        i++;
        htmlLinhas +="<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead'>"+dado.nomeAluno+"</td><td class='lead text-center'>"+dado.numeroInterno+"</td><td class='lead'>"+nomeDisciplina(dado.escola.idGestLinguaEspecialidade)+"</td><td class='lead'>"+nomeDisciplina(dado.escola.idGestDisEspecialidade)+"</td><td class='lead text-center'><a href='#' title='Alterar' class='editDados' idPMatricula='"+dado.idPMatricula+"' grupo='"+dado.grupo+"' idGestDisEspecialidade='"+dado.escola.idGestDisEspecialidade+"' idGestLinguaEspecialidade='"+dado.escola.idGestLinguaEspecialidade+"'><i class='fa fa-check text-success'></i></a></td></tr>"
      })
      $("#tabelaAlunosPendentes tbody").html(htmlLinhas)
    }

    function trocarDisciplinasOpcoes(){
      $("#divAlterarOpcoes").modal("hide")
      var form = new FormData(document.getElementById("divAlterarOpcoesForm"));
      chamarJanelaEspera("")
      enviarComPost(form);

      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          fecharJanelaEspera()
          if(resultado.trim().substring(0, 1)=="F"){
            $("#divAlterarOpcoes").modal("show")
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
            mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");
            alunosSemTurma = JSON.parse(resultado)
            listarAlunos()       
          }
        }
      }
    }

  })
</script>