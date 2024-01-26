    var idPTransferencia="";
    var action="";

    window.onload = function(){
        fecharJanelaEspera();
        seAbrirMenu();
        entidade ="alunos";
        directorio = "areaSecretaria/transferenciasPendentes/";
        fazerPesquisa();

        $("#periodoAluno").change(function(){
          paraTurno();
        })
        DataTables("#example1", "sim")
        $("#anosLectivos").change(function(){
          window.location ="?idPAno="+$("#anosLectivos").val();
        });

        var repet=true;
        $("#tabTransferencia").bind("click mouseenter", function(){
            repet=true;
            $("#tabTransferencia a.detalhes").click(function(){
                if(repet==true){
                  idPTransferencia = $(this).attr("idPTransferencia");                  
                  alunosTransferidos.forEach(function(dado){
                    if(dado.transferencia.idPTransferencia==idPTransferencia){
                        $("#detalhesTransferencia #nomeAluno").text(dado.nomeAluno);
                        $("#detalhesTransferencia #numeroInterno").text(dado.numeroInterno);
                        $("#detalhesTransferencia #turmaAluno").text(dado.transferencia.turmaTransferencia);
                        $("#detalhesTransferencia #numeroAluno").text(dado.numeroAlunoTransferencia);
                        $("#detalhesTransferencia #escolaOrigem").text(dado.nomeEscola);
                        $("#detalhesTransferencia #provinciaOrigem").text(dado.nomeProvincia);
                        $("#detalhesTransferencia #municipioOrigem").text(dado.nomeMunicipio);
                        $("#detalhesTransferencia #dataTransferencia").text(retornDataExtensa(dado.dataTransferencia));
                        $("#detalhesTransferencia #funcionarioTransferencia").text(dado.nomeEntidade);

                    }
                  })
                  $("#detalhesTransferencia").modal("show");
                  repet=false;
                }
            })

            $("#tabTransferencia a.accionar").click(function(){
                if(repet==true){
                  idPTransferencia = $(this).attr("idPTransferencia");
                  idPMatricula = $(this).attr("idPMatricula");
                  $("#formulario #idPTransferencia").val(idPTransferencia)
                  $("#formulario #idPMatricula").val(idPMatricula)
                  
                  if($(this).attr("action")=="aceitar"){
                    alunosTransferidos.forEach(function(dado){
                      if(dado.transferencia.idPTransferencia==idPTransferencia){
                          $("#formulario #nomeAluno").val(dado.nomeAluno);
                      }
                      
                      $("#formulario #lingEspecialidade").val("")
                      $("#formulario #discEspecialidade").val("")
                    })
                    $("#formulario #numeroProcesso").val("")
                    $("#formulario #periodoAluno").val("")
                    $("#formulario #action").val("aceitarTransferencia")
                    $("#formulario").modal("show")
                  }else{
                    $("#formulario #action").val("rejeitarTransferencia")
                    mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes rejeitar esta transferência?");
                  }
                  repet=false;
                }
            });
        })

         var rep=true;
        $("body").bind("mouseenter click", function(){
              rep=true;
            $("#janelaPergunta #pergSim").click(function(){
              if(rep==true){
                if(estadoExecucao=="ja"){
                  fecharJanelaToastPergunta();
                  estadoExecucao="espera";
                  manipularTransferencia();
                }
                rep=false;
              }         
          })
        })

        $("#formularioForm").submit(function(){
          manipularTransferencia();
          return false;
        }) 


    }
    
    function fazerPesquisa(){
        var contagem=-1;
        var html ="";
        $(".numTAlunos").text(completarNumero(alunosTransferidos.length));

         alunosTransferidos.forEach(function(dado){
            contagem++;
            html += "<tr><td class='lead text-center'>"+completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
            +"</td><td class='lead text-center'>"+dado.numeroInterno
            +"</td><td class='lead'>"+dado.nomeEscola
            +"</td><td class='lead text-center'>"+converterData(dado.transferencia.dataTransferencia)
            +"</td><td class='lead text-center'><a href='#' idPTransferencia='"+dado.transferencia.idPTransferencia+"' class='detalhes'><i class='fa fa-info-circle'></i> Detalhes</a>"
            +"</td><td class='text-center'><a href='#' title='Rejeitar' class='lead text-center text-danger accionar' action='anular' idPTransferencia='"
            +dado.transferencia.idPTransferencia+"' idPMatricula='"
            +dado.idPMatricula+"'><i class='fa fa-times'></i></a>&nbsp;&nbsp;&nbsp;<a href='#' title='Aceitar' action='aceitar' class='lead text-center text-success accionar' idPTransferencia='"
            +dado.transferencia.idPTransferencia+"' idPMatricula='"
            +dado.idPMatricula+"'><i class='fa fa-sign-out-alt'></i></a></td></tr>";
                    
         });
      $("#tabTransferencia").html(html);
    };

    function manipularTransferencia(){
      $("#formulario").modal("hide");
      chamarJanelaEspera("...");
     http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          estadoExecucao="ja";
          if(resultado.substring(0, 1)=="F"){
          $("#formulario").modal("show");
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }else{               
            mensagensRespostas('#mensagemCerta', "Acção concluída com sucesso.");
            alunosTransferidos = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
      var form = new FormData(document.getElementById("formularioForm"));
      enviarComPost(form);
    }
    function paraTurno(){

      if(criterioEscolhaTurno=="opcional"){
        $("#turnoAluno").empty();
        if($("#periodoAluno").val()=="reg"){
          $("#turnoAluno").append("<option>Matinal</option>");
          $("#turnoAluno").append("<option>Vespertino</option>");
        }else{
          $("#turnoAluno").append("<option>Noturno</option>");
        }
      }else{
        $("#turnoAluno").append("<option>Automático</option>");
      }
    }


    
    