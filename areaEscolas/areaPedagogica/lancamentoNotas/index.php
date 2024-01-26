<?php session_start();
    include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Laçamento de Notas", "lancamentoNotas");
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
  <style type="text/css">
    #listaAlunos form{
      border-bottom:solid rgba(0, 0, 0, 0.2) 2px;
      padding-top: 10px;
      padding-bottom: 20px;
    }

    .caixaResultado{
      background-color: rgba(0,0,0,0.2) !important;
    }

    #listaAlunos form input.valorCt{
        background-color: transparent;
        color: black;
        font-weight: 700;
    }
     #listaAlunos form input{
      font-size: 12pt !important;
      padding: 0px;
    }

    #listaAlunos form input.observacaoF{
      font-weight: 700;
      background-color: transparent;
    }

    #divMapasEstatisticos .modal-dialog{
      width: 60%; 
      margin-left: -30%;
    }
    @media (max-width: 768px) {
          #divMapasEstatisticos .modal-dialog, .modal .modal-dialog{
              width: 94%;
              margin-left: 3%;

          }
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside();
    
    echo "<script>var modeloPauta='".$manipulacaoDados->modeloPauta."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
           <div class="row" >
              <div class="col-lg-12">
              <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                  <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                        <b class="caret"></b>
                                    </a>
                  <h1 class="lead navbar-brand" style="color: white;"><i class="fa fa-eye"></i><strong id="pGeral"> Control de Lançamento de Notas</strong></h1>
            </nav>
            </div>
          </div>
          <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "lancamentoNotas", array(), "msg")){

          $luzingu = isset($_GET["luzingu"])?$_GET["luzingu"]:classeInicial($manipulacaoDados);
            echo "<script>var luzingu='".$luzingu."'</script>";

          $luzingu = explode("-", $luzingu);
          $trimestreDefault="I";
          if($manipulacaoDados->mes>=3 && $manipulacaoDados->mes<=5){
            $trimestreDefault="II";
          }else if($manipulacaoDados->mes>5 && $manipulacaoDados->mes<=7){
            $trimestreDefault="III";
          }

          $trimestre = isset($_GET['trimestre'])?$_GET['trimestre']:$trimestreDefault;

          if($trimestre=="I" || $trimestre=="II" || $trimestre=="III"){
            $campo="mt".$trimestre;
          }else{
            $campo="exame";
          }

          $idCurso = isset($luzingu[2])?$luzingu[2]:"";
          $classe = isset($luzingu[1])?$luzingu[1]:"";
          $periodo = isset($luzingu[0])?$luzingu[0]:"";


          echo "<script>var periodo='".$periodo."'</script>";
          echo "<script>var classeP='".$classe."'</script>";
          echo "<script>var idCursoP='".$idCurso."'</script>";
          echo "<script>var trimestre='".$trimestre."'</script>";

          

          
          $disciplinas=array();
          foreach($array = $manipulacaoDados->disciplinas($idCurso, $classe, $periodo, "", array(), [58, 59, 60, 231, 232, 233], ["idPNomeDisciplina", "abreviacaoDisciplina2"]) as $disciplina){
            
            $disciplinas[]=["idPNomeDisciplina"=>$disciplina["idPNomeDisciplina"], "nomeDisciplina"=>$disciplina["abreviacaoDisciplina2"]];
          }

          $listaTurmas = array_filter(turmasEscola($manipulacaoDados), function ($mamale) use ($classe, $idCurso){
            return ($mamale["classe"]==$classe && ($mamale["idPNomeCurso"]==$idCurso || $classe<=9));
          });
          ?>

    <div class="card">
        <div class="card-body">
          <div class="row">
               <div class="col-lg-2 col-md-2 lead">
                  Classe:
                   <select class="form-control" id="luzingu">   
                    <?php 
                  if(isset($_SESSION['classesPorCurso'])){
                    echo $_SESSION['classesPorCurso'];
                  }else{
                    $_SESSION['classesPorCurso']=retornarClassesPorCurso($manipulacaoDados, "", "nao");
                  }
                  ?>               
                  </select>
              </div>
              <div class="col-lg-2 col-md-2 lead">
                  Trimestre:
                   <select class="form-control" id="trimestre">   
                    <option value="I">I.º Trimestre</option>     
                    <option value="II">II.º Trimestre</option>     
                    <option value="III">III.º Trimestre</option>     
                    <option value="IV">IV.º Trimestre</option>                 
                  </select>
              </div>  
              <div class="col-lg-2 col-md-2 lead"><br>
                <!--<a href="../../relatoriosPdf/lancamentoNotas.php?idPCurso=<?php echo $idCurso ?>&classe=<?php echo $classe ?>&trimestre=<?php echo $trimestre; ?>" class="btn btn-primary btn-2x"><i class="fa fa-print"></i> Visualizar</a>!-->
              </div> 
          </div>
          <table id="example1" class="table table-striped table-bordered table-hover" >
              <thead class="corPrimary">
                  <tr>                     
                      <th><strong>Turmas</strong></th>
                      <?php 
                      foreach ($disciplinas as $tur) {
                        echo '<th class="text-center" style="font-size:11pt;"><strong>'.$tur["nomeDisciplina"].'</strong></th>';                   
                      } ?>
                  </tr>
              </thead>
              <tbody id="tabela">
                <?php

                  $camposBuscar =["pautas.".$campo, "pautas.idPautaDisciplina"];
                  if($trimestre=="I" || $trimestre=="II" || $trimestre=="III"){
                    $camposBuscar[]="pautas.mac".$trimestre;
                    $camposBuscar[]="pautas.npp".$trimestre;
                    $camposBuscar[]="pautas.npt".$trimestre;
                  }
                  foreach($listaTurmas as $turma){
                   
                    echo "<tr><td>".$turma["designacaoTurma"]."</td>";
                    $pautasTurma = $manipulacaoDados->alunosPorTurma($idCurso, $classe, $turma["nomeTurma"], $manipulacaoDados->idAnoActual, array(), $camposBuscar, ["pautas"], ["pautas.classePauta"=>$classe, "pautas.idPautaCurso"=>$idCurso]);

                    foreach ($disciplinas as $disciplina) {
                      $pautasDisciplina = array();

                      $totaPauta=0;
                      $totaLancado=0;
                      $totaLancadoMac=0;
                      $totaLancadoNpp=0;
                      $totaLancadoNpt=0;

                       foreach(array_filter($pautasTurma, function ($mamale) use ($disciplina){
                          return $mamale["pautas"]["idPautaDisciplina"]==$disciplina["idPNomeDisciplina"];
                       }) as $pauta){
                          $totaPauta++;
                          if(isset($pauta["pautas"][$campo]) && $pauta["pautas"][$campo]!=NULL && $pauta["pautas"][$campo]!=""){
                            $totaLancado++;
                          }
                          if($trimestre=="I" || $trimestre=="II" || $trimestre=="III"){
                            if(isset($pauta["pautas"]["mac".$trimestre]) && $pauta["pautas"]["mac".$trimestre]!=NULL && $pauta["pautas"]["mac".$trimestre]!=""){
                                $totaLancadoMac++;
                            }
                            if(isset($pauta["pautas"]["npp".$trimestre]) && $pauta["pautas"]["npp".$trimestre]!=NULL && $pauta["pautas"]["npp".$trimestre]!=""){
                                $totaLancadoNpp++;
                            }
                            if(isset($pauta["pautas"]["npt".$trimestre]) && $pauta["pautas"]["npt".$trimestre]!=NULL && $pauta["pautas"]["npt".$trimestre]!=""){
                                $totaLancadoNpt++;
                            }
                          }
                       }
                      echo "<td class='text-center'>".seJaLancou($manipulacaoDados, $idCurso, $classe, $turma["nomeTurma"], $disciplina["idPNomeDisciplina"], $trimestre, $turma["periodoTurma"], $totaPauta, $totaLancado, $totaLancadoMac, $totaLancadoNpp, $totaLancadoNpt)."</td>";
                    }
                    echo "</tr>";
                  }

                 ?>

              </tbody>
          </table><br>
        </div>
    </div><br>
    <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();
  

  function seJaLancou($m, $idCurso, $classe, $turma, $idPNomeDisciplina, $trimestre, $periodoTurma, $totaPauta, $totaLancado, $totaLancadoMac, $totaLancadoNpp, $totaLancadoNpt){
    
     
     if($totaLancado>($totaPauta*0.5)){
        $stringAdicionar="";
        if($trimestre=="I" || $trimestre=="II" || $trimestre=="III"){
          if($totaLancadoMac<=($totaPauta*0.5)){
            $stringAdicionar .="<span class='text-danger'><i class='fa fa-times-circle'></i>MAC</span>";
          }
          if($totaLancadoNpp<=($totaPauta*0.5)){
            if($stringAdicionar!=""){
              $stringAdicionar .="&nbsp;&nbsp;&nbsp;";
            }
            $stringAdicionar .="<span class='text-danger'><i class='fa fa-times-circle'></i>NPP</span>";
          }

          if($totaLancadoNpt<=($totaPauta*0.5)){
            if($stringAdicionar!=""){
              $stringAdicionar .="&nbsp;&nbsp;&nbsp;";
            }
            $stringAdicionar .="<span class='text-danger'><i class='fa fa-times-circle'></i>NPT</span>";
          }
        }
        return "<i class='fa fa-check text-success fa-2x'></i><br/><span style='font-size:9pt;'>".$stringAdicionar."</span>";
     }else{
        if($classe<=4){
          $divisaoProfessor =$m->selectArray("divisaoprofessores", ["nomeEntidade"], ["classe"=>$classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$m->idAnoActual, "nomeTurmaDiv"=>$turma]);
        }else{
          $divisaoProfessor =$m->selectArray("divisaoprofessores", ["nomeEntidade"], ["classe"=>$classe, "idPEscola"=>$_SESSION["idEscolaLogada"], "idDivAno"=>$m->idAnoActual, "nomeTurmaDiv"=>$turma, "idPNomeDisciplina"=>$idPNomeDisciplina, "idPNomeCurso"=>$idCurso]);
        }
        $professor = abreviarDoisNomes(valorArray($divisaoProfessor, "nomeEntidade"));

      return "<i class='fa fa-times text-danger fa-2x'></i><br/><span style='font-size:10pt;'>".$professor."</span>";
     }      
  }


 ?>