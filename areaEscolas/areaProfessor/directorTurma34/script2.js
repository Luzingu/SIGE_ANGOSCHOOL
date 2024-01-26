  var valorMaximo = 10;
  var tipoPublicacaoPauta="";

  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaProfessor/directorTurma34/";
      entidade ="alunos";
      $("#luzingu").val(luzingu)
      $("#turma").val(turma)
      $("#epocaReferencia").val(trimestreDefault)
      if(classeP<=6){
        valorMaximo=10;
      }else{
        valorMaximo=20;
      }
      fazerPesquisa();

      $("#luzingu").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()
      })
      $("#listaDisciplinas").change(function(){
        fazerPesquisa();
      })

      $("#horarioTurma").click(function(){
        window.location =caminhoRecuar+"relatoriosPdf/horarioTurmas/index.php?turma="+turma
            +"&classe="+classeP+"&idPCurso="+idCursoP;
      })

      $("#imprimirlistaAlunos").click(function(){
        window.location =caminhoRecuar+"relatoriosPdf/exemplarLitaTurmas/listaTurmas.php/?turma="+turma
          +"&classe="+classeP+"&idPCurso="+idCursoP;
      })

      $(".imprimirPauta").click(function(){
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
        +"&classe="+classeP+"&idPCurso="+idCursoP+"&trimestreApartir="+trim
        +"&mesPagamentoApartir=0&tamanhoFolha="+$(this).attr("tamanhoFolha")
        +"&tipoPauta="+$(this).attr("tipoPauta")+"&resultPauta=definitivo";
      })
  }

    function fazerPesquisa(){

        var html="";
        var notas = new Array();

        var i=0;
        
        $("#numTotMasculino").text(0);

        $("#numTotAlunos").text(0);
        $("#numTotMasculino").text(0);            
        $("#numTotAprovado").text(0);

      var numTotAprovado=0;
      var numTotMasculino =0;
      var numTot=0

      listaAlunos.filter(condicao).filter(fazerPesquisaCondition).forEach(function(dado){

        var obs="";
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
          if(dado.reconfirmacoes.seAlunoFoiAoRecurso=="A"){
            obs="Recurso";
          }
        }

            if(dado.sexoAluno=="F"){
              numTotMasculino++;
            }
            numTot++;
            $("#numTotAlunos").text(completarNumero(numTot));
            $("#numTotMasculino").text(completarNumero(numTotMasculino));            
            $("#numTotAprovado").text(completarNumero(numTotAprovado));

        
            i++;
            html +='<form class="row form'+dado.numeroInterno+' formulario formularioNotas" numeroInterno="'+dado.numeroInterno
            +'" method="POST">'+
            '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 lead text-center"><div class="visible-md visible-lg"><br/></div>'+completarNumero(i)+'</div>'+
            '<div class="col-lg-3 col-md-3 col-sm-11 col-xs-11 lead toolTipeImagem" imagem="'+dado.fotoAluno+'"><div class="visible-md visible-lg">'
            +'<br/></div><strong style="font-size:14pt;">'+dado.nomeAluno+
            '<a href="'
            +caminhoRecuar+'areaSecretaria/relatorioAluno?idPMatricula='+dado.idPMatricula
            +'" class="lead black"> <br/>('
            +dado.numeroInterno+')</a></strong></div>';

            camposAvaliacao[dado.pautas.idPautaDisciplina].forEach(function(campo){

              html +='<div class="col-lg-1 col-md-1 col-sm-4 col-xs-4 text-center"><strong>'+
              campo.designacao1+'</strong><input type="text" class="form-control text-center inputVal mac1 lead" step="0.01" min="'+campo.notaMinima+'" max="'
              +campo.notaMaxima+'" media="'+campo.notaMedia
              +'" value="'+vazioNull(dado.pautas[campo.identUnicaDb])+'" disabled style="font-size:13pt; font-weight:600; padding:0px;"></div>'
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
      return elem.pautas.idPautaDisciplina== $("#listaDisciplinas").val()  
  }
