var idPGestor="";
var tipoResultado="";
window.onload=function(){

  fecharJanelaEspera();
  seAbrirMenu()
  directorio = "areaGestaoInscricao/lancamentoResultados/";

  $("#curso").val(idPCurso)
  fazerPesquisa();
  $("#curso").change(function(){
    window.location ='?idPCurso='+$(this).val();
  })
  $(".pesquisaAluno").keyup(function(){
    fazerPesquisa();
  })
}


function fazerPesquisa(){

  $("#numTAlunos").text(0);
  $("#numTMasculinos").text(0);

  $("#numTAlunos").text(completarNumero(alunos.filter(condition).length));
        $("#numTMasculinos").text(0);

    var tbody = "";
      if(jaTemPaginacao==false){
        paginacao.baraPaginacao(alunos.filter(condition).length, 50);
      }else{
          jaTemPaginacao=false; 
      }

      var contagem=-1;
        var numM=0;
        var numApurado=0;
    alunos.filter(condition).forEach(function(dado){
        contagem++;
         if(dado.sexoAluno=="F"){
                numM++;
            }

            if(dado.inscricao.obsApuramento=="A"){
                numApurado++;
            }

            $("#numTMasculinos").text(completarNumero(numM));
            $("#numTAprovados").text(completarNumero(numApurado));
        if(contagem>=paginacao.comeco && contagem<=paginacao.final){
            
            var html =""
            if(criterioTeste=="exameAptidao"){
              html +="<td class='lead text-center valor'>"+vazioNull(dado.inscricao.notaExame1)+"</td><td class='lead text-center valor'>"
              +vazioNull(dado.inscricao.notaExame2)+"</td><td class='lead text-center valor'>"
              +vazioNull(dado.inscricao.notaExame3)+"</td><td class='lead text-center valor'>"
              +vazioNull(dado.inscricao.mediaExames)+"</td><td class='lead text-center'>"
              +vazioNull(dado.sexoAluno)+"</td><td class='lead text-center'>"
              +converterData(dado.dataNascAluno)+"</td>"
            }else if(criterioTeste=="factor"){
              html +="<td class='lead text-center'>"
              +vazioNull(dado.inscricao.mediaDiscNuclear)+"</td><td class='lead text-center'>"
              +converterData(dado.dataNascAluno)+"</td><td class='lead text-center'>"
              +vazioNull(dado.sexoAluno)+"</td><td class='lead text-center percentagem'>"
              +vazioNull(dado.inscricao.percentagemAcumulada)+"</td>"
            }else {
              html +="<td class='lead text-center valor'>"
              +vazioNull(dado.inscricao.mediaDiscNuclear)+"</td><td class='lead text-center'>"
              +converterData(dado.dataNascAluno)+"</td><td class='lead text-center'>"
              +vazioNull(dado.sexoAluno)+"</td>"
            }

            var obs = dado.inscricao.obsApuramento;
            if(obs=="A"){
              obs="Apurado";
            }else if(obs=="P"){
              obs="--";
            }else{
              obs="Excluído(a)";
            }
            var periodo = vazioNull(dado.inscricao.periodoApuramento)
            if(periodo=="reg"){
              periodo="Regular"
            }else if(periodo=="pos"){
              periodo="Pós-Laboral"
            }

          tbody +="<tr><td class='lead text-center'>"
          +completarNumero(dado.inscricao.posicaoApuramento)+"</td><td class='lead'><a class='black' href='"+caminhoRecuar+"areaGestaoInscricao/relatorioAluno?idPAluno="
          +dado.idPAluno+"'>"+dado.nomeAluno
          +"</a></td><td class='lead text-center'>"
          +dado.codigoAluno+"</td>"+html+"<td class='lead text-center'>"
          +periodo+"</td><td class='lead text-center obs'><strong>"
          +obs+"</strong></td></tr>";
        }
           
    });
    $("#tabela").html(tbody)
    corCelula();
}

function condition(elem, ind, obj){
  return (elem.nomeAluno.toLowerCase().indexOf($(".pesquisaAluno").val().toLowerCase().trim())>=0 || elem.codigoAluno.toLowerCase().indexOf($(".pesquisaAluno").val().toLowerCase().trim())>=0) ;
}

function corCelula(){
    $("table .valor").each(function(i){
        if($(this).text()<10 && $(this).text().trim()!=""){
            $(this).css("color", "red");
        }else{
          $(this).css("color", "darkblue");
        }
    });

    $("table .obs").each(function(i){
        if($(this).text()=="Excluído(a)" && $(this).text().trim()!=""){
            $(this).css("color", "red");
        }else if($(this).text()=="Apurado" && $(this).text().trim()!=""){
            $(this).css("color", "darkgreen");
        }else{
          $(this).css("color", "darkblue");
        }
    });

    $("table .percentagem").each(function(i){
        if($(this).text()<50 && $(this).text().trim()!=""){
            $(this).css("color", "red");
        }else{
          $(this).css("color", "darkblue");
        }
    });

  }