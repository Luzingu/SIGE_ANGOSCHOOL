var idPSubsistema=0;
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/subSistemas00/";

    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#novoDado").click(function(){
      limparFormulario("#formularioSubSistema")
      $("#formularioSubSistema #action").val("novoSubSistema")
      $("#formularioSubSistema").modal("show")
    })

    $("#formularioSubSistema form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          manipular();
      } 
        return false;
    })
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPSubsistema = $(this).attr("idPSubsistema")
          $("#formularioSubSistema #action").val($(this).attr("action"))
          $("#formularioSubSistema #idPSubsistema").val(idPSubsistema)

          if($(this).attr("action")=="excluirSubsistema"){
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir este Subsistema?");
          }else{
            subsistemasDeEnsino.forEach(function(dado){
              if(dado.idPSubsistema==idPSubsistema){
                $("#formularioSubSistema #categroria").val(dado.categroria);
                $("#formularioSubSistema #ordem").val(dado.ordem);
                $("#formularioSubSistema #designacaoSubistema").val(dado.designacaoSubistema);
              }
            })
            $("#formularioSubSistema").modal("show")
          }
        }
      })
    })
    
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
  var tbody="";
  $("#numTCursos").text(completarNumero(subsistemasDeEnsino.length));
      var i=0;
    subsistemasDeEnsino.forEach(function(dado){

      tbody +="<tr><td class='lead text-center'>"
      +dado.categroria+"</td><td class='lead text-center'>"+dado.ordem
      +"</td><td class='lead'>"+dado.designacaoSubistema
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarSubSistema' idPSubsistema='"+dado.idPSubsistema
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirSubsistema' idPSubsistema='"+dado.idPSubsistema
      +"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}
  function manipular(){
    $("#formularioSubSistema").modal("hide")
      chamarJanelaEspera("...")
     var form = new FormData(document.getElementById("formularioSubSistemaForm"));
     enviarComPost(form);
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim();
          estadoExecucao="ja";
          if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          }else{
              mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
              subsistemasDeEnsino = JSON.parse(resultado)
              fazerPesquisa();
          }
        }
      }
  }