    var idPMatricula="";
    window.onload = function(){
        fecharJanelaEspera();
        seAbrirMenu();
        entidade ="alunos";
        directorio = "areaSecretaria/novaTrasferencia/";
        $("#luzingu").val(luzingu);

        selectProvincias("#novaTrasferencia #pais", 
          "#novaTrasferencia #nomeProvincia", "#novaTrasferencia #nomeMunicipio",
           "#novaTrasferencia #nomeComuna");
        
        fazerPesquisa();
        DataTables("#example1", "sim")

        $("#seTransferenciaLocal").change(function(){
            if($(this).prop("checked")==true){
              $("#nomeEscolaOption").attr("required", "");
              $("#novaTrasferencia .paraOutrasEscolas").removeAttr("required");
              $("#nomeEscolaOption").show();
              $("#novaTrasferencia .paraOutrasEscolas").hide();
            }else{
               $("#nomeEscolaOption").removeAttr("required")
              $("#nomeEscolaOption").hide();
              $("#novaTrasferencia .paraOutrasEscolas").attr("required", "");
              $("#novaTrasferencia .paraOutrasEscolas").show();
            }
        })

        $("#luzingu").change(function(){
            window.location ="?luzingu="+$("#luzingu").val();
        })

        var repet=true;
        $("#tabJaReconfirmados").bind("mouseenter click", function(){ 
            repet=true;
            $("#tabJaReconfirmados a.trasnferir").click(function(){
              if(repet==true){
                idPMatricula = $(this).attr("idPMatricula");
                limparFormulario("#formNovaTransferencia #formNovaTransferencia");
                $("#nomeEscolaOption").attr("required", "");
                 $("#nomeEscolaOption").show();
                $("#novaTrasferencia #seTransferenciaLocal").prop("checked", true);
                $("#novaTrasferencia .paraOutrasEscolas").hide();
                $("#novaTrasferencia .paraOutrasEscolas").removeAttr("required");
                $("#documentosAnexos").val("");
                $("#nomeEscolaCaixa").val("");
                $("#numeroTransferencia").val("");

                alunosReconfirmados.forEach(function(dado){
                  if(dado.idPMatricula==idPMatricula){
                      $("#formNovaTransferencia .nomeAluno").val(dado.nomeAluno)
                     $("#formNovaTransferencia .numeroConta").val(dado.numeroInterno)
                     $("#formNovaTransferencia #idPMatricula").val(dado.idPMatricula);
                     $("#formNovaTransferencia #turma").val(dado.reconfirmacoes.nomeTurma);
                     $("#classe").val(dado.reconfirmacoes.classeReconfirmacao);
                     $("#idMatCurso").val(dado.escola.idMatCurso)
                  }
                })
                $("#novaTrasferencia").modal("show");
                repet=false;
              }                
            })
        })

        $("#formNovaTransferencia").submit(function(){
          $(".modal").modal("hide");
          transferirAluno();
          return false;
        })


}
    
    function fazerPesquisa(){
        var contagem=0;
        var html ="";
        var masculino=0;
        $(".numTAlunos").text(completarNumero(alunosReconfirmados.length));
        $(".numTMasculinos").text(completarNumero(masculino)); 
        alunosReconfirmados.forEach(function(dado){
            if(dado.sexoAluno=="F"){
              masculino++;
            }
            $(".numTMasculinos").text(completarNumero(masculino))
           contagem++;

            html += "<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
            +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno
            +"</a></td><td class='lead text-center'>"+vazioNull(dado.reconfirmacoes.designacaoTurma)
            +"</td><td class='text-center'><a href='#' class='lead text-success trasnferir' title='Transferir' idPMatricula='"+dado.idPMatricula+"'><i class='fa fa-check-circle'></i></a></td></tr>";
                  
       });
       $("#tabJaReconfirmados").html(html);
    };


    function transferirAluno(){
      chamarJanelaEspera("Transferindo...");
     http.onreadystatechange = function(){
        if(http.readyState==4){
          fecharJanelaEspera();
          resultado = http.responseText.trim()
          if(resultado.substring(0, 1)=="F"){
            mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
          }else{               
            mensagensRespostas('#mensagemCerta', "A transferência foi processada com sucesso");
            alunosReconfirmados = JSON.parse(resultado)
            fazerPesquisa();
          }
        }
      }
      var form = new FormData(document.getElementById("formNovaTransferencia"));
      enviarComPost(form);
    }
    