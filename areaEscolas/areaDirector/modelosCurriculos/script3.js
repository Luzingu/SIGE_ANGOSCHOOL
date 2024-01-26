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

    $("#novaDisciplina").click(function(){
      var htmlDisciplinas =""
      listaNomeDisciplinas.forEach(function(dado){
        nomeDisciplina = dado.nomeDisciplina
        if(dado.idPNomeDisciplina==22 || dado.idPNomeDisciplina==23){
          nomeDisciplina +=" (F. E.)";
        }
        htmlDisciplinas += "<option value='"+dado.idPNomeDisciplina+"'>"+nomeDisciplina+"</option>";
      })
      $("#formularioDisciplinas #action").val("salvarDisciplina")
      $("#formularioDisciplinas #idPNomeDisciplina").html(htmlDisciplinas)
      $("#formularioDisciplinas").modal("show")
    })

    $("#copiarCuriculo").click(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="espera";
        copiarCurriculo();
      }
    })

    $("#formularioDisciplinas form").submit(function(){

      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          var anosLectivos ="";
          $("#paraAnosLectivos input[type=checkbox]").each(function(){
            if($(this).prop("checked")==true){
              if(anosLectivos!=""){
                anosLectivos +=", "
              }
              anosLectivos +=$(this).attr("id")
            }
          })
          $("#formularioDisciplinas #anosLectivos").val(anosLectivos)
          manipular();
      }
        return false;
    });


    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPNomeDisciplina = $(this).attr("idPNomeDisciplina")
          $("#formularioDisciplinas #action").val($(this).attr("action"))

          if($(this).attr("action")=="editarDisciplina"){
            porValoresNoFormulario()
            $("#formularioDisciplinas").modal("show")
          }else{
            $("#formularioDisciplinas #idPNomeDisciplina").html("<option value='"+idPNomeDisciplina+"'>--</option>")
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir esta disciplina?");
          }
          repet=false
        }

      })
    })

    var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
            if(estadoExecucao=="ja"){
              estadoExecucao="espera";
              manipular();
            }
            rep=false;
          }
      })
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
        +"</td><td class='lead'>"
        +vazioNull(dado.disciplinas.anosLectivos)+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarDisciplina' idPNomeDisciplina='"+dado.idPNomeDisciplina
        +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirDisciplina' idPNomeDisciplina='"+dado.idPNomeDisciplina
        +"'><i class='fa fa-times'></i></a></div></td></tr>";
      }
    });
  $("#tabela").html(tbody);
}


function porValoresNoFormulario(){
    disciplinas.forEach(function(dado){
      if(dado.idPNomeDisciplina==idPNomeDisciplina){
        var nomeDisciplina = dado.nomeDisciplina
        if(idPNomeDisciplina==22 || idPNomeDisciplina==23){
          nomeDisciplina +=" (F. E.)";
        }
        $("#formularioDisciplinas #idPNomeDisciplina").html("<option value='"+idPNomeDisciplina
          +"'>"+nomeDisciplina+"</option>")
        $("#formularioDisciplinas #estadoDisciplina").val(dado.disciplinas.estadoDisciplina);
        $("#formularioDisciplinas #tipoDisciplina").val(dado.disciplinas.tipoDisciplina);
        $("#formularioDisciplinas #idPNomeDisciplina").val(dado.idPNomeDisciplina);
        $("#formularioDisciplinas #ordemDisciplina").val(dado.disciplinas.ordenacao);
        $("#formularioDisciplinas #semestreDisciplina").val(dado.disciplinas.semestreDisciplina)

        $("#paraAnosLectivos input").prop("checked", false)

        var anosLectivos = dado.disciplinas.anosLectivos
        if(anosLectivos!=undefined && anosLectivos!=null && anosLectivos!=""){

          anosLectivos.toString().split(",").forEach(function(anosL){
            $("#paraAnosLectivos input[id="+anosL.trim()+"]").prop("checked", true)
          })
        }
      }
   });
}


function manipular(){
    $("#formularioDisciplinas").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          disciplinas = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioDisciplinasForm"));
   enviarComPost(form);
}

function copiarCurriculo(mensagem){
  chamarJanelaEspera("");
  enviarComGet("tipoAcesso=copiarCurriculo&classe="+classeP
    +"&periodo="+periodo+"&idPCurso="+idCursoP+"&idCurriculoCopiar="+$("#idCurriculoCopiar").val()+"&idCurriculoCurso="+curriculo);

  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim()
      estadoExecucao="ja"
      if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
        mensagensRespostas("#mensagemCerta", "O Curriculo foi copiado.");
        disciplinas = JSON.parse(resultado)
        fazerPesquisa();
      }
    }
  }
}
