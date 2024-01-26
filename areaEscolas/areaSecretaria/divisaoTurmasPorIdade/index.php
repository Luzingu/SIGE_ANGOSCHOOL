<?php session_start(); 
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    
    $manipulacaoDados = new manipulacaoDados("Divisão de Turmas por Idade", "divisaoTurmasPorIdade");
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
          <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-check-double"></i> <strong>Divisão de Turmas por Idade</strong></h1>
      </nav>
    </div>
    </div>
    <div class="main-body">
        <?php 
          if($verificacaoAcesso->verificarAcesso("", ["divisaoTurmasPorIdade"], array(), "msg")){

          $idPAnexo =  isset($_GET["idPAnexo"])?$_GET["idPAnexo"]:$manipulacaoDados->selectUmElemento("escolas", "idPAnexo", ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]);
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

          $condicaoTurma = ["idPEscola"=>$_SESSION["idEscolaLogada"], "idListaAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "periodoTurma"=>$periodo, "idAnexoTurma"=>$idPAnexo, "idPNomeCurso"=>$idCurso];

          $condicaoAlunos = ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.periodoAluno"=>$periodo, "escola.idMatAnexo"=>$idPAnexo, "reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idCurso];

          if(valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")=="opcional"){
            $condicaoAlunos["escola.turnoAluno"]=$turno;
            $condicaoTurma["periodoT"]=$turno;
          }
        
          echo "<script>var turmas = ".$manipulacaoDados->selectJson("listaturmas", [], $condicaoTurma, [], "", [], ["nomeTurma"=>1])."</script>";
          echo "<script>var listaAlunos =".$manipulacaoDados->selectJson("alunosmatriculados", ["nomeAluno", "numeroInterno", "reconfirmacoes.nomeTurma", "sexoAluno", "dataNascAluno", "idPMatricula", "fotoAluno", "grupo"], $condicaoAlunos, ["escola", "reconfirmacoes"], "", [], ["nomeAluno"=>1], $manipulacaoDados->matchMaeAlunos($manipulacaoDados->idAnoActual, $idCurso, $classe))."</script>";

           ?>
    <div class="card">
      <div class="card-body">
      
      <form id="formDivisaoTurmas">     
      <div class="row">

      <div class="col-lg-3 col-md-3 lead">
        Classe:
        <select class="form-control" id="luzingu">
          <?php 
          if(isset($_SESSION['classesPorCursoPeriodo'])){
            echo $_SESSION['classesPorCursoPeriodo'];
          }else{
            $_SESSION['classesPorCursoPeriodo']=retornarClassesPorCurso($manipulacaoDados, "A");
          }
          ?>         
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
      <div class="col-lg-2 col-md-2 col-xs-6 col-sm-6 lead text-center">
          <strong id="numInscritos" class="lead text-danger" style="font-weight:bolder; font-size:14pt;"></strong>
          <input type="number" id="numeroTurmas" class="form-control text-center lead" min="1" max="26">
      </div>
      <?php if(count($manipulacaoDados->selectArray("escolas", ["estadoperiodico.objecto"] ,["idPEscola"=>$_SESSION['idEscolaLogada'], "estadoperiodico.estado"=>"V", "estadoperiodico.objecto"=>"divTurmas"], ["estadoperiodico"]))>0){ ?>

        <div class="col-lg-2 col-md-2 col-xs-6 col-sm-6"><br>
          <button type="submit" class="btn lead btn-success font-weight-bolder" id="btnDividirTurma"><i class="fa fa-check-double"></i> Dividir</button>
        </div>
      <?php } ?>      
    </div>

     <div class="row">
        
        <div class="col-lg-2 col-md-2 col-xs-6 col-sm-6 text-center lead">
          Turmas:
          <select class="form-control text-center" id="turma">
          </select>
        </div>        
         
        <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12"><br>
           <label class="lead">Total: <span id="numTotalAlunos" class="quantidadeTotal">0</span></label>&nbsp;&nbsp;&nbsp;
            <label class="lead">Femininos: <span class="quantidadeTotal" id="numTMasculinos">0</span></label>
        </div>
        
      
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
                      <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 lead">
                        N.º Interno
                        <input type="text" class="form-control" id="numeroInterno" readonly style="background-color: white;">
                      </div>
                      <div class="col-lg-4 col-md-4 lead">
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