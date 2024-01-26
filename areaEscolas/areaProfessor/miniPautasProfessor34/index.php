<?php
    session_cache_expire(60); 
    session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Mini-Pautas Professor", "miniPautasProfessor33");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = 2;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $trimestre = isset($_GET["trimestre"])?$_GET["trimestre"]:"I";
    if($trimestre!="I" && $trimestre!="II" && $trimestre!="III"){
      $trimestre="I";
    }
    echo "<script>var trimestre='".$trimestre."'</script>";
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
     #listaAlunos form .obs{
      font-weight: bolder;
     }
    #listaAlunos form input{
      font-size: 12pt !important;
      padding: 0px;
    }

     #listaAlunos form .nomeAluno{
      font-size: 15pt !important;
     }

     .nomeAluno{
      font-size: 20pt;
     }
     @media (max-width: 768px) {
        #referenciaDisciplina{
        font-size: 11pt;
        margin-top: 10px;
      }
     }
     #paraPaginacao ul li a{
      height: 40px;
      font-size: 15pt;
      padding: 5px;
      padding-right: 10px;
      padding-left: 10px;
      font-weight: bolder;
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
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno text-center" style="padding:8px;">
              <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-table"></i> <?php 
                echo "MINI-PAUTA(S) ";
                if($trimestre=="I" || $trimestre=="II" || $trimestre=="III"){
                  echo "DO ".$trimestre."º TRIMESTRE";
                }else{
                  echo "FINAL";
                } ?><br>
              </strong></h1> 
            </nav>
            </div>
          </div>

<?php if($verificacaoAcesso->verificarAcesso(2, array(), array(), "msg")){  ?>

    <div class="card">
      <div class="card-body">
        <div class="main-body">
          <div class="row">
            <div class="col-lg-5 col-md-5 lead" id="pesqUsario">
              Disciplinas
                <select id="referenciaDisciplina" name="referenciaDisciplina" class="form-control">
                 <?php 
                    $array = $manipulacaoDados->selectArray("divisaoprofessores", ["classe", "sePorSemestre", "designacaoTurmaDiv", "nomeDisciplina", "abrevCurso", "abreviacaoDisciplina2", "periodoTurmaDiv", "semestre", "idPNomeDisciplina", "idPNomeCurso", "nomeTurmaDiv", "avaliacoesContinuas", "areaFormacaoCurso", "nomeCurso", "tipoCurso"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$manipulacaoDados->idAnoActual, "idPEntidade"=>$_SESSION['idUsuarioLogado']]);

                    foreach ($array as $divisao){
                      
                      if(!(nelson($divisao, "tipoCurso")=="pedagogico" && ($divisao["idPNomeDisciplina"]==51 || $divisao["idPNomeDisciplina"]==140))){

                        $semestre = retornarSemestreActivo($manipulacaoDados, nelson($divisao, "idPNomeCurso"), $divisao["classe"]);

                        if((nelson($divisao, "sePorSemestre")=="sim" && $trimestre=="I" && nelson($divisao, "semestre")==$semestre) || nelson($divisao, "sePorSemestre")!="sim"){

                          $sobreCurso = nelson($divisao, "abrevCurso")."/";
                          $sobreDisciplina = $manipulacaoDados->disciplinas(nelson($divisao, "idPNomeCurso"), $divisao["classe"], $divisao["periodoTurmaDiv"], "",[$divisao["idPNomeDisciplina"]], array(), ["nomeDisciplina", "abreviacaoDisciplina1"]);

                          if(count($sobreDisciplina) > 0)
                          {
                          
                            echo "<option idPCurso='".nelson($divisao, "idPNomeCurso")."' semestreSeleccionada='".$divisao["semestre"]."' classe='".$divisao["classe"]."' classeExtensa='".classeExtensa($manipulacaoDados, $divisao["idPNomeCurso"], $divisao["classe"])."' turma='".$divisao["nomeTurmaDiv"]."' designacaoTurmaDiv='".$divisao["designacaoTurmaDiv"]."' idPNomeDisciplina='".$divisao["idPNomeDisciplina"]."' tipoCurso='".nelson($divisao, "tipoCurso")."' areaFormacaoCurso='".nelson($divisao, "areaFormacaoCurso")."' nomeCurso='".nelson($divisao, "nomeCurso")."' abrevCurso='".nelson($divisao, "abrevCurso")."' nomeDisciplina='".valorArray($sobreDisciplina, "nomeDisciplina")."' avaliacoesContinuas='".$divisao["avaliacoesContinuas"]."' sePorSemestre='".nelson($divisao, "sePorSemestre")."' continuidadeDisciplina='".valorArray($sobreDisciplina, "continuidadeDisciplina", "disciplinas")."'>".$sobreCurso.classeExtensa($manipulacaoDados, $divisao["idPNomeCurso"], $divisao["classe"])."/".$divisao["designacaoTurmaDiv"]." - ".valorArray($sobreDisciplina, "abreviacaoDisciplina1")."</option>";
                          }
                        }
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
            <div class="col-lg-6 col-md-6">
              <h4>Área de Formação: <strong id="areaFormacaoCursoEtiqueta"></strong></h4>
              <h4>Curso/Opção: <strong id="nomeCursoEtiqueta"></strong></h4>
              <h4>Classe: <strong id="classeEtiqueta"></strong></h4>
               <h4>Turma: <strong id="turmaEtiqueta"></strong></h4>
              <h4>Disciplina: <strong id="nomeDisciplinaEtiqueta" class="text-danger"></strong></h4>
              <!--<h4>Época: <strong id="semestreDisciplinaEtiqueta" class="text-danger"></strong></h4>!-->
            </div>
            <div class="col-lg-4 col-md-4 lead">
              <h4>Total de Alunos: <strong id="numTotAlunos">0</strong></h4>
              <h4>Femininos: <strong id="numTotFeminino">0</strong></h4>
              <h4>Aprovados: <strong id="numTotAprovado">0</strong></h4>
              <h4><a href="#" id="visualizarMiniPauta" class=" btn-primary btn lead"><i class="fa fa-print"></i> Mini-Pauta</a></h4>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 col-md-12 text-right">
              <div class="row forPagination" id="paraPaginacao" style="margin-top: -20px;">
                <div class="col-md-12 col-lg-12 coluna">
                  <div class="form-group paginacao">
                        
                  </div>
                </div>
              </div>
              <button class="btn btn-success lead btnAlterarNotas" style="display:none;"><i class="fa fa-check"></i> Alterar</button>
            </div>
          </div>
          <div id="listaAlunos" class="fotFoto">
              
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12 text-right">
              <div class="row forPagination" id="paraPaginacao" style="margin-top: -20px;">
                <div class="col-md-12 col-lg-12 coluna">
                  <div class="form-group paginacao">
                        
                  </div>
                </div>
              </div>
              <button class="btn btn-success lead btnAlterarNotas" style="display:none;"><i class="fa fa-check"></i> Alterar</button>
            </div>
          </div><br>
        </div>
      </div>


    <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();
 ?>

 <form id="formularioDados">
    <input type="hidden" name="action" id="action">
    <input type="hidden" name="idPCurso" id="idPCurso">
    <input type="hidden" name="classe" id="classe">
    <input type="hidden" name="turma" id="turma">
    <input type="hidden" name="trimestre" id="trimestre" value="<?php echo $trimestre; ?>">
    <input type="hidden" name="tipoCurso" id="tipoCurso">
    <input type="hidden" name="idPNomeDisciplina" id="idPNomeDisciplina">
    <input type="hidden" name="semestreActivo" id="semestreActivo">
    <input type="hidden" name="dados" id="dados">   
 </form> 


