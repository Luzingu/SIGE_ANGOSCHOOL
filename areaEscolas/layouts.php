<?php
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
    include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/manipulacaoDados.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/verificadorAcesso.php');
    curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/layoutEAcessos.php');

    class layouts extends manipulacaoDados {

      private $caminhoRetornar ="";
      public $totTransferencias=0;
      public  $estadoInscricao;

       function __construct(){

            parent::__construct();
            $this->verificadorAcesso = new verificacaoAcesso();

            if(!isset($_SESSION["idUsuarioLogado"]) || !isset($_SESSION["tipoUsuario"])){
              session_destroy();
              session_unset();
              echo "<script>window.location='".$this->enderecoSite."'</script>";
            }else{
              //Verificar se realmente é usuário desta escola...

              if($_SESSION["tipoUsuario"]=="aluno"){

                  if(count($this->selectArray("alunosmatriculados", ["idPMatricula"], ["idPMatricula"=>$_SESSION['idUsuarioLogado'], "escola.idMatEscola"=>$_SESSION["idEscolaLogada"], "escola.estadoAluno"=>"A"], ["escola"]))<=0){

                    $this->editar("entidadesonline", "estadoExpulsao", ["I"], ["idPOnline"=>$_SESSION["idPOnline"]]);

                    session_destroy();
                    session_unset();
                    echo "<script>window.location='".$this->enderecoSite."'</script>";
                  }
              }else{
                  $zipador =["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "idPEntidade"=>$_SESSION['idUsuarioLogado']];

                  if(valorArray($this->sobreUsuarioLogado, "ninjaF5")!="A")
                    $zipador["escola.estadoActividadeEntidade"]="A";

                  if(count($this->selectArray("entidadesprimaria", ["idPEntidade"], $zipador, ["escola"]))<=0){

                    $this->editar("entidadesonline", "estadoExpulsao", ["I"], ["idPOnline"=>$_SESSION["idPOnline"]]);
                    session_destroy();
                    session_unset();
                    echo "<script>window.location='".$this->enderecoSite."'</script>";
                  }
              }
              if(!$this->verificarSeParaExpulasar()){
                session_destroy();
                session_unset();
                echo "<script>window.location='".$this->enderecoSite."'</script>";
              }

            }
        }
    function cabecalho(){ ?>
      <section id="container" class="">
        <header class="header dark-bg">
          <div class="toggle-nav" id="chamadorMenu">

            <div class="icon-reorder tooltips" data-original-title="Menus" data-placement="bottom">&nbsp;&nbsp;<i class="fa fa-bars"></i></div>
          </div>
          <a href="#AngoSchool" class="logo hidden-xs hidden-sm"><?php echo $this->designacaoArea; ?></a>

          <div class="top-nav notification-row">
            <ul class="nav pull-right top-menu">

              <li id="mail_notificatoin_bar" class="dropdown">
                <a class="dropdown-toggle chamadorListaMenuCima"  chamar="messages" href="#">
                                <i class="icon-envelope-l"></i>
                                <span class="badge bg-important numeroTotalMensagens"></span>
                            </a>
                <ul class="dropdown-menu extended inbox listaMenuCima" id="messages">
                  <div class="notify-arrow notify-arrow-blue"></div>
                </ul>
              </li>
              <li class="dropdown">
                <?php
                $foto = valorArray($this->sobreUsuarioLogado, "fotoEntidade");
                $nome = valorArray($this->sobreUsuarioLogado, "nomeEntidade");
                if($_SESSION["tipoUsuario"]=="aluno"){
                  $foto = valorArray($this->sobreUsuarioLogado, "fotoAluno");
                  $nome = valorArray($this->sobreUsuarioLogado, "nomeAluno");
                }


                ?>
                <a data-toggle="dropdown" class="dropdown-toggle chamadorListaMenuCima" href="#" chamar="informUsuario">
                  <span class="profile-ava">
                      <img alt="" src="<?php echo $this->enderecoArquivos.'/fotoUsuarios/'.$foto; ?>" class="imagemUsuarioCorrente principal">
                  </span>
                  <span class="username apelidoUsuarioCorente"><?php echo explode(" ", $nome)[0]; ?></span>
                  <b class="caret"></b>
                </a>

                <ul class="dropdown-menu extended logout listaMenuCima" id="informUsuario">
                  <div class="log-arrow-up"></div>

                  <?php
                  if($_SESSION["tipoUsuario"]=="aluno"){ ?>
                    <li style="height: 36px; font-weight: 550;">
                      <a style="height: 36px; font-weight: 550;" href="<?php echo $this->verificadorAcesso->enderecoArquivos; ?>areaEscolas/areaAluno/index.php"><i class="fa fa-user"></i>Área do Aluno</a>
                    </li>
                  <?php }else if($_SESSION["tipoUsuario"]=="professor"){

                    foreach($this->selectArray("areas", ["designacaoArea", "icone", "idPArea", "eGratuito"], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], "idPArea"=>array('$nin'=>[1, 7, 8])], ["instituicoes"]) as $area){

                      if($this->verificadorAcesso->verificarAcesso($area["idPArea"], ["qualquerAcesso"], array(), "", valorArray($area, "eGratuito") )){

                      $linkArea = "areas.php?idPArea=".$area["idPArea"];
                      if($area["idPArea"]==2){
                        $linkArea = "index.php";
                      }
                       ?>
                        <li style="height: 36px; font-weight: 550;">
                          <a style="height: 36px; font-weight: 550;" href="<?php echo $this->enderecoArquivos."areaEscolas/".$linkArea; ?>"><i class="<?php echo $area["icone"]; ?>"></i><?php echo $area["designacaoArea"]; ?></a>
                        </li>
                        <?php
                      }
                    } ?>
                  <?php
                  if(valorArray($this->sobreUsuarioLogado, "nivelSistemaEntidade", "escola")==0 || valorArray($this->sobreUsuarioLogado, "LUZL", "escola")=="V"){ ?>
                  <li style="height: 36px; font-weight: 550;">
                    <a style="height: 36px; font-weight: 550;" href="<?php echo $this->verificadorAcesso->enderecoArquivos; ?>areaEscolas/layoutEAcessos/index.php"><i class="fa fa-gamepad"></i> Layout e Acessos</a>
                  </li>
                <?php }

                  if($_SESSION['idUsuarioLogado']==35 || $this->verificadorAcesso->verificarAcesso(8, ["qualquerAcesso"], array(), "")){ ?>
                    <li style="height: 36px; font-weight: 550;">
                      <a style="height: 36px; font-weight: 550;" href="<?php echo $this->verificadorAcesso->enderecoArquivos; ?>areaEntretenimento/index.php"><i class="fa fa-gamepad"></i> Entretenimento</a>
                    </li>
                  <?php }
                  } ?>

                  <li style="height: 36px; font-weight: 550;">
                    <a style="height: 36px; font-weight: 550;" href="<?php echo $this->verificadorAcesso->enderecoArquivos; ?>areaInterConexao/usuariosConnectados55/index.php"><i class="fa fa-share-alt"></i>Inter-Conexão</a>
                  </li>

                  <li style="height: 36px; font-weight: 550;">
                    <a style="height: 36px; font-weight: 550;" href="#" id="trocarSenha"><i class="fa fa-key"></i>Alterar Senha</a>
                  </li>

                  <li style="height: 36px; font-weight: 550;">
                    <a style="height: 36px; font-weight: 550;" href="<?php echo $this->enderecoArquivos; ?>funcoesAuxiliares.php?termSessao=break"><i class="fa fa-sign-out-alt"></i> Terminar Sessão</a>
                  </li>

                  <li></li>
                </ul>
              </li>
              <!-- user login dropdown end -->
            </ul>
            <!-- notificatoin dropdown end-->
          </div>
        </header>

   <?php }
    function aside($idPArea=""){
      if($idPArea==""){
        $idPArea=$this->idPArea;
      }

        if($idPArea==0){
          layoutEAcessos($this);
        }else if($idPArea==8 || $idPArea==7){ ?>
         <aside>
          <div id="sidebar" class="nav-collapse">
            <!-- sidebar menu start-->
            <ul class="sidebar-menu">
              <li id="paraAnoLectivo">
                  <div class="text-center lead anoLectivo"><?php echo $this->numAnoActual; ?></div>
              </li>

         <?php foreach($this->selectArray("menus", [], ["idAreaPorDefeito"=>$idPArea], [], "", [], ["ordenacao"=>1]) as $a){

              $subMenus = isset($a["subMenus"])?$a["subMenus"]:array();
              if(count($subMenus)>0){
           ?>
            <li class="sub-menu">
              <a href="javascript:;" class="" >
                <i class="<?php echo valorArray($a, "icone"); ?>"></i>
                <span class="lead"><?php echo valorArray($a, "designacaoMenu"); ?></span>
                <span class="menu-arrow arrow_carrot-right"></span>
              </a>
              <ul class="sub">
                <?php foreach($subMenus as $menu){
                  if($this->verificadorAcesso->verificarAcesso($idPArea, [valorArray($menu, "identificadorSubMenu")], array(), "") &&  file_exists($_SERVER["DOCUMENT_ROOT"]."/angoschool/".explode("?", $menu["linkSubMenu"])[0])){
                ?>
                  <li><a class="lead" href="<?php echo $this->enderecoArquivos.$menu["linkSubMenu"]; ?>" id="novaMatricula"><?php echo $menu["designacaoSubMenu"]; ?></a></li>
                <?php } } ?>
              </ul>
            </li>

          <?php } else {

            $linkMenu = $a["linkMenu"];
            if($a["identificadorMenu"]=="nivelAcesso"){
              $linkMenu ="areaEscolas/areaSecretaria/niveisAcessoSecretaria/index.php?idPArea=".valorArray($a, "idArea", "instituicoes");
            }
            if($this->verificadorAcesso->verificarAcesso(valorArray($a, "idArea", "instituicoes"), [$a["identificadorMenu"]], array(), "") && file_exists($_SERVER["DOCUMENT_ROOT"]."/angoschool/".explode("?", $linkMenu)[0])){ ?>
              <li class="sub-menu">
                <a href="<?php echo $this->enderecoArquivos.$linkMenu; ?>" class="" >
                              <i class="<?php echo valorArray($a, "icone"); ?>"></i>
                              <span class="lead"><?php echo $a["designacaoMenu"]; ?></span>
                </a>
              </li>
                <?php  } } ?>
              <?php }
            }else{ ?>
              <aside>
              <div id="sidebar" class="nav-collapse">
                <!-- sidebar menu start-->
                <ul class="sidebar-menu">
                  <li id="paraAnoLectivo">
                      <div class="text-center lead anoLectivo"><?php echo $this->numAnoActual; ?></div>
                  </li>
            <?php
              if(!isset($_SESSION['layout_'.$idPArea])){

                $_SESSION['layout_'.$idPArea] ='';

                $mariaMengi = $this->selectArray("menus", [], ["instituicoes.idEscola"=>$_SESSION['idEscolaLogada'], "instituicoes.idArea"=>$idPArea], ["instituicoes"], "", [], ["ordenacao"=>1]);

                $mariaMengi = ordenar($mariaMengi, "ordemMenu ASC");


                foreach($mariaMengi as $a){

                  $subMenus = isset($a["subMenus"])?$a["subMenus"]:array();
                  if(count($subMenus)>0){

                    $_SESSION['layout_'.$idPArea] .='
                    <li class="sub-menu">
                      <a href="javascript:;" class="" >
                        <i class="'.valorArray($a, "icone").'"></i>
                        <span class="lead">'.valorArray($a, "designacaoMenu").'</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
                      </a>
                      <ul class="sub">';
                    foreach($subMenus as $menu){
                      if($this->verificadorAcesso->verificarAcesso(valorArray($a, "idArea", "instituicoes"), [valorArray($menu, "identificadorSubMenu")], array(), "", valorArray($a, "eGratuito")) && file_exists($_SERVER["DOCUMENT_ROOT"]."/angoschool/".explode("?", $menu["linkSubMenu"])[0])){

                          $_SESSION['layout_'.$idPArea] .='<li><a class="lead" href="'.$this->enderecoArquivos.$menu["linkSubMenu"].'" id="novaMatricula">'.$menu["designacaoSubMenu"].'</a></li>';
                      }
                    }
                    $_SESSION['layout_'.$idPArea] .='</ul></li>';
                }else {

                  $linkMenu = $a["linkMenu"];
                  if($a["identificadorMenu"]=="nivelAcesso"){
                    $linkMenu ="areaEscolas/areaSecretaria/niveisAcessoSecretaria/index.php?idPArea=".valorArray($a, "idArea", "instituicoes");
                  }
                if($this->verificadorAcesso->verificarAcesso(valorArray($a, "idArea", "instituicoes"), [$a["identificadorMenu"]], array(), "", valorArray($a, "eGratuito")) && file_exists($_SERVER["DOCUMENT_ROOT"]."/angoschool/".explode("?", $linkMenu)[0]) ){

                  $_SESSION['layout_'.$idPArea] .='<li class="sub-menu">
                    <a href="'.$this->enderecoArquivos.$linkMenu.'" class="" >
                                  <i class="'.valorArray($a, "icone").'"></i>
                                  <span class="lead">'.$a["designacaoMenu"].'</span>
                    </a>
                  </li>';
                } }
              }

              if((valorArray($this->sobreUsuarioLogado, "BACKUP", "escola")=="V" || $_SESSION['idUsuarioLogado']==35) && (!($_SERVER['SERVER_NAME']=="angoschool.com" || $_SERVER['SERVER_NAME']=="angoschool.org"))){

                $_SESSION['layout_'.$idPArea] .='<li class="sub-menu">
                  <a href="'.$this->enderecoArquivos.'areaEscolas/areaDirector/backupDados/index.php?idPArea='.$idPArea.'" class="" >
                                <i class="#"></i>
                                <span class="lead"><i class="fa fa-save"></i>BACKUP DOS DADOS</span>
                  </a>
                </li>
                <li class="sub-menu">
                  <a href="'.$this->enderecoArquivos.'areaEscolas/areaDirector/downloadArquivos/index.php?idPArea='.$idPArea.'" class="" >
                                <i class="#"></i>
                                <span class="lead"><i class="fa fa-save"></i>Download de Arquivos</span>
                  </a>
                </li>';
              }
            }

            echo $_SESSION['layout_'.$idPArea];


         } ?>
        </ul>
    </aside>

    <?php
    }
} ?>
