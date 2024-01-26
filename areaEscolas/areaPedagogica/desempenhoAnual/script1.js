var idPAval="";
var posicaoArray=0;
var valorMaximo=10
  window.onload = function (){
  
    fecharJanelaEspera();
    seAbrirMenu();
      $("#anosLectivos").val(idPAno);
      $("#luzingu").val(luzingu);
      directorio = "areaPedagogica/desempenhoAnual/";
      if(classeP>=10){
        valorMaximo=20
      }
      fazerPesquisa();
      DataTables("#example1", "sim")

      $("#anosLectivos, #luzingu").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()+"&idPAno="
          +$("#anosLectivos").val();
      });
      var repet1=true;
    $("#tabela").bind("click mouseenter", function(){
        repet1=true;
        $("#tabela a.alterar").click(function(){
            if(repet1==true){
                idPReconf = $(this).attr("idPReconf");
                listaAlunos.forEach(function(dado){
                  if(dado.reconfirmacoes.idPReconf==idPReconf){
                    if(dado.reconfirmacoes.estadoDesistencia=="D" || dado.reconfirmacoes.estadoDesistencia=="N" || dado.reconfirmacoes.estadoDesistencia=="F"){
                      $("#formularioAvaliacao #estadoAluno").val(dado.reconfirmacoes.estadoDesistencia)
                    }else{
                      $("#formularioAvaliacao #estadoAluno").val("A")
                    }
                    $("#formularioAvaliacaoF #nomeAluno").val(dado.nomeAluno)
                    $("#formularioAvaliacaoF #idPMatricula").val(dado.idPMatricula)
                    $("#formularioAvaliacaoF #grupoAluno").val(dado.grupo)
                  }
                })
                $("#formularioAvaliacaoF #idPReconf").val(idPReconf)
                $("#formularioAvaliacao").modal("show");
                                  
                repet1=false;
            }

        });
    });

    $("#formularioAvaliacao").submit(function(){
        if(estadoExecucao=="ja"){
            estadoExecucao="aindaNao";
            manipularAvaliacaoAnual();
        }
        return false;
    })


  }


  
  function fazerPesquisa(){
    $("#numTAlunos").text(completarNumero(listaAlunos.length));
          
      var tbody = "";
    var contagem=0;
    var numF=0;
    listaAlunos.forEach(function(dado){
       contagem++;

      var obs = dado.reconfirmacoes.observacaoF;
      if(dado.reconfirmacoes.observacaoF=="D"){
        obs="Desistente";
      }else if(obs=="A"){
          obs="Apto(a)";
      }else if(obs=="TR"){
          obs="Transita";
      }else if(obs=="N"){
          obs="Anulado";
      }else if(obs=="EF"){
          obs="Excl. por Faltas";
      }else if(obs=="RI"){
          obs="Rep. I";
      }else if(obs=="F"){
          obs="Rep. F";
      }else{
        obs="N. Apto(a)";
      }
      var estadoActividade = "Activo";
      if(dado.reconfirmacoes.estadoDesistencia=="D"){
          estadoActividade="Desistente";
      }else if(dado.reconfirmacoes.estadoDesistencia=="N"){
          estadoActividade="Anulado";
      }else if(dado.reconfirmacoes.estadoDesistencia=="F"){
          estadoActividade="Excluido por Faltas";
      }

      tbody +='<tr><td class="text-center">'+completarNumero(contagem)
      +'</td><td class=" lead">'+dado.nomeAluno
      +'</td><td class="text-center"><a href="'
            +caminhoRecuar+'areaSecretaria/relatorioAluno?idPMatricula='+dado.idPMatricula
            +'" class="black">'+dado.numeroInterno
      +'</a></td><td class="text-center lead textVal">'+dado.reconfirmacoes.mfT1
      +'</td><td class="text-center lead textVal">'+dado.reconfirmacoes.mfT2
      +'</td><td class="text-center lead textVal">'+dado.reconfirmacoes.mfT3
      +'</td><td class="text-center lead textVal">'+dado.reconfirmacoes.mfT4
      +'</td><td class="text-center lead">'+estadoActividade
      +'</td><td class="text-center lead obs">'+obs
      +'</td><td class="text-center lead"><a href="#" class="alterar" idPReconf="'+
      dado.reconfirmacoes.idPReconf
      +'" ><i class="fa fa-check-circle text-success fa-2x"></i></a></td></tr>';
    });
    $("#tabela").html(tbody);
    
    corNotasVermelhaAzul2("#tabela tr", valorMaximo)
  }
  function manipularAvaliacaoAnual(){
    $("#idPAval").val(idPAval);
    $("#classeF").val(classeP);
    $("#idPCursoF").val(idCursoP);
    $("#formularioAvaliacao").modal("hide");
    chamarJanelaEspera("")
    http.onreadystatechange = function(){
      if(http.readyState==4){
        estadoExecucao ="ja";
        resultado = http.responseText.trim()
        fecharJanelaEspera()
        if(resultado.substring(0, 1)=="F"){
          $("#formularioAvaliacao").modal("show");
          mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length))
        }else if(resultado!=""){
          mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso.")
          listaAlunos = JSON.parse(resultado)
          fazerPesquisa();
        }
      }    
    }
    var form = new FormData(document.getElementById("formularioAvaliacaoF"));
    enviarComPost(form);
  }

function corCelula (dom){

    $("#"+dom+" strong.observacaoF").each(function(i){
        if($(this).text()=="Apto" || $(this).text()!="Apta" && $(this).text()=="Transita"){
          $(this).css("color", "darkgreen");
        }else{
          $(this).css("color", "red");
        }
    });

    $("#"+dom+" strong.estadoActividade").each(function(i){
        if($(this).text()=="Activo"){
          $(this).css("color", "darkblue");
        }else{
          $(this).css("color", "red");
        }
    });


  }


