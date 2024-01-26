<?php session_start();
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Conselho de Notas", "conselhoNotas");
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
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(); 
  ?>
  <section id="main-content"> 
        <section class="wrapper" id="containers">
          <div class="row" >
            <div class="col-lg-12 col-md-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <strong class="caret"></strong>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-users"></i> <strong>Conselho de Notas</strong></h1>
              
            </nav>
          </div>
        </div>
          <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", "conselhoNotas", array(), "msg")){ 


            $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
            echo "<script>var luzingu='".$luzingu."'</script>";
            $luzingu = explode("-", $luzingu);
            $idCurso = isset($luzingu[2])?$luzingu[2]:"";
            $classe = isset($luzingu[1])?$luzingu[1]:"";
            $periodo = isset($luzingu[0])?$luzingu[0]:""; 

            echo "<script>var periodo='".$periodo."'</script>";
            echo "<script>var classeP='".$classe."'</script>";
            echo "<script>var idCursoP='".$idCurso."'</script>";

            $array = $manipulacaoDados->selectArray("listaturmas", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idListaAno"=>$manipulacaoDados->idAnoActual, "classe"=>$classe, "idPNomeCurso"=>$idCurso]);
            $array = $manipulacaoDados->anexarTabela($array, "entidadesprimaria", "idPEntidade", "idPresidenteConselho");
            echo "<script>var gerenciadorTurmas = ".json_encode($array)."</script>";
          ?> 

        <div class="row">
            <div class="col-lg-2 col-md-2 lead">
              Classe:
              <select class="form-control lead" id="luzingu">
                <?php 
                  if(isset($_SESSION['classesPorCurso'])){
                    echo $_SESSION['classesPorCurso'];
                  }else{
                    $_SESSION['classesPorCurso']=retornarClassesPorCurso($manipulacaoDados, "", "nao");
                  }
                  ?>             
              </select>
            </div>           
        </div>

        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead class="corPrimary">
                <tr>
                    <th class="lead font-weight-bolder text-center"><strong>Turma</strong></th>
                    <th class="lead text-center"><strong>Data</strong></th>
                    <th class="lead"><strong>Hora</strong></th>
                    <th class="lead"><strong>Sala n.º</strong></th>
                    <th class="lead paraCoordenador"><strong>Presidente</strong></th>
                    <th class="lead text-center"></th>
                </tr>
            </thead>
            <tbody id="tabTurmas">

              
            </tbody>
        </table>
      </div><br>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>



 <div class="modal fade" id="formularioGerTurmas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
  <form class="modal-dialog" id="formularioGerTurmasF">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title lead font-weight-bolder" id="myModalLabel"><i class="fa fa-users"></i> Conselho de Notas</h4>
        </div>

        <div class="modal-body">
            <div class="row">
              <div class="col-md-4 lead">
                <label class="lead">Data</label>
                <input type="date" required class="form-control lead text-center" name="dataConselhoNotas" id="dataConselhoNotas">
              </div>
              <div class="col-md-4 lead">
                <label class="lead">Hora</label>
                <input type="text" required class="form-control lead text-center" name="horaConselhoNotas" id="horaConselhoNotas">
              </div>
              <div class="col-md-4 lead">
                <label>Reunido na Sala n.º</label>
                <input type="number" required min="0" class="form-control lead text-center" name="salaReunidoConselho" id="salaReunidoConselho">
              </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 lead">
                  <label class="lead">Presidente</label>
                  <select class="form-control" name="idPresidenteConselho" id="idPresidenteConselho">
                    <option value="">Seleccionar</option>
                    <?php foreach($manipulacaoDados->entidades() as $a){
                    echo "<option value='".$a["idPEntidade"]."'>".$a["nomeEntidade"]."</option>";
                    } ?>
                  </select>
                </div>
            </div>

            <input type="hidden" name="idPCurso" id="idPCurso" value="<?php echo $idCurso ?>">
            <input type="hidden" name="classe" id="classe" value="<?php echo $classe; ?>">
            <input type="hidden" name="idPAno" id="idPAno" value="<?php echo $manipulacaoDados->idAnoActual; ?>">
            <input type="hidden" name="idPListaTurma" id="idPListaTurma">
            <input type="hidden" name="action" value="manipularGerenciadorTurma">

        </div>
        <div class="modal-footer">
            <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 text-left">
                <button ype="submit" class="btn btn-success lead btn-lg submitter" id="Cadastar"><i class="fa fa-check"></i> Alterar</button>

              </div>                    
            </div>                
        </div>
      </div>
    </form>
</div>
