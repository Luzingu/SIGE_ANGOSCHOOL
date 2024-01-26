  var valorTotalPago=0;
  $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaGestaoEscolas/CPainel/pagamentoSistemaPosPago00/";
      
      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)
      fazerPesquisa(); 
      DataTables("#example1", "sim")
      
      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })
  }) 




  function fazerPesquisa(){
    var html="";
    contagem=0
    var totalPago=0
    pagamentos_escola.forEach(function(dado){
      totalPago +=dado.pagamentos.valorTotalPago
      contagem++;
        html += "<tr><td class='lead text-center'>"+completarNumero(contagem)
        +"</td><td class='lead text-center'>"+dado.pagamentos.horaReqPagamento+"<br/>"
         +converterData(dado.pagamentos.dataReqPagamento)+"</td><td class='lead'>"+
         dado.nomeEscola+
         "</td><td class='lead text-center'>"+
         converterNumerosTresEmTres(new Number(dado.contrato.valorPagoPor15Dias)*2)+
         "</td><td class='lead text-center'>"+
         converterNumerosTresEmTres(dado.pagamentos.valorTotalPago)+
         "</td><td class='lead text-center'><a title='Comprovativo' href='../../../Ficheiros/Escola_"+
           dado.idPEscola+"/Bolderon_Pag_Sistema/"+dado.pagamentos.imgBolderon+"'><i class='fa fa-print'></i></a></td></tr>";
    })
    $("#valorTotalPago").text(converterNumerosTresEmTres(totalPago))
    $("#tabela").html(html); 
 
   }
  