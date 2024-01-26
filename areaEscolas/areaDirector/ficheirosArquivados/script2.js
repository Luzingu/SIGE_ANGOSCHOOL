window.onload=function(){
    fecharJanelaEspera();
    seAbrirMenu()
    directorio = "areaDirector/ficheirosArquivados/";

    $("#idPAno").val(idPAno)
    $("#idPAno").change(function(){
      window.location ="?idPAno="+$(this).val()
    })

    $(".visualizadorRelatorio").click(function(){
      abrirLink($(this).attr("referencia"));
    })


}


function abrirLink(referencia){
  chamarJanelaEspera("");
  enviarComGet("tipoAcesso=abrirLink&idPAno="+idPAno+
    "&luzingu="+$("#luzingu").val()+"&trimestreReferencia="+
    $("#trimestreReferencia").val()+"&referencia="+referencia);

  http.onreadystatechange = function(){
    if(http.readyState==4){
      resultado = http.responseText.trim();
      alert(resultado)
      fecharJanelaEspera(); 
      if(resultado==""){
        mensagensRespostas("#mensagemErrada", "Este arquivo não foi encontrado no Servidor!");
      }else{
        window.location=resultado
      }     
      
    }
  }
}