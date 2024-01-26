var idPAvalDesEnt="";
window.onload = function (){
   directorio = "areaAdministrativa/avaliacaoDesempenhoPessoalNaoDocente/";

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
    comissAvalDesempPessoalNaoDocente.forEach(function(dado){
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
              CAP:$("form[idPEntidade="+idPEntidade+"] input[name=CAP]").val(),
              interesse:$("form[idPEntidade="+idPEntidade+"] input[name=interesse]").val(),
              CLT:$("form[idPEntidade="+idPEntidade+"] input[name=CLT]").val(),
              organizacao:$("form[idPEntidade="+idPEntidade+"] input[name=organizacao]").val(),
              SP:$("form[idPEntidade="+idPEntidade+"] input[name=SP]").val(),
              criatividade:$("form[idPEntidade="+idPEntidade+"] input[name=criatividade]").val(),
              RIP:$("form[idPEntidade="+idPEntidade+"] input[name=RIP]").val(),
              atencao:$("form[idPEntidade="+idPEntidade+"] input[name=atencao]").val(),
              PA:$("form[idPEntidade="+idPEntidade+"] input[name=PA]").val(),
              disciplina:$("form[idPEntidade="+idPEntidade+"] input[name=disciplina]").val(),

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

            if(nomeCampoComErroEncontrado=="CAP"){
                msgErro ="Os valores de Capacidade de análise Profissional "+msgErro;
            }else if(nomeCampoComErroEncontrado=="interesse"){
                msgErro ="Os valores de Interesse "+msgErro;
            }else if(nomeCampoComErroEncontrado=="CLT"){
                msgErro ="Os valores de Conhecimentos ligados ao trabalho "+msgErro;
            }else if(nomeCampoComErroEncontrado=="SP"){
                msgErro ="Os valores de Sigilo Profissional "+msgErro;
            }else if(nomeCampoComErroEncontrado=="criatividade"){
                msgErro ="Os valores de Criatividade "+msgErro;
            }else if(nomeCampoComErroEncontrado=="RIP"){
                msgErro ="Os valores de Relacionamento InterPessoal "+msgErro;
            }else if(nomeCampoComErroEncontrado=="atencao"){
                msgErro ="Os valores de Atenção "+msgErro;
            }else if(nomeCampoComErroEncontrado=="PA"){
                msgErro ="Os valores de Pontualidade e Assiduidade "+msgErro;
            }else if(nomeCampoComErroEncontrado=="disciplina"){
                msgErro ="Os valores de Disciplina "+msgErro;
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

          tbody +='<form class="row formularioNotas" idPAvalDesEnt="'+dado.aval_desemp.idPAvalDesEnt
          +'" idPEntidade="'+dado.idPEntidade+'">'+
          '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-1 lead text-center"><div class="visible-md visible-lg"><br/></div>'+
          completarNumero(contagem+1)+'</div>'+
          '<div class="col-lg-5 col-md-5 col-sm-11 col-xs-11 lead nomeAluno toolTipeImagem" imagem="'+dado.fotoEntidade
                        +'"><div class="visible-md visible-lg"><br/></div><strong style="font-size:18pt;">'+
            dado.nomeEntidade+' ('+dado.numeroInternoEntidade+')</strong></div>'+

            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Data</strong><input type="date"'+
            ' name="dataAvaliacao" class="form-control text-center lead"'+
            ' value="'+dado.aval_desemp.dataAvaliacao+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>CAP</strong><input type="number"'+
            ' name="CAP" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="60" value="'+dado.aval_desemp.CAP+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Interesse</strong><input type="number"'+
            ' name="interesse" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.interesse+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>CLT</strong><input type="number"'+
            ' name="CLT" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.CLT+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Organização</strong><input type="number"'+
            ' name="organizacao" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.organizacao+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>SP</strong><input type="number"'+
            ' name="SP" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.SP+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Criatividade</strong><input type="number"'+
            ' name="criatividade" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.criatividade+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>RIP</strong><input type="number"'+
            ' name="RIP" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.RIP+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Atenção</strong><input type="number"'+
            ' name="atencao" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.atencao+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>PA</strong><input type="number"'+
            ' name="PA" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.PA+'"></div>'+
            '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>Disciplina</strong><input type="number"'+
            ' name="disciplina" class="form-control text-center inputVal lead"'+
            ' step="5" min="0" max="20" value="'+dado.aval_desemp.disciplina+'"></div>'+
            '<div class="col-lg-12 col-md-12 text-center"><strong class="lead">Comentário</strong>'+
                '<textarea class="form-control" name="comentario" style="max-width:100%; height:70px;">'+vazioNull(dado.aval_desemp.comentario)+'</textarea>'+
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
      $("#formDados #dadosEnviar").val(JSON.stringify(valoresEnviar))
      $("#formDados #idPAno").val($("#anosLectivos").val())
      var form = new FormData(document.getElementById("formDados"))
      enviarComPost(form)
  }
