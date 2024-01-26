$(document).ready(function(){
	fecharJanelaEspera();
    seAbrirMenu();

    $("#anoCivil").val(anoCivil)
    $("#anoCivil").change(function(){
    	window.location='?anoCivil='+$(this).val()
    })
})
      


