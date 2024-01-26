<?php session_start();      
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar=''</script>";
    includar("../");
    if($_SESSION['tipoUsuario']=="aluno"){
      echo "<script>window.location='areaAluno/index.php'</script>";
    }
    $manipulacaoDados = new manipulacaoDados("Área do Professor");
    $manipulacaoDados->idPArea=2;
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea=$manipulacaoDados->idPArea;
    $layouts->designacaoArea="Área do Professor";
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
    $layouts->aside();
  ?> 

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="main-bdoy">
      <?php  if(($verificacaoAcesso->verificarAcesso(2, [], array(), "msg", "sim"))){ 
                
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