<?php function areaGestaoGPE ($db, $caminhoRetornar, $verificadorAcesso, $idAnoActual){
  include_once ('manipulacaoDados.php');
  $db = new manipulacaoDados(__DIR__); ?>
    <aside> 
      <div id="sidebar" class="nav-collapse">          
        <!-- sidebar menu start-->
    <ul class="sidebar-menu">
        <li id="paraAnoLectivo">
          <div class="text-center lead anoLectivo"><?php echo $db->numAnoActual; ?></div>
        </li>
        <?php if($verificadorAcesso->verificarAcesso(["FuncionarioDP"], "", "semMensagem", "Especialista", "sim")){ ?>
        <li class="sub-menu">
          <a href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaGestaoGPE/index.php" id="novaMatricula" class="" >
                        <i class="fa fa-user"></i>
                        <span class="lead">Perfil</span>      
          </a>
        </li>
        <?php } ?>
       
        <?php if($verificadorAcesso->verificarAcesso(["areaGestaoGPE"], "", "semMensagem", "Especialista", "sim")){ ?>
          <li class="sub-menu">
            <a href="javascript:;" class="" >
                          <i class="fa fa-users"></i>
                          <span class="lead">Funcionários</span>
                          <span class="menu-arrow arrow_carrot-right"></span>
                      </a>
            <ul class="sub">
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaGestaoGPE/adicionarFuncionarios/index.php' ?>" id="novaMatricula">Adicionar Funcionário</a></li>
             
                <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaGestaoGPE/listaFuncionarios/index.php" id="reconfirmacao">Lista dos Funcionários</a></li>
            </ul>
          </li>
        <?php } ?>
        <?php if($verificadorAcesso->verificarAcesso(["areaGestaoGPE"], "", "semMensagem", "Especialista", "sim")){ ?>
          <li>
            <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaGestaoGPE/direccoesMunicipais/index.php' ?>" class="chamadorContainer" id="painelControl">
                          <i class="fa fa-school"></i>
                          <span class="lead">Direcções Municipais</span>
                      </a>
          </li>
        <?php } ?>
        <?php if($verificadorAcesso->verificarAcesso(["areaGestaoGPE"], "", "semMensagem", "Especialista", "sim")){ ?>
          <li>
            <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaGestaoGPE/escolas/index.php' ?>" class="chamadorContainer" id="painelControl">
                <i class="fa fa-school"></i>
                <span class="lead">Escolas</span>
            </a>
          </li>
        <?php } ?>  
        <?php if($verificadorAcesso->verificarAcesso(["areaGestaoGPE"], "", "semMensagem", "Especialista", "sim")){ ?>
          <li>
            <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaGestaoGPE/definicoesConta1/index.php' ?>" class="chamadorContainer" id="painelControl">
                          <i class="fa fa-cog"></i>
                          <span class="lead">Sobre a Instituição</span>
                      </a>
          </li>
        <?php } ?> 
        <h2 class="sub-menu" style="color:white; font-weight: bolder; margin-top:-20px;"><br/>&nbsp;&nbsp;&nbsp;CPainel</h2>
         <li class="sub-menu">
            <a href="javascript:;" class="">
                          <i class="fa fa-user-tie"></i>
                          <span class="lead">Agentes</span>
                          <span class="menu-arrow arrow_carrot-right"></span>
                      </a>
            <ul class="sub">
              <?php if($verificadorAcesso->verificarAcesso(["areaGestaoGPE"], "", "semMensagem")){ ?>
                <li><a class="chamadorContainer" href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaGestaoGPE/CPainel/adicionarAgentes/index.php" id="divisaoTurmas">Adicionar Agentes</a></li>
                <li><a class="chamadorContainer" href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaGestaoGPE/CPainel/listaAgentes/index.php" id="divisaoTurmas">Lista dos Agentes</a></li>
              <?php } ?>               
            </ul>
          </li>
          <?php if($verificadorAcesso->verificarAcesso(["areaGestaoGPE"], "", "semMensagem")){ ?>
            <li>
              <a href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaGestaoGPE/CPainel/acessoAreas/index.php" class="chamadorContainer" id="painelControl">
                  <i class="fa fa-cog"></i>
                  <span class="lead">Acesso a Áreas</span>
              </a>
            </li>
          <?php } ?>
        </ul>
    </aside>

<?php } ?>