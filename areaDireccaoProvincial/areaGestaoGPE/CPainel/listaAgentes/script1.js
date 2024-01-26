window.onload = function(){
  
  directorio = "areaGestaoGPE/CPainel/listaAgentes/";
  $("#idPEscola").val(idPEscola)
  $("#idPEscola").change(function(){
    window.location='?idPEscola='+$(this).val()
  })
}