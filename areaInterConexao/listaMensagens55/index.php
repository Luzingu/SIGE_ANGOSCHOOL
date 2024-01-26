<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Lista de Mensagens");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea=7;
    $layouts->designacaoArea="Inter Conexão";
 ?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>

  <style>	



  	#fotoUsuario, .contacts-list img{
  		width: 60px;
  		height: 60px;
		max-width: 60px;
  		max-height: 60px;
  		border-radius: 50%;	
  	}
  	.contacts-list a{
  		outline: none;
  	}
  	.contacts-list .contacts-list-name{
  		font-size: 14pt;
  	}

  	#mensagens .direct-chat-name{
  		font-size: 12pt;
  	}

  	#mensagens .direct-chat-timestamp{
  		font-size: 9pt;
  	}
  	#mensagens .direct-chat-text{
  		font-size: 12pt;
  	}

  	.online_icon{
      height: 12px;
      width:12px;
      display: block;
      background-color: #4cd137;
      border-radius: 50%;
      border:1.5px solid white;
      margin-top: -15px;
      margin-left:48px;
      z-index: 700;
      position: absolute;
    }
    .contacts-list .online_icon{
    	margin-top: 42px;
    }
    .offline_icon{
    	background-color: #c23616 !important;
    }

    table tr td, table{
      vertical-align: middle;
    }

	
  </style>
		
 </head>

<body>
	<?php
	$janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();

  $arrayMensagem=array();

  foreach ($manipulacaoDados->selectArray("mensagens", ['$or'=>[array("idReceptorEnt"=>(int)$_SESSION['idUsuarioLogado']), array("idReceptorMat"=>(int)$_SESSION['idUsuarioLogado']), array("idEmissorEnt"=>(int)$_SESSION['idUsuarioLogado']), array("idEmissorMat"=>(int)$_SESSION['idUsuarioLogado'])]], ['sort'=>array("estadoMensagem"=>-1)]) as $mensagem ) {

        $idExaminar="";
        $tipoUsuario="";
        if($_SESSION["tipoUsuario"]=="aluno" && nelson($mensagem, "idReceptorMat")!=$_SESSION["idUsuarioLogado"] && nelson($mensagem, "idReceptorMat")!=NULL){

            $idExaminar=nelson($mensagem, "idReceptorMat");
            $tipoUsuario="aluno";

        }else if($_SESSION["tipoUsuario"]=="aluno" && nelson($mensagem, "idEmissorMat")!=$_SESSION["idUsuarioLogado"] && nelson($mensagem, "idEmissorMat")!=NULL){
            $idExaminar=nelson($mensagem, "idEmissorMat");
            $tipoUsuario="aluno";
        }else if($_SESSION["tipoUsuario"]!="aluno" && nelson($mensagem, "idReceptorEnt")!=$_SESSION["idUsuarioLogado"] && nelson($mensagem, "idReceptorEnt")!=NULL){
            $idExaminar=nelson($mensagem, "idReceptorEnt");
            $tipoUsuario="entidade";
        }else if($_SESSION["tipoUsuario"]!="aluno" && nelson($mensagem, "idEmissorEnt")!=$_SESSION["idUsuarioLogado"] && nelson($mensagem, "idEmissorEnt")!=NULL){
            $idExaminar=nelson($mensagem, "idEmissorEnt");
            $tipoUsuario="entidade";
        }
        if(seParaAdicionar($arrayMensagem, $idExaminar, $tipoUsuario)=="sim"){
          if($idExaminar!="" && $idExaminar!=NULL){
            if(count($arrayMensagem)<=30){
              $arrayMensagem[] = array('idPMensagem'=>$mensagem["idPMensagem"], "idUsuario"=>$idExaminar, "tipoUsuario"=>$tipoUsuario);
            }
            
          }
        }
  }



  function seParaAdicionar($arrayMensagem, $idExaminar, $tipoUsuario){
      $retorno="sim";
      foreach ($arrayMensagem as $ok) {
          if($ok["idUsuario"]==$idExaminar && $ok["tipoUsuario"]==$tipoUsuario){
              $retorno="nao";
              break;
          }
      }
      return $retorno;
  }
?>
	 <section id="main-content"> 
      <section class="wrapper" id="containers">
        	
        <div class="main-body">

            <div class="row">
                <div class="col-md-12 col-lg-12">
                  <div class="card card-primary card-outline">
                    <div class="card-header">
                      <h3 class="card-title lead bolder"><i class="fa fa-comments"></i> Mensangens</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                      
                      <div class="table-responsive mailbox-messages">
                        <table class="table table-hover table-striped" id="tabelaMensagem">
                          <tbody>
                          <?php foreach ($arrayMensagem as $msg) {

                            $sobreMsg = $manipulacaoDados->selectArray("mensagens", ["idPMensagem"=>$msg["idPMensagem"]]);

                            $cssMsg="";
                            if($sobreMsg[0]["estadoMensagem"]=="F"){
                              $cssMsg="bolder";
                            }
                            $nomeUsuario="";
                            $fotoUsuario="";
                            if($msg["tipoUsuario"]=="aluno"){
                                foreach ($manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula"=>$msg["idUsuario"]]) as $us) {
                                  $nomeUsuario = $us["nomeAluno"];
                                  $fotoUsuario=$us["fotoAluno"];
                                }
                            }else{
                                foreach ($manipulacaoDados->selectArray("entidadesprimaria", ["idPEntidade"=>$msg["idUsuario"]]) as $us) {
                                  $nomeUsuario = $us["nomeEntidade"];
                                  $fotoUsuario=$us["fotoEntidade"];
                                }                                
                            }


                            ?>

                            <tr style="cursor: pointer;" idUsuario="<?php echo $msg['idUsuario'] ?>" tipoUsuario="<?php echo $tipoUsuario; ?>">
                              <td class="mailbox-star"><img src="../../fotoUsuarios/<?php echo $fotoUsuario; ?>" style="width: 80px; height: 80px; border-radius: 50%;"></td>
                              <td class="mailbox-name text-primary lead" style="width: 250px;"><br/><?php echo $nomeUsuario; ?></td>
                              <td class="<?php echo $cssMsg; ?> lead"><?php echo $sobreMsg[0]["textoMensagem"]; ?></td>
                              <td class="text-center lead" style="width: 190px;"> <?php echo $sobreMsg[0]["horaMensagem"]."<br/>".dataExtensa($sobreMsg[0]["dataMensagem"]); ?></td>
                            </tr>
                          <?php } ?>
                          
                          </tbody>
                        </table>
                        <!-- /.table -->
                      </div>
                      <!-- /.mail-box-messages -->
                    </div>
                  </div>
                  <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>

        </div>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

