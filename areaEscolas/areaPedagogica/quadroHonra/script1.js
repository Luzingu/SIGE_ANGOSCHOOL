    window.onload = function(){
        
        fecharJanelaEspera();
        seAbrirMenu();
        $("#anosLectivos").val(idPAno);
 
        entidade ="alunos";
        directorio = "areaDirector/reconfirmacao/";
        $("#trimestre").val(trimestre)
        $("#luzingu").val(luzingu)

        $("#formPesquisar").submit(function(){
          window.location ="?luzingu="+$("#luzingu").val()+"&idPAno="+
          $("#anosLectivos").val()+"&trimestre="+$("#trimestre").val()
          +"&numero="+$("#numero").val();          
          return false;
        })
        fazerPesquisa(); 
        DataTables("#example1", "sim")
}
    
    function fazerPesquisa(){
        var contagem=-1;
            var html ="";
            var masculino=0;
            $(".numTMasculinos").text(masculino);
            $(".numTAlunos").text(completarNumero(quadroHonra.length));

           quadroHonra.forEach(function(dado){
               contagem++;
                if(dado.sexoAluno=="F"){
                  masculino++;
                }

                $(".numTMasculinos").text(completarNumero(masculino)); 

                  var mf = null;
                  if(trimestre==1){
                    mf=dado.reconfirmacoes.mfT1;
                  }else if(trimestre==2){
                    mf=dado.reconfirmacoes.mfT2;
                  }else if(trimestre==3){
                    mf=dado.reconfirmacoes.mfT3;
                  }else if(trimestre==4){
                    mf=dado.reconfirmacoes.mfT4;
                  }

                html += "<tr><td class='lead text-center'>"+completarNumero(dado.idPMatricula)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
                +"</td><td class='lead text-center'>"+dado.numeroInterno
                +"</td><td class='lead text-center'>"+vazioNull(dado.abrevCurso)
                +"</td><td class='text-center lead'>"+dado.reconfirmacoes.classeReconfirmacao
                +"</td><td class='text-center lead'>"+dado.reconfirmacoes.designacaoTurma+"</td><td class='text-center lead'><strong>"
                +vazioNull(mf)+"</strong></td><td class='text-center lead'>"
                +calcularIdade(dado.dataNascAluno)+" Aos</td></tr>";
           });
           $("#tabJaReconfirmados").html(html);
    };

    