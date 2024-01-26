var areasMarcadas= new Array()
var areasDesmarcadas = new Array()
window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/areasPorInstituicao66/";
    fazerPesquisa();

    $("#btnAlterar").click(function(){
      areasMarcadas = new Array();
      $("#tabela tr").each(function(){
        var id = $(this).attr("idPArea");

        if($("#tabela tr[idPArea="+id+"] input.estadoArea").prop("checked")==true){

          var listaAcessos="";
          $("#tabela tr[idPArea="+id+"] .acessos input").each(function(){
            if($(this).prop("checked")==true){

              listaAcessos +=","

              listaAcessos +=$(this).attr("idPCargo")
            }
          })
          areasMarcadas.push({idPArea:id, acessos:listaAcessos})
        }

      })
      luzinguLuame()
    })
}

function fazerPesquisa(){
  var tbody="";
  var i=0;
  listaAreas.forEach(function(dado){
    i++;
    var checkedEstado="";
    var listaAcessos=""
    if(dado.instituicoes!=undefined && dado.instituicoes!=""){
      dado.instituicoes.forEach(function(escola){
        if(escola.idEscola==idPEscola){
          checkedEstado="checked";
          listaAcessos = escola.acessos
        }
      })
    }
    tbody +="<tr idPArea='"+dado.idPArea+"'><td class='lead'><label><input class='estadoArea' type='checkbox' idPArea='"+dado.idPArea
    +"' "+checkedEstado+"> "+dado.designacaoArea+"</label></td><td class='lead acessos'>"

    var labelF=""
    listaCargos.forEach(function(cargo){
      var setimoDia=""
      if (typeof listaAcessos === 'string'){
        var mbenzaPedro = listaAcessos.split(",")
        for(var i=0; i<=mbenzaPedro.length; i++){
          if(mbenzaPedro[i]==cargo.idPCargo){
            setimoDia="checked"
          }
        }
      }
      tbody +="<label><input  idPCargo='"+
      cargo.idPCargo+"' "+setimoDia+" type='checkbox'> "+cargo.designacaoCargo+"</label>&nbsp;&nbsp;&nbsp;"
    })

    tbody +="</td></tr>";
  })
  $("#tabela").html(tbody)
}

function luzinguLuame(){
  chamarJanelaEspera("...")
  enviarComGet("tipoAcesso=luzinguLuame&areasMarcadas="
    +JSON.stringify(areasMarcadas));
  http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera()
      resultado = http.responseText.trim();
      if(resultado.trim().substring(0, 1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        mensagensRespostas("#mensagemCerta", "alteração efectuada com sucesso.");
        listaAreas = JSON.parse(resultado)
        fazerPesquisa();
      }
    }
  }
}
