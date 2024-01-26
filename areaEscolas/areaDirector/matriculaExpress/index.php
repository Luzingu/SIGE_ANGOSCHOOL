<?php session_start(); 
   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Matrícula Express", "matriculaExpress");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-user-plus"></i> Matrícula Express</strong></h1>
                  
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php if($verificacaoAcesso->verificarAcesso("", ["matriculaExpress"], array(), "msg")){ 
          
          include_once '../../manipulacaoDadosDoAjax.php';
          $mAjax = new manipulacaoDadosAjax(__DIR__);

          if(isset($_POST['btnEnviar'])){

              if(isset($_FILES['arquivo']) ){

                $arquivo = new DomDocument();
                $arquivo->load($_FILES['arquivo']['tmp_name']);            
                  
                $linhas = $arquivo->getElementsByTagName("Row");

                foreach($linhas as $linha){
                  $nomeAluno = trim($linha->getElementsByTagName("Data")->item(0)->nodeValue);
                  $sexoAluno = trim($linha->getElementsByTagName("Data")->item(1)->nodeValue);
                  $sexoAluno = substr($sexoAluno, 0, 1);

                  $idAnexo = trim($linha->getElementsByTagName("Data")->item(2)->nodeValue);

                  if(count(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "anexos", ["idPAnexo=".$idAnexo]))<=0){
                    $idAnexo = valorArray(listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "anexos", []), "idPAnexo");
                  }

                  echo $nomeAluno."<br>";

                  $periodo = trim($linha->getElementsByTagName("Data")->item(3)->nodeValue);

                  if($periodo!="reg" && $periodo!="pos"){
                    $periodo="reg";
                  }

                  $turno = trim($linha->getElementsByTagName("Data")->item(4)->nodeValue);
                  $idPCurso = trim($linha->getElementsByTagName("Data")->item(5)->nodeValue);
                  if($idPCurso==0){
                      $idPCurso="";
                  }
                  if($turno!="Matinal" && $turno!="Vespertino" && $turno!="Noturno"){
                    $turno="Automático";
                  }
                  $classe = trim($linha->getElementsByTagName("Data")->item(6)->nodeValue);
                  $turma = trim($linha->getElementsByTagName("Data")->item(7)->nodeValue);
                  $idGestLinguaEspecialidade = trim($linha->getElementsByTagName("Data")->item(8)->nodeValue);
                  $idGestDisEspecialidade = trim($linha->getElementsByTagName("Data")->item(9)->nodeValue);
                  
                  $array = $manipulacaoDados->selectArray("alunosmatriculados", [], ["nomeAluno"=>$nomeAluno, "escola.idMatEscola"=>$_SESSION["idEscolaLogada"]], ["escola"]);

                  if(count($array)<=0){
                      
                      $jaExistemNumero="V";
                      while ($jaExistemNumero=="V"){
                          $characters= "1234567890";
                          $numeroUnico = substr(str_shuffle($characters),0, 4)."ANGOS2".substr(str_shuffle($characters),0, 3);
                          if(count($manipulacaoDados->selectArray("alunosmatriculados", ["numeroInterno"], ["numeroInterno"=>$numeroUnico],[], 1))<=0){
                              $jaExistemNumero="F";
                          }   
                      }
                      $agrupador = $manipulacaoDados->selectArray("agrup_alunos", ["idPGrupo"]);
                      $grupo = count($agrupador)-1;

                      $idMatriculaPorDefeito="";
                      if(valorArray($manipulacaoDados->selectArray("alunos_".$grupo, ["soma"], [], [], "", ["soma"=>['$sum'=>1]]), "soma")>=15000){

                          //Criando uma nova colecção modelo...
                          $ultimoRegisto=$manipulacaoDados->selectArray("alunos_".$grupo, ["idPMatricula"], ["idPMatricula"=>array('$ne'=>"modelo")], [], 1, [], ["idPMatricula"=>-1]);
                          $idMatriculaPorDefeito=valorArray($ultimoRegisto, "idPMatricula");
                          $grupo++;
                          $manipulacaoDados->inserir("agrup_alunos", "idPGrupo", "grupo", [$grupo]);
                      }
                      
                      if($manipulacaoDados->inserir("alunos_".$grupo, "idPMatricula", "nomeAluno, sexoAluno, dataNascAluno, comunaNascAluno, municNascAluno, provNascAluno, paisNascAluno, numeroInterno, fotoAluno, estadoActividade, estadoAcessoAluno, senhaAluno, grupo, classe_".$_SESSION['idEscolaLogada'].", idMatCurso_".$_SESSION['idEscolaLogada'].", escola_".$_SESSION['idEscolaLogada'].", turma_".$_SESSION['idEscolaLogada'],  [$nomeAluno, $sexoAluno, "1999-03-01", 7, 5, 7, 7, $numeroUnico, "usuario_default.png", "A", "A", "0c7".criptografarMd5("0000")."ab", $grupo, $classe, $idPCurso, "sim", $turma])=="sim"){

                          $idPMatricula = $manipulacaoDados->selectUmElemento("alunosmatriculados", "idPMatricula", ["numeroInterno"=>$numeroUnico]);

                          $expl = explode("ANGOS", $numeroUnico);
                          $numeroProcesso = $expl[0].$expl[1];
                          
                          $arrayIdCursos[]=array("idMatAno"=>$manipulacaoDados->idAnoActual, "idMatEntidade"=>$_SESSION['idUsuarioLogado'], "estadoAluno"=>"A", "dataMatricula"=>$manipulacaoDados->dataSistema, "horaMatricula"=>$manipulacaoDados->tempoSistema, "periodoAluno"=>$periodo, "numeroProcesso"=>$numeroProcesso, "inscreveuSeAntes"=>"F", "idMatAnexo"=>$idAnexo, "idMatCurso"=>$idPCurso, "classeActualAluno"=>$classe, "estadoDeDesistenciaNaEscola"=>"A", "turnoAluno"=>$turno, "idGestLinguaEspecialidade"=>$idGestLinguaEspecialidade, "idGestDisEspecialidade"=>$idGestDisEspecialidade);

                          $manipulacaoDados->inserirObjecto("alunos_".$grupo, "escola", "idPAlEscola", "idFMatricula, idMatAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, numeroProcesso, inscreveuSeAntes, idMatAnexo, idMatCurso, classeActualAluno, estadoDeDesistenciaNaEscola, turnoAluno, idGestLinguaEspecialidade, idGestDisEspecialidade, idCursos", [$idPMatricula, $manipulacaoDados->idAnoActual, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], "A",$manipulacaoDados->dataSistema, $manipulacaoDados->tempoSistema, $periodo, $numeroProcesso, "F", $idAnexo, $idPCurso, "".$classe."", "A", $turno, $idGestLinguaEspecialidade, $idGestDisEspecialidade, $arrayIdCursos], ["idPMatricula"=>$idPMatricula]);


                        $manipulacaoDados->inserirObjecto("alunos_".$grupo, "reconfirmacoes", "idPReconf", "idReconfMatricula, dataReconf, horaReconf, idMatCurso, classeReconfirmacao, tipoEntrada, chaveReconf, idReconfProfessor, idReconfAno, estadoReconfirmacao, idReconfEscola, nomeTurma, designacaoTurma", [$idPMatricula, $manipulacaoDados->dataSistema, $manipulacaoDados->tempoSistema, $idPCurso, $classe, "novaMatricula", $idPMatricula."-".$idPCurso."-".$manipulacaoDados->idAnoActual."-".$_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $manipulacaoDados->idAnoActual, "A", $_SESSION["idEscolaLogada"], $turma, $turma], ["idPMatricula"=>$idPMatricula]);

                        $mAjax->actuazalizarReconfirmacaAluno($idPMatricula);
          
                          if(count($manipulacaoDados->selectArray("listaturmas", ["idPNomeCurso"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "classe"=>$classe, "nomeTurma"=>$turma, "idListaAno"=>$manipulacaoDados->idAnoActual, "idPNomeCurso"=>$idPCurso]))<=0 && $turma!="" && $turma!=NULL){
                            
                            if($turno=="Automático"){
                              $turno="Matinal";
                            }
                            $manipulacaoDados->inserir("listaturmas", "idPListaTurma", "nomeTurma, designacaoTurma, classe, idPEscola, idListaAno, periodoTurma, idAnexoTurma, idPNomeCurso, periodoT", [$turma, $turma, "".$classe."", $_SESSION["idEscolaLogada"], $manipulacaoDados->idAnoActual, $periodo, $idAnexo, $idPCurso, $turno], "sim", "nao", [["nomecursos", $idPCurso, "idPNomeCurso"]]); 
                        }

                      }else{
                          echo $nomeAluno." não foi possível<br/>";
                      }
                  }else{
                    if(count($manipulacaoDados->selectArray("listaturmas", ["idPNomeCurso"], ["idPEscola"=>$_SESSION['idEscolaLogada'], "classe"=>$classe, "nomeTurma"=>$turma, "idListaAno"=>$manipulacaoDados->idAnoActual, "idPNomeCurso"=>$idPCurso]))<=0 && $turma!="" && $turma!=NULL){
                          if($turno=="Automático"){
                            $turno="Matinal";
                          }
                          $manipulacaoDados->inserir("listaturmas", "idPListaTurma", "nomeTurma, designacaoTurma, classe, idPEscola, idListaAno, periodoTurma, idAnexoTurma, idPNomeCurso, periodoT", [$turma, $turma, "".$classe."", $_SESSION["idEscolaLogada"], $manipulacaoDados->idAnoActual, $periodo, $idAnexo, $idPCurso, $turno], "sim", "nao", [["nomecursos", $idPCurso, "idPNomeCurso"]]); 
                      }

                    $idCursos = valorArray($array, "idCursos", "escola");
                    $novoArrayIdCursos = array();
                    $i=0;
                    $ja="nao";
                    foreach($idCursos as $a){
                      if($a["idMatCurso"]==$idPCurso){

                        $ja="sim";
                        $novoArrayIdCursos[$i]["periodoAluno"]=$periodo;
                        $novoArrayIdCursos[$i]["numeroProcesso"]=valorArray($array, "numeroProcesso", "escola");
                        $novoArrayIdCursos[$i]["idMatAnexo"]=$idAnexo;
                        $novoArrayIdCursos[$i]["idMatCurso"]=$idPCurso;
                        $novoArrayIdCursos[$i]["classeActualAluno"]=$classe;
                        $novoArrayIdCursos[$i]["turnoAluno"]=$turno;
                      }else{
                        $novoArrayIdCursos[$i]=$a;
                      }
                      $i++;
                    }
                    if($ja=="nao"){
                      $novoArrayIdCursos[]=array("idMatAno"=>$manipulacaoDados->idAnoActual, "idMatEntidade"=>$_SESSION['idUsuarioLogado'], "estadoAluno"=>"A", "dataMatricula"=>$manipulacaoDados->dataSistema, "horaMatricula"=>$manipulacaoDados->tempoSistema, "periodoAluno"=>$periodo, "numeroProcesso"=>valorArray($array, "numeroProcesso", "escola"), "inscreveuSeAntes"=>"F", "idMatAnexo"=>$idAnexo, "idMatCurso"=>$idPCurso, "classeActualAluno"=>$classe, "estadoDeDesistenciaNaEscola"=>"A", "turnoAluno"=>$turno);
                    }
                    

                    $manipulacaoDados->editarItemObjecto("alunos_".valorArray($array, "grupo"), "escola", "idFMatricula, idMatAno, idMatEscola, idMatEntidade, estadoAluno, dataMatricula, horaMatricula, periodoAluno, numeroProcesso, inscreveuSeAntes, idMatAnexo, idMatCurso, classeActualAluno, estadoDeDesistenciaNaEscola, turnoAluno, idGestLinguaEspecialidade, idGestDisEspecialidade, idCursos", [valorArray($array, "idPMatricula"), $manipulacaoDados->idAnoActual, $_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], "A",$manipulacaoDados->dataSistema, $manipulacaoDados->tempoSistema, $periodo, valorArray($array, "numeroProcesso", "escola"), "F", $idAnexo, $idPCurso, "".$classe."", "A", $turno, $idGestLinguaEspecialidade, $idGestDisEspecialidade, $novoArrayIdCursos], ["idPMatricula"=>valorArray($array, "idPMatricula")], ["idMatEscola"=>$_SESSION['idEscolaLogada']]);

                    $manipulacaoDados->excluirItemObjecto("alunos_".valorArray($array, "grupo"), "reconfirmacoes", ["idPMatricula"=>valorArray($array, "idPMatricula")], ["idReconfEscola"=>$_SESSION['idEscolaLogada'], "idReconfAno"=>$manipulacaoDados->idAnoActual]);

                    $manipulacaoDados->inserirObjecto("alunos_".valorArray($array, "grupo"), "reconfirmacoes", "idPReconf", "idReconfMatricula, dataReconf, horaReconf, idMatCurso, classeReconfirmacao, tipoEntrada, chaveReconf, idReconfProfessor, idReconfAno, estadoReconfirmacao, idReconfEscola, nomeTurma, designacaoTurma", [valorArray($array, "idPMatricula"), $manipulacaoDados->dataSistema, $manipulacaoDados->tempoSistema, $idPCurso, $classe, "novaMatricula", valorArray($array, "idPMatricula")."-".$idPCurso."-".$manipulacaoDados->idAnoActual."-".$_SESSION["idEscolaLogada"], $_SESSION["idUsuarioLogado"], $manipulacaoDados->idAnoActual, "A", $_SESSION["idEscolaLogada"], $turma, $turma], ["idPMatricula"=>valorArray($array, "idPMatricula")]);
                    $mAjax->actuazalizarReconfirmacaAluno(valorArray($array, "idPMatricula")); 

                      echo $nomeAluno. " já existe<br/>";
                  }
                }
              }
          } ?>
        
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12 col-md-12">
              <h3 style="font-weight: bolder; line-height:40px; padding: 10px;">NOME DO ALUNO <span style="color: red;">/</span> SEXO <span style="color: red;">/</span> ID ANEXO <span style="color: red;">/</span> PERÍODO <span style="color: red;">/</span> TURNO(Automático) <span style="color: red;">/</span> ID CURSO <span style="color: red;">/</span> CLASSE <span style="color: red;">/</span> TURMA</h3>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 col-md-4 col-md-offset-4 col-lg-offset-4">
              <form method="POST" enctype="multipart/form-data">
                 <div class="row">
                   <div class="col-lg-12 col-md-12">
                    <label>Arquivo</label>
                     <input type="file" class="form-control" required name="arquivo" placeholder="Pesquisar" size="30">
                   </div>
                 </div>
                 <div class="row">
                   <div class="col-lg-4 col-md-4 ">
                    <button type="submit" class="btn btn-primary" name="btnEnviar"><i class="fa fa-send"></i> Enviar</button>
                   </div>
                 </div>
              </form>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 col-md-12" style="font-size:19px; margin-left: 20px;">
              <?php 
                foreach($manipulacaoDados->selectArray("nomecursos", [], [], [], "", [], ["nomeCurso"=>1]) as $a){
                  echo $a["nomeCurso"]." - (<span style='color:red'>".$a["idPNomeCurso"]."</span>)<br>";
                }

               ?>
            </div>
          </div>
          </div>
          </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs();$janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>