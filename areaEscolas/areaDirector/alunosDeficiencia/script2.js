
    var tabelaAListar="#aindaNaoReconfirmados";
    var dataReconf ="";
    var classeEnviar="";
    var idAnoMatriculadoAluno =0;

    var idPMatricula="";
    var action="";
    var mensagemEspera ="";
    var posicaoArray = -1;

    window.onload = function(){
        fecharJanelaEspera();
        seAbrirMenu();
        entidade ="alunos";
        directorio = "areaDirector/reconfirmacao/";    
        fazerPesquisa();
        $("#example1").DataTable({
          "responsive": true, "lengthChange": true, "autoWidth": false,
          "buttons": ["copy", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    } 
    
    function fazerPesquisa(){
            var html ="";
            var masculino=0;
            $(".numTMasculinos").text(masculino);

            $(".numTAlunos").text(completarNumero(alunosDeficientes.length));
            var i=0;
           alunosDeficientes.forEach(function(dado){
               i++
                if(dado.sexoAluno=="F"){
                  masculino++;
                }
                $(".numTMasculinos").text(completarNumero(masculino)); 
                classe=classeExtensa(dado.escola.classeActualAluno);
                if(dado.escola.classeActualAluno==120){
                    classe="Finalista"
                }
                html += "<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
                +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno
                +"</a></td><td class='lead text-center'>"+dado.abrevCurso
                +"</td><td class='text-center lead'>"
                +classe+"</td><td class='lead'>"
                +dado.deficienciaAluno+"</td><td class='text-center lead'>"
                +dado.tipoDeficienciaAluno+"</td></tr>";      
           });
           $("#alunosDeficientes").html(html);
    };

    