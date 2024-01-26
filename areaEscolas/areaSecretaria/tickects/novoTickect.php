<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
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
          if(isset($_POST["btnEnviar"]))
          {
            $assuntoTicket = isset($_POST["assuntoTicket"])?$_POST["assuntoTicket"]:"";
            $mensagemTicket = isset($_POST["mensagemTicket"])?$_POST["mensagemTicket"]:"";

            $anexo1 = $manipulacaoDados->dia.$manipulacaoDados->mes.$manipulacaoDados->ano.date("H").date("s").date("i");
            $anexo1 = $manipulacaoDados->upload("anexo1", $anexo1, "Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Arquivos", "../../", "", "", "");

            $anexo2 = $manipulacaoDados->dia.$manipulacaoDados->mes.$manipulacaoDados->ano.date("H").date("s").date("i");
            $anexo2 = $manipulacaoDados->upload("anexo1", $anexo1, "Ficheiros/Escola_".$_SESSION["idEscolaLogada"]."/Arquivos", "../../", "", "", "");

            $manipulacaoDados->inserir("tickects", "idPTicket", "assuntoTicket, mensagemTicket, idPEntidade, nomeEntidade, dataSubmissao, horaSubmissao, idPEscola, anexo1, anexo2", [$assuntoTicket, $mensagemTicket, $_SESSION["idUsuarioLogado"], valorArray($manipulacaoDados->sobreUsuarioLogado, "nomeEntidade"), $manipulacaoDados->dataSistema, $manipulacaoDados->tempoSistema, $_SESSION["idEscolaLogada"], $anexo1, $anexo2]);

            //echo "<script>window.location=''</script>";
          }
        ?>
        <div class="row">
            <div class="col-lg-4 col-md-4">
              <div class="panel panel-info" id="listaTickets">
                 <div class="panel-heading lead">
                    <strong>Tickets Recentes</strong>
                 </div>
                 <div class="panel-body">
                   <?php
                   foreach($manipulacaoDados->selectArray("tickects", ["idPTicket", "assuntoTicket"], ["idPEscola"=>$_SESSION["idEscolaLogada"]], [], "", [], ["idPTicket"=>-1]) as $a)
                   {
                     echo "<a href='ticketEnviado/index.php?idPTicket=".$a["idPTicket"]."'>".$a["assuntoTicket"]."</a><br>";
                   }
                    ?>

                 </div>
               </div>
            </div>
            <form class="col-lg-8 col-md-8"  enctype="multipart/form-data" action="" method="POST">
              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <div class="panel panel-warning">
                     <div class="panel-heading lead">
                        <strong>Abrir Ticket</strong>
                     </div>
                     <div class="panel-body">
                       <div class="row">
                         <div class="col-lg-12 col-md-12">
                           <label for="">Assunto</label>
                           <input type="text" class="form-control" required name="assuntoTicket" id="assuntoTicket">
                         </div>
                       </div>
                       <div class="row">
                         <div class="col-lg-12 col-md-12">
                           <label for="">Mensagem</label>
                           <textarea type="text" required class="form-control" required name="mensagemTicket" id="mensagemTicket"></textarea>
                         </div>
                       </div>

                     </div>
                   </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <div class="panel panel-warning">
                     <div class="panel-body">
                       <div class="row">
                         <div class="col-lg-12 col-md-12">
                           <label>Anexos</label>
                           <input type="file" class="form-control" name="anexo1" id="anexo1">
                         </div>
                       </div>
                       <div class="row">
                         <div class="col-lg-12 col-md-12">
                           <label>Anexos</label>
                           <input type="file" class="form-control" name="anexo2" id="anexo2">
                         </div>
                       </div>

                     </div>
                   </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12 col-md-12">
                  <div class="panel panel-warning">
                     <div class="panel-body">
                       <div class="row">
                         <div class="col-lg-12 col-md-12 text-right">
                           <button type="submit" name="btnEnviar" class="btn btn-lg btn-primary btn-lg"><i class="fa fa-send"></i> Enviar</button>
                         </div>
                       </div>
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

      $('#mensagemTicket').summernote();


    })
  </script>
