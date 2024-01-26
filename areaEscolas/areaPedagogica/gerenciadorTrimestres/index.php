<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Gerenciador de Trimestres", "gerenciadorTrimestres");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-cog"></i> Gerenciador de Trimestres</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["gerenciadorTrimestres"], array(), "msg")){


          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:"luzingu";
          echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);
          $classe=isset($luzingu[1])?$luzingu[1]:"";
          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $turma = isset($luzingu[0])?$luzingu[0]:"";

          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var turma='".$turma."'</script>";

          $condicao = ["idPEscola"=>$_SESSION['idEscolaLogada'], "idDivAno"=>$manipulacaoDados->idAnoActual];
          if($turma!="luzingu"){
            $condicao["nomeTurmaDiv"]=$turma;
            $condicao["classe"]=$classe;
            if($classe>=10){
              $condicao["idPNomeCurso"]=$idCurso;
            }
          }

          echo "<script>var listaDivisaoProfessores = ".$manipulacaoDados->selectJson("divisaoprofessores", ["abrevCurso", "classe", "designacaoTurmaDiv", "abreviacaoDisciplina2", "periodoTrimestre", "idPDivisao", "sePorSemestre"], $condicao)."</script>";
           ?>

    <div class="card">
      <div class="card-body">
          <div class="row">
            <div class="col-lg-4 col-md-4 lead">
                Turma:
                <select class="form-control lead" id="luzingu">
                  <option value="luzingu">Todas turmas</option>
                  <?php optTurmas($manipulacaoDados); ?>
                </select>
            </div>
          </div>
          <div class="table-responsive">

                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead>
                      <tr class="corPrimary">
                        <td class="lead">
                        </td>
                        <td class="lead">
                        </td>
                        <td class="text-center lead">trimestre1<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-trimestre1" class="altEstado"><span class="lever"></span></label></div></td>
                        <td class="text-center lead">trimestre2<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-trimestre2" class="altEstado"><span class="lever"></span></label></div></td>
                        <td class="text-center lead">trimestre3<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-trimestre3" class="altEstado"><span class="lever"></span></label></div></td>
                        <td class="text-center lead">exame<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-exame" class="altEstado"><span class="lever"></span></label></div></td>
                        <td class="text-center lead">conselho<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-conselho" class="altEstado"><span class="lever"></span></label></div></td>
                        <td class="text-center lead">recurso<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-recurso" class="altEstado"><span class="lever"></span></label></div></td>
                        <td class="text-center lead">todos<div class="switch"><label class="lead"><input type="checkbox" style="margin-left: -15px;" id="0-todos" class="altEstado"><span class="lever"></span></label></div></td>
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>

                </table>
          </div>
        </div>
      </div><br>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>
