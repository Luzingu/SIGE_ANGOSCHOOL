<?php session_start();
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Reconfirmação", "reconfirmacao");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->listaClassesPorCurso();
 ?> 

 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-circle"></i> Reconfirmação de Matricula</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso("", "reconfirmacao", array(), "msg")){ 

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
          echo "<script>var luzingu='".$luzingu."'</script>";
          $luzingu = explode("-", $luzingu);
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:""; 

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";

          $classeCondicionar=$classe;
          $expl = explode("_", $classe);

          $condicao =["escola.classeActualAluno"=>$classe, "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoAluno"=>"A"];
          if($classe>=10){
              $condicao["escola.idMatCurso"]=$idCurso;
          }
          if(count($expl)>1){
            $condicao["escola.classeActualAluno"]=10;
            $condicao["escola.idMatCurso"]=['$in'=>array(null, "", 0)];
          }

          $luzinguLuame = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "sexoAluno", "dataNascAluno", "biAluno", "dataEBIAluno", "paiAluno", "maeAluno", "encarregadoEducacao", "telefoneAluno", "emailAluno", "fotoAluno", "dataCaducidadeBI", "estadoAcessoAluno", "tipoDocumento", "localEmissao", "numeroProcesso", "deficienciaAluno", "numeroInterno", "deficienciaAluno", "tipoDeficienciaAluno", "paisNascAluno", "provNascAluno", "municNascAluno", "comunaNascAluno", "idPMatricula", "escola.estadoDeDesistenciaNaEscola","escola.idMatAnexo","escola.idGestLinguaEspecialidade","escola.idGestDisEspecialidade","escola.periodoAluno","escola.numeroProcesso","escola.idMatCurso","escola.classeActualAluno", "escola.turnoAluno", "reconfirmacoes.tipoEntrada", "reconfirmacoes.idReconfAno", "reconfirmacoes.idReconfEscola", "reconfirmacoes.estadoReconfirmacao", "reconfirmacoes.idMatCurso"], $condicao, ["escola"], "", [], array("nomeAluno"=>1));

          $alunosNaoReconfirmados = array();
          foreach ($luzinguLuame as $aluno) {
            if(count(listarItensObjecto($aluno, "reconfirmacoes", ["idReconfAno=".$manipulacaoDados->idAnoActual, "idMatCurso=".$aluno["escola"]["idMatCurso"], "idReconfEscola=".$_SESSION['idEscolaLogada'], "estadoReconfirmacao=A"]))<=0){
               $alunosNaoReconfirmados[]=$aluno;
            }
          }
          echo "<script>alunosNaoReconfirmados=".json_encode($alunosNaoReconfirmados)."</script>";

          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["escola.classeActualAluno", "reconfirmacoes.dataReconf", "reconfirmacoes.horaReconf", "nomeAluno", "numeroInterno", "fotoAluno", "idPMatricula", "reconfirmacoes.idPReconf", "escola.idMatAno"], ["escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfAno"=>$manipulacaoDados->idAnoActual, "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.classeReconfirmacao"=>$classe, "reconfirmacoes.idReconfProfessor"=>$_SESSION['idUsuarioLogado'], "reconfirmacoes.estadoReconfirmacao"=>"A", "escola.idMatCurso"=>$idCurso], ["escola", "reconfirmacoes"], "", [], array("nomeAluno"=>1), $manipulacaoDados->matchMaeAlunos($manipulacaoDados->idAnoActual, $idCurso, $classe));

          $array = $manipulacaoDados->anexarTabela2($array, "nomecursos", "escola", "idPNomeCurso", "idMatCurso");
            echo "<script>var alunosReconfirmados=".json_encode($array)."</script>";

          ?>

  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-12 col-md-12">
            <!--Basic Tabs   -->
            <ul class="nav nav-tabs" id="chamadorTabela">
                <li class="active"><a href="#aindaNaoReconfirmados" data-toggle="tab" class="lead text-danger"><i class="fa fa-user-times"></i> Não Reconfirmados</a>
                </li>
                <li ><a href="#jaReconfirmados" data-toggle="tab" class="lead text-success"><i class="fa fa-user-check"></i> Reconfirmados</a>
                </li>
            </ul>

            <div class="tab-content" style="padding-top: 15px;">
                <div class="tab-pane active" id="aindaNaoReconfirmados">
                  <div class="row">
                      <div class="col-lg-3 col-md-3 lead">
                        Classe:
                          <select class="form-control lead" id="luzingu">
                            <?php 
                              if(isset($_SESSION['classesPorCurso'])){
                                echo $_SESSION['classesPorCurso'];
                              }else{
                                $_SESSION['classesPorCurso']= retornarClassesPorCurso($manipulacaoDados, "A", "nao", "nao", "sim");
                              }
                            ?> 
                          </select>
                      </div>

                    <div class="col-md-5 col-lg-5 col-sm-12 col-xs-12"><br>
                        <label class="lead">
                            Total: <span class="numTAlunos lead quantidadeTotal">0</span>
                        </label>
                        <label class="lead">Femininos: <span  class="quantidadeTotal numTMasculinos">0</span></label>
                    </div>
                      <div class="col-lg-4 col-md-4"><br>
                        <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <span class="input-group-addon"><i class="fa fa-search"></i></span>
                          <input type="search" class="form-control lead" tipoEntidade="alunos" placeholder="Pesquisar Aluno..."  id="pesqAluno" list="listaOpcoes">
                  
                        </div>
                      </div>
                  </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped tabela" >
                            <thead class="corPrimary">
                              <tr>
                                  <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                                  <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                                  <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                                  <th class="lead text-center"><strong><i class="fa fa-restroom"></i> Sexo</strong></th>
                                  <th class="lead text-center"><strong><i class="fa fa-hiking"></i> Idade</strong></th>
                                  <th class="lead text-center"></th>
                              </tr>
                            </thead>
                            <tbody id="tabelaNaoReconfirmados">
                        
                            </tbody>
                        </table>
                    </div>

                     <div class="row" id="paraPaginaca" style="margin-top: -30px;">
                        <div class="col-md-12 col-lg-12 coluna">
                          <div class="form-group paginacao" id="paginacao1">
                                
                          </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="jaReconfirmados">
                    
                    <div class="card">
                       <div class="card-body">
                        <div class="row">
                          <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                             <label class="lead">
                                  Total: <span class="numTotal quantidadeTotal">0</span>
                              </label>
                          </div>
                        </div>
                        <table id="tabela2" class="table table-bordered table-striped">
                            <thead class="corPrimary">
                                <tr>
                                    <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                                    <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                                    <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                                    <th class="lead text-center"><strong><i class="fa fa-graduation-cap"></i> Clsse</strong></th>
                                    <th class="lead text-center"><strong><i class='fa fa-clock'></i> Hora</strong></th>
                                    <th class="lead text-center"><strong><i class="fa fa-file"></i></strong></th>
                                    <th class="lead text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="tabJaReconfirmados">

                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>                
      </div>             
    </div>             
  </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs();$janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>



<div class="modal fade" id="formularioMatricula" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioMatriculaF" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-check"></i> Confirmação de Matricula</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                      <label for="nomeAluno" class="lead">Nome Completo</label>
                      <input type="text" name="nomeAluno" class="form-control fa-border somenteLetras vazio" id="nomeAluno" autocomplete="off" required maxlength="60">

                      <div class="nomeAluno discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 ">
                        <label for="sexoAluno" class="lead">Sexo</label>
                        <select class="form-control lead" id="sexoAluno" name="sexoAluno">
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </div>                      
                  </div>  

                  <div class="row">
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="dataNascAluno" class="lead">Data de Nasc.</label>
                        <input type="date" name="dataNascAluno" class="form-control vazio" id="dataNascAluno" required title="Data de nascimento" max="<?php echo $manipulacaoDados->dataSistema; ?>" >
                        <div class="dataNascAluno discasPrenchimento"></div>
                      </div><div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <label for="pais" class="lead">País</label>
                          <select id="pais" name="pais" class="form-control lead" required>
                            <?php 
                              foreach($manipulacaoDados->selectArray("div_terit_paises", [], [], [], "", [], ["nomePais"=>1]) as $a){
                                echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                              }
                             ?>
                          </select>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                          <label for="provincia" class="lead">Província</label>
                          <select id="provincia" name="provincia" class="form-control lead"></select>
                      </div>
                  </div>


                  <div class="row">
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="municipio" class="lead">Municipio</label>
                          <select id="municipio" name="municipio" class="form-control municipio lead" required></select>                         
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="comuna" class="lead">Comuna</label>
                          <select id="comuna" name="comuna" class="form-control comuna" required></select>                         
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="tipoDocumento" class="lead labelBI">Documento de Identif.</label>
                        <select type="text" name="tipoDocumento" class="form-control vazio" id="tipoDocumento" autocomplete="off" maxlength="15" >
                          <option value="BI">Bilhete de Indentidade</option>
                          <option>Cédula</option>
                          <option>Passaporte</option>
                        </select>
                      </div>                 
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label for="numBI" class="lead labelBI">N.º de  Identificação</label>
                      <input type="text" name="numBI" class="form-control vazio" id="numBI" autocomplete="off" maxlength="15" >
                      <div class="numBI discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label for="localEmissao" class="lead labelBI">Local de Emissão</label>
                      <input type="text" name="localEmissao" class="form-control vazio" id="localEmissao" autocomplete="off" maxlength="15" >
                      <div class="numBI discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                      <label for="dataEmissaoBI" class="lead">Emitido aos</label>
                          <input type="date" name="dataEmissaoBI" class="form-control data" id="dataEmissaoBI" title="Data de emissão" max="<?php echo $manipulacaoDados->dataSistema; ?>">
                          <div class="dataEmissaoBI discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="dataCaducidadeBI" class="lead">Caduca aos</label>
                          <input type="date" name="dataCaducidadeBI" class="form-control data" min="<?php echo $manipulacaoDados->dataSistema; ?>" id="dataCaducidadeBI" title="Data de emissão">
                          <div class="dataCaducidadeBI discasPrenchimento"></div>
                      </div>
                  </div>

                  <div class="row">
                      
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="nomePai" class="lead">Nome do Pai</label>
                           <input type="text" name="nomePai" class="form-control vazio somenteLetras" id="nomePai" title="Nome do Pai" maxlength="60" >
                           <div class="nomePai discasPrenchimento" autocomplete="off"></div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="nomeMae" class="lead">Nome da Mãe</label>
                           <input type="text" name="nomeMae" class="form-control vazio somenteLetras" id="nomeMae" title="Nome da mãe" maxlength="60" >
                           <div class="nomeMae discasPrenchimento" autocomplete="off" style="margin-top: -15px;"></div>
                      </div>

                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="nomeEncarregado" class="lead">Encarregado(a)</label>
                           <input type="text" name="nomeEncarregado" class="form-control vazio" id="nomeEncarregado" title="Número de telefone" autocomplete="off" maxlength="60">

                           <div class="nomeEncarregado discasPrenchimento"></div>
                      </div>
                  </div>

                  <div class="row">

                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="numTelefone" class="lead">Telefone</label>
                           <input type="text" name="numTelefone" class="form-control numeroDeTelefone vazio" id="numTelefone" title="Número de telefone" autocomplete="off" maxlength="12" >
                           <div class="numTelefone discasPrenchimento"></div>
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <label for="nomeEncarregado" class="lead">E-mail</label>
                           <input type="email" name="emailAluno" class="form-control vazio" id="emailAluno" title="E-mail do Aluno" autocomplete="off" maxlength="60">
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="numTelefone" class="lead">Período</label>
                        <select class="form-control lead" name="periodoAluno" id="periodoAluno" required="">
                           <?php 
                            if(trim(valorArray($manipulacaoDados->sobreEscolaLogada, "periodosEscolas"))=="regPos"){
                              echo "<option value='reg'>Regular</option>
                              <option value='pos'>Pós-Laboral</option>";
                            }else{
                               echo "<option value='reg'>Regular</option>";
                            }

                           ?> 
                         </select>
                      </div>
                    </div>

                  
                  <div class="row">   
                    <div class="col-lg-2 col-md-2">
                      <label for="turnoAluno" class="lead">Turno</label>
                      <select class="form-control lead" name="turnoAluno" id="turnoAluno" required="">  
                       </select>
                    </div>

                    <div class="col-lg-5 col-md-5">
                      <label for="idPCursoForm" class="lead">Curso (Opção):</label>
                      <select class="lead form-control" id="idPCursoForm" name="idPCurso">
                        <?php 
                            foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "nomeCurso", "areaFormacaoCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"],["cursos"], "", [], ["nomeCurso"=>1]) as $curso){ 

                            echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                          }
                         ?> 
                      </select> 
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label for="classeAlunoForm" class="lead">Classe</label>
                       <select class="form-control lead" id="classeAlunoForm" name="classeAluno" required="">
                         <?php echo "<optgroup id='listaClasses'></optgrup>"; ?>          
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2">
                      <label for="nomeMae" class="lead">N.º de Proc.</label>
                      <input type="text" name="numeroProcesso" class="form-control vazio" id="numeroProcesso" title="Número de Processo" maxlength="20" placeholder="Automático">
                    </div>
                  </div>

                  <div class="row">
                    
                    <div class="col-lg-4 col-md-4 lead">
                        Anexo
                        <select class="form-control lead" required="" id="idMatAnexo" name="idMatAnexo">
                          <?php 
                            foreach ($manipulacaoDados->selectArray("escolas", ["anexos.idPAnexo", "anexos.identidadeAnexo"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]) as $a) {

                              echo "<option value='".$a["anexos"]["idPAnexo"]."'>".$a["anexos"]["identidadeAnexo"]."</option>";
                            }
                           ?>
                        </select>
                    </div>
                  
                    <div class="lead col-md-4 col-lg-4">
                      <label for="discEspecialidade" class="lead">Disciplina (Opção)</label>
                        <select class="form-control lead" id="discEspecialidade" name="discEspecialidade">
                          <?php
                            echo "<option value=''>Seleccionar</option>";
                            foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)]]) as $disciplina) {
 
                              echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                            }

                           ?>
                        </select>
                    </div>                       
                    <div class="lead col-md-4 col-lg-4">
                      <label for="lingEspecialidade" class="lead">Língua (Opção)</label>
                        <select class="form-control lead" id="lingEspecialidade" name="lingEspecialidade">
                          <?php 
                            echo "<option value=''>Seleccionar</option>";
                            foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(20, 21)]]) as $disciplina) {
 
                              echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                            }
                           ?>
                        </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <label for="numTelefone" class="lead">Acesso ao Sistema</label>
                         <select class="form-control lead" name="acessoConta" id="acessoConta">
                           <option value="A">Autorizado</option>
                           <option value="I">Não Autorizado</option>
                         </select>
                    </div>
                    <div class="lead col-md-4 col-lg-4">
                      <label for="deficiencia" class="lead">Deficiência</label>
                        <select class="form-control lead" id="deficiencia" name="deficiencia">
                          <option value="">Nenhuma Deficiencia</option>
                          <option>Física</option>
                          <option>Mental</option>
                          <option>Visual</option>
                          <option>Auditiva</option>
                        </select>
                    </div>
                    <div class="lead col-md-5 col-lg-5">
                      <label for="tipoDeficiencia" class="lead">Patologia da Deficiência</label>
                        <select class="form-control lead" id="tipoDeficiencia" name="tipoDeficiencia">
                        </select>
                    </div>
                  </div>
                  <div class="row">                    
                    <div class="lead col-md-4 col-lg-4">
                      <label for="tipoDeficiencia" class="lead">Foto</label>
                        <input type="file" name="fotoAluno" value="" accept='.jpg, .png, .jpeg' class="form-control fa-border vazio" id="fotoAluno">
                        </select>
                    </div>
                  </div>
                  <input type="hidden" name="idPMatricula" id="idPMatricula">
                  <input type="hidden" name="classe" id="classe" value="<?php echo $classe ?>">
                  <input type="hidden" name="idPCursoVerificacao" id="idPCursoVerificacao" value="<?php echo $idCurso ?>">
                  <input type="hidden" name="action" id="action">
                  </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-7 col-xs-7 text-left">
                      <button type="submit" class="btn btn-success btn lead submitter" id="Cadastar"><i class="fa fa-user-check"></i> Confirmar </button>
                    </div>                   
                  </div>                
              </div>
          </form>

      </div>
    </div>