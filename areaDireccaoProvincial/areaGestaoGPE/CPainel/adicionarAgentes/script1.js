window.onload=function(){
  directorio = "areaGestaoGPE/CPainel/adicionarAgentes/";
  $("#idPEscola").val(idPEscola)
  $("#idPEscola").change(function(){
    window.location='?idPEscola='+$(this).val()
  })
} 