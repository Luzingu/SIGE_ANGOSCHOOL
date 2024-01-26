var idMatAno=0;
window.onload=function(){

    fecharJanelaEspera();
     seAbrirMenu();
    entidade ="alunos";
    directorio = "areaSecretaria/novaMatricula/";

    $("#luzingu").val(luzingu);
    fazerPesquisa();
    DataTables("#example1", "sim") 

    $("#luzingu").change(function(){
      window.location ="?luzingu="+$("#luzingu").val();    
    })

    var repet=true
    $("#tabela").bind("click mouseenter", function(){
        repet=true
        $("#tabela tr td a.alterar").click(function(){
            if(repet==true){
              $("#formularioMatricula #action").val($(this).attr("action"))
              $("#formularioMatricula #idPMatricula").val($(this).attr("idPrincipal"))
              if($(this).attr("action")=="excluirMatricula"){
                mensagensRespostas("#janelaPergunta", "Tens certeza que pretendes eliminar esta matricula?")
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
              fecharJanelaToastPergunta();
              manipular();
            }
            rep=false;
          }         
      })
    }) 
}

function fazerPesquisa(){
  $("#numTAlunos").text(0);
  $("#numTMasculinos").text(0);

  $("#numTAlunos").text(completarNumero(listaAlunos.length));
        $("#numTMasculinos").text(0);

    var tbody = "";
    var contagem=0;
    var numM=0;

    listaAlunos.forEach(function(dado){
      contagem++;
      if(dado.sexoAluno=="F"){
          numM++;
      }
      $("#numTMasculinos").text(completarNumero(numM));

     //retornarPeriodo(dado.periodoAluno)
      tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"
      +completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
      +"'>"+dado.nomeAluno+"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
      +"' class='lead black'>"+dado.numeroInterno
      +"</a></td><td class='lead text-center'>"+vazioNull(dado.telefoneAluno)
      +"</td><td class='lead text-center'>"
      +dado.estadoAcessoAluno+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-danger alterar' "+
      "title='Excluir' href='#a' action='excluirMatricula' idPrincipal='"+dado.idPMatricula
      +"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody);
}

function manipular(){
  $("#formularioMatricula").modal("hide")
  var form = new FormData(document.getElementById("formularioMatriculaF"));
  chamarJanelaEspera("")
  enviarComPost(form);
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      if(resultado.trim().substring(0, 1)=="V"){
          actualizarLista(resultado.substring(1, resultado.length));
      }else if(resultado.trim().substring(0, 1)=="F"){
        estadoExecucao="ja";
        fecharJanelaEspera()
        if(action=="editarMatricula"){ 
          $("#formularioMatricula").modal("show")
        }
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }
    }
  }
}


function actualizarLista(mensagem){
  enviarComGet("tipoAcesso=listarMatriculados&todos=sim&classe="+classeP
    +"&periodo="+periodo+"&idPCurso="+idCursoP);

  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      if(resultado.trim()!=""){
        listaAlunos = JSON.parse(resultado);
      }
      estadoExecucao="ja";
      fecharJanelaEspera()
      fazerPesquisa();
      mensagensRespostas("#mensagemCerta", mensagem );
    }
  }
}