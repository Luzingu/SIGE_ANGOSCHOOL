window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaSecretaria/divisaoTeritMunicipios/";

    accoes("#tabela", "#formularioMunicipio", "Municipio", "Tens certeza que pretendes excluir este municipio?");
    

    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#formularioMunicipioForm").submit(function(){
      if(estadoExecucao=="ja" && validarFormularios("#formularioMunicipioForm")==true){
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
  listaMunicipios.forEach(function(dado){
    contagem++;

    tbody +="<tr><td class='lead text-center'>"
    +completarNumero(contagem)+"</td><td class='lead'>"+dado.nomeMunicipio
    +"</td><td class='lead text-center'>"+dado.preposicaoMunicipio
    +"</td><td class='lead text-center'>"+dado.preposicaoMunicipio2
    +"</td><td class='lead text-center'>"+"<a href='../divisaoTeritComunas/index.php?idPMunicipio="+dado.idPMunicipio
    +"'><i class='fa fa-link'></i></a>"
    +"</td><td class='text-center'><div class='btn-group alteracao text-right'>"+
    "<a class='btn btn-success' title='Editar' href='#as' action='editarMunicipio' idPrincipal='"+dado.idPMunicipio
    +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirMunicipio' idPrincipal='"+dado.idPMunicipio
    +"'><i class='fa fa-times'></i></a></div></td></tr>";
         
  });
  $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    listaMunicipios.forEach(function(dado){
      if(dado.idPMunicipio==idPrincipal){
        $("#formularioMunicipio #nomeMunicipio").val(dado.nomeMunicipio);
        $("#formularioMunicipio #preposicaoMunicipio").val(dado.preposicaoMunicipio)
        $("#formularioMunicipio #preposicaoMunicipio2").val(dado.preposicaoMunicipio2)
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
          listaMunicipios = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioMunicipioForm"));
   enviarComPost(form);
}