  window.onload=function(){
      fecharJanelaEspera();
       seAbrirMenu();

      $(".visualizadorRelatorio").click(function(){
        window.location =caminhoRecuar+"relatoriosPdf/"+$(this).attr("caminho");
      })
      
  }