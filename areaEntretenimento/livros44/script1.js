var idPCurso = "";
window.onload=function(){
    fecharJanelaEspera(); 
    seAbrirMenu();
    directorio = "areaEntretenimento/livros44/";
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
      $("#formularioCategoria #action").val("salvarLivro")
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
          idPLivro = $(this).attr("idPLivro")
          $("#formularioCategoria #action").val($(this).attr("action"))
          $("#formularioCategoria #idPLivro").val($(this).attr("idPLivro"))
          mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir esta disciplina?");
          
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
    livraria.forEach(function(dado){
      i++
      tbody +="<tr><td class='lead text-center'>"
      +completarNumero(i)+"</td><td class='lead'>"+dado.tituloLivro
      +"</td><td class='lead'>"+dado.autoresLivro
      +"</td><td class='lead'>"+vazioNull(dado.subCategoria)
      +"</td><td class='lead'>"+dado.publicador
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-danger' title='Excluir' href='#a' action='excluirLivro' idPLivro='"+dado.idPLivro
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
    });
  $("#tabela").html(tbody);
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
          livraria = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioCategoriaForm"));
   enviarComPost(form);
}



