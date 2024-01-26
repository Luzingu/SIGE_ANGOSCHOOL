    var idPHistoricoConta="";
    var action="";
    var posicaoArray=0;

    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaSecretaria/facturasAnuladas/";

      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)
      fazerPesquisa(); 
      DataTables("#example1", "sim")
      
      $("#anoCivil, #mesPagamento").change(function(){
        window.location="?anoCivil="+$("#anoCivil").val()
        +"&mesPagamento="+$("#mesPagamento").val()
      })
    }

    function fazerPesquisa(){

      var html="";
      var dinheiroTotal=0;
      $("#totValores").text(0);

      listaFacturas.forEach(function(dado){
        dinheiroTotal +=new Number(dado.valorTotComImposto);
        html+= "<tr><td class='text-center'>"+dado.identificacaoUnica
        +"</td><td class='text-center'>"+dado.referenciaFactura
        +"</td><td class='text-center'>"+converterData(dado.dataEmissao)
        +"<br>"+dado.horaEmissao
        +"</td><td>"+dado.nomeFuncionario
        +"</td><td>"+dado.nomeCliente
        +"</td><td class='text-center'>"+converterNumerosTresEmTres(dado.valorTotComImposto)
        +"</td><td class='text-center'><a href='"+
        caminhoRecuar+"relatoriosPdf/reciboPagamento/index.php?idPDocumento="+dado.idPDocumento
        +"' title='Facturas'><i class='fa fa-print'></i></a></tr>";
      })
      $("#totValores").text(converterNumerosTresEmTres(dinheiroTotal));
      $("#tabDados").html(html);
    }