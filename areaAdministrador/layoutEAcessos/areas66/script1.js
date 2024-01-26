window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "layoutEAcessos/areas66/";

    $("#btnNovaArea").click(function(){
      limparFormulario("#formularioAreaEscolas")
      $("#formularioAreaEscolas #action").val("novaArea")
      $("#formularioAreaEscolas").modal("show")
    })
    var repet=true;
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          $("#formularioAreaEscolas #action").val($(this).attr("action"))
          idPArea = $(this).attr("idPArea");
          $("#formularioAreaEscolas #idPArea").val(idPArea)

          if($(this).attr("action")=="editarArea"){
            listaAreas.forEach(function(dado){
              if(dado.idPArea==idPArea){
                $("#formularioAreaEscolas #designacaoArea").val(dado.designacaoArea)
                $("#formularioAreaEscolas #instituicao").val(dado.instituicao)
                $("#formularioAreaEscolas #icone").val(dado.icone)
                $("#formularioAreaEscolas #ordenacao").val(dado.ordenacao)
                $("#formularioAreaEscolas #eGratuito").val(dado.eGratuito)

              }
            })
            $("#formularioAreaEscolas").modal("show")
          }else{
            mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar esta área?");
          }
          repet=false
        }
      })
    })
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#formularioAreaEscolasForm").submit(function(){
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
  $("#numTCursos").text(completarNumero(listaAreas.length));
      var i=0;
    listaAreas.forEach(function(dado){
      i++;
      tbody +="<tr><td class='lead text-center'>"+
      dado.ordenacao+"</td><td class='lead text-center'><i class='"+dado.icone
      +"'></i></td><td class='lead'>"+dado.designacaoArea
      +"</td><td class='lead'>"+dado.instituicao
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarArea' idPArea='"+dado.idPArea
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirArea' idPArea='"+dado.idPArea
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
    });
    $("#tabela").html(tbody)
}

  function manipular(){
    $("#formularioAreaEscolas").modal("hide")
     var form = new FormData(document.getElementById("formularioAreaEscolasForm"));
     enviarComPost(form);

     chamarJanelaEspera("")

     http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          fecharJanelaEspera();
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
            mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
            listaAreas = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
  }
