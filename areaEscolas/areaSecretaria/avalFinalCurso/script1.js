var idPMatricula="";

  window.onload = function (){

    directorio = "areaSecretaria/avalFinalCurso/";

    fecharJanelaEspera();
    seAbrirMenu();

    $("#luzingu").val(luzingu);

    entidade="alunos";
    fazerPesquisa();
    $("#example1").DataTable({
        "responsive": true, "lengthChange": true, "autoWidth": false,
        "buttons": ["copy", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $("#actualizar").click(function(){
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao";
          gravarNotaAptidao();
        }        
    })

    $("#luzingu").change(function(){
        window.location ="?luzingu="+$("#luzingu").val();
    });

    var repet1=true;
    $("#tabela").bind("click mouseenter", function(){
      repet1=true;
      $("#tabela tr td a.alterar").click(function(){
          if(repet1==true){
              idPMatricula = $(this).attr("idPMatricula");

              listaAlunos.forEach(function(dado){
                if(dado.idPMatricula==idPMatricula){
                  $("#formularioCadastro #nomeAluno").val(dado.nomeAluno);
                  $("#formularioCadastro #idPMatricula").val(dado.idPMatricula);
                  $("#formularioCadastro #idPMatricula").val(dado.idPMatricula);
                  $("#notaExpoTrabalho").val(dado.escola.notaExposicaoW);

                  $("#notaTrabEscrito").val(dado.escola.notaAvalTrabEscrito);
                  $("#notaEstagio").val(dado.escola.notaEstagio);
                  $("#notaRelatorioEstagio").val(dado.escola.notaRelatorioEstagio);
                  
                  $("#numeroActa").val(dado.escola.numeroActa);
                  $("#numeroFolha").val(dado.escola.numeroFolha);
                  $("#dataDefesa").val(dado.escola.dataDefesa);
                  $("#horaDefesa").val(dado.escola.horaDefesa);
                  $("#membrosJuri").val(dado.escola.membrosJuriDefesa);
                  $("#temaTrabalho").val(dado.escola.temaTrabalho);
                  $("#casoPratico").val(dado.escola.casoPratico);

                  $("#dataConclusao").val(dado.escola.dataConclusaoCurso);
                  $("#numeroLivroRegistro").val(dado.numeroLivroRegistro);
                  $("#numeroFolhaRegistro").val(dado.escola.numeroFolhaRegistro);
                  $("#numeroPauta").val(dado.escola.numeroPauta);
                  $("#grupoAluno").val(dado.grupo);
                }
              })
              $("#formularioCadastro").modal("show");
                                
              repet1=false;
          }
      });
    });

    $("#avalFinalCursoF").submit(function(){
      manipularNotaAptidao();
      return false;
    })
  }

  
  function fazerPesquisa(){

    $("#numTAlunos").text(0);
    $("#numTMasculinos").text(0);
    $("#numTAlunos").text(completarNumero(listaAlunos.length));
    $("#numTMasculinos").text(0);

    var html = "";
    var contagem=0;
    var numM=0;

    listaAlunos.forEach(function(dado){
      contagem++;
      if(dado.sexoAluno=="F"){
          numM++;
      }

      $("#numTMasculinos").text(completarNumero(numM));
      

      html +="<tr><td class='lead text-center'>"+completarNumero(contagem)
      +"</td><td imagem='"+dado.fotoAluno+"' class='lead toolTipeImagem'><a href='"+caminhoRecuar+"areaSecretaria/relatorioAluno?idPMatricula="+dado.idPMatricula+"' class='lead black'>"+dado.nomeAluno
      +"</a></td><td class='lead text-center'>"+
      vazioNull(dado.escola.provAptidao)+"</td><td class='lead text-center inputVal'>"+
      vazioNull(dado.escola.notaEstagio)+"</td><td class='lead text-center'>"+
      converterData(vazioNull(dado.escola.dataConclusaoCurso))
      +"</td><td class='lead text-center'><a href='"+caminhoRecuar+"relatoriosPdf/actaDefesa/?idPMatricula="+dado.idPMatricula
      +"'><i class='fa fa-print'></i> Acta</a>"+
      "</td><td><a href='#' class='alterar' idPMatricula='"+dado.idPMatricula
      +"'><i class='fa fa-check' title='Alterar' ></i></a></td></tr>";
    })
    $("#tabela").html(html);
    corCelula();
  }

  function corCelula (){
    $("#tabela .inputVal").each(function(i){
        if($(this).text().trim()<10 && $(this).text().trim()!=""){
            $(this).css("color", "red");
        }
    });
  }

  function manipularNotaAptidao(){ 
    $("#formularioCadastro").modal("hide");
    chamarJanelaEspera("...") 
    enviarComPost(new FormData(document.getElementById("avalFinalCursoF")))

    http.onreadystatechange = function(){
      if(http.readyState==4){
        estadoExecucao ="ja";
        resultado = http.responseText.trim()
        fecharJanelaEspera();            
        if(resultado.substring(0, 1).trim()=="F"){
          mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length));
        }else{
          mensagensRespostas('#mensagemCerta', "Os dados foram actualizados com sucesso.");
          listaAlunos = JSON.parse(resultado);
          fazerPesquisa(); 
        }
        
      }    
    }
  }


