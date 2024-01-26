<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../../");
    $manipulacaoDados = new manipulacaoDados("Tickets", "novaTrasferencia");
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
<style media="screen">
  #listaTickets a{
    font-size: 14pt;
    line-height: 30px;
    color:black;
    font-weight: bolder;
  }
</style>

<head>
  <?php $conexaoFolhas->folhasCss();?>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    $layouts->cabecalho();

  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
        <div class="main-body" style="height:1200px;">
        <?php

        if($verificacaoAcesso->verificarAcesso("", "novaTrasferencia",array(), "msg")){

          $idPTicket = isset($_GET["idPTicket"])?$_GET["idPTicket"]:"";
          $array = $manipulacaoDados->selectArray("tickects", [], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idPTicket"=>$idPTicket], [], "", [], ["idPTicket"=>-1]);

        ?>
        <div class="row">
            <div class="col-lg-4 col-md-4">
              <div class="panel panel-info"id="listaTickets">
                 <div class="panel-heading lead">
                    <strong>Tickets Recentes</strong>
                 </div>
                 <div class="panel-body">
                   <?php
                   foreach($manipulacaoDados->selectArray("tickects", ["idPTicket", "assuntoTicket"], ["idPEscola"=>$_SESSION["idEscolaLogada"]], [], "", [], ["idPTicket"=>-1]) as $a)
                   {
                     echo "<a href='?idPTicket=".$a["idPTicket"]."'>".$a["assuntoTicket"]."</a><br>";
                   }
                    ?>

                 </div>
               </div>
            </div>
            <form class="col-lg-8 col-md-8"  action="" method="POST">
              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <div class="panel panel-warning">
                     <div class="panel-heading lead">
                        <strong><?php echo valorArray($array, "assuntoTicket") ?></strong>
                     </div>
                     <div class="panel-body">
                       <?php echo valorArray($array, "mensagemTicket"); ?>
                     </div>
                   </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <div class="panel panel-warning">

                     <div class="panel-body">
                      <?php if( valorArray($array, "anexo1") != "")
                      { ?>
                        <a href="<?php echo $manipulacaoDados->enderecoArquivos.'Ficheiros/Escola_'.$_SESSION["idEscolaLogada"].'/Arquivos/'.valorArray($array, "anexo1"); ?>" style="font-size:60pt;"><i class="fa fa-file-pdf fa-4px"></i></a>
                      <?php } ?>
                      <?php if( valorArray($array, "anexo2") != "")
                      { ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $manipulacaoDados->enderecoArquivos.'Ficheiros/Escola_'.$_SESSION["idEscolaLogada"].'/Arquivos/'.valorArray($array, "anexo2"); ?>" style="font-size:60pt;"><i class="fa fa-file-pdf fa-4px"></i></a>
                      <?php } ?>
                     </div>
                   </div>
                </div>
              </div>


            </form>
        </div>

        <?php } echo "</div>"; ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

  <script type="text/javascript">
    $(document).ready(function(){
      fecharJanelaEspera();
    })
  </script>
