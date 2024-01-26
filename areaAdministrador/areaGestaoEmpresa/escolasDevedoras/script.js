  var valorTotalPago=0;
  $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaGestaoEscolas/escolasDevedoras/";
      fazerPesquisa(); 
      DataTables("#example1", "sim")
  }) 




  function fazerPesquisa(){
    var html="";
    contagem=0
    var valorTotal=0
    arrayDevedores.forEach(function(dado){
      valorTotal +=dado.total
      contagem++;
        html += "<tr><td class='lead text-center'>"+completarNumero(contagem)
        +"</td><td class='lead'>"+dado.nomeEscola+"</td><td class='lead'>"+
         converterNumerosTresEmTres(dado.valorPagoMensal)+
         "</td><td class='lead text-center'>"+dado.numeroMeses+
         "</td><td class='lead text-center'>"+
         converterNumerosTresEmTres(dado.total)+
         "</td></tr>";
    })
    $("#valorTotal").text(converterNumerosTresEmTres(valorTotal))
    $("#tabela").html(html); 
 
   }
  