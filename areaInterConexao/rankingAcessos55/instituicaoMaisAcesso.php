<?php session_start();  
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Instituição com mais acesso.");
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $layouts->idPArea=7;
    $layouts->designacaoArea="Inter Conexão";
 ?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
</head>

<body>
  <?php
    $janelaMensagens->processar(); 
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-star"></i> Instituições com Mais Acessos</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(11, ["qualquerAcesso"], array(), "msg")){
          $topAcessos = isset($_GET["topAcessos"])?$_GET["topAcessos"]:10;
          echo "<script>var topAcessos='".$topAcessos."'</script>";

         ?>
          <div class="row">
            <form id="formulario">
              <div class="col-md-2 col-lg-2 lead">
                <label>De</label>
                <select class="form-control lead" id="idDataInicial">
                    <?php 
                        $idDataInicial="";

                        $datasEntradas = $manipulacaoDados->selectDistinct("entidadesonline", "dataEntrada", [], ["limit"=>60]);

                        $i=0;
                        foreach ($datasEntradas as $data) {
                          $i++;
                          if($i==1){ 
                            $idDataInicial = $manipulacaoDados->selectUmElemento("entidadesonline", "idPOnline", ["dataEntrada"=>$data], "idPOnline ASC");
                          } 
                          echo "<option value='".$manipulacaoDados->selectUmElemento("entidadesonline", "idPOnline", ["dataEntrada"=>$data], "idPOnline ASC")."'>".converterData($data)."</option>";
                        }
                        if(isset($_GET["idDataInicial"])){
                          $idDataInicial = $_GET["idDataInicial"];
                        }
                        echo "<script>var idDataInicial='".$idDataInicial."'</script>";
                    ?>
                </select>
              </div>
                <div class="col-md-2 col-lg-2 lead">
                  <label>Até:</label>
                  <select class="form-control lead" id="idDataFinal">
                      <?php 
                          $idDataFinal="";
                          $i=0;
                          foreach ($datasEntradas as $data) {
                            $i++;
                            if($i==count($datasEntradas)){ 
                              $idDataFinal = $manipulacaoDados->selectUmElemento("entidadesonline", "idPOnline", ["dataEntrada"=>$data], "idPOnline ASC");
                            }
                            echo "<option value='".$manipulacaoDados->selectUmElemento("entidadesonline", "idPOnline", ["dataEntrada"=>$data], "idPOnline ASC")."'>".converterData($data)."</option>";
                          }
                          if(isset($_GET["idDataFinal"])){
                            $idDataFinal = $_GET["idDataFinal"];
                          }
                          echo "<script>var idDataFinal='".$idDataFinal."'</script>";
                      ?>
                  </select>
                </div>
                <div class="col-md-2 col-lg-2"><br>
                  <input type="number" min="0" id="topAcesso" class="form-control lead text-center" value="<?php echo $topAcessos; ?>">
                </div>
                <div class="col-md-2 col-lg-2"><br>
                  <button type="submit" class="btn btn-primary lead"><i class="fa fa-search"></i> Pesquisar</button>
                </div>
              </form>
          </div>
          <?php

             $entidadesOnline = $manipulacaoDados->selectArray("entidadesonline", ["idPOnline"=>array('$gte'=>$idDataInicial ), "idPOnline"=>array('$lte'=>$idDataFinal)]);
            $entidadesOnline = $manipulacaoDados->anexarTabela($entidadesOnline, "escolas", "idPEscola", "idOnlineEntEscola");

             
            $arrayFinal=array();
            foreach ($manipulacaoDados->selectDistinct("entidadesonline", "idOnlineEntEscola", ["idPOnline"=>array('$gte'=>$idDataInicial ), "idPOnline"=>array('$lte'=>$idDataFinal)]) as $acesso) {
      
              $arrayFinal[] = totalDeTempo($entidadesOnline, $acesso);                                             
            }
            
            //Ordenando os acessos...
            usort($arrayFinal, "ordenarLUZL");

            $arrayFinalComOrdenacao=array();
            //Posicionandos os elementos do array
            $i=0;
            foreach ($arrayFinal as $ar) {
              $i++;
              $ar["ordem"]=($i);
              if($i<=$topAcessos){
                $arrayFinalComOrdenacao[] = array('escola'=>$ar["escola"], "numeroInterno"=>$ar["numeroInterno"], "Dias"=>$ar["Dias"], "Horas"=>$ar["Horas"], "Minutos"=>$ar["Minutos"], "Segundos"=>$ar["Segundos"], "totalSegundos"=>$ar["totalSegundos"], "ordem"=>$i);
              }else{
                break;
              }
            } 

            echo "<script>var lista=".json_encode($arrayFinalComOrdenacao)."</script>"       

           ?>   

            <div class="row">
              <div class="col-md-12">
                <input type="text" name="" placeholder="Pesquisar..." class="lead form-control" id="pesUsuarioOnline">
              </div>
            </div>   

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong>Nº</strong></th>
                        <th class="lead" style="width: 350px;"><strong>Escola</strong></th>
                        <th class="lead text-center"><strong>Número Interno</strong></th>
                        <th class="lead text-center"><strong>Segundos</strong></th>
                        <th class="lead"><strong>Duração</strong></th>                        
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>
            </div>
            <div class="row" id="paraPaginacao" style="margin-top: -30px;">
                <div class="col-md-12 col-lg-12 coluna">
                    <div class="form-group paginacao">
                          
                    </div>
                </div>
            </div>
        <?php } echo "</div>"; $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha();

  function totalDeTempo($entidadesOnline, $idOnlineEntEscola=""){

      $totalDia=0;
      $totalHora=0;
      $totalMinuto=0;
      $totalSegundo=0;

      
      $numeroInterno ="";
      $escola="";
    foreach ($entidadesOnline as $entidade) {

      if($entidade->idOnlineEntEscola==$idOnlineEntEscola){

          $escola = $entidade["nomeEscola"];
          $numeroInterno = $entidade->numeroInternoEscola;
          

          $dataEnter = explode("-", $entidade["dataEntrada"]);
          $horaEnter = explode(":", $entidade["horaEntrada"]);

          $dataTempoEntrada = new DateTime(date("Y-m-d H:i:s", mktime($horaEnter[0], $horaEnter[1], $horaEnter[2], $dataEnter[1], $dataEnter[2], $dataEnter[0])));

          $dataSaida = explode("-", $entidade["dataSaida"]);
          $horaSaida = explode(":", $entidade["horaSaida"]);

          $dataTempoSaida = new DateTime(date("Y-m-d H:i:s", mktime($horaSaida[0], $horaSaida[1], $horaSaida[2], $dataSaida[1], $dataSaida[2], $dataSaida[0])));


          $intervalo = $dataTempoSaida->diff($dataTempoEntrada);

          //$dataFinalDiferenca = date("Y-m-d h:i:s", mktime("18", $intervalo->i, $intervalo->s, $intervalo->m, "12", "2020"));
          $totalDia +=$intervalo->d;
          $totalHora +=$intervalo->h;
          $totalMinuto +=$intervalo->i;
          $totalSegundo +=$intervalo->s;
      }
    }
    $varRetorno = explode("-", segundosParaMinuto($totalSegundo));
    $totalSegundo = $varRetorno[1];
    $totalMinuto += $varRetorno[0];

    $varRetorno = explode("-", minutoParaHora($totalMinuto));
    $totalMinuto = $varRetorno[1];
    $totalHora += $varRetorno[0];

    $varRetorno = explode("-", horaParaDia($totalHora));
    $totalHora = $varRetorno[1];
    $totalDia += $varRetorno[0];

    $arayRetorno =array('escola'=>$escola, "numeroInterno"=>$numeroInterno, "Dias"=>$totalDia, "Horas"=>$totalHora, "Minutos"=>$totalMinuto, "Segundos"=>$totalSegundo, "totalSegundos"=>totalSegundos($totalDia, $totalHora, $totalMinuto, $totalSegundo));

    return $arayRetorno;
  }

  function segundosParaMinuto($segundos){
    $totalMinutos = explode(".", ((int) $segundos/60))[0];
    if($segundos==0){
      $totalSegundo=0;  
    }else{
      $totalSegundo = $segundos%60;
    }
    return $totalMinutos."-".$totalSegundo;
  }

  function minutoParaHora($minutos){
    $totalHoras = explode(".", ((int) $minutos/60))[0];

    if($minutos==0){
      $totalMinutos=0;  
    }else{
      $totalMinutos = $minutos%60;
    }
    return $totalHoras."-".$totalMinutos;
  }

  function horaParaDia($hora){
    $totalDias = explode(".", ((int) $hora/24))[0];

    if($hora==0){
      $totalHoras=0;  
    }else{
      $totalHoras = $hora%24;
    }
    return $totalDias."-".$totalHoras;
  }

  function totalSegundos($totalDia, $totalHora, $totalMinuto, $totalSegundo){

    return ($totalDia*3600*24+$totalHora*3600+$totalMinuto*60+$totalSegundo);
  }

  function ordenarLUZL($a, $b){
    return $a["totalSegundos"]<$b["totalSegundos"];
  }

  function primeiroIdDaData($entidadesOnline, $data){
    $retorno="";
    foreach ($entidadesOnline as $ent) {
      if($ent["dataEntrada"]==$data){
          $retorno = $ent["idPOnline"];
          break;
      }
    }
    return $retorno;
  }

  function ultimoIdDaData($entidadesOnline, $data){
    $retorno="";
    foreach ($entidadesOnline as $ent) {
      if($ent["dataEntrada"]==$data){
          $retorno = $ent["idPOnline"];          
      }
    }
    return $retorno;
  }




 ?>
 <script type="text/javascript">
   var idPEntidade="";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="professores";
    directorio = "areaInterconexao/rankingAcessos/";
    $("#idDataInicial").val(idDataInicial);
    $("#idDataFinal").val(idDataFinal)

    fazerPesquisa();

    $("#formulario").submit(function(){
      window.location ='?idDataFinal='+$("#idDataInicial").val()+'&idDataFinal='+$("#idDataFinal").val()
      +"&topAcessos="+$("#topAcesso").val();
      return false;
    })

    $("#pesUsuarioOnline").keyup(function(){
      fazerPesquisa();
    })


}


function fazerPesquisa(){
  var html="";

  if(jaTemPaginacao==false){
      paginacao.baraPaginacao(lista.filter(condition).length, 150);
  }else{
      jaTemPaginacao=false;
  }
    var contagem=-1;     

  lista.filter(condition).forEach(function(dado){
     contagem++;
    if(contagem>=paginacao.comeco && contagem<=paginacao.final){
      html +="<tr><td class='lead text-center'>"+completarNumero(dado.ordem)
      +"</td><td class='lead'>"+dado.escola
      +"</td><td class='lead'>"+dado.numeroInterno
      +"</td><td class='lead'>"+converterNumerosTresEmTres(dado.totalSegundos)+"</td><td class='lead'>"+dado.Dias+"d "+dado.Horas+"h "+
      dado.Minutos+"m "+dado.Segundos+"s"
      +"</td></tr>"
    }
  })
  $("#tabela").html(html);
}

function condition(elem, ind, arr){
  return (elem.escola.toLowerCase().indexOf($("#pesUsuarioOnline").val().toLowerCase())>=0
    );
}
 </script>
