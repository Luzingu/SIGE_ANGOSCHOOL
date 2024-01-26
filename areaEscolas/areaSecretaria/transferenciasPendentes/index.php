<?php session_start(); 
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Transferências Pendentes", "transferenciasPendentes");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
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

                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-mail-reply-all"></i> Recepção de Transferências</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", ["transferenciasPendentes"], array(), "msg")){          
          
           echo "<script>var criterioEscolhaTurno='".valorArray($manipulacaoDados->sobreUsuarioLogado, "criterioEscolhaTurno")."'</script>";

           $luzinguLuame = $manipulacaoDados->selectArray("alunosmatriculados", ["idPMatricula", "nomeAluno", "numeroInterno", "transferencia.dataTransferencia", "transferencia.idPTransferencia", "transferencia.turmaTransferencia", "transferencia.idTransfEscolaOrigem"], ["transferencia.idTransfEscolaDestino"=>$_SESSION['idEscolaLogada'], "transferencia.idTransfAno"=>$manipulacaoDados->idAnoActual, "transferencia.estadoTransferencia"=>"Y"], ["transferencia"],"", [], ["nomeAluno"=>1]);
          $luzinguLuame = $manipulacaoDados->anexarTabela2($luzinguLuame, "escolas", "transferencia", "idPEscola", "idTransfEscolaOrigem");

          echo "<script>var alunosTransferidos=".json_encode($luzinguLuame)."</script>";
          ?>
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                 <label class="lead">
                      Total: <span class="numTAlunos quantidadeTotal">0</span>
                  </label>
              </div>
            </div>

                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                        <tr>
                            <th class="lead text-center"><i class='fa fa-sort-numeric-down'></i></th>
                            <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome Completo</strong></th>
                            <th class="lead text-center"><strong><i class="fa fa-id-card"></i> Número Interno</strong></th>
                            <th class="lead"><strong><i class="fa fa-school"></i> Origem</strong></th>
                                                 
                            <th class="lead text-center"><strong><i class="fa fa-calendar"></i> Data</strong></th>
                            <th class="lead text-center"></th>
                            <th class="lead text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="tabTransferencia">

                    </tbody>
                </table>
          </div>
        </div>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>



<div class="modal fade" id="detalhesTransferencia" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <form class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-info-circle"></i> Detalhes da Transferência</h4>
          </div>

          <div class="modal-body">
            <div class="row">

              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="table-responsive">
                
                <table class="table table-hover" id="tabContas">
                    <tr>
                        <td class="lead text-right">Nome do Aluno:</td>
                        <td class="lead" id="nomeAluno"></td>
                    </tr>
                    <tr>
                        <td class="lead text-right">Número Interno:</td>
                        <td class="lead" id="numeroInterno"></td>
                    </tr>

                    <tr>
                        <td class="lead text-right">Turma:</td>
                        <td class="lead" id="turmaAluno"></td>
                    </tr>
                    <tr>
                        <td class="lead text-right">Escola:</td>
                        <td class="lead" id="escolaOrigem"></td>
                    </tr>
                    <tr>
                        <td class="lead text-right">Província:</td>
                        <td class="lead" id="provinciaOrigem"></td>
                    </tr>
                    <tr>
                        <td class="lead text-right">Municipio:</td>
                        <td class="lead" id="municipioOrigem"></td>
                    </tr>

                    <tr>
                        <td class="lead text-right">Data:</td>
                        <td class="lead" id="dataTransferencia"></td>
                    </tr>
                    <tr>
                        <td class="lead text-right">Funcionário:</td>
                        <td class="lead" id="funcionarioTransferencia"></td>
                    </tr>
                </table>
              
              </div>
            </div>
            </div>
          </div>              
      </div>
    </form>
</div>

<div class="modal fade" id="formulario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioForm">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-mail-reply-all"></i> Receber Transferência</h4>
          </div>

          <div class="modal-body">
                <div class="row">                        
                    <div class="col-lg-12 col-md-12 lead">
                      <label>Nome do Aluno</label>
                      <input type="text" style="color:black; font-weight: bolder; font-size:13pt;" name="nomeAluno" readonly id="nomeAluno" class="form-control">
                    </div>                      
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Anexo</label>
                      <select class="form-control lead" required="" id="idMatAnexo" name="idMatAnexo">
                        <?php 
                          foreach ($manipulacaoDados->selectArray("escolas", [], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["anexos"]) as $a) {

                            echo "<option value='".$a["anexos"]["idPAnexo"]."'>".$a["anexos"]["identidadeAnexo"]."</option>";
                          }
                         ?> 
                      </select>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Período</label>
                      <select class="form-control lead" name="periodoAluno" id="periodoAluno" required="">
                         <?php 
                          if(trim(valorArray($manipulacaoDados->sobreUsuarioLogado, "periodosEscolas"))=="regPos"){
                            echo "<option value='reg'>Regular</option>
                            <option value='pos'>Pós-Laboral</option>";
                          }else{
                             echo "<option value='reg'>Regular</option>";
                          }

                         ?> 
                       </select>
                    </div>
                    <div class="col-lg-4 col-md-4 lead">
                      <label>Turno</label>
                      <select class="form-control lead" name="turnoAluno" id="turnoAluno" required="">
                         
                       </select>
                    </div>
                </div>
                <div class="row">

                  <div class="col-lg-3 col-md-3 lead">
                    <label>N.º de Processo</label>
                    <input type="text"  name="numeroProcesso" id="numeroProcesso" class="form-control text-center">
                  </div>
                  <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 lead">
                    <label>Disciplina (Opção)</label>
                    <select class="form-control lead" id="discEspecialidade" name="discEspecialidade">
                      <?php echo "<option value=''>Seleccionar</option>";
                        foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)]]) as $disciplina) {
 
                          echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                        } 
                      ?>
                    </select>
                  </div> 
                  <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 lead">
                    <label>Língua (Opção)</label>
                    <select class="form-control lead" id="lingEspecialidade" name="lingEspecialidade">
                        <?php 
                          foreach ($manipulacaoDados->selectArray("nomedisciplinas", ["idPNomeDisciplina", "nomeDisciplina"], ["idPNomeDisciplina"=>['$in'=>array(20, 21)]]) as $disciplina) {
                            echo "<option value='".$disciplina["idPNomeDisciplina"]."'>".$disciplina["nomeDisciplina"]."</option>";
                          }
                         ?>
                      </select>
                  </div> 
                </div>                   
          </div>
          <input type="hidden" name="idPTransferencia" id="idPTransferencia">
          <input type="hidden" name="idPMatricula" id="idPMatricula">
          <input type="hidden"  name="action" id="action">

          <div class="modal-footer">
              <div class="row">
                <div class="col-lg-12 col-md-12 text-left">
                  <button type="submit" class="btn btn-primary lead btn-lg" id="Cadastar"><i class="fa fa-check"></i> Concluir</button>
                </div>                    
              </div>                
          </div>
        </div>
      </form>
  </div>