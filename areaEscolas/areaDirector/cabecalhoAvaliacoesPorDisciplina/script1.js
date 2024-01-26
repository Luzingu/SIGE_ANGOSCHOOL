var listaDadosConjuntoClasses=""
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaDirector/cabecalhoAvaliacoesPorDisciplina/";
    $("#idPNomeCurso").val(idPNomeCurso)
    $("#anosLectivos").val(idPAno)
    $("#idPNomeCurso, #anosLectivos").change(function(){
      window.location='?idPNomeCurso='+$("#idPNomeCurso").val()+"&idPAno="+$("#anosLectivos").val()
    })

    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          $("#formularioAvaliacoes #idPDisciplina").val($(this).attr("idPDisciplina"))
          $("#formularioAvaliacoes #idPNomeDisciplina").val($(this).attr("idPNomeDisciplina"))

          conjuntoDados = $(this).attr("conjuntoDados")
          
          $("#formularioAvaliacoes input").prop("checked", false)
          if(conjuntoDados!=undefined && conjuntoDados!=null){
            conjuntoDados.split(",").forEach(function(dado){
              if(dado!=null && dado!=""){
                $("#formularioAvaliacoes input#"+dado.trim()).prop("checked", true)
              }
            })
          }
          $("#formularioAvaliacoes").modal("show")
          repet=true
        }
      })
    })

    $("#formularioAvaliacoesForm").submit(function(){

      var conjuntoDados="";
      var conjuntoDadosExt="";
      $("#formularioAvaliacoes input[type=checkbox]").each(function(){
        if($(this).prop("checked")==true){
          if(conjuntoDados!=""){
            conjuntoDados +=", "
          }
          if(conjuntoDadosExt!=""){
            conjuntoDadosExt +=", "
          }
          conjuntoDados +=$(this).attr("id")
          conjuntoDadosExt +=$(this).attr("idExtenso")
        }
      })
      $("#formularioAvaliacoesForm #conjuntoDados").val(conjuntoDados)
      $("#formularioAvaliacoesForm #conjuntoDadosExt").val(conjuntoDadosExt)
      manipular()
      return false
    })


    fazerPesquisa();
}

function fazerPesquisa(){
  var tbody="";
      var i=0;
    listaDisciplinas.forEach(function(dado){
      i++;

      var campo = "cabecalhoAvaliacoesExt-"+idPAno
      var campo2 = "cabecalhoAvaliacoes-"+idPAno

      tbody +="<tr><td class='lead'>"+dado.abreviacaoDisciplina1 
      +"</td><td class='lead text-center'>"+dado.disciplinas.periodoDisciplina 
      +"</td><td class='lead text-center'>"+dado.disciplinas.classeDisciplina
      +"</td><td class='lead text-center'>"+dado.disciplinas.tipoDisciplina
      +"</td><td class='lead text-center'>"+dado.disciplinas.continuidadeDisciplina
      +"</td><td class=''>"+vazioNull(dado.disciplinas[campo])+"</td><td class='text-center'>"
      +"<a href='#' idPDisciplina='"+dado.disciplinas.idPDisciplina
      +"' idPNomeDisciplina='"+dado.idPNomeDisciplina+"' conjuntoDados='"+dado.disciplinas[campo2]
      +"'><i class='fa fa-pen'></i></a></td></tr>";           
    });
    $("#tabela").html(tbody)
}

function manipular(){
  chamarJanelaEspera()
  $("#formularioAvaliacoes").modal("hide")
   var form = new FormData(document.getElementById("formularioAvaliacoesForm"));
   enviarComPost(form);
   http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      fecharJanelaEspera()
      estadoExecucao="ja";
      if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
          mensagensRespostas("#mensagemCerta", "Acção concluida com sucesso.");
          listaDisciplinas = JSON.parse(resultado)
          fazerPesquisa();
      }
    }
  }
}
