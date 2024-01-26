<?php session_start();
    
    if(!isset($_SESSION["abrevNomeEscolaEntrar"])){
      echo "<script>window.location='../../../'</script>";
    }else if(isset($_SESSION["idOnlineUsuario"])){
      if($_SESSION["tipoUsuario"]=="professor"){
          echo "<script>window.location='../../areaEscolas/areaProfessor/index.php'</script>";         
      }else if($_SESSION["tipoUsuario"]=="administrador"){
          echo "<script>window.location='../../areaAdministrador/areaGestaoEmpresa/index.php'</script>";
      }else if($_SESSION["tipoUsuario"]=="aluno"){
          echo "<script>window.location='../../areaEscolas/areaAluno/index.php'</script>";
      }         
    }else{
      echo "<script>var caminhoRecuar='../../'</script>";
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/login/funcoesAuxiliares.php';
      includar("", "login"); 
      $conexaoFolha = new conexaoFolhas();
    }
    $m = new manipulacaoDadosMae();

 ?>


<!DOCTYPE html>
<html lang="en">

<head> 
  <?php $conexaoFolha->folhasCss(); ?>
</head>

<body class="" style="background-image: url('../../icones/fontLogin.jpg');">

  <div class="container" style="margin-top:10%;" >


    <form class="login-form" id="formLogin" style="padding: 10px; background-color: rgba(10,10,10,0.4); border:1px solid #FFF; border-radius: 10px;" method="POST" >
      <div class="login-wrap">

        <p class="login-img"><i class="icon_lock_alt" style="color:#fff;"></i></p>
        <center>
        <div class="input-group lead" id="linhaErro"  style=" font-weight: 600; font-size: 14pt; display: none; text-align: center; background-color: transparent; color: red;">
          
        </div><br>
        </center>
        <div class="input-group">
          <label class="input-group-addon paraNumeroInterno" for="endereco" style="margin-left:-20px;"><?php echo $_SESSION["abrevNomeEscolaEntrar"]; ?> @</label>
          <input type="text" class="form-control paraNumeroInterno" name="numeroInterno" id="numeroInterno" placeholder="Número Interno" autofocus required autocomplete="off" style="margin-left:-12px; color: black;" maxlength="14">          
        </div>
        <div class="input-group">
          <span class="input-group-addon paraSenha"><i class="icon_key_alt"></i></span>
          <input class="form-control paraSenha" placeholder="Senha" name="password" id="password" style="margin-left:-12px;" type="password" value="" required>
        </div>
        <input type="hidden" name="action" value="fazerLogin">

      <div class="col-md-12"> 
     
        <button class="btn btn-primary btn-lg btn-block lead" type="submit" style=" float: right; border-radius: 5px;"><i class="fa fa-sign-out-alt"></i> Entrar</button>
        </div><br/><br/>
        <label class="checkbox" >
        <span class="pull-right"> <a href="../recuperarSenha" style="color:white !important;"> Esqueci a minha palavra passe?</a></span>
        </label>
        <br>
    </div>

    </form>
    <div class="text-right">
      <div class="credits">
        </div>
    </div>
  </div>
</body>
</html>
<?php $conexaoFolha->folhasJs("sim"); ?>
<script type="text/javascript" src="script2.js"></script>

<style type="text/css">
  .paraNumeroInterno{
    font-size:26px !important; 
    font-weight: bolder !important;
    text-align:left !important;
  }
  .paraSenha{
    font-size:26px !important;
    font-weight: bolder !important;
  }

  @media (max-width: 700px) {
    .paraNumeroInterno{
      font-size:17px !important; 
    }
    .paraSenha{
      font-size:20px !important;
    }
  }
</style>