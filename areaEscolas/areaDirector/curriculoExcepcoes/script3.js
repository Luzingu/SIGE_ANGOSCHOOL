var idPExcepcao = "";
window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu();
    $("#luzingu").val(luzingu);
    entidade ="disciplinas";
    directorio = "areaDirector/curriculoExcepcoes/";

    fazerPesquisa();

    $("#anosLectivos").change(function(){
      fazerPesquisa()
    })
    DataTables("#example1", "sim")

    $("#luzingu").change(function(){
        window.location ="?luzingu="+$("#luzingu").val();
    })

    $("#novaExcepcao").click(function(){
      var htmlDisciplinas =""
      listaNomeDisciplinas.forEach(function(dado){
        nomeDisciplina = dado.nomeDisciplina
        if(dado.idPNomeDisciplina==22 || dado.idPNomeDisciplina==23){
          nomeDisciplina +=" (F. E.)";
        }
        htmlDisciplinas += "<option value='"+dado.idPNomeDisciplina+"'>"+nomeDisciplina+"</option>";
      })
      $("#formularioExcepcoes #action").val("novaExcepcao")
      $("#formularioExcepcoes #idPNomeDisciplina").html(htmlDisciplinas)
      $("#formularioExcepcoes").modal("show")
    })

    $("#excluirCuriculo").click(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="espera";
        excluirCuriculo();
      }
    })

    $("#copiarCuriculo").click(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="espera";
        copiarCurriculo();
      }
    })

    $("#formularioExcepcoes form").submit(function(){

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
          $("#formularioExcepcoes #anosLectivos").val(anosLectivos)
          manipular();
      }
        return false;
    });


    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPExcepcao = $(this).attr("idPExcepcao")
          $("#formularioExcepcoes #action").val($(this).attr("action"))
          $("#formularioExcepcoes #idPExcepcao").val($(this).attr("idPExcepcao"))

          if($(this).attr("action")=="editarExcepcoes"){
            porValoresNoFormulario()
            $("#formularioExcepcoes").modal("show")
          }else{
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

  $("#numTDisciplinas").text(completarNumero(listaExcepcoes.length));

    var tbody = "";
    var i = 0;
    var tipoDisciplina="";
    listaExcepcoes.forEach(function(dado){

      var seEDesteAnoLectivo=""
      var anosLectivos = dado.anosLectivos
      if(anosLectivos!=undefined && anosLectivos!=null && anosLectivos!=""){
        anosLectivos.toString().split(",").forEach(function(anosL){
          if(anosL.trim()==$("#anosLectivos").val()){
            seEDesteAnoLectivo="sim";
          }
        })
      }
      if(seEDesteAnoLectivo=="sim" || $("#anosLectivos").val()==""){
        i++;
        tbody +="<tr><td class='lead text-center'>"
        +completarNumero(i)+"</td><td class='lead'>"+dado.nomeDisciplina
        +"</td><td class='lead'>"
        +vazioNull(dado.anosLectivos)+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarExcepcoes' idPExcepcao='"+dado.idPExcepcao
        +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirExcepcao' idPExcepcao='"+dado.idPExcepcao
        +"'><i class='fa fa-times'></i></a></div></td></tr>";
      }
    });
  $("#tabela").html(tbody);
}


function porValoresNoFormulario(){
  listaExcepcoes.forEach(function(dado){
      if(dado.idPExcepcao==idPExcepcao){
        var nomeDisciplina = dado.nomeDisciplina
        if(idPNomeDisciplina==22 || idPNomeDisciplina==23){
          nomeDisciplina +=" (F. E.)";
        }
        $("#formularioExcepcoes #idPNomeDisciplina").html("<option value='"+idPNomeDisciplina
          +"'>"+nomeDisciplina+"</option>")
        $("#paraAnosLectivos input").prop("checked", false)

        var anosLectivos = dado.anosLectivos
        if(anosLectivos!=undefined && anosLectivos!=null && anosLectivos!=""){
          anosLectivos.toString().split(",").forEach(function(anosL){
            $("#paraAnosLectivos input[id="+anosL.trim()+"]").prop("checked", true)
          })
        }
      }
   });
}


function manipular(){
    $("#formularioExcepcoes").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaExcepcoes = JSON.parse(resultado)
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioExcepcoesForm"));
   enviarComPost(form);
}
