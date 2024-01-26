<?php session_start();

    if(!isset($_SESSION['tipoUsuario'])){
      echo "<script>window.location='../../'</script>";
    }else if($_SESSION["tipoUsuario"]=="aluno" || $_SESSION["tipoUsuario"]=="professor"){
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaEscolas/funcoesAuxiliares.php';
      includar("", "areaEscolas");
    }else{
      include_once $_SERVER['DOCUMENT_ROOT'].'/angoschool/areaAdministrador/funcoesAuxiliares.php';
      includar("../../", "areaAdministrador");
    } 
    echo "<script>var caminhoRecuar='../../'</script>";
    $manipulacaoDados = new manipulacaoDados();
    $includarHtmls = new includarHtmls();
    $janelaMensagens = new janelaMensagens();
    $conexaoFolhas = new conexaoFolhas();
    $verificacaoAcesso = new verificacaoAcesso();
    $layouts = new layouts();
    $manipulacaoDados->conDb("entretenimento", true);
 ?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <?php $conexaoFolhas->folhasCss();?>
  
</head>

<body>
  <?php
    $janelaMensagens->processar (); 
    if($_SESSION["tipoUsuario"]=="aluno"){
      $layouts->idPArea=1;
      $layouts->designacaoArea="Área do Aluno";
      $layouts->cabecalho();
      $layouts->aside();
    }else if($_SESSION["tipoUsuario"]=="professor"){
      $layouts->idPArea=2;
      $layouts->designacaoArea="Área do Professor";
      $layouts->cabecalho();
      $layouts->aside();
    }else{
      $layouts->idPArea=11;
      $layouts->designacaoArea="Gestão de Empresa";
      $layouts->cabecalho(11);
      $layouts->aside(11);
    }
    echo "<script>var enderecoArquivos='".$manipulacaoDados->enderecoArquivos."'</script>";
  ?>

  <section id="main-content"> 
        <section class="wrapper" id="containers">

          
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <nav role="navigation" class="navbar navbar-inverse paternSubMenuInterno" > 

                <a data-toggle="dropdown" class="dropdown-toggle navbar-brand chamMenuInterno"   href="#">
                    <b class="caret"></b>
                </a>
                <h1 class="lead navbar-brand" style="color: white;"><strong><i class="fa fa-history"></i> Histórico</strong></h1>
            </nav>
          </div>
      </div>

        <div class="main-body">
            <div class="card" style="min-height:700px;">
               <div class="card-body">
                  <div class="row">
                    <div class="col-lg-3 col-md-3 lead">
                      <label>Data</label>
                      <select class="form-control" id="dataSeleccionada">
                        <?php 
                        $tipoUsuario="aluno";
                        if($_SESSION['tipoUsuario']!="aluno"){
                            $tipoUsuario="entidade";
                        }
                        foreach($manipulacaoDados->selectDistinct("historial_jogador", "dataQuestao", ["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario], [], "", [], ["idPHistorial"=>-1]) as $a){
                          if($a["_id"]!=""){
                            echo "<option value='".$a["_id"]."'>".converterData($a["_id"])."<option>";
                          }
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                  <?php 
                    $dataSeleccionada = isset($_GET['dataSeleccionada'])?$_GET['dataSeleccionada']:$manipulacaoDados->selectUmElemento("historial_jogador", "dataQuestao", ["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario], [], [], ["idPHistorial"=>-1]);
                    echo "<script>var dataSeleccionada='".$dataSeleccionada."'</script>";
                    echo "<script>var historialJogador=".$manipulacaoDados->selectJson("historial_jogador", [], ["idJogador"=>$_SESSION['idUsuarioLogado'], "tipoJogador"=>$tipoUsuario, "dataQuestao"=>$dataSeleccionada, "resultado"=>array('$nin'=>["Ajuda", "Saltar"])], [], "", [], ["idPHistorial"=>-1])."</script>";
                  ?>


                <table id="example1" class="table table-striped table-bordered table-hover" >
                    <thead class="corPrimary">
                      <tr>
                        <th class="lead text-center"><strong><i class='fa fa-sort-numeric-down'></i> Nº</strong></th>
                        <th class="lead"><strong>Questão</strong></th>
                        <th class="lead text-center"><strong>Hora</strong></th> 
                        <th class="lead text-center"><strong>Resultado</strong></th>
                        <th class="lead text-center"><strong>Pontuação</strong></th>                     
                      </tr>
                    </thead>
                    <tbody id="tabela">
                    </tbody>
                </table>

               </div>
            </div>
         </div><br>
        <?php $includarHtmls->rodape(); ?>
      </section>
  </section>
</body>
</html>
<?php $conexaoFolhas->folhasJs(); $janelaMensagens->funcoesDaJanelaJs(); $includarHtmls->formTrocarSenha(); ?>

<script type="text/javascript">

   $(document).ready(function(){
      directorio = "areaEntretenimento/livros44/";
      fecharJanelaEspera();
      seAbrirMenu()
      $("#dataSeleccionada").val(dataSeleccionada)
      fazerPesquisa();
      DataTables("#example1", "sim") 

      $("#dataSeleccionada").change(function(){
        window.location='?dataSeleccionada='+$(this).val()
      })
   })

   function fazerPesquisa(){
      var i=0;
      var tbody=""
      historialJogador.forEach(function(dado){
        i++
        tbody +="<tr><td class='lead text-center'>"
        +completarNumero(i)+"</td><td class='lead'>"
        +dado.questao+"</td><td class='lead text-center'>"
        +dado.horaQuestao+"</td><td class='lead text-center'>"
        +dado.resultado+"</td><td class='lead text-center'>"
        +vazioNull(dado.pontuacao)+"</td></tr>";           
      });
      $("#tabela").html(tbody);
  }


</script>
