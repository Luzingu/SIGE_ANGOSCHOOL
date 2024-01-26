  var dadosEnviar=new Array();
  window.onload=function (){
     directorio = "areaPedagogica/comissaoExames/";
    fecharJanelaEspera()
    seAbrirMenu();
    $("#idPNomeDisciplina").val(idPNomeDisciplina)

    $("#idPNomeDisciplina").change(function(){
      window.location='?idPNomeDisciplina='+$(this).val()
    })
    $("#totoCheckBox").change(function(){
      if($(this).prop("checked")==true){
        $("#tabDivisao input[type=checkbox]").prop("checked", true)
      }else{
        $("#tabDivisao input[type=checkbox]").prop("checked", false)
      }
    })

    $("#btnAlterar").click(function(){
        dadosEnviar = new Array();
      $("#tabDivisao tr").each(function(){
        var idPDivisao = $(this).attr("id")
        var estadoComissaoExame = "F";
        if($("tr#"+idPDivisao+" input[type=checkbox]").prop("checked")==true){
          estadoComissaoExame="V";
        }
        dadosEnviar.push({idPDivisao:idPDivisao,
        idPresidenteComissaoExame:$("tr#"+idPDivisao+" select.idPresidenteComissaoExame").val(),
        estadoComissaoExame:estadoComissaoExame})
      })
      manipular();
    })
  }

  
  function manipular(){
    chamarJanelaEspera("");
    http.onreadystatechange = function(){
      if(http.readyState==4){
        estadoExecucao="ja";
        fecharJanelaEspera();
        resultado = http.responseText.trim()
        if(resultado.substring(0,1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
            mensagensRespostas("#mensagemCerta", "A comissão foi criada com sucesso.");
        }    
      }
    }
    enviarComGet("tipoAcesso=alterarDados&dadosEnviar="+JSON.stringify(dadosEnviar));
  }

