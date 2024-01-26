var seApenasAlunosReconf="nao"
var totalMarcado=0;
window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaSecretaria/novoComunicado/";
    listarUsuarios()
    $("#labelDonel").change(function(){
      if($(this).prop("checked")==true){
        seApenasAlunosReconf="sim";
      }else{
        seApenasAlunosReconf="nao";
      }
      listarUsuarios()
    })

    tipoInformacao();
    $("#tipoInformacao").change(function(){
       tipoInformacao()
    })

    $("#textoMensagem").bind("change keyup", function(){
      precificador();
    })
    $("#destinatario").change(function(){
      $("#luzingu").val("")
      if($(this).val()=="alunos"){
        $("#luzingu").removeAttr("disabled")
        $("#labelDonel").prop("checked", true)
        $("#labelForDonel").show()
      }else{
        $("#luzingu").attr("disabled", "")
        $("#labelForDonel").hide()
      }
      listarUsuarios()
      tipoInformacao()
    })
    $("#luzingu").change(function(){
      listarUsuarios();
    })

    var repet=true
    $("#tabela").bind("click mouseenter", function(){
      repet=true
      $("#tabela tr td input[type=checkbox]").change(function(){
        if(repet==true){
          var contador=0
          $("#tabela tr td input[type=checkbox]").each(function(){
              if($(this).prop("checked")==true){
                contador++
              }
          })
          totalMarcado = contador
          precificador()
          repet=false
        }
      })
    })
    $("#formSubmit").submit(function(){
      var dadosEnviar=new Array();
      $("#tabela tr td input[type=checkbox]").each(function(){
          if($(this).prop("checked")==true){

            dadosEnviar.push({id:$(this).attr("id"), "nome":$(this).attr("nome"),
              "telefone":$(this).attr("telefone")})
          }
      })
      if(dadosEnviar.length==0){
        mensagensRespostas2("#mensagemErrada", "Não seleccionaste nenhum destinatário.")
      }else{
        $("#dadosEnviar").val(JSON.stringify(dadosEnviar))
        enviarMensagens()
      }
      return false
    })
}

function fazerPesquisa(){
    var tbody=""
    var i=0 
    listaUsuarios.forEach(function(dado){
      i++
      id = dado.idPMatricula
      if(id==undefined || id=="" || id==null){
        id=dado.idPEntidade
      }

      nome = dado.nomeAluno
      if(nome==undefined || nome=="" || nome==null){
        nome=dado.nomeEntidade
      }
      numTelefone = dado.telefoneAluno
      if(numTelefone==undefined || numTelefone==null){
        numTelefone=dado.numeroTelefoneEntidade
      }

      email = dado.emailAluno
      if(email==undefined || email==null){
        email=dado.emailEntidade
      }
      tbody +="<tr><td class='lead text-center'>"+completarNumero(i)+"</td><td class='lead text-center'><input type='checkbox' id='"+
      id+"' nome='"+nome+"' telefone='"+numTelefone+"' checked></td><td>"
      +nome+"</td><td>"+email+"</td><td class='text-center'>"+numTelefone
      +"</td></tr>";           
    });
    totalMarcado=i
    precificador()
    $("#tabela").html(tbody)
}

function listarUsuarios(){
  chamarJanelaEspera("...") 
  enviarComGet("tipoAcesso=listarUsuarios&seApenasAlunosReconf="+
  seApenasAlunosReconf+"&destinatario="+$("#destinatario").val()+"&luzingu="+$("#luzingu").val());
    http.onreadystatechange = function(){
      if(http.readyState==4){
        fecharJanelaEspera()
        resultado = http.responseText.trim()
        listaUsuarios = JSON.parse(resultado)
        fazerPesquisa ()
      }
    }
}

function enviarMensagens(){
  chamarJanelaEspera("")
  http.onreadystatechange = function(){
    if(http.readyState==4){
        estadoExecucao ="ja";
        fecharJanelaEspera();
        resultado = http.responseText.trim()
        if(resultado.substring(0, 1)=="F"){
          mensagensRespostas('#mensagemErrada', resultado.substring(1,resultado.length))
        }else{
          $("#textoMensagem").val("")
          mensagensRespostas('#mensagemCerta', "A mensagem foi enviada com sucesso.")
        }
    }    
  }
  var form = new FormData(document.getElementById("formSubmit"))
  enviarComPost(form)
}

function precificador(){

  var numeroCaracteres = (abrevNomeEscola2+
    " "+$("#textoMensagem").val().trim()).length
  $("#numeroCaracter").text(numeroCaracteres)
  multiplicador = Math.floor(numeroCaracteres/160)+1;

  var precoTotSMS = new Number(totalMarcado)*new Number(precoPorMensagem)*multiplicador
  $("#precoTotSMS").val(precoTotSMS)
  $("#precoMensagem").text(totalMarcado+" / "+
    converterNumerosTresEmTres(precoTotSMS)+" AOA")
}

function tipoInformacao(){
  if($("#tipoInformacao").val()=="convocatoria"){
    if($("#destinatario").val()=="professor"){

      $("#textoMensagem").val("Saudações. Convocamos o professor para uma reunião de caráter organizativa na sala de reuniões desta instituição às 13:00 do dia 01/03/2023.")
    }else if($("#destinatario").val()=="alunos"){
      $("#textoMensagem").val("Saudações. Convocamos o aluno para uma reunião de caráter organizativa na sala de reuniões desta instituição às 13:00 do dia 01/03/2023.")
    }
  }else if($("#tipoInformacao").val()=="notificacao"){
    if($("#destinatario").val()=="professor"){
      $("#textoMensagem").val("Cumprimentos. desejamos comunicar ao professor que o Sistema para o lançamento de notas do 1º Trimestre está agora disponível até 01/03/2023")
    }else if($("#destinatario").val()=="alunos"){
      $("#textoMensagem").val("Comunicamos que, até 01/03/2023, alunos com pendências de pagamento de propinas de Dezembro poderão ser expulsos. Cumprimentos.")
    }
  }
  precificador();
}