var idPCurso = "";
window.onload=function(){
    fecharJanelaEspera(); 
    seAbrirMenu();
    directorio = "areaEntretenimento/subCategorias44/";
    $("#idPCategoria").val(idPCategoria)
    fazerPesquisa()

    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#idPCategoria").change(function(){
      window.location='?idPCategoria='+$(this).val()
    })

    $("#novaCategoria").click(function(){
      $("#formularioSubCategoria #action").val("salvarSubCategoria")
      limparFormulario("#formularioSubCategoria")
      $("#formularioSubCategoria").modal("show")
    })

    $("#formularioSubCategoria form").submit(function(){
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
          idPSubCategoria = $(this).attr("idPSubCategoria")
          $("#formularioSubCategoria #action").val($(this).attr("action"))
          $("#formularioSubCategoria #idPSubCategoria").val($(this).attr("idPSubCategoria"))
          if($(this).attr("action")=="editarSubCategoria"){
            porValoresNoFormulario()
            $("#formularioSubCategoria").modal("show")
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
    listaSubCategorias.forEach(function(dado){
      i++
      tbody +="<tr><td class='lead text-center'>"
      +completarNumero(i)+"</td><td class='lead'>"+dado.nomeCategoria
      +"</td><td class='lead'>"+dado.subCategoria.nomeSubCategoria
      +"</td><td class='lead'>"+dado.subCategoria.autor
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarSubCategoria' idPSubCategoria='"+dado.subCategoria.idPSubCategoria
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' title='Excluir' href='#a' action='excluirSubCategoria' idPSubCategoria='"+dado.subCategoria.idPSubCategoria
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
    });
  $("#tabela").html(tbody);
}


function porValoresNoFormulario(){
  listaSubCategorias.forEach(function(dado){
    if(dado.subCategoria.idPSubCategoria==idPSubCategoria){
      $("#formularioSubCategoria #subCategoriaLivro").val(dado.subCategoria.nomeSubCategoria);
    }
  })
}
function manipular(){
    $("#formularioSubCategoria").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaSubCategorias = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioSubCategoriaForm"));
   enviarComPost(form);
}



