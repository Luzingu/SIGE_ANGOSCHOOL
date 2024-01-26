  var valorMaximo = 20;
  var valoresEnviar = new Array();
  var seFezAlteracao="nao"

  window.onload=function(){
       
      fecharJanelaEspera();
      seAbrirMenu();
        
      directorio = "areaPedagogica/pautaRecurso/"
      entidade ="alunos";
      $("#luzingu").val(luzingu);
      $("#anosLectivos").val(idPAno)
      fazerPesquisa()

      $("#luzingu, #anosLectivos").change(function(){
        window.location ="?luzingu="+$("#luzingu").val()+"&idPAno="+$("#anosLectivos").val();
      })

      $(".btnAlterarNotas").click(function(){        
        irmaNeusa();              
      })

      var repet=true
      $("#tabela").bind("click mouseenter", function(){
        repet=true
        $("input").bind("keyup change", function(){
            id1 = $(this).attr("idPDisciplina")
            id2 = $(this).attr("idPMatricula")

            $("tr[idPDisciplina="+id1+"][idPMatricula="+id2+"]").attr("alterou", "sim")
            seFezAlteracao="sim"  
        })
      })
    }

  function fazerPesquisa(){
    var html="";
    var contagem=0;
    pautas.forEach(function(dado){
      contagem++;
      var mf = dado.pautas.mf
      if(tipoCurso=="tecnico" && campoAvaliar=="cfd"){
        mf=dado.pautas.cf
      }
      html +='<tr idPMatricula="'+
      dado.idPMatricula+'" grupo="'+dado.grupo+'" idPDisciplina="'
      +dado.idPNomeDisciplina
      +'" >'+
      '<td class="lead">'+completarNumero(contagem)+'</td>'+
      '<td class="lead">'+dado.nomeAluno+'</td>'+
      '<td class="lead text-center"><a href="'+caminhoRecuar+'areaSecretaria/relatorioAluno?idPMatricula='+dado.idPMatricula
      +'" class="lead black">'+dado.numeroInterno+'</a></td>'+
      '<td class="lead">'+dado.nomeDisciplina+'</td>'+
      '<td class="lead text-center textVal">'+mf+'</td>'+
      '<td class="lead text-center"><input type="number" class="form-control inputVal text-center'+
      '" value="'+dado.pautas.recurso+'" idPDisciplina="'+dado.idPNomeDisciplina
      +'" idPMatricula="'+dado.idPMatricula+'" name="nota2"></td>'+
      '</tr>'
    });
    $("#tabela").html(html);
    corNotasVermelhaAzul("#tabela tr", valorMaximo)
    corNotasVermelhaAzul2("#tabela tr", valorMaximo)
  }

  function irmaNeusa(){
    valoresEnviar = new Array();

    var nomeCampoComErroEncontrado="";
    var msgErro="";
     $("#tabela tr[alterou=sim]").each(function(){
        var idPDisciplina = $(this).attr("idPDisciplina")
        var idPMatricula = $(this).attr("idPMatricula")

          valoresEnviar.push({idPMatricula:$(this).attr("idPMatricula"),
          idPDisciplina:$(this).attr("idPDisciplina"),
          grupo:$(this).attr("grupo"), 
          recurso:$("tr[idPMatricula="+idPMatricula+"][idPDisciplina="+idPDisciplina+"] input[name=nota2]").val()})

        $("tr[idPDisciplina="+idPDisciplina+"][idPMatricula="+idPMatricula+"] input[type=number]").each(function(){
            
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

      if(seFezAlteracao=="nao"){
        mensagensRespostas2("#mensagemErrada", "Não fizeste nenhuma alteração")
      }else if(nomeCampoComErroEncontrado!=""){
        if(nomeCampoComErroEncontrado=="nota2"){
          msgErro ="As notas "+msgErro;
        }
        mensagensRespostas2("#mensagemErrada", msgErro) 
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
        fecharJanelaEspera();
        estadoExecucao="ja";
        seFezAlteracao="nao"
        if(resultado.substring(0,1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else if(resultado.trim()!=""){
          mensagensRespostas("#mensagemCerta", "Os dados foram alterados com sucesso.");
          pautas = JSON.parse(resultado)
          fazerPesquisa();   
        }
      }     
    }
    enviarComGet("dados="+JSON.stringify(valoresEnviar)
      +"&tipoAcesso=alterarNotas&classe="+classe+"&idPNomeCurso="+idPNomeCurso
      +"&idPAno="+idPAno)
  }
