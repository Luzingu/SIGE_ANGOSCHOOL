<?php session_start();
     include_once ('../../funcoesAuxiliares.php');
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Horário Individual - Professor", "horarioProfessor33");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->retornarAnosEmJavascript();
    $layouts->idPArea = 2;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
 ?>

 <!DOCTYPE html> 
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #tabHorario{
      border: solid rgba(0,0,0,0.2) 2px;
    }
    #tabHorario .luzingu{
      background-color: rgba(0,0,0,0.2);
    }
    #tabHorario tr td{
      text-align: center;
      vertical-align: middle;
      background-color: rgba(0,0,0,0.1);
      font-weight: bolder;
      font-size: 9pt !important;
    }
    #tabHorario{
      background-color: rgba(0,0,0,0.1);
    }
  </style>
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    $layouts->cabecalho();
    $layouts->aside(2);
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">
      <div class="row" >
        <div class="col-lg-12 col-md-12">
          <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

              <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                                    <b class="caret"></b>
                                </a>
              <h1 class="lead navbar-brand" style="color: white;"><strong><i class="<?php echo $manipulacaoDados->iconeMenu; ?>"></i> <?php echo $manipulacaoDados->designacaoMenu ?></strong></h1>
        </nav>
      </div>
    </div>
    <div class="main-body">
      <?php if($verificacaoAcesso->verificarAcesso(2, array(), array(), "msg")){
        
        $horarioProfesor = $manipulacaoDados->selectArray("horario", [], ["idPEscola"=>$_SESSION['idEscolaLogada'], "idHorAno"=>$manipulacaoDados->idAnoActual, "idPEntidade"=>$_SESSION['idUsuarioLogado']]);

        $gerenciador_periodo = listarItensObjecto($manipulacaoDados->sobreEscolaLogada, "gerencPerido", ["idGerPerEscola=".$_SESSION['idEscolaLogada'], "idGerPerAno=".$manipulacaoDados->idAnoActual]);

        $diasProfessor = array();
        foreach (distinct2($horarioProfesor, "dia") as $dia) {
           $diasProfessor[] = $dia;
        }
        usort($diasProfessor, "usortTest");

        $turnosProfessor=array();
        foreach (distinct2($horarioProfesor, "periodoT") as $turno) {
            $turnosProfessor[] = $turno;
        }

        $temposPorTurnoProfessor=array();
        foreach ($turnosProfessor as $turno) {
            foreach(distinct2(condicionadorArray($horarioProfesor, ["periodoT=".$turno]), "tempo") as $tempo){
                $temposPorTurnoProfessor[]=["turno"=>$turno, "tempo"=>$tempo];
            }
       }
        $temposPorTurnoProfessor = ordenar($temposPorTurnoProfessor, "tempo ASC");
         ?>

      <div class="row">
        <div class="col-md-12 text-right"><a href="../../relatoriosPdf/relatoriosProfessores/horarioProfessor.php" class="lead btn-primary btn" ><i class="fa fa-print"></i> Visualizar</a></div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="tabHorario">
          <?php 
            echo "<tr class='corPrimary'><td  rowspan='2' class='lead'><strong>T</strong></td><td rowspan='2' class='lead'><strong>HORAS</strong></td>";
            foreach ($diasProfessor as $dia) { 
                echo "<td class='maiuscula lead' colspan='3'><strong>".diaSemana2($dia)."</strong></td>";
            }
            echo "</tr><tr class='bolder corPrimary'>";
            foreach ($diasProfessor as $dia) {
                echo "<td class='maiuscula bolder'>TURMA</td><td class='lead maiuscula text-center bolder'>DISC.</td><td class='lead maiuscula border'>SALA</td></td>";
            }
            echo "</tr>";


        foreach ($turnosProfessor as $turno) {
              echo "<tr><td class='maiuscula bolder corPrimary' colspan='".(2+count($diasProfessor)*3)."' style='background-color: #428bca !important;'>HORÁRIO ".$turno."</td></tr>";

              foreach ($temposPorTurnoProfessor as $tempo) {
                 if($tempo["turno"]==$turno){

                      echo "<tr><td class='maiuscula text-center' style='background-color: #428bca !important;'>".$tempo["tempo"]."º</td><td class='maiuscula text-center' style='background-color: #428bca !important;'>".retornarHora($gerenciador_periodo, $turno, $tempo["tempo"])."</td>";
                      foreach ($diasProfessor as $dia) { 
                          echo "<td style='bolder maiuscula text-center' >".retornarCampo($manipulacaoDados, $horarioProfesor, $tempo["tempo"], $tempo["turno"], $dia, "turma")."</td><td class='maiuscula bolder'>".retornarCampo($manipulacaoDados, $horarioProfesor, $tempo["tempo"], $tempo["turno"], $dia, "abreviacaoDisciplina2")."</td><td class='maiuscula text-center bolder'>".completarNumero(retornarCampo($manipulacaoDados, $horarioProfesor, $tempo["tempo"], $tempo["turno"], $dia, "numeroSalaTurma"))."</td>";
                      }
                      echo "</tr>";
                      if($tempo["tempo"]==retornarCampoPorPeriodo($gerenciador_periodo, $turno, "intevaloDepoisDoTempo")){
                          echo "<tr><td class='maiuscula bolder text-center corPrimary' colspan='".(2+count($diasProfessor)*3)."' >INTERVALO</td></tr>";
                      }
                 }
              }
          }



          ?>
            
        </table>
      </div>

        <?php } echo "</div><br>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); 
  

    function retornarCampo($manipulacaoDados, $horarioProfesor, $tempo, $turno, $dia, $campoPesquisado=""){
        $retorno="";
        foreach ($horarioProfesor as $horario) {
            if($horario["tempo"]==$tempo && $horario["periodoT"]==$turno && $horario["dia"]==$dia){

                if($campoPesquisado=="turma"){
                    $matondo=$horario["designacaoTurmaHor"];
                        
                    if(trim($matondo)==NULL || trim($matondo)==""){
                        $matondo = $horario["designacaoTurmaHor"];
                    }
                    $retorno = $matondo;
                    if($_SESSION["idEscolaLogada"]!=10){
                        $retorno = $horario["abrevCurso"].$horario["classe"].$matondo;
                    }
                }else{
                    $retorno = $horario->$campoPesquisado;
                }
                break;
            }
        }
        return $retorno;
    }

    function retornarCampoPorPeriodo($gerenciador_periodo, $periodo, $campo){
        $retorno="";
        foreach ($gerenciador_periodo as $ger) {
           if($ger["periodoGerenciador"]==$periodo){
                 $retorno= $ger[$campo];
                break;
           }
        }
        return $retorno;
    }


    function retornarHora($gerenciador_periodo, $periodo, $tempo){

            $duracaoPorTempo = retornarCampoPorPeriodo($gerenciador_periodo, $periodo, "duracaoPorTempo");

            $duracaoIntervalo = retornarCampoPorPeriodo($gerenciador_periodo, $periodo, "duracaoIntervalo");

            $tempoEntrada = explode(":", retornarCampoPorPeriodo($gerenciador_periodo, $periodo, "horaEntrada"));

            $horaEntrada = intval(isset($tempoEntrada[0])?$tempoEntrada[0]:0);
            $minutoEntrada = intval(isset($tempoEntrada[1])?$tempoEntrada[1]:0);

            $parteA=0;
            if($tempo==1){
                $parteA = mktime($horaEntrada, $minutoEntrada, 0, 0,0,0);
            }else{
                if($tempo<=retornarCampoPorPeriodo($gerenciador_periodo, $periodo, "intevaloDepoisDoTempo")){
                    $parteA = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*($tempo-1)), 0, 0,0,0);
                }else{
                    $parteA = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*($tempo-1) + $duracaoIntervalo), 0, 0,0,0);
                }
            }
            

            $parteB=0;
            if($tempo<=retornarCampoPorPeriodo($gerenciador_periodo, $periodo, "intevaloDepoisDoTempo")){
                $parteB = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*$tempo), 0, 0,0,0);
            }else{
                $parteB = mktime($horaEntrada, ($minutoEntrada+$duracaoPorTempo*$tempo + $duracaoIntervalo), 0, 0,0,0);
            }
            return date("H:i", $parteA)."-".date("H:i", $parteB);
        }

?>