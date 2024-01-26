<?php session_start(); 

    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Lista dos Agentes", "listaAgentes");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">

       #formularioEntidade .modal-dialog{
          width: 70%; 
          margin-left: -35%;
        }
      @media (max-width: 768px) {
            #formularioEntidade .modal-dialog, .modal .modal-dialog{
                width: 94%;
                margin-left: 3%;

            }
      }
      fieldset{
        border:solid rgba(0, 0, 0, 0.6) 2px;
        border-radius: 10px;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 10px;
      }
      fieldset legend{
        width: 150px;
        font-weight: bolder;
      }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar ();
    $layouts->cabecalho();
    $layouts->aside();
  ?>

  <section id="main-content">
        <section class="wrapper" id="containers">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Agentes</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "listaAgentes", array(), "msg")){
            
            
            echo "<script>var listaEntidades = ".json_encode($manipulacaoDados->entidades(array()))."</script>";
         ?>

      <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12 col-lg-12">
                    <button class="btn btn-success btn-lg" id="novoAgente"><i class="fa fa-plus-circle"></i> Novo Agente</button>&nbsp;&nbsp;&nbsp;
                    <select id="tamanhoFolha" class=" lead">
                      <option>A3</option>
                      <option>A2</option>
                      <option>A1</option>
                      <option>A0</option>
                      <option>A4</option>
                    </select>&nbsp;&nbsp;&nbsp;
                       <a href="#" id="pessoalDocente" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Lista de Pessoal Docente</a>
                      
                      <label class="lead btn btn-primary"><i class="fa fa-print"></i> Mapa de Força de Trabalho (<select id="periodoProfessor" class="btn btn-primary">
                        <option value="">Seleccionar</option>
                        <option value="todos">Todos</option>
                        <option>Matinal</option>
                        <option>Vespertino</option>
                        <option>Noturno</option>
                        
                        </select>)</label>
                     
                      <a href="#" id="pessoalPorEspecilaidade" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Lista de Pessoal por Especialidade</a>&nbsp;&nbsp;&nbsp;
                      <a href="#" id="mapaControlProfessorPorDisciplina" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Mapa de Contr. de Prof. por Disciplinas</a>&nbsp;&nbsp;&nbsp;
                      <a href="#" id="mapaOrganizacaoFuniconarios" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Organização dos Funcionários</a>&nbsp;&nbsp;&nbsp;
                      <a href="#" id="pessoalDocentePorIdade" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> Pessoal Docente por Idade</a>&nbsp;&nbsp;&nbsp;
                      <a href="#" id="numeroInternoProfessores" class="lead btn btn-primary visualizadorLista"><i class="fa fa-print"></i> N.º Internos</a>&nbsp;&nbsp;&nbsp;
                      
                      <!--<label class="lead btn btn-primary"><i class="fa fa-print"></i> Professores (<select id="tipoDisciplinaProfessor" class="btn btn-primary">
                        <option value="">Seleccionar</option>
                        <?php /*foreach($manipulacaoDados->selectDistinct("nomedisciplinas", "disciplinas.tipoDisciplina", ["disciplinas.idDiscEscola"=>$_SESSION['idEscolaLogada']], ["disciplinas"]) as $disc){
                          echo "<option value='".$disc["_id"]."'>".$disc["_id"]."</option>";
                        }*/ ?>
                        </select>)</label>!-->
                               
                  </div>
                  <div class="col-md-12 col-lg-12"><br/>
                        <label class="lead">Total de Agentes: <span id="numTProfessores" class="quantidadeTotal"></span></label>     
                    </div>
                </div>
                <?php $includarHtmls->indexAgente(); ?>
            </div>
          </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>

<?php $conexaoFolhas->folhasJs();  $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();  ?>