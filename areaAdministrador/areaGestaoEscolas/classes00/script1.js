
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/classes00/";

    $("#idPNomeCurso").val(idPNomeCurso)
    $("#idPNomeCurso").change(function(){
      window.location="?idPNomeCurso="+$(this).val()
    })
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#novaClasse").click(function(){
      limparFormulario("#formularioClasses")
      $("#formularioClasses #seComRecurso").prop("checked", false)
      $("#formularioClasses #action").val("novaClasse")
      $("#formularioClasses").modal("show")
    })

    $("#btnCopiar").click(function(){
      copiarDadosCurso();
    })

    $("#formularioClasses form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          var periodosUsar ="";
          $("#periodosUsar input").each(function(){
            if($(this).prop("checked")==true){
              if(periodosUsar!=""){
                periodosUsar +=","
              }
              periodosUsar +=$(this).attr("id")
            }
          })
          $("#formularioClasses #periodos").val(periodosUsar)

          manipular();
      } 
        return false;
    })
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPClasse = $(this).attr("idPClasse")
          $("#formularioClasses #action").val($(this).attr("action"))
          $("#formularioClasses #idPClasse").val(idPClasse)

          if($(this).attr("action")=="excluirClasse"){
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir esta classe?");
          }else{
            listaClasses.forEach(function(dado){
              if(dado.idPClasse==idPClasse){
                $("#formularioClasses #ordem").val(dado.ordem);
                $("#formularioClasses #identificador").val(dado.identificador);
                $("#formularioClasses #designacao").val(dado.designacao);
                $("#formularioClasses #abreviacao1").val(dado.abreviacao1);
                $("#formularioClasses #abreviacao2").val(dado.abreviacao2)
                $("#formularioClasses #notaMaxima").val(dado.notaMaxima)
                $("#formularioClasses #notaMedia").val(dado.notaMedia)
                $("#formularioClasses #notaMinima").val(dado.notaMinima)
                $("#formularioClasses #seComRecurso").prop("checked", false)
                if(dado.seComRecurso == "A")
                  $("#formularioClasses #seComRecurso").prop("checked", true)

                $("#periodosUsar input").prop("checked", false)
                if(dado.periodos!=undefined && dado.periodos!=null && dado.periodos!=""){

                  dado.periodos.split(",").forEach(function(anosL){
                    $("#periodosUsar input[id="+anosL.trim()+"]").prop("checked", true)
                  })
                }

              }
            })
            $("#formularioClasses").modal("show")
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
  $("#numTCursos").text(completarNumero(listaClasses.length));
      var i=0;
    listaClasses.forEach(function(dado){
      i++;
      
      tbody +="<tr><td class='lead text-center'>"
      +vazioNull(dado.ordem)+"</td><td class='lead'>"+dado.identificador
      +"</td><td class='lead'>"+dado.designacao
      +"</td><td class='lead'>"+dado.abreviacao1
      +"</td><td class='lead'>"+dado.abreviacao2
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarClasse' idPClasse='"+dado.idPClasse
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirClasse' idPClasse='"+dado.idPClasse
      +"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}
function manipular(){
  $("#formularioClasses").modal("hide")
    chamarJanelaEspera("...")
   var form = new FormData(document.getElementById("formularioClassesForm"));
   enviarComPost(form);

    http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
            mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
            listaClasses = JSON.parse(resultado)
            fazerPesquisa();
        }
      }
    }
}

function copiarDadosCurso(){
  chamarJanelaEspera("...")
  http.onreadystatechange = function(){
    if(http.readyState==4){
        resultado = http.responseText.trim()
        fecharJanelaEspera()
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Os dados foram copiados com sucesso.");
          listaClasses = JSON.parse(resultado)
          fazerPesquisa()
        }
    }     
  }
  enviarComGet("tipoAcesso=copiarDadosCurso&idCursoDestino="
    +idPNomeCurso+"&idCursoOrigem="+$("#copiarCurso").val());
}