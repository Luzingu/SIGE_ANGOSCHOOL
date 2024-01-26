var arrayDadosAtraso = new Array()
var notasAtraso= new Array()
var cabecalhoParaNotasAtraso=new Array()
var changeAnoLectivo=false
$(document).ready(function(){


	$(".btnAlterarNotasAtraso").click(function(){
	  irmaTeresaAtraso();
	})
	  $("#carregarNotasAtraso").click(function(){
	    carregarNotasAtraso()
	  })
	  $("#classeNotasAtraso").change(function(){
	    buscarNotasAtrasoAlunos()
	  })
    $("#anoAnterior").change(function(){
      changeAnoLectivo=true
      buscarCabelho() 
    })
})


function  carregarNotasAtraso(){
    directorio = "areaSecretaria/relatorioAluno/";
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera();
      var resultado = http.responseText.trim()
      if(resultado.trim().substring(0, 1)=="F"){
        mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));                
      }else{
        notasAtraso = JSON.parse(resultado)
        listarNotasAtraso()
      }
    }
  }
  enviarComGet("tipoAcesso=carregarNotasAtraso&idPCurso="+idCursoP+"&idPMatricula="
    +idPMatricula+"&classe="+$("#classeNotasAtraso").val());
}

function  buscarNotasAtrasoAlunos (){
    directorio = "areaSecretaria/relatorioAluno/";
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera();
      var resultado = http.responseText.trim()
      listaDados = JSON.parse(resultado)
      cabecalhoParaNotasAtraso = listaDados[0]
      notasAtraso = listaDados[1]
      changeAnoLectivo=false
      listarNotasAtraso();
    }
  }
  enviarComGet("tipoAcesso=listarNotasAtrasoAtrasoAlunos&idPMatricula="
    +idPMatricula+"&classe="+$("#classeNotasAtraso").val()
    +"&idPCurso="+idCursoP); 
}

function  buscarCabelho (){
    directorio = "areaSecretaria/relatorioAluno/";
    chamarJanelaEspera("...");
    http.onreadystatechange = function(){
    if(http.readyState==4){
      fecharJanelaEspera();
      var resultado = http.responseText.trim()
      cabecalhoParaNotasAtraso = JSON.parse(resultado)
      listarNotasAtraso();
    }
  }
  enviarComGet("tipoAcesso=buscarCabelho&classe="+$("#classeNotasAtraso").val()
    +"&idPAno="+$("#anoAnterior").val()+"&idPCurso="+idCursoP); 
}

function manipularPautasAtraso(){
  directorio ="areaSecretaria/relatorioAluno/"
  chamarJanelaEspera("...")
  http.onreadystatechange = function(){
    if(http.readyState==4){
      estadoExecucao="ja"
      resultado = http.responseText.trim()
      if(resultado!=""){
        fecharJanelaEspera();
        mensagensRespostas("#mensagemErrada", resultado);
      }else{
        mensagensRespostas("#mensagemCerta", "As notas foram alteradas com sucesso.");
        buscarNotasAtrasoAlunos();                         
      }
    }
  }
  $("#formValoresNotaSistema #action").val("alterarNotasAtraso")
  $("#formValoresNotaSistema #notas").val(JSON.stringify(valoresEnviar))
  $("#formValoresNotaSistema #dadosAtraso").val(JSON.stringify(arrayDadosAtraso))
  $("#formValoresNotaSistema #idPCurso").val(idCursoP)
  $("#formValoresNotaSistema #tipoCurso").val(tipoCurso)
  $("#formValoresNotaSistema #idPMatricula").val(idPMatricula)
  $("#formValoresNotaSistema #classeAluno").val(classeAlunoP)
  $("#formValoresNotaSistema #classeNotas").val($("#classeNotasAtraso").val())
  $("#formValoresNotaSistema #modeloPauta").val($("#modeloPautaAtraso").val())
  var form = new FormData(document.getElementById("formValoresNotaSistema"))
  enviarComPost(form)
}

 function irmaTeresaAtraso(){
	valoresEnviar = new Array()
	arrayDadosAtraso = new Array()
	msgErro="";
	nomeCampoComErroEncontrado="";
	arrayDadosAtraso.push({
      idDAtraso:$("#paraDadosAtraso").attr("idDAtraso"),
      anoLectivo:$("#anoAnterior").val(),
      turma:$("#paraDadosAtraso input[name=turma]").val(),
      numeroPauta:$("#paraDadosAtraso input[name=numeroPauta]").val(),
      numero:$("#paraDadosAtraso input[name=numero]").val()
    })


	 $(".formularioNotas").each(function(){
    	var idPPauta = $(this).attr("idPPauta")

        var avaliacoesQuantitativas=new Array();
        $(".formularioNotas[idPPauta="+idPPauta+"] input[type=number]").each(function(){
          avaliacoesQuantitativas.push({name:$(this).attr("name"), valor:$(this).val()
          , idCampoAvaliacao:$(this).attr("idCampoAvaliacao"), periodo:$(this).attr("periodo"), tipoCampo:$(this).attr("tipoCampo")})
        })

        valoresEnviar.push({
          idPPauta:$(this).attr("idPPauta"),
          idPDisciplina:$(this).attr("idPDisciplina"),
          continuidadeDisciplina:$(this).attr("continuidadeDisciplina"),
          tipoCurso:tipoCurso,
          semestrePauta:$(this).attr("semestrePauta"),
          avaliacoesQuantitativas:avaliacoesQuantitativas
        })
        
        $(".formularioNotas[idPPauta="+idPPauta+"] input").each(function(){
            
            //Avaliando os dados do formulário...
            if(nomeCampoComErroEncontrado==""){
              if($(this).attr("required")=="required" && $(this).val().trim()==""){
                nomeCampoComErroEncontrado = $(this).attr("designacao")
                $(this).focus()
                $(this).css("border", "solid red 1px");
                msgErro ="são obrigatórios.";
              }
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
	if(nomeCampoComErroEncontrado!=""){
	  msgErro ="As notas da "+nomeCampoComErroEncontrado+" "+msgErro;
	}
	if(msgErro!=""){
	  mensagensRespostas2("#mensagemErrada", msgErro);
	}else{
	  if(estadoExecucao=="ja"){
	    estadoExecucao="aindaNao"
	    manipularPautasAtraso();
	  }
	}
}

function listarNotasAtraso(){

    var html=""
    var contadorNotas=0
      $("#notasEmAtraso").html("")
    notasAtraso.filter(condicaoNotasAtraso).forEach(function(dado){

        attrDisciplina = vazioNull(dado.tipoDisciplina);
        if(tipoCurso=="tecnico"){
          attrDisciplina = dado.continuidadeDisciplina;
        }
        if(dado.nomeDisciplina.length<=34){
        	attrDisciplina+=")<br/>&nbsp;&nbsp;"
        } 
        contadorNotas++

        if(contadorNotas==1){
          if(changeAnoLectivo==false){
            $("#anoAnterior").val(dado.dadosatraso.anoAnterior)
          }

        	html += '<div id="paraDadosAtraso" idDAtraso="'+dado.dadosatraso.idDAtraso+'" class="row"><div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>TURMA</strong><input type="text" idPMatricula="'+dado.idPMatricula+'" name="turma" class="form-control valorDigitado text-center lead" value="'
	        +vazioNull(dado.dadosatraso.turmaAnterior)+'" required="required"></div>'+
	        '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>N.º da Pauta (Livro)</strong><input type="text" idPMatricula="'+dado.idPMatricula+'" name="numeroPauta" class="form-control valorDigitado text-center lead" step="1" min="1" value="'+vazioNull(dado.dadosatraso.numeroPauta)
	        +'"></div>'+
	        '<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>N.º</strong><input type="number" idPMatricula="'+dado.idPMatricula+'" name="numero" class="form-control valorDigitado text-center lead" step="1" min="1" value="'+vazioNull(dado.dadosatraso.numeroAnterior)
	        +'" required="required"></div></div>'
        }

        html +='<form class="row  formulario formularioNotas" idPPauta="'
        +dado.pautas.idPPauta+'" idPDisciplina="'+dado.pautas.idPautaDisciplina
        +'" continuidadeDisciplina="'+dado.continuidadeDisciplina
        +'" idDAtraso="'+dado.dadosatraso.idDAtraso
        +'" semestrePauta="'+dado.pautas.semestrePauta
        +'" method="POST" id="form'+dado.idPMatricula+'"><div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 lead"><div class="visible-md visible-lg"><br/></div><strong>'+dado.nomeDisciplina
          +'</strong></div>'

        cabecalhoParaNotasAtraso.forEach(function(campo){

          readonly="";
          if(campo.seApenasLeitura=="V"){
            readonly=" readonly"
          }
          html +='<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 text-center"><strong>'+
          campo.designacao1+'</strong><input type="number" name="'+campo.identUnicaDb
          +'" designacao="'+campo.designacao1+'" tipoCampo="'+campo.tipoCampo+'" periodo="'+campo.periodo+'" idCampoAvaliacao="'+campo.idCampoAvaliacao
          +'" class="form-control text-center inputVal mac1 lead" step="0.01" min="'+campo.notaMinima+'" media="'+campo.notaMedia+'" max="'
          +campo.notaMaxima+'" value="'+vazioNull(dado.pautas[campo.identUnicaDb])+'" '+readonly
          +' style="font-size:13pt; font-weight:600; padding:0px;"></div>'
        })
        html +='</form>'
    })
    $("#notasEmAtraso").html(html)
    corNotasVermelhaAzul("#notasEmAtraso form")
}

function condicaoNotasAtraso(elem, ind, obj){
  return (elem.pautas.classePauta ==$("#classeNotasAtraso").val())
}