<?php function areaEstatERelat ($db, $caminhoRetornar, $verificadorAcesso, $idAnoActual){
  include_once ('manipulacaoDados.php');
  $db = new manipulacaoDados(__DIR__); ?>
    <aside> 
      <div id="sidebar" class="nav-collapse">          
        <!-- sidebar menu start-->
    <ul class="sidebar-menu">
        <li id="paraAnoLectivo">
          <div class="text-center lead anoLectivo"><?php echo $db->numAnoActual; ?></div>
        </li>
        <li class="sub-menu">
          <a href="<?php echo $caminhoRetornar; ?>areaDireccaoProvincial/areaRelatEEstatistica/index.php" id="novaMatricula" class="">
                        <i class="fa fa-home"></i>
                        <span class="lead">Página Inicial</span>      
          </a>
        </li>
        <li class="sub-menu">
          <a href="javascript:;" class="" >
                        <i class="fa fa-school"></i>
                        <span class="lead">Escolas</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
          </a>
          <ul class="sub">
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/escolas/index.php?privacidade=Pública' ?>" id="novaMatricula">Públicas</a></li>
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/escolas/index.php?privacidade=Privada' ?>" id="novaMatricula">Privadas</a></li>
          </ul>
        </li>
        <li>
          <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/listaAgentesMEDPorInstituicao/index.php' ?>" class="chamadorContainer" id="painelControl">
              <i class="fa fa-users"></i>
              <span class="lead">Agentes do MED</span>
          </a>
        </li>
        <!--<li class="sub-menu">
          <a href="javascript:;" class="" >
                        <i class="fa fa-users"></i>
                        <span class="lead">Agentes do MED</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
          </a>
          <ul class="sub">
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/listaAgentesMED/index.php' ?>" id="novaMatricula">por Categoria da Instituicao</a></li>
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/listaAgentesMEDPorInstituicao/index.php' ?>" id="novaMatricula">por Instituição</a></li>
          </ul>
        </li>!-->

        <?php for($i=1; $i<=2; $i++){
          $mamale="Pública";
          $matondo ="Público";
          if($i==2){
            $mamale="Privada";
            $matondo ="Privado";
          }
        ?>

          <h4 style="padding-left:5px; color:orange; font-weight:bolder;">Ensino <?php echo $matondo; ?></h4>

          <li>
            <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/resumoMatriculas/index.php?privacidade='.$mamale.''; ?>" class="chamadorContainer" id="painelControl">
                <i class="fa fa-user"></i>
                <span class="lead">Resumo de Matrículas</span>
            </a>
          </li>

          <li class="sub-menu">
            <a href="javascript:;" class="" >
                          <i class="fa fa-mail-forward"></i>
                          <span class="">Aproveitamento</span>
                          <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/aproveitamentoDosAlunos/index.php?privacidade='.$mamale.'&trimestre=I';?>" id="novaMatricula">I Trimestre</a></li>
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/aproveitamentoDosAlunos/index.php?privacidade='.$mamale.'&trimestre=II';?>" id="novaMatricula">II Trimestre</a></li>
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/aproveitamentoDosAlunos/index.php?privacidade='.$mamale.'&trimestre=III';?>" id="novaMatricula">III Trimestre</a></li>
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/aproveitamentoDosAlunos/index.php?privacidade='.$mamale.'&trimestre=IV';?>" id="novaMatricula">Período Final</a></li>
            </ul>
          </li>

          <li class="sub-menu">
            <a href="javascript:;" class="" >
                          <i class="fa fa-mail-forward"></i>
                          <span class="">Transferências</span>
                          <span class="menu-arrow arrow_carrot-right"></span>
            </a>
            <ul class="sub">
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/transferenciaEntrada/index.php?privacidade='.$mamale;?>" id="novaMatricula">Entradas</a></li>
              <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/transferenciaSaida/index.php?privacidade='.$mamale; ?>" id="novaMatricula">Saídas</a></li>
            </ul>
          </li>
        <?php } ?>
        <br>
        <li class="sub-menu">
          <a href="javascript:;" class="" >
                        <i class="fa fa-star"></i>
                        <span class="">Quadro de Honra</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
          </a>
          <ul class="sub">
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/melhoresAlunos/index.php?privacidade='.$mamale.'&trimestre=I';?>" id="novaMatricula">I Trimestre</a></li>
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/melhoresAlunos/index.php?privacidade='.$mamale.'&trimestre=II';?>" id="novaMatricula">II Trimestre</a></li>
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/melhoresAlunos/index.php?privacidade='.$mamale.'&trimestre=III';?>" id="novaMatricula">III Trimestre</a></li>
            <li><a class="chamadorContainer lead" href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/melhoresAlunos/index.php?privacidade='.$mamale.'&trimestre=IV';?>" id="novaMatricula">Período Final</a></li>
          </ul>
        </li>
        <li>
          <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/perfilAluno/index.php' ?>" class="chamadorContainer" id="painelControl">
              <i class="fa fa-search"></i>
              <span class="lead">Pesquisar Aluno</span>
          </a>
        </li>
        <li>
          <a href="<?php echo $caminhoRetornar.'areaDireccaoProvincial/areaRelatEEstatistica/perfilEntidade/index.php' ?>" class="chamadorContainer" id="painelControl">
              <i class="fa fa-search"></i>
              <span class="lead">Pesquisar Agente</span>
          </a>
        </li>


        </ul>
    </aside>

<?php } ?>