var idPCurso = "";
window.onload=function(){

    seAbrirMenu();
    fecharJanelaEspera();

    directorio = "areaGestaoEscolas/disciplinas00/";

    accoes("#tabela", "#formularioDisciplinas", "Disciplina", "Tens certeza que pretendes excluir esta disciplina?");
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#formularioDisciplinas form").submit(function(){
      if(estadoExecucao=="ja"){
          idEspera = "#formularioDisciplinas form #Cadastar";
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

  $("#numTDisciplinas").text(disciplinas.length);

    var tbody = "";
    var i=0
    disciplinas.forEach(function(dado){
      i++;
      tbody +="<tr><td class='lead text-center'>"
      +completarNumero(i)+"</td><td class='lead'>"+dado.nomeDisciplina
      +" ("+dado.idPNomeDisciplina+")</td><td class='lead'>"+vazioNull(dado.abreviacaoDisciplina1)
      +"</td><td class='lead'>"+vazioNull(dado.abreviacaoDisciplina2)
      +"</td><td class='lead'>"+dado.nivelDisciplina
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarDisciplina' posicaoNoArray='"
      +dado.chave+"' idPrincipal='"+dado.idPNomeDisciplina
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirDisciplina' posicaoNoArray='"
      +dado.chave+"' idPrincipal='"+dado.idPNomeDisciplina
      +"'><i class='fa fa-times'></i></a></div></td></tr>";
           
    });
    $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    disciplinas.forEach(function(dado){
      if(dado.idPNomeDisciplina==idPrincipal){
        $("#formularioDisciplinas #nomeDisciplina").val(dado.nomeDisciplina);
        $("#formularioDisciplinas #abreviacaoDisciplina1").val(dado.abreviacaoDisciplina1);
        $("#formularioDisciplinas #abreviacaoDisciplina2").val(dado.abreviacaoDisciplina2);

        $("#formularioDisciplinas #nivelDisciplina").val(dado.nivelDisciplina);
        $("#formularioDisciplinas #ordemDisciplina").val(dado.ordemDisciplina);
        $("#formularioDisciplinas #atributoDisciplina").val(dado.atributoDisciplina);
      }
   });
}


function manipular(){
   var form = new FormData(document.getElementById("formularioDisciplinasForm"));
   enviarComPost(form);
   $(idEspera).focus();
    $(idEspera).html('<i class="fa fa-spinner fa-spin"></i>');
   http.onreadystatechange = function(){
      if(http.readyState<4){
                  
      }else{
        estadoExecucao="ja";
        $(".modal").modal("hide");
        resultado = http.responseText.trim();
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
          disciplinas = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
}