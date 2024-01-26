    var idPListaTurma="";
    var classe="";
    var idPCurso="";

    window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      fecharJanelaEspera()
      
        directorio = "areaPedagogica/conselhoNotas/";
        $("#luzingu").val(luzingu);

        listarGerenciador();
        
        $("#formularioGerTurmas #formIdPCurso").val(idPCurso)
        $("#formularioGerTurmas #formClasse").val(classe);

        $("#luzingu").change(function(){
          window.location ="?luzingu="+$("#luzingu").val();
        })

        var repet=true;
        $("#tabTurmas").bind("click mouseenter", function(){
            repet=true;
            $("#tabTurmas .alteracao a").click(function(){
                if(repet==true){                    
                  idPListaTurma = $(this).attr("idPListaTurma");

                  $("#idPListaTurma").val(idPListaTurma);
                  porValoresFormulario();
                  repet=false;
                }
                
            });
        });

        $("#formularioGerTurmasF").submit(function(){
          if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao";
            manipularGerenciadorTurma();
          }
          return false;
        });
    }

    function listarGerenciador(){
      var html=""
       gerenciadorTurmas.forEach(function(dado){ 


        html +="<tr><td class='lead text-center'>"+dado.nomeTurma+" ("+
        dado.designacaoTurma+")</td><td class='lead text-center'>"+converterData(dado.dataConselhoNotas)
        +"</td><td class='lead text-center'>"+
         completarNumero(vazioNull(dado.horaConselhoNotas))+"</td><td class='lead text-center'>"+vazioNull(dado.salaReunidoConselho)
        +"</td><td class='lead toolTipeImagem' imagem='"
        +dado.fotoEntidade+"' title='"+dado.numeroInternoEntidade+"'><a href='"+caminhoRecuar+"areaSecretaria/relatorioFuncionario?idPFuncionario="
        +dado.idPEntidade+"' class='black'>"+vazioNull(dado.nomeEntidade)+
        "</a></td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn' href='#' idPListaTurma='"
        +dado.idPListaTurma+"' ><i class='fa fa-check-circle fa-2x'></i></a></div></td></tr>";
      });
      $("#tabTurmas").html(html);
    }

    function porValoresFormulario(){
        gerenciadorTurmas.forEach(function(dado){
          if(dado.idPListaTurma==idPListaTurma){
              $("#formularioGerTurmas #dataConselhoNotas").val(dado.dataConselhoNotas);
              $("#formularioGerTurmas #horaConselhoNotas").val(dado.horaConselhoNotas);
              $("#formularioGerTurmas #salaReunidoConselho").val(dado.salaReunidoConselho);
              $("#formularioGerTurmas #idPresidenteConselho").val(dado.idPresidenteConselho);
              $("#formularioGerTurmas").modal("show");
          }
        })
    }

    function manipularGerenciadorTurma(){
      $("#formularioGerTurmas").modal("hide");
      chamarJanelaEspera("")
      var form = new FormData(document.getElementById("formularioGerTurmasF"));
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();              
          estadoExecucao="ja";
          resultado = http.responseText.trim()
          if(resultado.substring(0,1)=="F") {
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));                                                               
          }else{
            gerenciadorTurmas = JSON.parse(resultado)
            mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso.");
            listarGerenciador();
          }
        }    
      }
      enviarComPost(form);
    }
