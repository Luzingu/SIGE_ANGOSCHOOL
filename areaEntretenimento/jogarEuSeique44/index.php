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
    .linhaIpotese{
      margin-top: 20px;
      font-size: 18pt;
      border-radius: 10px;
      padding: 7px;
      cursor: pointer;
      padding: 13px;
    }
    #pergunta{
      font-weight: bolder;
      font-family: arial;
      border-radius: 10px; 
      padding: 10px; 
      font-size: 18pt;
    }
    .paito{
      font-size:30px;
      border-radius: 50%;
      color: white;
      padding:10px;
    }

    .tbImagem td img{
      width: 250px;
      cursor: pointer;
      border-radius: 10px;
      border: solid rgba(0, 0, 0, 0.3) 5px;
    }
    @media (max-width: 768px) {
      .tbImagem td img{
        width: 130px;
      }
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
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

        <div class="main-body"><br>
            <div class="text-right">
            <span class="bg-danger paito" id="tempoRestante" style="font-size: 30pt;">10</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong class="bg-success paito" id="numTotPontos"></strong></div><br>

            <div class="card" style="min-height:700px;">
               <div class="card-body">
                  <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2">
                    <h1 class="text-primary"><i class="fa fa-question-circle fa-2x"></i><i style="font-size:30pt; font-weight: bolder;">Eu sei que</i></h1>

                    <div id="pergunta">
                    </div><br>
                    <div class="btn-primary linhaIpotese ipotese1">
                      
                    </div>
                    <div class="btn-primary linhaIpotese ipotese2">
                      
                    </div>
                    <div class="btn-primary linhaIpotese ipotese3">
                      
                    </div>
                    <div class="btn-primary linhaIpotese ipotese4">
                      
                    </div>

                    <div>

                    <div id="btnTempo"></div>
                      
                    <table style="width:100%; margin-top:20px; border-spacing: 3px; vertical-align: top !important;" class="tbImagem">
                      <tr>
                        <td class="text-center"><img src="../../../livraria/1309ANGOS171334.jpg" class="ipoteseImagem1 linhaIpoteseImagem"></td>
                        <td><img src="../../../livraria/1309ANGOS171334.jpg" class="ipoteseImagem2 linhaIpoteseImagem"></td>
                      </tr>
                    </table>  
                    </div>

                    <div class="text-center" style="margin-top:20px;">
                      <button class="btn btn-success btn-lg" style="font-size: 15pt;" id="btnAjuda"><i class="fa fa-phone"></i> Ajuda</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="btn btn-danger btn-lg" id="btnSaltar" style="font-size: 15pt;"><i class="fa fa-mail-forward"></i> Saltar</button>
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
  var idPQuestao=""
   $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu()
      directorio = "jogarEuSeique44/";
      accionador("novaQuestao");

      $("#btnAjuda").click(function(){
        pedirAjuda()
      })

      $("#btnSaltar").click(function(){
        clearInterval(intervalo)
        accionador("saltar")
      })

      $(".linhaIpotese, .linhaIpoteseImagem").click(function(){
        clearInterval(intervalo)
        if($(this).attr("id")=="resp1"){
          var som = new Audio('../../../icones/audioRespostaCerta.mp3'); 
          som.play();
          $(this).addClass("btn-success")
          mensagensRespostas2("#mensagemCerta", "Resposta Certa")
          setTimeout(accionador("respostaCerta"), 1500)
        }else{

          $("#resp1").addClass("btn-success")

          var som = new Audio('../../../icones/audioRespostaErrada.mp3'); 
          som.play();
          $(this).addClass("btn-danger")
          mensagensRespostas2("#mensagemErrada", "Resposta Errada")
          setTimeout(accionador("respostaErrada"), 5000)
        }
      })
   })

    function pedirAjuda(){
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          $(".linhaIpotese#resp1").addClass("btn-success")
          $(".linhaIpoteseImagem#resp1").addClass("btn-success")
          if(resultado=="F"){
            $("#btnAjuda").attr("disabled", "")
          }
        }
      }
      enviarComGet("tipoAcesso=pedirAjuda&idPQuestao="+idPQuestao);
    }

    function accionador(accao){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();     
          resultado = JSON.parse(http.responseText.trim())
          var tempoRestante = 10; 

          intervalo = setInterval(function() {
            $("#tempoRestante").text(completarNumero(tempoRestante))
            if (tempoRestante === 0) {
              clearInterval(intervalo);

              if ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) {
                // Solicita permissão do usuário para acessar o áudio
                navigator.mediaDevices.getUserMedia({ audio: true })
                .then(function(stream) {
                  // Permissão concedida, você pode reproduzir áudio agora
                  var audio = new Audio('../../../icones/audioTempoAcabou.mp3');
                  audio.play();
                });
              }
              
              mensagensRespostas("#mensagemErrada", "O tempo acabou, resposta errada.")
              accionador("tempoAcabou")
            }
            tempoRestante--;
          }, 1000);


          idPQuestao = resultado.idPQuestao

          if(resultado.estadoAjuda=="F"){
            $("#btnAjuda").attr("disabled", "")
          }
          if(resultado.estadoSaltos=="F"){
            $("#btnSaltar").attr("disabled", "")
          }

          $("#numTotPontos").text(completarNumero(resultado.pontuacao))
          $("#pergunta").text(resultado.questao)

          $(".linhaIpotese, .linhaIpoteseImagem").removeClass("btn-success")
          $(".linhaIpotese, .linhaIpoteseImagem").removeClass("btn-danger")


          if(resultado.tipoResposta=="Imagem"){
            $(".linhaIpotese").hide()
            $(".tbImagem").show()

            var numeroAleatorio = Math.floor(Math.random() * 2) + 1
            var n1=1; var n2=2;
            if(numeroAleatorio==2){
              n1=2; n2=1;
            }
            $(".ipoteseImagem"+n1).attr("src", "../../../livraria/"+resultado.resposta1)
            $(".ipoteseImagem"+n1).attr("id", "resp1")

            $(".ipoteseImagem"+n2).attr("src", "../../../livraria/"+resultado.resposta2)
            $(".ipoteseImagem"+n2).attr("id", "resp2")
          }else{
            $(".linhaIpotese").show()
            $(".tbImagem").hide()
            var numeroAleatorio = Math.floor(Math.random() * 7) + 1
            var n1=1; var n2=2; var n3=3; var n4=4;
            if(numeroAleatorio==2){
              n1=2; n2=3; n3=4; n4=1;
            }else if(numeroAleatorio==3){
              n1=4; n2=3; n3=1; n4=2;
            }else if(numeroAleatorio==4){
              n1=2; n2=1; n3=3; n4=4;
            }else if(numeroAleatorio==5){
              n1=4; n2=3; n3=1; n4=2;
            }else if(numeroAleatorio==6){
              n1=1; n2=2; n3=4; n4=3;
            }else if(numeroAleatorio==7){
              n1=3; n2=2; n3=1; n4=4;
            }
            $(".linhaIpotese.ipotese"+n1).text(resultado.resposta1)
            $(".linhaIpotese.ipotese"+n1).attr("id", "resp1")

            $(".linhaIpotese.ipotese"+n2).text(resultado.resposta2)
            $(".linhaIpotese.ipotese"+n2).attr("id", "resp2")

            $(".linhaIpotese.ipotese"+n3).text(resultado.resposta3)
            $(".linhaIpotese.ipotese"+n3).attr("id", "resp3")

            $(".linhaIpotese.ipotese"+n4).text(resultado.resposta4)
            $(".linhaIpotese.ipotese"+n4).attr("id", "resp4")
          }
        }
      }
      enviarComGet("tipoAcesso="+accao+"&idPQuestao="+idPQuestao);
    }

</script>
