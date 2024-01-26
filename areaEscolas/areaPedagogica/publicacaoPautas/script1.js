var estado="";
var mesApartirPublicado=0;
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaPedagogica/publicacaoPautas/";

    fazerPesquisa();

    var repet=true;
    $("#example1").bind("click mouseenter", function(){
      repet=true;
      $("#example1 tr td input, #example1 tr td select").bind("change", function(){
          if(repet==true){

            var referencia = $(this).attr("referencia")
            if($("#example1 tr td input[referencia="+referencia+"]").prop("checked")==true){
              estado="sim";
            }else{
              estado="nao";
            }
            mesApartirPublicado = $("#example1 tr td select[referencia="+referencia+"]").val()

            alterarEstadoTrimestre(referencia);
            repet=false;
          }
      })  
    })
}

function fazerPesquisa(){

    var tbody = "";
    listaTurma.forEach(function(dado){
        var trimestre1 =retornarCheck(dado.idPListaTurma+"-1", dado.trimestrePublicado, 1, dado.mesApartirPublicado);
        var trimestre2 =retornarCheck(dado.idPListaTurma+"-2", dado.trimestrePublicado, 2, dado.mesApartirPublicado);
        var trimestre3 =retornarCheck(dado.idPListaTurma+"-3", dado.trimestrePublicado, 3, dado.mesApartirPublicado);
        var trimestreFinal =retornarCheck(dado.idPListaTurma+"-4", dado.trimestrePublicado, 4, dado.mesApartirPublicado);
       
        
        var terraMoto = "-"+classeExtensa(dado.classe, dado.sePorSemestre, "sim")
        if(dado.classe<=9){
          terraMoto=classeExtensa(dado.classe, dado.sePorSemestre, "sim") 
        }
      tbody +="<tr><td class='lead'>"+vazioNull(dado.abrevCurso)
      +terraMoto+"-"+dado.designacaoTurma
      +"</td><td class='lead text-center'>"+trimestre1+"</td><td class='lead text-center'>"
      +trimestre2+"</td><td class='lead text-center'>"
      +trimestre3+"</td><td class='lead text-center'>"
      +trimestreFinal+"</td></tr>";           
    });
    $("#tabela").html(tbody)
}

function alterarEstadoTrimestre(inputAlterar){
  chamarJanelaEspera("...");
  http.onreadystatechange = function(){
    if(http.readyState==4){
      estadoExecucao="ja";
      fecharJanelaEspera();
      resultado = http.responseText.trim();
      if(resultado.substring(0,1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else if(http.responseText.trim()!=""){
          listaTurma = JSON.parse(resultado);
          fazerPesquisa();
      }
    }
  }
  enviarComGet("tipoAcesso=aletrarEstados&input="+inputAlterar
    +"&estado="+estado+"&mesApartirPublicado="+mesApartirPublicado);
}

function retornarCheck(idCheck, trimestrePublicado, valorPretendido, mesPublicado){
  var labelTrimestre = "<small>"+valorPretendido+"</small>";
  if(valorPretendido==1){
    labelTrimestre = "<small>Iº Trimestre</small>"
  }else if(valorPretendido==2){
    labelTrimestre = "<small>IIº Trimestre</small>"
  }else if(valorPretendido==3){
    labelTrimestre = "<small>IIIº Trimestre</small>"
  }else if(valorPretendido==4){
    labelTrimestre = "<small>Pauta Final</small>"
  }

  var retorno="";
  if(valorPretendido<=trimestrePublicado){
    retorno = labelTrimestre+' <div class="switch">'+
      '<label class="lead">'+
      '<input type="checkbox" checked style="margin-left: -15px;"'+
      ' referencia="'+idCheck+'" class="altEstado">'+
            '<span class="lever"></span>'+
        '</label></div>'+listarMeses(mesPublicado, idCheck);
  }else{      
      retorno =labelTrimestre+' <div class="switch">'+
      '<label class="lead">'+
      '<input type="checkbox" style="margin-left: -15px;"'+
      ' referencia="'+idCheck+'" class="altEstado">'+
            '<span class="lever"></span>'+
        '</label></div>'+listarMeses(mesPublicado, idCheck);
  }
  return retorno;
}


function listarMeses(mesPublicado, idCheck){
  var htmlRetorno="<select referencia='"+idCheck+"'><option value='0'>Para todos</option>";
  
  mesesAnoLectivo.forEach(function(dado){
    if(dado==mesPublicado){
      htmlRetorno +="<option value='"+dado+"' selected>"+retornarMesExtensa(dado)+"</option>";
    }else{
      htmlRetorno +="<option value='"+dado+"'>"+retornarMesExtensa(dado)+"</option>";
    }
  })

  return htmlRetorno+"</select>";
}