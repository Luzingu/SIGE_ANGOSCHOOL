window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/cargos66/";

    $("#btnNovoCargo").click(function(){
      limparFormulario("#formularioCargos")
      $("#formularioCargos #action").val("novoCargo")
      $("#formularioCargos").modal("show")
    })
    var repet=true;
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a.alterar").click(function(){
        if(repet==true){
          $("#formularioCargos #action").val($(this).attr("action"))
          idPCargo = $(this).attr("idPCargo");
          $("#formularioCargos #idPCargo").val(idPCargo)
          if($(this).attr("action")=="editarCargo"){

            listaCargos.forEach(function(dado){
              if(dado.idPCargo==idPCargo){
                $("#formularioCargos #designacaoCargo").val(dado.designacaoCargo)
                $("#formularioCargos #instituicao").val(dado.instituicao)
              }
            })
            $("#formularioCargos").modal("show")
          }else{
            mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar este cargo?");
          }
          repet=false
        }
      })
    })
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#formularioCargosForm").submit(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="espera";
        manipular();
      }
      return false;
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
  var tbody="";
  $("#numTCursos").text(completarNumero(listaCargos.length));
  var i=0;
  listaCargos.forEach(function(dado){
    i++;
    tbody +="<tr><td class='lead text-center'>"
    +dado.idPCargo+"</td><td class='lead'>"+dado.designacaoCargo
    +"</td><td class='lead'>"+dado.instituicao
    +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success alterar' title='Editar' href='#as' action='editarCargo' idPCargo='"+dado.idPCargo
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger alterar' n='' title='Excluir' href='#a' action='excluirCargo' idPCargo='"+dado.idPCargo
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
  })
  $("#tabela").html(tbody)
}

  function manipular(){
    $("#formularioCargos").modal("hide")
     var form = new FormData(document.getElementById("formularioCargosForm"));
     enviarComPost(form);
     chamarJanelaEspera("")
     http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim();
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
            mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
            listaCargos = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
  }
