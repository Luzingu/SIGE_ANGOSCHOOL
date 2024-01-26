
window.onload = function (){
  
  fecharJanelaEspera();
  seAbrirMenu();
  fazerPesquisa()

  directorio = "areaGestaoEmpresa/controlSaidas/";
  $("#anoCivil").val(anoCivil)
  $("#mesPagamento").val(mesPagamento)

  $("#anoCivil, #mesPagamento").change(function(){
    window.location ='?anoCivil='+$("#anoCivil").val()
    +"&mesPagamento="+$("#mesPagamento").val()
  })

  $("#novaSaida").click(function(){
    $("#formularioSaida #action").val("novaSaida")
    $("#formularioSaida .vazio").val()
    $("#formularioSaida").modal("show")
  });

  $("#formularioNovaSaidaForm").submit(function(){
    manipular();
    return false;
  });

  var repet=true;
  $("#tabDados").bind("click mouseenter", function(){
      repet=true;
      $("#tabDados a.btnCancelar").click(function(){
        if(repet==true){
            $("#formularioSaida #action").val("excluirSaida");
            $("#formularioSaida #idPSaida").val($(this).attr("idPSaida"));
            mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes excluir a saída?");
          repet=false;
        }
      });
  });


  var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            if(estadoExecucao=="ja"){
              estadoExecucao="espera";
              manipular();
            }
            rep=false;
          }         
      })
    })

}

function fazerPesquisa(){
  var html="";
  var totValores=0
  listaSaidas.forEach(function(dado){
    totValores += dado.valor
      html +="<tr><td>"+dado.dataSaida+"<br>"+dado.horaSaida
      +"</td><td>"+dado.nomeFuncionario+"</td><td>"
      +dado.descricaoSaida+"</td><td>"
      +converterNumerosTresEmTres(dado.valor)+"</td><td>";
      if(dado.factura !="" && dado.factura !=null && dado.factura !=undefined)
      {
        html +="<a href='../../../Ficheiros/Escola_7/Facturas/"+dado.factura
        +"'><i class='fa fa-print'></i></a>"
      }
      html += "</td><td class='text-center'>"+
      "<a href='#' class='text-danger btnCancelar' idPSaida='"+dado.idPSaida
      +"'><i class='fa fa-times'></i></a></td></tr>";
  }); 
  $("#totValores").text(converterNumerosTresEmTres(totValores))
  $("#tabDados").html(html);
}

function manipular(){
  $("#formularioSaida").modal("hide");
  chamarJanelaEspera(".."); 
   var form = new FormData(document.getElementById("formularioNovaSaidaForm"));
   enviarComPost(form);
   http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        estadoExecucao="ja";
        resultado = http.responseText.trim()
        if(resultado.substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
            listaSaidas = JSON.parse(resultado);
            fazerPesquisa();
        }
      }
  }
}