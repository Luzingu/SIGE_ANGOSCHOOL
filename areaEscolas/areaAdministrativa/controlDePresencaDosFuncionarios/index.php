<?php session_start();

   include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("control de Presença do Professor", "controlDePresencaDosFuncionarios");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea = $manipulacaoDados->idPArea;
    $layouts->designacaoArea = $manipulacaoDados->designacaoArea;
    $manipulacaoDados->retornarAnosEmJavascript();
 ?> 
 
 <!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  <style type="text/css">
    #listaProfessor form{
      border-bottom:solid rgba(0, 0, 0, 0.2) 2px;
      padding-top: 10px;
      padding-bottom: 20px;
      padding-left: 50px;
      padding-right: 50px;
    }
    #paraPaginacao ul li a{
      height: 40px;
      font-size: 15pt;
      padding: 5px;
      padding-right: 10px;
      padding-left: 10px;
      font-weight: bolder;
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
                  <h1 class="lead navbar-brand tituloPrincipal" style="color: white;"><strong><i class="fa fa-user-check"></i> Control de Presença</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  

        if($verificacaoAcesso->verificarAcesso("", "controlDePresencaDosFuncionarios", array(), "msg")){

            $idPAno = isset($_GET["idPAno"])?$_GET["idPAno"]:$manipulacaoDados->idAnoActual;
            $numAno = $manipulacaoDados->selectUmElemento("anolectivo", "numAno", ["idPAno"=>$idPAno]);
            $mes = isset($_GET["mes"])?$_GET["mes"]:intval($manipulacaoDados->mes);
            $ordemSemana = isset($_GET["ordemSemana"])?$_GET["ordemSemana"]:1;

            $anoCivil = explode("/", $numAno)[0];
            if($mes>=1 && $mes<=8){
              $anoCivil++;
            }


            echo "<script>var idPAno='".$idPAno."'</script>";
            echo "<script>var mes='".$mes."'</script>";
            echo "<script>var ordemSemana='".$ordemSemana."'</script>";

            echo "<script>var idEscolaLogada='".$_SESSION['idEscolaLogada']."'</script>";
            echo "<script>var anoCivil='".$anoCivil."'</script>";
            echo "<script>var diasDasActividades='".valorArray($manipulacaoDados->sobreEscolaLogada, "diasDasActividades")."'</script>";
            echo "<script>var diasDosFeriados='".valorArray($manipulacaoDados->sobreEscolaLogada, "diasDosFeriados")."'</script>";

            
            $ultimoDia = date("t", strtotime($anoCivil."-".$mes."-01"));
            $datas = array();

            $contadorSemana=0;

            $contador=0;
            for($i=1; $i<=$ultimoDia; $i++){
              $data = date("Y-m-d", strtotime($anoCivil."-".$mes."-".completarNumero($i)));
              $semana = date("w", strtotime($anoCivil."-".$mes."-".completarNumero($i)));
              
              if($semana!=0){
                if($contador%7==0){
                  $contadorSemana++;
                }
                if($contadorSemana==$ordemSemana){
                  $datas[]=array("data"=>$data, "dataExtensa"=>converterData($data), "semana"=>diaSemana2($semana), "diaSemana"=>$semana, "dia"=>$i);
                }
                $contador++;             
              }
              
            }

            echo "<script>var datas=".json_encode($datas)."</script>";

            $horario = $manipulacaoDados->selectArray("horario", ["dia", "idPEntidade"], ["idPEscola"=>$_SESSION["idEscolaLogada"], "idHorAno"=>$idPAno]);
            
            echo "<script>var horario=".json_encode($horario)."</script>";
            echo "<script>var listaAgentes=".json_encode($manipulacaoDados->selectArray("entidadesprimaria", ["idPEntidade", "nomeEntidade", "numeroInternoEntidade", "fotoEntidade", "controlPresenca.data", "controlPresenca.faltas", "controlPresenca.presencas", "controlPresenca.idEscola", "contadorFaltas.idEscola", "contadorFaltas.mes", "contadorFaltas.anoCivil", "contadorFaltas.ausencias", "contadorFaltas.faltas"], ["escola.idEntidadeEscola"=>$_SESSION['idEscolaLogada'], "escola.estadoActividadeEntidade"=>"A", "escola.tipoPessoal"=>"docente"], ["escola"], "", [], ["nomeEntidade"=>1]))."</script>";
            
            
          ?>

      <div class="card">
      <div class="card-body">
        <div class="row" id="paraPaginacao">

          <div class="col-lg-2 col-md-2 lead">
            <label>Ano</label>
            <select class="form-control" id="anosLectivos">
              <?php 
                foreach($manipulacaoDados->anosLectivos as $ano){
                  if($ano->idPAno!=1 && $ano->idPAno!=842){                  
                    echo "<option value='".$ano["idPAno"]."'>".$ano["numAno"]."</option>";
                  }
                }

               ?>
            </select>
          </div>

          <div class="col-lg-3 col-md-3 lead">
            <label>Mês</label>
            <select class="form-control" id="mes">
              <?php 
                foreach($manipulacaoDados->mesesAnoLectivo as $a){
                  echo "<option value='".$a."'>".nomeMes($a)."</option>";
                }

               ?>
            </select>
          </div>
          <div class="col-lg-2 col-md-2 lead">
            <label>Semana</label>
            <select class="form-control" id="ordemSemana">
              <option value="1">1.ª Semana</option>
              <option value="2">2.ª Semana</option>
              <option value="3">3.ª Semana</option>
              <option value="4">4.ª Semana</option>
              <option value="5">5.ª Semana</option>
            </select>
          </div> 
      </div>
      <fieldset style="border:solid rgba(0, 0, 0, 0.1); padding: 10px; margin-bottom: 50px;">
        <legend>Relatório</legend>
        <div class="row">
          <div class="col-lg-1 col-md-1"><br>
             <select id="tamanhoFolha" class="form-control">
              <?php  
                echo "<option>A4</option>";
                echo "<option>A3</option>";
               ?>
            </select>
          </div>
          <div class="col-lg-1 col-md-1">
            De
            <select id="diaInicial" class="form-control">
              <?php 
                for ($i=1; $i<=$ultimoDia ; $i++) { 
                  echo "<option value='".$i."'>".$i."</option>";
                }
               ?>
            </select>
          </div>
          <div class="col-lg-1 col-md-1"><br>
             <select id="mesInicial" class="form-control">
              <?php  
                echo "<option value='".$mes."'>".$mes."</option>";
                if($mes==1){
                  echo "<option value='12'>12</option>";
                }else{
                  echo "<option value='".($mes-1)."'>".($mes-1)."</option>";
                }
               ?>
            </select>
          </div>
          <div class="col-lg-1 col-md-1">
            À
            <select id="diaFinal" class="form-control">
              <?php 
                for ($i=$ultimoDia; $i>=1 ; $i--) { 
                  echo "<option value='".$i."'>".$i."</option>";
                }
               ?>
            </select>
          </div>
          <div class="col-lg-1 col-md-1"><br>
             <select id="mesFinal" class="form-control">
              <?php  
                echo "<option value='".$mes."'>".$mes."</option>";
                if($mes==1){
                  echo "<option value='12'>12</option>";
                }else{
                  echo "<option value='".($mes-1)."'>".($mes-1)."</option>";
                }
               ?>
            </select>
          </div>
          <div class="col-md-6 col-lg-6"><br>
            <a arquivo="../../relatoriosPdf/mapasProfessores/mapaDeControlFaltas.php" class='btn-primary btn openLink'><i class="fa fa-print"></i> Mapa de Control de Faltas</a>&nbsp;&nbsp;&nbsp;
            <a arquivo="../../relatoriosPdf/mapasProfessores/mapaDeControlPresenca.php" class='btn-primary btn openLink'><i class="fa fa-print"></i> Mapa de Control de Presença</a>
          </div>
        </div>
      </fieldset>

      <div class="row">
        <div class="col-lg-12 col-md-12 text-right">
            <button class="btn btn-success lead btnAlterarNotas"><i class="fa fa-check"></i> Alterar</button>
          </div>
      </div>

      <table id="example1" class="table table-bordered table-striped">
        <thead class="">
          <tr>
            <th class="lead font-weight-bolder text-center"><strong>N.º</strong></th>
            <th class="lead "><strong>Funcionário</strong></th>
            <?php 
            foreach ($datas as $data){
              echo '<th class="lead text-center"><strong><strong style="font-size:9pt; border-radius:50%; padding:3px; font-weight:bolder;" class="bg bg-primary">'.$data["dia"].'</strong><br>'.$data["semana"].'</strong></th>';
            }

             ?>
          </tr>
        </thead>
        <tbody id="tabDados">
        </tbody>
      </table>
        <div class="row">
          <div class="col-lg-12 col-md-12 text-right">
              <button class="btn btn-success lead btnAlterarNotas"><i class="fa fa-check"></i> Alterar</button>
            </div>
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


<form id="formularioDados">
    <input type="hidden" name="action" id="action" value="">
    <input type="hidden" name="idPAno" id="idPAno" value="<?php echo $idPAno; ?>">
    <input type="hidden" name="mes" id="mes" value="<?php echo $mes; ?>">
    <input type="hidden" name="anoCivil" id="anoCivil" value="<?php echo $anoCivil; ?>">
    <input type="hidden" name="dados" id="dados">   
 </form>