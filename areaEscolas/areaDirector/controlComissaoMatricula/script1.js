    var idReconfProfessor=""
    var dataReconf=""; 
    window.onload = function(){
        
        fecharJanelaEspera();
        
        seAbrirMenu();
        luzingu = $("#luzingu").val().toString().split("_")
        idReconfProfessor = luzingu[0]
        dataReconf = luzingu[1]
        fazerPesquisa()
        $("#anosLectivos").val(idPAno);
        entidade ="alunos";
        directorio = "areaDirector/reconfirmacao/";

        fazerPesquisa();
          
        DataTables("#example1", "sim")
 
        
        $("#anosLectivos").change(function(){
          window.location ="?idPAno="+$("#anosLectivos").val();
        }) 
        $("#luzingu").change(function(){
            luzingu = $("#luzingu").val().toString().split("_")
            idReconfProfessor = luzingu[0]
            dataReconf = luzingu[1]
            fazerPesquisa()
        })

}
    
    function fazerPesquisa(){
        var contagem=0;
        var html ="";
        var masculino=0;
        $(".numTMasculinos").text(masculino);

        $(".numTAlunos").text(completarNumero(alunosReconfirmados.filter(condicao).length));

       alunosReconfirmados.filter(condicao).forEach(function(dado){
           contagem++;
            if(dado.sexoAluno=="F"){
              masculino++;
            }
            $(".numTMasculinos").text(completarNumero(masculino)); 

            var forClasse=""
            if(dado.classeReconfirmacao>=10){
                forClasse = vazioNull(dado.abrevCurso)+" - "
            }
            forClasse += classeExtensa(dado.classeReconfirmacao, dado.sePorSemestre, "sim")
          
            html += "<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
            +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaPedagogica/perfilAluno?aWRQTWF0cmljdWxh="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno
            +"</a></td><td class='lead'>"+dado.nomeEntidade+"</td><td class='lead text-center'>"+forClasse+"</td><td class='text-center lead'>"+dado.horaReconf+"<br/>"+converterData(dado.dataReconf)+"</td></tr>";
                   
       });
       $("#tabJaReconfirmados").html(html)
    }


    function condicao(elem, ind, obj){
      return (elem.idReconfProfessor== idReconfProfessor || idReconfProfessor=="todo")
      && (elem.dataReconf== dataReconf || dataReconf=="todo")   
    }
