<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    includar("", "login");
    $m = new manipulacaoDados();
    if(!isset($_SESSION["recuperacaoSenha"])){
      echo "<script>window.location='".$m->enderecoSite."'</script>";
    }
    $conexaoFolha = new conexaoFolhas();
 ?>


<!DOCTYPE html>
<html lang="pt-br">

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

    <form class="login-form" id="formTrocarSonhaRecuper" style="padding: 10px; margin-top: 20px; margin-bottom: 40px;">
      <p class="text-center lead"><i class="fa fa-key fa-3x"></i></p>
        

        <h1 class="lead paragrafo " style="color:black; text-align: justify; font-size: 20pt; line-height: 35px;">Mantenha a tua conta segura, usando uma Senha Segura.</h1><br>
      <div class="login-wrap">
        <div class="input-group lead" id="linhaErro" style="color:red; font-weight: 600; font-size: 15pt; text-align: justify;">
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="icon_key_alt"></i></span>
          <input class="form-control" placeholder="Nova Palavra Passe" name="novaPassword" id="novaPassword" type="password" value="" required minlength="8">
        </div>

        <div class="input-group">
          <span class="input-group-addon"><i class="icon_key_alt"></i></span>
          <input class="form-control" placeholder="Confirmar Palavra Passe" name="confirmarPasword" id="confirmarPasword" type="password" value="" minlength="8" required>
        </div>

        <input type="hidden" name="action" value="trocarSenhaConfirm">
        <button class="btn btn-primary btn-lg btn-block lead" type="submit"><i class="fa fa-check"></i> Trocar e Entrar</button>
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
    $("#formTrocarSonhaRecuper").submit(function(){

      $("form#formTrocarSonhaRecuper #novaPassword, #confirmarPasword").css("border", "none");

        var form = new FormData(document.getElementById("formTrocarSonhaRecuper"));

        $("form#formTrocarSonhaRecuper button[type=submit]").html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Verificando...');

        var x = new XMLHttpRequest();
          x.onreadystatechange = function(){
          if(x.readyState==4){
            $("form#formTrocarSonhaRecuper button[type=submit]").html('<i class="fa fa-check"></i> Confirmar');
            if(x.responseText.trim().substring(0, 1)=="0" || x.responseText.trim().substring(0, 1)=="2"){
                $("form#formTrocarSonhaRecuper #linhaErro").text(x.responseText.trim().substring(1, x.responseText.trim().length));

                if(x.responseText.trim().substring(0, 1)=="0"){
                  $("form#formTrocarSonhaRecuper #novaPassword, #confirmarPasword").css("border", "solid red 0.1px");
                }
            }else{
                window.location =x.responseText.trim()+"?login=sim";
            }
            
          }
        }
        x.open("POST", "manipulacaoDadosDoAjax.php", true);
        x.send(form);
      return false;
    })

  })
</script>
