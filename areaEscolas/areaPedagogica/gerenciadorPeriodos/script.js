  var idPGerPeriodo="";
  $(document).ready(function(){
      
    fecharJanelaEspera();
    seAbrirMenu();
    fazerPesquisa();
    directorio = "areaPedagogica/gerenciadorPeriodos/";

    var repet=true;
    $("#tabGerenciador").bind("click mouseenter", function(){
      repet=true;
      $("#tabGerenciador a.alterador").click(function(){
        if(repet==true){
          idPGerPeriodo = $(this).attr("idPGerPeriodo");
          $("#formularioPeriodos #idPGerPeriodo").val(idPGerPeriodo)
          gerenciadorPeriodo.forEach(function(dado){
            if(dado.gerencPerido.idPGerPeriodo==idPGerPeriodo){
              $("#formularioPeriodos #periodoGerenciador").val(dado.gerencPerido.periodoGerenciador)
              $("#formularioPeriodos #horaEntrada").val(dado.gerencPerido.horaEntrada)
              $("#formularioPeriodos #duracaoPorTempo").val(dado.gerencPerido.duracaoPorTempo)

              $("#formularioPeriodos #intevaloDepoisDoTempo").val(dado.gerencPerido.intevaloDepoisDoTempo)
              $("#formularioPeriodos #duracaoIntervalo").val(dado.gerencPerido.duracaoIntervalo)
              $("#formularioPeriodos #idCoordernadorPeriodo").val(dado.gerencPerido.idCoordernadorPeriodo)
              $("#formularioPeriodos #numeroTempos").val(dado.gerencPerido.numeroTempos)
              $("#formularioPeriodos #numeroDias").val(dado.gerencPerido.numeroDias)      
            }
          })
          $("#formularioPeriodos").modal("show")
          repet=false;
        }
      })
    })

    $("#actualizar").click(function(){
        gerenciadorPeriodos();
    })

    $("#formularioPeriodosForm").submit(function(){
      manipularPeriodo();
      return false;
    })
      
  }) 

  function fazerPesquisa(){
    var html="";
    gerenciadorPeriodo.forEach(function(dado){
      duracaoPorTempo = dado.gerencPerido.duracaoPorTempo
      if(duracaoPorTempo==null){
        duracaoPorTempo=0;
      }

      html +="<tr><td class='lead'>"+dado.gerencPerido.periodoGerenciador+"</td><td class='lead text-center'>"
      +vazioNull(dado.gerencPerido.horaEntrada)+"</td><td class='lead text-center'>"
      +vazioNull(dado.gerencPerido.numeroTempos)+" tempos/"+vazioNull(dado.gerencPerido.numeroDias)+" dias</td><td class='lead text-center'>"
      +duracaoPorTempo+" Minutos</td><td class='lead text-center'>"
      +vazioNull(dado.gerencPerido.duracaoIntervalo)+" Minutos, após "+vazioNull(dado.gerencPerido.intevaloDepoisDoTempo)+"º Tempo</td><td class='lead text-center'><a href='"+caminhoRecuar
      +"/areaDirector/perfilFuncionario?aWRQUHJvZmVzc29y="+dado.gerencPerido.idPEntidade
      +"' class='lead black'>"
      +vazioNull(dado.nomeEntidade)
      +"</a></td><td class='lead text-center'><a href='#' class='alterador' idPGerPeriodo='"+dado.gerencPerido.idPGerPeriodo+"'><i class='fa fa-check'></i></a></td></tr>"
    })
    $("#tabGerenciador").html(html)
  }

  function manipularPeriodo(){
    $("#formularioPeriodos").modal("hide")
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim();
        fecharJanelaEspera();
        if(resultado.substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");
          gerenciadorPeriodo = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioPeriodosForm"));
   enviarComPost(form);
  }

  function gerenciadorPeriodos(){
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
          if(http.readyState==4){
            fecharJanelaEspera();
            resultado = http.responseText.trim()
            if(resultado.substring(0, 1)=="F"){
                mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
            }else{
              mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");
              gerenciadorPeriodo = JSON.parse(resultado)
              fazerPesquisa();
            }       
          }    
    }
    enviarComGet("tipoAcesso=gravarGerenciadorPeriodos&idPAno="+idPAno);
  }

  