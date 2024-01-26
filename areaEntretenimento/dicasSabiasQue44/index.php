<?php session_start();
    if(!isset($_SESSION['tipoUsuario'])){
      echo "<script>window.location='../../'</script>";
    }else if($_SESSION["tipoUsuario"]=="aluno" || $_SESSION["tipoUsuario"]=="professor"){
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
      includar("", "areaEscolas");
    }else{
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
      includar("../../", "areaAdministrador");
    } 
    echo "<script>var caminhoRecuar='../'</script>";
    $manipulacaoDados = new manipulacaoDados();
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->conDb("entretenimento", true);
 ?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">

    #respSabiasQue{
      font-weight: bolder;
      font-family: arial;
      border-radius: 10px; 
      padding: 10px; 
      font-size: 18pt;
      margin-top: 15px;
      text-align: justify;
    }
    #paraImagem img{
      border-radius: 10px;
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    if($_SESSION["tipoUsuario"]=="aluno"){
      $layouts->idPArea=1;
      $layouts->designacaoArea="Área do Aluno";
      $layouts->cabecalho();
      $layouts->aside();
    }else if($_SESSION["tipoUsuario"]=="professor"){
      $layouts->idPArea=2;
      $layouts->designacaoArea="Área do Professor";
      $layouts->cabecalho();
      $layouts->aside();
    }else{
      $layouts->idPArea=11;
      $layouts->designacaoArea="Gestão de Empresa";
      $layouts->cabecalho(11);
      $layouts->aside(11);
    } 
    echo "<script>var enderecoArquivos='".$manipulacaoDados->enderecoArquivos."'</script>";

    echo "<script>var sabiasQue =".$manipulacaoDados->selectJson("sabias_que", [], [], [], "", [], ["idPSabiasQue"=>1])."</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

        <div class="main-body"><br>

            <div class="card" style="min-height:700px;">
               <div class="card-body">
                  <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2">
                    <h1 class="text-primary"><i class="fa fa-question fa-2x"></i><i style="font-size:38pt; font-weight: bolder;">Sabias que</i></h1>

                    <div id="respSabiasQue">
                      
                    </div><br>
                    <div class="text-center" id="paraImagem">
                      <img src="../../livraria/30062023003010.jpg">
                    </div>


                    <div class="text-center" style="margin-top:20px;">
                      <button class="btn btn-primary btn-lg" style="font-size: 13pt;" id="btnRecuar"><i class="fa fa-arrow-left"></i> Recuar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-primary btn-lg" id="btnAvancar" style="font-size: 13pt;"><i class="fa fa-arrow-right"></i> Avançar</button>
                    </div>
                  </div>
               </div>
            </div>
         </div><br>
        <?php $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">
  var numeroAleatorio=""
  var tempoRestante=10;

   $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu()
      directorio = "areaEntretenimento/livros44/";
      numeroAleatorio = Math.floor(Math.random()*sabiasQue.length)+1

      gerarSabiasQue()

      setInterval(function() {
        $("#tempoRestante").text(completarNumero(tempoRestante))
        if(tempoRestante === 0) {
          tempoRestante=10
          if(numeroAleatorio==sabiasQue.length){
            numeroAleatorio = 1;
          }else{
            numeroAleatorio++
          }

          gerarSabiasQue()
        }
        tempoRestante--;
      }, 1000);

      
      $("#btnRecuar").click(function(){
        tempoRestante=10
        if(numeroAleatorio==1){
          numeroAleatorio = sabiasQue.length;
        }else{
          numeroAleatorio--
        }
        gerarSabiasQue()
      })

      $("#btnAvancar").click(function(){
        tempoRestante=10
        if(numeroAleatorio==sabiasQue.length){
          numeroAleatorio = 1;
        }else{
          numeroAleatorio++
        }
        gerarSabiasQue()
      })
   })

   function gerarSabiasQue(){
      $("#respSabiasQue").html(sabiasQue[(numeroAleatorio-1)].resposta);
      if(sabiasQue[(numeroAleatorio-1)].arquivo!=""){
        $("#paraImagem").show()
        $("#paraImagem img").attr("src", "")
        $("#paraImagem img").attr("src", "../../../livraria/"+sabiasQue[(numeroAleatorio-1)].arquivo)
      }else{
        $("#paraImagem").hide()
      }
   }

</script>
