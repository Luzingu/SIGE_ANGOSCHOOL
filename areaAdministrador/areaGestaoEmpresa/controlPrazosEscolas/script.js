  var valorTotalPago=0;
  $(document).ready(function(){
      fecharJanelaEspera();
      seAbrirMenu();
      directorio = "areaGestaoEscolas/CPainel/pagamentoSistemaPosPago00/";

      fazerPesquisa();
      DataTables("#example1", "sim")
  })




  function fazerPesquisa(){
    var html="";
    contagem=0
    listaEscolas.forEach(function(dado){

      contagem++;
        html += "<tr><td class='lead text-center'>"+completarNumero(contagem)
        +"</td><td class='lead'>"+dado.nomeEscola+"</td><td class='lead text'center'>"+
         dado.numeroTelefone+
         "</td><td class='lead text-center'>"+converterData(dado.fimPrazo)+
         "</td><td class='lead text-center'>"
         var jibele = new Number(dado.numeroDias);
         if (jibele <= 0)
            jibele = "<strong class='text-danger'>Expirado ("+(-1*jibele)+")</strong>"

         html += jibele +
         "</td></tr>";
    })
    $("#tabela").html(html);
   }
