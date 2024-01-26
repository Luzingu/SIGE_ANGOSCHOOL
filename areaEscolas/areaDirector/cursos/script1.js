window.onload=function(){

    fecharJanelaEspera();
    seAbrirMenu()
    entidade ="cursos";
    directorio = "areaDirector/cursos/";

    fazerPesquisa();
    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#novoCurso").click(function(){
      $("#formularioCursos #action").val("salvarCurso")
      $("#formularioCursos #idPNomeCurso").empty()
      $("#formularioCursos #idPNomeCurso").append("<option value=''>Seleccionar</option>");
      listaNomeCursos.forEach(function(dado){
        $("#formularioCursos #idPNomeCurso").append("<option value='"+dado.idPNomeCurso+"'>"+
          dado.nomeCurso+" ("+dado.areaFormacaoCurso+")</option>");
      })
      $("#formularioCursos").modal("show")
    })

    $("#formularioCursos #idPNomeCurso").change(function() {

      listaNomeCursos.forEach(function(dado){
        if($("#formularioCursos #idPNomeCurso").val()==dado.idPNomeCurso){

          if(dado.sePorSemestre=="sim"){
            $("#formularioCursos #semestreActivo").html("<option value='I'>I Semestre</option><option value='II'>II Semestre</option>")
          }else{
            $("#formularioCursos #semestreActivo").html("<option value='I'>I Semestre</option>")
          }
        }
      })
      $("#formularioCursos #semestreActivo").val("I")
    })

    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPNomeCurso = $(this).attr("idPNomeCurso")
          $("#formularioCursos #action").val($(this).attr("action"))

          if($(this).attr("action")=="editarCurso"){
            porValoresNoFormulario()
            $("#formularioCursos").modal("show")
          }else{
            $("#formularioCursos #idPNomeCurso").html("<option value='"+idPNomeCurso+"'>...</option>");
            $("#formularioCursos #idPNomeCurso").val(idPNomeCurso);
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir este curso?");
          }
          repet=false
        }

      })
    })

    $("#formularioCursos form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          manipular();
      }
        return false;
    });

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
  $("#numTCursos").text(completarNumero(listaCursos.length));

    var tbody = "";
      var contagem=0;
    listaCursos.forEach(function(dado){
      contagem++;

      var tipoCurso = dado.tipoCurso;
      if(tipoCurso=="tecnico"){
        tipoCurso="Técnico"
      }else if(tipoCurso=="pedagogico"){
        tipoCurso="Pedagógico"
      }else if(tipoCurso=="geral"){
        tipoCurso="Geral"
      }else if(tipoCurso=="fundamental"){
        tipoCurso="Fundamental"
      }

      tbody +="<tr><td class='lead text-center'>"
      +completarNumero(contagem)+"</td><td class='lead'>"+dado.nomeCurso+" ("+dado.areaFormacaoCurso+")"
      +"</td><td class='lead text-center'>"+dado.cursos.semestreActivo
      +"</td><td class='lead text-center'>"+dado.cursos.modoPenalizacao
      +"</td><td class='lead text-center'>"
      +dado.cursos.estadoCurso+"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarCurso' idPNomeCurso='"+dado.idPNomeCurso
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirCurso'  idPNomeCurso='"+dado.idPNomeCurso
      +"'><i class='fa fa-times'></i></a></div></td></tr>";

    });
    $("#tabela").html(tbody)
}

function porValoresNoFormulario(){
    listaCursos.forEach(function(dado){
      if(dado.idPNomeCurso==idPNomeCurso){
        $("#formularioCursos #idPNomeCurso").html("<option value='"+dado.idPNomeCurso+"'>"+
          dado.nomeCurso+" ("+dado.areaFormacaoCurso+")</option>");
        $("#formularioCursos #idPNomeCurso").val(dado.idPNomeCurso);
        $("#formularioCursos #nomeCoordenador").val(dado.cursos.idCursoEntidade);
        $("#formularioCursos #estadoCurso").val(dado.cursos.estadoCurso);
        $("#formularioCursos #numeroCpp").val(dado.cursos.numeroCpp)
        $("#formularioCursos #modLinguaEst").val(dado.cursos.modLinguaEstrangeira)
        $("#formularioCursos #campoAvaliar").val(dado.cursos.campoAvaliar)
        $("#formularioCursos #curriculoEscola").val(dado.cursos.curriculoEscola)
        $("#formularioCursos #tipoCurriculo").val(dado.cursos.tipoCurriculo)

        $("#formularioCursos #modoPenalizacao").val(dado.cursos.modoPenalizacao)
        $("#formularioCursos #paraDiscComNegativas").val(dado.cursos.paraDiscComNegativas)
        if(dado.sePorSemestre=="sim"){
          $("#formularioCursos #semestreActivo").html("<option value='I'>I Semestre</option><option value='II'>II Semestre</option>")
        }else{
          $("#formularioCursos #semestreActivo").html("<option value='I'>I Semestre</option>")
        }
        $("#formularioCursos #semestreActivo").val(dado.cursos.semestreActivo)
      }
   });
}


function manipular(){
    $("#formularioCursos").modal("hide")
    chamarJanelaEspera("")
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        fecharJanelaEspera();
        if(resultado.trim().substring(0, 1)=="F"){
            mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "Acção concluída com sucesso.");
          listaCursos = JSON.parse(resultado);
          fazerPesquisa();
        }
      }
    }
    var form = new FormData(document.getElementById("formularioCursosForm"));
   enviarComPost(form);
}
