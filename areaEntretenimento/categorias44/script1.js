var idPCurso = "";
window.onload=function(){
    fecharJanelaEspera(); 
    seAbrirMenu();
    directorio = "areaEntretenimento/categorias44/";
    fazerPesquisa()
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');


    $("#novaCategoria").click(function(){
      $("#formularioCategoria #action").val("salvarCategoria")
      limparFormulario("#formularioCategoria")
      $("#formularioCategoria").modal("show")
    })

    $("#formularioCategoria form").submit(function(){
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
          idPCategoria = $(this).attr("idPCategoria")
          $("#formularioCategoria #action").val($(this).attr("action"))
          $("#formularioCategoria #idPCategoria").val($(this).attr("idPCategoria"))
          if($(this).attr("action")=="editarCategoria"){
            porValoresNoFormulario()
            $("#formularioCategoria").modal("show")
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
    listaCategorias.forEach(function(dado){
      i++
      tbody +="<tr><td class='lead text-center'>"
      +completarNumero(i)+"</td><td class='lead'>"+dado.nomeCategoria
      +"</td><td class='lead'>"+dado.autor
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarCategoria' idPCategoria='"+dado.idPCategoria
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' title='Excluir' href='#a' action='excluirCategoria' idPCategoria='"+dado.idPCategoria
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
    });
  $("#tabela").html(tbody);
}


function porValoresNoFormulario(){
  listaCategorias.forEach(function(dado){
    if(dado.idPCategoria==idPCategoria){
      $("#formularioCategoria #categoriaLivro").val(dado.nomeCategoria);
    }
  })
}
function manipular(){
    $("#formularioCategoria").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaCategorias = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioCategoriaForm"));
   enviarComPost(form);
}



