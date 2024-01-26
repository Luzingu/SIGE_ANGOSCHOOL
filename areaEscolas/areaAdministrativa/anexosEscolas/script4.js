window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    entidade ="Anexos";
    directorio = "areaAdministrativa/anexosEscolas/";

    accoes("#tabela", "#formularioAnexos", "Anexo", "Tens certeza que pretendes excluir este anexo?");
    
    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#formularioAnexos form").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioAnexos form")==true){
          idEspera = "#formularioAnexos form #Cadastar";
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
  $("#numTAnexos").text(completarNumero(anexos.length));

    var tbody = "";
    var contagem=0;
    anexos.forEach(function(dado){
      contagem++;

      tbody +="<tr><td class='lead text-center'>"
      +dado.anexos.ordenacaoAnexo+"</td><td class='lead'>"+dado.anexos.identidadeAnexo+" ("+dado.anexos.idPAnexo+")"
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'>"+
      "<a class='btn btn-success' title='Editar' href='#as' action='editarAnexo' posicaoNoArray='"
      +dado.chave+"' idPrincipal='"+dado.anexos.idPAnexo
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirAnexo' posicaoNoArray='"
      +dado.chave+"' idPrincipal='"+dado.anexos.idPAnexo
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
           
    });
    $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    anexos.forEach(function(dado){
      if(dado.anexos.idPAnexo==idPrincipal){
        $("#formularioAnexos #idPAnexo").val(dado.anexos.idPAnexo);
        $("#formularioAnexos #identidadeAnexo").val(dado.anexos.identidadeAnexo);
        $("#formularioAnexos #ordenacaoAnexo").val(dado.anexos.ordenacaoAnexo);
      }
   });
}


function manipular(){
  
  $("#formularioAnexos").modal("hide")
      chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        fecharJanelaEspera()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          anexos = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioAnexosForm"));
   enviarComPost(form);
}