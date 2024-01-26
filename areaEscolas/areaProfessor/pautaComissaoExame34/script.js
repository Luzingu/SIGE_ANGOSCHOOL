  var idPProfessor ="";
  var posicaoArray ="";
  var valoresEnviar = new Array();

  var idPCurso="";
  var classe="";
  var turma="";
  var idPDisciplina="";
  var tipoCurso="";
  var seFezAlteracao="nao"
  var camposAvaliacao= new Array()

  window.onload=function(){
       
      fecharJanelaEspera();
      seAbrirMenu();
        
      directorio = "areaProfessor/miniPautasProfessor34/";
      entidade ="alunos";

      $(".btnAlterarNotas").click(function(){        
        irmaNeusa();              
      })
      $("#listaAlunos").bind("click mouseenter", function(){
        $("#listaAlunos input[type=number]").bind("keyup change", function(){
          seFezAlteracao="sim"
          $("form[idPMatricula="+$(this).attr("idPMatricula")+"]").attr("alterou", "sim")
          $("div.forPagination").hide(200)
          $(".btnAlterarNotas").show(200)
        })
      })

      $("form").submit(function(){
        irmaNeusa();
        return false;
      });
      change();

      $("#referenciaDisciplina").change(function(){
        change();
      });

      $("#visualizarMiniPauta").click(function(){
        trimestreEnviar=4;
        if(idPDisciplina!=""){
          window.location =caminhoRecuar+"relatoriosPdf/pautas/miniPautas.php?turma="+turma
          +"&classe="+classe+"&idPCurso="+idPCurso
          +"&trimestreApartir="+trimestreEnviar+"&idPDisciplina="+idPDisciplina;
        }
      })

  }

  function change(){
    idPCurso = $("#referenciaDisciplina option:selected").attr("idPCurso");
    classe = $("#referenciaDisciplina option:selected").attr("classe");
    turma = $("#referenciaDisciplina option:selected").attr("turma");
    idPDisciplina = $("#referenciaDisciplina option:selected").attr("idPNomeDisciplina");
 
    tipoCurso = $("#referenciaDisciplina option:selected").attr("tipoCurso");
    semestreSeleccionada = $("#referenciaDisciplina option:selected").attr("semestreSeleccionada")
    sePorSemestre=$("#referenciaDisciplina option:selected").attr("sePorSemestre")

    $("#formularioDados #trimestre").val(trimestre)
    $("#formularioDados #idPCurso").val(idPCurso)
    $("#formularioDados #classe").val(classe)
    $("#formularioDados #turma").val(turma)
    $("#formularioDados #idPNomeDisciplina").val(idPDisciplina)
    $("#formularioDados #tipoCurso").val(tipoCurso)
    $("#formularioDados #semestreActivo").val(semestreSeleccionada) 
 
    $("#nomeCursoEtiqueta").text($("#referenciaDisciplina option:selected").attr("nomeCurso"))
    $("#classeEtiqueta").text($("#referenciaDisciplina option:selected").attr("classeExtensa"))
    $("#turmaEtiqueta").text($("#referenciaDisciplina option:selected").attr("designacaoTurmaDiv"))
    $("#nomeDisciplinaEtiqueta").text($("#referenciaDisciplina option:selected").attr("nomeDisciplina"))
    $("#semestreDisciplinaEtiqueta").text(semestreSeleccionada+" Semestre")
    $("#areaFormacaoCursoEtiqueta").text($("#referenciaDisciplina option:selected").attr("areaFormacaoCurso"))
    
    if(classe<=6){
      valorMaximo=10;
    }else{
      valorMaximo=20;
    }
    buscarNotas()
  }
  
  function fazerPesquisa(){
    modelo_mod_2020();
    corNotasVermelhaAzul("#listaAlunos form", valorMaximo)
  };

  function modelo_mod_2020(){
    var trim ="";
    if(trimestre=="I"){
      trim=1;
    }else if(trimestre=="II"){
      trim=2;
    }else if(trimestre=="III"){
      trim=3;
    }
    $("#cabecalhoTable").show();
    var html="";
    var notas = new Array();
    var i=0;
     
    $("#numTotAlunos").text(completarNumero(pautas.filter(fazerPesquisaCondition).length));
    $("#numTotMasculino").text(0);
    var numTotAprovado=0;
    var numTotMasculino =0;
    var contagem=-1;
    if(jaTemPaginacao==false){
      paginacao.baraPaginacao(pautas.filter(fazerPesquisaCondition).length, 25);
    }else{
        jaTemPaginacao=false;
    }
    var i=paginacao.comeco;

    pautas.filter(fazerPesquisaCondition).forEach(function(dado){
      contagem++
      if(dado.sexoAluno=="F"){
        numTotMasculino++;
      }
      $("#numTotMasculino").text(completarNumero(numTotMasculino));

      if(dado.pautas.mf>=10){
        numTotAprovado++;
      }
      $("#numTotAprovado").text(completarNumero(numTotAprovado));

      if(contagem>=paginacao.comeco && contagem<=paginacao.final){
        i++
        html +='<form class="row form'+dado.numeroInterno+' formularioNotas" idPMatricula="'+
        dado.idPMatricula+'" idPDisciplina="'
        +dado.pautas.idPautaDisciplina+'" numeroInterno="'+dado.numeroInterno+'">'+
          '<div class="col-lg-1 col-md-1 col-sm-2 col-xs-2 lead text-center"><div class="visible-md visible-lg"><br/></div>'+completarNumero(i)+'</div>'+
          '<div class="col-lg-6 col-md-6 col-sm-10 col-xs-10 lead nomeAluno toolTipeImagem" imagem="'+dado.fotoAluno
          +'"><div class="visible-md visible-lg"><br/></div><strong style="font-size:20pt;">'+
          dado.nomeAluno+'</strong></div>'

        camposAvaliacao.forEach(function(campo){
          var seApenasLeitura="";
          if(campo.seApenasLeitura=="V"){
            seApenasLeitura="readonly"
          }
          html +='<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 text-center"><strong>'+campo.designacao1+'</strong><input type="number" idPMatricula="'+dado.idPMatricula
          +'" name="'+campo.identUnicaDb+'" designacao="'+campo.designacao1+'" idCampoAvaliacao="'+campo.idCampoAvaliacao+'" tipoCampo="'+campo.tipoCampo
          +'" class="form-control avaliacaoQuantitativa text-center inputVal lead" step="0.01" min="'+campo.notaMinima+'" media="'+campo.notaMedia+'" max="'
          +campo.notaMaxima+'" value="'+vazioNull(dado.pautas[campo.identUnicaDb])+'" '+seApenasLeitura+' style="font-size:13pt; font-weight:600; padding:0px;"></div>'
        })

        html +='</form>';
      }
    });
    $("#listaAlunos").html(html);
  }

  function irmaNeusa(){
    valoresEnviar = new Array();

    var nomeCampoComErroEncontrado="";
    var msgErro="";
     $("form.formularioNotas[alterou=sim]").each(function(){
        var numeroInterno = $(this).attr("numeroInterno")

          var avaliacoesQuantitativas=new Array();
          $("form[numeroInterno="+numeroInterno+"] input.avaliacaoQuantitativa[tipoCampo=exame]").each(function(){
            avaliacoesQuantitativas.push({name:$(this).attr("name"), valor:$(this).val()
            , idCampoAvaliacao:$(this).attr("idCampoAvaliacao")})
          })

          valoresEnviar.push({idPMatricula:$(this).attr("idPMatricula"),
          idPDisciplina:$(this).attr("idPDisciplina"),
          avaliacoesQuantitativas:avaliacoesQuantitativas})

        $("form[numeroInterno="+numeroInterno+"] input[type=number]").each(function(){
            
            //Avaliando os dados do formulário...
            if($(this).attr("required")=="required" && $(this).val().trim()==""){
              nomeCampoComErroEncontrado = $(this).attr("designacao")
              $(this).focus()
              $(this).css("border", "solid red 1px");
              msgErro ="são obrigatórios.";
            }

            //Fazer a segunda avaliacao...
            if(nomeCampoComErroEncontrado==""){
              if($(this).attr("min")!=undefined && 
                $(this).val().trim()!="" && new Number($(this).val())<new Number($(this).attr("min"))){

                nomeCampoComErroEncontrado = $(this).attr("designacao")
                $(this).focus()
                $(this).css("border", "solid red 1px");
                msgErro ="não devem ser inferior que "+$(this).attr("min")+"."; 
              }
            }

            //Fazer a terceira avaliacao...
            if(nomeCampoComErroEncontrado==""){
              if($(this).attr("max")!=undefined && 
                $(this).val().trim()!="" && new Number($(this).val())>new Number($(this).attr("max"))){
                nomeCampoComErroEncontrado = $(this).attr("designacao")
                $(this).focus()
                $(this).css("border", "solid red 1px");
                msgErro ="não devem ser superior que "+$(this).attr("max")+".";
              }
            }
        })
     })
      if(seFezAlteracao=="nao"){
        mensagensRespostas2("#mensagemErrada", "Não fizeste nenhuma alteração.")
      }else if(nomeCampoComErroEncontrado!=""){
          mensagensRespostas2("#mensagemErrada", "As notas da "+nomeCampoComErroEncontrado+" "+msgErro) 
      }else{
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao"
          manipularNotas()
        }        
      }   
  }

  function manipularNotas(){
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
      if(http.readyState==4){          
        resultado = http.responseText.trim()
        $("div.forPagination").show(500)
        $(".btnAlterarNotas").hide(500)
        estadoExecucao="ja";
        fecharJanelaEspera();
        seFezAlteracao="sim"
        if(resultado.substring(0,1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length))
          fazerPesquisa()
        }else if(resultado.trim()!=""){
          resultado = JSON.parse(resultado)
          exibirMensagens(resultado[2]);
          pautas = resultado[1]
          fazerPesquisa()                       
        }
      }     
    }
    $("#formularioDados #action").val("alterarNotas")
    $("#formularioDados #dados").val(JSON.stringify(valoresEnviar))
    var form = new FormData(document.getElementById("formularioDados"))
    enviarComPost(form)
  }
  function buscarNotas(){
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera();
        $("div.forPagination").show(500)
        $(".btnAlterarNotas").hide(500)
        resultado = JSON.parse(http.responseText.trim())
        pautas = resultado[1]
        camposAvaliacao = resultado[0]
        fazerPesquisa()
      }
    }
    enviarComGet("tipoAcesso=buscarNotas&idPCurso="+idPCurso+"&classe="+
      classe+"&turma="+turma+"&idPDisciplina="+idPDisciplina+"&trimestre=IV"
      +"&areaEmExecucao=exame");
  }

  function exibirMensagens(resultadoManipulacao){
    if(resultadoManipulacao!=""){
      resultadoManipulacao.forEach(function(dado){
          var msg = dado.msg;
          if(msg.substring(0,1)!="V"){
            encontrouNotaNaoAlterada="sim";
            mensagensRespostas("#mensagemErrada", msg);                  
          }else{
            encontrouNotaNaoAlterada="1";
          }              
      }) 
      if(encontrouNotaNaoAlterada=="nao"){
        mensagensRespostas("#mensagemCerta", "Todas notas foram alteradas com sucesso.");
      }else if(encontrouNotaNaoAlterada=="1"){
        mensagensRespostas("#mensagemCerta", "As notas foram alteradas com sucesso.");
      }
    }
  }