
  
  window.onload = function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    $("#anosLectivos").val(idPAno);
    entidade ="alunos";

    fazerPesquisa();

    $("#anosLectivos").change(function(){
      window.location ="?idPAno="+$(this).val()+"&trimestre="+trimestre
    })

    $("#example1").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "buttons": ["copy", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
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
          if(trimestre=="I"){
            mf=dado.mfT1;
          }else if(trimestre=="II"){
            mf=dado.mfT2;
          }else if(trimestre=="III"){
            mf=dado.mfT3;
          }else if(trimestre=="IV"){
            mf=dado.mfT4;
          }

          html += "<tr><td class='lead text-center'>"+completarNumero(dado.idPMatricula)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
          +"</td><td class='lead text-center'>"+dado.abrevNomeEscola2
          +"</td><td class='lead text-center'>"+vazioNull(dado.abrevCurso)
          +"</td><td class='text-center lead'>"+classeExtensa(dado.classeReconfirmacao, dado.sePorSemestre)
          +"</td><td class='text-center lead'>"+dado.designacaoTurma+"</td><td class='text-center lead'><strong>"+mf+"</strong></td></tr>";
        });
        $("#tabJaReconfirmados").html(html);
    };

    