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
      mesesEmAtraso.forEach(function(dado){
        contador++
        dinheiroTotal +=new Number(dado.valorPagoMensal);
        html+= "<tr><td class='text-center lead'>"+completarNumero(contador)
        +"</td><td class='text-center lead'>"+converterData(dado.dataDivida1)
        +" - "+converterData(dado.dataDivida2)
        +"</td><td class='text-center lead'>"+converterNumerosTresEmTres(dado.valorPagoMensal)
        +"</td></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }