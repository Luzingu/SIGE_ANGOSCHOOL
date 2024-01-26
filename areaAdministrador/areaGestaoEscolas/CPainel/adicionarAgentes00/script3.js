window.onload=function(){
  directorio = "areaGestaoEscolas/CPainel/adicionarAgentes00/";
  $("#idPEscola").val(idPEscola)
  $("#idPEscola").change(function(){
    window.location='?idPEscola='+$(this).val()
  })
} 