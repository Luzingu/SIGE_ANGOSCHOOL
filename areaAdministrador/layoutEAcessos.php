 <?php function layoutEAcessos ($db){ ?>
  <aside>
            <div id="sidebar" class="nav-collapse ">

              <!-- sidebar menu start-->
              <ul class="sidebar-menu">
                <li id="paraAnoLectivo">
                  <div class="text-center lead anoLectivo">Layout e Acessos</div>
                </li>
                <li class="sub-menu">
                  <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/areas66/index.php' ?>" class="" >
                                <i class="fa fa-link"></i>
                                <span class="lead">Áreas</span>
                  </a>
                </li>
                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/menus66/index.php' ?>" class="" >
                                  <i class="fa fa-table"></i>
                                  <span class="lead">Menus</span>
                    </a>
                  </li>
                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/cargos66/index.php' ?>" class="" >
                                  <i class="fa fa-user-circle"></i>
                                  <span class="lead">Cargos</span>
                    </a>
                  </li>
                   <h3 class="sub-menu text-primary" style="color:white; font-weight: bolder; margin-top:-5px;"><br/>&nbsp;&nbsp;&nbsp;Config. / Instituição</h3>

                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/funcionariosEscolas66/index.php' ?>" class="" >
                                  <i class="fa fa-users"></i>
                                  <span class="lead">Funcionários</span>
                    </a>
                  </li>

                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/areasPorInstituicao66/index.php' ?>" class="" >
                                  <i class="fa fa-pen-alt"></i>
                                  <span class="lead">Áreas</span>
                    </a>
                  </li>

                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/menusPorInstituicao66/index.php' ?>" class="" >
                                  <i class="fa fa-table"></i>
                                  <span class="lead">Menus</span>
                    </a>
                  </li>
                  <li class="sub-menu">
                    <a href="<?php echo $db->enderecoArquivos.'areaAdministrador/layoutEAcessos/menusParaTodasEscolas/index.php' ?>" class="" >
                                  <i class="fa fa-table"></i>
                                  <span class="lead">Menus para Todas</span>
                    </a>
                  </li>
              </ul>
              <!-- sidebar menu end-->
            </div>
          </aside>

<?php } ?>
