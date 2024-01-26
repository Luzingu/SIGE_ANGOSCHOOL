<?php 
    if(session_status()!==PHP_SESSION_ACTIVE){
      session_cache_expire(60);
      session_start();
    }
  include_once ($_SERVER['DOCUMENT_ROOT'].'/angoschool/funcoesAuxiliares.php');
  curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/'.directorioEmExecucao().'/funcoesAuxiliares.php');
  curtina($_SERVER['DOCUMENT_ROOT'].'/angoschool/htmlsMae.php');

class includarHtmls extends includarHtmlsMae{
   function __construct(){
        parent::__construct();
        
    }
    function formConfirmarSenhaAdministrador (){?>
      <div class="modal fade" id="confirmarSenhaAdministrador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
        <form class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel">Adicionar novo Ano Lectivo</h4>
              </div>

              <div class="modal-body">                  
                  <div class="row">
                      <div class="col-lg-12 col-md-12">

                        <input type="password" name="" class="form-control fa-border caixaSenha somenteLetras vazio" id="txtConfirmarSenhar" placeholder="Confirme Aqui a Sua Palavra Passe" required>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-5 col-md-5">
                      <input type="submit" class="btn btn-primary col-lg-12 lead btn-lg" value="Confirmar">
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
    </div>

    <?php } public function formularioInscTrocCurso($areaEmExecucao, $arrayCursos=array()){ ?>

        <div class="modal fade" id="formularioInscTrocCurso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formularioInscTrocCursoForm">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-sign-out-alt"></i> Trocar Curso</h4>
              </div>

              <div class="modal-body">
                <div class="row">
                  <div class="col-lg-12 col-md-12">
                    <h2 class="text-primary" style="margin-top:0px;"><strong id="nomeAluno"></strong></h2>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    <label>Curso:</label>
                    <select class="form-control lead" id="idNovoCurso" name="idNovoCurso" required>
                      <?php foreach($arrayCursos as $a){
                        echo "<option value='".$a["idPNomeCurso"]."'>".$a["nomeCurso"]." (".$a["areaFormacaoCurso"].")</option>";
                      }  ?>
                    </select>
                  </div>
                </div>
              </div>
              <input type="hidden" name="action" id="action" value="trocarCursoAluno">
              <input type="hidden" name="idPAluno" id="idPAluno" value="">
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-4 col-md-4 text-left">
                      <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-sign-out-alt"></i> Concluir</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>
    <?php } function formularioDeCadastro($areaEmExecucao="", $idPCurso=""){ ?>

        <div class="modal fade" id="formularioCadastro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioCadastroF" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-edit"></i> Inscrição</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-lg-12 col-md-12 lead mensagemErroFormulario"></div>
                  </div>
                  <h2 class="text-success" style="text-transform: uppercase;"><strong><?php echo $this->selectUmElemento("nomecursos", "nomeCurso", ["idPNomeCurso"=>$idPCurso]); ?></strong></h2>

                  <div class="row">

                    <div class="lead col-md-2 col-lg-2">
                      <label>RPI</label>
                      <input type="text" autocomplete="off" name="rpm" class="form-control vazio" id="rpm" title="Referência de Pagamento de Inscrição" maxlength="6">
                    </div>

                    <div class="col-lg-5 col-md-5">
                      <label for="nomeAluno" class="lead">Nome Completo</label>
                      <input type="text" name="nomeAluno" class="form-control fa-border somenteLetras vazio" id="nomeAluno" autocomplete="off" required maxlength="60">

                      <div class="nomeAluno discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-2 col-md-2 ">
                        <label for="sexoAluno" class="lead">Sexo</label>
                        <select class="form-control lead" id="sexoAluno" name="sexoAluno">
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label for="dataNascAluno" class="lead">Data de Nasc.</label>
                      <input type="date" max="<?php echo $this->dataSistema; ?>" name="dataNascAluno" class="form-control vazio" id="dataNascAluno" required title="Data de nascimento" placeholder="Data de Nascimento" >
                      <div class="dataNascAluno discasPrenchimento"></div>
                    </div>                      
                  </div>  

                  <div class="row">
                      <div class="col-lg-3 col-md-3">
                        <label for="pais" class="lead">País</label>
                          <select id="pais" required name="pais" class="form-control lead nomePaisBI" required>
                            <?php 
                              foreach($this->selectArray("div_terit_paises", [], [], [],"", [], ["nomePais"=>1]) as $a){
                                echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                              }
                             ?>
                          </select>
                      </div>
                      <div class="col-lg-3 col-md-3">
                          <label for="provincia" class="lead">Província</label>
                          <select id="provincia" required name="provincia" class="form-control"></select>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="municipio" class="lead">Municipio</label>
                          <select id="municipio" required name="municipio" class="form-control municipio">                            
                          </select>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="comuna" class="lead">Comuna</label>
                          <select id="comuna" required name="comuna" class="form-control municipio"></select> 
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-4 col-md-4">
                      <label for="numBI" class="lead">Número do BI</label>
                         <input type="text" name="numBI" class="form-control somenteLetrasNumeros vazio" id="numBI" title="Número do BI" autocomplete="off" maxlength="15" >
                         <div class="numBI discasPrenchimento"></div>
                    </div> 
                    <div class="col-lg-3 col-md-3">
                        <label for="dataEmissaoBI" class="lead">Emitido aos</label>
                            <input type="date" max="<?php echo $this->dataSistema; ?>" name="dataEmissaoBI" class="form-control data" id="dataEmissaoBI" title="Data de emissão" placeholder="Data de Emissão do BI">
                            <div class="dataEmissaoBI discasPrenchimento"></div>
                      </div> 
                      <div class="col-lg-5 col-md-5">
                        <label for="nomePai" class="lead">Nome do Pai</label>
                           <input type="text" name="nomePai" class="form-control vazio somenteLetras" id="nomePai" title="Nome do Pai" maxlength="60" >
                           <div class="nomePai discasPrenchimento" autocomplete="off"></div>
                      </div>                 
                  </div>
                  <div class="row">
                      <div class="col-lg-4 col-md-4">
                        <label for="nomeMae" class="lead">Nome da Mãe</label>
                           <input type="text" name="nomeMae" class="form-control lead vazio somenteLetras" id="nomeMae" title="Nome da mãe" maxlength="60" >
                           <div class="nomeMae discasPrenchimento" autocomplete="off" style="margin-top: -15px;"></div>
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="numTelefone" class="lead">N.º de Telefone</label>
                           <input type="text" name="numTelefone" class="form-control numeroDeTelefone vazio" id="numTelefone" title="Número de telefone" autocomplete="off" maxlength="12" >
                           <div class="numTelefone discasPrenchimento"></div>
                      </div>
                      <div class="col-lg-3 col-md-3 lead">
                        Período
                        <select class="form-control fa-border" id="periodoInscricao" name="periodoInscricao" required="">

                        </select>
                      </div>
                      <div class="col-lg-2 col-md-2 lead">
                        Média
                        <input type="number" step="0.01" class="form-control text-center" required="" name="mediaDiscNuclear" min="0" max="20" id="mediaDiscNuclear">
                      </div>             
                  </div>

                    <input type="hidden" name="idPAluno" idChave="sim">
                    <input type="hidden" name="idPCurso" id="idPCurso" value="<?php echo $idPCurso; ?>">
                    <input type="hidden" name="areaEmExecucao" value="<?php echo $areaEmExecucao; ?>">
                    <input type="hidden" name="action" id="action" value="salvarInscricao">
                  </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button type="submit" class="btn btn-primary btn lead submitter" id="Cadastar"><i class="fa fa-user-edit"></i> Cadastrar </button>
                    </div>                   
                  </div>                
              </div>
          </form>

      </div>
    </div>
   <?php } function formularioDaMatricula($areaEmExecucao=""){ ?>

        <div class="modal fade" id="formularioMatricula" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">

      <div class="modal-dialog" style="margin-top: -15px;" >
          <form class="modal-content" id="formularioMatriculaF" method="">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-user-circle"></i> Matricula</h4>
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
                    
                    <div class="col-lg-7 col-md-7">
                      <label for="nomeAluno" class="lead">Nome Completo</label>
                      <input type="text" name="nomeAluno" class="form-control fa-border somenteLetras vazio" id="nomeAluno" autocomplete="off" required maxlength="60">

                      <div class="nomeAluno discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3 ">
                        <label for="sexoAluno" class="lead">Sexo</label>
                        <select class="form-control lead" id="sexoAluno" name="sexoAluno">
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                        </select>
                    </div>                      
                  </div>  

                  <div class="row">
                      <div class="col-lg-3 col-md-3">
                        <label for="dataNascAluno" class="lead">Data de Nasc.</label>
                        <input type="date" name="dataNascAluno" class="form-control vazio" id="dataNascAluno" required title="Data de nascimento" max="<?php echo $this->dataSistema; ?>" >
                        <div class="dataNascAluno discasPrenchimento"></div>
                      </div>
                      <?php $this->parte2FormularioMatricula($areaEmExecucao); ?>
                      
                <?php } public function parte2FormularioMatricula ($areaEmExecucao){

                 ?>
                      <div class="col-lg-5 col-md-5">
                        <label for="pais" class="lead">País</label>
                          <select id="pais" name="pais" class="form-control lead nomePaisBI" required>
                            <?php 
                              foreach($this->selectArray("div_terit_paises", [], [], [],"", [], ["nomePais"=>1]) as $a){
                                echo "<option value='".$a["idPPais"]."'>".$a["nomePais"]."</option>";
                              }
                             ?>
                          </select>
                      </div>
                      <div class="col-lg-4 col-md-4">
                          <label for="provincia" class="lead">Província</label>
                          <select id="provincia" name="provincia" class="form-control lead" required></select>
                      </div>
                  </div>


                  <div class="row">
                      <div class="col-lg-4 col-md-4">
                        <label for="municipio" class="lead">Municipio</label>
                          <select id="municipio" name="municipio" class="form-control municipio lead" required></select>                         
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label for="comuna" class="lead">Comuna</label>
                          <select id="comuna" name="comuna" class="form-control comuna" required></select>                         
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label for="tipoDocumento" class="lead labelBI">Documento de Identif.</label>
                        <select type="text" name="tipoDocumento" class="form-control vazio" id="tipoDocumento">
                          <option value="BI">Bilhete de Indentidade</option>
                          <option>Cédula</option>
                          <option>Passaporte</option>
                        </select>
                      </div>                  
                  </div>
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                      <label for="numBI" class="lead labelBI">N.º de  Identificação</label>
                      <input type="text" name="numBI" class="form-control vazio" id="numBI" autocomplete="off" maxlength="15" >
                      <div class="numBI discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label for="localEmissao" class="lead labelBI">Local de Emissão</label>
                      <input type="text" name="localEmissao" class="form-control vazio" id="localEmissao" autocomplete="off" maxlength="15" >
                      <div class="numBI discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label for="dataEmissaoBI" class="lead">Emitido aos</label>
                          <input type="date" name="dataEmissaoBI" class="form-control data" id="dataEmissaoBI" title="Data de emissão" max="<?php echo $this->dataSistema; ?>">
                          <div class="dataEmissaoBI discasPrenchimento"></div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <label for="dataCaducidadeBI" class="lead">Caduca aos</label>
                          <input type="date" name="dataCaducidadeBI" class="form-control data" min="<?php echo $this->dataSistema; ?>" id="dataCaducidadeBI" title="Data de emissão">
                          <div class="dataCaducidadeBI discasPrenchimento"></div>
                      </div>
                  </div>

                  <div class="row">
                      
                      <div class="col-lg-4 col-md-4">
                        <label for="nomePai" class="lead">Nome do Pai</label>
                           <input type="text" name="nomePai" class="form-control vazio somenteLetras" id="nomePai" title="Nome do Pai" maxlength="60" >
                           <div class="nomePai discasPrenchimento" autocomplete="off"></div>
                      </div>
                      <div class="col-lg-4 col-md-4">
                        <label for="nomeMae" class="lead">Nome da Mãe</label>
                           <input type="text" name="nomeMae" class="form-control vazio somenteLetras" id="nomeMae" title="Nome da mãe" maxlength="60" >
                           <div class="nomeMae discasPrenchimento" autocomplete="off" style="margin-top: -15px;"></div>
                      </div>

                      <div class="col-lg-4 col-md-4">
                        <label for="nomeEncarregado" class="lead">Encarregado(a)</label>
                           <input type="text" name="nomeEncarregado" class="form-control vazio" id="nomeEncarregado" title="Número de telefone" autocomplete="off" maxlength="60">

                           <div class="nomeEncarregado discasPrenchimento"></div>
                      </div>
                  </div>

                  <div class="row">

                      <div class="col-lg-4 col-md-4">
                        <label for="numTelefone" class="lead">Telefone</label>
                           <input type="text" name="numTelefone" class="form-control numeroDeTelefone vazio" id="numTelefone" title="Número de telefone" autocomplete="off" maxlength="12" >
                           <div class="numTelefone discasPrenchimento"></div>
                      </div>
                      <div class="col-lg-5 col-md-5">
                        <label for="nomeEncarregado" class="lead">E-mail</label>
                           <input type="email" name="emailAluno" class="form-control vazio" id="emailAluno" title="E-mail do Aluno" autocomplete="off" maxlength="60">
                      </div>
                      <div class="col-lg-3 col-md-3">
                        <label for="numTelefone" class="lead">Período</label>
                        <select class="form-control lead" name="periodoAluno" id="periodoAluno" required="">
                           <?php 
                            if(trim(valorArray($this->sobreEscolaLogada, "periodosEscolas"))=="regPos"){
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
                      <select class="lead form-control" id="idPCursoForm" name="idPCurso" required>
                      <?php 
                        foreach($this->selectArray("nomecursos", ["idPNomeCurso", "nomeCurso", "areaFormacaoCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"],["cursos"], "", [], ["nomeCurso"=>1]) as $curso){ 
                          echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["nomeCurso"]." (".$curso["areaFormacaoCurso"].")</option>";
                        }
                       ?> 
                      </select>
                    </div>
                    <div class="col-lg-3 col-md-3">
                      <label for="classeAlunoForm" class="lead">Classe</label>
                       <select class="form-control lead" id="classeAlunoForm" name="classeAluno" required="">
                          <?php 
                          if($areaEmExecucao=="backup"){
                            echo "<optgroup label='Finalista'>";
                            foreach($this->selectArray("anolectivo", [], ["idPAno"=>array('$ne'=>(int)$this->idAnoActual)], [], "", [], ["numAno"=>-1]) as $ano){ 

                              echo "<option value='F_".$ano["idPAno"]."'>FIN_".$ano["numAno"]."</option>";
                            }
                            echo "</optgrup>";
                          }else{
                            echo "<optgroup id='listaClasses'></optgrup>";
                          }
                          ?>            
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
                            foreach ($this->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]) as $a) {

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
                            foreach ($this->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)]]) as $disciplina) {
 
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
                             foreach ($this->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(20, 21)]]) as $disciplina) {
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

                    <div class="lead col-md-3 col-lg-3">
                      <label for="tipoDeficiencia" class="lead">Estado</label>
                        <select id="estadoDeDesistenciaNaEscola" name="estadoDeDesistenciaNaEscola" class="form-control lead" required="">
                            <option value="A">Activo</option>
                            <option value="D">Desistente</option>
                            <option value="N">Mat. Anulada</option>
                            <option value="F">Excluido por Faltas</option>
                        </select>
                    </div>
                  <?php if($areaEmExecucao=="matricula"){ ?>
                    <div class="lead col-md-3 col-lg-3">
                      <label for="tipoDeficiencia" class="lead">Tipo de Entrada</label>
                        <select id="tipoEntrada" name="tipoEntrada" class="form-control lead" required="">
                            <option value="novaMatricula">Nova Matricula</option>
                            <option value="porTransferencia">Por Transferência</option>
                        </select>
                    </div>
                  <?php } ?>
                    <div class="lead col-md-4 col-lg-4">
                      <label for="tipoDeficiencia" class="lead">Foto</label>
                        <input type="file" name="fotoAluno" value="" accept='.jpg, .png, .jpeg' class="form-control fa-border vazio" id="fotoAluno">
                    </div>
                    
                <?php if($_SESSION["idEscolaLogada"]==25){ ?>
                    <div class="lead col-md-2 col-lg-2">
                      <label for="tipoDeficiencia" class="lead">Turma</label>
                        <select id="turmaAluno" name="turmaAluno" class="form-control lead" required="">
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                        </select>
                    </div>
                <?php } ?>
                  </div>
                    <input type="hidden" name="idPMatricula" id="idPMatricula" idChave="sim">
                    <input type="hidden" name="action" id="action" value="salvarMatricula">
                    <input type="hidden" name="areaEmExecucao" id="areaEmExecucao" value="<?php echo $areaEmExecucao; ?>">
                  </div>
              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 text-left">
                      <button type="submit" class="btn btn-success btn lead submitter" id="Cadastar"><i class="fa fa-check"></i> Salvar </button>
                    </div>                   
                  </div>                
              </div>
          </form>

      </div>
    </div>
   <?php }  } ?>