<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar=''</script>";
    includar();
    $manipulacaoDados = new manipulacaoDados("Mensagens");
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


    #usuarios .imgUsuario{
      height: 70px;
      width: 70px;
      max-height: 70px;
      max-width: 70px;
    }
    #usuarios  .link{
      display: block;
      outline: none;
      text-decoration: none;
    }
    #usuarios .users-list-name{
      font-weight: bolder;
      color: black;
      font-size: 11pt;
    }
    #usuarios li{
      width: 50% !important;
    }

  
  </style>
    
 </head>

<body>
  <?php
  $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
  
  $entidade = isset($_GET["usuario"])?$_GET["usuario"]:"";
  $entidade = explode("_", $entidade);
  $tipoUsuario = $entidade[0];
  $idPUsuario = isset($entidade[1])?$entidade[1]:"";
  $dataSaida = strtotime($manipulacaoDados->dataSistema.$manipulacaoDados->tempoSistema." - 1200 seconds");
 
  $estadoUsuario="off";
  $nomeUsuario="";
  $fotoUsuario="";
  $link="";
  $cursoClasse="";

  $estadoUsuario='<span class="online_icon offline_icon"></span>';

  if($tipoUsuario=="entidade"){

    $arr = $manipulacaoDados->selectArray("entidadesprimaria", ["nomeEntidade", "fotoEntidade", "idPEntidade", "escola.nivelSistemaEntidade", "escola.idEntidadeEscola"], ["idPEntidade"=>$idPUsuario], ["escola"]);

    $arr = $manipulacaoDados->anexarTabela2($arr, "escolas", "idPEscola", "idEntidadeEscola", "escola");

    if(count($manipulacaoDados->selectArray("entidadesonline", ["idPOnline"], ["estadoExpulsao"=>"A", "dataSaida"=>date("Y-m-d", $dataSaida), "horaSaida"=>array('$gt'=>date("H:i:s", $dataSaida)), "idUsuarioLogado"=>$idPUsuario], [], 1))>0){
      $estadoUsuario='<span class="online_icon"></span>';
    }

    $nomeUsuario = valorArray($arr, "nomeEntidade");
    $fotoUsuario ="../../fotoUsuarios/".valorArray($arr, "fotoEntidade");
    $idPUsuario = valorArray($arr, "idPEntidade");
    $cursoClasse = valorArray($arr, "nivelSistemaEntidade", "escola")." - ".valorArray($arr, "abreviarNomeEscola");
  }else{

    $arr = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "fotoAluno", "idPMatricula", "escola.classeActualAluno", "escola.idMatCurso", "escola.idMatEscola"], ["idPMatricula"=>$idPUsuario], ["escola"]);
    $arr = $manipulacaoDados->anexarTabela2($arr, "escolas", "idPEscola", "idMatEscola", "escola");
    $arr = $manipulacaoDados->anexarTabela2($arr, "nomecursos", "idPNomeCurso", "idMatCurso", "escola");

    if(count($manipulacaoDados->selectArray("entidadesonline", ["idPOnline"], ["estadoExpulsao"=>"A", "dataSaida"=>date("Y-m-d", $dataSaida), "horaSaida"=>array('$gt'=>date("H:i:s", $dataSaida)), "idUsuarioLogado"=>$idPUsuario], [], 1))>0){
      $estadoUsuario='<span class="online_icon"></span>';
    }

    $nomeUsuario = valorArray($arr, "nomeAluno");
    $fotoUsuario ="../../fotoUsuarios/".valorArray($arr, "fotoAluno");
    $idPUsuario = valorArray($arr, "idPMatricula");
    $cursoClasse = valorArray($arr, "classeActualAluno", "escola")."ª ".valorArray($arr, "abreviarNomeEscola")." - ";
    if(strlen(valorArray($arr, "nomeEscola"))>20){
      $cursoClasse .= valorArray($arr, "abreviarNomeEscola");
    }else{
      $cursoClasse .=valorArray($arr, "nomeEscola");
    }
  }
  echo "<script>var idPUsuario=".$idPUsuario."</script>";
  echo "<script>var tipoUsuario='".$tipoUsuario."'</script>";

  $usuarioLogado = $_SESSION['tbUsuario']."_".$_SESSION["idUsuarioLogado"];
  $mensagens = $manipulacaoDados->selectArray("mensagens", [], ["emissor"=>['$in'=>[$usuarioLogado, $tipoUsuario."_".$idPUsuario]], "receptor"=>['$in'=>[$usuarioLogado, $tipoUsuario."_".$idPUsuario]]], [], "", [], ["idPMensagem"=>-1]);

  $manipulacaoDados->editar("mensagens", "estadoMensagem", ["V"], ['$or'=>[["emissor"=>['$in'=>[$usuarioLogado, $tipoUsuario."_".$idPUsuario]]], ["receptor"=>['$in'=>[$usuarioLogado, $tipoUsuario."_".$idPUsuario]]]], "estadoMensagem"=>"F"]);

  echo "<script>var listaMensagens=".json_encode($mensagens)."</script>";
  echo "<script>var usuarioLogado='".$usuarioLogado."'</script>";

?>
   <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">

          
          <div class="col-md-7 col-lg-7 chat">
            <div class="card direct-chat direct-chat-primary">
              <div class="card-header">
                <div class="card-title" style="font-size: 14pt;">
                  <img src="<?php echo $fotoUsuario; ?>" id="fotoUsuario">
                  <?php echo $estadoUsuario; ?>

                  <p style="margin-top: -55px; margin-left: 70px;"><strong><?php echo $nomeUsuario; ?></strong></p>
                  <p style="margin-top: -15px; margin-left: 70px; font-size: 11pt; color: #428bca;"><strong><i><?php echo $cursoClasse; ?></strong></i></p>
                 
                </div>

                <div class="card-tools visible-sm visible-xs"><br>

                  <button type="button" class="btn btn-tool text-success" title="Usuários Online" data-widget="chat-pane-toggle" style="font-size: 16pt;">
                    <i class="fas fa-user-circle"></i>
                  </button>

                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body" ><br/>
                <form method="post" style="margin:5px;" id="novaMensagem">

                        <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          
                         <input type="text" placeholder="Digite aqui a sua mensagem ..." class="form-control" style="font-size: 12pt; height: 40px;" name="mensagem" id="mensagem" autocomplete="off" required="">
                         <span class="input-group-addon" style="padding: 0px; background-color: #428bca;">
                            <button type="submit" id="btnSubmit" style="font-size: 12pt; border:none;" class="btn btn-primary"><i class="fas fa-location-arrow"></i> Enviar</button>
                         </span>
                
                        </div> 

                  <input type="hidden" name="idPUsuario" id="idPUsuario" value="<?php echo $idPUsuario; ?>">
              <input type="hidden" name="tipoUsuario" id="tipoUsuario" value="<?php echo $tipoUsuario; ?>">
              <input type="hidden" name="action" value="enviarMensagem">
                </form>
                <!-- Conversations are loaded here -->
                <div class="direct-chat-messages" style="min-height: 500px; height: inherit;" id="mensagens">

                </div>
                
              </div>
            </div>
          </div>

        </div>

    </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

