<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");    
    $manipulacaoDados = new manipulacaoDados("Divisão de Turmas Personalizada", "divisaoTurmasPersonalizada");
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
          <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-check-double"></i> <strong>Divisão de Turmas Personalizada</strong></h1>
      </nav>
    </div>
    </div>
    <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("", "divisaoTurmasPersonalizada", array(), "msg")){
             
          $idPAnexo =  isset($_GET["idPAnexo"])?$_GET["idPAnexo"]:$manipulacaoDados->selectUmElemento("escolas", "idPAnexo", ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]);;
          echo "<script>var idPAnexo='".$idPAnexo."'</script>";

          if(isset($_GET["turno"])){
            $turno = $_GET["turno"];
          }else{
            if(valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
              $turno ="Matinal";
            }else{
              $turno="";
            }
          }
          echo "<script>var turno='".$turno."'</script>";

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = $luzingu[2];
          $classe = $luzingu[1];
          $periodo = $luzingu[0];

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var periodo='".$periodo."'</script>";

         $condicaoTurma = ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo];

          $condicaoAlunos = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.classeReconfirmacao"=>$classe, "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "escola.idMatCurso"=>$idCurso];

          if(valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
            $condicaoAlunos["escola.turnoAluno"]=$turno;
            $condicaoTurma["periodoT"]=$turno;
          }

          $disciplinasOpcao=array();
          foreach($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9, 20, 21)] ]) as $a){
            $disciplinasOpcao[]=array("idPNomeDisciplina"=>$a["_id"], "nomeDisciplina"=>$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$a["_id"]]));
          }
          echo "<script>var disciplinasDeOpcao = ".json_encode($disciplinasOpcao)."</script>";

          echo "<script>var turmas = ".$manipulacaoDados->selectCondClasseCurso("json", "listaturmas", [], $condicaoTurma, $classe, ["idPNomeCurso"=>$idCurso], [], "", [], ["nomeTurma"=>1])."</script>";
          echo "<script>var listaAlunos =".$manipulacaoDados->selectJson("alunosmatriculados", ["nomeAluno", "numeroInterno", "reconfirmacoes.nomeTurma", "sexoAluno", "dataNascAluno", "idPMatricula", "grupo", "fotoAluno"], $condicaoAlunos, ["escola", "reconfirmacoes"], "", [], ["nomeAluno"=>1], $manipulacaoDados->matchMaeAlunos($manipulacaoDados->idAnoActual, $idCurso, $classe))."</script>"; ?>
    <div class="card">
      <div class="card-body">
      <form id="formCriarTurmas">     
      <div class="row">

      <div class="col-lg-3 col-md-3 lead">
        Classe:
        <select class="form-control" id="luzingu">
          <?php retornarClassesPorCurso($manipulacaoDados, "A"); ?>         
        </select>
      </div>
      <div class="col-lg-2 col-md-2 lead">
        Turno
        <select id="turno" class="form-control">
          <?php 
            if(valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){

              echo "<option>Matinal</option>";
              echo "<option>Vespertino</option>";
              if(trim(valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas"))=="regPos"){
                echo "<option>Noturno</option>";
              }
            }else{
              echo "<option value=''>Nenhum</option>";
            }
           ?>
        </select>
      </div>
      
        <div class="col-lg-3 col-md-3 text-center lead">
          Turmas:
          <select class="form-control text-center" id="turma">
          </select>
        </div>
        <div class="col-lg-2 col-md-2 lead">
        Anexo:
        <select class="form-control" id="idPAnexo">
          <?php 
            foreach ($manipulacaoDados->selectArray("escolas",[], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]) as $a) {

              echo "<option value='".$a["anexos"]["idPAnexo"]."'>".$a["anexos"]["identidadeAnexo"]."</option>";
            }
          ?>
        </select>
      </div>
    </div>

     
    <div class="row"> 
        <div class="lead col-lg-3 col-md-3 lead">
          Língua (Opção)
          <select class="form-control" id="linguaEstangeira">
            <option value="">Qualquer</option>
            <?php 
              foreach ($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "idPNomeDisciplina"=>['$in'=>array(20, 21)] ]) as $disciplina) {

                echo "<option value='".$disciplina["_id"]."'>".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$disciplina["_id"]])."</option>";
              } 
            ?>
          </select>
        </div>     
        <div class="lead col-lg-3 col-md-3 lead">
          Disciplina (Opção):
          <select class="form-control" id="disciplinaOpcao">
            <option value="">Qualquer</option>
            <?php 
            foreach ($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada'], "idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)] ]) as $disciplina) {
 
                echo "<option value='".$disciplina["_id"]."'>".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$disciplina["_id"]])."</option>";
              } 
            ?>
          </select>
        </div>
      <div class="col-md-4 col-lg-4 col-sm-12 col-xs-12"><br>
          <label class="lead">Total: <span id="numTotalAlunos" class="quantidadeTotal">0</span></label>
          <label class="lead">Femininos: <span class="quantidadeTotal" id="numTMasculinos">0</span></label>
      </div>      
    </div>
    <div class="row">
      <fieldset style="border: solid rgba(0,0,0,0.3) 1px; padding: 2px; border-radius: 10px;padding-bottom: 10px; margin-right: 10px; margin-left: 10px;">
        <legend style="width:200px;">Criação de Turmas</legend>

        <div class="col-lg-5 col-md-5 lead"><br>Pendentes: <span id="numInscritosPendentes" class="lead"></span>&nbsp;&nbsp;&nbsp;<a href="visualizarAlunosPendentes.php?idCurso=<?php echo $idCurso ?>&classe=<?php echo $classe ?>&periodo=<?php echo $periodo ?>"><i class="fa fa-eye"></i></a></div>
        <div class="col-lg-3 col-md-3 col-xs-5 col-sm-5 lead text-center">
          Tot. de alunos:
          <input type="number" id="numeroAlunosTurma" class="form-control text-center lead" min="1" required="" max="">
        </div>
        <?php if(count($manipulacaoDados->selectArray("escolas", ["estadoperiodico.objecto"] ,["idPEscola"=>$_SESSION['idEscolaLogada'], "estadoperiodico.estado"=>"V", "estadoperiodico.objecto"=>"divTurmas"], ["estadoperiodico"]))>0){ ?>
          <div class="col-lg-4 col-md-4"><br>
            <button type="submit" class="lead btn btn-success font-weight-bolder" id="btnAdicionarTurma"><i class="fa fa-plus-circle"></i> Adicionar</button>&nbsp;&nbsp;&nbsp;
            <button type="button" class="lead btn btn-danger font-weight-bolder" id="btnResetTurma"><i class="fa fa-minus-circle"></i> Reset</button>
          </div> 
        <?php } ?>
      </fieldset> 
    </div>

    </form>  
         <div class="row">

          <div class="col-lg-8 col-md-8 visible-mg visible-lg">
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="pesqUsario">
            <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="search" class="form-control lead pesquisaEntidade" tipoEntidade="alunos" placeholder="Pesquisar Aluno..." list="listaOpcoes">
                
            </div>   
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover" >
              <thead>
                  <tr class="corPrimary">
                      <th class="lead text-center"><strong>Nº</strong></th>
                      <th class="lead"><strong>Nome Completo</strong></th>
                      <th class="lead text-center"><strong>Número Interno</strong></th>
                      <th class="lead text-center"><strong><i class="fa fa fa-restroom"></i></strong></th>
                      <th class="lead text-center"><strong>Idade</strong></th>
                      <th class="lead text-center"></th>
                  </tr>
                  </thead>
              <tbody id="listaAlunos">
                  
              </tbody>
          </table>
        </div>

        <div class="row" id="paraPaginacao" style="margin-top: -30px;">
          <div class="col-md-12 col-lg-12 coluna">
            <div class="form-group paginacao">
                  
            </div>
          </div>
        </div> 
        </div> 
      </div><br>

            
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>


  <div class="modal fade" id="trocarTurma" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="trocarTurmaForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"> <i class="fa fa-dungeon"></i> Trocar Turma</h4>
              </div>

              <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 lead">
                      N.º Interno:
                      <input type="text" class="form-control" id="numeroInterno" readonly>
                    </div>
                    <div class="col-lg-5 col-md-5 lead">
                      Turma:
                      <select class="form-control lead" id="turmaTrocar" >

                      </select>
                    </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 text-left">
                      <button type="submit" class="btn btn-primary lead btn-lg" id="Cadastar"><i class="fa fa-check"></i> Trocar</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>