var idPTransferencia="";

window.onload = function(){
    fecharJanelaEspera();
    seAbrirMenu();
    entidade ="alunos";
    directorio = "areaDirector/transferenciaEfectuada/";
    $("#anosLectivos").val(idPAno);
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#anosLectivos").change(function(){
      window.location ="?idPAno="+$("#anosLectivos").val()+"&privacidade="+privacidade;
    });
}
    
    function fazerPesquisa(){
    var contagem=0;
    var html ="";
    var masculino=0;
    $(".numTMasculinos").text(masculino);  

    $(".numTAlunos").text(completarNumero(alunosTransferidos.length));

     alunosTransferidos.forEach(function(dado){
         contagem++;

          $(".numTMasculinos").text(completarNumero(masculino)); 

          html += "<tr><td class='lead text-center'>"+completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
          +"</td><td class='lead text-center'>"+dado.numeroInterno
          +"</td><td class='lead'>"+dado.nomeEscolaOrigem
          +"</td><td class='lead'>"+dado.nomeEscolaDestino
          +"</td></tr>";
                
     });
     $("#tabTransferencia").html(html);
    };


    
    