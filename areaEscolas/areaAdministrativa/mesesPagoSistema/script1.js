    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaAdministrativa/mesesPagoSistema/";
      $("#anoCivil").val(anoCivil)
      fazerPesquisa(); 
      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
      })
    }


    function fazerPesquisa(){
      var html="";
      var dinheiroTotal=0;
      $("#totValores").text(0);
      var contador=0;
      mesesPago.forEach(function(dado){
        contador++
          dinheiroTotal +=new Number(dado.mesPagosSistema.valorPago);

          html+= "<tr><td class='text-center lead'>"+completarNumero(contador)
          +"</td><td class='text-center lead'>"+dado.mesPagosSistema.data
          +"</td><td class='text-center lead'>"+dado.mesPagosSistema.hora
          +"</td><td class='text-center lead'>"+converterData(dado.mesPagosSistema.dataPagamento1)
          +" - "+converterData(dado.mesPagosSistema.dataPagamento2)
          +"</td><td class='text-center lead'>"+converterNumerosTresEmTres(dado.mesPagosSistema.valorPago)
          +"</td></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }