var idPAno = "";
window.onload=function(){

    seAbrirMenu();
    fecharJanelaEspera();

    directorio = "areaGestaoEscolas/anosLectivos00/";

    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#btnAdicionarAno").click(function(){
      $("#formularioAnoLectivo #nomeDisciplina").val("");
      $("#formularioAnoLectivo #action").val("adicionarAnoLectivo");
      $("#formularioAnoLectivo").modal("show");
    })

    $("#formularioAnoLectivoForm").submit(function(){
      if(estadoExecucao=="ja"){
          manipular();
      }
      return false;
    });

    var repet=true;
    $("#tabela").bind("mouseenter click", function(){
      repet=true;
      $("#tabela tr td a").click(function(){
        if(repet==true){
          var action =$(this).attr("action")
          idPAno = $(this).attr("idPrincipal");
          $("#formularioAnoLectivo #action").val(action)
          $("#formularioAnoLectivo #idPAno").val(idPAno)
          if(action=="editarAnoLectivo"){
            anolectivo.forEach(function(dado){
                if(dado.idPAno==idPAno){
                  $("#formularioAnoLectivo #numAno").val(dado.numAno)
                  $("#formularioAnoLectivo #estado").val(dado.estado)
                }
            })
            $("#formularioAnoLectivo").modal("show")
          }else if(action="excluirAnoLectivo"){
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir ano?");
          }

          repet=false;
        }
      })
    })



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

  var tbody = "";
  var i=0
  anolectivo.forEach(function(dado){
    i++;
    tbody +="<tr><td class='lead text-center'>"
    +dado.idPAno+"</td><td class='lead text-center'>"
    +dado.numAno+"</td><td class='lead text-center'>"+dado.estado+
    "</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' "+
    "href='#as' action='editarAnoLectivo' idPrincipal='"+dado.idPAno
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' "+
    "href='#a' action='excluirAnoLectivo' idPrincipal='"+dado.idPAno
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
  });
  $("#tabela").html(tbody)
}


function manipular(){
  chamarJanelaEspera("...")
  estadoExecucao="espera";
  $("#formularioAnoLectivo").modal("hide")
   var form = new FormData(document.getElementById("formularioAnoLectivoForm"));
   enviarComPost(form);
   http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        estadoExecucao="ja";
        resultado = http.responseText.trim();
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
          anolectivo = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
}
