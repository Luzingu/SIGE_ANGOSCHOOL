$(document).ready(function(){
	directorio = "areaInterConexao/listaMensagens/";
	fecharJanelaEspera();
	seAbrirMenu();

	$("#tabelaMensagem tr").click(function(){
		window.location =caminhoRecuar+"mensagem/index.php?idPUsuario="
		+$(this).attr("idUsuario")+"&tipoUsuario="+$(this).attr("tipoUsuario")		
	})
});