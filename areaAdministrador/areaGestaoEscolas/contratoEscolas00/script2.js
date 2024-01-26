window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="escolas";
    directorio = "areaGestaoEscolas/contratoEscolas00/";
    accoes("#tabEscola", "#formularioEscola", "Escola", "Tens certeza que pretendes eliminar esta escola?");
  
    fazerPesquisa();
    DataTables("#example1", "sim")

    $("#formularioEscola form").submit(function(){
      if(estadoExecucao=="ja"){
          estadoExecucao="espera";
          idEspera = "#formularioEscola form #Cadastar";
          manipularEscola();
      } 
        return false;
    })
    $("#formularioEscolaForm #modoPagamento").change(function(){
      changeModeloPagamento();
    })
}

function porValoresNoFormulario(){
 listaEscolas.forEach(function(dado){
      if(dado.idPEscola==idPrincipal){
         $("#formularioEscolaForm #modoPagamento").val(dado.contrato.modoPagamento)
         $("#formularioEscolaForm #valorPagoPor15Dias").val(dado.contrato.valorPagoPor15Dias);
         $("#formularioEscolaForm #valorPorAluno").val(dado.contrato.valorPorAluno);
         changeModeloPagamento()
         $("#formularioEscolaForm #nomeEscola").val(dado.nomeEscola);

         $("#formularioEscolaForm #idEscolaContrato").val(dado.contrato.idEscolaContrato);
         $("#formularioEscolaForm #dataInicioContrato").val(dado.contrato.dataInicioContrato);
         $("#formularioEscolaForm #dataFimContrato").val(dado.contrato.dataFimContrato);
         $("#formularioEscolaForm #tipoPagamento").val(dado.contrato.tipoPagamento);
         $("#formularioEscolaForm #dataExpiracaoValidade").val(dado.contrato.dataExpiracaoValidade);
         $("#formularioEscolaForm #idEntGestorEscola1").val(dado.contrato.idEntGestorEscola1);
         $("#formularioEscolaForm #idEntGestorEscola2").val(dado.contrato.idEntGestorEscola2);
         $("#formularioEscolaForm #inicioPrazoPosPago").val(dado.contrato.inicioPrazoPosPago);
         $("#formularioEscolaForm #fimPrazoPosPago").val(dado.contrato.fimPrazoPosPago);
         $("#formularioEscolaForm #mesesConsecutivosParaBloquear").val(dado.contrato.mesesConsecutivosParaBloquear);  
      }
  });
}

function changeModeloPagamento(){

  if($("#formularioEscolaForm #modoPagamento").val()=="valorGlobal"){
    $("#formularioEscolaForm .valorPorAluno").hide();
    $("#formularioEscolaForm #valorPorAluno").removeAttr("required");
    $("#formularioEscolaForm .idValorPagoPor15Dias").show();
    $("#formularioEscolaForm #valorPagoPor15Dias").attr("required");
  }else{
    $("#formularioEscolaForm .idValorPagoPor15Dias").hide();
    $("#formularioEscolaForm #valorPagoPor15Dias").val();
    $("#formularioEscolaForm #valorPagoPor15Dias").removeAttr("required");
    $("#formularioEscolaForm .valorPorAluno").show();
    $("#formularioEscolaForm #valorPorAluno").attr("required");
  }
}

function manipularEscola(){
    $("#formularioEscola").modal("hide")
    var form = new FormData(document.getElementById("formularioEscolaForm"));
    chamarJanelaEspera("") 
   enviarComPost(form);
   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        fecharJanelaEspera();
       if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "O contrato foi alterado com sucesso.");
          listaEscolas = JSON.parse(resultado)
          fazerPesquisa();
        }

      }
    }
}

function fazerPesquisa(){

    var tbody = "";
    var i=0;
    var contagem=-1;
    var numEscolaA=0;

    $("#numTEscolas").text(completarNumero(listaEscolas.length));

    listaEscolas.forEach(function(dado){
      contagem++;

      $("#numTActivo").text(completarNumero(numEscolaA));
          i++;

          var imgContrato=dado.contrato.imgContrato;
          if(imgContrato!=null && imgContrato!=""){

            imgContrato ="<a href='../../../Ficheiros/Escola_"+dado.idPEscola
            +"/Icones/"+dado.contrato.imgContrato+"'><i class='fa fa-print'></i></a>"
          } 

          var tipoPagamento=vazioNull(dado.contrato.tipoPagamento);
          if(tipoPagamento=="nao"){
            tipoPagamento="Nehum<br/>Pagamento";
          }else if(tipoPagamento=="pre"){
            tipoPagamento="Pré-Pago";
          }else if(tipoPagamento=="pos"){
            tipoPagamento="Pós-Pago";
          }

          tbody +="<tr><td class='lead'>"+dado.nomeEscola+"</td>"+
          "<td class='lead text-center'>"+vazioNull(dado.tipoPacoteEscola)
          +"</td><td class='lead text-center'>"+converterData(dado.contrato.dataInicioContrato)+
          "</td><td class='lead text-center'>"+converterNumerosTresEmTres(vazioNull(dado.contrato.valorPagoPor15Dias))+" kz</td>"+
          "<td class='lead text-center'>"+tipoPagamento
          +"</td><td class='lead text-center'>"+vazioNull(imgContrato)
          +"</td><td class='text-center'><div class='btn-group alteracao text-right'><a class='btn btn-success' title='Editar' href='#' action='editarEscola'"+  " idPrincipal='"+dado.idPEscola+"'><i class='fa fa-pen'></i></a></div></td></tr>";
           
    });
    $("#tabEscola").html(tbody);
}

