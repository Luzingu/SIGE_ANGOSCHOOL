<?php session_start(); 
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Pag. de Propinas Personalizado", "efectuarPagamentosPersonalizado77");
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
                    <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-check"></i> Pagamentos Personalizados</strong></h1>
                </nav>
              </div>
          </div>
          <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ['efectuarPagamentosPersonalizado77'], array(), "msg")){ 

          echo "<script>var dataSistema='".$manipulacaoDados->dataSistema."'</script>";

          $mesesParaPagar = array();

          $posicaoMes=0;
          foreach($manipulacaoDados->mesesAnoLectivo as $mes){

           foreach ($manipulacaoDados->selectArray("nomecursos", [], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"], ["cursos"])   as $c) { 
              
              foreach(listarItensObjecto($c, "classes") as $classe){ 

                $valorPreco = valorArray(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "emolumentos", ["classe=".$classe["identificador"], "mes=".$mes, "idTipoEmolumento=1", "idCurso=".$c["idPNomeCurso"]]), "valor");

                if((int)$valorPreco>0){
                  $mesesParaPagar[]=array("mes"=>$mes, "posicao"=>$posicaoMes, "valorPreco"=>$valorPreco, "classe"=>$classe["identificador"], "idPCurso"=>$c["idPNomeCurso"]);
                }
              }
            }
            
            $posicaoMes++;
          }

          echo "<script>var mesesParaPagar=".json_encode($mesesParaPagar)."</script>";
?>
  

  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-8 col-md-8 visible-md visible-lg"></div>
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" id="pesqUsario">
          <div class="form-group input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="search" class="form-control lead" placeholder="Pesquisar Aluno..." id="btnPesquisarAluno">
              
          </div>   
        </div>
      </div>

      <div class="row">
        
        <div class="col-md-5 col-lg-5 col-sm-12 col-xs-12">
           <label class="lead">Total: <span id="totContas" class="quantidadeTotal">0</span></label>
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
                     <th class="lead text-center"><strong>N.º<br/>Meses</strong></th>
                     <th class="lead text-center"></th>
                    <th class="lead text-center"></th>
                </tr>
            </thead>
            <tbody>
                
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
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();  
?>



      <div class="modal fade" id="formularioPagamento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
      <form class="modal-dialog" id="formPagamento" method="POST">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-money-bill"></i> Pagamentos</h4>
              </div>
              <div class="modal-body" style="padding-left: 40px; padding-right: 40px;">
                
                    <div class="row">
                      <div style="font-size:20pt; border:solid rgba(0, 0, 0, 0.3) 1px; padding:3px;" class="text-center col-lg-12">
                        <img src="../../../fotoUsuarios/aluno0146LUZL2020.jpg" id="fotoAluno" style="height:90px; width: 90px; border-radius: 50%;">&nbsp;&nbsp;&nbsp;<strong class="text-success" id="nomeAluno">Afonso Luzingu</strong>                      
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
                      <div class="col-lg-12 col-md-12 divBorder " style="padding-bottom: 5px; padding-top:15x;">

                        <div class="col-lg-3 col-md-3">
                          <label>Ano Lectivo</label>
                          <select class="form-control lead" id="idPAno" name="idPAno">
                          <?php 
                            foreach($manipulacaoDados->anosLectivos as $ano){ 
                                                  
                              echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                            }
                           ?>
                        </select>
                        </div>
                        <div class="col-lg-3 col-md-3">
                          <label>Mês:</label>
                            <select class="form-control" disabled id="mesPagar" name="mesPagar" required>

                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 text-center">
                          <label>Valor Pago</label>
                          <input type="number" step="5" min="0" class="form-control text-center" required name="valorPropina" id="valorPropina">  
                        </div>

                        <div class="col-lg-3 col-md-3 text-center">
                          <label>Valor</label>
                          <div class="divValor" id="divPropina"></div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-12 col-md-12 divBorder" style="padding-bottom: 10px; padding-top:15x;">

                        <div class="col-lg-4 col-md-4">
                          <strong>Conta</strong>
                          <select class="form-control" id="contaUsar" name="contaUsar">
                            <?php 
                              foreach($manipulacaoDados->selectArray("contas_bancarias", ["idPContaFinanceira", "descricaoConta"], ["idContaEscola"=>$_SESSION['idEscolaLogada']]) as $a){
                                echo "<option value='".$a["idPContaFinanceira"]."'>".$a["descricaoConta"]."</option>";
                              }
                             ?>
                          </select>
                        </div>
                        <div class="col-lg-4 col-md-4 text-center">
                          <label>Multa</label>
                          <input class="form-control text-center" required="" type="number" step="5" name="valorMulta" id="valorMulta">
                        </div>
                        <div class="col-lg-4 col-md-4 text-center">
                          <label>Total a Pagar</label>
                          <div class="divValor text-success" id="divPagar"></div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-lg-6 col-md-6">
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
                      <div class="col-lg-4 col-md-4">
                        Data do Pag.
                        <input type="date" value="<?php  echo $manipulacaoDados->dataSistema; ?>" required class="form-control" id="dataPagamento" name="dataPagamento">
                      </div>                      
                    </div>

                    <div class="row">
                        
                        <div class="col-lg-4 col-md-4 text-center">
                            <div class="valSoma">
                              <label>Soma</label>
                              <input class="form-control text-center" type="number" step="5" name="valorSomar" id="valorSomar">
                            </div>
                        </div>
                      <div class="col-lg-8 col-md-8 text-right lead"><br/>
                          <label><input type="checkbox" name="sePagamentoParcelado" id="sePagamentoParcelado"> Pagamento Em Parcela</label>
                      </div>
                    </div>

                    <input type="hidden" name="idPMatricula" id="idPMatricula">
                    <input type="hidden" name="classe" id="classe" value="<?php echo $classe ?>">
                    <input type="hidden" name="idPCurso" id="idPCurso" value="<?php echo $idCurso; ?>">
                    <input type="hidden" name="valorPagar" id="valorPagar">
                    <input type="hidden" name="mesInicialContar" id="mesInicialContar">
                    <input type="hidden" id="action" name="action" value="efectuarPagamento">
                    <input type="hidden" id="idPHistoricoConta" name="idPHistoricoConta" value="efectuarPagamento">
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