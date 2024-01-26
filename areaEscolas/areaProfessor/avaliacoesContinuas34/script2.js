  var idPProfessor ="";
  var valorMaximo = 10;
  var posicaoArray ="";
  var valoresEnviar = new Array();

  var idPCurso="";
  var classe="";
  var turma="";
  var idPDisciplina="";
  var tipoCurso="";
  var aperteiBotao=false
  var semestreSeleccionada=""
  var seFezAlteracao="nao"

  window.onload=function(){
       
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaProfessor/miniPautasProfessor34/";
    entidade ="alunos";
    $(".btnAlterarNotas").click(function(){        
      irmaNeusa();              
    })

    $("#numeroAvalContinuas").bind("keyup change", function(){
      aperteiBotao=true
      fazerPesquisa()
    })

    $("#listaAlunos").bind("click mouseenter", function(){
      $("form input[type=number]").bind("keyup change", function(){
        seFezAlteracao="sim"
        $("form[idPMatricula="+$(this).attr("idPMatricula")+"]").attr("alterou", "sim")
        $("div.forPagination").hide(200)
        $(".btnAlterarNotas").show(200)
      })
    })

    $("form").submit(function(){
      irmaNeusa();
      return false;
    })
    change();

    $("#referenciaDisciplina").change(function(){
      change();
    });

    $("#visualizarMiniPauta").click(function(){
      var trimestreEnviar="";
      if(trimestre=="I"){ 
        trimestreEnviar=1;
      }else if(trimestre=="II"){
        trimestreEnviar=2;
      }else if(trimestre=="III"){
        trimestreEnviar=3;
      }
      if(idPDisciplina!=""){
        window.location =caminhoRecuar+"relatoriosPdf/pautas/mapaAvaliacoesContinuas.php?turma="+turma
        +"&classe="+classe+"&idPCurso="+idPCurso
        +"&trimestreApartir="+trimestreEnviar+"&idPDisciplina="+idPDisciplina+"&semetre="+semestreSeleccionada
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
    $("#semestreDisciplinaEtiqueta").text(semestreSeleccionada+" Semestre")
    $("#nomeDisciplinaEtiqueta").text($("#referenciaDisciplina option:selected").attr("nomeDisciplina"))

    $("#areaFormacaoCursoEtiqueta").text($("#referenciaDisciplina option:selected").attr("areaFormacaoCurso"))
    if(classe<=6){
      valorMaximo=10;
    }else{
      valorMaximo=20;
    }
    buscarNotas()
    $("#listaAlunos assaa").focus()
  }
  
  function fazerPesquisa(){
    modelo_mod_2020();
    corNotasVermelhaAzul("#listaAlunos form")
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
      $("#paraPaginacao").show();
      var html="";
      var notas = new Array();

      var i=0;         
      $("#numTotAlunos").text(completarNumero(pautas.filter(fazerPesquisaCondition).length));
      $("#numTotFeminino").text(0);
      var numTotAprovado=0;
      var numTotFeminino =0;
      var contagem=-1;
      if(jaTemPaginacao==false){
        paginacao.baraPaginacao(pautas.filter(fazerPesquisaCondition).length, 25);
      }else{
          jaTemPaginacao=false;
      }
      var i=paginacao.comeco;

      pautas.filter(fazerPesquisaCondition).forEach(function(dado){
          if(dado.sexoAluno=="F"){
            numTotFeminino++;
          }
          $("#numTotFeminino").text(completarNumero(numTotFeminino));

          if(trimestre=="I" && dado.pautas.mtI>=10){
            numTotAprovado++;
          }else if(trimestre=="II" && dado.pautas.mtII>=10){
            numTotAprovado++;
          }else if(trimestre=="III" && dado.pautas.mtIII>=10){
            numTotAprovado++;
          }else if(dado.pautas.mf>=10){
            numTotAprovado++;
          }
          
          $("#numTotAprovado").text(completarNumero(numTotAprovado));

        contagem++
        if(contagem>=paginacao.comeco && contagem<=paginacao.final){
          
          i++;
          var avaliacoesContinuas =""
          var mac = "1"
          if(trimestre=="I"){
            avaliacoesContinuas = dado.pautas.avaliacoesContinuasI
            mac = dado.pautas.macI
          }else if(trimestre=="II"){
            avaliacoesContinuas = dado.pautas.avaliacoesContinuasII
            mac = dado.pautas.macII
          }else if(trimestre=="III"){
            avaliacoesContinuas = dado.pautas.avaliacoesContinuasIII
            mac = dado.pautas.macIII
          }
          if(mac==null){
            mac="";
          }
          if(avaliacoesContinuas==null){
            avaliacoesContinuas=""
          }
          avalContinuas = avaliacoesContinuas.split("-")
          if(aperteiBotao==false){
            $("#numeroAvalContinuas").val(avalContinuas[0])
          }
          
          var n = 0;
          while(n<20){
            notas[n]="";
            n++
          }
          for(var n=0; n<(avalContinuas.length); n++){
            notas[n]=avalContinuas[n]
          }
          

          html +='<form class="row form'+dado.numeroInterno+' formularioNotas" idPMatricula="'+
          dado.idPMatricula+'"  idPDisciplina="'+dado.pautas.idPautaDisciplina
          +'" numeroInterno="'+dado.numeroInterno+'">'+
          '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 lead text-center"><div class="visible-md visible-lg"><br/></div>'+completarNumero(i)+'</div>'+
          '<div class="col-lg-5 col-md-5 col-sm-11 col-xs-11 lead toolTipeImagem nomeAluno" imagem="'+dado.fotoAluno+'"><div class="visible-md visible-lg"><br/></div><strong style="font-size:20pt;">'+dado.nomeAluno+'</strong></div>'

          for(contador=1; contador<=$("#numeroAvalContinuas").val(); contador++){
            if(contador<=12){
              html +='<div class="col-lg-1 col-md-1 col-sm-4 col-xs-4 text-center"><strong>'+contador+'.ª Aval.</strong><input type="number" idPMatricula="'+dado.idPMatricula+'" value="'+
              notas[contador]+'" name="aval'+contador
              +'" style="font-size:13pt; font-weight:600; padding:0px;" class="form-control text-center inputVal lead" step="0.01" min="0" media="'+(valorMaximo/2)+'" max="'+valorMaximo
              +'"></div>'
            }
            
          }
          html +='<div class="col-lg-1 col-md-1 col-sm-4 col-xs-4 text-center"><strong>MAC'+trim+'</strong><input type="text" value="'+mac
          +'" media="'+(valorMaximo/2)+'" disabled class="form-control text-center valorCt inputVal lead"></div>';
          html +='</form>';
        }
      })
      $("#listaAlunos").html(html)
  }

  function irmaNeusa(){
    valoresEnviar = new Array();

    var nomeCampoComErroEncontrado="";
    var msgErro="";
     $("form.formularioNotas").each(function(){
        var numeroInterno = $(this).attr("numeroInterno")

          valoresEnviar.push({idPDisciplina:$(this).attr("idPDisciplina"),
          idPMatricula:$(this).attr("idPMatricula"), 
          numeroAvalContinuas:$("#numeroAvalContinuas").val(), 
          aval1:$("form[numeroInterno="+numeroInterno+"] input[name=aval1]").val(), 
          aval2:$("form[numeroInterno="+numeroInterno+"] input[name=aval2]").val(), 
          aval3:$("form[numeroInterno="+numeroInterno+"] input[name=aval3]").val(), 
          aval4:$("form[numeroInterno="+numeroInterno+"] input[name=aval4]").val(), 
          aval5:$("form[numeroInterno="+numeroInterno+"] input[name=aval5]").val(), 
          aval6:$("form[numeroInterno="+numeroInterno+"] input[name=aval6]").val(), 
          aval7:$("form[numeroInterno="+numeroInterno+"] input[name=aval7]").val(), 
          aval8:$("form[numeroInterno="+numeroInterno+"] input[name=aval8]").val(), 
          aval9:$("form[numeroInterno="+numeroInterno+"] input[name=aval9]").val(), 
          aval10:$("form[numeroInterno="+numeroInterno+"] input[name=aval10]").val(), 
          aval11:$("form[numeroInterno="+numeroInterno+"] input[name=aval11]").val(), 
          aval12:$("form[numeroInterno="+numeroInterno+"] input[name=aval12]").val()})

        $("form[numeroInterno="+numeroInterno+"] input[type=number]").each(function(){
            
            //Avaliando os dados do formulário...
            if($(this).attr("required")=="required" && $(this).val().trim()==""){
              nomeCampoComErroEncontrado = $(this).attr("name")
              $(this).focus()
              $(this).css("border", "solid red 1px");
              msgErro ="são obrigatórios.";
            }

            //Fazer a segunda avaliacao...
            if(nomeCampoComErroEncontrado==""){
              if($(this).attr("min")!=undefined && 
                $(this).val().trim()!="" && new Number($(this).val())<new Number($(this).attr("min"))){

                nomeCampoComErroEncontrado = $(this).attr("name")
                $(this).focus()
                $(this).css("border", "solid red 1px");
                msgErro ="não devem ser inferior que "+$(this).attr("min")+"."; 
              }
            }

            //Fazer a terceira avaliacao...
            if(nomeCampoComErroEncontrado==""){

              if($(this).attr("max")!=undefined && 
                $(this).val().trim()!="" && new Number($(this).val())>new Number($(this).attr("max"))){
                nomeCampoComErroEncontrado = $(this).attr("name")
                $(this).focus()
                $(this).css("border", "solid red 1px");
                msgErro ="não devem ser superior que "+$(this).attr("max")+".";
              }
            }

        })
     })

      if($("#numeroAvalContinuas").val()<1){
        $("#numeroAvalContinuas").focus()
        mensagensRespostas2("#mensagemErrada",
          "O número das avaliações deve ser pelo menos de 1.")
      }else if($("#numeroAvalContinuas").val()>12){
        $("#numeroAvalContinuas").focus()
        mensagensRespostas2("#mensagemErrada",
          "O número das avalição não deve ser mais de 8.")
      }else if(nomeCampoComErroEncontrado!=""){
        mensagensRespostas2("#mensagemErrada", "As notas das avaliações "+msgErro) 
      }else{
        if(estadoExecucao=="ja"){
          estadoExecucao="aindaNao"
          manipularNotas();
        }        
     }   
  }

  function manipularNotas(){
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
      if(http.readyState==4){          
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        seFezAlteracao="nao"
        $("div.forPagination").show(500)
        $(".btnAlterarNotas").hide(500)
        fecharJanelaEspera()
        aperteiBotao=false
        if(resultado.substring(0,1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
          fazerPesquisa()
        }else if(resultado.trim()!=""){
          resultado = JSON.parse(resultado)
          exibirMensagens(resultado[2]);
          pautas = resultado[1]
          fazerPesquisa()
        }
      }     
    }
    $("#formularioDados #action").val("alterarAvaliacoesContinuas")
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
        resultado = http.responseText.trim()
        pautas = JSON.parse(resultado)[1]
        fazerPesquisa()
      }
    }
    enviarComGet("tipoAcesso=buscarNotas&idPCurso="+idPCurso+"&classe="+
      classe+"&turma="+turma+"&idPDisciplina="+idPDisciplina+"&areaEmExecucao=avalContinuas&trimestre="+trimestre);
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