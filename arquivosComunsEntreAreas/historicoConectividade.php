<?php function historicoConectividade($manipulador){ 
  $dataSaida = strtotime($manipulador->dataSistema.$manipulador->tempoSistema." - 600 seconds");
 ?>
  <div class="row" style="min-height:400px;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="row">
    <div class="col-md-2 col-lg-2 lead">
      Data de Entrada:
      <select class="form-control lead" id="dataExp">
      <?php 
          $dataExp="";
          $i=0;
          //, "estadoExpulsao"=>array('$ne'=>"A")
          foreach ($manipulador->selectDistinct("entidadesonline", "dataEntrada", ["idOnlineEntEscola"=>$_SESSION["idEscolaLogada"], "idUsuarioLogado"=>$_SESSION['idUsuarioLogado']], [], 7, [], ["idPOnline"=>-1]) as $data) {
            $i++;
            $dataExp = $data["_id"];
            echo "<option value='".$data["_id"]."'>".converterData($data["_id"])."</option>";
          }
          if(isset($_GET["dataEx"])){
            $dataExp = $_GET["dataEx"];
          }
          echo "<script>var dataExp='".$dataExp."'</script>";
      ?>
      </select>
    </div>
  </div>

  <?php 
    //, "estadoExpulsao"=>"I"
     echo "<script>var entidadesOnline=".$manipulador->selectJson("entidadesonline", [], ["idOnlineEntEscola"=>$_SESSION["idEscolaLogada"], "dataEntrada"=>$dataExp, "idUsuarioLogado"=>$_SESSION["idUsuarioLogado"]])."</script>";

   ?>
        <div class="table-responsive" >
        <table class="table table-striped table-bordered table-hover" >
            <thead class="corPrimary">
                  <tr>
              <th class="lead text-center"><strong>Entrou</strong></th>
              <th class="lead text-center"><strong>Saiu</strong></th>
              <th class="lead text-center"><strong>Actividades</strong></th>
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
    </div>
  </div>
  <script type="text/javascript">
	var idPEntidade="";
   
	$(document).ready(function(){

	    fecharJanelaEspera();
	    seAbrirMenu();

	    entidade ="professores";
	    directorio = "areaMista/historicoConectividade/";

	    $("#dataExp").val(dataExp);

	    fazerPesquisa();
	    $("#dataExp").change(function(){
	      window.location ='?dataEx='+$("#dataExp").val();
	    })
	})

	function fazerPesquisa(){
	      if(jaTemPaginacao==false){

	        $("#numTProfessores").text(completarNumero(entidadesOnline.length));

	        paginacao.baraPaginacao(entidadesOnline.length, 20);
	      }else{
	          jaTemPaginacao=false;
	      }
	    var i=paginacao.comeco;
	    var contagem=-1;
	    var html="";

	  entidadesOnline.forEach(function(dado){
	     contagem++;

	     if(contagem>=paginacao.comeco && contagem<=paginacao.final){
	        html +="<tr><td class='lead text-center'>"+
	        dado.horaEntrada+"</td><td class='lead text-center'>"+
	        dado.horaSaida+"</td><td class='lead' style='font-size:10pt;'>"+
	        vazioNull(dado.areasAcessadas)+"</td></tr>";
	      }
	  })
	  $("#tabela").html(html);
	}
  </script>
<?php } ?>