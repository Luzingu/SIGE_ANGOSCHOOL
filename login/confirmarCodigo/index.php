<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    includar("", "login");
    $m = new manipulacaoDados();

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/conexaoFolhasMae.php';
    if(!isset($_SESSION["recuperacaoSenha"])){
      echo "<script>window.location='".$m->enderecoSite."'</script>";
    }
    $conexaoFolha = new conexaoFolhas();
 ?>


<!DOCTYPE html>
<html lang="en">

<head> 
  <?php $conexaoFolha->folhasCss(); ?>
  <style type="">
    .paragrafo{
      font-size:  14pt;
      color:  black;

    }
  </style>
</head>

<body  class="login-img3-body">


        
  <div class="container">
      <div class="paragrafos">
        <p class="text-center lead"><i class="fa fa-key fa-3x"></i></p>
        <h1 class="lead text-center paragrafo"><strong>Recuperar Senha</strong></h1>

        <div class="lead paragrafo " style="color:black; text-align: justify;">Confirme aqui o código que enviamos na tua conta do E-mail.</div>
      </div><br>

    <form class="login-form" id="formConfirmarCodigo" style="padding: 10px; margin-top: -5px; margin-bottom: 40px;">
      <div class="login-wrap">
        <div class="input-group lead" id="linhaErro" style="color:red; font-weight: 600; font-size: 15pt; text-align: justify;">
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="icon_key_alt"></i></span>
          <input class="form-control" placeholder="Código" name="codigo" id="codigo" type="text" value="" required maxlength="8">
        </div>

        <input type="hidden" name="action" value="confirmarCodigo">
        <button class="btn btn-primary btn-lg btn-block lead" type="submit"><i class="fa fa-check"></i> Confirmar</button>
      </div>
    </form>
    <div class="text-right">
      <div class="credits">
        </div>
    </div>
  </div>
</body>
</html>
<?php $conexaoFolha->folhasJs(); ?>

<script type="text/javascript">
  $(document).ready(function(){
    $("#formConfirmarCodigo").submit(function(){

      $("form#formConfirmarCodigo #codigo").css("border", "none");

        var form = new FormData(document.getElementById("formConfirmarCodigo"));
        var x = new XMLHttpRequest();
        $("form#formConfirmarCodigo button[type=submit]").html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Verificando...');

          x.onreadystatechange = function(){
          if(x.readyState==4){
            $("form#formConfirmarCodigo button[type=submit]").html('<i class="fa fa-check"></i> Confirmar');
            if(x.responseText.trim().substring(0, 1)=="0" || x.responseText.trim().substring(0, 1)=="2"){
                $("form#formConfirmarCodigo #linhaErro").text(x.responseText.trim().substring(1, x.responseText.trim().length));
                if(x.responseText.trim().substring(0, 1)=="0"){
                  $("form#formConfirmarCodigo #codigo").css("border", "solid red 0.1px");
                }
            }else if(x.responseText.trim()=="pode"){
                window.location ="../novaSenha/index.php";
            }
          }
        }
        x.open("POST", "manipulacaoDadosDoAjax.php", true);
        x.send(form);
      return false;
    })

  })
</script>
