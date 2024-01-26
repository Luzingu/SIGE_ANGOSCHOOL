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
        $("#anosLectivos").val(idPAno);

        directorio = "areaDirector/reconfirmacao/";
        $("#luzingu").val(luzingu);
        $("#idPEscola").val(idPEscola);

        fazerPesquisa()
        $(".abrirRelatorio").click(function(){
          window.location =caminhoRecuar+"relatoriosPdf/mapasEstaticosDosAlunosReconfirmados/"
          +$(this).attr("caminho")+"?idPAno="+
          idPAno+"&idPCurso="+idCursoP+"&classe="+classeP+"&privacidade="+privacidade; 
        })
          
        DataTables("#example1", "sim")
 
        var repet1 = true;
        $("table").bind("mouseenter click", function(){
            repet1=true;
            $("table div.alteracao a").click(function(){
                if(repet1==true){
                    idPMatricula = $(this).attr("idPMatricula");
                    classeEnviar = $(this).attr("classe");
                    action = $(this).attr("action");
                    posicaoArray = $(this).attr("posicaoArray");
                    idAnoMatriculadoAluno = $(this).attr("idAnoMatriculadoAluno");
                    
                    if(action=="anularReconfirmacao"){
                      mensagemEspera="Anulando a Reconfirmação...";
                      mensagensRespostas('#janelaPergunta', "Tens certeza que pretendes anular a reconfirmação de matricula deste(a) aluno(a)?");
                    }
                    repet1=false;
                }
            });

        });
        
        $("#luzingu, #anosLectivos, #idPEscola").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()
          +"&idPAno="+$("#anosLectivos").val()
          +"&idPEscola="+$("#idPEscola").val()+"&privacidade="+privacidade;
        })

}
    
    function fazerPesquisa(){
        var contagem=0;
            var html ="";
            var masculino=0;
            $(".numTMasculinos").text(masculino);

            $(".numTAlunos").text(completarNumero(alunosReconfirmados.length));

           alunosReconfirmados.forEach(function(dado){
               contagem++;
                if(dado.sexoAluno=="F"){
                  masculino++;
                }
                $(".numTMasculinos").text(completarNumero(masculino)); 

                html += "<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
                +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaPedagogica/perfilAluno?aWRQTWF0cmljdWxh="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno
                +"</a></td><td class='lead text-center'>"+dado.designacaoTurma
                +"</td></tr>";
                       
           });
           $("#tabJaReconfirmados").html(html);
    };



    