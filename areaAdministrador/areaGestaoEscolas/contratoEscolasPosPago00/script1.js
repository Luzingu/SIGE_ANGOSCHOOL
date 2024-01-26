idPEscola="";
window.onload = function(){
  seAbrirMenu();
  fecharJanelaEspera();
    entidade ="escolas";
    directorio = "areaGestaoEscolas/contratoEscolasPosPago00/";
    
    fazerPesquisa();
    DataTables("#example1", "sim")

    var repet=true;
    $("#example1 tbody").bind("click mouseenter", function(){
      repet=true;
      $("#example1 tbody tr td a").click(function(){
          if(repet==true){
            idPEscola = $(this).attr("idPEscola")
            prorogarContrato();
            repet=false;
          }
      })
    })
}

function porValoresNoFormulario(){
 listaEscolas.forEach(function(dado){
      if(dado.idPEscola==idPrincipal){
         $("#formularioEscolaForm #nomeEscola").val(dado.nomeEscola);
         $("#formularioEscolaForm #idEscolaContrato").val(dado.contrato.idEscolaContrato);
         $("#formularioEscolaForm #dataInicioContrato").val(dado.contrato.dataInicioContrato);
         $("#formularioEscolaForm #dataFimContrato").val(dado.contrato.dataFimContrato);
         $("#formularioEscolaForm #tipoPagamento").val(dado.contrato.tipoPagamento);
         $("#formularioEscolaForm #valorPagoPor15Dias").val(dado.contrato.valorPagoPor15Dias);
         $("#formularioEscolaForm #dataExpiracaoValidade").val(dado.contrato.dataExpiracaoValidade);
         $("#formularioEscolaForm #idEntGestorEscola1").val(dado.idEntGestorEscola1);
         $("#formularioEscolaForm #idEntGestorEscola2").val(dado.idEntGestorEscola2);
      }
  });
}

function prorogarContrato(){

  chamarJanelaEspera("")
  enviarComGet("tipoAcesso=prorogarContrato&idPEscola="+idPEscola);

   http.onreadystatechange = function(){
      if(http.readyState==4){
        resultado = http.responseText.trim()
        estadoExecucao="ja";
        fecharJanelaEspera();
       if(resultado.trim().substring(0, 1)=="F"){
          mensagensRespostas("#mensagemErrada", resultado.substring(1, resultado.length));
        }else{
          mensagensRespostas("#mensagemCerta", "O contrato foi renovado com sucesso.");
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
            +"/Icones/"+dado.contrato.imgContrato+"'><i class='fa fa-print'></i> Visualizar</a>"
          }

          tbody +="<tr><td class='lead'>"+dado.nomeEscola+"</td>"+
          "<td class='lead text-center'>"+vazioNull(dado.tipoPacoteEscola)
          +"</td><td class='lead text-center'>"+converterData(dado.contrato.dataInicioContrato)+
          " <br/> "+converterData(dado.contrato.dataFimContrato)
          +"</td><td class='lead text-center'>"+converterData(dado.contrato.inicioPrazoPosPago)+"</td>"+
          "<td class='lead text-center'>"+converterData(dado.contrato.fimPrazoPosPago)
          +"</td><td class='lead text-center'>"+converterNumerosTresEmTres(dado.contrato.saldoParaPagamentoPosPago)
          +" Kz</td><td class='lead text-center'>"+vazioNull(imgContrato)
          +"</td><td class='text-center'><a class='btn btn-success' title='Editar' href='#' action='editarEscola'"+
          " idPEscola='"+dado.idPEscola+"'><i class='fa fa-check-circle'></i> Extender</a></td></tr>";       
    });
    $("#tabEscola").html(tbody);
}

