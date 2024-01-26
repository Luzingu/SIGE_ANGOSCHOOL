
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/periodosCursos00/";
    $("#idPNomeCurso").val(idPNomeCurso)
    $("#idPNomeCurso").change(function(){
      window.location="?idPNomeCurso="+$(this).val()
    })
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#novoPeriodo").click(function(){
      limparFormulario("#formularioPeriodos")
      $("#formularioPeriodos #action").val("novoPeriodo")
      $("#formularioPeriodos").modal("show")
    })

    $("#formularioPeriodos form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          manipular();
      } 
        return false;
    })
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPPeriodo = $(this).attr("idPPeriodo")
          $("#formularioPeriodos #action").val($(this).attr("action"))
          $("#formularioPeriodos #idPPeriodo").val(idPPeriodo)

          if($(this).attr("action")=="excluirPeriodo"){
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir este período?");
          }else{
            listaPeriodos.forEach(function(dado){
              if(dado.idPPeriodo==idPPeriodo){
                $("#formularioPeriodos #ordem").val(dado.ordem);
                $("#formularioPeriodos #identificador").val(dado.identificador);
                $("#formularioPeriodos #designacao").val(dado.designacao);
                $("#formularioPeriodos #abreviacao1").val(dado.abreviacao1);

                $("#formularioPeriodos #abreviacao2").val(dado.abreviacao2)
              }
            })
            $("#formularioPeriodos").modal("show")
          }
        }
      })
    })
    
     var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
              if(estadoExecucao=="ja"){
                idEspera = "#janelaPergunta #pergSim";
                estadoExecucao="espera";
                manipular();
              }            
            rep=false;
          }         
      })
    })
}

function fazerPesquisa(){
  var tbody="";
  $("#numTCursos").text(completarNumero(listaPeriodos.length));
      var i=0;
    listaPeriodos.forEach(function(dado){
      i++;
      
      tbody +="<tr><td class='lead text-center'>"
      +vazioNull(dado.ordem)+"</td><td class='lead'>"+dado.identificador
      +"</td><td class='lead'>"+dado.designacao
      +"</td><td class='lead'>"+dado.abreviacao1
      +"</td><td class='lead'>"+dado.abreviacao2
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarPeriodo' idPPeriodo='"+dado.idPPeriodo
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirPeriodo' idPPeriodo='"+dado.idPPeriodo
      +"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}
  function manipular(){
    $("#formularioPeriodos").modal("hide")
      chamarJanelaEspera("...")
     var form = new FormData(document.getElementById("formularioPeriodosForm"));
     enviarComPost(form);

      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
              mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
              listaPeriodos = JSON.parse(resultado)
              fazerPesquisa();
          }
        }
      }
  }