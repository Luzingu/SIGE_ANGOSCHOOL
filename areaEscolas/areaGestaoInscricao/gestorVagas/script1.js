var idPGestor="";
window.onload=function(){

  fecharJanelaEspera();
  seAbrirMenu()
  directorio = "areaGestaoInscricao/gestorVagas/";
  seleccionarOption();
  listarGestorVagas();

  

  $("#criterioTeste").change(function(){
    seleccionarOption();
    nomeProvas();
  })
  $("#periodoCurso").change(function(){
    criterioEscolhaPeriodo();
  })

  $("#gestorvagasForm #numeroProvas").change(function(){
    nomeProvas();
  })

  $("#gestorvagasForm input, #gestorvagasForm select").bind("change keyup", function(){
     $("#gestorvagasForm #msgErro").text("")
  })

  $("#gestorvagasForm").submit(function(){
    if($("#criterioTeste").val()=="factor"){
     var percTotal = new Number($("#precedencia3 #percIdade").val()) + 
     new Number($("#precedencia3 #perMedDiscNucleares").val()) + new Number($("#precedencia3 #percGenero").val())
     + new Number($("#precedencia3 #percAlunosEmRegime").val())

     if(percTotal!=100){
        $("#gestorvagasForm #msgErro").text("A percentagem total deve igual a 100.")
     }else{
      manipular();
     }
    }else{
        manipular();
    }
    
    return false;
  }) 

  $("#actualizar").click(function(){
    gravarGestorVagas();    
  })

  var repet=true;
  $("#tabela").bind("click mouseenter", function(){
    repet=true;
      $("#tabela tr td a").click(function(){
       idPGestor = $(this).attr("idPGestor");

       gestorvagas.forEach(function(dado){
          if(dado.idPGestor==idPGestor){
            $("#gestorvagasForm #msgErro").text("")
            $("#gestorvagasForm #numeroVagasReg").val(dado.vagasReg)
            $("#gestorvagasForm #numeroVagasPosLab").val(dado.vagasPos)
            $("#gestorvagasForm #idPGestor").val(dado.idPGestor)
            $("#gestorvagasForm #codigoTurma").val(dado.codigoDeTurma)
            $("#gestorvagasForm #criterioTeste").val(dado.criterioTeste)
            $("#gestorvagasForm #tipoAutenticacao").val(dado.tipoAutenticacao)
            if(dado.seAvaliarApenasMF=="sim"){
              $("#seAvaliarApenasMF").prop("checked", true);
            }else{
              $("#seAvaliarApenasMF").prop("checked", false);
            }
            seleccionarOption();
            $("#gestorvagasForm #procFactor1, #gestorvagasForm #avalFactor1").val(dado.factor1)
            $("#gestorvagasForm #procFactor2, #gestorvagasForm #avalFactor2").val(dado.factor2)
            $("#gestorvagasForm #procFactor3, #gestorvagasForm #avalFactor3").val(dado.factor3)
            $("#gestorvagasForm #procFactor4, #gestorvagasForm #avalFactor4").val(dado.factor4)

            $("#gestorvagasForm #periodoCurso").val(dado.periodosCurso)
            criterioEscolhaPeriodo();
            $("#gestorvagasForm #criterioEscolhaPeriodo").val(dado.criterioEscolhaPeriodo)

            $("#gestorvagasForm #percIdade").val(dado.percDataNascAluno)
            $("#gestorvagasForm #perMedDiscNucleares").val(dado.perMedDiscNucleares)
            $("#gestorvagasForm #percGenero").val(dado.percGenero)
            $("#gestorvagasForm #percAlunosEmRegime").val(dado.percAlunosEmRegime)
            $("#gestorvagasForm #notaMinDiscNucleares").val(dado.notaMinDiscNucleares)
            $("#gestorvagasForm #numeroProvas").val(dado.numeroProvas)
            nomeProvas();
            $("#gestorvagasForm #nomeProva1").val(dado.nomeProva1)
            $("#gestorvagasForm #nomeProva2").val(dado.nomeProva2)
            $("#gestorvagasForm #nomeProva3").val(dado.nomeProva3)
            $("#gestorvagas").modal("show");
          }
       })

      })
  }) 
}

function criterioEscolhaPeriodo(){
    $("#criterioEscolhaPeriodo").empty();
    if($("#periodoCurso").val()=="reg" || $("#periodoCurso").val()=="pos"){
      $("#criterioEscolhaPeriodo").append("<option value='auto'>Automático</option>")
    }else{
      $("#criterioEscolhaPeriodo").append("<option value='auto'>Automático</option>")
      $("#criterioEscolhaPeriodo").append("<option value='opcional'>Opcional</option>")
      for(var i=16; i<=22; i++){
        $("#criterioEscolhaPeriodo").append("<option value='"+i+"'>(>"+i+") Pós-Laboral</option>")
      }
    }
}

function nomeProvas(){
    $("#gestorvagasForm .nomeProvas").remove();

    if($("#criterioTeste").val()=="exameAptidao"){
      var tino ="";
      for(var i=1; i<=$("#gestorvagasForm #numeroProvas").val(); i++){

        tino +='<div class="col-lg-4 col-md-4 lead text-center nomeProvas">'+
        '<label class="lead" for="nomeProva'+i+'">'+i+'ª Prova:</label>'+
        '<input type="text" placeholder="Nome da 1ª Prova"  class="form-control lead" name="nomeProva'+
        i+'" id="nomeProva'+i+'" required></div>';
      }
      $("#gestorvagasForm .numeroProvas").after(tino)  
    }
    
}

function seleccionarOption(){
  $("#gestorvagasForm #msgErro").text("")
  $("fieldset select, fieldset input").removeAttr("required");
  $("#notaMinDiscNucleares").val("10");
  if($("#criterioTeste").val()=="exameAptidao"){
      $("#precedencia1 #procFactor2").val("dataNascAluno");

      $("#precedencia1 #procFactor3").val("mediaDiscNuclear");
      $("#precedencia1 #procFactor4").val("sexoAluno");
      $("#notaMinDiscNucleares").val("13")

      $("#precedencia1 select, #precedencia1 input").attr("required", "");
      $("#precedencia1").show();
      $("#precedencia3, #precedencia2").hide();

      $("#gestorvagas #numeroProvas").attr("required", "")
      $("#gestorvagas #numeroProvas").val(2)
      $("#gestorvagas .numeroProvas").show();
 
  }else if($("#criterioTeste").val()=="factor"){
      $("#precedencia3 #percIdade").val("70")
      $("#precedencia3 #perMedDiscNucleares").val("25")
      $("#precedencia3 #percGenero").val("5");
      $("#precedencia3").show();
      $("#precedencia2, #precedencia1").hide();

      $("#precedencia3 select, #precedencia3 input").attr("required", "");

      $("#gestorvagas #numeroProvas").removeAttr("required")
      $("#gestorvagas #numeroProvas").val("")
      $("#gestorvagas .numeroProvas").hide();

  }else if($("#criterioTeste").val()=="criterio"){
      $("#precedencia2").show();
      $("#precedencia3, #precedencia1").hide();

      $("#precedencia2 #avalFactor1").val("dataNascAluno");
      $("#precedencia2 #avalFactor2").val("sexoAluno");
      $("#precedencia2 #avalFactor3").val("mediaDiscNuclear");
      $("#precedencia2 #avalFactor4").val("alunosEmRegime");

      $("#precedencia2 select, #precedencia2 input").attr("required", "")

      $("#gestorvagas #numeroProvas").removeAttr("required")
      $("#gestorvagas #numeroProvas").val("")
      $("#gestorvagas .numeroProvas").hide();
  }

  $("fieldset select, fieldset input[type=checkbox]").removeAttr("required");
}

function listarGestorVagas(){
  var html="";
  gestorvagas.forEach(function(dado){
    $("#codigoTurma").val(dado.codigoDeTurma);
    var vagasReg = dado.vagasReg;
    if(vagasReg==null){
      vagasReg=0;
    }
    var vagasPos = dado.vagasPos;
    if(vagasPos==null){
      vagasPos=0;
    }
      var criterio  = dado.criterioTeste
      if(criterio=="exameAptidao"){
        criterio ="Exame de Aptidão"
      }else if(criterio=="factor"){
        criterio ="Factores"
      }else if(criterio=="criterio"){
        criterio ="Critérios"
      }

      var estado=""
      if(dado.estadoTransicaoCurso=="F"){        
        estado ="<i class='fa fa-check text-success' title='Aberto'></i>"
      }else if(dado.estadoTransicaoCurso=="Y"){
         estado ="<i class='fa fa-refresh text-primary' title='Em curso...'></i>"
      }else if(dado.estadoTransicaoCurso=="V"){
         estado ="<i class='fa fa-times text-danger' title='Fechado'></i>"
      }
      var numeroProvas = dado.numeroProvas;
      if(numeroProvas==null){
        numeroProvas=0;
      }
      periodosCurso = vazioNull(dado.periodosCurso);
      if(periodosCurso=="reg"){
        periodosCurso ="Somente Regular"
      }else if(periodosCurso=="pos"){
        periodosCurso ="Somente Pós-Laboral";
      }else if(periodosCurso=="regPos"){
        periodosCurso ="Regular e Pós-Laboral"
      }

    html +="<tr><td class='lead text-center'>"+abrevNomeDoCurso(dado.idGestCurso)+"</td>"
    +"<td class='lead text-center'>"+vagasReg+"</td>"
    +"<td class='lead text-center'>"+vagasPos+"</td>"
    +"<td class='lead text-center'>"+(new Number(vagasReg)+new Number(vagasPos))
    +"</td>"
    +"<td class='lead text-center'>"+criterio+"</td>"
    +"<td class='lead text-center'>"+periodosCurso+"</td>"
    +"<td class='lead text-center'>"+numeroProvas+"</td>"
    +"<td class='lead text-center'>"+estado+"</td>"   
    +"<td class='lead text-center'><a href='#' idPGestor='"+dado.idPGestor+"'><i class='fa fa-check'></i></a></td>"
    +"</tr>";
  })
  $("#tabela").html(html);
}


function gravarGestorVagas(){
  chamarJanelaEspera("...");
  http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera();
      resultado = http.responseText.trim()
      if(resultado.trim().substring(0, 1)=="F"){
         mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        gestorvagas = JSON.parse(resultado);
        listarGestorVagas();
      }
    }
  }
  enviarComGet("tipoAcesso=gravarGestorVagas");
}
 

 function manipular(){
    $("#gestorvagasForm #Cadastar").html('<i class="fa fa-spinner fa-spin"></i> Alterando...'); 
    $("#gestorvagasForm #Cadastar").focus();     
   http.onreadystatechange = function(){
      if(http.readyState==4){
        $("#gestorvagasForm #Cadastar").html('<i class="fa fa-check"></i> Alterar'); 
        $("#gestorvagas").modal("hide");
        resultado = http.responseText.trim()  
        if(resultado.trim().substring(0, 1)=="F"){
           mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Dados alterados com sucesso.");
          gestorvagas = JSON.parse(resultado);
          listarGestorVagas();
        }
      }
    }
    var form = new FormData(document.getElementById("gestorvagasForm"));
   enviarComPost(form);
}