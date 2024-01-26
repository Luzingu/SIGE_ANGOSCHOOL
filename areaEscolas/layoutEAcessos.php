 <?php function layoutEAcessos ($db){ ?>
  <aside>
            <div id="sidebar" class="nav-collapse ">

              <!-- sidebar menu start-->
              <ul class="sidebar-menu">
                <li id="paraAnoLectivo">
                  <div class="text-center lead anoLectivo">Layout e Acessos</div>
                </li>
                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaEscolas/layoutEAcessos/areasPorInstituicao66/index.php' ?>" class="" >
                                  <i class="fa fa-link"></i>
                                  <span class="lead">Áreas</span>
                    </a>
                  </li>
                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaEscolas/layoutEAcessos/menusPorInstituicao66/index.php' ?>" class="" >
                                  <i class="fa fa-table"></i>
                                  <span class="lead">Menus</span>
                    </a>
                  </li>
              </ul>
              <!-- sidebar menu end-->
            </div>
          </aside>

<?php } ?>
