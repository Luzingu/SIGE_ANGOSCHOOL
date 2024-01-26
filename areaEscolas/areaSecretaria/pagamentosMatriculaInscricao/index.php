  <?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pagamentos", "pagamentosMatriculaInscricao");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?> 

<!DOCTYPE html>
<html lang="pt">
<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
        
        #detalhePagamento form table tr td{
          padding: 3px;
          font-size: 14pt;

        }
        #detalhePagamento form table tr td:nth-child(2){
          font-weight: bolder;
          padding-left: 10px;
        }
        #detalhePagamento form table tr td:nth-child(1){
          padding-right: 10px;
          text-align: right;
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-check-double"></i> Pagamento de Matrícula/Inscrição</strong></h1>
              </nav>
            </div>
        </div> 
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "pagamentosMatriculaInscricao", array(), "msg")){

          $mesPagamento = isset($_GET["mesPagamento"])?$_GET["mesPagamento"]:explode("-", $manipulacaoDados->dataSistema)[1];
          echo "<script>var mesPagamento='".$mesPagamento."'</script>";

          $anoCivil = isset($_GET["anoCivil"])?$_GET["anoCivil"]:explode("-", $manipulacaoDados->dataSistema)[0];
          echo "<script>var anoCivil='".$anoCivil."'</script>";

          echo "<script>var precoEmolumentos = ".$manipulacaoDados->selectJson("escolas",["emolumentos.codigoEmolumento", "emolumentos.classe", "emolumentos.idCurso", "emolumentos.mes", "emolumentos.valor"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "emolumentos.codigoEmolumento"=>array('$in'=>["matricula", "inscricao"])], ["emolumentos"])."</script>";



          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["nomeAluno", "numeroInterno", "idPMatricula", "grupo", "fotoAluno", "pagamentos.idHistoricoEscola", "pagamentos.dataPagamento", "pagamentos.horaPagamento", "pagamentos.nomeFuncionario", "pagamentos.idTipoEmolumento", "pagamentos.designacaoEmolumento", "pagamentos.referenciaPagamento", "pagamentos.idPHistoricoConta", "pagamentos.precoPago", "pagamentos.estadoPagamento"], ["pagamentos.idHistoricoEscola"=>$_SESSION['idEscolaLogada'], "pagamentos.idHistoricoAno"=>$manipulacaoDados->idAnoActual, "pagamentos.dataPagamento"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-"), "pagamentos.idTipoEmolumento"=>array('$ne'=>1)], ["pagamentos"], "", [], ["pagamentos.dataPagamento"=>1, "pagamentos.horaPagamento"=>1]);

            echo "<script>var listaPagamentos=".$manipulacaoDados->selectJson("pagamentos_matricula_inscricao", [], ["idPagEscola"=>$_SESSION['idEscolaLogada'], "dataPagamento"=>new \MongoDB\BSON\Regex($anoCivil."-".completarNumero($mesPagamento)."-")], [], "", [], ["idPPagamento"=>-1])."</script>";
          ?>

      <div class="card">
        <div class="card-body">
            <div class="row">
              <div class="col-lg-2 col-md-2 lead">
                Ano:
              <select class="form-control lead" id="anosLectivos">
                <?php 
                for($i=explode("-", $manipulacaoDados->dataSistema)[0]; $i>=2023; $i--){
                  echo "<option>".$i."</option>";
                } 
                ?>
              </select>
            </div>
              <div class="col-md-3 col-lg-3 lead">
                Mês
                <select class="form-control lead" id="mesPagamento">
                  <?php 
                  foreach($manipulacaoDados->mesesAnoLectivo as $m){
                    echo "<option value='".completarNumero($m)."'>".nomeMes($m)."</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-7 col-lg-7"><br/>
                <button class="btn btn-success" id="novoPagamento"><i class="fa fa-check-circle"></i> Novo Pagamento</button>&nbsp;&nbsp;&nbsp;&nbsp;
               <label class="lead">Total (Kz): <span id="totValores" class="quantidadeTotal">0</span></label>
              </div>
            </div>           
            <table id="example1" class="table table-bordered table-striped">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead font-weight-bolder"><strong>N.º</strong></th>
                      <th class="lead font-weight-bolder"><strong>Data</strong></th>
                      <th class="lead font-weight-bolder"><strong>Nome do Aluno</strong></th>
                      <th class="lead"><strong>Referencia</strong></th>
                      <th class="lead text-center"><strong>Conta</strong></th>
                      <th class="lead "><strong>RPM</strong></th>  
                      <th class="lead text-center"><strong>Valor</strong></th>
                      <th class="lead text-center"></th>
                       <th class="lead text-center"></th>
                       <th class="lead text-center"></th>
                  </tr>
              </thead>
              <tbody id="tabDados">
                  
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


 <div class="modal fade" id="formularioMatriculaInscricao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog"   id="formularioMatriculaInscricaoForm">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-check-double"></i> Pagamento de Matrícula/Inscrição</h4>
        </div>

        <div class="modal-body">
            <h1 class="text-center text-primary paraCamposGerais"><strong><?php echo $manipulacaoDados->numAnoActual; ?></strong></h1>

            <div class="row paraCamposGerais">
              <div class="col-lg-8 col-md-8 lead">
                Nome do Cliente
                <input type="text" autocomplete="off" class="form-control" id="nomeCliente" name="nomeCliente">
              </div>
              <div class="col-lg-4 col-md-4 lead">
                NIF
                <input type="text" autocomplete="off" class="form-control" id="nifCliente" name="nifCliente">
              </div>
            </div>
            <div class="row paraCamposGerais">
              <div class="col-lg-4 col-md-4 lead">
                Classe:
                <select class="form-control" required id="luzingu" name="luzingu">
                  <?php 
                  if(isset($_SESSION['classesPorCurso'])){
                    echo $_SESSION['classesPorCurso'];
                  }else{
                    $_SESSION['classesPorCurso']=retornarClassesPorCurso($manipulacaoDados, "", "nao");
                  }
                  ?>         
                </select>
              </div>
              <div class="col-lg-3 col-md-3 lead">
                Referência
                <select class="form-control" required id="referenciaPagamento" name="referenciaPagamento">
                  <option value="matricula">Matricula</option>
                  <option value="inscricao">Inscrição</option>
                </select>
              </div>
              <div class="col-lg-5 col-md-5 lead">
                Conta
                <select class="form-control" id="contaUsar" required name="contaUsar">
                  <?php 
                  foreach($manipulacaoDados->selectArray("contas_bancarias", ["idPContaFinanceira", "descricaoConta"], ["idContaEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                    echo "<option value='".$a["idPContaFinanceira"]."'>".$a["descricaoConta"]."</option>";
                  }

                   ?>
                </select>
              </div>
            </div>
            <div class="row paraCamposGerais">
              <div class="col-lg-4 col-md-4 lead">
                Valor (KZ)
                <input type="text" required readonly style="font-weight: bolder; font-size: 16pt;" required class="form-control text-center text-primary" id="valorPago" name="valorPago">
              </div>
              <div class="col-lg-4 col-md-4 lead">
                Meio de Pagamts
                <select class="form-control" id="meioPagamento" name="meioPagamento">
                  <option value="NU">Numerário</option>
                  <option value="TB">Transferência Bancária</option>
                  <option value="MB">Referências de Pagamentos para Multicaixa</option>
                  <option value="CC">Cartão de Crédito</option>
                  <option value="CD">Cartão de débito</option>
                  <option value="OU">Outros meios não assinalados</option>
                </select>
              </div>
              <div class="col-lg-4 col-md-4 lead">
                Data do Pag.
                <input type="date" value="<?php  echo $manipulacaoDados->dataSistema; ?>" required class="form-control" id="dataPagamento" name="dataPagamento">
              </div>
            </div>
            <div class="row paraCampoMotivo">
              <div class="col-lg-12 col-md-12 lead">
                Motivo
                <textarea class="form-control" style="font-size:18px;" id="motivoCancelamento" name="motivoCancelamento" required></textarea>
              </div>
            </div>

            <input type="hidden" name="anoCivil" id="anoCivil" value="<?php echo $anoCivil; ?>">
            <input type="hidden" name="mesPagamento" id="mesPagamento" value="<?php echo $mesPagamento; ?>">
            <input type="hidden" name="idPPagamento" id="idPPagamento">
            <input type="hidden" name="action" id="action">

        </div>
        <div class="modal-footer">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 text-left">
                <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Efectuar</button>

              </div>                    
            </div>                
        </div>
      </div>
    </form>
</div>