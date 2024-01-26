var estadoAlterar="";
var classeTrans=0;
var idPCursoTrans=0;
var tipoTransicao="";

    window.onload = function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaDirector/painelControl/";
      fazerPesquisaEstadoTrimestre();

        $(".altEstado").change(function(){
          estadoAlterar = $(this).attr("id");
          manipularEstadoPeriodico();
        });

        $("#btnAdicionarAnoLectivo").click(function(){
         mensagensRespostas('#janelaPergunta', "Ao Avançar ao próximo ano deve ter Certeza"
         +" de que não há mais alterações que podem ser feita neste ano. Porque não há possibilidade de retroceder no ano anterior. Tens certeza que pretendes continuar com esta accção?");
        })

        var rep=true;
        $("body").bind("mouseenter click", function(){
            rep=true;
            $("#janelaPergunta #pergSim").click(function(){
              if(rep==true){
                fecharJanelaToastPergunta();
                novoAnoLectivo()
                rep=false;
              }
          })
        })

        $(".tranistarAno").change(function(){
           if($(this).prop("checked")==true){
              classeTrans =$(this).attr("classe")
               idPCursoTrans = $(this).attr("idPNomeCurso")
               transitarClasseAluno();
           }else{
              $(this).prop("checked", true)
           }
        })

    }


      function transitarClasseAluno(){
        chamarJanelaEspera("Transitando os alunos, por favor aguarde...");
          http.onreadystatechange = function(){
              if(http.readyState==4){
                fecharJanelaEspera();
                resultado = http.responseText;
                window.location='';
              }
          }
          enviarComGet("tipoAcesso=transitarClasseAluno&classeTrans="+classeTrans
            +"&idPCursoTrans="+idPCursoTrans+"&tipoTransicao="+tipoTransicao);
      }


      function manipularEstadoPeriodico(){
        chamarJanelaEspera("Alterando, Por favor aguarde...");
          http.onreadystatechange = function(){
              if(http.readyState==4){
                fecharJanelaEspera();
                if(http.responseText.trim()!=""){
                  estadoPeriodico = JSON.parse(http.responseText);
                }
                fazerPesquisaEstadoTrimestre();
              }
          }
          enviarComGet("tipoAcesso=manipularEstadoPeriodico&estadoAlterar="+estadoAlterar);
      }

      function fazerPesquisaEstadoTrimestre(){
          estadoPeriodico.forEach(function(dado){
              if(dado.estadoperiodico.estado=="F"){
                  $("#"+dado.estadoperiodico.objecto).prop("checked", false);
              }else{
                  $("#"+dado.estadoperiodico.objecto).prop("checked", true);
              }
              if(dado.estadoperiodico.objecto=="tipoDocumento"){
                  $("#tipoDocumento").val(dado.estadoperiodico.estado);
              }
          })
      }




      function novoAnoLectivo(){
        $(".modal").modal("hide");
        chamarJanelaEspera("Adicionando novo ano, por favor aguarde...");
        http.onreadystatechange = function(){
            if(http.readyState==4){
                estadoExecucao="ja";
                fecharJanelaEspera();
                resultado = http.responseText.trim()
                if(resultado.trim().substring(0, 1)=="V"){
                  window.location='';
                }else{
                  mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
                }
            }
          }
          enviarComGet("tipoAcesso=novoAnoLectivo");
      }
