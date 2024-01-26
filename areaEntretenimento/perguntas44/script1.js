var idPCurso = "";
window.onload=function(){
    fecharJanelaEspera(); 
    seAbrirMenu();
    directorio = "areaEntretenimento/perguntas44/";

    fazerPesquisa()

    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#tipoResposta").change(function(){
      joaquim()
    })
    $("#novaCategoria").click(function(){
      $("#formularioQuestao #tipoResposta").removeAttr("disabled")
      $("#formularioQuestao #action").val("salvarQuestao")
      $("#formularioQuestao .vazio").val("")
      joaquim()

      $("#formularioQuestao").modal("show")
    })

    $("#formularioQuestao form").submit(function(){
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
          idPQuestao = $(this).attr("idPQuestao")
          $("#formularioQuestao #action").val($(this).attr("action"))
          $("#formularioQuestao #idPQuestao").val($(this).attr("idPQuestao"))
          if($(this).attr("action")=="editarQuestao"){
            porValoresNoFormulario()
            $("#formularioQuestao").modal("show")
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
    listaPerguntas.forEach(function(dado){
      i++

      tbody +="<tr><td class='text-center'>"
      +completarNumero(i)+"</td><td><strong>"+dado.questao+"<span class='text-success'> ["+vazioNull(dado.pontuacao)+"]"
      +"</span></strong></td>";
      if(dado.tipoResposta=="Texto"){
        tbody +="<td>"+dado.resposta1
        +"</td><td>"+dado.resposta2
        +"</td><td>"+dado.resposta3
        +"</td><td>"+dado.resposta4
        +"</td>";

         tbody +="<td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarQuestao' idPQuestao='"+dado.idPQuestao
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' title='Excluir' href='#a' action='excluirQuestao' idPQuestao='"+dado.idPQuestao
      +"'><i class='fa fa-times'></i></a></div></td></tr>"
      }else{
        tbody +="<td class='lead text-center'><img src='../../../livraria/"+dado.resposta1+"'>"+
        "</td><td class='lead text-center'><img src='../../../livraria/"+dado.resposta2+"'></td><td></td><td></td>";
        tbody +="<td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-danger' title='Excluir' href='#a' action='excluirQuestao' idPQuestao='"+dado.idPQuestao
      +"'><i class='fa fa-times'></i></a></div></td></tr>"
      }
      
    });
  $("#tabela").html(tbody);
}


function porValoresNoFormulario(){
  listaPerguntas.forEach(function(dado){
    if(dado.idPQuestao==idPQuestao){
      $("#formularioQuestao #questao").val(dado.questao);
      $("#formularioQuestao #tipoResposta").val(dado.tipoResposta);
      $("#formularioQuestao #tipoResposta").attr("disabled", "");
      $("#formularioQuestao #resposta1").val(dado.resposta1);
      $("#formularioQuestao #resposta2").val(dado.resposta2);
      $("#formularioQuestao #resposta3").val(dado.resposta3);
      $("#formularioQuestao #resposta4").val(dado.resposta4);
      $("#formularioQuestao #pontuacao").val(dado.pontuacao);
      joaquim()
    }
  })
}
function manipular(){
    $("#formularioQuestao").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaPerguntas = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioQuestaoForm"));
   enviarComPost(form);
}


function joaquim(){
  if($("#tipoResposta").val()=="Imagem"){
    $("#formularioQuestao .respImagem").show();
    $("#formularioQuestao .respImagem input").attr("required", "");

    $("#formularioQuestao .respTexto").hide();
    $("#formularioQuestao .respTexto input").removeAttr("required");

  }else{

    $("#formularioQuestao .respTexto").show();
    $("#formularioQuestao .respTexto input").attr("required", "");
    
    $("#formularioQuestao .respImagem").hide();
    $("#formularioQuestao .respImagem input").removeAttr("required");
  }
}

