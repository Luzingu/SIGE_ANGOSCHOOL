  var idAlunosVisualizar="";
  $(document).ready(function(){ 
    
      fecharJanelaEspera();
      seAbrirMenu();       
      directorio = "areaPedagogica/actualizarPautas/";

      $("#luzingu").val(luzingu)  
      fazerPesquisa();

      DataTables("#example1", "sim")
      $("#luzingu").change(function(){ 
        window.location ="?luzingu="+$("#luzingu").val();
      });

      $(".visualizadorRelatorio").click(function(){
        window.location =caminhoRecuar+"relatoriosPdf/"+$(this).attr("caminho")+"/?turma="+turma
          +"&classe="+classeP+"&idPCurso="+idCursoP;
      })
      $("#example1 #actualizarTodos").change(function(){
        if($(this).prop("checked")==true){
          $("#example1 input[type=checkbox]").prop("checked", true)
        }else{
          $("#example1 input[type=checkbox]").prop("checked", false)
        }
      })

      $("#actualizarPautas").click(function(){
        idAlunosVisualizar="";
        $("#example1 input[type=checkbox]").each(function(){
          if($(this).prop("checked")==true){
            if(idAlunosVisualizar==""){
              idAlunosVisualizar=$(this).attr("idPMatricula")
            }else{
              idAlunosVisualizar +=","+$(this).attr("idPMatricula")
            }
          }
        })
        manipular()
      })
  })


    function fazerPesquisa(){

      $("#numTAlunos").text(completarNumero(listaAlunos.length));
      $("#numTMasculinos").text(0);

      var numM=0;
      var tbody="";
      var i=0
      listaAlunos.forEach(function(dado){

        i++;
        if(dado.sexoAluno=="F"){
          numM++;
        }
        $("#numTMasculinos").text(completarNumero(numM));
        var contPauta=0;
        if(dado.pautas!=undefined){
          dado.pautas.forEach(function(pauta){
              if(pauta.classePauta==classeP && (pauta.idPautaCurso==idCursoP || pauta.classePauta<=9)){
                contPauta++
              }
          })
        }        
        tbody +="<tr><td class='lead text-center' style='font-size:20pt; font-weight:bolder'>"+contPauta+"</td><td class='lead text-center'><input type='checkbox' idPMatricula='"+dado.idPMatricula
        +"'></td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
        +"'>"+dado.nomeAluno
        +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
        +"' class='lead black'>"+dado.numeroInterno
        +"</a></td><td class='lead text-center'>"+vazioNull(dado.biAluno)
        +"</td><td class='lead text-center'>"+dado.sexoAluno
        +"</td></tr>";
      })
      $("#tabela").html(tbody)
    }

  function manipular(){
    chamarJanelaEspera("");
    http.onreadystatechange = function(){
      if(http.readyState==4){
        estadoExecucao="ja";
        fecharJanelaEspera();
        resultado = http.responseText.trim()
        if(resultado.substring(0,1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "A comissão foi criada com sucesso.")
          listaAlunos = JSON.parse(resultado)
          fazerPesquisa()
        }    
      }
    }
    enviarComGet("tipoAcesso=actualizarPautas&idAlunosVisualizar="+
    idAlunosVisualizar+"&classe="+classeP+"&idPCurso="+idCursoP+"&turma="+turma);
  }