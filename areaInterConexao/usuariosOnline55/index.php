<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Usuários Online");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea=7;
    $layouts->designacaoArea="Inter Conexão";
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-rss"></i> Usuários Online</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(11, ["qualquerAcesso"], array(), "msg")){

            echo "<script>var cargoProfessor='".valorArray($manipulacaoDados->sobreUsuarioLogado, "nivelSistemaEntidade")."'</script>";
             
        $dataSaida = strtotime($manipulacaoDados->dataSistema.$manipulacaoDados->tempoSistema." - 1200 seconds");

        $condicao = ["estadoExpulsao"=>"A", "dataSaida"=>date("Y-m-d", $dataSaida), "horaSaida"=>array('$gt'=>date("H:i:s", $dataSaida))];

        if($_SESSION["tipoUsuario"]=="aluno"){
          $condicao["idOnlineMat"]=array('$ne'=>(int)$_SESSION['idUsuarioLogado']);
        }else{
          $condicao["idOnlineEnt"]=array('$ne'=>(int)$_SESSION['idUsuarioLogado']);
        }

        $entidades = $manipulacaoDados->selectArray("entidadesonline", $condicao);
        $entidades = $manipulacaoDados->anexarTabela($entidades, "alunosmatriculados", "idPMatricula", "idOnlineMat");
        $entidades = $manipulacaoDados->anexarTabela($entidades, "entidadesprimaria", "idPEntidade", "idOnlineEnt");
        $entidades = $manipulacaoDados->anexarTabela($entidades, "escolas", "idPEscola", "idOnlineEntEscola");


            echo "<script>var entidadesOnline=".json_encode($entidades)."</script>";

            
         ?>
         
            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
              <div>
                  <label class="lead">Total: <span id="numTProfessores" class="quantidadeTotal"></span></label>
              </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                          <tr>
                      <th class="lead text-center"><strong>Nº</strong></th>
                      <th class="lead"><strong>Nome</strong></th>
                      <th class="lead text-center"><strong>Número Interno</strong></th>
                      <th class="lead text-center"><strong>Tipo de Usuário</strong></th>
                      <th class="lead"><strong>Instituição</strong></th>
                      <th class="lead text-center"><strong>Entrou</strong></th>
                      <th class="lead text-center" style="width: 150px;"></th>
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
