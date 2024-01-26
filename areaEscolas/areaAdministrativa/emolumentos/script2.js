var idPCurso="";
var idEspera ="";
var accao="";
var valoresAlteradosPrecos =new Array();
var nomeValoresAlteradosPrecos =new Array();

window.onload=function(){
    
    fecharJanelaEspera();
    seAbrirMenu();
    directorio = "areaAdministrativa/emolumentos/";

    listarClasses("", "", "#idPCurso", "#classe")
    listarPrecos();
    $("#idPTipoEmolumento").val(idPTipoEmolumento)
    $("#idPTipoEmolumento").change(function(){
        window.location='?idPTipoEmolumento='+$(this).val()
    })

    $("#formPrecosForm #precoUnico").keyup(function(){
        if($(this).val().trim()==""){
          passarValorNoFormulario();
        }else{
          $("#formPrecosForm input[valor]").val($(this).val());
        }
    })
    $("#tabPagamentos").dblclick(function(){
      $("#formPrecosForm #precoUnico").val("")
      accao ="alterarOutrosPrecos";
      $("#formPrecos #outrosPagamentos").show();
      $("#formPrecos .declaracao").hide(); 
      if(codigoEmolumento=="declaracao"){
        $("#formPrecos .declaracao").show(); 
      }
      
      $("#formPrecos .outrosPagamentos").show();
      $("#formPrecos #paraMensalidades").hide();
      passarValorNoFormulario()
      $("#formPrecos").modal("show");
    });

    $("#tabPrecoMensalidades").dblclick(function(){
      $("#formPrecosForm #precoUnico").val("")
      accao ="alterarPrecosMensalidades";
      $("#formPrecos #outrosPagamentos").hide();
      $("#formPrecos #paraMensalidades").show();
      passarValorNoFormulario();
      $("#formPrecos #paraMensalidades .titulo").text($("#idPCurso option:selected").text()+" - "+$("#classe").val());
      
      $("#formPrecos").modal("show");
    });

    $("#actualizar").click(function(){
      gravarPrecos();
    })

    $("#idPCurso").change(function(){
      listarPrecos();
    });

    $("#classe").change(function(){
      classe = $(this).val();
      listarPrecos();
    })

    $("#formPrecos form").submit(function(){
      if(estadoExecucao=="ja"){
        estadoExecucao="aindaNao";
        alterarPrecos();
      }
      return false;
    })
}

function gravarPrecos(){
  chamarJanelaEspera("...");
  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim();
      fecharJanelaEspera();
      estadoExecucao="ja";
      tabelaprecos = JSON.parse(http.responseText);
      listarPrecos();
    }
  }
  enviarComGet("tipoAcesso=gravarPrecos&idPTipoEmolumento="+idPTipoEmolumento);
}

function passarValorNoFormulario(){
  $("#formPrecosForm input[valor]").val(0);
  tabelaprecos.forEach(function(dado){

      if(accao=="alterarPrecosMensalidades"){
        if($("#classe").val()==dado.emolumentos.classe && $("#idPCurso").val()==dado.emolumentos.idCurso){
            $("#formPrecosForm input[type=number]#preco"+
            dado.emolumentos.mes).val(dado.emolumentos.valor);
          }
      }else{
        $("#formPrecosForm input[type=number]#preco_"+
          dado.emolumentos.classe+"_"+dado.emolumentos.idCurso).val(dado.emolumentos.valor);
                        
      }
  });

}


function listarPrecos(){
  tabelaprecos.forEach(function(dado){

    $("#tabPagamentos .valor").html()
    if(dado.emolumentos.codigoEmolumento=="propina"){
        if($("#classe").val()==dado.emolumentos.classe && $("#idPCurso").val()==dado.emolumentos.idCurso){
          $("#tabPrecoMensalidades #mes"+dado.emolumentos.mes).html(dado.emolumentos.valor);
        }
    }else{ 
      $("#tabPagamentos #preco_"+dado.emolumentos.classe+"-"+dado.emolumentos.idCurso).html(dado.emolumentos.valor);
    }
  });
}

function alterarPrecos(){

  if(accao=="alterarPrecosMensalidades"){
      $("#formPrecosForm #paraMensalidades input[type=number]").each(function(){
          nomeValoresAlteradosPrecos.push($(this).attr("valor"));
          valoresAlteradosPrecos.push($(this).val());
      })
  }else{
    $("#formPrecosForm #outrosPagamentos input[type=number]").each(function(){
      nomeValoresAlteradosPrecos.push($(this).attr("valor"));
      valoresAlteradosPrecos.push($(this).val());
    })
  }

  chamarJanelaEspera("")
  http.onreadystatechange = function(){
    $(".modal").modal("hide");
    if(http.readyState==4){
      estadoExecucao="ja";
      fecharJanelaEspera();
      resultado = http.responseText.trim();
      if(resultado.substring(0,1)=="0"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
      }else{
          tabelaprecos = JSON.parse(http.responseText);
        mensagensRespostas("#mensagemCerta", "Os emulumentos foram actualizados com sucesso.");
        listarPrecos();
      }
    }
  }
  enviarComGet("tipoAcesso="+accao
    +"&idPCurso="+$("#idPCurso").val()+"&classe="+$("#classe").val()+"&valoresAlteradosPrecos="
    +valoresAlteradosPrecos+"&nomeValoresAlteradosPrecos="
    +nomeValoresAlteradosPrecos+"&idPTipoEmolumento="+idPTipoEmolumento);
}

