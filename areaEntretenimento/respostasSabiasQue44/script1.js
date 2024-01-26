var idPCurso = "";
window.onload=function(){
    fecharJanelaEspera(); 
    seAbrirMenu();
    directorio = "areaEntretenimento/respostasSabiasQue44/";

    fazerPesquisa()

    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#novaCategoria").click(function(){
      $("#formularioSabiasQue #action").val("salvarSabiasQue")
      $("#formularioSabiasQue .vazio").val("")
      $("#formularioSabiasQue").modal("show")
    })

    $("#formularioSabiasQue form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          manipular();
      }
      return false;
    });


    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPSabiasQue = $(this).attr("idPSabiasQue")
          $("#formularioSabiasQue #action").val($(this).attr("action"))
          $("#formularioSabiasQue #idPSabiasQue").val($(this).attr("idPSabiasQue"))
          if($(this).attr("action")=="editarSabiasQue"){
            porValoresNoFormulario()
            $("#formularioSabiasQue").modal("show")
          }else{
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir esta disciplina?");
          }
          repet=false
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
    sabiasQue.forEach(function(dado){
      i++
      tbody +="<tr><td class='text-center'>"
      +completarNumero(i)+"</td><td>"+dado.resposta
      +"</td><td class='text-center'>"
      if(dado.arquivo!=""){
        tbody +="<img src='../../../livraria/"+dado.arquivo+"'>";
      }
      tbody +="</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarSabiasQue' idPSabiasQue='"+dado.idPSabiasQue
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' title='Excluir' href='#a' action='excluirSabiasQue' idPSabiasQue='"+dado.idPSabiasQue
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
    });
  $("#tabela").html(tbody);
}


function porValoresNoFormulario(){
  sabiasQue.forEach(function(dado){
    if(dado.idPSabiasQue==idPSabiasQue){
      $("#formularioSabiasQue #resposta").val(dado.resposta);
    }
  })
}
function manipular(){
    $("#formularioSabiasQue").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          sabiasQue  = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioSabiasQueForm"));
   enviarComPost(form);
}