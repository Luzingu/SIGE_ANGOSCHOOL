var idPNomeCurso=0;
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaGestaoEscolas/cursos00/";

    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#novoCurso").click(function(){
      limparFormulario("#formularioCursos")
      $("#formularioCursos #action").val("salvarCurso")
      $("#formularioCursos").modal("show")
    })

    $("#formularioCursos form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          manipular();
      } 
        return false;
    })
    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td a").click(function(){
        if(repet==true){
          idPNomeCurso = $(this).attr("idPNomeCurso")
          $("#formularioCursos #action").val($(this).attr("action"))
          $("#formularioCursos #idPCurso").val(idPNomeCurso)

          if($(this).attr("action")=="excluirCurso"){
            mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes excluir este curso?");
          }else{
            listaCursos.forEach(function(dado){
              if(dado.idPNomeCurso==idPNomeCurso){
                $("#formularioCursos #nomeCurso").val(dado.nomeCurso);
                $("#formularioCursos #abrevCurso").val(dado.abrevCurso);
                $("#formularioCursos #ordem").val(dado.ordem);
                $("#formularioCursos #idSubSistema").val(dado.idSubSistema);

                $("#formularioCursos #tipoCurso").val(dado.tipoCurso)
                $("#formularioCursos #areaFormacao").val(dado.areaFormacaoCurso)
                $("#formularioCursos #especialidadeCurso").val(dado.especialidadeCurso)
                $("#formularioCursos #sePorSemestre").val(dado.sePorSemestre)
                $("#formularioCursos #primeiraClasse").val(dado.primeiraClasse)
                $("#formularioCursos #ultimaClasse").val(dado.ultimaClasse)
                $("#formularioCursos #curriculo1").val(dado.curriculo1)
                $("#formularioCursos #curriculo2").val(dado.curriculo2)
                $("#formularioCursos #curriculo3").val(dado.curriculo3)
              }
            })
            $("#formularioCursos").modal("show")
          }
        }
      })
    })
    
     var rep=true;
    $("body").bind("mouseenter click", function(){
          rep=true;
        $("#janelaPergunta #pergSim").click(function(){
          if(rep==true){
              if(estadoExecucao=="ja"){
                idEspera = "#janelaPergunta #pergSim";
                estadoExecucao="espera";
                manipular();
              }            
            rep=false;
          }         
      })
    })
}

function fazerPesquisa(){
  var tbody="";
  $("#numTCursos").text(completarNumero(listaCursos.length));
      var i=0;
    listaCursos.forEach(function(dado){
      i++;      
      tbody +="<tr><td class='lead text-center'>"
      +vazioNull(dado.ordem)+"</td><td class='lead'>"+dado.nomeCurso
      +"</td><td class='lead'>"+dado.abrevCurso + " ("+dado.idPNomeCurso+")"
      +"</td><td class='lead'>"+vazioNull(dado.desCurriculo1)
      +"</td><td class='lead'>"+vazioNull(dado.desCurriculo2)
      +"</td><td class='lead'>"+vazioNull(dado.desCurriculo3)
      +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#as' action='editarCurso' idPNomeCurso='"+dado.idPNomeCurso
      +"'><i class='fa fa-pen'></i></a><a class='btn btn-danger' n='' title='Excluir' href='#a' action='excluirCurso' idPNomeCurso='"+dado.idPNomeCurso
      +"'><i class='fa fa-times'></i></a></div></td></tr>";           
    });
    $("#tabela").html(tbody)
}
  function manipular(){
    $("#formularioCursos").modal("hide")
      chamarJanelaEspera("...")
     var form = new FormData(document.getElementById("formularioCursosForm"));
     enviarComPost(form);

      http.onreadystatechange = function(){
        if(http.readyState==4){
          resultado = http.responseText.trim();
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