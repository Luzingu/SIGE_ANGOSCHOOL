<?php session_start();  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar();
    $manipulacaoDados = new manipulacaoDados("Usuários Conectados");
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

  <link rel="stylesheet" href="<?php echo $caminhoRetornar.'bibliotecas/css/adminlte.css'; ?>">

  <style type="text/css">
   #usuarios .imgUsuario{
      height: 70px;
      width: 70px;
      max-height: 70px;
      max-width: 70px;
    }
    #usuarios  .link{
      display: block;
      outline: none;
      text-decoration: none;
    }
    #usuarios .users-list-name{
      font-weight: bolder;
      color: black;
      font-size: 11pt;
    }
  @media (max-width: 768px) {
    #usuarios li{
      width: 33.33% !important;
    }
  }
    #paraPaginacao ul li a{
      height: 30px;
      font-size: 10pt;
      padding: 5px;
    }

    .online_icon{
      height: 12px;
      width:12px;
      display: block;
      background-color: #4cd137;
      border-radius: 50%;
      border:1.5px solid white;
      margin-left:118px;
      margin-top: -10px;
      z-index: 700;
    }
  </style>
  
 </head>

<body>
  <?php
      $janelaMensagens->processar(); 
    $layouts->cabecalho();
    $layouts->aside();
 
      $estadoUsuarios = isset($_GET["estadoUsuarios"])?$_GET["estadoUsuarios"]:"online";
      echo "<script>var estadoUsuarios='".$estadoUsuarios."'</script>";
      
      
        $dataSaida = strtotime($manipulacaoDados->dataSistema.$manipulacaoDados->tempoSistema." - 5000 seconds");
 
        $condicao = ["estadoExpulsao"=>"A", "dataSaida"=>date("Y-m-d", $dataSaida), "horaSaida"=>array('$gt'=>date("H:i:s", $dataSaida)), "idUsuarioLogado"=>array('$ne'=>(int)$_SESSION['idUsuarioLogado'])];
        $usuariosOnline = $manipulacaoDados->selectArray("entidadesonline", [], $condicao, [], "", [], array("idPOnline"=>1));
        echo "<script>var usuarios=".json_encode($usuariosOnline)."</script>";
   ?>
   <section id="main-content"> 
      <section class="wrapper" id="containers">

        <!-- /.card-header -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-lg-offset-2 col-md-offset-2">
              <input type="text" name="" placeholder="Pesquisar..." class="lead text-center form-control lead" id="pesUsuarioOnline">
            </div>
            <div class="col-lg-2 col-md-2">
              <label class="lead" id="totalUsuarios"></label>
            </div>
        </div>
        <div class="card-body p-0">
          
          <ul class="users-list clearfix" id="usuarios" style="min-height: 500px;">
           
          </ul>
          <!-- /.users-list -->
        </div>
        <div class="row" id="paraPaginacao" style="margin-top: -30px; margin-left: 10px; margin-right: 10px;">
        <div class="col-md-12 col-lg-12 coluna">
            <div class="form-group paginacao">
                  
            </div>
        </div>
      </div>
        
        </div>
    </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>