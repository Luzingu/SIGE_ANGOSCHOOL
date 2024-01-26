<?php 
     function headerUsuario ($db, $caminhoRetornar  , $verificadorAcesso){
      ?>
        <section id="container" class="">

            <header class="header dark-bg">
              <div class="toggle-nav" id="chamadorMenu">                
                <div class="icon-reorder tooltips" data-original-title="Menus" data-placement="bottom">&nbsp;&nbsp;<i class="fa fa-bars"></i></div>
              </div>
              

              <!--logo start-->
              <?php 
                $end = explode("-", $_SESSION["areaActual"]);
                if(count($end)>1){
                  $endereco = trim($end[0]);
                  $_SESSION["areaActual"]= trim($end[1]);
                }else{
                  $endereco = trim($_SESSION["areaActual"]);
                  $_SESSION["areaActual"]= trim($_SESSION["areaActual"]);
                }

                ?>
              <a href="#AngoSchool" class="logo hidden-xs hidden-sm"><?php echo $endereco."@AngoSchool"; ?><!--<span class="lite">Admin</span>!--></a>
              <!--logo end-->

              <div class="top-nav notification-row" >
                <!-- notificatoin dropdown start-->
                <ul class="nav pull-right top-menu">
                   
                  <li id="alert_notificatoin_bar" class="dropdown" id="">

                    <a class="dropdown-toggle chamadorListaMenuCima" href="#" chamar="notificacoes">

                                    <i class="icon-bell-l"></i>
                                    <span class="badge bg-important numeroNotificacoes"></span>
                                </a>
                    <ul class=" listaMenuCima" id="notificacoes" style="display: none; position: absolute; margin-left:  -250px; z-index:   3;">

                       
                    </ul>
                  </li>
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
                    
                    <a data-toggle="dropdown" class="dropdown-toggle chamadorListaMenuCima" href="#" chamar="informUsuario">
                                    <span class="profile-ava">
                                        <img alt="" src="<?php echo $caminhoRetornar.'fotoUsuarios/'.valorArray($verificadorAcesso->sobreUsuarioLogado, "fotoEntidade"); ?>" class="imagemUsuarioCorrente principal">
                                    </span> 
                                    <span class="username apelidoUsuarioCorente"><?php echo explode(" ", valorArray($verificadorAcesso->sobreUsuarioLogado, "nomeEntidade"))[0]; ?></span>
                                    <b class="caret"></b>
                                </a>
                    <ul class="dropdown-menu extended logout listaMenuCima" id="informUsuario">
                      <div class="log-arrow-up"></div>
                      
                      <li>
                        <a href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaGestaoGPE/index.php"><i class="fa fa-user-tie"></i>Gestão do GP</a>
                      </li>
                      <li>
                        <a href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaRelatEEstatistica/index.php"><i class="fa fa-pen"></i>Estatística e Relatórios</a>
                      </li>
                      <li>
                        <a href="<?php echo $caminhoRetornar; ?>areaInterConexao/usuariosConnectados/index.php"><i class="fa fa-share-alt"></i>Inter-Conexão</a>
                      </li>
                      <li>
                        <a href="#" id="trocarSenha"><i class="fa fa-key"></i>Alterar Senha</a>
                      </li>

                      <li>
                        <a href="<?php echo $caminhoRetornar; ?>funcoesAuxiliares.php?termSessao=break"><i class="fa fa-sign-out-alt"></i> Terminar Sessão</a>
                      </li>

                      <li></li>
                    </ul>
                  </li>
                  <!-- user login dropdown end -->
                </ul>
                <!-- notificatoin dropdown end-->
              </div>
            </header> 
      <?php  } ?>