  window.onload=function(){
      fecharJanelaEspera();
      seAbrirMenu();

      $("#luzingu").val(luzingu)
      $("#trimestre").val(trimestre)

      $("#luzingu, #trimestre").change(function(){
          window.location ="?luzingu="+$("#luzingu").val()+"&trimestre="+$("#trimestre").val();
      });
  }
