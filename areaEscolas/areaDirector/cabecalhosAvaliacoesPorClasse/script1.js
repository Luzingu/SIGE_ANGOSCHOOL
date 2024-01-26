var listaDadosConjuntoClasses=""
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaDirector/cabecalhosAvaliacoesPorClasse/";
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
          $("#formularioAvaliacoes #classe").val($(this).attr("classe"))

          conjuntoDados = $(this).attr("conjuntoDados")
          
          $("#formularioAvaliacoes input").prop("checked", false)
          if(conjuntoDados!=undefined && conjuntoDados!=null){
            conjuntoDados.split(",").forEach(function(dado){
              if(dado!="" && dado!=null){
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
    listaCursos.forEach(function(dado){

      if(dado.classes!=undefined && dado.classes!=null){

        dado.classes.forEach(function(classe){
          i++;

          var campo = "cabecalhoAvaliacoesExt"+classe.identificador+"-"+idPAno
          var campo2 = "cabecalhoAvaliacoes"+classe.identificador+"-"+idPAno

          tbody +="<tr><td class='lead'>"+classe.designacao 
          +"</td><td class='lead'>"+vazioNull(dado.cursos[campo])
          +"</td><td class='text-center'>"
          +"<a href='#' classe='"+classe.identificador+"' conjuntoDados='"+dado.cursos[campo2]+"'><i class='fa fa-pen'></i></a></td></tr>";
        }) 
      }          
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
          listaCursos = JSON.parse(resultado)
          fazerPesquisa();
      }
    }
  }
}
