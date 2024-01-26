    var idPTransferencia="";

    window.onload = function(){
        fecharJanelaEspera();
        seAbrirMenu();
        directorio = "areaSecretaria/transferenciasRecebidas/";
        $("#anosLectivos").val(idPAno);      
        fazerPesquisa();
        DataTables("#example1", "sim")

        $("#anosLectivos").change(function(){
          window.location ="?idPAno="+$("#anosLectivos").val();
        });
    }
    
    function fazerPesquisa(){
        var contagem=-1;
        var html ="";
        $(".numTAlunos").text(completarNumero(alunosTransferidos.length));
       alunosTransferidos.forEach(function(dado){
           contagem++;
            html += "<tr><td class='lead text-center'>"+completarNumero(contagem+1)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
            +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno
            +"</a></td><td class='lead'>"+dado.nomeEscola
            +"</td><td class='lead text-center'>"+converterData(dado.transferencia.dataTransferencia)
            +"</td></tr>";      
       });
       $("#tabTransferencia").html(html);
    };   
    