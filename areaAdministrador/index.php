<?php session_start();      
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar=''</script>";
    includar("../");
    $manipulacaoDados = new manipulacaoDados("Gestão de Empresa");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
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
      color: orange;
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
    .valor, .etiqueta{
      color: black;
    }
  </style>
</head>

<body>
  <?php
     $janelaMensagens->processar ();
    $layouts->cabecalho();
    $layouts->aside(11);
  ?> 

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-bdoy">
      <?php  if(($verificacaoAcesso->verificarAcesso(11, ["qualquerAcesso"]))){ 

        $includarHtmls->perfilFuncionario();

       } echo "</div>"; $includarHtmls->rodape(); ?>
        </section>
    </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">
  fecharJanelaEspera();
</script>