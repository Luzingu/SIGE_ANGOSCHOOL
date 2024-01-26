window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    entidade ="Anexos";
    directorio = "areaSecretaria/divisaoTeritPaises/";

    accoes("#tabela", "#formularioPais", "Pais", "Tens certeza que pretendes excluir este país?");
    
    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#formularioPaisForm").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioPaisForm")==true){
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
  listaPaises.forEach(function(dado){
    contagem++;

    tbody +="<tr><td class='lead text-center'>"
    +completarNumero(contagem)+"</td><td class='lead'>"+dado.nomePais
    +"</td><td class='lead'>"+dado.continentePais
    +"</td><td class='lead text-center'>"+dado.preposicaoPais
    +"</td><td class='lead text-center'>"+dado.preposicaoPais2
    +"</td><td class='lead text-center'>"+"<a href='../divisaoTeritProvincias/index.php?idPPais="+dado.idPPais
    +"'><i class='fa fa-link'></i></a>"
    +"</td><td class='text-center'><div class='btn-group alteracao text-right'>"+
    "<a class='btn btn-success' title='Editar' href='#as' action='editarPais' idPrincipal='"+dado.idPPais
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirPais' idPrincipal='"+dado.idPPais
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
         
  });
  $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    listaPaises.forEach(function(dado){
      if(dado.idPPais==idPrincipal){
        $("#formularioPais #continentePais").val(dado.continentePais);
        $("#formularioPais #nomePais").val(dado.nomePais);
        $("#formularioPais #preposicaoPais").val(dado.preposicaoPais)
        $("#formularioPais #preposicaoPais2").val(dado.preposicaoPais2)
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
          listaPaises = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioPaisForm"));
   enviarComPost(form);
}