<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Efectuar Pagamentos", "efectuarOutrosPagamentos");
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
         

        #formularioPagamento label{
          font-weight: normal !important;
        }

        #relatorioPagamento #messages div{
          margin-bottom: 20px;
          border-bottom: solid rgba(0, 0, 0, 0.3) 0.5px;
        }
        .divValor{
          text-align: center;
          font-weight: 600;
          font-size: 14pt;
        }
        .divBorder>div{
          border-bottom:solid rgba(0, 0, 0, 0.3) 1px;
          padding-bottom: 10px;
        }
      </style>

</head>

<body>
  <?php
    $janelaMensagens->processar(); 
    $layouts->cabecalho();
    $layouts->aside();
    $classe=1;
    $idCurso=""; 
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row">
              <div class="col-lg-12 col-md-12">
                <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                    <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                          <b class="caret"></b>
                                      </a>
                    <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-check-square"></i> Efectuar Pagamentos</strong></h1>
                </nav>
              </div>
          </div>
          <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ['efectuarOutrosPagamentos'], array(), "msg")){
          
          echo "<script>var idAnoActual='".$manipulacaoDados->idAnoActual."'</script>";

          $idPTipoEmolumento = isset($_GET["idPTipoEmolumento"])?$_GET['idPTipoEmolumento']:$manipulacaoDados->selectUmElemento("tipos_emolumentos", "idPTipoEmolumento", ["idPTipoEmolumento"=>array('$ne'=>1), "codigo"=>array('$nin'=>["matricula", "inscricao"])], [], [], ["designacaoEmolumento"=>1]);
          echo "<script>var idPTipoEmolumento='".$idPTipoEmolumento."'</script>";
          echo "<script>var mesesAnoLectivo='".json_encode($manipulacaoDados->mesesAnoLectivo)."'</script>";

          
          $arrayTipoEmolumento = $manipulacaoDados->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>$idPTipoEmolumento, "codigo"=>array('$nin'=>["matricula", "inscricao"])]);

          echo "<script>var tipoPagamento='".valorArray($arrayTipoEmolumento, "tipoPagamento")."'</script>";


          echo "<script>var tabelaprecos =".json_encode(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "emolumentos", ["idTipoEmolumento=".$idPTipoEmolumento]))."</script>";
          ?>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-4 col-md-4 lead">
            <label>Pagamento</label>
            <select class="form-control" id="idPTipoEmolumento">
              <?php 
              foreach($manipulacaoDados->selectArray("tipos_emolumentos", [], ["idPTipoEmolumento"=>array('$ne'=>1), "codigo"=>array('$nin'=>["matricula", "inscricao"])], [], "", [], ["designacaoEmolumento"=>1]) as $a){
                echo "<option value='".$a["idPTipoEmolumento"]."'>".$a["designacaoEmolumento"]."</option>";
              }
            ?>
            </select>
          </div>
          <div class="col-lg-4 col-md-4 lead visible-md visible-lg">
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="pesqUsario"><br>
            <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                <input type="search" class="form-control lead" placeholder="Pesquisar Aluno..." id="btnPesquisarAluno">
                
            </div>   
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover" id="tabContas">
              <thead class="corPrimary">
                  <tr>
                      <th class="lead"><strong><i class='fa fa-sort-alpha-down'></i> Nome do Aluno</strong></th>
                       <th class="lead text-center"><strong>Número Interno</strong></th>
                       <th class="lead text-center"><strong>Curso</strong></th>
                       <th class="lead text-center"><strong>Classe</strong></th>
                      <th class="lead text-center"></th>
                      <th class="lead text-center"></th>
                  </tr>
              </thead>
              <tbody id="tabDados">
                  
              </tbody>
          </table><br>
        </div>

      </div>
    </div>


          

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>



      <div class="modal fade" id="formularioPagamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formPagamento" method="POST">
          <div class="modal-content">
              <div class="modal-body" style="padding-left: 40px; padding-right: 40px;">
                <h3 class="text-center text-danger"><strong><?php echo valorArray($arrayTipoEmolumento, "designacaoEmolumento") ?></strong></h3><br/>
                <div class="row">
                  <div style="font-size:20pt; border:solid rgba(0, 0, 0, 0.3) 1px; padding:3px;" class="text-center col-lg-12">
                    <img src="" id="fotoAluno" style="height:90px; width: 90px; border-radius: 50%;">&nbsp;&nbsp;&nbsp;<strong class="text-success" id="nomeAluno"></strong>                      
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-8 col-md-8 lead">
                    Nome do Cliente
                    <input type="text" class="form-control" id="nomeCliente" name="nomeCliente">
                  </div>
                  <div class="col-lg-4 col-md-4 lead">
                    NIF
                    <input type="text" class="form-control" id="nifCliente" name="nifCliente">
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 lead">
                    Designação
                    <select class="form-control" id="referenciaPagamento" name="referenciaPagamento">
                      <?php 
                      foreach ($referencias as $ref){
                        echo "<option valor='".$ref["valor"]."' value='".$ref["referencia"]."'>".$ref["refPagamento"]."</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-3 lead">
                    Valor
                    <input class="form-control lead text-center" type="number" min="5" required step="5" id="valorPago" name="valorPago">
                  </div>
                  <div class="col-lg-5 col-md-5 lead">
                    Conta Usada
                    <select class="form-control" required id="contaUsar" name="contaUsar">
                      <?php 
                        foreach($manipulacaoDados->selectArray("contas_bancarias", ["idPContaFinanceira", "descricaoConta"], ["idContaEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                          echo "<option value='".$a["idPContaFinanceira"]."'>".$a["descricaoConta"]."</option>";
                        }
                       ?>
                    </select>
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
                </div>
                <div class="row">
                  <div class="col-lg-4 col-md-4 lead">
                    <label>Data do Pag.</label>
                    <input type="date" value="<?php  echo $manipulacaoDados->dataSistema; ?>" required class="form-control" id="dataPagamento" name="dataPagamento">
                  </div>
                </div>


                <input type="hidden" name="idPMatricula" id="idPMatricula">
                <input type="hidden" name="grupo" id="grupo">

                <input type="hidden" name="classe" id="classe" value="<?php echo $classe ?>">
                <input type="hidden" name="periodo" id="periodo" value="<?php echo $periodo ?>">
                <input type="hidden" name="idPCurso" id="idPCurso" value="<?php echo $idCurso; ?>">
                <input type="hidden" id="action" name="action" value="efectuarPagamento">

                <input type="hidden" id="idPTipoEmolumento" name="idPTipoEmolumento" value="<?php echo $idPTipoEmolumento; ?>">
              </div>

              <div class="modal-footer">
                  <div class="row">
                    <div class="col-lg-12 col-md-12 text-right">
                      <button type="submit" id="Cadastrar" class="btn btn-success lead btn-lg"><i class="fa fa-check"></i> Concluir</button>
                    </div>                    
                  </div>                
              </div>
            </div>
          </form>
      </div>