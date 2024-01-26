    var valorPesquisado="";
    var idPMatricula="";
    var numeroTurmas  ="";
  $(document).ready(function(){ 
    
      fecharJanelaEspera();
      seAbrirMenu();       
      directorio = "areaSecretaria/divisaoTurmasPorIdade/";

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
        
          i++;
          var seCadeirante = "";
          if(dado.classeCadeirante!="" && dado.classeCadeirante!=null){
            seCadeirante=" (Cadeirante)";
          }

          tbody +="<tr id='linha"+dado.idPMatricula+"'><td class='lead text-center'>"+completarNumero(i)
          +"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
          +"'>"+dado.nomeAluno +seCadeirante
          +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula
          +"' class='lead black'>"+dado.numeroInterno
          +"</a></td><td class='lead text-center'>"+vazioNull(dado.biAluno)
          +"</td><td class='lead text-center'>"+dado.sexoAluno
          +"</td></tr>";
        })
        $("#tabela").html(tbody)
    }