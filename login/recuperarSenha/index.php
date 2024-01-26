<?php 
    session_cache_expire(60);
    session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
    includar("", "login");
    
    $m = new manipulacaoDados();
    $conexaoFolha = new conexaoFolhas();

    if(!isset($_SESSION["idInstituicaoEntrar"])){
      echo "<script>window.location='".$m->enderecoSite."'</script>";   
    }else if(isset($_SESSION["idOnlineUsuario"])){
      if($_SESSION["tipoUsuario"]=="professor"){
          echo "<script>window.location='../../areaEscolas/areaProfessor/index.php'</script>";         
      }else if($_SESSION["tipoUsuario"]=="administrador"){
          echo "<script>window.location='../../areaAdministrador/areaGestaoEmpresa/index.php'</script>";
      }else if($_SESSION["tipoUsuario"]=="aluno"){
          echo "<script>window.location='../../areaEscolas/areaAluno/index.php'</script>";
      }         
    }
 ?>


<!DOCTYPE html>
<html lang="pt">

<head> 
  <?php $conexaoFolha->folhasCss(); ?>
  <style type="">
    .paragrafo{
      font-size:  14pt;
      color:  black;

    }
  </style>
</head>

<body style="background-color: rgba(0, 0, 0, 0.3);">


        
  <div class="container">
      <div class="paragrafos">
        <p class="text-center lead"><i class="fa fa-key fa-3x"></i></p>
        <h1 class="lead text-center paragrafo"><strong>Recuperar Senha</strong></h1>
        <div class="lead paragrafo " style="color:black; text-align: justify;">Bem vindo a área de recuperação de Senha. Para recuperar a sua senha, siga as seguintes instruções:</div>
        <ol>
          <li class="lead" style="color:black; text-align: justify;">Introduzir o seu número Interno com o endereço da tua Escola.</li>
          <li class="lead" style="color:black; text-align: justify;">Inserir o teu endereço de e-mail que está cadastrado no sistema.</li>
          <li class="lead" style="color:black; text-align: justify;">Clicar em Enviar-me Código e logo lhe será enviada um cógico na tua caixa de e-mail.</li>
        </ol>
      </div>

    <form class="login-form" id="formRecuperarSenha" style="padding: 5px; margin-top: -5px; margin-bottom: 40px;">
      <div class="login-wrap">
        <div class="input-group lead" id="linhaErro" style="color:red; font-weight: 600; font-size: 15pt; text-align: justify;">
        </div>
        <div class="input-group">
          <label class="input-group-addon paraNumeroInterno" for="endereco" style="margin-left:-20px;"><?php echo $_SESSION["abrevNomeEscolaEntrar"]; ?> @</label> 
          <input type="text" class="form-control paraNumeroInterno" name="numeroInterno" id="numeroInterno" placeholder="Número Interno" autofocus required autocomplete="off" style="margin-left:-12px; color: black;" maxlength="14">      
        </div>
        <div class="input-group">
          <span class="input-group-addon paraEmail">@</span>
          <input class="form-control paraEmail" placeholder="E-mail" name="email" id="email" type="email" required>
        </div>
        <input type="hidden" name="action" value="recuperarSenha">
        <button class="btn btn-primary btn-lg btn-block lead" type="submit"><i class="fa fa-send"></i> Enviar-me Código</button>
      </div>
    </form>
    <div class="text-right">
      <div class="credits">
        </div>
    </div>
  </div>
</body>
</html>
<style type="text/css">
  .paraNumeroInterno{
    font-size:22px !important; 
    font-weight: bolder !important;
    text-align:left !important;
  }
  .paraEmail{
    font-size:22px !important;
    font-weight: bolder !important;
  }

  @media (max-width: 700px) {
    .paraNumeroInterno{
      font-size:22px !important; 
    }
    .paraEmail{
      font-size:22px !important;
    }
  }
</style>
<?php $conexaoFolha->folhasJs(); ?>

<script type="text/javascript">
  $(document).ready(function(){
    $("#formRecuperarSenha").submit(function(){


      $("form#formRecuperarSenha #password").css("border", "none");
      $("form#formRecuperarSenha #email").css("border", "none");
      $("form#formRecuperarSenha button[type=submit]").html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> Autenticando...');

        var form = new FormData(document.getElementById("formRecuperarSenha"));
        var x = new XMLHttpRequest();
          x.onreadystatechange = function(){

          if(x.readyState==4){
            $("form#formRecuperarSenha button[type=submit]").html('<i class="fa fa-send"></i> Enviar-me Código');
            if(x.responseText.trim().substring(0, 1)=="0" || x.responseText.trim().substring(0, 1)=="1"){
                $("form#formRecuperarSenha #linhaErro").text(x.responseText.trim().substring(1, x.responseText.trim().length));
                if(x.responseText.trim().substring(0, 1)=="0"){
                  $("form#formRecuperarSenha #numeroInterno").css("border", "solid red 0.1px");
                }else if(x.responseText.trim().substring(0, 1)=="1"){
                  $("form#formRecuperarSenha #email").css("border", "solid red 0.1px");
                }
            }else if(x.responseText.trim()=="enviado"){
                window.location ="../confirmarCodigo"; 
            }
            
          }
        }
        x.open("POST", "manipulacaoDadosDoAjax.php", true);
        x.send(form);

      return false;
    })

  })
</script>
