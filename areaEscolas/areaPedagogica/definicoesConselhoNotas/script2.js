
var estado="";
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaPedagogica/definicoesConselhoNotas/";
    definicoesConselhoNotas.forEach(function(dado){
      $("#negativasPorDeliberar").val(dado.negativasPorDeliberar)
      $("#notaMinimaPorDeliberar").val(dado.notaMinimaPorDeliberar)

      $("#exprParaAprovado").val(dado.exprParaAprovado)
      $("#exprParaAprovadoComDef").val(dado.exprParaAprovadoComDef)
      $("#exprParaAprovadoComRecurso").val(dado.exprParaAprovadoComRecurso)
      $("#exprParaNaoAprovado").val(dado.exprParaNaoAprovado)

      if(dado.mac=="V"){
        $("#mac").prop("checked", true)
      }
      if(dado.trimestre1=="V"){
        $("#trimestre1").prop("checked", true)
      }
      if(dado.trimestre2=="V"){
        $("#trimestre2").prop("checked", true)
      }
      if(dado.trimestre3=="V"){
        $("#trimestre3").prop("checked", true)
      }
      if(dado.exame=="V"){
        $("#exame").prop("checked", true)
      }
    })
    $("#definicoesConselhoNotas").submit(function(){
      manipular()
      return false;
    })
}


function manipular(){
  var form = new FormData(document.getElementById("definicoesConselhoNotas"))
  enviarComPost(form)
  chamarJanelaEspera("...")
  http.onreadystatechange = function(){
    if(http.readyState==4){
      estadoExecucao="ja";
      fecharJanelaEspera();
      resultado = http.responseText.trim()
      if(resultado.substring(0,1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");
      }
    }
  }
  
}

function retornarCheck(idCheck, peridoTrimestre, valorPretendido){
  var labelTrimestre = "<small>"+valorPretendido+"</small>";

  var retorno="";
  if(peridoTrimestre==valorPretendido || 
    (peridoTrimestre=="todos" && valorPretendido!="conselho")){
    retorno = labelTrimestre+' <div class="switch">'+
      '<label class="lead">'+
      '<input type="checkbox" checked style="margin-left: -15px;"'+
      ' id="'+idCheck+'" class="altEstado">'+
            '<span class="lever"></span>'+
        '</label></div>';
  }else{      
      retorno =labelTrimestre+' <div class="switch">'+
      '<label class="lead">'+
      '<input type="checkbox" style="margin-left: -15px;"'+
      ' id="'+idCheck+'" class="altEstado">'+
            '<span class="lever"></span>'+
        '</label></div>';
  }
  return retorno;
}