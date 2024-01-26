<?php session_start();
    if(!isset($_SESSION["directorioPaterno"])){
      $protocolo = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS']=="on") ? "https" : "http");
      echo "<script>window.location='".$protocolo."://".$_SERVER["HTTP_HOST"]."'</script>";
    }       
    include_once $_SESSION["directorioPaterno"].'angoschool/areaDireccaoProvincial/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $caminhoRetornar = retornarCaminhoRecuarArquivosPhp(__DIR__);
    $manipulacaoDados = new manipulacaoDados(__DIR__, "Escolas");
    $includarHtmls = new includarHtmls(__DIR__);
    $janelaMensagens = new janelaMensagens(__DIR__);
    $conexaoFolhas = new conexaoFolhas(__DIR__);
    $verificacaoAcesso = new verificacaoAcesso(__DIR__);
    $layouts = new layouts(__DIR__);
    $_SESSION["areaActual"]="Relatório e Estatística";
 ?>

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="">

       #formularioEscola .modal-dialog{
          width: 60%; 
          margin-left: -30%;
        }
      @media (max-width: 768px) {
            #formularioEscola .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
      }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
   $layouts->headerUsuario();
    $layouts->areaEstatERelat();
    $usuariosPermitidos[] = "aRelEstatistica";

    $idPEscola = isset($_GET["idPEscola"])?$_GET["idPEscola"]:$manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM', 'DP') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC");

    if(count($manipulacaoDados->selectArray("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM', 'DP') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola AND idPEscola=:idPEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", $idPEscola], "nomeEscola ASC"))<=0){

      $idPEscola = $manipulacaoDados->selectUmElemento("escolas", "idPEscola", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM', 'DP') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC");
    }
    echo "<script>var idPEscola='".$idPEscola."'</script>";
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class=" navbar-brand" style="color: white;"><strong><i class="fa fa-school"></i> <?php echo "Agentes do Ministério da Educação" ?></strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($usuariosPermitidos)){

          $listaAgentesMED = array();

          foreach($manipulacaoDados->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "DISTINCT idPEntidade", "estadoActividadeEntidade=:estadoActividadeEntidade AND provincia=:provincia AND idPEscola not in (4,7) AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND (naturezaVinc not in ('Colaborador') OR naturezaVinc IS NULL) AND idPEscola=:idPEscola", ["A", valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", "A", $idPEscola], "nomeEntidade ASC") as $a){

            $listaAgentesMED = array_merge($listaAgentesMED, $manipulacaoDados->selectArray("entidadesprimaria LEFT JOIN dadosadicionasentidade ON idDAEntidade=idPEntidade LEFT JOIN entidade_escola ON idFEntidade=idPEntidade LEFT JOIN escolas ON idPEscola=idEntidadeEscola", "*", "estadoActividadeEntidade=:estadoActividadeEntidade AND provincia=:provincia AND idPEscola not in (4,7) AND privacidadeEscola=:privacidadeEscola AND estadoEscola=:estadoEscola AND (naturezaVinc not in ('Colaborador') OR naturezaVinc IS NULL) AND idPEntidade=:idPEntidade AND idPEscola=:idPEscola", ["A", valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública", "A", $a->idPEntidade, $idPEscola], "nomeEntidade ASC LIMIT 1"));
          }
          echo "<script>var listaAgentes = ".json_encode($listaAgentesMED)."</script>";



         ?>
    
      
            <div class="card">              
              <div class="card-body fotFoto">
                <div class="row">
                  <div class="col-md-5 col-lg-5">
                    <label class="">Instituição</label>
                    <select class="form-control lead" id="idPEscola">
                      <?php foreach($manipulacaoDados->selectArray("escolas", "*", "idPEscola not in (4, 7) AND tipoInstituicao in ('escola', 'DM', 'DP') AND provincia=:provincia AND privacidadeEscola=:privacidadeEscola", [valorArray($manipulacaoDados->sobreUsuarioLogado, "provincia"), "Pública"], "nomeEscola ASC") as $a){
                        echo "<option value='".$a->idPEscola."'>".$a->nomeEscola."</option>";
                      } ?>
                    </select>
                  </div>
                  <div class="col-md-6 col-lg-6"><br>
                    <label class="">Total: <span id="numTEscolas" class="quantidadeTotal"></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label class="">Feminino: <span id="sexoFeminino" class="quantidadeTotal"></span></label>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-lg-12">

                    <select id="tamanhoFolha" class=" lead">
                      <option>A3</option>
                      <option>A2</option>
                      <option>A1</option>
                      <option>A0</option>
                      <option>A4</option>
                    </select>&nbsp;&nbsp;&nbsp;
                       <a href="#" id="pessoalDocente" class="btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Lista de Pessoal Docente</a>
                      
                      <label class="btn btn-primary"><i class="fa fa-print"></i> Mapa de Força de Trabalho (<select id="periodoProfessor" class="btn btn-primary">
                        <option value="">Seleccionar</option>
                        <option value="todos">Todos</option>
                        <option>Matinal</option>
                        <option>Vespertino</option>
                        <option>Noturno</option>
                        
                        </select>)</label>
                     
                      <a href="#" id="pessoalPorEspecilaidade" class="btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Lista de Pessoal por Especialidade</a>&nbsp;&nbsp;&nbsp;
                      <!--<a href="#" id="mapaControlProfessorPorDisciplina" class="btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Mapa de Contr. de Prof. por Disciplinas</a>&nbsp;&nbsp;&nbsp;!-->
                      <a href="#" id="mapaOrganizacaoFuniconarios" class="btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Organização dos Funcionários</a>&nbsp;&nbsp;&nbsp;
                      <!--<a href="#" id="pessoalDocentePorIdade" class="btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Pessoal Docente por Idade</a>&nbsp;&nbsp;&nbsp;
                      <a href="#" id="numeroInternoProfessores" class="btn btn-primary visualizadorLista"><i class="fa fa-print"></i> N.º Internos</a>&nbsp;&nbsp;&nbsp;!-->
                      
                      <label class="btn btn-primary"><i class="fa fa-print"></i> Professores (<select id="tipoDisciplinaProfessor" class="btn btn-primary">
                        <option value="">Seleccionar</option>
                        <option>FG</option>
                        <option>FE</option>
                        <option>Op</option>
                        <option>CSC</option>
                        <option>CC</option>
                        <option>CTTP</option>
                      </select>)</label>
                               
                  </div>
                  <div class="col-md-12 col-lg-12"><br/>
                        <label class="lead">Total: <span id="numTProfessores" class="quantidadeTotal"></span></label>     
                    </div>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                  <thead class="corPrimary">
                        <tr>
                            <th class=" text-center">Nº</th>
                            <th class=" font-weight-bolder "><strong>Nome Completo</strong></th>
                            <th class=" text-center"><strong>N.º Agente</strong></th>
                            <th class=" font-weight-bolder"><strong>Categoria</strong></th>
                            <th class=" text-center"><strong>Inicio</strong></th>
                            <th class=" text-center"><strong>Função</strong></th>
                        </tr>
                    </thead>
                    <tbody id="tabEscola">
                    </tbody>
                </table>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->dataList(); $includarHtmls->formTrocarSenha(); ?>