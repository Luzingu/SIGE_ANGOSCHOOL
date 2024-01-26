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

        fazerPesquisa();
          
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
        
        $("#anosLectivos").change(function(){
          window.location ="?idPAno="+$("#anosLectivos").val()
          +"&privacidade="+privacidade;
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
                +"</a></td><td class='lead'>"+dado.nomeEscola
                +"</td></tr>";
                       
           });
           $("#tabJaReconfirmados").html(html);
    };

    function manipularReconfirmacao(){
        chamarJanelaEspera("");
        http.onreadystatechange = function(){
          if(http.readyState==4){
            fecharJanelaEspera();
            estadoExecucao="ja";
            resultado = http.responseText.trim();
            if(resultado.substring(0,1)=="V") {
              acualizarListas(resultado.substring(1,resultado.length));                           
            }else{
              estadoExecucao="ja";
               mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
            }
          }
        }
        enviarComGet("tipoAcesso="+action+"&idPMatricula="+idPMatricula+"&classe="
          +classeEnviar+"&idPCursoVerificacao="+idCursoP
          +"&idAnoMatriculadoAluno="+idAnoMatriculadoAluno+"&idPAno="+idPAno);
    }

    function acualizarListas(mensagem="", posoListarTodos="nao"){
      fecharJanelaEspera();
      chamarJanelaEspera("...");
      http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          if(mensagem!=""){
            mensagensRespostas('#mensagemCerta', mensagem);
          }
          alunosReconfirmados = JSON.parse(http.responseText.trim());
          fazerPesquisa();
        }     
      }
      enviarComGet("tipoAcesso=actualizarLista&posoListarTodos=sim&idPCurso="+idCursoP
        +"&classe="+classeP+"&periodo="+periodo);
    }



    