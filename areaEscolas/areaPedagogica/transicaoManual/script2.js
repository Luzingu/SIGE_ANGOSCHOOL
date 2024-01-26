  var valorMaximo = 10;
  var seFezAlteracao ="nao";

  var idAlunoSeleccionado="";
  var posicaoSeleccionada=0;
  var grupoAluno =0;
  var copiouDados="nao"
  var classeActualAluno="";
  var classeReconfirmacao="";

  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaPedagogica/transicaoManual/";
      $("#luzingu").val(luzingu);
      $("#anosLectivos").val(idPAno);

      if(classeP<=6){
        valorMaximo=10;
      }else{
        valorMaximo=20;
      }
      contador();
      $("#luzingu, #anosLectivos").change(function(){
          window.location ="?luzingu="+$(this).val()
          +"&idPAno="+$("#anosLectivos").val()
      })

      $("#listaAlunos").bind("click mouseenter", function(){
        $("#listaAlunos input[type=number]").bind("keyup change", function(){
          $("#listaAlunos form[idPDisciplina="+$(this).attr("idPDisciplina")+"]").attr("alterou", "sim")
          seFezAlteracao="sim"
        })
      })

      $("#listaAlunos").bind("click mouseenter", function(){
        $("#listaAlunos input.mfdAtribuir").bind("keyup change", function(){
          var matondo = "#listaAlunos form[idPDisciplina="+$(this).attr("idPDisciplina")+"]";
          $(matondo).attr("alterou", "sim")

          var factorizador = $(this).val()*3-new Number($(matondo+" .mtI").val())-new Number($(matondo+" .mtII").val())
          $(matondo+" .mac3, "+matondo+" .npp3, "+matondo+" .npt3").val(factorizador)

          if(superConselho=="sim" && factorizador>15){
            $(matondo+" .mac3, "+matondo+" .npp3, "+matondo+" .npt3").val($(this).val())

            $(matondo+" .mac1, "+matondo+" .npp1, "+matondo+" .npt1").val($(this).val())
            $(matondo+" .mac2, "+matondo+" .npp2, "+matondo+" .npt2").val($(this).val())
          }
          seFezAlteracao="sim"
        })
      })

      $("#actualizarEstadoAluno").click(function(){
        if(classeReconfirmacao==classeActualAluno){
          actualizarEstadoAluno()
        }
      })
      $("#copiarDados").click(function(){
        copiarDados()
      })

      $("#btnRefresh").click(function(){
        carregarPautas()
      })

      $("#recuarAluno").click(function(){

        if(posicaoSeleccionada>1 && estadoExecucao=="ja"){

          $("#copiarDados").show()
          $(".btnAlterarNotas").hide()
          if(idAnoPassado!=idPAno){
            $(".btnAlterarNotas").show()
          }
          estadoExecucao="aindaNao"
          posicaoSeleccionada--
          idAlunoSeleccionado = $("#selectAluno option[posicao="+posicaoSeleccionada+"]").attr("value")
          grupoAluno = $("#selectAluno option[posicao="+posicaoSeleccionada+"]").attr("grupoAluno")
          $("#selectAluno").val(idAlunoSeleccionado)
          buscarDadosAluno()
        }
      })

      $("#avancarAluno").click(function(){
        if(posicaoSeleccionada<listaAlunos.length && estadoExecucao=="ja"){

          $("#copiarDados").show()
          $(".btnAlterarNotas").hide()
          if(idAnoPassado!=idPAno){
            $(".btnAlterarNotas").show()
          }
          estadoExecucao="aindaNao"
          posicaoSeleccionada++
          idAlunoSeleccionado = $("#selectAluno option[posicao="+posicaoSeleccionada+"]").attr("value")
          grupoAluno = $("#selectAluno option[posicao="+posicaoSeleccionada+"]").attr("grupoAluno")
          $("#selectAluno").val(idAlunoSeleccionado)
          buscarDadosAluno()
        }
      })

      $("#selectAluno").change(function(){
        $("#copiarDados").show()
        $(".btnAlterarNotas").hide()
        if(idAnoPassado!=idPAno){
          $(".btnAlterarNotas").show()
        }
        idAlunoSeleccionado = $(this).val();
        posicaoSeleccionada = $("#selectAluno option:selected").attr("posicao")
        grupoAluno = $("#selectAluno option[posicao="+posicaoSeleccionada+"]").attr("grupoAluno")
        buscarDadosAluno()
      })
      $(".btnAlterarNotas").click(function(){
        irmaNeusa();
      })
  }

  function contador(){
    var total=0;
    var totalFem=0;
    var totalAprov=0;
    var totalAprovFem=0;
    listaAlunos.forEach(function(dado){
      total ++;
      if(dado.sexoAluno=="F"){
        totalFem++;
      }
      var observacaoF=dado.reconfirmacoes.observacaoF;
      if(observacaoF=="A" || observacaoF=="TR"){
        totalAprov++;
      }

      if((observacaoF=="A" || observacaoF=="TR") && dado.sexoAluno=="F"){
        totalAprovFem++;
      }
    })
    $("#total").text(completarNumero(total));
    $("#totalF").text(completarNumero(totalFem));
    $("#totalAprov").text(completarNumero(totalAprov));
    $("#totalAprovFem").text(completarNumero(totalAprovFem));
  }

    function irmaNeusa(){
      valoresEnviar = new Array();

      var nomeCampoComErroEncontrado="";
      var msgErro="";
       $("form.formularioNotas[alterou=sim]").each(function(){

          var idPDisciplina = $(this).attr("idPDisciplina")

          var avaliacoesQuantitativas=new Array();
          $("form[idPDisciplina="+idPDisciplina+"] input[tipoCampo=avaliacao]").each(function(){
            avaliacoesQuantitativas.push({name:$(this).attr("name"), valor:$(this).val()
            , idCampoAvaliacao:$(this).attr("idCampoAvaliacao"), periodo:$(this).attr("periodo")})
          })
          $("form[idPDisciplina="+idPDisciplina+"] input[tipoCampo=exame]").each(function(){
            avaliacoesQuantitativas.push({name:$(this).attr("name"), valor:$(this).val()
            , idCampoAvaliacao:$(this).attr("idCampoAvaliacao"), periodo:$(this).attr("periodo")})
          })

          valoresEnviar.push({
            estadoAluno: $("#estadoAluno").val(),
            idPMatricula:$(this).attr("idPMatricula"),
            idPDisciplina:$(this).attr("idPDisciplina"),
            avaliacoesQuantitativas:avaliacoesQuantitativas
          })

          $("form[idPDisciplina="+idPDisciplina+"] input[type=number]").each(function(){

              //Avaliando os dados do formulário...
              if($(this).attr("required")=="required" && $(this).val().trim()==""){
                nomeCampoComErroEncontrado = $(this).attr("name")
                $(this).focus()
                $(this).css("border", "solid red 1px");
                msgErro ="é obrigatório.";
              }

              //Fazer a segunda avaliacao...
              if(nomeCampoComErroEncontrado==""){
                if($(this).attr("min")!=undefined &&
                  $(this).val().trim()!="" && new Number($(this).val())<new Number($(this).attr("min"))){

                  nomeCampoComErroEncontrado = $(this).attr("name")
                  $(this).focus()
                  $(this).css("border", "solid red 1px");
                  msgErro ="não deve ser inferior que "+$(this).attr("min")+".";
                }
              }

              //Fazer a terceira avaliacao...
              if(nomeCampoComErroEncontrado==""){
                if($(this).attr("max")!=undefined &&
                  $(this).val().trim()!="" && new Number($(this).val())>new Number($(this).attr("max"))){
                  nomeCampoComErroEncontrado = $(this).attr("name")
                  $(this).focus()
                  $(this).css("border", "solid red 1px");
                  msgErro ="não deve ser superior que "+$(this).attr("max")+".";
                }
              }

          })
       })
        if(msgErro!=""){
        msgErro ="A nota "+msgErro;
        mensagensRespostas2("#mensagemErrada", msgErro)
       }else{
          if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao"
            manipularNotas();
          }
       }
    }

    function actualizarEstadoAluno(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          estadoExecucao="ja"
          seFezAlteracao="nao"
          resultado = http.responseText.trim()
          if(resultado=="V"){
            window.location="?luzingu="+$("#luzingu").val()
          }else{
            mensagensRespostas("#mensagemErrada", "Não foi possível actualizar o estado do aluno")
          }
        }
      }
      enviarComGet("idAlunoSeleccionado="+idAlunoSeleccionado
      +"&tipoAcesso=actualizarEstadoAluno&idPAno="+$("#anosLectivos").val()
      +"&idPCurso="+idCursoP+"&classe="+classeP+"&tipoCurso="+tipoCurso
      +"&turma="+turma+"&periodoTurma="+periodo);
    }

    function manipularNotas(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim()
          estadoExecucao="ja"
          seFezAlteracao="nao"
          fecharJanelaEspera()
          if(resultado.substring(0,1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length))
            porDadoAluno()
          }else if(resultado.trim()!=""){
            resultado = JSON.parse(resultado)
            exibirMensagens(resultado[0]);
            listaDados = resultado[1]
            porDadoAluno()
          }
        }
      }
      $("#formularioDados #dados").val(JSON.stringify(valoresEnviar))
      $("#formularioDados #idPCurso").val(idCursoP)
      $("#formularioDados #tipoCurso").val(tipoCurso)
      $("#formularioDados #classe").val(classeP)
      $("#formularioDados #turma").val(turma)
      $("#formularioDados #periodoTurma").val(periodo)
      $("#formularioDados #idAlunoSeleccionado").val(idAlunoSeleccionado)
      $("#formularioDados #estadoAluno").val($("#estadoAluno").val())
      $("#formularioDados #grupoAluno").val(grupoAluno)
      $("#formularioDados #idPAno").val($("#anosLectivos").val())

      var form = new FormData(document.getElementById("formularioDados"))
      enviarComPost(form)
    }

    function copiarDados(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          estadoExecucao="ja"
          seFezAlteracao="nao"
          resultado = http.responseText.trim()
          $("#copiarDados").hide()
          $(".btnAlterarNotas").show()
          buscarDadosAluno()
        }
      }
      enviarComGet("idAlunoSeleccionado="+idAlunoSeleccionado
        +"&tipoAcesso=copiarDados&idPCurso="+idCursoP+"&classe="+classeP+
        "&tipoCurso="+tipoCurso+"&turma="+turma
        +"&periodoTurma="+periodo+"&idPAno="+$("#anosLectivos").val());
    }

    function carregarPautas(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          estadoExecucao="ja"
          seFezAlteracao="nao"
          resultado = http.responseText.trim()
          buscarDadosAluno()
        }
      }
      enviarComGet("idAlunoSeleccionado="+idAlunoSeleccionado
        +"&tipoAcesso=carregarPautas&idPCurso="+idCursoP+"&classe="+classeP+
        "&tipoCurso="+tipoCurso+"&turma="+turma
        +"&periodoTurma="+periodo+"&idPAno="+$("#anosLectivos").val());
    }

    function buscarDadosAluno(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera()
          estadoExecucao="ja"
          seFezAlteracao="nao"
          resultado = http.responseText.trim()
          listaDados = JSON.parse(resultado)
          porDadoAluno()

        }
      }
      enviarComGet("idAlunoSeleccionado="+idAlunoSeleccionado+"&tipoAcesso=buscarDadosAluno&idPCurso="+idCursoP+"&classe="+classeP+
        "&tipoCurso="+tipoCurso+"&turma="+turma
        +"&periodoTurma="+periodo+"&idPAno="+$("#anosLectivos").val());
    }

    function exibirMensagens(resultadoManipulacao){
      if(resultadoManipulacao==""){
        mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");
      }else{
        resultadoManipulacao.forEach(function(dado){
            var msg = dado.msg;
            if(msg.substring(0,1)!="V"){
              encontrouNotaNaoAlterada="sim";
              mensagensRespostas("#mensagemErrada", msg);
            }else{
              encontrouNotaNaoAlterada="1";
            }
        })
        if(encontrouNotaNaoAlterada=="nao"){
          mensagensRespostas("#mensagemCerta", "Todas notas foram alteradas com sucesso.");
        }else if(encontrouNotaNaoAlterada=="1"){
          mensagensRespostas("#mensagemCerta", "As notas foram alteradas com sucesso.");
        }
      }
    }

    function porDadoAluno(){
      var i=0;
      $("#totalDeficiencia").text(" (0)")
      $("#dadosAlunos .vazio").text("--");
      $("#dadosAlunos img").attr("src", caminhoRecuar+"../fotoUsuarios/default.png");
      $("#listaDificiencias1").empty();
      $("#listaDificiencias2").empty();

      var totalDeficiencia=0;
      outroBloqueo="";
      $("#infoSobreAluno").text("")

     listaDados.forEach(function(dado){
      i++;
        if(i==1){
            $("#dadosAlunos img").attr("src", caminhoRecuar+"../fotoUsuarios/"+dado.fotoAluno);

            $("#dadosAlunos #nomeAluno").text(dado.nomeAluno)
            $("#dadosAlunos #dataNascAluno").text(converterData(dado.dataNascAluno))

            $("#dadosAlunos #idadeAluno").text(calcularIdade(dado.dataNascAluno)+" Anos")
            $("#dadosAlunos #sexoAluno").text(sexoExtensa(dado.sexoAluno))

            var estadoActividadeAluno=dado.reconfirmacoes.estadoDesistencia;
            var observacaoF=dado.reconfirmacoes.observacaoF;
            var seAlunoFoiAoRecurso=dado.reconfirmacoes.seAlunoFoiAoRecurso
            classeActualAluno = dado.escola.classeActualAluno
            classeReconfirmacao = dado.reconfirmacoes.classeReconfirmacao
            if(estadoActividadeAluno==""){
              $("#estadoAluno").val("A")
            }else{
              $("#estadoAluno").val(estadoActividadeAluno)
            }

            if(estadoActividadeAluno=="N"){
              estadoActividadeAluno="<span class='text-danger'>Mat. Anulada</span>";
            }else if(estadoActividadeAluno=="D"){
              estadoActividadeAluno="<span class='text-danger'>Desistente</span>";
            }else if(estadoActividadeAluno=="A/TRANSF"){
              estadoActividadeAluno="<span class='text-danger'>Apto/Transf.</span>";
            }else if(estadoActividadeAluno=="NA/TRANSF"){
              estadoActividadeAluno="<span class='text-danger'>N. Apto/Transf.</span>";
            }else if(estadoActividadeAluno=="RI"){
              estadoActividadeAluno="<span class='text-danger'>Rep. Indisc.</span>";
            }else if(estadoActividadeAluno=="RFN"){
              estadoActividadeAluno="<span class='text-danger'>R. Falta de Notas</span>";
            }else if(estadoActividadeAluno=="F"){
              estadoActividadeAluno="<span class='text-danger'>Excluido por Faltas</span>";
            }else{
              estadoActividadeAluno="<span class='text-success'>Activo</span>";
            }
            $("#dadosAlunos #estadoActividadeAluno").html(estadoActividadeAluno)

             if(observacaoF=="D"){
                observacaoF ="<span class='text-danger'>Desistente";
              }else if(observacaoF=="N"){
                observacaoF ="<span class='text-danger'>Anulado(a)";
              }else if(observacaoF=="A/TRANSF"){
                observacaoF="<span class='text-danger'>Apto/Transf.</span>";
              }else if(observacaoF=="NA/TRANSF"){
                observacaoF="<span class='text-danger'>N. Apto/Transf.</span>";
              }else if(observacaoF=="RI"){
                observacaoF="<span class='text-danger'>Rep. Indisc.</span>";
              }else if(observacaoF=="RFN"){
                observacaoF="<span class='text-danger'>R. por Falta de Notas</span>";
              }else if(observacaoF=="F"){
                observacaoF ="<span class='text-danger'>Rep. F";
              }else if(observacaoF=="TR"){
                observacaoF ="<span class='text-success'>Transita";
              }else if(observacaoF=="A"){
                observacaoF ="<span class='text-success'>Apto(a)";
              }else{
                observacaoF ="<span class='text-danger'>N. Apto(a)";
                if(seAlunoFoiAoRecurso=="A"){
                  observacaoF ="<span class='text-primary'>Recurso";
                }
              }
            $("#observacaoF").html(observacaoF+"</span>")
            estadoExecucao="ja";
        }

        var mediaConsiderar = vazioNull(dado.pautas.mf);
        if(tipoCurso=="tecnico" && dado.continuidadeDisciplina=="T" && campoAvaliar=="cfd"){
            mediaConsiderar=vazioNull(dado.pautas.cf);
        }
        var attrDisciplina = vazioNull(dado.tipoDisciplina);
        if(tipoCurso=="tecnico"){
          attrDisciplina =dado.continuidadeDisciplina;
        }
        if(mediaConsiderar<(valorMaximo/2)){
          totalDeficiencia++;
          if(totalDeficiencia<=8){
            if(totalDeficiencia<=4){
              $("#listaDificiencias1").append("<h5>"+dado.nomeDisciplina+" ("+attrDisciplina+") -> <span class='text-danger'>"+mediaConsiderar+"</span></h5>")
            }else{
              $("#listaDificiencias2").append("<h5>"+dado.nomeDisciplina+" ("+attrDisciplina+") -> <span class='text-danger'>"+mediaConsiderar+"</span></h5>")
            }
          }
        }
      })
      $("#totalDeficiencia").text(" ("+totalDeficiencia+")")
      fazerPesquisa()
    }

    function fazerPesquisa(){

      var html="";
      var notas = new Array();
      listaDados.forEach(function(dado){
        var attrDisciplina = vazioNull(dado.tipoDisciplina);
        if(tipoCurso=="tecnico"){
          attrDisciplina =dado.continuidadeDisciplina;
        }

        var resultFinal = dado.pautas.mf;
        if(tipoCurso=="tecnico" && dado.continuidadeDisciplina=="T" && campoAvaliar=="cfd"){
          resultFinal = dado.pautas.cf;
        }
        html +='<form class="row form'+dado.numeroInterno+' formulario formularioNotas" method="POST" idPMatricula="'
        +dado.idPMatricula+'" idPDisciplina="'+dado.pautas.idPautaDisciplina+'">'+
        '<div class="col-lg-4 col-md-4 col-sm-11 col-xs-11 lead"><strong style="font-size:20pt;"><br/>'+dado.nomeDisciplina+
        " ("+attrDisciplina+')</strong></div>'

        if(dado.pautas!=undefined && dado.pautas.idPautaDisciplina!=undefined &&
        camposAvaliacao[dado.pautas.idPautaDisciplina]!=undefined){

          camposAvaliacao[dado.pautas.idPautaDisciplina].forEach(function(campo){
            readonly="";
            if(campo.seApenasLeitura=="V"){
              readonly=" readonly"
            }
            html +='<div class="col-lg-1 col-md-1 col-sm-4 col-xs-4 text-center"><strong>'+
            campo.designacao1+'</strong><input type="number" name="'+campo.identUnicaDb
            +'" designacao="'+campo.designacao1+'" tipoCampo="'+campo.tipoCampo+'" periodo="'+campo.periodo+'" idCampoAvaliacao="'+campo.idCampoAvaliacao
            +'" class="form-control text-center inputVal mac1 lead" step="0.01" min="'+campo.notaMinima+'" max="'
            +campo.notaMaxima+'" media="'+campo.notaMedia+'" value="'+vazioNull(dado.pautas[campo.identUnicaDb])+'" '+readonly
            +' idPDisciplina="'+
            dado.pautas.idPautaDisciplina+'" style="font-size:13pt; font-weight:600; padding:0px;"></div>'
          })
        }

        html +='</form>';

      });
      $("#listaAlunos").html(html);
      corNotasVermelhaAzul("#listaAlunos form")
  }

  function condicao(elem, ind, obj){
      return elem.idPautaMatricula== idAlunoSeleccionado
  }
