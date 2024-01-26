<?php session_start(); 
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Copiar Dados dos alunos", "copiarDadosAlunos");
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
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-copy"></i> Copiar Dados</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso("", ["copiarDadosAlunos"], array(), "msg")){ 

            
          echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";
          ?>

          <form class="row" id="formPesquisar">
            <div class="col-lg-4 col-md-4">
              <label>Nome do Aluno</label>
              <input type="text" disabled="" style="font-weight: bolder; color: orange; font-size: 16pt;" class="form-control" required="" id="nomeAluno" name="nomeAluno">
            </div>
            <div class="col-lg-3 col-md-3">
              <label>N.º Interno</label>
              <input type="text" class="form-control" required="" id="numeroInternoAluno" name="numeroInternoAluno">
            </div>
            <div class="col-lg-3 col-md-3">
              <label>Bilhete de Identidade</label>
              <input type="text" class="form-control" required="" id="bilheteIdentidade" name="bilheteIdentidade">
            </div>
            <div class="col-lg-1 col-md-1"><br>
              <button type="submit" id="btn" name="btn" class="btn btn-primary lead"><i class="fa fa-search"></i> Pesquisar</button>
            </div>
          </form><br>

          <div class="row">
            <div class="col-lg-12 col-md-12">

              <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped tabela" >
                      <thead class="corPrimary">
                        <tr>
                            <th class="lead"><strong> Nome Completo</strong></th>
                            <th class="lead text-center"><strong> Número Interno</strong></th>
                            <th class="lead text-center"><strong>Sexo</strong></th>
                            <th class="lead text-center"><strong>Escola Actual</strong></th>
                            <th class="lead text-center"><strong>Classe</strong></th>
                            <th class="lead text-center"></th>
                        </tr>
                      </thead>
                      <tbody id="dadoTabela">
                  
                      </tbody>
                  </table>
              </div>
            </div>
          </div>

    

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs();$janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>