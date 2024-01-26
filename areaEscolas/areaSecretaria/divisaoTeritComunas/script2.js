window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaSecretaria/divisaoTeritComunas/";
    accoes("#tabela", "#formularioComuna", "Comuna", "Tens certeza que pretendes excluir esta comuna?");
    
    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#formularioComunaForm").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioComunaForm")==true){
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
  var tbody = "";
  var contagem=0;
  listaComunas.forEach(function(dado){
    contagem++;

    tbody +="<tr><td class='lead text-center'>"
    +completarNumero(contagem)+"</td><td class='lead'>"+dado.nomeComuna
    +"</td><td class='lead text-center'>"+dado.preposicaoComuna
    +"</td><td class='lead text-center'>"+dado.preposicaoComuna2
    +"</td><td class='text-center'><div class='btn-group alteracao text-right'>"+
    "<a class='btn btn-success' title='Editar' href='#as' action='editarComuna' idPrincipal='"+dado.idPComuna
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirComuna' idPrincipal='"+dado.idPComuna
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
         
  });
  $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    listaComunas.forEach(function(dado){
      if(dado.idPComuna==idPrincipal){
        $("#formularioComuna #nomeComuna").val(dado.nomeComuna);
        $("#formularioComuna #preposicaoComuna").val(dado.preposicaoComuna)
        $("#formularioComuna #preposicaoComuna2").val(dado.preposicaoComuna2)
      }
   });
}


function manipular(){
    $(".modal").modal("hide");
   chamarJanelaEspera();
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim();
        estadoExecucao="ja";
        fecharJanelaEspera();
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaComunas = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioComunaForm"));
   enviarComPost(form);
}