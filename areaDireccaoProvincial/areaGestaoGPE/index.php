<?php session_start();
  if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    includar("../");
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Gestão do GPE");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Gestão do GPE";
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #citacaoProfessor{
      color: white;
      font-style: italic;
      color: white;
      font-weight: 800;
      color: red;
    }
    .valor{
      font-weight: 800;
    }
    .informPerfil{
      font-weight: 1000;
    }
    .cargoProfessor{
      color: white;
      font-weight: 700;
      font-size: 18px;  
    }
    .nomeUsuarioCorente{
      font-weight: 800;
      color:white;
    }
    #imageProfessor{
      width: 130px;
      height: 130px;
      max-height: 130px;
      max-width: 130px;
      min-width: 130px;
      min-height: 130px;
    }
    .border div{
      border: solid white 1px;
      height: 120px;
      padding:0px;
      padding: 0px;
      margin: 0px;
      padding-top: 50px;
      color: white;
      font-weight: bolder;
      font-size: 19px;
    }
    .outrasInformacoes{
      color: white;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
      $layouts->headerUsuario();
      $layouts->areaGestaoGPE();

    $usuariosPermitidos[] = "FuncionarioDirProv";

   
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-bdoy">
      <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos, "", "sim", "Especialista", "sim")){ 

        $includarHtmls->perfilFuncionario();

       } echo "</div>"; $includarHtmls->rodape(); ?>
        </section>
    </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $includarHtmls->formConfirmarSenhaAdministrador(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>