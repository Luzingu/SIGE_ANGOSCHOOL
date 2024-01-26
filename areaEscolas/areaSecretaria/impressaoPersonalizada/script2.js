    var valorPesquisado="";
    var idPMatricula="";
    var numeroTurmas  ="";
  $(document).ready(function(){ 
    
      fecharJanelaEspera();
      seAbrirMenu();       
      directorio = "areaSecretaria/divisaoTurmasPorIdade/";

      $("#luzingu").val(luzingu)  
    
    $("#visTodosCartoes").change(function(){
      if($(this).prop("checked")==true){
        $("#listaAlunos1 input[type=checkbox]").prop("checked", true)
      }else{
        $("#listaAlunos1 input[type=checkbox]").prop("checked", false)
      }
    })
    $("#luzingu").change(function(){
      window.location ="?luzingu="+$("#luzingu").val()
    })

    $(".visualizadorRelatorio1").click(function(){
      var idAlunosVisualizar="";
      $("#listaAlunos1 input[type=checkbox]").each(function(){
        if($(this).prop("checked")==true){
          if(idAlunosVisualizar==""){
            idAlunosVisualizar=$(this).attr("id")
          }else{
            idAlunosVisualizar +=","+$(this).attr("id")
          }
        }
      })
      if(idAlunosVisualizar==""){
        mensagensRespostas2("#mensagemErrada", "Deves seleccionar pelo menos um aluno.");
      }else{
        window.location =caminhoRecuar+"relatoriosPdf/"+$(this).attr("caminho")+"/?turma="+turma
        +"&classe="+classeP+"&idPCurso="+idCursoP
        +"&idAlunosVisualizar="+idAlunosVisualizar+"&trimestreApartir="+$(this).attr("trimestreApartir")
      }
    })
  })
