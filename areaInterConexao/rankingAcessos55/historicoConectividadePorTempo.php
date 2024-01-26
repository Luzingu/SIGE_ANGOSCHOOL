<?php session_start();
    include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaInterConexao/funcoesAuxiliares.php';
    echo "<script>var caminhoRecuar='../'</script>";
    includar("../../");
    $manipulacaoDados = new manipulacaoDados("Histórico de Conectividade por Tempo");
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
                  <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-star"></i> Histórico de Conectividade por Tempo</strong></h1>
              </nav>
            </div>
        </div>
        <div class="main-body">
        <?php  if($verificacaoAcesso->verificarAcesso(11, ["qualquerAcesso"], array(), "msg")){

         ?>
          <div class="row">
              <div class="col-md-2 col-lg-2">
                <select class="form-control lead" id="dataEntrada">
                    <?php 
                        $dataEntrada="";

                        $datasEntradas = $manipulacaoDados->selectDistinct("entidadesonline", "dataEntrada", [], [], 54, [], array("idPOnline"=>1));

                        $i=0;
                        foreach ($datasEntradas as $data) {
                          $i++;
                          $dataEntrada = $data["_id"];
                          echo "<option value='".$data["_id"]."'>".converterData($data["_id"])."</option>";
                        }
                        if(isset($_GET["dataEntrada"])){
                          $dataEntrada = $_GET["dataEntrada"];
                        }
                        echo "<script>var dataEntrada='".$dataEntrada."'</script>";
                    ?>
                </select>
              </div>
          </div>
          <?php

            $entidades = $manipulacaoDados->selectArray("entidadesonline", [], ["dataEntrada"=>$dataEntrada]);
            //Ordenando os acessos...

            $arrayFinalComOrdenacao=array();
            //Posicionandos os elementos do array
            $i=0;
            foreach ($entidades as $ar) {
              $i++;
              $ar["ordem"]=($i);
              if($ar["nomeUsuario"]!=null && $ar["nomeUsuario"]!=""){
                $arrayFinalComOrdenacao[] = array('escola'=>$ar["escola"], 'tipoUsuario'=>$ar["tipoUsuario"], "nomeUsuario"=>$ar["nomeUsuario"], "numeroInterno"=>$ar["numeroInterno"], "fotoUsuario"=>$ar["fotoUsuario"], "idUsuario"=>$ar["idUsuario"], "Dias"=>$ar["Dias"], "Horas"=>$ar["Horas"], "Minutos"=>$ar["Minutos"], "Segundos"=>$ar["Segundos"], "totalSegundos"=>$ar["totalSegundos"], "ordem"=>$i);              
              }
            }
            echo "<script>var lista=".json_encode($arrayFinalComOrdenacao)."</script>";


            $tempoTotal[] = totalDeTempo($entidades, "");


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
                        <th class="lead"><strong>Nome do Usuário</strong></th>
                        <th class="lead text-center"><strong>Número Interno</strong></th>
                        <th class="lead" style="width: 250px;"><strong>Escola</strong></th>
                        <th class="lead text-center"><strong>Tipo de Usuário</strong></th>
                        <th class="lead text-center"><strong>Segundos</strong></th>
                        <th class="lead"><strong>Duração</strong></th>                        
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>

                    <tfoot>
                       <tr class="text-danger">
                        <th class="lead text-center" colspan="5"><strong>Tempo Total</strong></th>
                        <th class="lead text-center"><strong><?php echo $tempoTotal[0]["totalSegundos"] ?></strong></th>
                        <th class="lead"><strong><?php echo $tempoTotal[0]["Dias"]."d ".$tempoTotal[0]["Horas"]."h ".$tempoTotal[0]["Minutos"]."m ".$tempoTotal[0]["Segundos"]."s "; ?></strong></th>                        
                      </tr>
                    </tfoot>
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

  function totalDeTempo($entidadesOnline, $idOnlineEnt="", $idOnlineMat=""){

      $totalDia=0;
      $totalHora=0;
      $totalMinuto=0;
      $totalSegundo=0;

      $nomeUsuario="";
      $tipoUsuario="";
      $fotoUsuario="";
      $idUsuario="";
      $numeroInterno ="";
      $escola="";
    foreach ($entidadesOnline as $entidade) {

      if(($idOnlineEnt!="" && $idOnlineMat=="" && nelson($entidade, "idOnlineEnt")==$idOnlineEnt) || ($idOnlineMat!="" && $idOnlineEnt=="" && nelson($entidade, "idOnlineMat")==$idOnlineMat) || ($idOnlineEnt=="" && $idOnlineMat=="")){

          $escola = $entidade["nomeEscola"];
          if($idOnlineEnt!=""){
            $nomeUsuario=$entidade["nomeEntidade"];
            $tipoUsuario="Entidade";
            $fotoUsuario=$entidade["fotoEntidade"];
            $idUsuario=$entidade->idPEntidade;
            $numeroInterno =$entidade["numeroInternoEntidade"];
          }else if($idOnlineMat!=""){
            $nomeUsuario=$entidade["nomeAluno"];
            $tipoUsuario="Aluno";
            $fotoUsuario=$entidade["fotoAluno"];
            $idUsuario=$entidade["idPMatricula"];
            $numeroInterno =$entidade["numeroInterno"];
          }

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
    $varRetorno = explode("-", segundosParaMinuto($totalSegundo));
    $totalSegundo = $varRetorno[1];
    $totalMinuto += $varRetorno[0];

    $varRetorno = explode("-", minutoParaHora($totalMinuto));
    $totalMinuto = $varRetorno[1];
    $totalHora += $varRetorno[0];

    $varRetorno = explode("-", horaParaDia($totalHora));
    $totalHora = $varRetorno[1];
    $totalDia += $varRetorno[0];

    $arayRetorno =array('escola'=>$escola, 'tipoUsuario'=>$tipoUsuario, "nomeUsuario"=>$nomeUsuario, "numeroInterno"=>$numeroInterno, "fotoUsuario"=>$fotoUsuario, "idUsuario"=>$idUsuario, "Dias"=>$totalDia, "Horas"=>$totalHora, "Minutos"=>$totalMinuto, "Segundos"=>$totalSegundo, "totalSegundos"=>totalSegundos($totalDia, $totalHora, $totalMinuto, $totalSegundo));

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

  function ordenarLuzl($a, $b){
    return $a["totalSegundos"]<$b["totalSegundos"];
  }



 ?>
<script type="text/javascript">
  var idPEntidade="";
  window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();
      entidade ="professores";
      directorio = "areaInterconexao/rankingAcessos/";
      $("#dataEntrada").val(dataEntrada);

      fazerPesquisa();

      $("#dataEntrada").change(function(){
        window.location ='?dataEntrada='+$(this).val();
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
        var link="";
        if(dado.tipoUsuario=="Aluno"){
          link ="<a href='"+caminhoRecuar+"../areaAdministrador/areaGestaoEscolas/perfilAluno?aWRQTWF0cmljdWxh="+dado.idUsuario+"' class='black'>";
        }else{
          link ="<a href='"+caminhoRecuar+"../areaAdministrador/areaGestaoEscolas/perfilEntidade?aWRQUHJvZmVzc29y="+dado.idUsuario+"' class='black'>";
        }
        html +="<tr><td class='lead text-center'>"+completarNumero(dado.ordem)
        +"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoUsuario+"'>"+dado.nomeUsuario
        +"</td><td class='lead'>"+link+dado.numeroInterno
        +"</a></td><td class='lead'>"+dado.escola
        +"</td><td class='lead text-center'>"+dado.tipoUsuario
        +"</td><td class='lead'>"+converterNumerosTresEmTres(dado.totalSegundos)+"</td><td class='lead'>"+dado.Horas+"h "+
        dado.Minutos+"m "+dado.Segundos+"s"
        +"</td></tr>"
      }
    })
    $("#tabela").html(html);
  }

  function condition(elem, ind, arr){
    return (elem.nomeUsuario.toLowerCase().indexOf($("#pesUsuarioOnline").val().toLowerCase())>=0
      );
  }
</script>