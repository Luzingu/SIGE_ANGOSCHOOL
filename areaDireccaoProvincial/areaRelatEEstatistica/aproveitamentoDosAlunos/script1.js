var notaMinima=10
window.onload = function(){
      
    fecharJanelaEspera();
    seAbrirMenu();
    $("#anosLectivos").val(idPAno);

    directorio = "areaDirector/reconfirmacao/";
    $("#luzingu").val(luzingu);
    $("#idPEscola").val(idPEscola);
    if(classeP<=6){
        notaMinima=5
    }

    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#aproveitamento").change(function(){
        fazerPesquisa()
    })

    $(".visualizadorMapa").click(function(){
      window.location = caminhoRecuar+"relatoriosPdf/"+$(this).attr("caminho")+"?idPCurso="+idCursoP
      +"&classe="+classeP+"&trimestreApartir="+trimestre+"&privacidade="
      +privacidade
    })
    
    $("#luzingu, #anosLectivos, #idPEscola").change(function(){
      window.location ="?luzingu="+$("#luzingu").val()
      +"&idPAno="+$("#anosLectivos").val()
      +"&idPEscola="+$("#idPEscola").val()+"&privacidade="
      +privacidade+"&trimestre="+trimestre;
    })
}

function fazerPesquisa(){
    var contagem=0;
        var html ="";
        var masculino=0;
        $(".numTMasculinos").text(masculino);

        $(".numTAlunos").text(completarNumero(alunosReconfirmados.filter(condition).length));

       alunosReconfirmados.filter(condition).forEach(function(dado){
           contagem++;
            if(dado.sexoAluno=="F"){
              masculino++;
            }
            $(".numTMasculinos").text(completarNumero(masculino))

            var varObservacao =""
            if(trimestre=="I"){
                if(dado.mfT1>=notaMinima){
                    varObservacao="<span class='text-success'>Bom</span>";
                }else{
                    varObservacao="<span class='text-danger'>Mau</span>";
                }
            }else if(trimestre=="II"){
                if(dado.mfT2>=notaMinima){
                    varObservacao="<span class='text-success'>Bom</span>";
                }else{
                    varObservacao="<span class='text-danger'>Mau</span>";
                }
            }else if(trimestre=="III"){
                if(dado.mfT3>=notaMinima){
                    varObservacao="<span class='text-success'>Bom</span>";
                }else{
                    varObservacao="<span class='text-danger'>Mau</span>";
                }
            }else{
                if(dado.observacaoF=="A" || dado.observacaoF=="TR"){
                    varObservacao="<span class='text-success'>Bom</span>";
                }else{
                    varObservacao="<span class='text-danger'>Mau</span>";
                }
            }

            html += "<tr><td class='lead text-center'>"+completarNumero(contagem)+"</td><td class='lead toolTipeImagem' imagem='"+dado.fotoAluno+"'>"+dado.nomeAluno
            +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"areaPedagogica/perfilAluno?aWRQTWF0cmljdWxh="+dado.idPMatricula+"' class='lead black'>"+dado.numeroInterno
            +"</a></td><td class='lead text-center'>"+dado.designacaoTurma
            +"</td><td class='lead text-center'>"+varObservacao+"</td></tr>";
                   
       });
       $("#tabJaReconfirmados").html(html);
}

function condition(elem, ind, arr){
    if($("#aproveitamento").val()==""){
        return true
    }else{
        var valorComparar=""
        if(trimestre=="I"){
            valorComparar=elem.mfT1
        }else if(trimestre=="II"){
            valorComparar=elem.mfT2
        }else if(trimestre=="III"){
            valorComparar=elem.mfT3
        }

        if(trimestre=="I" || trimestre=="II" || trimestre=="III"){
            if($("#aproveitamento").val()=="Bom"){
                return valorComparar>=notaMinima
            }else{
                return valorComparar<notaMinima
            }
        }else{
            if($("#aproveitamento").val()=="Bom"){
                return (elem.observacaoF=="A" || elem.observacaoF=="TR")
            }else{
                return !(elem.observacaoF=="A" || elem.observacaoF=="TR")
            }
        }
    }
}



    