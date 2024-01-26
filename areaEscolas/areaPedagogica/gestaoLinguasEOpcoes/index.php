<?php session_start(); 
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Gestão de Línguas e Opções", "gestaoLinguasEOpcoes");
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
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-pen-alt"></i> <strong>Gerenciador de Línguas e Disciplinas de Opção</strong></h1>
              
            </nav>
          </div>
        </div>
          <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso("", ["gestaoLinguasEOpcoes"], "", "", valorArray($manipulacaoDados->sobreUsuarioLogado, "tipoPacoteEscola"))){ 

             $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
              echo "<script>var idPAno=".$idPAno."</script>";


            $array = $manipulacaoDados->selectArray("escolas", ["gerencMatricula.idGerMatEscola", "gerencMatricula.idPGerMatr", "gerencMatricula.idCurso", "gerencMatricula.classe", "gerencMatricula.periodoClasse", "gerencMatricula.idsLinguasEtrang", "gerencMatricula.idsDisciplOpcao", "gerencMatricula.chavePrincipal"], ["idPEscola"=>$_SESSION['idEscolaLogada']], ["gerencMatricula"]);
            $array = $manipulacaoDados->anexarTabela2($array, "nomecursos", "gerencMatricula", "idPNomeCurso", "idCurso");

            echo "<script>var listaValores =".json_encode($array)."</script>";

              $linguasEstrangeira="";

              foreach ($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["idPNomeDisciplina"=>['$in'=>array(20, 21)]]) as $disciplina) {

                $linguasEstrangeira .="<label><input type='checkbox' idDisciplina='".$disciplina["_id"]."' class='linguasEstrangeira'> ".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$disciplina["_id"]])."<label>&nbsp;&nbsp;&nbsp;";
              }

              $disciplinasOpcao="";
              foreach ($manipulacaoDados->selectDistinct("nomedisciplinas", "idPNomeDisciplina", ["idPNomeDisciplina"=>['$in'=>array(122, 14, 17, 9)]]) as $disciplina) {

                $disciplinasOpcao .="<label><input type='checkbox' idDisciplina='".$disciplina["_id"]."' class='disciplinasOpcao'> ".$manipulacaoDados->selectUmElemento("nomedisciplinas", "nomeDisciplina", ["idPNomeDisciplina"=>$disciplina["_id"]])."<label>&nbsp;&nbsp;&nbsp;";
              }

          ?> 
      
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-10 col-md-10"><br>
            <button class="btn btn-success lead" id="btnAlterar"><i class="fa fa-check"></i> Alterar</button>&nbsp;&nbsp;&nbsp;
            <a href="#" id="actualizar" title="Actualizar"><i class="fa fa-spinner fa-2x"></i></a>
          </div>
        </div>
        <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead class="corPrimary">
              <tr>
                  <th class="lead font-weight-bolder text-center"><strong>Classes</strong></th>
                  <th class="lead"><strong>Línguas Estrangeiras</strong></th>
                  <th class="lead"><strong>Disciplinas de Opções</strong></th>
              </tr>
            </thead>
            <tbody id="tabTurmas">
              <?php 
                foreach($array as $a){

                  echo '<tr id="'.$a["gerencMatricula"]["idPGerMatr"].'"><td class="lead" style="font-size:14pt;"><br/><strong>'.nelson($a, "abrevCurso")." - ".$a["gerencMatricula"]["classe"].' - '.periodoExtenso($a["gerencMatricula"]["periodoClasse"]).'</strong></td><td class="lead">'.$linguasEstrangeira.'</td><td class="lead">'.$disciplinasOpcao.'</td></tr>';
                }
               ?>
            </tbody>
        </table>
      </div></div></div><br>

        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>