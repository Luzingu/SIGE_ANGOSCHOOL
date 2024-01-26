<?php session_start();
     include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Novo Comunicado", "novoComunicado");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-envelope"></i> Comunicados (<span id="numeroCaracter"></span>)</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso($manipulacaoDados->idPArea, "novoComunicado", array(), "msg")){
          $precoPorMensagem=14;
          echo "<script>var precoPorMensagem=".$precoPorMensagem."</script>";
          echo "<script>var abrevNomeEscola2='".valorArray($manipulacaoDados->sobreEscolaLogada, "abrevNomeEscola2")."'</script>";

         ?>

          <div class="card"> 
            <div class="card-body">
              <form id="formSubmit">
                 <div class="row">
                   <div class="col-lg-2 col-md-2">
                      <label class="lead">Destinatário</label>
                     <select class="form-control" id="destinatario" name="destinatario">
                       <option value="professor">Professores</option>
                       <option value="alunos">Alunos</option>
                     </select>
                   </div>
                   <div class="col-lg-3 col-md-3">
                      <label class="lead">Usuários</label>
                     <select class="form-control" id="luzingu" name="luzingu" disabled>
                      <option value="">Todos</option>
                      <?php 
                        if(isset($_SESSION['classesPorCursoPeriodoFinalista'])){
                          echo $_SESSION['classesPorCursoPeriodoFinalista'];
                        }else{
                          echo retornarClassesPorCurso($manipulacaoDados, "", "sim", "sim", "sim"); 
                        }
                       ?>
                     </select>
                   </div>
                   <div class="col-lg-2 col-md-2">
                      <label class="lead">Informação</label>
                     <select class="form-control" id="tipoInformacao" name="tipoInformacao">
                      <option value="convocatoria">Convocatoria</option>
                      <option value="notificacao">Notificação</option>
                     </select>
                   </div>

                   <div class="col-lg-5 col-md-5 text-right">
                     <h3 id="precoMensagem" class="text-primary" style="font-weight:bolder;"></h3>
                   </div>
                 </div>
                 <input type="hidden" id="dadosEnviar" name="dadosEnviar">
                 <input type="hidden" id="action" name="action" value="enviarMensagens">
                 <input type="hidden" name="precoTotSMS" id="precoTotSMS">
                 <div class="row">
                   <div class="col-lg-10 col-md-10">
                      <label class="lead" id="labelForDonel" style="display:none;"><input type="checkbox" id="labelDonel" checked> Apenas alunos reconfirmados</label>
                     <textarea class="form-control lead" required id="textoMensagem" name="textoMensagem" placeholder="Digite aqui a mensagem..." style="max-width: 100%; max-height: 100px;"></textarea>
                   </div>
                   <div class="col-lg-2 col-md-2 text-right"><br>
                     <button type="submit" class="btn lead btn-success"><i class="fa fa-send"></i> Enviar</button>
                   </div>
                 </div>
              </form>

                <table id="example1" class="table table-striped table-bordered table-hover" >
                  <thead class="corPrimary">
                    <tr>
                      <th class="lead text-center"></th>
                      <th class="lead text-center"><strong>Nº</strong></th>
                      <th class="lead"><strong>Nome</strong></th>
                      <th class="lead text-center"><strong>E-mail</strong></th>
                      <th class="lead text-center"><strong>Telefone</strong></th>
                    </tr>
                  </thead>
                  <tbody id="tabela">
                  </tbody>
                </table><br><br>
            </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>