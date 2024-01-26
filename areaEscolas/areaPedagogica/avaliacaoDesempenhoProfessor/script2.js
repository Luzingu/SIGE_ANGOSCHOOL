var idPAvalDesEnt="";
window.onload = function (){
   directorio = "areaPedagogica/avaliacaoDesempenhoProfessor/";

   	entidade ="professores";
    fecharJanelaEspera();
    seAbrirMenu();
    $("#anosLectivos").val(idPAno);
    $(".btnAlterarNotas").hide(200)

    $("select").change(function(){
        $(".btnAlterarNotas").show(200)
        $(".paginacao").hide(200)
    })
    $("#dataInicial, #dataFinal").click(function(){
        $(".btnAlterarNotas").show(200)
        $(".paginacao").hide(200)
    })
    $("#listaProfessor").bind("click mouseenter", function(){
        $("#listaProfessor input, #listaProfessor textarea").bind("keyup change", function(){

            $(".btnAlterarNotas").show(200)
            $(".paginacao").hide(200)
        })
    })
    comissAvalDesempProfessor.forEach(function(dado){
        $("#coordenador").val(dado.coordenador)
        $("#coordenadorAdjunto").val(dado.coordenadorAdjunto)
        $("#secretario").val(dado.secretario)
        $("#vogal1").val(dado.vogal1)
        $("#vogal2").val(dado.vogal2)
        $("#vogal3").val(dado.vogal3)
        $("#dataInicial").val(dado.dataInicial)
        $("#dataFinal").val(dado.dataFinal)
    })
    fazerPesquisa();
    

    $("#actualizar").click(function(){
    	gravarAvaliacaoProfessor();
    })

    $("#anosLectivos").change(function(){
          window.location ="?idPAno="+$(this).val()+"&trimestre="+trimestre;
    });
    $(".btnAlterarNotas").click(function(){
        irmaoLuzingu();
    })

     function irmaoLuzingu(){
        valoresEnviar = new Array();

        var nomeCampoComErroEncontrado="";
        var msgErro="";
         $("form.formularioNotas").each(function(){
            var idPAvalDesEnt = $(this).attr("idPAvalDesEnt")
            var idPEntidade = $(this).attr("idPEntidade")

              valoresEnviar.push({idPAvalDesEnt:$(this).attr("idPAvalDesEnt"),
              idPEntidade:$(this).attr("idPEntidade"), 
              QPEA:$("form[idPEntidade="+idPEntidade+"] input[name=QPEA]").val(), 
              aperfProf:$("form[idPEntidade="+idPEntidade+"] input[name=aperfProf]").val(), 
              PA:$("form[idPEntidade="+idPEntidade+"] input[name=PA]").val(), 
              relHum:$("form[idPEntidade="+idPEntidade+"] input[name=relHum]").val(), 
              resposabilidade:$("form[idPEntidade="+idPEntidade+"] input[name=resposabilidade]").val(), 
              dataAvaliacao:$("form[idPEntidade="+idPEntidade+"] input[name=dataAvaliacao]").val(), 
              comentario:$("form[idPEntidade="+idPEntidade+"] textarea[name=comentario]").val()})

            $("form[idPAvalDesEnt="+idPAvalDesEnt+"][idPEntidade="+idPEntidade+"] input[type=number]").each(function(){
                
                //Avaliando os dados do formulário...
                if($(this).attr("required")=="required" && $(this).val().trim()==""){
                  nomeCampoComErroEncontrado = $(this).attr("name")
                  $(this).focus()
                  $(this).css("border", "solid red 1px");
                  msgErro ="são obrigatórios.";
                }

                // && new Number($(this).val().trim())%5!=0
                /*if($(this).hasClass("inputVal") && trimestre!="IV"){
                  nomeCampoComErroEncontrado = $(this).attr("name")
                  $(this).focus()
                  $(this).css("border", "solid red 1px");
                  msgErro ="devem ser multiplos de 5.";
                }*/

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

         if(nomeCampoComErroEncontrado!=""){
            if(nomeCampoComErroEncontrado=="QPEA"){
                msgErro ="Os valores de QPEA "+msgErro;
            }else if(nomeCampoComErroEncontrado=="aperfProf"){
                msgErro ="Os valores de Aperfeiçoamento Profissional "+msgErro;
            }else if(nomeCampoComErroEncontrado=="PA"){                
                msgErro ="Os valores de Progresso do Aluno "+msgErro;                
            }else if(nomeCampoComErroEncontrado=="resposabilidade"){                
                msgErro ="Os valores da Responsabilidade "+msgErro;                
            }else if(nomeCampoComErroEncontrado=="relHum"){                
                msgErro ="Os valores de Relações Humanas "+msgErro;                
            }
            mensagensRespostas2("#mensagemErrada", msgErro) 
         }else{
            if(estadoExecucao=="ja"){
              estadoExecucao="aindaNao"
              manipularNotas();
            }        
         }  
      }


  }


function fazerPesquisa(){
    var tbody = "";
    if(jaTemPaginacao==false){
        paginacao.baraPaginacao(avaliacaoProfessor.filter(fazerPesquisaCondition).length, 20);
      }else{
          jaTemPaginacao=false;
      }
    var i=paginacao.comeco;
      var contagem=-1;
      $("#numTProfessores").text(completarNumero(avaliacaoProfessor.filter(fazerPesquisaCondition).length))
      var numTFemininos=0;
    avaliacaoProfessor.filter(fazerPesquisaCondition).forEach(function(dado){
      contagem++;

      if(dado.generoEntidade=="F"){
          numTFemininos++;
      }
      $("#numTFemininos").text(completarNumero(numTFemininos));

      if(contagem>=paginacao.comeco && contagem<=paginacao.final){
          i++;
          
          qualProcEnsAprend = dado.aval_desemp.qualProcEnsAprendI
          aperfProfissional = dado.aval_desemp.aperfProfissionalI
          PA = dado.aval_desemp.PAI
          resposabilidade = dado.aval_desemp.resposabilidadeI
          relHum = dado.aval_desemp.relHumI
          comentario = dado.aval_desemp.comentarioI
          dataAvaliacao = dado.aval_desemp.dataAvaliacaoI
          var readOnly="";
          if(trimestre=="II"){
            qualProcEnsAprend = dado.aval_desemp.qualProcEnsAprendII
            aperfProfissional = dado.aval_desemp.aperfProfissionalII
            PA = dado.aval_desemp.PAII
            resposabilidade = dado.aval_desemp.resposabilidadeII
            relHum = dado.aval_desemp.relHumII
            comentario = dado.aval_desemp.comentarioII
          dataAvaliacao = dado.aval_desemp.dataAvaliacaoII
          }else if(trimestre=="III"){
            qualProcEnsAprend = dado.aval_desemp.qualProcEnsAprendIII
            aperfProfissional = dado.aval_desemp.aperfProfissionalIII
            PA = dado.aval_desemp.PAIII
            resposabilidade = dado.aval_desemp.resposabilidadeIII
            relHum = dado.aval_desemp.relHumIII
            comentario = dado.aval_desemp.comentarioIII
          dataAvaliacao = dado.aval_desemp.dataAvaliacaoIII
          }else if(trimestre=="IV"){
            qualProcEnsAprend = dado.aval_desemp.qualProcEnsAprendIV
            aperfProfissional = dado.aval_desemp.aperfProfissionalIV
            PA = dado.aval_desemp.PAIV
            resposabilidade = dado.aval_desemp.resposabilidadeIV
            relHum = dado.aval_desemp.relHumIV
            dataAvaliacao = dado.aval_desemp.dataAvaliacaoIV
            comentario = dado.aval_desemp.comentarioIV
            readOnly="readonly"
          }
          tbody +='<form class="row formularioNotas" idPAvalDesEnt="'+dado.aval_desemp.idPAvalDesEnt
          +'" idPEntidade="'+dado.idPEntidade+'">'+
          '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 lead text-center"><div class="visible-md visible-lg"><br/></div>'+
          completarNumero(contagem+1)+'</div>'+
          '<div class="col-lg-5 col-md-5 col-sm-11 col-xs-11 lead nomeAluno toolTipeImagem" imagem="'+dado.fotoEntidade
                        +'"><div class="visible-md visible-lg"><br/></div><strong style="font-size:18pt;">'+
            dado.nomeEntidade+' ('+dado.numeroInternoEntidade+')</strong></div>'+

            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Data</strong><input type="date"'+
            ' name="dataAvaliacao" class="form-control text-center lead"'+
            ' value="'+vazioNull(dataAvaliacao)+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>QPEA</strong><input type="number"'+
            ' name="QPEA" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="60" value="'+vazioNull(qualProcEnsAprend)+'" '+readOnly+'></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Apef. Prof.</strong><input type="number"'+
            ' name="aperfProf" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+vazioNull(aperfProfissional)+'" '+readOnly+'></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Progr. Aluno</strong><input type="number"'+
            ' name="PA" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+vazioNull(PA)+'" '+readOnly+'></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Responsab.</strong><input type="number"'+
            ' name="resposabilidade" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+vazioNull(resposabilidade)+'" '+readOnly+'></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Rel. Humano</strong><input type="number"'+
            ' name="relHum" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+relHum+'" '+readOnly+'></div>'+
            '<div class="col-lg-12 col-md-12 text-center"><strong class="lead">Comentário</strong>'+
                '<textarea class="form-control" name="comentario" style="max-width:100%; height:70px;">'+vazioNull(comentario)+'</textarea>'+
            '</div>'
          tbody +='</form>';
      }           
    });
    $("#listaProfessor").html(tbody)
    corCelula()
}
function corCelula(){
  $("#listaProfessor form .inputVal").each(function(i){
      if($(this).val()<10 && $(this).val().trim()!=""){
          $(this).css("color", "red");
      }else{
        $(this).css("color", "darkblue");
      }
  });
}

function gravarAvaliacaoProfessor(){
	 chamarJanelaEspera("Actualizando...");
	http.onreadystatechange = function(){
		if(http.readyState==4){
		    fecharJanelaEspera();
		    resultado = http.responseText.trim();
            if(resultado.substring(0, 1)=="F"){
                mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length))
            }else if(resultado!=""){
            	avaliacaoProfessor = JSON.parse(resultado);
                fazerPesquisa();
            }
		}
	}
	enviarComGet("tipoAcesso=gravarAvaliacaoProfessor&idPAno="+idPAno);
}

function manipularNotas(){
    chamarJanelaEspera("")
      http.onreadystatechange = function(){
        if(http.readyState==4){
            estadoExecucao ="ja";
            fecharJanelaEspera();
            resultado = http.responseText.trim()
            $(".btnAlterarNotas").hide(200)
            $(".paginacao").show(200)
            if(resultado.substring(0, 1)=="F"){
                mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length))
            }else if(resultado!=""){
                mensagensRespostas('#mensagemCerta', "Os dados foram alterados com sucesso.")
                avaliacaoProfessor = JSON.parse(resultado);
                fazerPesquisa();
            }
        }    
      }
    $("#formularioDados #dadosEnviar").val(JSON.stringify(valoresEnviar))
    var form = new FormData(document.getElementById("formularioDados"))
    enviarComPost(form)
}


