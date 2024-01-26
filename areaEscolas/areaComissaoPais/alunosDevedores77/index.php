<?php session_start();  
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Relatório de Pagamentos de Propinas", "alunosDevedores77");
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
          #formularioPagamento .modal-dialog{
            width: 60%;
            margin-left: -30%;
        }

        @media (max-width: 768px) {
            #formularioPagamento .modal-dialog{
                width: 94%;
                margin-left: 3%;
            }
             #formularioPagamento .modal-dialog .lab{
                text-align: left;
             }
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
                  <h1 class=" navbar-brand" style="color: white;"><strong><i class="fa fa-info-circle"></i> Lista dos Devedores</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "alunosDevedores77", array(), "msg")){ 

          $precoPorMensagem=14;
          echo "<script>var precoPorMensagem=".$precoPorMensagem."</script>";
          echo "<script>var abrevNomeEscola2='".valorArray($manipulacaoDados->sobreEscolaLogada, "abrevNomeEscola2")."'</script>";

          $mesPorDefeito = ($manipulacaoDados->mes-1);
          if($mesPorDefeito==0){
            $mesPorDefeito=12;
          }else if($mesPorDefeito==8){
            $mesPorDefeito=9;
          }

          $mes = isset($_GET["mes"])?$_GET["mes"]:$mesPorDefeito;
          $idPAno =  isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
          $idPNomeCurso =  isset($_GET["idPNomeCurso"])?$_GET["idPNomeCurso"]:$manipulacaoDados->selectUmElemento("nomecursos", "idPNomeCurso", ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada'], "cursos.estadoCurso"=>"A"]);

          echo "<script>var mes ='".$mes."'</script>";
          echo "<script>var idPAno='".$idPAno."'</script>";
          echo "<script>var idPNomeCurso='".$idPNomeCurso."'</script>";

          $array = $manipulacaoDados->selectArray("alunosmatriculados", ["pagamentos.idHistoricoAno", "telefoneAluno", "pagamentos.idHistoricoEscola", "pagamentos.idTipoEmolumento", "pagamentos.referenciaPagamento", "nomeAluno", "numeroInterno", "reconfirmacoes.nomeTurma", "reconfirmacoes.designacaoTurma", "fotoAluno", "reconfirmacoes.classeReconfirmacao", "escola.beneficiosDaBolsa"], ["reconfirmacoes.idReconfAno"=>$idPAno, "escola.idMatEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.idReconfEscola"=>$_SESSION['idEscolaLogada'], "reconfirmacoes.estadoReconfirmacao"=>"A", "reconfirmacoes.idMatCurso"=>$idPNomeCurso], ["reconfirmacoes", "escola"], "", [], array("nomeAluno"=>1));

          $alunosDevedores = array();
          foreach ($array as $aluno) {

            $beneficiosDaBolsa = valorArray($aluno, "beneficiosDaBolsa","escola");
            $beneficiosDaBolsa = (is_array($beneficiosDaBolsa) || is_object($beneficiosDaBolsa))?$beneficiosDaBolsa:array();

            $eGratuito="nao";
            foreach($beneficiosDaBolsa as $ben){
              if($ben["idPTipoEmolumento"]==1 && $ben["mes"]==$mes){
                  if($ben["valorPreco"]<=0){
                    $eGratuito="sim";
                  }
                  break;
              }
            }

            if(count(listarItensObjecto($aluno, "pagamentos", ["idHistoricoAno=".$idPAno, "idHistoricoEscola=".$_SESSION['idEscolaLogada'], "idTipoEmolumento=1", "referenciaPagamento=".$mes]))<=0 && $eGratuito=="nao"){
              $alunosDevedores[]=$aluno;
            }
          }
          echo "<script>var listaAlunos=".json_encode($alunosDevedores)."</script>";
          

          ?>
      <div class="card">
        <div class="card-body"> 
          <div class="row">
            <div class="col-lg-2 col-md-2 lead">
                Ano Lectivo
              <select class="form-control " id="anosLectivos">
                <?php 
                  foreach($manipulacaoDados->anosLectivos as $ano){                     
                    echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                  }
                 ?>
              </select>
            </div>
            <div class="col-lg-2 col-md-2 lead">
                Curso
              <select class="form-control " id="idPNomeCurso">
                <?php 
                  foreach($manipulacaoDados->selectArray("nomecursos", ["idPNomeCurso", "abrevCurso"], ["cursos.idCursoEscola"=>$_SESSION['idEscolaLogada']], ["cursos"], "", [], ["ordenacao"=>1]) as $curso){
                    echo "<option value='".$curso["idPNomeCurso"]."'>".$curso["abrevCurso"]."</option>";
                  }
                 ?>
              </select>
            </div>

            <div class="col-lg-2 col-md-2 lead">
                Mês
                  <select class="form-control " name="mesAtraso" id="mesAtraso">
                    <?php 

                  foreach($manipulacaoDados->mesesAnoLectivo as $i){ ?>
                    <option class="" value="<?php echo $i; ?>"><?php echo nomeMes($i); ?></option>
                 <?php } ?>
                  </select>
            </div>
            <div class="col-md-2 col-lg-2 "><br>
              <label class="">Total: <span id="totContas" class="quantidadeTotal">0</span></label>&nbsp;&nbsp;&nbsp;
            </div>
            <div class="col-lg-4 col-md-4 text-right"><br>
              <a href="../../relatoriosPdf/relatoriosFinanceiros/listaGeralDevedoresPropinas.php?idPAno=<?php echo $idPAno ?>&mes=<?php echo $mes; ?>&idPNomeCurso=<?php echo $idPNomeCurso; ?>" class="btn btn-primary "><i class="fa fa-print"></i> Lista</a>&nbsp;&nbsp;&nbsp;
              <a href="../../relatoriosPdf/relatoriosFinanceiros/listaDevedoresPorTurma.php?idPAno=<?php echo $idPAno ?>&mes=<?php echo $mes; ?>&idPNomeCurso=<?php echo $idPNomeCurso; ?>" class="btn btn-primary "><i class="fa fa-print"></i> Lista por Turma</a>
            </div>
          </div>

          <form id="formSubmit">
             <div class="row">
               <div class="col-lg-7 col-md-7">
                <h3 class="text-primary"><strong>Notificação</strong></h3>
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
                 <textarea class="form-control lead" required id="textoMensagem" name="textoMensagem" placeholder="Digite aqui a mensagem..." style="max-width: 100%; max-height: 100px;">Prezado aluno, lembramos do pagamento de <?php echo nomeMes($mes) ?>. A falta pode afetar acesso às notas e atividades acadêmicas.</textarea>
               </div>
               <div class="col-lg-2 col-md-2 text-right"><br>
                 <button type="submit" class="btn lead btn-success"><i class="fa fa-send"></i> Enviar</button>
               </div>
             </div>
          </form><br>

        <table id="example1" class="table table-striped table-bordered table-hover" >
          <thead class="corPrimary">
              <tr>
                <th class=" text-center"><strong></strong></th>
                <th class=""><strong>Nome do Aluno</strong></th>
                <th class=" font-weight-bolder text-center"><strong>N.º Interno</strong></th>
                <th class=" font-weight-bolder text-center"><strong>Classe</strong></th>
                <th class=" text-center"><strong>Turma</strong></th>
              </tr>
          </thead>
          <tbody>
              
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