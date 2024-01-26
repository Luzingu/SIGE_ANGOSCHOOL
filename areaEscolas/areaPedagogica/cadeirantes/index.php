<?php session_start();
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Cadeirantes", "cadeirantes");
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
    #listaAlunos form input.observacaoF{
      font-weight: 700;
      background-color: transparent;
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
           <div class="row" >
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-book-medical"></i> Cadeirantes</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("","cadeirantes", array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
            echo "<script>var luzingu='".$luzingu."'</script>";
            $luzingu = explode("-", $luzingu);

          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
            
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";

           $alunos = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "cadeiras_atraso.idPCadeirantes", "numeroInterno", "fotoAluno", "idPMatricula", "cadeiras_atraso.exameEspecial", "cadeiras_atraso.idCadAno", "grupo", "cadeiras_atraso.idCadDisciplina"], ["cadeiras_atraso.idCadEscola"=>$_SESSION['idEscolaLogada'], "cadeiras_atraso.classeCadeira"=>$classe, "cadeiras_atraso.idCadCurso"=>$idCurso, "cadeiras_atraso.estadoCadeira"=>"F"], ["cadeiras_atraso"], "", [], ["nomeAluno"=>1]);
          $alunos = $manipulacaoDados->anexarTabela2($alunos, "nomedisciplinas", "cadeiras_atraso", "idPNomeDisciplina", "idCadDisciplina");
          $alunos = $manipulacaoDados->anexarTabela2($alunos, "anolectivo", "cadeiras_atraso", "idPAno", "idCadAno");

          echo "<script>var pautas =".json_encode($alunos)."</script>";
        ?>

<div class="card">
  <div class="card-body">
      <div class="row">
        <div class="col-lg-2 col-md-2 lead">
            Cadeiras da:
            <select class="form-control" id="luzingu">
                <?php 
                  if(isset($_SESSION['classesPorCurso'])){
                    echo $_SESSION['classesPorCurso'];
                  }else{
                    $_SESSION['classesPorCurso']= retornarClassesPorCurso($manipulacaoDados, "A", "nao", "nao", "sim");
                  }
                ?>                          
            </select>
        </div>
        <div class="col-lg-6 col-md-6 lead"><br><a href="../../relatoriosPdf/listaCadeirantes.php?idPCurso=<?php echo $idCurso; ?>&classe=<?php echo $classe ?>" class="lead btn-primary btn" ><i class="fa fa-print"></i> Visualizar Lista</a>&nbsp;&nbsp;&nbsp;Total: <span class="quantidadeTotal" id="numTotal">0</span></div>
    </div>

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
 
    <div id="listaAlunos" class="fotFoto">
        
    </div>

     <div class="row" id="paraPaginacao" style="margin-top: -10px;" style="display: none;">
        <div class="col-md-12 col-lg-12 coluna">
            <div class="form-group paginacao">
                  
            </div>
          </div>
        </div>
      </div>
      </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>