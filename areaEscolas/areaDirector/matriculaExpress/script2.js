
$(document).ready(function(){
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaDirector/copiarDadosAlunos/";
    
  
    $("#formPesquisar").submit(function(){
      pesquisarAluno();
      return false;
    }); 

    $("#formularioMatricula #tipoDocumento").change(function(){
    paraDocumentos()
  })

    var repet1 = true;

    $("#dadoTabela").bind("mouseenter click", function(){
        repet1=true;
        $("#dadoTabela a.alteracao").click(function(){
          if(repet1==true){
            idAlunoActual = $(this).attr("idAlunoActual");
            idAlunoNovo = $(this).attr("idAlunoNovo")
            copiarDados();
            repet1=false;
          }
        });
    });
})
 

    function pesquisarAluno(){
      chamarJanelaEspera("Pesquisando...")
      http.onreadystatechange = function(){
        if(http.readyState==4){
            resultado = http.responseText.trim()
            fecharJanelaEspera();
            var htmlAdicionar="";

            $("#dadoTabela").html("")
            alunosEncontrados = JSON.parse(resultado)
            JSON.parse(resultado).forEach(function(dado){
              var nivelAluno = classeExtensa(dado.escola.classeActualAluno, dado.sePorSemestre)+"<br/><strong>"+
              vazioNull(dado.abrevCurso)+"</strong>"
              
              if(dado.escola.classeActualAluno==120){
                nivelAluno ="Técnico Médio<br/><strong>"+dado.abrevCurso
                +"</strong>"
              }else if(dado.escola.classeActualAluno==90){
                nivelAluno ="Técnico Básico"
              }else if(dado.escola.classeActualAluno==60){
                nivelAluno ="Técnico Primário"
              }
              $("#nomeAluno").val(dado.nomeAlunoActual)

              htmlAdicionar +="<tr><td class='lead text-danger' colspan='6'>"+dado.informacao+"</td></tr>"
              htmlAdicionar +="<tr><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno
            +"'>"+dado.nomeAluno+"</td><td class='text-center'>"
              +dado.numeroInterno+"<br/><strong>"+vazioNull(dado.biAluno)
              +"</strong></td><td class='text-center'>"
              +dado.sexoAluno+"</td><td class=''>"
              +dado.abrevNomeEscola2+"</td><td class=''>"
              +nivelAluno+"</td><td class='text-center'>";
              if(dado.informacao==""){
                htmlAdicionar +="<a href='#' title='Copiar'"+
                " class='text-success alteracao' idAlunoActual='"+dado.idAlunoActual
                +"' idAlunoNovo='"+dado.idPMatricula+"'>"+
                "<i class='fa fa-copy fa-2x'></i></a>"
              }
              htmlAdicionar +="</td></tr>";
            })
            $("#dadoTabela").html(htmlAdicionar)
        }     
      }
      enviarComGet("tipoAcesso=pesquisarAluno&numeroInternoAluno="
        +$("#numeroInternoAluno").val()+"&bilheteIdentidade="+$("#bilheteIdentidade").val());
    }



    function copiarDados(){
      chamarJanelaEspera("...")
      http.onreadystatechange = function(){
        if(http.readyState==4){
            resultado = http.responseText.trim()
            fecharJanelaEspera()
            if(resultado.trim().substring(0, 1)=="F"){
              mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
            }else{
              $("#dadoTabela").html("")
              mensagensRespostas("#mensagemCerta", "Os dados foram copiados com sucesso.");
            }
        }     
      }
      enviarComGet("tipoAcesso=copiarDados&idAlunoActual="
        +idAlunoActual+"&idAlunoNovo="+idAlunoNovo);
    }

    