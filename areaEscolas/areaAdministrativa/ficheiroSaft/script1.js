
    window.onload=function(){
      
      fecharJanelaEspera();
      seAbrirMenu();

      directorio = "areaAdministrativa/documentosComercFornecedores/";

      $("#anoCivil").val(anoCivil)
      $("#mesPagamento").val(mesPagamento)

      $("#gerarFicheiro").click(function(){
        window.location='../../relatoriosPdf/ficheiroSAFT/index.php?anoCivil='+
        $("#anoCivil").val()+"&dataInicial="+$("#dataInicial").val()
        +"&dataFinal="+$("#dataFinal").val()
      })

    }

