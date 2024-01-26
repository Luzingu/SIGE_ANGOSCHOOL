var listaDadosConjuntoClasses=""
window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/modeloAvaliacoes00/";
    $("#luzingu").val(luzingu)
    $("#anosLectivos").val(idPAno)
    $("#luzingu, #anosLectivos").change(function(){
      window.location='?luzingu='+$("#luzingu").val()+"&idPAno="+$("#anosLectivos").val()
    })

    $("#btnCopiar").click(function(){
      copiarAvaliacoes()
    })
    fazerPesquisa();
}

function fazerPesquisa(){
  var tbody="";
      var i=0;
    listaDisciplinas.forEach(function(dado){
      i++;

      var campo = "camposAvaliacoesExt-"+idPAno
      var campo2 = "camposAvaliacoes-"+idPAno
      tbody +="<tr><td class='lead'>"+dado.abreviacaoDisciplina1
      +"</td><td class='lead text-center'>"+dado.disciplinas.periodoDisciplina
      +"</td><td class='lead text-center'>"+dado.disciplinas.classeDisciplina
      +"</td><td class='lead text-center'>"+dado.disciplinas.tipoDisciplina
      +"</td><td class='lead text-center'>"+dado.disciplinas.continuidadeDisciplina
      +"</td><td class=''>"+vazioNull(dado.disciplinas[campo])+"</td></tr>";
    });
    $("#tabela").html(tbody)
}

function copiarAvaliacoes(){
  chamarJanelaEspera("...")
  http.onreadystatechange = function(){
    if(http.readyState==4){
        resultado = http.responseText.trim()
        fecharJanelaEspera()
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          window.location ='?luzingu='+luzingu+"&idPAno="+idPAno;
        }
    }
  }
  enviarComGet("tipoAcesso=copiarAvaliacoes&idDestino="
    +$("#luzingu").val()+"&anoDestino="+$("#anosLectivos").val()+"&anoOrigem="+$("#anoCopiar").val());
}
