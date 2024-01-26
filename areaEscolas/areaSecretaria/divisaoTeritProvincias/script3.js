window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaSecretaria/divisaoTeritProvincias/";

    accoes("#tabela", "#formularioProvincia", "Provincia", "Tens certeza que pretendes excluir esta província?");
    
    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#formularioProvinciaForm").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioProvinciaForm")==true){
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
  listaProvincias.forEach(function(dado){
    contagem++;

    tbody +="<tr><td class='lead text-center'>"
    +completarNumero(contagem)+"</td><td class='lead'>"+dado.nomeProvincia
    +"</td><td class='lead text-center'>"+dado.preposicaoProvincia
    +"</td><td class='lead text-center'>"+dado.preposicaoProvincia2
    +"</td><td class='lead text-center'>"+"<a href='../divisaoTeritMunicipios/index.php?idPProvincia="+dado.idPProvincia
    +"'><i class='fa fa-link'></i></a>"
    +"</td><td class='text-center'><div class='btn-group alteracao text-right'>"+
    "<a class='btn btn-success' title='Editar' href='#as' action='editarProvincia' idPrincipal='"+dado.idPProvincia
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirProvincia' idPrincipal='"+dado.idPProvincia
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
         
  });
  $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    listaProvincias.forEach(function(dado){
      if(dado.idPProvincia==idPrincipal){
        $("#formularioProvincia #nomeProvincia").val(dado.nomeProvincia);
        $("#formularioProvincia #preposicaoProvincia").val(dado.preposicaoProvincia)
        $("#formularioProvincia #preposicaoProvincia2").val(dado.preposicaoProvincia2)
      }
   });
}


function manipular(){
    $(".modal").modal("hide");
   chamarJanelaEspera();
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        fecharJanelaEspera();
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaProvincias = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioProvinciaForm"));
   enviarComPost(form);
}