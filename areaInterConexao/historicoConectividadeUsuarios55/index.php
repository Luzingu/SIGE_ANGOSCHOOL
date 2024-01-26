<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar();
    $manipulacaoDados = new manipulacaoDados("Histórico de Conectividade de Usuários");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->conDb("escola", true);
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-chain"></i> Histórico de Conectividade</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(7, ["qualquerAcesso"], array(), "msg")){
         ?>
          <div class="row">
            <div class="col-md-8 col-lg-8 lead">
              <label>Instituição:</label>
              <select class="form-control" id="idPEscola">
                <option value="">Todas Instituições</option>
                <?php 
                foreach($manipulacaoDados->selectArray("escolas", [], [], [],"", [], ["nomeEscola"=>1]) as $a){
                  echo "<option value='".$a["idPEscola"]."'>".$a["nomeEscola"]."</option>";
                }

                 ?>
              </select>
            </div>
            <div class="col-md-2 col-lg-2 lead">
              <label>Data:</label>
              <select class="form-control lead" id="dataExp">
                  <?php 
                      $dataExp="";

                      $i=0;
                      foreach ($manipulacaoDados->selectDistinct("entidadesonline", "dataEntrada", [], [], 54, [], array("idPOnline"=>-1)) as $data) {
                        $i++;
                        if($i==1){
                          $dataExp = $data["_id"];
                        }
                        echo "<option value='".$data["_id"]."'>".converterData($data["_id"])."</option>";
                      }
                      if(isset($_GET["dataEx"])){
                        $dataExp = $_GET["dataEx"];
                      }
                      echo "<script>var dataExp='".$dataExp."'</script>";

                      $entidades = $manipulacaoDados->selectArray("entidadesonline", [], ["dataEntrada"=>$dataExp], [], "", [], array("idPOnline"=>1));
                      echo "<script>var entidadesOnline=".json_encode($entidades)."</script>";
                  ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8 col-lg-8 visible-md visible-lg"><br>
            </div>
            <div class="col-md-4 col-lg-4"><br>
              <input type="text" class="form-control lead" placeholder="Pesquisar..." id="pesquisarEntidade">
            </div>
          </div>
            

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                          <tr>
                      <th class="lead text-center"><strong>Nº</strong></th>
                      <th class="lead"><strong>Nome do Usuário</strong></th>
                      <th class="lead text-center"><strong>Tipo</strong></th>
                      <th class="lead"><strong>Instituição</strong></th>
                      <th class="lead"><strong>Acessos</strong></th>
                      <th class="lead text-center"><strong>Conexão</strong></th>
                      
                  </tr>

                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
            <div class="row" id="paraPaginacao" style="margin-top: -30px;">
                <div class="col-md-12 col-lg-12 coluna">
                    <div class="form-group paginacao">
                          
                    </div>
                </div>
            </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
