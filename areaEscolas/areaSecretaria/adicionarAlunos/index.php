<?php session_start();
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Adicionar Alunos", "adicionarAlunos");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->retornarAnosEmJavascript(); 
    $manipulacaoDados->listaClassesPorCurso();
 ?>

 <!DOCTYPE html> 
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    .fotoAluno{
      width: 85px; 
      height: 85px;
      border-radius: 10px;
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-plus"></i> Adicionar Alunos</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso("", ["adicionarAlunos"], array(), "msg")){  


          echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreEscolaLogada, "criterioEscolhaTurno")."'</script>";

          echo "<script>var listaEscolas=".$manipulacaoDados->selectJson("escolas", ["idPEscola", "abrevNomeEscola2"])."</script/>";

          echo "<script>var listaNomeCursos=".$manipulacaoDados->selectJson("nomecursos", ["idPNomeCurso", "abrevCurso"])."</script/>";
          ?>

          <div class="row">
            <div class="col-lg-12 col-md-12">
              <input type="search" class="form-control" placeholder="Pesquisar aluno..." id="pesqAluno" name="pesqAluno">
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12">

              <div class="table-responsive">
                  <table class="table table-bordered table-hover table-striped tabela" >
                      <thead class="corPrimary">
                        <tr>
                            
                            <th class="lead"><strong>Foto</strong></th>
                            <th class="lead"><strong> Nome Completo</strong></th>
                            <th class="lead text-center"><strong> Número Interno</strong></th>
                            <th class="lead text-center"><strong>BI</strong></th>
                            <th class="lead text-center"><strong>Sexo</strong></th>
                            <th class="lead text-center"><strong>Escolas</strong></th>
                            <th class="lead text-center"></th>
                        </tr>
                      </thead>
                      <tbody id="dadoTabela">
                  
                      </tbody>
                  </table>
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
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-check"></i> Adicionar Aluno na Instituição</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-10 col-md-10 col-lg-offset-2 col-md-offset-2 lead mensagemErroFormulario"></div>
                  </div>

                  <div class="row">
                    <div class="lead col-md-2 col-lg-2">
                      <label>RPM</label>
                      <input type="text" autocomplete="off" name="rpm" class="form-control vazio" id="rpm" title="Referência de Pagamento de Matricula" maxlength="6">
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
                      <label for="nomeAluno" class="lead">Nome Completo</label>
                      <input type="text" name="nomeAluno" class="form-control fa-border somenteLetras vazio" id="nomeAluno" readonly autocomplete="off" required maxlength="60">

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
                          <select id="provincia" name="provincia" class="form-control lead nomeProvinciaBI">
                          </select>
                      </div>
                  </div>


                  <div class="row">
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="municipio" class="lead">Municipio</label>
                          <select id="municipio" name="municipio" class="form-control municipio lead">                            
                          </select> 
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="comuna" class="lead">Comuna</label>
                          <select id="comuna" name="comuna" class="form-control comuna">                            
                          </select>                       
                      </div>

                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="numBI" class="lead labelBI">N.º BI/Cédula</label>
                           <input type="text" name="numBI" class="form-control vazio" id="numBI" title="Número do BI" autocomplete="off" maxlength="15" >
                           <div class="numBI discasPrenchimento"></div>
                      </div>
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="dataEmissaoBI" class="lead">Emitido aos</label>
                            <input type="date" name="dataEmissaoBI" class="form-control data" id="dataEmissaoBI" title="Data de emissão" max="<?php echo $manipulacaoDados->dataSistema; ?>">
                            <div class="dataEmissaoBI discasPrenchimento"></div>
                      </div>                    
                  </div>

                  <div class="row">
                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="dataCaducidadeBI" class="lead">Caduca aos</label>
                            <input type="date" name="dataCaducidadeBI" class="form-control data" id="dataCaducidadeBI" title="Data de emissão" min="<?php echo $manipulacaoDados->dataSistema; ?>">
                            <div class="dataCaducidadeBI discasPrenchimento"></div>
                      </div>
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <label for="nomePai" class="lead">Nome do Pai</label>
                           <input type="text" name="nomePai" class="form-control vazio somenteLetras" id="nomePai" title="Nome do Pai" maxlength="60" >
                           <div class="nomePai discasPrenchimento" autocomplete="off"></div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="nomeMae" class="lead">Nome da Mãe</label>
                           <input type="text" name="nomeMae" class="form-control vazio somenteLetras" id="nomeMae" title="Nome da mãe" maxlength="60" >
                           <div class="nomeMae discasPrenchimento" autocomplete="off" style="margin-top: -15px;"></div>
                      </div>
                  </div>

                  <div class="row">
                      <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                        <label for="nomeEncarregado" class="lead">Nome do(a) Encarregado(a)</label>
                           <input type="text" name="nomeEncarregado" class="form-control vazio" id="nomeEncarregado" title="Número de telefone" autocomplete="off" maxlength="60">

                           <div class="nomeEncarregado discasPrenchimento"></div>
                      </div>

                      <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                        <label for="numTelefone" class="lead">Telefone</label>
                           <input type="text" name="numTelefone" class="form-control numeroDeTelefone vazio" id="numTelefone" title="Número de telefone" autocomplete="off" maxlength="12" >
                           <div class="numTelefone discasPrenchimento"></div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <label for="nomeEncarregado" class="lead">E-mail</label>
                           <input type="email" name="emailAluno" class="form-control vazio" id="emailAluno" title="E-mail do Aluno" autocomplete="off" maxlength="60">
                      </div>
                    </div>

                  
                  <div class="row">                    
                    <div class="col-lg-2 col-md-2">
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
                          <?php    
                            echo "<optgroup id='listaClasses'></optgrup>";
                            echo "<optgroup label='Finalista'>";
                            foreach($manipulacaoDados->selectArray("anolectivo", [], ["idPAno"=>array('$ne'=>(int)$manipulacaoDados->idAnoActual)], [], "", [], ["numAno"=>-1]) as $ano){ 
                              echo "<option value='F_".$ano["idPAno"]."'>FIN_".$ano["numAno"]."</option>";
                            }
                            echo "</optgrup>";
                          ?>            
                        </select>
                    </div>
                    
                  </div>

                  <div class="row">
                    <div class="col-lg-2 col-md-2">
                      <label for="nomeMae" class="lead">N.º de Proc.</label>
                         <input type="text" name="numeroProcesso" class="form-control vazio" id="numeroProcesso" title="Número de Processo" maxlength="20" >
                    </div>
                    <div class="col-lg-3 col-md-3 lead">
                        Anexo
                        <select class="form-control lead" required="" id="idMatAnexo" name="idMatAnexo">
                          <?php 
                            foreach ($manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]) as $a) {

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
                            foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)] ]) as $disciplina) {
                              echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                            }
                           ?>
                        </select>
                    </div>                       
                    <div class="lead col-md-3 col-lg-3">
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
                    <div class="lead col-md-3 col-lg-3">
                      <label for="tipoDeficiencia" class="lead">Tipo de Entrada</label>
                        <select id="tipoEntrada" name="tipoEntrada" class="form-control lead" required="">
                            <option value="novaMatricula">Nova Matricula</option>
                            <option value="porTransferencia">Por Transferência</option>
                        </select>
                    </div>
                  </div>
                  <input type="hidden" name="idPMatricula" id="idPMatricula">
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