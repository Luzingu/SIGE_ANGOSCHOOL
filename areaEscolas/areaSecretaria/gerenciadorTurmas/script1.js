    var idPListaTurma="";
    var classe="";
    var idPCurso="";

    window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      fecharJanelaEspera()
      
        directorio = "areaSecretaria/gerenciadorTurmas/";
        $("#anosLectivos").val(idPAno);
        $("#luzingu").val(luzingu);

        listarGerenciador();
        
        $("#formularioGerTurmas #formIdPCurso").val(idPCurso)
        $("#formularioGerTurmas #formClasse").val(classe);

        $("#luzingu, #anosLectivos").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()
          +"&idPAno="+$("#anosLectivos").val();
        })

        var repet=true;
        $("#tabTurmas").bind("click mouseenter", function(){
            repet=true;
            $("#tabTurmas .alteracao a").click(function(){
                if(repet==true){                    
                  idPListaTurma = $(this).attr("idPListaTurma");
                  periodoTurma = $(this).attr("periodoTurma");

                  $("#formularioGerTurmas #periodoT").empty();
                  if(periodoTurma=="reg"){
                    $("#formularioGerTurmas #periodoT").append("<option>Matinal</option>")
                    $("#formularioGerTurmas #periodoT").append("<option>Vespertino</option>")
                  }else{
                    $("#formularioGerTurmas #periodoT").append("<option>Noturno</option>")                    
                  }
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
      $("#formularioGerTurmas #formIdPCurso").val(idPCurso)
      $("#formularioGerTurmas #formClasse").val(classe);

       $("#numTTurmas").text(completarNumero(gerenciadorTurmas.length));
        var html ="";
         gerenciadorTurmas.forEach(function(dado){         
         

          var opcao="";
          if(dado.atributoTurma!=null && dado.atributoTurma!="" && dado.atributoTurma!=undefined){
            var opcoes = dado.atributoTurma.toString().split("-");
            if(opcoes.length==2){
              opcao = retornarNomeDisciplina(opcoes[0])+
              " e<br/>"+retornarNomeDisciplina(opcoes[1])
            }else{
              opcao = retornarNomeDisciplina(opcoes[0])
            }
          }


          html +="<tr><td class='lead text-center'>"+dado.nomeTurma+" ("+dado.designacaoTurma+")</td><td class='lead text-center'>"+
           completarNumero(vazioNull(dado.numeroSalaTurma))+"</td><td class='lead'>"+retornarPeriodo(dado.periodoTurma)
          +"</td><td class='lead'>"+vazioNull(dado.periodoT)
          +"</td><td class='lead paraCoordenador toolTipeImagem' imagem='"
          +dado.fotoEntidade+"' title='"+dado.numeroInternoEntidade+"'><a href='"+caminhoRecuar+"areaSecretaria/relatorioFuncionario?idPFuncionario="
          +dado.idPEntidade+"' class='black'>"+vazioNull(dado.nomeEntidade)+
          "</a></td><td class='lead'>"+opcao+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn' href='#' idPListaTurma='"
          +dado.idPListaTurma+"'  periodoTurma='"+dado.periodoTurma+"'><i class='fa fa-check-circle fa-2x'></i></a></div></td></tr>";
        });
        $("#tabTurmas").html(html);
    }

    function porValoresFormulario(){
        gerenciadorTurmas.forEach(function(dado){
          if(dado.idPListaTurma==idPListaTurma){
              $("#formularioGerTurmas #nomeTurma1").val(dado.nomeTurma);
              $("#formularioGerTurmas #numeroTurma").val(dado.numeroSalaTurma);
              $("#formularioGerTurmas #periodoT").val(dado.periodoT);
              $("#formularioGerTurmas #dataConselhoNotas").val(dado.dataConselhoNotas);
              $("#formularioGerTurmas #horaConselhoNotas").val(dado.horaConselhoNotas);
              $("#formularioGerTurmas #salaReunidoConselho").val(dado.salaReunidoConselho);
              $("#formularioGerTurmas #idPresidenteConselho").val(dado.idPresidenteConselho);
              $("#formularioGerTurmas #numeroPauta").val(dado.numeroPauta);

              $("#formularioGerTurmas #designacaoTurma").val(dado.designacaoTurma);
              $("#formularioGerTurmas #listaProfessor").val(dado.idCoordenadorTurma);
              $("#formularioGerTurmas").modal("show");
          }
        })
    }


    function retornarNomeDisciplina(id){
      retorno="";
      nomedisciplinas.forEach(function(dado){
        if(dado.idPNomeDisciplina==id){
          retorno=dado.nomeDisciplina
        }
      })
      return retorno;
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
