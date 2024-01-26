var idPCurso = "";
window.onload=function(){

    fecharJanelaEspera(); 
    seAbrirMenu();
    $("#luzingu").val(luzingu);
    entidade ="disciplinas";
    directorio = "areaDirector/modelosCurriculos/";
    fazerPesquisa();
    $("#anosLectivos").change(function(){
      fazerPesquisa()
    })
    DataTables("#example1", "sim")
    $("#luzingu").change(function(){
        window.location ="?luzingu="+$("#luzingu").val();
    })
}

function fazerPesquisa(){

  $("#numTDisciplinas").text(completarNumero(disciplinas.length));
    var tbody = "";

    var tipoDisciplina="";
    disciplinas.forEach(function(dado){

      var seEDesteAnoLectivo=""
      var anosLectivos = dado.disciplinas.anosLectivos
      if(anosLectivos!=undefined && anosLectivos!=null && anosLectivos!=""){
        anosLectivos.toString().split(",").forEach(function(anosL){
          if(anosL.trim()==$("#anosLectivos").val()){
            seEDesteAnoLectivo="sim";
          }
        })
      }
      if(seEDesteAnoLectivo=="sim" || $("#anosLectivos").val()==""){
        tbody +="<tr><td class='lead text-center'>"
        +completarNumero(vazioNull(dado.disciplinas.ordenacao))+"</td><td class='lead'>"+dado.nomeDisciplina
        +"</td><td class='lead'>"+dado.disciplinas.tipoDisciplina
        +"</td><td class='lead text-center'>"+vazioNull(dado.disciplinas.semestreDisciplina)
        +"</td><td class='lead text-center'>"+vazioNull(dado.disciplinas.continuidadeDisciplina)
        +"</td><td class='lead'>"
        +vazioNull(dado.disciplinas.anosLectivos)+"</td></tr>";
      }
    });
  $("#tabela").html(tbody);
}