  var valorMaximo = 10;
  var tipoPublicacaoPauta="";

  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaPedagogica/pautaGeral1/";
      entidade ="alunos";
      $("#luzingu").val(luzingu)
      $("#anosLectivos").val(idPAno)
      
      fazerPesquisa();

      $("#luzingu, #anosLectivos").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()+"&idPAno="+$("#anosLectivos").val()
      })
      $("#listaDisciplinas").change(function(){
        fazerPesquisa();
      })

      $("#visualizarMiniPauta").click(function(){
        var trim = $("#epocaReferencia").val();
        if(trim=="I"){
          trim=1;
        }else if(trim=="II"){
          trim=2;
        }else if(trim=="III"){
          trim=3;
        }else if(trim=="IV"){
          trim=4;
        }
        if($("#listaDisciplinas").val()!=""){
          window.location =caminhoRecuar+"relatoriosPdf/pautas/miniPautas.php?turma="+turma
          +"&classe="+classeP+"&idPCurso="+idCursoP
          +"&trimestreApartir="+trim
          +"&idPDisciplina="+$("#listaDisciplinas").val()+"&idPAno="+idPAno
          +"&dataVisualizacao="+$("#dataVisualizacao").val();
        } 
      })
      $(".visualizarPauta").click(function(){
        var trim = $("#epocaReferencia").val();
        if(trim=="I"){
          trim=1;
        }else if(trim=="II"){
          trim=2;
        }else if(trim=="III"){
          trim=3;
        }else if(trim=="IV"){
          trim=4;
        }
        window.location =caminhoRecuar+"relatoriosPdf/pautas/?turma="+turma
        +"&classe="+classeP+"&idPCurso="+idCursoP
        +"&listarTodas="+$(this).attr("tipo")+"&trimestreApartir="+trim
        +"&mesPagamentoApartir="+$("#mesPagamentoApartir").val()+"&tamanhoFolha="
        +$("#tamanhoFolha").val()+"&tipoPauta="+$("#tipoPauta").val()
        +"&resultPauta="+$("#resultPauta").val()+"&idPAno="+idPAno
        +"&dataVisualizacao="+$("#dataVisualizacao").val()
      })

      $(".visualizadorMapa").click(function(){
          window.location = caminhoRecuar+"relatoriosPdf/"+$(this).attr("caminho")+"?idPCurso="+idCursoP
          +"&classe="+classeP+"&trimestreApartir="+$("#epocaReferencia").val()
          +"&idPDisciplina="+$("#listaDisciplinas").val()+"&idPAno="+idPAno;
      })

      $("#visualizarPublicar").click(function(){
        $("#referenciaVisualizacao").text("PAUTA DO "+$("#epocaReferencia").val()
          +" TRIMESTRE")

        if($("#epocaReferencia").val()=="IV"){
          $("#referenciaVisualizacao").text("PAUTA FINAL")
        }
        $("#tipoPauta").empty()
        if($("#epocaReferencia").val()=="IV"){
          $("#tipoPauta").append("<option value='pautaGeral'>Pauta Geral</option>");
          $("#tipoPauta").append("<option value='resumo'>Resumo</option>");
          $("#tipoPauta").append("<option value='aprovGeral'>Aproveitamento Geral</option>");
        }else{
          $("#tipoPauta").append("<option value='aprovGeral'>Pauta Geral</option>");
          $("#tipoPauta").append("<option value='resumo'>Resumo</option>");
        }
        $("#publicarPautas").modal("show")      
      });

  }

    function fazerPesquisa(){

        var html="";
        var notas = new Array();

        var i=0;
        
        $("#numTotAlunos").text(completarNumero(listaAlunos.filter(fazerPesquisaCondition).filter(condicao).length));
        $("#numTotMasculino").text(0);

        var numTotAprovado=0;
        var numTotMasculino =0;
        var obs="";

      listaAlunos.filter(fazerPesquisaCondition).filter(condicao).forEach(function(dado){
        if(dado.sexoAluno=="F"){
          numTotMasculino++;
        }
        $("#numTotMasculino").text(completarNumero(numTotMasculino));

        if(dado.reconfirmacoes.observacaoF=="D"){
          obs="Desistente";
        }else if(dado.reconfirmacoes.observacaoF=="N"){
          obs="Anulado";
        }else if(dado.reconfirmacoes.observacaoF=="RI"){
          obs="Rep. I";
        }else if(dado.reconfirmacoes.observacaoF=="RF"){
          obs="Rep. F";
        }else if(dado.reconfirmacoes.observacaoF=="TR"){
          obs="Transita";                  
          numTotAprovado++;
        }else if(dado.reconfirmacoes.observacaoF=="A"){
          obs="Apto(a)";
          numTotAprovado++;
        }else{                  
          obs="N. Apto(a)";
        }
        $("#numTotAprovado").text(completarNumero(numTotAprovado));

    
        i++;
        html +='<form class="row form'+dado.numeroInterno+' formulario formularioNotas" numeroInterno="'+dado.numeroInterno
        +'" method="POST" idPPauta="'+dado.idPPauta+'">'+
        '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 lead text-center"><div class="visible-md visible-lg"><br/></div>'+completarNumero(i)+'</div>'+
        '<div class="col-lg-3 col-md-3 col-sm-11 col-xs-11 lead toolTipeImagem" imagem="'+dado.fotoAluno+'"><div class="visible-md visible-lg">'
        +'<br/></div><strong style="font-size:14pt;">'+dado.nomeAluno+
        '<a href="'
        +caminhoRecuar+'areaSecretaria/relatorioAluno?idPMatricula='+dado.idPMatricula
        +'" class="lead black"> <br/>('
        +dado.numeroInterno+')</a></strong></div>';
        
        camposAvaliacao[dado.arquivo_pautas.idPautaDisciplina].forEach(function(campo){
          html +='<div class="col-lg-1 col-md-1 col-sm-4 col-xs-4 text-center"><strong>'+
          campo.designacao1+'</strong><input type="text" class="form-control text-center inputVal mac1 lead" step="0.01" min="'+campo.notaMinima+'" max="'
          +campo.notaMaxima+'" media="'+campo.notaMedia+'" value="'+vazioNull(dado.arquivo_pautas[campo.identUnicaDb])+'" disabled style="font-size:13pt; font-weight:600; padding:0px;"></div>'
        })

        html +='<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center">'+
        '<strong>OBS. F</strong><input readonly type="text" value="'+obs
        +'" class="form-control text-center observacaoF inputVal lead"></div>'
        html +='</form>';
      });
    $("#listaAlunos").html(html);
    corNotasVermelhaAzul("#listaAlunos form")
  }

  function condicao(elem, ind, obj){
      return elem.arquivo_pautas.idPautaDisciplina == $("#listaDisciplinas").val()    
  }